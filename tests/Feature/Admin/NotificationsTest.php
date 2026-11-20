<?php

use App\Models\Book;
use App\Models\DeliveryZone;
use App\Models\Lead;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->admin = User::factory()->withRole(Role::SuperAdmin)->create();
    $this->developer = User::factory()->withRole(Role::Developer)->create();
    $this->marketing = User::factory()->withRole(Role::Marketing)->create();
    $this->sales = User::factory()->withRole(Role::SalesExecutive)->create();
    $this->manager = User::factory()->withRole(Role::Manager)->create();
});

test('a new website lead notifies the lead distributors but not sales executives or developers', function () {
    $this->post('/leads', ['form' => 'callback', 'phone' => '01712345678', 'consent' => '1']);

    expect($this->marketing->notifications()->count())->toBe(1)
        ->and($this->manager->notifications()->count())->toBe(1)
        ->and($this->admin->notifications()->count())->toBe(1)
        ->and($this->sales->notifications()->count())->toBe(0)
        ->and($this->developer->notifications()->count())->toBe(0)
        ->and($this->marketing->notifications()->first()->data['title'])->toContain('নতুন লিড');
});

test('assigning a lead notifies the sales executive', function () {
    $lead = Lead::factory()->create(['guardian_name' => 'Rahman']);

    $this->actingAs($this->marketing)->post('/admin/leads/assign', ['lead_ids' => [$lead->id], 'assigned_to' => $this->sales->id]);

    expect($this->sales->notifications()->sole()->data)
        ->title->toBe('আপনাকে লিড দেওয়া হয়েছে: Rahman')
        ->url->toBe(route('admin.leads.show', $lead));
});

test('a book order alerts the people who verify payments and ship orders', function () {
    DeliveryZone::factory()->create();
    $book = Book::factory()->create();

    $this->post("/books/{$book->slug}/order", [
        'quantity' => 1, 'customer_name' => 'Rahim', 'phone' => '01712345678', 'shipping_address' => 'Road 2',
        'district' => 'Dhaka', 'payment_method' => 'bkash', 'sender_number' => '01712345678', 'transaction_id' => 'NOTIFY1',
    ]);

    // Manager holds both orders.manage and payments.verify.
    expect($this->manager->notifications()->count())->toBe(2)
        ->and($this->sales->notifications()->count())->toBe(0);
});

test('the bell summary, opening a notification and marking all as read', function () {
    $this->post('/leads', ['form' => 'callback', 'phone' => '01712345678', 'consent' => '1']);
    $this->post('/leads', ['form' => 'callback', 'phone' => '01812345678', 'consent' => '1']);

    $this->actingAs($this->marketing)
        ->getJson('/admin/notifications/summary')
        ->assertOk()
        ->assertJsonPath('unread', 2)
        ->assertJsonCount(2, 'latest');

    $notification = $this->marketing->notifications()->first();

    $this->actingAs($this->marketing)
        ->get("/admin/notifications/{$notification->id}/open")
        ->assertRedirect($notification->data['url']);

    expect($this->marketing->unreadNotifications()->count())->toBe(1);

    $this->actingAs($this->marketing)->post('/admin/notifications/read-all');
    expect($this->marketing->unreadNotifications()->count())->toBe(0);

    $this->actingAs($this->marketing)->get('/admin/notifications')->assertOk();
});

test('staff cannot open someone else\'s notification', function () {
    $this->post('/leads', ['form' => 'callback', 'phone' => '01712345678', 'consent' => '1']);
    $notification = $this->marketing->notifications()->first();

    $this->actingAs($this->sales)->get("/admin/notifications/{$notification->id}/open")->assertNotFound();
});

test('every role sees its own guide', function () {
    $this->actingAs($this->sales)->get('/admin/guide')->assertOk()->assertSee('সেলস এক্সিকিউটিভের কাজ')->assertDontSee('ডেটাবেস কনসোল');
    $this->actingAs($this->developer)->get('/admin/guide')->assertOk()->assertSee('ডেটাবেস কনসোল');
});
