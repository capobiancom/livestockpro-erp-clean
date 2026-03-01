<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinancialReportsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('financialReports', JournalEntry::class);
        // Reuse existing financial report routes (Balance Sheet, P&L, Cash Flow, Trial Balance).
        // This page is a professional "hub" to access them quickly.
        return Inertia::render('Reports/FinancialReports/Index');
    }
}
