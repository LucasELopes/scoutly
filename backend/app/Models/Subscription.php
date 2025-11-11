<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory, HasUuids;

    public const PLAN_FREE = 'free';
    public const PLAN_STARTER = 'starter';
    public const PLAN_PRO = 'pro';

    protected $fillable = [
        'user_id',
        'plan',
        'active_until'
    ];

    public static function getPlans(): array {
        return [
            self::PLAN_FREE,
            self::PLAN_STARTER,
            self::PLAN_PRO
        ];
    }
}
