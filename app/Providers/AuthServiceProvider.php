<?php

namespace App\Providers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\Budget;
use App\Policies\IncomePolicy;
use App\Policies\ExpensePolicy;
use App\Policies\BudgetPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Income::class => IncomePolicy::class,
        Expense::class => ExpensePolicy::class,
        Budget::class => BudgetPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
