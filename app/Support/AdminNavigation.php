<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Route;

/**
 * The admin sidebar. Each module registers one entry here; an entry is
 * shown only when its route exists and the signed-in staff member holds
 * its permission, so every role sees exactly the panel it needs.
 */
class AdminNavigation
{
    /**
     * @var list<array{group: string, items: list<array{label: string, route: string, icon: string, permission: ?string, active: string}>}>
     */
    private const SECTIONS = [
        [
            'group' => 'ওভারভিউ',
            'items' => [
                ['label' => 'ড্যাশবোর্ড', 'route' => 'admin.dashboard', 'icon' => 'home', 'permission' => null, 'active' => 'admin.dashboard'],
                ['label' => 'নোটিফিকেশন', 'route' => 'admin.notifications.index', 'icon' => 'bell', 'permission' => null, 'active' => 'admin.notifications.*'],
                ['label' => 'নির্দেশিকা', 'route' => 'admin.guide', 'icon' => 'help', 'permission' => null, 'active' => 'admin.guide'],
            ],
        ],
        [
            'group' => 'সেলস',
            'items' => [
                ['label' => 'লিড', 'route' => 'admin.leads.index', 'icon' => 'users', 'permission' => 'leads.work', 'active' => 'admin.leads.*'],
                ['label' => 'অ্যাসেসমেন্ট', 'route' => 'admin.assessments.index', 'icon' => 'target', 'permission' => 'assessments.manage', 'active' => 'admin.assessments.*'],
                ['label' => 'এনরোলমেন্ট', 'route' => 'admin.enrollments.index', 'icon' => 'check', 'permission' => 'enrollments.manage', 'active' => 'admin.enrollments.*'],
                ['label' => 'বইয়ের অর্ডার', 'route' => 'admin.orders.index', 'icon' => 'book', 'permission' => 'orders.manage', 'active' => 'admin.orders.*'],
                ['label' => 'পেমেন্ট যাচাই', 'route' => 'admin.payments.index', 'icon' => 'shield', 'permission' => 'payments.verify', 'active' => 'admin.payments.*'],
            ],
        ],
        [
            'group' => 'ক্যাটালগ',
            'items' => [
                ['label' => 'প্রোগ্রাম', 'route' => 'admin.programs.index', 'icon' => 'layers', 'permission' => 'catalog.manage', 'active' => 'admin.programs.*'],
                ['label' => 'কোর্স', 'route' => 'admin.courses.index', 'icon' => 'play', 'permission' => 'catalog.manage', 'active' => 'admin.courses.*'],
                ['label' => 'শিক্ষক', 'route' => 'admin.teachers.index', 'icon' => 'users', 'permission' => 'catalog.manage', 'active' => 'admin.teachers.*'],
                ['label' => 'বই', 'route' => 'admin.books.index', 'icon' => 'book', 'permission' => 'books.manage', 'active' => 'admin.books.*'],
            ],
        ],
        [
            'group' => 'ওয়েবসাইট',
            'items' => [
                ['label' => 'হোমপেজ', 'route' => 'admin.homepage.edit', 'icon' => 'home', 'permission' => 'homepage.manage', 'active' => 'admin.homepage.*'],
                ['label' => 'ক্যাম্পেইন', 'route' => 'admin.campaigns.index', 'icon' => 'megaphone', 'permission' => 'homepage.manage', 'active' => 'admin.campaigns.*'],
            ],
        ],
        [
            'group' => 'কনটেন্ট',
            'items' => [
                ['label' => 'পেজ', 'route' => 'admin.pages.index', 'icon' => 'file', 'permission' => 'content.manage', 'active' => 'admin.pages.*'],
                ['label' => 'FAQ', 'route' => 'admin.faqs.index', 'icon' => 'message', 'permission' => 'content.manage', 'active' => 'admin.faqs.*'],
                ['label' => 'রিভিউ', 'route' => 'admin.testimonials.index', 'icon' => 'star', 'permission' => 'content.manage', 'active' => 'admin.testimonials.*'],
                ['label' => 'মিডিয়া', 'route' => 'admin.media.index', 'icon' => 'image', 'permission' => 'media.manage', 'active' => 'admin.media.*'],
            ],
        ],
        [
            'group' => 'সেটিংস',
            'items' => [
                ['label' => 'স্টাফ', 'route' => 'admin.users.index', 'icon' => 'shield', 'permission' => 'users.manage', 'active' => 'admin.users.*'],
                ['label' => 'যোগাযোগ', 'route' => 'admin.settings.contact.edit', 'icon' => 'phone', 'permission' => 'settings.manage', 'active' => 'admin.settings.contact.*'],
                ['label' => 'বিক্রয় সেটিংস', 'route' => 'admin.settings.sales.edit', 'icon' => 'refund', 'permission' => 'settings.manage', 'active' => 'admin.settings.sales.*'],
                ['label' => 'পেমেন্ট অ্যাকাউন্ট', 'route' => 'admin.payment-accounts.index', 'icon' => 'wallet', 'permission' => 'settings.manage', 'active' => 'admin.payment-accounts.*'],
                ['label' => 'ছবি স্টোরেজ', 'route' => 'admin.settings.media.edit', 'icon' => 'cloud', 'permission' => 'settings.manage', 'active' => 'admin.settings.media.*'],
            ],
        ],
        [
            'group' => 'ডেভেলপার',
            'items' => [
                ['label' => 'ডেভেলপার হাব', 'route' => 'admin.developer.index', 'icon' => 'code', 'permission' => 'developer.tools', 'active' => 'admin.developer.index'],
                ['label' => 'ডেটাবেস কনসোল', 'route' => 'admin.developer.database', 'icon' => 'database', 'permission' => 'developer.tools', 'active' => 'admin.developer.database*'],
                ['label' => 'ER ডায়াগ্রাম', 'route' => 'admin.developer.schema', 'icon' => 'layers', 'permission' => 'developer.tools', 'active' => 'admin.developer.schema'],
                ['label' => 'এরর লগ', 'route' => 'admin.developer.errors', 'icon' => 'alert', 'permission' => 'developer.tools', 'active' => 'admin.developer.errors*'],
                ['label' => 'স্টাফ অ্যাক্সেস', 'route' => 'admin.developer.staff', 'icon' => 'key', 'permission' => 'developer.tools', 'active' => 'admin.developer.staff*'],
                ['label' => 'ডেভেলপার গাইড', 'route' => 'admin.developer.guide', 'icon' => 'file', 'permission' => 'developer.tools', 'active' => 'admin.developer.guide'],
            ],
        ],
    ];

    /**
     * @return list<array{group: string, items: list<array{label: string, url: string, icon: string, active: bool}>}>
     */
    public static function for(User $user): array
    {
        $sections = [];

        foreach (self::SECTIONS as $section) {
            $items = [];

            foreach ($section['items'] as $item) {
                if (! Route::has($item['route'])) {
                    continue;
                }

                if ($item['permission'] !== null && ! $user->can($item['permission'])) {
                    continue;
                }

                $items[] = [
                    'label' => $item['label'],
                    'url' => route($item['route']),
                    'icon' => $item['icon'],
                    'active' => request()->routeIs($item['active']),
                ];
            }

            if ($items !== []) {
                $sections[] = ['group' => $section['group'], 'items' => $items];
            }
        }

        return $sections;
    }
}
