<?php

use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\ContactSettingsController;
use App\Http\Controllers\Admin\CourseBatchController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CourseVariationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryZoneController;
use App\Http\Controllers\Admin\Developer\DatabaseConsoleController;
use App\Http\Controllers\Admin\Developer\DeveloperController;
use App\Http\Controllers\Admin\Developer\ErrorLogController;
use App\Http\Controllers\Admin\Developer\SchemaController;
use App\Http\Controllers\Admin\Developer\StaffAccessController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\Admin\ExamAttemptController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\Admin\HomePageController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\Admin\LearningTopicController;
use App\Http\Controllers\Admin\LearningVideoController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaSettingsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PaymentAccountController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SalesSettingsController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Site\ArenaController;
use App\Http\Controllers\Site\BookController;
use App\Http\Controllers\Site\CourseController;
use App\Http\Controllers\Site\EnrollmentController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\LeadController;
use App\Http\Controllers\Site\LearnController;
use App\Http\Controllers\Site\OrderController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\ProgramController;
use App\Http\Controllers\Site\TeacherController;
use App\Http\Middleware\CaptureMarketingAttribution;
use App\Support\Localization;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public, localized routes
|--------------------------------------------------------------------------
|
| Every public page is registered once per locale. Bangla (default) lives
| at the root ("/courses"), other locales under a prefix ("/en/courses")
| with a "{locale}." route-name prefix ("en.courses.index").
|
*/

$publicRoutes = function (): void {
    Route::get('/', HomeController::class)->name('home');

    Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('programs.show');

    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/enroll', [EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store'])
        ->middleware('throttle:public-forms')
        ->name('enrollments.store');
    Route::get('/enrollments/{enrollment:ulid}', [EnrollmentController::class, 'success'])->name('enrollments.success');

    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/books/{book}/order', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/books/{book}/order', [OrderController::class, 'store'])
        ->middleware('throttle:public-forms')
        ->name('orders.store');
    Route::get('/orders/{order:ulid}', [OrderController::class, 'success'])->name('orders.success');
    Route::get('/track-order', [OrderController::class, 'track'])
        ->middleware('throttle:public-forms')
        ->name('orders.track');

    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');

    Route::get('/start', [LeadController::class, 'create'])->name('start');
    Route::post('/leads', [LeadController::class, 'store'])
        ->middleware('throttle:public-forms')
        ->name('leads.store');
    Route::get('/thank-you', [LeadController::class, 'thanks'])->name('leads.thanks');

    Route::get('/info/{page}', [PageController::class, 'show'])->name('pages.show');

    // Free learning: video library and Exam Arena (MCQ practice, no login)
    Route::get('/learn', [LearnController::class, 'index'])->name('learn.index');
    Route::get('/arena', [ArenaController::class, 'index'])->name('arena.index');
    Route::post('/arena/practice', [ArenaController::class, 'startPractice'])
        ->middleware('throttle:public-forms')
        ->name('arena.practice');
    Route::get('/arena/exams/{exam}', [ArenaController::class, 'showExam'])->name('arena.exams.show');
    Route::post('/arena/exams/{exam}/start', [ArenaController::class, 'startExam'])
        ->middleware('throttle:public-forms')
        ->name('arena.exams.start');
    Route::get('/arena/attempts/{attempt}', [ArenaController::class, 'show'])->name('arena.attempts.show');
    Route::post('/arena/attempts/{attempt}', [ArenaController::class, 'submit'])
        ->middleware('throttle:public-forms')
        ->name('arena.attempts.submit');
    Route::get('/arena/attempts/{attempt}/result', [ArenaController::class, 'result'])->name('arena.attempts.result');

    if (! app()->isProduction()) {
        Route::view('/design-system', 'pages.design-system')->name('design-system');
    }
};

foreach (array_keys(Localization::supportedLocales()) as $locale) {
    $group = Route::middleware(["locale:{$locale}", CaptureMarketingAttribution::class]);

    if ($locale !== Localization::defaultLocale()) {
        $group->prefix($locale)->name("{$locale}.");
    }

    $group->group($publicRoutes);
}

/*
|--------------------------------------------------------------------------
| Admin panel (Bangla only, staff sign in with phone + password)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware('locale:bn')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [LoginController::class, 'create'])->name('login');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    });

    Route::middleware(['auth', 'staff'])->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        Route::get('/guide', GuideController::class)->name('guide');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/summary', [NotificationController::class, 'summary'])->name('notifications.summary');
        Route::get('/notifications/{notification}/open', [NotificationController::class, 'open'])->name('notifications.open');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

        // Reachable while viewing the panel as another staff member.
        Route::post('/impersonation/stop', [StaffAccessController::class, 'stopImpersonating'])->name('impersonation.stop');

        // Developer tools
        Route::middleware('can:developer.tools')->prefix('developer')->name('developer.')->group(function (): void {
            Route::get('/', [DeveloperController::class, 'index'])->name('index');
            Route::get('/guide', [DeveloperController::class, 'guide'])->name('guide');
            Route::get('/database', [DatabaseConsoleController::class, 'show'])->name('database');
            Route::post('/database', [DatabaseConsoleController::class, 'run'])->name('database.run');
            Route::get('/schema', [SchemaController::class, 'show'])->name('schema');
            Route::get('/errors', [ErrorLogController::class, 'index'])->name('errors');
            Route::post('/errors/clear', [ErrorLogController::class, 'clear'])->name('errors.clear');
            Route::get('/staff', [StaffAccessController::class, 'index'])->name('staff');
            Route::post('/staff/{user}/impersonate', [StaffAccessController::class, 'impersonate'])->name('staff.impersonate');
            Route::post('/staff/{user}/reset-password', [StaffAccessController::class, 'resetPassword'])->name('staff.reset-password');
        });

        Route::resource('users', UserController::class)
            ->except('show')
            ->middleware('can:users.manage');

        Route::middleware('can:settings.manage')->prefix('settings')->name('settings.')->group(function (): void {
            Route::get('/contact', [ContactSettingsController::class, 'edit'])->name('contact.edit');
            Route::put('/contact', [ContactSettingsController::class, 'update'])->name('contact.update');
            Route::get('/media', [MediaSettingsController::class, 'edit'])->name('media.edit');
            Route::put('/media', [MediaSettingsController::class, 'update'])->name('media.update');
            Route::post('/media/test', [MediaSettingsController::class, 'test'])->name('media.test');
            Route::get('/sales', [SalesSettingsController::class, 'edit'])->name('sales.edit');
            Route::put('/sales', [SalesSettingsController::class, 'update'])->name('sales.update');
        });

        Route::middleware('can:settings.manage')->group(function (): void {
            Route::resource('payment-accounts', PaymentAccountController::class)->except('show');
            Route::resource('delivery-zones', DeliveryZoneController::class)->only(['store', 'update', 'destroy']);
        });

        Route::middleware('can:media.manage')->group(function (): void {
            Route::get('/media', [MediaController::class, 'index'])->name('media.index');
            Route::post('/media', [MediaController::class, 'store'])->name('media.store');
            Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
        });

        // Catalog
        Route::middleware('can:catalog.manage')->group(function (): void {
            Route::resource('programs', AdminProgramController::class)->except('show');
            Route::resource('courses', AdminCourseController::class)->except('show');
            Route::resource('courses.variations', CourseVariationController::class)->only(['store', 'update', 'destroy'])->scoped();
            Route::resource('courses.batches', CourseBatchController::class)->only(['store', 'update', 'destroy'])->scoped();
            Route::resource('teachers', AdminTeacherController::class)->except('show');
        });

        Route::resource('books', AdminBookController::class)->except('show')->middleware('can:books.manage');

        // Website: homepage builder and campaigns
        Route::middleware('can:homepage.manage')->group(function (): void {
            Route::get('/homepage', [HomePageController::class, 'edit'])->name('homepage.edit');
            Route::put('/homepage', [HomePageController::class, 'update'])->name('homepage.update');
            Route::resource('campaigns', CampaignController::class)->except('show');
        });

        // Learning: video library, question bank and Exam Arena
        Route::middleware('can:learning.manage')->group(function (): void {
            Route::resource('learning-topics', LearningTopicController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::resource('learning-videos', LearningVideoController::class)->except('show');
            Route::resource('questions', QuestionController::class)->except('show');
            Route::resource('exams', ExamController::class)->except('show');
            Route::get('/exam-attempts', [ExamAttemptController::class, 'index'])->name('exam-attempts.index');
        });

        // Content
        Route::middleware('can:content.manage')->group(function (): void {
            Route::resource('pages', AdminPageController::class)->except('show');
            Route::resource('faqs', FaqController::class)->except('show');
            Route::resource('testimonials', TestimonialController::class)->except('show');
        });

        // Sales
        Route::middleware('can:leads.work')->group(function (): void {
            Route::get('/leads', [AdminLeadController::class, 'index'])->name('leads.index');
            Route::post('/leads/assign', [AdminLeadController::class, 'assign'])->name('leads.assign');
            Route::get('/leads/{lead}', [AdminLeadController::class, 'show'])->name('leads.show');
            Route::put('/leads/{lead}', [AdminLeadController::class, 'update'])->name('leads.update');
            Route::post('/leads/{lead}/activities', [AdminLeadController::class, 'storeActivity'])->name('leads.activities.store');
        });

        Route::middleware('can:assessments.manage')->group(function (): void {
            Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
            Route::get('/assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
            Route::put('/assessments/{assessment}', [AssessmentController::class, 'update'])->name('assessments.update');
        });

        Route::middleware('can:enrollments.manage')->group(function (): void {
            Route::get('/enrollments', [AdminEnrollmentController::class, 'index'])->name('enrollments.index');
            Route::get('/enrollments/create', [AdminEnrollmentController::class, 'create'])->name('enrollments.create');
            Route::post('/enrollments', [AdminEnrollmentController::class, 'store'])->name('enrollments.store');
            Route::get('/enrollments/{enrollment}', [AdminEnrollmentController::class, 'show'])->name('enrollments.show');
            Route::put('/enrollments/{enrollment}', [AdminEnrollmentController::class, 'update'])->name('enrollments.update');
            Route::post('/enrollments/{enrollment}/payments', [AdminEnrollmentController::class, 'storePayment'])->name('enrollments.payments.store');
        });

        Route::middleware('can:orders.manage')->group(function (): void {
            Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
            Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
        });

        Route::middleware('can:payments.verify')->group(function (): void {
            Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
            Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
            Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        });
    });
});
