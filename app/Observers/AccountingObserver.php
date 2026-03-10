<?php

namespace App\Observers;

use App\Models\Accounting\Accounting;
use App\Services\CacheService;

class AccountingObserver
{
    public function created(Accounting $accounting): void
    {
        CacheService::clearRelated('accounting');
    }

    public function updated(Accounting $accounting): void
    {
        CacheService::clearRelated('accounting');
    }

    public function deleted(Accounting $accounting): void
    {
        CacheService::clearRelated('accounting');
    }
}
