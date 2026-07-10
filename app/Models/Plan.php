<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'stripe_monthly_price_id',
        'stripe_yearly_price_id',
        'razorpay_monthly_plan_id',
        'razorpay_yearly_plan_id',
        'features',         // JSON: limits per feature
        'is_active',
        'is_free',
        'trial_days',
        'sort_order',
    ];

    protected $casts = [
        'features'      => 'array',
        'is_active'     => 'boolean',
        'is_free'       => 'boolean',
        'price_monthly' => 'decimal:2',
        'price_yearly'  => 'decimal:2',
    ];

    // ─── Relationships ─────────────────────────────────────────

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    // ─── Feature Limits ────────────────────────────────────────

    /**
     * Feature ki limit nikalo.
     * Returns -1 for unlimited, 0 for not allowed.
     *
     * Usage: $plan->getFeatureLimit('users') → 5
     */
    public function getFeatureLimit(string $feature): int
    {
        return $this->features[$feature] ?? 0;
    }

    /**
     * Kya yeh feature is plan mein allowed hai?
     */
    public function hasFeature(string $feature): bool
    {
        $limit = $this->getFeatureLimit($feature);
        return $limit !== 0;
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // ─── Helpers ───────────────────────────────────────────────

    public function getFormattedPriceAttribute(): string
    {
        if ($this->is_free) return 'Free';
        return '$' . number_format($this->price_monthly, 0) . '/mo';
    }
}
