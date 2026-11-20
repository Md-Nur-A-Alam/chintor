<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Every permission and the roles that hold it by default. Admins (super
     * admins) pass every check (see AppServiceProvider), so they are not
     * listed. Route middleware and form requests use these names.
     *
     * @var array<string, array{label: string, roles: list<string>}>
     */
    public const PERMISSIONS = [
        'users.manage' => ['label' => 'স্টাফ অ্যাকাউন্ট', 'roles' => []],
        'settings.manage' => ['label' => 'সাইট সেটিংস ও স্টোরেজ', 'roles' => []],
        'media.manage' => ['label' => 'মিডিয়া লাইব্রেরি', 'roles' => [Role::Manager, Role::Marketing, Role::CourseCoordinator]],
        'catalog.manage' => ['label' => 'প্রোগ্রাম, কোর্স, ব্যাচ ও শিক্ষক', 'roles' => [Role::Manager, Role::CourseCoordinator]],
        'books.manage' => ['label' => 'বই ও লেখক', 'roles' => [Role::Manager, Role::Marketing]],
        'homepage.manage' => ['label' => 'হোমপেজ ও ক্যাম্পেইন', 'roles' => [Role::Marketing]],
        'learning.manage' => ['label' => 'শেখার ভিডিও, প্রশ্ন ব্যাংক ও পরীক্ষা', 'roles' => [Role::Manager, Role::Marketing, Role::CourseCoordinator]],
        'content.manage' => ['label' => 'পেজ, FAQ ও রিভিউ', 'roles' => [Role::Manager, Role::Marketing]],
        'leads.view_all' => ['label' => 'সব লিড দেখা (কেন্দ্রীয় কিউ)', 'roles' => [Role::Manager, Role::Marketing]],
        'leads.assign' => ['label' => 'লিড বণ্টন', 'roles' => [Role::Manager, Role::Marketing]],
        'leads.work' => ['label' => 'নিজের লিডে কাজ', 'roles' => [Role::Manager, Role::Marketing, Role::SalesExecutive]],
        'assessments.manage' => ['label' => 'অ্যাসেসমেন্ট সমন্বয়', 'roles' => [Role::Manager, Role::CourseCoordinator]],
        'orders.manage' => ['label' => 'বইয়ের অর্ডার ও ডেলিভারি', 'roles' => [Role::Manager]],
        'enrollments.manage' => ['label' => 'এনরোলমেন্ট', 'roles' => [Role::Manager, Role::CourseCoordinator, Role::SalesExecutive]],
        'payments.verify' => ['label' => 'পেমেন্ট যাচাই', 'roles' => [Role::Manager]],
        'reports.view' => ['label' => 'রিপোর্ট', 'roles' => [Role::Manager, Role::Marketing]],
        'developer.tools' => ['label' => 'ডেভেলপার টুলস', 'roles' => [Role::Developer]],
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $name => $definition) {
            $permission = Permission::updateOrCreate(['name' => $name], ['label' => $definition['label']]);

            $permission->roles()->sync(Role::whereIn('name', $definition['roles'])->pluck('id'));
        }
    }
}
