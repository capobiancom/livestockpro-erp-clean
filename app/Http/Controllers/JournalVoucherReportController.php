<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JournalVoucherReportController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('voucherReport', JournalEntry::class);

        $query = JournalEntry::with(['farm', 'createdBy', 'lines.account'])->latest();

        if ($request->filled('reference_type')) {
            $query->where('reference_type', $request->reference_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }

        $journalEntries = $query->paginate(15)->withQueryString();

        $referenceTypes = JournalEntry::distinct()
            ->pluck('reference_type')
            ->filter()
            ->values();

        return Inertia::render('JournalEntries/VoucherReport', [
            'journalEntries' => $journalEntries,
            'filters'        => $request->only(['reference_type', 'status', 'date_from', 'date_to']),
            'referenceTypes' => $referenceTypes,
        ]);
    }

    public function printVoucher(JournalEntry $journalEntry): Response
    {
        $this->authorize('voucherReport', JournalEntry::class);

        $journalEntry->load(['farm', 'createdBy', 'lines.account']);

        return Inertia::render('JournalEntries/PrintVoucher', [
            'journalEntry' => $journalEntry,
        ]);
    }
}
