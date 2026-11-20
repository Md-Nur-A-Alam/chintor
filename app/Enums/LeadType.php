<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

/**
 * What the visitor asked for when the lead was captured.
 */
enum LeadType: string
{
    use HasOptions;

    case Wizard = 'wizard';

    case Callback = 'callback';

    case Assessment = 'assessment';

    case Enrollment = 'enrollment';

    case BookOrder = 'book_order';

    case Waitlist = 'waitlist';

    case Contact = 'contact';

    case ExamArena = 'exam_arena';

    public function label(): string
    {
        return match ($this) {
            self::Wizard => 'চলো শিখি ফর্ম',
            self::Callback => 'কল ব্যাক',
            self::Assessment => 'ফ্রি অ্যাসেসমেন্ট',
            self::Enrollment => 'ভর্তির অনুরোধ',
            self::BookOrder => 'বইয়ের অর্ডার',
            self::Waitlist => 'ওয়েটলিস্ট',
            self::Contact => 'যোগাযোগ ফর্ম',
            self::ExamArena => 'এক্সাম এরিনা',
        };
    }

    /**
     * Badge tone used by <x-admin.badge>.
     */
    public function tone(): string
    {
        return match ($this) {
            self::Wizard => 'info',
            self::Callback => 'neutral',
            self::Assessment => 'success',
            self::Enrollment => 'success',
            self::BookOrder => 'neutral',
            self::Waitlist => 'warning',
            self::Contact => 'neutral',
            self::ExamArena => 'info',
        };
    }
}
