<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * Yeh columns 'data' JSON column mein store hote hain (tenancy ka default)
     * Inhe custom columns ki tarah use karo.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'plan_id',
            'trial_ends_at',
            'is_active',
            'created_at',
            'updated_at',
        ];
    }

    protected $fillable = [
        'id',
        'name',
        'email',
        'plan_id',
        'trial_ends_at',
        'is_active',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'is_active'     => 'boolean',
    ];

    // ─── Relationships ─────────────────────────────────────────

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    // ─── Helper Methods ────────────────────────────────────────

    /**
     * Kya tenant ka trial chal raha hai?
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Kya tenant ka subscription active hai?
     */
    public function hasActivePlan(): bool
    {
        return $this->is_active && $this->plan_id !== null;
    }

    /**
     * Tenant ke plan ki feature limit check karo.
     * Example: $tenant->withinLimit('users', 5)
     */
    public function withinLimit(string $feature, int $count): bool
    {
        if (!$this->plan) return false;

        $limit = $this->plan->getFeatureLimit($feature);

        // -1 matlab unlimited
        if ($limit === -1) return true;

        return $count < $limit;
    }

    /**
     * Tenant ka primary domain
     */
    public function getPrimaryDomainAttribute(): ?string
    {
        return $this->domains()->first()?->domain;
    }
}
