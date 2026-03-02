<?php

namespace App\Providers;

use App\Models\Material;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\WorkOrder;
use App\Models\Accounting;
use App\Models\Customer;
use App\Observers\AccountingObserver;
use App\Observers\CustomerObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {   
        // Register auth view namespace
        View::addNamespace('auth', resource_path('views/Auth'));

        // Register model observers for cache management
        Accounting::observe(AccountingObserver::class);
        Customer::observe(CustomerObserver::class);

        if(app()->environment('production')) {
            \URL::forceScheme('https');
        }

        Relation::morphMap([
            'material' => Material::class,
            'product' => Product::class,
            'purchase_order' => PurchaseOrder::class,
            'work_order' => WorkOrder::class,
        ]);
    }
}
