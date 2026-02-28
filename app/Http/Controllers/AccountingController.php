<?php

namespace App\Http\Controllers;
use App\Models\Accounting;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;    

class AccountingController extends Controller
{
    public function index()
    {
        // Calculate date range 
        $now = Carbon::now();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Get all totals in one query 
        $incomeTransactions = Accounting::where('transaction_type', 'Income')
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();
        $expenseTransactions = Accounting::where('transaction_type', 'Expense')
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();
        $lastMonthData = Accounting::whereBetween('date', [$startOfLastMonth, $endOfLastMonth])
            ->selectRaw('transaction_type, SUM(amount) as total')
            ->groupBy('transaction_type')
            ->get();

        $totalRevenue = (float)($incomeTransactions->total ?? 0);
        $totalExpenses = (float)($expenseTransactions->total ?? 0);
        $netProfit = $totalRevenue - $totalExpenses;

        $lastMonthRevenue = (float)($lastMonthData->where('transaction_type', 'Income')->first()->total ?? 0);
        $lastMonthExpenses = (float)($lastMonthData->where('transaction_type', 'Expense')->first()->total ?? 0);
        $lastMonthNetProfit = $lastMonthRevenue - $lastMonthExpenses;

        $lastMonthRevenuePercentage = $this->lastMonthRevenue($totalRevenue);
        $lastMonthNetProfitPercentage = $this->lastMonthNetprofit($netProfit);
        $lastMonthExpensesPercentage = $this->lastMonthTotalExpenses($totalExpenses);

        // Calculate current month data
        $startOfCurrentMonth = $now->copy()->startOfMonth();
        $endOfCurrentMonth = $now->copy()->endOfMonth();
        
        $currentMonthData = Accounting::whereBetween('date', [$startOfCurrentMonth, $endOfCurrentMonth])
            ->selectRaw('transaction_type, SUM(amount) as total')
            ->groupBy('transaction_type')
            ->get();
            
        $currentMonthRevenue = (float)($currentMonthData->where('transaction_type', 'Income')->first()->total ?? 0);
        $currentMonthExpenses = (float)($currentMonthData->where('transaction_type', 'Expense')->first()->total ?? 0);
        $currentMonthNetProfit = $currentMonthRevenue - $currentMonthExpenses;

        $monthlyPerformance = [
            [
                'month' => $startOfCurrentMonth->format('M'),
                'revenue' => $currentMonthRevenue,
                'expenses' => $currentMonthExpenses,
                'net_profit' => $currentMonthNetProfit,
            ],
            [
                'month' => $startOfLastMonth->format('M'),
                'revenue' => $lastMonthRevenue,
                'expenses' => $lastMonthExpenses,
                'net_profit' => $lastMonthNetProfit,
            ]
        ];

        // Use raw queries for calculations instead of loading all data
        $laborExpense = (float) Accounting::where('transaction_type', 'Expense')
            ->where('description', 'like', 'Labor - Work Order%')
            ->sum('amount');

        // Get only the pending/partial payment orders with pagination
        $salesOrders = SalesOrder::with(['customer'])
            ->whereIn('payment_status', ['Pending', 'Partial'])
            ->orderBy('order_date', 'desc')
            ->paginate(15);

        // Fetch purchase orders with pagination
        $purchaseOrders = PurchaseOrder::with(['supplier'])
            ->whereIn('payment_status', ['Pending', 'Partial'])
            ->orderBy('order_date', 'desc')
            ->paginate(15);

        // Calculate materials expense from unpaid orders
        $materialsExpense = (float) PurchaseOrder::where('payment_status', 'Pending')
            ->orWhere('payment_status', 'Partial')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)'));

        $totalBreakdown = $materialsExpense + $laborExpense;
        $materialsPercent = $totalBreakdown > 0 ? ($materialsExpense / $totalBreakdown) * 100 : 0;
        $laborPercent = $totalBreakdown > 0 ? ($laborExpense / $totalBreakdown) * 100 : 0;

        // Fetch aggregated transactions to group multiple payments for the same order
        $transactions = Accounting::with(['salesOrder.customer', 'purchaseOrder.supplier'])
            ->select([
                'transaction_type',
                'sales_order_id',
                'purchase_order_id',
                DB::raw('MAX(id) as id'),
                DB::raw('MAX(date) as date'),
                DB::raw('SUM(amount) as amount'),
                DB::raw('MAX(description) as description'),
                DB::raw('MAX(user_id) as user_id'),
            ])
            ->groupBy('transaction_type', 'sales_order_id', 'purchase_order_id', DB::raw('(CASE WHEN sales_order_id IS NULL AND purchase_order_id IS NULL THEN id ELSE 0 END)'))
            ->orderBy('date', 'desc')
            ->paginate(25);
        


        return view('Systems.accounting', compact(
            'totalRevenue',
            'totalExpenses',
            'netProfit',
            'lastMonthRevenuePercentage',
            'lastMonthNetProfitPercentage',
            'lastMonthExpensesPercentage',
            'salesOrders',
            'purchaseOrders',
            'transactions',
            'materialsExpense',
            'laborExpense',
            'materialsPercent',
            'laborPercent',
            'monthlyPerformance'
        ));
    }

    public function dashboard()
    {
        // Only fetch limited data with pagination, not everything
        $salesOrders = SalesOrder::with(['customer'])->latest()->limit(50)->get();

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();
        $startOfWeek = $now->copy()->startOfWeek();

        // Use raw queries to get all metrics in one batch
        $metrics = Accounting::selectRaw('
            SUM(CASE WHEN transaction_type = "Income" THEN amount ELSE 0 END) as totalRevenue,
            SUM(CASE WHEN transaction_type = "Income" AND date BETWEEN ? AND ? THEN amount ELSE 0 END) as revenueThisMonth,
            SUM(CASE WHEN transaction_type = "Income" AND date BETWEEN ? AND ? THEN amount ELSE 0 END) as revenueLastMonth,
            SUM(CASE WHEN transaction_type = "Expense" THEN amount ELSE 0 END) as totalExpenses,
            SUM(CASE WHEN transaction_type = "Expense" AND date BETWEEN ? AND ? THEN amount ELSE 0 END) as expensesThisMonth,
            SUM(CASE WHEN transaction_type = "Expense" AND date BETWEEN ? AND ? THEN amount ELSE 0 END) as expensesLastMonth
        ')
        ->setBindings([
            $startOfMonth, $endOfMonth,
            $startOfLastMonth, $endOfLastMonth,
            $startOfMonth, $endOfMonth,
            $startOfLastMonth, $endOfLastMonth
        ])
        ->first();

        $totalRevenue = (float)($metrics->totalRevenue ?? 0);
        $revenueThisMonth = (float)($metrics->revenueThisMonth ?? 0);
        $revenueLastMonth = (float)($metrics->revenueLastMonth ?? 0);
        $totalExpenses = (float)($metrics->totalExpenses ?? 0);
        $expensesThisMonth = (float)($metrics->expensesThisMonth ?? 0);
        $expensesLastMonth = (float)($metrics->expensesLastMonth ?? 0);

        $income = $totalRevenue;
        $netProfit = $totalRevenue - $totalExpenses;
        $netProfitThisMonth = $revenueThisMonth - $expensesThisMonth;
        $netProfitLastMonth = $revenueLastMonth - $expensesLastMonth;

        $revenueChangePercent = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);
        
        $expensesChangePercent = $expensesLastMonth > 0
            ? round((($expensesThisMonth - $expensesLastMonth) / $expensesLastMonth) * 100, 1)
            : ($expensesThisMonth > 0 ? 100 : 0);
            
        $netProfitChangePercent = $netProfitLastMonth != 0
            ? round((($netProfitThisMonth - $netProfitLastMonth) / abs($netProfitLastMonth)) * 100, 1)
            : ($netProfitThisMonth != 0 ? 100 : 0);

        // Count queries (not full data fetches)
        $inProductionCount = WorkOrder::whereIn('status', ['in_progress', 'quality_check'])->count();
        $overdueWorkOrders = WorkOrder::where('status', '!=', 'completed')
            ->where('due_date', '<', $now->toDateString())
            ->count();

        $activeOrdersCount = SalesOrder::whereIn('status', ['Pending', 'In production', 'Ready'])->count();
        $newOrdersThisWeek = SalesOrder::where('order_date', '>=', $startOfWeek)->count();

        // Low stock - only fetch the count and top items
        $lowStockCount = Material::whereRaw('current_stock <= minimum_stock')->count();
        $lowStockMaterials = Material::whereRaw('current_stock <= minimum_stock')
            ->orderBy('current_stock')
            ->limit(20)
            ->get();

        // Accounting report: last 6 months - use raw SQL for efficiency
        $salesReportMonths = [];
        $salesReportRevenue = [];
        $salesReportExpenses = [];
        $chartLabels = [];
        $chartRevenue = [];
        $chartExpenses = [];
        $chartProfit = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            
            $monthLabel = $month->format('M Y');
            $salesReportMonths[] = $monthLabel;
            $chartLabels[] = $monthLabel;
            
            $monthData = Accounting::selectRaw('
                SUM(CASE WHEN transaction_type = "Income" THEN amount ELSE 0 END) as revenue,
                SUM(CASE WHEN transaction_type = "Expense" THEN amount ELSE 0 END) as expenses
            ')
            ->whereBetween('date', [$start, $end])
            ->first();
            
            $revenue = (float)($monthData->revenue ?? 0);
            $expenses = (float)($monthData->expenses ?? 0);
            
            $salesReportRevenue[] = $revenue;
            $salesReportExpenses[] = $expenses;
            $chartRevenue[] = $revenue;
            $chartExpenses[] = $expenses;
            $chartProfit[] = $revenue - $expenses;
        }

        return view('Systems.dashboard', compact(
            'salesOrders',
            'totalRevenue',
            'revenueChangePercent',
            'income',
            'totalExpenses',
            'expensesChangePercent',
            'netProfit',
            'netProfitChangePercent',
            'activeOrdersCount',
            'newOrdersThisWeek',
            'inProductionCount',
            'overdueWorkOrders',
            'lowStockCount',
            'lowStockMaterials',
            'salesReportMonths',
            'salesReportRevenue',
            'salesReportExpenses',
            'chartLabels',
            'chartRevenue',
            'chartExpenses',
            'chartProfit'
        ));
    }

    public function addTransaction( SalesOrder $salesOrder, PurchaseOrder $purchaseOrder )
    {
            


        return view('Systems.add_transaction');
    }

    public function salesTransaction(Request $request)
    {
        $request->validate([
            'transaction_type' => 'required|in:Income,Expense',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'reference' => 'required|string',
            'order_id' => 'required|numeric',
            'description' => 'nullable|string|max:500',
        ]);

        // For Income (Sales Orders)
        if ($request->transaction_type === 'Income') {
            $salesOrder = SalesOrder::findOrFail($request->order_id);
            $amountToPay = (float) $request->input('amount');
            $totalAmount = (float) $salesOrder->total_amount;

            // Check existing payments
            $existingPaid = (float) Accounting::where('sales_order_id', $request->order_id)
                ->where('transaction_type', 'Income')
                ->sum('amount');
            $newTotalPaid = $existingPaid + $amountToPay;

            // Validate that payment amount doesn't exceed total amount
            if ($newTotalPaid > $totalAmount) {
                return redirect()->back()->withErrors([
                    'amount' => "Payment amount exceeds the remaining balance of ₱" . number_format(max($totalAmount - $existingPaid, 0), 2)
                ]);
            }

            // Create the transaction (don't delete previous ones!)
            Accounting::create([
                'transaction_type' => 'Income',
                'amount' => $amountToPay,
                'date' => $request->input('date'),
                'description' => $request->input('description'),
                'sales_order_id' => $request->order_id,
                'purchase_order_id' => null,
                'user_id' => auth()->id(),
            ]);

            // Update payment status for sales order
            $totalPaid = (float) Accounting::where('sales_order_id', $request->order_id)
                ->where('transaction_type', 'Income')
                ->sum('amount');

            $paymentStatus = $totalPaid >= $totalAmount ? 'Paid' : 'Partial';
            $salesOrder->update([
                'payment_status' => $paymentStatus,
                'paid_amount' => $totalPaid
            ]);
        }
        // For Expense (Purchase Orders)
        elseif ($request->transaction_type === 'Expense') {
            $purchaseOrder = PurchaseOrder::findOrFail($request->order_id);
            $amountToPay = (float) $request->input('amount');
            $totalAmount = (float) $purchaseOrder->total_amount;

            $existingPaid = (float) Accounting::where('purchase_order_id', $request->order_id)
                ->where('transaction_type', 'Expense')
                ->sum('amount');
            $newTotalPaid = $existingPaid + $amountToPay;

            // Validate that payment amount doesn't exceed total amount
            if ($newTotalPaid > $totalAmount) {
                return redirect()->back()->withErrors([
                    'amount' => "Payment amount exceeds the remaining balance of ₱" . number_format(max($totalAmount - $existingPaid, 0), 2)
                ]);
            }

            // Create the transaction
            Accounting::create([
                'transaction_type' => 'Expense',
                'amount' => $amountToPay,
                'date' => $request->input('date'),
                'description' => $request->input('description'),
                'sales_order_id' => null,
                'purchase_order_id' => $request->order_id,
                'user_id' => auth()->id(),
            ]);

            // Update payment status for purchase order
            $totalPaid = (float) Accounting::where('purchase_order_id', $request->order_id)
                ->where('transaction_type', 'Expense')
                ->sum('amount');

            $paymentStatus = $totalPaid >= $totalAmount ? 'Paid' : 'Partial';
            $purchaseOrder->update([
                'payment_status' => $paymentStatus,
                'paid_amount' => $totalPaid
            ]);
        }

        \App\Models\SystemActivity::log('Accounting', 'Transaction Logged', "{$request->transaction_type} of ₱" . number_format($request->amount, 2) . " logged for reference {$request->reference}.", $request->transaction_type === 'Income' ? 'emerald' : 'red');

        return redirect()->route('accounting')->with('success', 'Transaction added successfully!');
    }

    

    public function lastMonthNetprofit($totalNetProfit) 
    {
        // Get last month's revenue
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        
        $lastMonthRevenue = Accounting::where('transaction_type', 'Income')
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');
        
        // Get last month's expenses
        $lastMonthExpenses = Accounting::where('transaction_type', 'Expense')
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');
        
        // Calculate last month's net profit
        $lastMonthNetProfit = $lastMonthRevenue - $lastMonthExpenses;
        
        // Calculate percentage
        $netProfitPercentage = $totalNetProfit > 0 ? ($lastMonthNetProfit / $totalNetProfit) * 100 : 0;
        
        return round($netProfitPercentage, 2);
    }

    public function lastMonthTotalExpenses($totalExpenses)
    {
        // Get last month's expenses
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        
        $lastMonthExpenses = Accounting::where('transaction_type', 'Expense')
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');
        
        // Calculate percentage
        $expensesPercentage = $totalExpenses > 0 ? ($lastMonthExpenses / $totalExpenses) * 100 : 0;
        
        return round($expensesPercentage, 2);
    }
    Public function lastMonthRevenue( $totalRevenue )
     {
            // Get last month's revenue from transactions
            $lastMonthStart = now()->subMonth()->startOfMonth();
            $lastMonthEnd = now()->subMonth()->endOfMonth();
            $lastMonthRevenue = Accounting::where('transaction_type', 'Income')
                ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
                ->sum('amount');
            // Calculate percentage
            $revenuePercentage = $totalRevenue > 0 ? ($lastMonthRevenue / $totalRevenue) * 100 : 0;
            return round($revenuePercentage, 2);
    }
 

    public function generateFinancialReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

       
        $financialData = [
            'total_revenue' => 0,
            'total_expenses' => 0,
            'net_profit' => 0,
        ];


        // Or get individual sales orders with their totals
        $salesOrders = SalesOrder::whereBetween('order_date', [$startDate, $endDate])
            ->get(['id', 'order_number', 'total_amount', 'order_date']);

        return view('Systems.accounting_report', compact('financialData', 'startDate', 'endDate', 'totalRevenue', 'salesOrders'));
    }

    public function exportTransactionReceipt($transactionId)
    {
        // Extract the actual ID from the format TO-YYYY-XXX
        $parts = explode('-', $transactionId);
        $id = isset($parts[2]) ? (int)$parts[2] : null;
        
        if (!$id) {
            abort(404, 'Invalid transaction ID');
        }

        $transaction = Accounting::with(['salesOrder.customer', 'purchaseOrder.supplier'])
            ->findOrFail($id);

        return view('exports.transaction-receipt', compact('transaction', 'transactionId'));
    }

    public function exportFinancialReport()
    {
        $totalRevenue = Accounting::where('transaction_type', 'Income')->sum('amount');
        $totalExpenses = Accounting::where('transaction_type', 'Expense')->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        $filename = 'financial-report-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($totalRevenue, $totalExpenses, $netProfit) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, ['Financial Report', 'Generated on ' . now()->format('F d, Y')]);
            fputcsv($file, []);
            fputcsv($file, ['Metric', 'Amount']);
            
            // CSV Data
            fputcsv($file, ['Total Revenue', number_format($totalRevenue, 2)]);
            fputcsv($file, ['Total Expenses', number_format($totalExpenses, 2)]);
            fputcsv($file, ['Net Profit', number_format($netProfit, 2)]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportTransactionHistory()
    {
        $transactions = Accounting::with(['salesOrder.customer', 'purchaseOrder.supplier'])
            ->orderBy('date', 'desc')
            ->get();

        $filename = 'transactions-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Transaction ID',
                'Date',
                'Type',
                'Category',
                'Description',
                'Amount',
            ]);

            // CSV Data
            foreach ($transactions as $transaction) {
                $transactionId = 'TO-' . \Carbon\Carbon::parse($transaction->date)->format('Y') . '-' . str_pad($transaction->id, 3, '0', STR_PAD_LEFT);
                $category = '';
                $description = '';

                if ($transaction->salesOrder) {
                    $category = $transaction->salesOrder->customer->name ?? 'N/A';
                    $description = $transaction->salesOrder->order_number ?? '';
                } elseif ($transaction->purchaseOrder) {
                    $category = $transaction->purchaseOrder->supplier->name ?? 'N/A';
                    $description = $transaction->purchaseOrder->order_number ?? '';
                } else {
                    $category = 'N/A';
                    $description = $transaction->description ?? '-';
                }

                fputcsv($file, [
                    $transactionId,
                    \Carbon\Carbon::parse($transaction->date)->format('M d, Y'),
                    $transaction->transaction_type,
                    $category,
                    $description,
                    number_format($transaction->amount, 2),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
   
}
