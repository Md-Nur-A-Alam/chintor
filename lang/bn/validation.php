<?php

/*
|--------------------------------------------------------------------------
| Bangla validation messages
|--------------------------------------------------------------------------
|
| Covers the rules used by Chintor's forms. Bangla is also the fallback
| locale, so add a message here whenever a form starts using a new rule.
|
*/

return [
    'accepted' => ':attribute ঘরে টিক দিন।',
    'alpha_num' => ':attribute-এ শুধু অক্ষর ও সংখ্যা থাকতে পারে।',
    'array' => ':attribute সঠিক নয়।',
    'before' => ':attribute অবশ্যই :date-এর আগের তারিখ হতে হবে।',
    'between' => [
        'numeric' => ':attribute অবশ্যই :min থেকে :max-এর মধ্যে হতে হবে।',
        'string' => ':attribute :min থেকে :max অক্ষরের মধ্যে হতে হবে।',
    ],
    'boolean' => ':attribute সঠিক নয়।',
    'confirmed' => ':attribute দুবার একই লেখা হয়নি।',
    'date' => ':attribute সঠিক তারিখ নয়।',
    'email' => 'সঠিক ইমেইল ঠিকানা দিন।',
    'enum' => 'বাছাই করা :attribute সঠিক নয়।',
    'exists' => 'বাছাই করা :attribute পাওয়া যায়নি।',
    'file' => ':attribute একটি ফাইল হতে হবে।',
    'image' => ':attribute একটি ছবি হতে হবে।',
    'in' => 'বাছাই করা :attribute সঠিক নয়।',
    'integer' => ':attribute একটি পূর্ণ সংখ্যা হতে হবে।',
    'max' => [
        'array' => ':attribute-এ :max টির বেশি থাকতে পারবে না।',
        'file' => ':attribute :max কিলোবাইটের বেশি হতে পারবে না।',
        'numeric' => ':attribute :max-এর বেশি হতে পারবে না।',
        'string' => ':attribute :max অক্ষরের বেশি হতে পারবে না।',
    ],
    'mimes' => ':attribute অবশ্যই :values ধরনের ফাইল হতে হবে।',
    'min' => [
        'array' => ':attribute-এ অন্তত :min টি থাকতে হবে।',
        'numeric' => ':attribute অন্তত :min হতে হবে।',
        'string' => ':attribute অন্তত :min অক্ষরের হতে হবে।',
    ],
    'numeric' => ':attribute একটি সংখ্যা হতে হবে।',
    'password' => [
        'letters' => ':attribute-এ অন্তত একটি অক্ষর থাকতে হবে।',
        'mixed' => ':attribute-এ বড় ও ছোট হাতের অক্ষর থাকতে হবে।',
        'numbers' => ':attribute-এ অন্তত একটি সংখ্যা থাকতে হবে।',
        'symbols' => ':attribute-এ অন্তত একটি চিহ্ন থাকতে হবে।',
        'uncompromised' => 'এই :attribute ফাঁস হওয়া তালিকায় আছে। অন্য একটি দিন।',
    ],
    'prohibited' => 'অনুরোধটি গ্রহণ করা যায়নি।',
    'regex' => ':attribute সঠিক ফরম্যাটে নেই।',
    'required' => ':attribute দিতে হবে।',
    'required_with' => ':values দিলে :attribute-ও দিতে হবে।',
    'string' => ':attribute লেখা হতে হবে।',
    'unique' => 'এই :attribute আগেই ব্যবহার হয়েছে।',
    'url' => ':attribute একটি সঠিক লিংক হতে হবে।',

    'custom' => [
        'phone' => [
            'bangladeshi' => 'সঠিক বাংলাদেশি মোবাইল নম্বর দিন (01XXXXXXXXX)।',
        ],
        'transaction_id' => [
            'unique' => 'এই ট্রানজেকশন আইডি আগেই ব্যবহার হয়েছে। আইডিটি আবার দেখুন।',
        ],
    ],

    'attributes' => [
        'consent' => 'সম্মতি',
        'agree_refund' => 'রিফান্ড নীতিতে সম্মতি',
        'phone' => 'ফোন নম্বর',
        'guardian_name' => 'অভিভাবকের নাম',
        'student_name' => 'শিক্ষার্থীর নাম',
        'customer_name' => 'প্রাপকের নাম',
        'shipping_address' => 'ঠিকানা',
        'district' => 'জেলা',
        'quantity' => 'পরিমাণ',
        'payment_method' => 'পেমেন্ট মাধ্যম',
        'sender_number' => 'প্রেরকের নম্বর',
        'transaction_id' => 'ট্রানজেকশন আইডি',
        'course_variation_id' => 'পেমেন্ট প্ল্যান',
        'course_batch_id' => 'ব্যাচ',
        'password' => 'পাসওয়ার্ড',
        'name' => 'নাম',
        'email' => 'ইমেইল',
        'reference' => 'অর্ডার নম্বর',
    ],
];
