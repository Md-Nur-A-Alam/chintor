<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use App\Support\PhoneNumber;
use Database\Seeders\RoleSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Create (or promote) a super admin. Details are typed at the prompt, so
 * personal data and passwords never have to live in a file.
 */
class CreateSuperAdmin extends Command
{
    protected $signature = 'chintor:create-super-admin
                            {--name= : Full name}
                            {--phone= : Bangladeshi mobile number (login ID)}
                            {--email= : Email address (optional)}
                            {--password= : Password (prompted if omitted)}
                            {--office-id= : Office ID, e.g. CNT-01}
                            {--designation= : Designation, e.g. CEO}';

    protected $description = 'Create a super admin account for the Chintor admin panel';

    private const MINIMUM_PRODUCTION_PASSWORD_LENGTH = 10;

    public function handle(): int
    {
        $this->callSilently('db:seed', ['--class' => RoleSeeder::class, '--force' => true]);

        $details = [
            'name' => $this->option('name') ?? text('Full name', required: true),
            'phone' => $this->option('phone') ?? text('Mobile number (login ID)', placeholder: '01XXXXXXXXX', required: true),
            'email' => $this->option('email') ?? text('Email (optional)'),
            'password' => $this->option('password') ?? password('Password', required: true),
            'office_id' => $this->option('office-id') ?? text('Office ID (optional)', placeholder: 'CNT-01'),
            'designation' => $this->option('designation') ?? text('Designation (optional)'),
        ];

        $details['phone'] = PhoneNumber::normalizeBangladeshMobile($details['phone']) ?? $details['phone'];

        $minimumLength = $this->laravel->isProduction() ? self::MINIMUM_PRODUCTION_PASSWORD_LENGTH : 6;

        $validator = Validator::make($details, [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^\+8801[3-9]\d{8}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', "min:{$minimumLength}"],
            'office_id' => ['nullable', 'string', 'max:20'],
            'designation' => ['nullable', 'string', 'max:255'],
        ], [
            'phone.regex' => 'The phone must be a valid Bangladeshi mobile number.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $admin = User::withTrashed()->updateOrCreate(
            ['phone' => $details['phone']],
            [
                'name' => $details['name'],
                'email' => $details['email'] ?: null,
                'password' => $details['password'],
                'type' => User::TypeStaff,
                'office_id' => $details['office_id'] ?: null,
                'designation' => $details['designation'] ?: null,
                'is_active' => true,
                'deleted_at' => null,
            ],
        );

        $admin->roles()->syncWithoutDetaching([Role::where('name', Role::SuperAdmin)->value('id')]);

        $this->components->info("Super admin ready: {$admin->name} ({$admin->phone}). Sign in at /admin/login.");

        return self::SUCCESS;
    }
}
