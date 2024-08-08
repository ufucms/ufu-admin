<?php

namespace Slowlyo\OwlAdmin\Models;

use Slowlyo\OwlAdmin\Admin;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User;
use Stancl\VirtualColumn\VirtualColumn;
use Slowlyo\OwlAdmin\Traits\StaticTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Slowlyo\OwlAdmin\Traits\DatetimeFormatterTrait;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class AdminUser extends User implements AuthenticatableContract
{
    use Authenticatable, HasApiTokens, VirtualColumn, StaticTrait, DatetimeFormatterTrait;

    protected $appends = ['administrator'];
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->setConnection(Admin::config('admin.database.connection'));

        parent::__construct($attributes);
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'username',
            'password',
            'mobile',
            'name',
            'gender',
            'birthday',
            'email',
            'avatar',
            'state',
            'reason',
            'remember_token',
            'memo',
    	    'created_at',
    	    'updated_at',
        ];
    }

    /**
     * 性别选项
     * 
    **/
    public static $genderDef = 0;
    public static $genderOpt = [
        [
            'label' => '未知',
            'value' => 0,
            'color' => '#303540',
            'icon'  => 'fa fa-genderless',
        ],
        [
            'label' => '男',
            'value' => 1,
            'color' => '#2468f2',
            'icon'  => 'fa fa-mars',
        ],
        [
            'label' => '女',
            'value' => 2,
            'color' => '#f23d3d',
            'icon'  => 'fa fa-venus',
        ],
    ];

    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_role_users', 'user_id', 'role_id')->withTimestamps();
    }

    public function avatar(): Attribute
    {
        $storage = \Illuminate\Support\Facades\Storage::disk(Admin::config('admin.upload.disk'));

        return Attribute::make(
            get: fn($value) => $value ? admin_resource_full_path($value) : url(Admin::config('admin.default_avatar')),
            set: fn($value) => str_replace($storage->url(''), '', $value)
        );
    }

    protected static function boot(): void
    {
        parent::boot();
        static::deleting(function (AdminUser $model) {
            $model->roles()->detach();
        });
    }

    public function allPermissions(): Collection
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten();
    }


    public function can($abilities, $arguments = []): bool
    {
        if (empty($abilities)) {
            return true;
        }

        if ($this->isAdministrator()) {
            return true;
        }

        return $this->roles->pluck('permissions')->flatten()->pluck('slug')->contains($abilities);
    }

    public function isAdministrator(): bool
    {
        return $this->isRole('administrator');
    }

    public function isRole(string $role): bool
    {
        return $this->roles->pluck('slug')->contains($role);
    }

    public function inRoles(array $roles = []): bool
    {
        return $this->roles->pluck('slug')->intersect($roles)->isNotEmpty();
    }

    public function visible(array $roles = []): bool
    {

        if ($this->isAdministrator()) {
            return true;
        }
        if (empty($roles)) {
            return false;
        }
        $roles = array_column($roles, 'slug');

        return $this->inRoles($roles);
    }

    public function administrator(): Attribute
    {
        return Attribute::get(fn() => $this->isAdministrator());
    }
}
