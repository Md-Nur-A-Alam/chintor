<?php

use App\Enums\LeadType;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\LearningTopic;
use App\Models\Question;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->math = LearningTopic::factory()->create(['slug' => 'math']);
    $this->geometry = LearningTopic::factory()->topicOf($this->math)->create(['slug' => 'geometry']);
    $this->algebra = LearningTopic::factory()->topicOf($this->math)->create(['slug' => 'algebra']);
});

test('a custom practice deals questions only from the chosen topics and class', function () {
    $geometry = Question::factory()->count(3)->for($this->math, 'subject')->create(['topic_id' => $this->geometry->id, 'class_level' => 6]);
    Question::factory()->count(3)->for($this->math, 'subject')->create(['topic_id' => $this->algebra->id, 'class_level' => 6]);
    Question::factory()->for($this->math, 'subject')->create(['topic_id' => $this->geometry->id, 'class_level' => 9]);
    Question::factory()->for($this->math, 'subject')->create(['topic_id' => $this->geometry->id, 'class_level' => 6, 'is_active' => false]);

    $this->post('/arena/practice', [
        'student_name' => 'Rafi',
        'subject_id' => $this->math->id,
        'topic_ids' => [$this->geometry->id],
        'class_level' => 6,
        'question_count' => 10,
        'duration_minutes' => 3,
    ])->assertRedirect();

    $attempt = ExamAttempt::sole();

    expect($attempt->question_ids)->toEqualCanonicalizing($geometry->pluck('id')->all())
        ->and($attempt->deadline_at->diffInMinutes($attempt->started_at, true))->toEqual(3);
});

test('a practice cannot give more than one minute per question', function () {
    Question::factory()->count(5)->for($this->math, 'subject')->create();

    $this->post('/arena/practice', [
        'student_name' => 'Rafi',
        'subject_id' => $this->math->id,
        'question_count' => 5,
        'duration_minutes' => 6,
    ])->assertSessionHasErrors('duration_minutes');

    expect(ExamAttempt::count())->toBe(0);
});

test('submitting grades the answers and the result shows explanations', function () {
    [$first, $second, $third] = Question::factory()->count(3)->for($this->math, 'subject')->create(['explanation' => 'কারণ দুই আর দুই চার।']);
    $attempt = ExamAttempt::factory()->create(['question_ids' => [$first->id, $second->id, $third->id]]);

    $this->post("/arena/attempts/{$attempt->ulid}", ['answers' => [$first->id => 1, $second->id => 0]])
        ->assertRedirect(route('arena.attempts.result', $attempt));

    expect($attempt->fresh())
        ->correct_count->toBe(1)
        ->wrong_count->toBe(1)
        ->skipped_count->toBe(1);

    $this->get("/arena/attempts/{$attempt->ulid}/result")
        ->assertOk()
        ->assertSee('কারণ দুই আর দুই চার।')
        ->assertSee('৩৩%');
});

test('answers sent long after the deadline are not counted', function () {
    $question = Question::factory()->for($this->math, 'subject')->create();
    $attempt = ExamAttempt::factory()->create([
        'question_ids' => [$question->id],
        'started_at' => now()->subMinutes(20),
        'deadline_at' => now()->subMinutes(10),
    ]);

    $this->post("/arena/attempts/{$attempt->ulid}", ['answers' => [$question->id => 1]]);

    expect($attempt->fresh())->correct_count->toBe(0)->skipped_count->toBe(1);
});

test('a submitted attempt cannot be graded again', function () {
    $question = Question::factory()->for($this->math, 'subject')->create();
    $attempt = ExamAttempt::factory()->create(['question_ids' => [$question->id]]);

    $this->post("/arena/attempts/{$attempt->ulid}", ['answers' => [$question->id => 0]]);
    $this->post("/arena/attempts/{$attempt->ulid}", ['answers' => [$question->id => 1]]);

    expect($attempt->fresh()->correct_count)->toBe(0);
    $this->get("/arena/attempts/{$attempt->ulid}")->assertRedirect(route('arena.attempts.result', $attempt));
});

test('a ready-made exam deals its question count and leaving a phone creates a lead', function () {
    $exam = Exam::factory()->create(['question_count' => 2]);
    $exam->questions()->attach(Question::factory()->count(4)->for($this->math, 'subject')->create());

    $this->post("/arena/exams/{$exam->slug}/start", ['student_name' => 'Nusrat', 'phone' => '01712345678', 'consent' => '1'])
        ->assertRedirect();

    $attempt = ExamAttempt::sole();

    expect($attempt->question_ids)->toHaveCount(2)
        ->and($attempt->lead->type)->toBe(LeadType::ExamArena)
        ->and($attempt->lead->phone)->toBe('+8801712345678');
});

test('a phone number needs consent', function () {
    $exam = Exam::factory()->create();
    $exam->questions()->attach(Question::factory()->for($this->math, 'subject')->create());

    $this->post("/arena/exams/{$exam->slug}/start", ['student_name' => 'Nusrat', 'phone' => '01712345678'])
        ->assertSessionHasErrors('consent');
});

test('results stay hidden until the exam publishes them', function () {
    $exam = Exam::factory()->create(['results_at' => now()->addDay()]);
    $question = Question::factory()->for($this->math, 'subject')->create(['explanation' => 'গোপন ব্যাখ্যা']);
    $attempt = ExamAttempt::factory()->for($exam)->create(['question_ids' => [$question->id], 'submitted_at' => now()]);

    $this->get("/arena/attempts/{$attempt->ulid}/result")->assertOk()->assertDontSee('গোপন ব্যাখ্যা');

    $this->travel(2)->days();

    $this->get("/arena/attempts/{$attempt->ulid}/result")->assertSee('গোপন ব্যাখ্যা');
});

test('an exam cannot be published with fewer questions than it deals', function () {
    $coordinator = User::factory()->withRole(Role::CourseCoordinator)->create();
    $question = Question::factory()->for($this->math, 'subject')->create();

    $this->actingAs($coordinator)->post('/admin/exams', [
        'title' => ['bn' => 'সাপ্তাহিক পরীক্ষা'],
        'slug' => 'weekly-1',
        'duration_minutes' => 5,
        'question_count' => 10,
        'is_published' => '1',
        'question_ids' => [$question->id],
    ])->assertSessionHasErrors('question_ids');
});

test('a question keeps only filled options and the right answer index', function () {
    $coordinator = User::factory()->withRole(Role::CourseCoordinator)->create();

    $this->actingAs($coordinator)->post('/admin/questions', [
        'subject_id' => $this->math->id,
        'topic_id' => $this->geometry->id,
        'body' => 'ত্রিভুজের কোণের সমষ্টি কত?',
        'options' => ['৯০°', '', '১৮০°', '৩৬০°'],
        'correct_option' => 2,
        'explanation_pdf_url' => 'https://drive.google.com/file/d/abc/view',
        'is_active' => '1',
    ])->assertRedirect(route('admin.questions.index'));

    expect(Question::sole())
        ->options->toBe(['৯০°', '১৮০°', '৩৬০°'])
        ->correct_option->toBe(1);
});

test('an explanation PDF must be a Google Drive link', function () {
    $coordinator = User::factory()->withRole(Role::CourseCoordinator)->create();

    $this->actingAs($coordinator)->post('/admin/questions', [
        'subject_id' => $this->math->id,
        'body' => 'প্রশ্ন',
        'options' => ['ক', 'খ'],
        'correct_option' => 0,
        'explanation_pdf_url' => 'https://example.com/file.pdf',
    ])->assertSessionHasErrors('explanation_pdf_url');
});
