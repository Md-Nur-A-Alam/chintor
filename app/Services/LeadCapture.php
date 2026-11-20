<?php

namespace App\Services;

use App\Enums\AssessmentStatus;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Http\Middleware\CaptureMarketingAttribution;
use App\Models\Assessment;
use App\Models\Lead;
use App\Models\LeadActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Turns a website form submission into a lead in the central queue.
 *
 * The same phone number never creates a second open lead within
 * DEDUPLICATION_DAYS: the new submission is added to the existing lead's
 * timeline instead, so the sales team never calls one family twice.
 */
class LeadCapture
{
    private const DEDUPLICATION_DAYS = 30;

    public function __construct(private readonly StaffNotifier $notifier) {}

    /**
     * @param  array{student_name?: ?string, guardian_name?: ?string, phone: string, whatsapp?: ?string, email?: ?string, class_level?: ?int, age?: ?int, message?: ?string}  $contact
     * @param  array{program_ids?: list<int>, course_ids?: list<int>, book_ids?: list<int>}  $interests
     */
    public function capture(LeadType $type, array $contact, Request $request, array $interests = []): Lead
    {
        return DB::transaction(function () use ($type, $contact, $request, $interests): Lead {
            $lead = $this->findOpenLead($contact['phone']);

            if ($lead === null) {
                $lead = Lead::create([
                    ...$contact,
                    'type' => $type,
                    'status' => LeadStatus::New,
                    'source' => LeadSource::Website,
                    'locale' => app()->getLocale(),
                    'consent_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
                    ...$request->session()->get(CaptureMarketingAttribution::SessionKey, []),
                ]);

                $this->notifier->newLead($lead);
            } else {
                // Fill in anything the earlier submission did not have.
                foreach ($contact as $field => $value) {
                    if (filled($value) && blank($lead->getAttribute($field))) {
                        $lead->setAttribute($field, $value);
                    }
                }

                $lead->save();

                $lead->activities()->create([
                    'type' => LeadActivity::TypeFormResubmitted,
                    'body' => $contact['message'] ?? null,
                    'meta' => ['form' => $type->value],
                ]);
            }

            $this->attachInterests($lead, $interests);

            if ($type === LeadType::Assessment) {
                $this->requestAssessment($lead, $interests['course_ids'][0] ?? null);
            }

            return $lead;
        });
    }

    /**
     * A free 1:1 assessment request; the sales team schedules it by phone.
     */
    private function requestAssessment(Lead $lead, ?int $courseId): void
    {
        $hasOpenRequest = Assessment::query()
            ->where('lead_id', $lead->id)
            ->whereIn('status', [AssessmentStatus::Requested, AssessmentStatus::Scheduled])
            ->exists();

        if (! $hasOpenRequest) {
            $assessment = Assessment::create([
                'lead_id' => $lead->id,
                'course_id' => $courseId,
                'status' => AssessmentStatus::Requested,
            ]);

            $this->notifier->assessmentRequested($assessment);
        }
    }

    private function findOpenLead(string $phone): ?Lead
    {
        return Lead::query()
            ->where('phone', $phone)
            ->open()
            ->where('created_at', '>=', now()->subDays(self::DEDUPLICATION_DAYS))
            ->latest()
            ->first();
    }

    /**
     * @param  array{program_ids?: list<int>, course_ids?: list<int>, book_ids?: list<int>}  $interests
     */
    private function attachInterests(Lead $lead, array $interests): void
    {
        $columns = ['program_ids' => 'program_id', 'course_ids' => 'course_id', 'book_ids' => 'book_id'];

        foreach ($columns as $key => $column) {
            foreach ($interests[$key] ?? [] as $id) {
                $lead->interests()->firstOrCreate([$column => $id]);
            }
        }
    }
}
