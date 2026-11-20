<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Roles agreed with the founder. Discount limits are starting values and
     * are editable later. Teacher and student roles are reserved for the LMS.
     *
     * @var array<string, array{label: string, description: string, max_discount_percent: int|null}>
     */
    public const ROLES = [
        Role::SuperAdmin => [
            'label' => 'অ্যাডমিন',
            'description' => 'সব মডিউল ও সেটিংসে পূর্ণ অ্যাক্সেস।',
            'max_discount_percent' => 100,
        ],
        Role::Developer => [
            'label' => 'ডেভেলপার',
            'description' => 'পূর্ণ অ্যাক্সেস + ডেভেলপার টুলস (ডেটাবেস, ER ডায়াগ্রাম, এরর লগ, প্রোফাইল সুইচ, পাসওয়ার্ড রিসেট)।',
            'max_discount_percent' => 100,
        ],
        Role::Manager => [
            'label' => 'ম্যানেজার',
            'description' => 'লিড বণ্টন, অর্ডার, এনরোলমেন্ট, পেমেন্ট ও রিপোর্ট।',
            'max_discount_percent' => 30,
        ],
        Role::Marketing => [
            'label' => 'মার্কেটিং',
            'description' => 'লিড কিউ, লিড বণ্টন, কনটেন্ট ও ক্যাম্পেইন।',
            'max_discount_percent' => 15,
        ],
        Role::SalesExecutive => [
            'label' => 'সেলস এক্সিকিউটিভ',
            'description' => 'নিজের লিডে কল, ফলো-আপ ও সীমিত ডিসকাউন্ট।',
            'max_discount_percent' => 10,
        ],
        Role::CourseCoordinator => [
            'label' => 'কোর্স কোঅর্ডিনেটর',
            'description' => 'প্রোগ্রাম, কোর্স, ব্যাচ, শিক্ষক ও অ্যাসেসমেন্ট সমন্বয়।',
            'max_discount_percent' => null,
        ],
        Role::Teacher => [
            'label' => 'শিক্ষক',
            'description' => 'অ্যাসেসমেন্ট ও (পরে) LMS-এ ক্লাস পরিচালনা।',
            'max_discount_percent' => null,
        ],
        Role::Student => [
            'label' => 'শিক্ষার্থী',
            'description' => 'LMS চালু হলে কোর্স কনটেন্টে অ্যাক্সেস।',
            'max_discount_percent' => null,
        ],
    ];

    public function run(): void
    {
        foreach (self::ROLES as $name => $attributes) {
            Role::updateOrCreate(['name' => $name], $attributes);
        }

        // Roles from the first draft that are no longer used.
        Role::whereIn('name', ['order_staff', 'content_editor'])->delete();
    }
}
