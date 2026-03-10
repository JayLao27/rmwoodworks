<?php

namespace App\Services;

use App\Models\Sales\Customer;
use App\Models\Inventory\Product;
use App\Models\Procurement\Supplier;
use App\Models\Inventory\Material;
use App\Models\Accounting\Accounting;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Get customers list with caching (10 minutes)
     */
    public static function getCustomers()
    {
        return Cache::remember('customers_list', 600, function () {
            return Customer::select('id', 'name', 'customer_type', 'email', 'phone')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get products list with caching (10 minutes)
     */
    public static function getProducts()
    {
        return Cache::remember('products_list', 600, function () {
            return Product::select('id', 'product_name', 'selling_price')
                ->orderBy('product_name')
                ->get();
        });
    }

    /**
     * Get suppliers list with caching (10 minutes)
     */
    public static function getSuppliers()
    {
        return Cache::remember('suppliers_list', 600, function () {
            return Supplier::select('id', 'name', 'email', 'phone')
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get materials list with caching (10 minutes)
     */
    public static function getMaterials()
    {
        return Cache::remember('materials_list', 600, function () {
            return Material::select('id', 'material_name', 'current_stock', 'minimum_stock')
                ->orderBy('material_name')
                ->get();
        });
    }

    /**
     * Get financial metrics with caching (5 minutes)
     */
    public static function getFinancialMetrics()
    {
        return Cache::remember('financial_metrics', 300, function () {
            return Accounting::selectRaw('
                SUM(CASE WHEN transaction_type = "Income" THEN amount ELSE 0 END) as total_revenue,
                SUM(CASE WHEN transaction_type = "Expense" THEN amount ELSE 0 END) as total_expenses
            ')->first();
        });
    }

    /**
     * Get total revenue with caching (5 minutes)
     */
    public static function getTotalRevenue()
    {
        return Cache::remember('total_revenue', 300, function () {
            return (float)(Accounting::where('transaction_type', 'Income')->sum('amount') ?? 0);
        });
    }

    /**
     * Get total expenses with caching (5 minutes)
     */
    public static function getTotalExpenses()
    {
        return Cache::remember('total_expenses', 300, function () {
            return (float)(Accounting::where('transaction_type', 'Expense')->sum('amount') ?? 0);
        });
    }

    /**
     * Clear all cache when data changes
     */
    public static function clearAll()
    {
        Cache::forget('customers_list');
        Cache::forget('products_list');
        Cache::forget('suppliers_list');
        Cache::forget('materials_list');
        Cache::forget('financial_metrics');
        Cache::forget('total_revenue');
        Cache::forget('total_expenses');
    }

    /**
     * Clear specific cache
     */
    public static function clearRelated($dataType)
    {
        $cacheMap = [
            'customer' => ['customers_list', 'financial_metrics'],
            'product' => ['products_list'],
            'supplier' => ['suppliers_list'],
            'material' => ['materials_list'],
            'accounting' => ['financial_metrics', 'total_revenue', 'total_expenses'],
        ];

        if (isset($cacheMap[$dataType])) {
            foreach ($cacheMap[$dataType] as $key) {
                Cache::forget($key);
            }
        }
    }
}
