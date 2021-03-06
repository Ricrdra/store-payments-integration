<?php

namespace App\Models;

use App\Enums\UserRoles;
use App\Http\Controllers\CartController;
use Dflydev\DotAccessData\Data;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    private $totalSpent;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin(): bool
    {
        return $this->role->description == UserRoles::ADMIN;
    }

    public function getOrdersQuantityAttribute(): int
    {
        return $this->orders->count();
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function currentCart(): HasOne
    {

        if ($this->carts === null || $this->carts->count() === 0) {
            CartController::createCart();
        }
        return $this->hasOne(Cart::class)->latestOfMany();
    }


    public function getTotalSpentAttribute(): array
    {
        $orders = $this->orders->toArray();
        $this->totalSpent = [];
        array_map(function ($order) {
            $currency = $order['currency'];
            $this->totalSpent[$currency] = $this->totalSpent[$currency] ?? 0;
            $this->totalSpent[$currency] += $order['total'];
        }, $orders);;

        return $this->totalSpent;
    }
}
