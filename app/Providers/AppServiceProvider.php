<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\CustomerRequirement;
use App\Models\PaymentTransaction;
use App\Models\RequirementProposal;
use App\Models\Review;
use App\Models\ServiceRequest;
use App\Models\SubscriptionInvoice;
use App\Models\SupportTicket;
use App\Models\TeamJobAssignment;
use App\Observers\CustomerRequirementObserver;
use App\Observers\PaymentTransactionObserver;
use App\Observers\RequirementProposalObserver;
use App\Observers\ReviewObserver;
use App\Observers\ServiceRequestObserver;
use App\Observers\SubscriptionInvoiceObserver;
use App\Observers\SupportTicketObserver;
use App\Observers\TeamJobAssignmentObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // ── Website nav categories ──────────────────────────────────────────
        View::composer('layouts.website', function ($view) {
            $view->with('navCategories', Category::where('status', 'active')
                ->orderBy('sort_order')
                ->take(8)
                ->get(['id', 'slug', 'translations', 'icon']));
        });

        // ── Notification Observers ──────────────────────────────────────────
        ServiceRequest::observe(ServiceRequestObserver::class);
        CustomerRequirement::observe(CustomerRequirementObserver::class);
        RequirementProposal::observe(RequirementProposalObserver::class);
        PaymentTransaction::observe(PaymentTransactionObserver::class);
        SubscriptionInvoice::observe(SubscriptionInvoiceObserver::class);
        Review::observe(ReviewObserver::class);
        SupportTicket::observe(SupportTicketObserver::class);
        TeamJobAssignment::observe(TeamJobAssignmentObserver::class);
    }
}
