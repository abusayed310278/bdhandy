<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use App\Models\RequirementProposal;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;

#[Fillable([
    'name', 'user_code', 'email', 'phone', 'phone_country_code', 'password',
    'photo', 'gender', 'date_of_birth', 'preferred_language', 'status',
    'email_verified_at', 'phone_verified_at', 'last_login_at', 'last_login_ip',
    'provider', 'provider_user_id', 'access_token', 'refresh_token', 'bio',
    'guide_dismissed',
    'onboarding_profile_done',
    'referred_by',
    'onesignal_player_id',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'phone_verified_at'  => 'datetime',
            'last_login_at'      => 'datetime',
            'date_of_birth'      => 'date',
            'password'           => 'hashed',
            'guide_dismissed'          => 'boolean',
            'onboarding_profile_done' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (User $user) {
            if (empty($user->user_code)) {
                do {
                    $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                } while (static::where('user_code', $code)->exists());
                $user->user_code = $code;
            }
        });

        static::created(function (User $user) {
            $user->notificationPreference()->create([
                'email_enabled'     => true,
                'sms_enabled'       => true,
                'push_enabled'      => true,
                'whatsapp_enabled'  => true,
                'marketing_enabled' => true,
                'event_preferences' => [],
            ]);
        });
    }

    /**
     * Override Laravel's default email-verification notification
     * to use our branded blade template.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    /**
     * Override Laravel's default password-reset notification
     * to use our branded blade template.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function notificationPreference()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function providerProfile()
    {
        return $this->hasOne(ProviderProfile::class);
    }

    public function teamMember()
    {
        return $this->hasOne(TeamMember::class);
    }

    public function isTeamMember(): bool
    {
        return $this->hasRole('team_member');
    }

    public function customerAddresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function primaryAddress()
    {
        return $this->hasOne(CustomerAddress::class)->where('is_primary', true);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'customer_id');
    }

    public function customerRequirements()
    {
        return $this->hasMany(CustomerRequirement::class, 'customer_id');
    }

    public function savedProviders()
    {
        return $this->hasMany(SavedProvider::class, 'customer_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'provider_id');
    }

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->whereIn('subscription_status', ['active', 'grace'])
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest()
            ->first();
    }

    public function pastDueSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('subscription_status', 'past_due')
            ->latest()
            ->first();
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function getSubscriptionPlanAttribute(): ?SubscriptionPlan
    {
        return $this->activeSubscription()?->plan;
    }

    public function canAddServiceArea(): bool
    {
        $plan = $this->subscription_plan;
        if (!$plan) return false;
        
        return $this->providerProfile->serviceAreas()->count() < $plan->service_area_limit;
    }

    public function canAddGalleryItem(): bool
    {
        $plan = $this->subscription_plan;
        if (!$plan) return false;
        
        return $this->providerProfile->gallery()->count() < $plan->gallery_limit;
    }

    public function canProposeLead(): bool
    {
        $plan = $this->subscription_plan;
        if (!$plan) return false;

        $leadsUsedThisMonth = RequirementProposal::where('provider_id', $this->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        return $leadsUsedThisMonth < $plan->lead_limit;
    }

    public function isVerifiedBadgeIncluded(): bool
    {
        return (bool) $this->subscription_plan?->is_verified_badge_included;
    }

    public function hasVerifiedBadge(): bool
    {
        return $this->providerProfile?->is_verified && $this->isVerifiedBadgeIncluded();
    }

    public function isProvider(): bool
    {
        return $this->hasRole(['freelancer', 'business']);
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['super_admin', 'admin', 'moderator', 'support']);
    }

    public function isSocialAuth(): bool
    {
        return !empty($this->provider);
    }

    public function getFullPhoneAttribute(): string
    {
        return ($this->phone_country_code ?? '') . $this->phone;
    }

    public function getDashboardRoute(): string
    {
        if ($this->hasRole(['super_admin', 'admin', 'moderator', 'support'])) {
            return 'admin.dashboard';
        }
        if ($this->hasRole('team_member')) {
            return 'tech.schedule.today';
        }
        if ($this->hasRole(['freelancer', 'business'])) {
            return 'provider.dashboard';
        }
        return 'customer.dashboard';
    }

    public function guideCompletionPercent(): int
    {
        if ($this->isProvider()) {
            $profile = $this->providerProfile;
            $steps = [
                'profile'       => $profile !== null,
                'documents'     => $profile && $profile->documents->count() > 0,
                'approved'      => $profile && $profile->verification_status === 'approved',
                'service_areas' => $profile && $profile->serviceAreas->count() > 0,
                'gallery'       => $profile && $profile->gallery->count() > 0,
            ];
            return (int) (array_sum($steps) / count($steps) * 100);
        }

        if ($this->isCustomer()) {
            $steps = [
                'profile'  => (bool) $this->onboarding_profile_done,
                'address'  => $this->customerAddresses->count() > 0,
            ];
            return (int) (array_sum($steps) / count($steps) * 100);
        }

        return 100;
    }
}
