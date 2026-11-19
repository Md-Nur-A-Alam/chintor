<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    public const SuperAdmin = 'super_admin';

    public const Manager = 'manager';

    public const Marketing = 'marketing';

    public const SalesExecutive = 'sales_executive';

    public const CourseCoordinator = 'course_coordinator';

    public const Teacher = 'teacher';

    public const Student = 'student';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'label',
        'description',
        'max_discount_percent',
    ];

    /**
     * @return BelongsToMany<Permission, $this>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    protected function casts(): array
    {
        return [
            'max_discount_percent' => 'integer',
        ];
    }
}
