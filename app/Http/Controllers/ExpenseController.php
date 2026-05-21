<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = (int) date('m');
        $currentYear = (int) date('Y');

        $filterMonth = $request->input('month', $currentMonth);
        $filterYear = $request->input('year', $currentYear);
        $search = $request->input('search');

        // Fetch category types for the form and filtering
        $categories = ExpenseType::orderBy('expense_type')->get();

        // Building the query
        $query = Expense::select('expenses.*', 'expense_type.expense_type as category_name')
            ->join('expense_type', 'expenses.expense_type_id', '=', 'expense_type.exp_type_id');

        if ($filterMonth !== 'all') {
            $query->where('expenses.month', '=', (int) $filterMonth);
        }
        if ($filterYear !== 'all') {
            $query->where('expenses.year', '=', (int) $filterYear);
        }

        if (!empty($search)) {
            $query->where('expenses.detail', 'like', '%' . $search . '%');
        }

        $expenses = $query->orderBy('expenses.date', 'desc')
            ->orderBy('expenses.created_at', 'desc')
            ->get();

        // Stats calculation
        $totalSpent = $expenses->sum('amount');
        
        // Month-over-month calculation
        $lastMonth = $filterMonth == 1 ? 12 : $filterMonth - 1;
        $lastYear = $filterMonth == 1 ? $filterYear - 1 : $filterYear;
        
        $prevMonthSpent = Expense::where('month', $lastMonth)
            ->where('year', $lastYear)
            ->sum('amount');

        return view('admin.expenses.index', compact(
            'expenses',
            'categories',
            'totalSpent',
            'prevMonthSpent',
            'filterMonth',
            'filterYear',
            'search'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_type_id' => ['required', 'integer', 'exists:expense_type,exp_type_id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'detail' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'file' => ['nullable', 'file', 'image', 'max:5120'], // Max 5MB
        ]);

        $date = Carbon::parse($data['date']);
        
        // Handle file upload
        $filePath = '';
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            // Store file in public disk, under receipts directory
            $filePath = $file->storeAs('receipts', $filename, 'public');
        }

        Expense::create([
            'expense_type_id' => $data['expense_type_id'],
            'amount' => $data['amount'],
            'detail' => $data['detail'],
            'file' => $filePath, // Empty string if no file uploaded, matching non-null column constraint safely
            'date' => $date->format('Y-m-d'),
            'month' => (int) $date->format('m'),
            'year' => (int) $date->format('Y'),
        ]);

        return redirect()->back()->with('success', 'Expense logged successfully.');
    }

    public function categories()
    {
        // Get categories with total expense count
        $categories = ExpenseType::select('expense_type.*')
            ->selectSub(function ($query) {
                $query->from('expenses')
                    ->whereColumn('expenses.expense_type_id', 'expense_type.exp_type_id')
                    ->selectRaw('COUNT(*)');
            }, 'expenses_count')
            ->selectSub(function ($query) {
                $query->from('expenses')
                    ->whereColumn('expenses.expense_type_id', 'expense_type.exp_type_id')
                    ->selectRaw('SUM(amount)');
            }, 'total_spent')
            ->orderBy('expense_type')
            ->get();

        return view('admin.expenses.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'expense_type' => ['required', 'string', 'max:20', 'unique:expense_type,expense_type'],
        ]);

        ExpenseType::create([
            'expense_type' => $data['expense_type'],
        ]);

        return redirect()->back()->with('success', 'Expense category created successfully.');
    }
}
