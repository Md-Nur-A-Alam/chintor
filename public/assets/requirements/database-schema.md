# চিন্তর — ডেটাবেস স্কিমা (প্রস্তাবিত, v0.1)

**তারিখ:** 2026-11-20 · **ফেজ:** ০ · **স্ট্যাটাস:** ⏳ খোলা প্রশ্ন তিনটির সিদ্ধান্ত হয়েছে (§৮)। পুরো স্কিমার চূড়ান্ত অনুমোদনের পর মাইগ্রেশন লেখা হবে।
**ডেটাবেস:** লোকালে SQLite, প্রোডাকশনে (Hostinger) MySQL 8। স্কিমা দুটোতেই চলবে।

---

## ০. ডিজাইন নীতি

| নীতি | সিদ্ধান্ত | কারণ |
|---|---|---|
| **LMS-রেডি** | ফেজ ১-এর টেবিল এমনভাবে বানানো যে LMS এলে শুধু **নতুন টেবিল যোগ** হবে, পুরোনো ভাঙবে না | §১১ |
| আইডি | ভেতরে `id` (bigint)। বাইরে দেখানোর জন্য `ulid` (লিড, অর্ডার, এনরোলমেন্ট, পেমেন্ট) + মানুষের পড়ার মতো `reference` (যেমন `CH-O-2611-0042`) | URL-এ সিরিয়াল নম্বর ফাঁস হয় না, ফোনে বলাও সহজ |
| টাকা | `unsigned integer`, **পুরো টাকায়** (পয়সা নয়) | বাংলাদেশে দাম পুরো টাকায়; গেটওয়ে এলে রূপান্তর সহজ |
| অনুবাদ | অনুবাদযোগ্য ফিল্ড **JSON**: `{"bn": "...", "en": "..."}` | বাড়তি প্যাকেজ ছাড়াই BN/EN; ভাষা যোগ করা সহজ (§৯.২) |
| স্লাগ | `slug` একটাই (ইংরেজি/ল্যাটিন, যেমন `math-olympiad-primary`) | দুই ভাষায় একই URL অংশ, শেয়ার করা সহজ |
| ফোন | সবসময় E.164 (`+8801XXXXXXXXX`) হিসেবে সেভ | ডুপ্লিকেট লিড ধরা যায়, WhatsApp লিংক বানানো যায় |
| স্ট্যাটাস | `string` কলাম + PHP **Enum** (DB enum নয়) | নতুন স্ট্যাটাস যোগ করতে মাইগ্রেশন লাগে না |
| মুছে ফেলা | ক্যাটালগ, লিড, অর্ডার, পেমেন্টে **soft delete** | ভুলে মুছে গেলেও ইতিহাস হারায় না |
| অডিট | গুরুত্বপূর্ণ পরিবর্তন `activity_log`-এ (কে, কখন, কী বদলেছে) | পেমেন্ট ভেরিফাই, ডিসকাউন্ট ও রিফান্ডের জবাবদিহি |
| ফাইল | ছবি/অডিওর `path` + `disk` (local → পরে Cloudflare R2) | ডিস্ক বদলালে কোড বদলাতে হবে না (§১০) |

**চিহ্ন:** 🟢 ফেজ ১-এ তৈরি · 🔵 ফেজ ২ (LMS), এখন শুধু পরিকল্পনা · `?` = nullable · `[bn/en]` = অনুবাদযোগ্য JSON

---

## ১. মানুষ ও অ্যাক্সেস

### 🟢 `users` — স্টাফ লগইন (এখন), পরে স্টুডেন্ট/অভিভাবক/শিক্ষক লগইন
| কলাম | টাইপ | নোট |
|---|---|---|
| id, name, email? (unique), password, remember_token, timestamps | — | Laravel ডিফল্ট; ✅ ইমেইল এখন ঐচ্ছিক |
| phone? | string, unique | E.164। ✅ **স্টাফ লগইন = ফোন + পাসওয়ার্ড** |
| type | string | `staff` এখন; LMS-এ `student`, `guardian`, `teacher` |
| office_id? | string, unique | ✅ যেমন `CNT-01` |
| designation? | string | ✅ যেমন "CEO, Chintor" |
| joined_on?, date_of_birth? | date | ✅ |
| is_active | bool | নিষ্ক্রিয় স্টাফ লগইন করতে পারবে না; সেশন চলাকালীন নিষ্ক্রিয় হলে লগআউট হয়ে যাবে |
| last_login_at? | timestamp | |

> ✅ **তৈরি হয়েছে (2026-11-20):** `users`-এর স্টাফ কলাম, `roles`, `permissions` ও pivot টেবিল, ৫টি রোল, প্রথম সুপার অ্যাডমিন (তথ্য `.env` থেকে), আর `/admin/login` (প্রতি ফোন + IP-তে ৫ বার ভুল চেষ্টার পর লক)।
| two_factor_secret?, two_factor_confirmed_at? | — | 2FA (ঐচ্ছিক, পরে) |

### 🟢 `roles`, `permissions`, `role_user`, `permission_role`
রোল: `super_admin`, `manager`, `sales_executive`, `order_staff`, `content_editor`। পারমিশন উদাহরণ: `leads.assign`, `payments.verify`, `coupons.create`, `teachers.edit_html`।
`roles.max_discount_percent?` — সেলস রোলের সর্বোচ্চ ডিসকাউন্ট সীমা (§৫.২)।
> বাড়তি প্যাকেজ (spatie/permission) না নিয়ে ছোট নিজস্ব টেবিল রাখছি। চাইলে পরে প্যাকেজে যাওয়া যাবে।

### 🟢 `guardians` — অভিভাবক (লগইন ছাড়াই রেকর্ড)
| কলাম | টাইপ | নোট |
|---|---|---|
| id, ulid | | |
| name | string | |
| phone | string, **unique** | E.164, মূল পরিচয় |
| whatsapp? | string | আলাদা হলে |
| email? | string | বিদেশি অভিভাবকের ইমেইল OTP-র জন্য (§১৬/৮) |
| country_code | string(2) | ডিফল্ট `BD` |
| user_id? | FK users | LMS লগইন চালু হলে যুক্ত হবে |
| phone_verified_at? | timestamp | OTP চালু হলে |
| timestamps, soft deletes | | |

### 🟢 `students`
| কলাম | টাইপ | নোট |
|---|---|---|
| id, ulid | | |
| guardian_id | FK guardians | একজন অভিভাবকের একাধিক সন্তান থাকতে পারে |
| name | string | |
| class_level? | tinyint | ৩–১০ (প্রি-স্কুল হলে ০) |
| birth_year? / age? | smallint / tinyint | উইজার্ডে বয়স নেওয়া হয় |
| school_name? | string | |
| assessed_level? | string | `beginner` / `intermediate` / `advanced` (অ্যাসেসমেন্ট থেকে) |
| user_id? | FK users | LMS লগইন |
| timestamps, soft deletes | | |

---

## ২. ক্যাটালগ

### 🟢 `programs` — বড় ভাগ (কোর্স গ্রুপ)
যেমন: ম্যাথ অলিম্পিয়াড · ৯–১০ বিজ্ঞান · ৯–১০ ব্যবসায় শিক্ষা · (পরে) সায়েন্স অলিম্পিয়াড
| কলাম | টাইপ |
|---|---|
| id, slug (unique), name `[bn/en]`, description? `[bn/en]`, icon?, sort_order, is_active, timestamps |

### 🟢 `courses`
| কলাম | টাইপ | নোট |
|---|---|---|
| id, slug (unique) | | |
| program_id | FK programs | |
| title `[bn/en]`, subtitle? `[bn/en]` | json | |
| class_from, class_to | tinyint | যেমন ৩–৫ |
| age_from?, age_to? | tinyint | |
| summary `[bn/en]` | json | কার্ডে |
| description? `[bn/en]` | json (rich) | "কোর্স সম্পর্কে বিস্তারিত" |
| learning_outcomes? `[bn/en]` | json array | "যা শিখবে" বুলেট |
| features? | json array | স্ট্যাট ব্লক: `[{icon, label[bn/en], value[bn/en]}]` (§৩.৫) |
| delivery_modes | json array | `["batch", "one_to_one"]` |
| explainer_video_url? | string | YouTube Unlisted। **না থাকলে প্রকাশ করা যাবে না** (§১৬/১৭) |
| thumbnail_path?, cover_path?, media_disk | string | |
| status | string | `draft` / `published` / `archived` |
| published_at? | timestamp | |
| is_featured, sort_order | | |
| seo_title? `[bn/en]`, seo_description? `[bn/en]` | json | |
| refund_policy? | json | ফাঁকা = গ্লোবাল ডিফল্ট (§৪ রিফান্ড নীতি) |
| timestamps, soft deletes | | |

### 🟢 `course_variations` — প্রাইসিং ভ্যারিয়েশন (§৩.৩)
| কলাম | টাইপ | নোট |
|---|---|---|
| id, course_id | | |
| label `[bn/en]` | json | "মাসিক", "৩ মাস", "ফুল কোর্স" |
| duration_months? | tinyint | |
| billing_type | string | `one_time` / `monthly` / `installments` |
| installment_count? | tinyint | কিস্তি থাকলে |
| regular_price | uint | **মেইন প্রাইস (বাধ্যতামূলক)** |
| discounted_price? | uint | ঐচ্ছিক; অবশ্যই `< regular_price` (ভ্যালিডেশন) |
| badge? `[bn/en]` | json | "সেরা মূল্য" |
| is_active, sort_order, timestamps | | |

### 🟢 `course_batches` — লাইভ ব্যাচ (§১৬/৬)
| কলাম | টাইপ | নোট |
|---|---|---|
| id, course_id, code | | যেমন `MO-P-2601` |
| name? `[bn/en]` | json | "সন্ধ্যার ব্যাচ" |
| class_days | json | `["sat","mon","wed"]` |
| class_start_time, class_end_time | time | |
| orientation_at? | datetime | |
| starts_on, ends_on? | date | |
| enrollment_deadline? | date | |
| seat_limit? | smallint | null = সীমা নেই |
| seats_taken | smallint | এনরোলমেন্ট কনফার্মে বাড়ে (ক্যাশ করা) |
| status | string | `upcoming` / `enrolling` / `full` / `running` / `completed` / `cancelled` |
| meeting_url? | string | জুম (LMS-এ ক্লাস-প্রতি আলাদা হবে) |
| timestamps, soft deletes | | |

### 🟢 `teachers` (§৩.৪)
| কলাম | টাইপ | নোট |
|---|---|---|
| id, slug (unique) | | |
| name `[bn/en]`, designation `[bn/en]` | json | |
| photo_path?, media_disk | | |
| summary `[bn/en]` | json | কার্ডে ও SEO-র জন্য |
| details_mode | string | `rich` / `html` |
| details_rich? `[bn/en]` | json | নিরাপদ ফরম্যাটেড লেখা |
| details_html? | longtext | কাস্টম HTML/CSS/JS, **sandboxed iframe-এ** দেখাবে |
| user_id? | FK users | LMS-এ শিক্ষক লগইন |
| is_active, sort_order, timestamps, soft deletes | | |

### 🟢 `course_teacher` (pivot) — `course_id`, `teacher_id`, `role?` (`lead` / `assistant`), `sort_order`
### 🟢 `batch_teacher` (pivot) — কোন ব্যাচে কোন শিক্ষক

### 🟢 `books`
| কলাম | টাইপ | নোট |
|---|---|---|
| id, slug (unique), isbn? | | |
| title `[bn/en]`, subtitle? `[bn/en]`, description? `[bn/en]` | json | |
| program_id?, class_from?, class_to? | | ফিল্টারের জন্য |
| cover_path, media_disk | | |
| table_of_contents? `[bn/en]` | json | |
| pages?, edition?, published_year?, language | | |
| regular_price, discounted_price? | uint | একই প্রাইস নিয়ম |
| stock_quantity | int | |
| weight_grams? | smallint | ভবিষ্যতে কুরিয়ার চার্জ |
| status, published_at?, is_featured, sort_order | | |
| seo_title? / seo_description? `[bn/en]` | json | |
| timestamps, soft deletes | | |

### 🟢 `authors` + `author_book` (pivot) — শিক্ষকদের মতো কাঠামো (name, slug, photo, bio `[bn/en]`, `teacher_id?`। একই ব্যক্তি শিক্ষক ও লেখক হলে লিংক থাকবে)

### 🟢 `media` (polymorphic গ্যালারি)
`mediable_type`, `mediable_id`, `collection` (`sample_pages`, `gallery`), `disk`, `path`, `alt` `[bn/en]`, `width`, `height`, `sort_order`। বইয়ের "ভেতরে দেখুন" পাতা, কোর্সের ছবি।

---

## ৩. লিড ও সেলস (গ্রোথ ইঞ্জিন)

### 🟢 `leads`
| কলাম | টাইপ | নোট |
|---|---|---|
| id, ulid, reference | | `CH-L-2611-0042` |
| type | string | `wizard`, `callback`, `assessment`, `waitlist`, `abandoned_checkout`, `contact` |
| status | string | `new` → `contacted` → `interested` → `follow_up` → `payment_pending` → `enrolled` / `lost` |
| student_name?, guardian_name? | string | |
| phone | string | E.164 (বাধ্যতামূলক) |
| whatsapp?, email? | string | |
| class_level?, age? | tinyint | |
| message? | text | |
| guardian_id?, student_id? | FK | কনভার্ট হলে যুক্ত হয় |
| assigned_to? | FK users | সেলস এক্সিকিউটিভ |
| lost_reason? | string | |
| next_follow_up_at? | datetime | ড্যাশবোর্ডে রিমাইন্ডার |
| source | string | `website`, `facebook`, `whatsapp`, `referral`, `walk_in`, `phone` |
| landing_page?, referrer? | string | |
| utm_source?, utm_medium?, utm_campaign?, utm_content?, utm_term? | string | ক্যাম্পেইন রিপোর্ট (§৭) |
| locale | string(5) | কোন ভাষায় ফর্ম পূরণ |
| consent_at? | timestamp | সম্মতি চেকবক্স |
| ip_address?, user_agent? | | স্প্যাম বিশ্লেষণ |
| timestamps, soft deletes | | |

> **ডুপ্লিকেট:** একই ফোন থেকে নতুন লিড এলে আগের খোলা লিডেই নতুন `lead_activity` যোগ হবে (সেলস টিমের কাছে একই মানুষের দুটো কার্ড আসবে না)।

### 🟢 `lead_interests` — একাধিক কোর্স আগ্রহ (§১৬/১৮)
`lead_id`, `course_id?`, `book_id?`, `program_id?`

### 🟢 `lead_activities` — টাইমলাইন
`lead_id`, `user_id?`, `type` (`note`, `call`, `whatsapp`, `status_change`, `assignment`, `discount_offered`, `form_resubmitted`), `outcome?` (`answered`, `no_answer`, `busy`, `callback_requested`), `body?`, `meta?` json, `created_at`।
কল লগ, নোট আর অ্যাসাইনমেন্টের ইতিহাস এক টেবিলে থাকবে, তাই আলাদা `call_logs` টেবিল লাগবে না।

### 🟢 `assessments` — ফ্রি ১:১ অ্যাসেসমেন্ট (§৩.৩.১)
| কলাম | টাইপ | নোট |
|---|---|---|
| id, ulid, lead_id, student_id? | | |
| course_id? | FK | কোন কোর্সের জন্য |
| teacher_id? | FK teachers | |
| scheduled_by? | FK users | সেলস এক্সিকিউটিভ |
| status | string | `requested` → `scheduled` → `completed` / `no_show` / `cancelled` |
| scheduled_at?, duration_minutes?, meeting_url? | | |
| result_level? | string | `beginner` / `intermediate` / `advanced` |
| teacher_notes? | text | |
| recommended_course_id?, recommended_batch_id? | FK | |
| report_sent_at? | timestamp | অভিভাবককে রিপোর্ট পাঠানো হলে |
| timestamps | | |

---

## ৪. কমার্স: এনরোলমেন্ট, অর্ডার, পেমেন্ট

### 🟢 `enrollments` — **LMS অ্যাক্সেসের ভিত্তি**
| কলাম | টাইপ | নোট |
|---|---|---|
| id, ulid, reference | | `CH-E-2611-0007` |
| student_id, guardian_id | FK | |
| course_id, course_batch_id, course_variation_id | FK | |
| lead_id? | FK | কোন লিড থেকে কনভার্ট হলো (রিপোর্ট) |
| sold_by? | FK users | সেলস কমিশন/রিপোর্ট |
| status | string | `pending_payment` → `active` → `completed` / `refund_requested` / `refunded` / `cancelled` |
| list_price, discount_amount, payable_amount | uint | ভর্তির সময়ের দাম **স্থির** থাকবে (পরে দাম বদলালেও) |
| coupon_id? | FK | |
| activated_at?, first_class_at?, ends_at? | | রিফান্ডের দিন গোনা শুরু হবে `first_class_at` থেকে (ব্যাচের `starts_on` থেকে অটো-ফিল, অ্যাডমিন বদলাতে পারবে) |
| refund_policy | json | **ভর্তির সময়ের নীতির স্ন্যাপশট:** `{mode, window, percent}`। পরে নীতি বদলালেও আগের শিক্ষার্থীর প্রতিশ্রুতি অপরিবর্তিত থাকবে |
| refund_eligible_until? | datetime | `days` মোডে হিসাব করা শেষ সময় (রিপোর্ট ও ড্যাশবোর্ডের জন্য) |
| timestamps, soft deletes | | |

### 🟢 `installments` — মাসিক/কিস্তি
`enrollment_id`, `sequence`, `amount`, `due_on`, `status` (`due` / `paid` / `overdue` / `waived`), `paid_at?`, `payment_id?`। বকেয়া তালিকা আর রিমাইন্ডার এখান থেকে আসবে।

### 🟢 `orders` — বইয়ের অর্ডার (লগইন ছাড়া)
| কলাম | টাইপ | নোট |
|---|---|---|
| id, ulid, reference | | `CH-O-2611-0042`, ট্র্যাকিংয়ের জন্য |
| guardian_id? | FK | |
| customer_name, phone, alt_phone? | | |
| shipping_address, district, upazila?, postal_code? | | |
| delivery_zone_id | FK | ঢাকার ভেতরে/বাইরে |
| status | string | `pending_verification` → `confirmed` → `packed` → `shipped` → `delivered` / `cancelled` / `returned` |
| subtotal, discount_amount, delivery_charge, total | uint | |
| coupon_id?, lead_id? | FK | |
| notes?, staff_notes? | text | |
| locale, ip_address?, user_agent?, utm_* ? | | |
| timestamps, soft deletes | | |

### 🟢 `order_items` — `order_id`, `book_id`, `title_snapshot`, `unit_price`, `quantity`, `line_total`
### 🟢 `delivery_zones` — `name` `[bn/en]`, `charge`, `districts` json (`["Dhaka"]`), `estimated_days`, `is_default`, `is_active`। প্রথমে ২টি জোন থাকবে।
### 🟢 `shipments` — `order_id`, `courier` (`steadfast`, `pathao`, `redx`, `sundarban`, `other`), `tracking_code?`, `consignment_id?`, `cod_amount`, `status`, `shipped_at?`, `delivered_at?`, `meta?` json (পরে কুরিয়ার API)

### 🟢 `payments` — সেমি-অটো পেমেন্ট (§৫.৫)
| কলাম | টাইপ | নোট |
|---|---|---|
| id, ulid, reference | | |
| payable_type, payable_id | morph | `Order` বা `Enrollment` (বা `Installment`) |
| method | string | `bkash`, `nagad`, `upay`, `bank_transfer`, `cash`, `gateway` |
| provider | string | `manual` এখন; পরে `bkash_pgw`, `sslcommerz`, `aamarpay` |
| amount | uint | |
| sender_number? | string | |
| transaction_id? | string, **unique (method + transaction_id)** | একই TrxID দুবার দেওয়া যাবে না |
| receipt_path? | string | ব্যাংক রসিদের ছবি |
| status | string | `pending` → `verified` / `rejected` / `refunded` |
| submitted_by? | string | `customer` / `staff` |
| verified_by?, verified_at?, rejection_reason? | | |
| gateway_payload? | json | ভবিষ্যৎ গেটওয়ের রেসপন্স |
| timestamps, soft deletes | | |

### 🟢 `payment_accounts` — যেসব নম্বর/অ্যাকাউন্টে টাকা নেওয়া হয়
`method`, `account_type` (`personal` / `merchant` / `agent` / `bank`), `account_name`, `account_number`, `bank_name?`, `branch?`, `instructions` `[bn/en]`, `is_active`, `sort_order`।
চেকআউটে এখান থেকেই দেখানো হবে, তাই নম্বর বদলাতে ডেপ্লয় লাগবে না।

### 🟢 রিফান্ড নীতি (অ্যাডমিন-নিয়ন্ত্রিত)
| স্তর | কোথায় | বিবরণ |
|---|---|---|
| গ্লোবাল ডিফল্ট | `settings` → `refund_policy` = `{"mode": "days", "window": 7, "percent": 70}` | সব কোর্সে প্রযোজ্য |
| কোর্সভিত্তিক (ঐচ্ছিক) | `courses.refund_policy?` json | ফাঁকা থাকলে গ্লোবাল ডিফল্ট; দিলে সেই কোর্সে ওভাররাইড |
| এনরোলমেন্ট | `enrollments.refund_policy` | ভর্তির মুহূর্তের স্ন্যাপশট (চূড়ান্ত) |

- `mode`: `days` | `classes`। `classes` মোড LMS-এর উপস্থিতি ট্র্যাকিং চালু না হওয়া পর্যন্ত অ্যাডমিন ফর্মে **disabled**।
- `percent`: ০–১০০ ভ্যালিডেশন। কোর্স পেজ ও চেকআউটের রিফান্ড লাইন এই মান থেকেই তৈরি হবে, হার্ডকোডেড "৭০%" নয়।

### 🟢 `refunds` (§৩.৩.১)
`enrollment_id?`, `order_id?`, `payment_id?`, `requested_amount`, `approved_amount?` (এনরোলমেন্টের নীতির % অনুযায়ী অটো-হিসাব, অ্যাডমিন বদলাতে পারবে), `policy_snapshot` json, `reason?`, `status` (`requested` / `approved` / `rejected` / `paid`), `requested_at`, `processed_by?`, `processed_at?`, `method?`, `transaction_id?`

### 🟢 `coupons` + `coupon_redemptions`
`coupons`: `code` (unique), `type` (`percent` / `fixed`), `value`, `max_discount?`, `applies_to` (`all`, `courses`, `books`, `course:<id>`), `min_amount?`, `usage_limit?`, `per_phone_limit?`, `starts_at?`, `expires_at?`, `created_by` (সেলস কে বানালো), `lead_id?` (এক লিডের জন্য ব্যক্তিগত কুপন), `is_active`।
`coupon_redemptions`: `coupon_id`, `redeemable` (morph: order/enrollment), `phone`, `discount_amount`।

### 🟢 `otp_verifications` (OTP এখন বন্ধ; টেবিল প্রস্তুত, §১৬/৮)
`channel` (`sms` / `email`), `destination`, `code_hash`, `purpose` (`order`, `enrollment`, `login`), `attempts`, `expires_at`, `verified_at?`, `ip_address?`

---

## ৫. কনটেন্ট ও সেটিংস

| টেবিল | কলাম (সংক্ষেপে) |
|---|---|
| 🟢 `pages` | `slug`, `title` `[bn/en]`, `body` `[bn/en]`, `type` (`policy`, `about`, `landing`), `seo_*`, `status` |
| 🟢 `faqs` | `faqable_type?/id?` (নির্দিষ্ট কোর্স/বই, বা null = সাধারণ), `question` `[bn/en]`, `answer` `[bn/en]`, `sort_order`, `is_active` |
| 🟢 `testimonials` | `author_name`, `author_role` `[bn/en]` (যেমন "অভিভাবক, ৫ম শ্রেণি"), `body` `[bn/en]`, `rating?`, `photo_path?`, `video_url?`, `course_id?`, `consent_confirmed` (bool, **বাধ্যতামূলক**), `is_published` — শুধু আসল রিভিউ |
| 🟢 `blog_posts` + `blog_categories` | `slug`, `title`/`excerpt`/`body` `[bn/en]`, `cover_path`, `author_id`, `published_at`, `seo_*` |
| 🟢 `banners` | `placement`, `title` `[bn/en]`, `image_path`, `link_url`, `starts_at`, `ends_at`, `is_active` |
| 🟢 `settings` | `key` (unique), `value` json — হটলাইন, WhatsApp, অফিসের সময়, ট্রেড লাইসেন্স, সোশ্যাল লিংক (এখন `config/chintor.php` থেকে, পরে এখানে) |
| 🟢 `activity_log` | `user_id?`, `subject_type/id`, `event`, `properties` json, `created_at` |

---

## ৬. 🔵 LMS (ফেজ ২): এখন শুধু পরিকল্পনা, মাইগ্রেশন পরে

সব LMS টেবিল ফেজ ১-এর `courses`, `course_batches`, `enrollments`, `students`, `teachers`-এর সাথে যুক্ত হবে। ফেজ ১-এর কোনো টেবিল বদলাতে হবে না।

| টেবিল | উদ্দেশ্য |
|---|---|
| `course_sections` | ফোল্ডার/সেকশন: `course_id`, `batch_id?` (ব্যাচ-নির্দিষ্ট কনটেন্ট), **`parent_id?` (সাবসেকশনের জন্য নেস্টেড)**, `title` `[bn/en]`, `sort_order`, `unlock_at?` |
| `activities` | `section_id`, `type` (`video`, `live_class`, `zoom_class`, `assignment`, `homework`, `quiz`, `pdf`, `audio`, `note`), `title` `[bn/en]`, `content` json (টাইপ অনুযায়ী: ইউটিউব আইডি, Drive লিংক, R2 অডিও পাথ…), `points`, `due_at?`, `available_from?`, `is_preview` (ফ্রি স্যাম্পল) |
| `live_sessions` | `activity_id`, `batch_id`, `starts_at`, `meeting_url`, `recording_url?` |
| `attendance` | `live_session_id`, `student_id`, `status`, `joined_at?` |
| `activity_progress` | `activity_id`, `student_id`, `status`, `score?`, `completed_at?` |
| `submissions` | `activity_id`, `student_id`, `body?`, `file_path?`, `score?`, `feedback?`, `graded_by?` |
| `quizzes` / `questions` / `question_options` / `quiz_attempts` / `attempt_answers` | কুইজ ও মক টেস্ট |
| `point_transactions` | `student_id`, `course_id`, `batch_id?`, `points`, `reason`, `source` (morph) — **লিডারবোর্ড এখান থেকে হিসাব হবে** |
| `badges` / `badge_student` | গেমিফিকেশন |
| `announcements` | `course_id` / `batch_id`, `body` |
| `certificates` | `enrollment_id`, `code` (unique, ভেরিফিকেশনের জন্য), `issued_at` |

> লিডারবোর্ড আলাদা টেবিলে জমা থাকবে না। `point_transactions` থেকে হিসাব করে ক্যাশ করা হবে, যাতে পয়েন্ট সবসময় যাচাই করা যায়।

---

## ৭. মূল সম্পর্ক (সংক্ষেপে)

```
programs ─┬─< courses ─┬─< course_variations
          │            ├─< course_batches ─< enrollments >─ students >─ guardians
          │            ├─<> teachers (course_teacher)
          │            └─< faqs, media
          └─< books ─<> authors
                 └─< order_items >─ orders ─< shipments
                                      │
payments (morph: orders | enrollments | installments)
leads ─< lead_interests >─ courses/books
leads ─< lead_activities, ─< assessments >─ teachers
enrollments ─< installments, ─< refunds
🔵 courses ─< course_sections (nested) ─< activities ─< progress / submissions
```

---

## ৮. রিভিউয়ের জন্য প্রশ্ন

1. ~~রিফান্ডের সময় গণনা?~~ ✅ **সিদ্ধান্ত (2026-11-20):** পুরোটাই অ্যাডমিন থেকে নিয়ন্ত্রণযোগ্য। দেখুন §৪-এর "রিফান্ড নীতি"।
   - **মোড:** `days` (দিন) অথবা `classes` (প্রথম N-টি ক্লাস)। LMS না আসা পর্যন্ত ক্লাস গোনা সম্ভব নয়, তাই `classes` অপশন অ্যাডমিনে **নিষ্ক্রিয় (disabled)** থাকবে।
   - **দিন গণনা শুরু:** প্রথম ক্লাসের দিন (`first_class_at`) থেকে।
   - **সময়সীমা (দিন/ক্লাস সংখ্যা) ও রিফান্ড %:** অ্যাডমিন সেট করবে (ডিফল্ট: ৭ দিন, ৭০%)।
2. ~~রোল/পারমিশন?~~ ✅ **সিদ্ধান্ত (2026-11-20):** নিজস্ব ছোট টেবিল (`roles`, `permissions`, pivot) + Laravel Gate/Policy। কোনো প্যাকেজ নয়।
3. ~~অ্যাডমিন প্যানেল?~~ ✅ **সিদ্ধান্ত (2026-11-20):** নিজে **Blade + Tailwind + Alpine** দিয়ে বানানো হবে (Filament নয়)। পাবলিক সাইটের একই ডিজাইন সিস্টেম ব্যবহার হবে। পুনর্ব্যবহারযোগ্য অ্যাডমিন কম্পোনেন্ট (ডেটা টেবিল, ফিল্টার, ফর্ম ফিল্ড, স্ট্যাটাস ব্যাজ, BN/EN ট্যাব-ইনপুট) আগে তৈরি হবে, যাতে প্রতিটি মডিউল দ্রুত বানানো যায়।
