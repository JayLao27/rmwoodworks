<?php

namespace App\Observers;

use App\Models\Sales\Customer;
use App\Services\CacheService;

class CustomerObserver
{
    public function created(Customer $customer): void
    {
        CacheService::clearRelated('customer');
    }

    public function updated(Customer $customer): void
    {
        CacheService::clearRelated('customer');
    }

    public function deleted(Customer $customer): void
    {
        CacheService::clearRelated('customer');
    }
}
