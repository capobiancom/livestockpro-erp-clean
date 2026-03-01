<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJournalEntryRequest;
use App\Http\Requests\UpdateJournalEntryRequest;
use App\Models\JournalEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class JournalEntryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(JournalEntry::class, 'journal_entry');
    }

    public function index(): Response
    {

        $journalEntries = JournalEntry::with(['farm', 'createdBy'])->latest()->paginate(10);

        return Inertia::render('JournalEntries/Index', [
            'journalEntries' => $journalEntries,
        ]);
    }

    public function create(): Response
    {

        $chartOfAccounts = Auth::user()->farm->chartOfAccounts()
            ->whereDoesntHave('children')
            ->get(['id', 'name']);

        return Inertia::render('JournalEntries/Create', [
            'chartOfAccounts' => $chartOfAccounts,
        ]);
    }

    public function store(StoreJournalEntryRequest $request): RedirectResponse
    {

        $validated = $request->validated();
        $validated['farm_id'] = Auth::user()->farm_id;
        $validated['user_id'] = Auth::id();

        $journalEntry = JournalEntry::create($validated);
        $journalEntry->lines()->createMany($validated['lines']);

        return redirect()->route('journal-entries.index')->with('success', 'Journal Entry created successfully.');
    }

    public function show(JournalEntry $journalEntry): Response
    {

        $journalEntry->load(['farm', 'createdBy', 'lines.account']);

        return Inertia::render('JournalEntries/Show', [
            'journalEntry' => $journalEntry,
        ]);
    }

    public function edit(JournalEntry $journalEntry): Response
    {

        $journalEntry->load(['lines.account']);
        $chartOfAccounts = Auth::user()->farm->chartOfAccounts()
            ->whereDoesntHave('children')
            ->get(['id', 'name']);

        return Inertia::render('JournalEntries/Edit', [
            'journalEntry' => $journalEntry,
            'chartOfAccounts' => $chartOfAccounts,
        ]);
    }

    public function update(UpdateJournalEntryRequest $request, JournalEntry $journalEntry): RedirectResponse
    {

        $validated = $request->validated();
        unset($validated['farm_id']); // Prevent updating farm_id
        unset($validated['created_by']); // Prevent updating created_by

        $journalEntry->update($validated);

        if ($request->has('lines')) {
            $journalEntry->lines()->delete();
            $journalEntry->lines()->createMany($validated['lines']);
        }

        return redirect()->route('journal-entries.index')->with('success', 'Journal Entry updated successfully.');
    }

    public function destroy(JournalEntry $journalEntry): RedirectResponse
    {
        $journalEntry->delete();

        return redirect()->route('journal-entries.index')->with('success', 'Journal Entry deleted successfully.');
    }
}
