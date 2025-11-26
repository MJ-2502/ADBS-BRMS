<?php

namespace App\Http\Controllers;

use App\Http\Requests\Household\StoreHouseholdRequest;
use App\Models\Household;
use App\Services\ActivityLogger;
use App\Services\HouseholdRecordArchive;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class HouseholdController extends Controller
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
        private readonly HouseholdRecordArchive $householdRecordArchive
    )
    {
    }

    public function index(): View
    {
        $households = Household::withCount('residents')->orderBy('household_number')->paginate(15);

        return view('households.index', [
            'households' => $households,
        ]);
    }

    public function create(): View
    {
        return view('households.create');
    }

    public function store(StoreHouseholdRequest $request): RedirectResponse
    {
        $household = Household::create($request->validated());
        $this->activityLogger->log('household.created', 'Household added', ['household_id' => $household->id]);

        $this->householdRecordArchive->appendHousehold($household, $request->user());

        return redirect()->route('households.index')->with('status', 'Household saved.');
    }

    public function update(StoreHouseholdRequest $request, Household $household): RedirectResponse
    {
        $household->update($request->validated());
        $this->activityLogger->log('household.updated', 'Household updated', ['household_id' => $household->id]);

        return redirect()->route('households.index')->with('status', 'Household updated.');
    }

    public function edit(Household $household): View
    {
        return view('households.edit', [
            'household' => $household,
        ]);
    }

    public function destroy(Household $household): RedirectResponse
    {
        if ($household->residents()->exists()) {
            return back()->withErrors(['household' => 'Cannot delete household with assigned residents.']);
        }

        $household->delete();
        $this->activityLogger->log('household.deleted', 'Household removed', ['household_id' => $household->id]);

        return redirect()->route('households.index')->with('status', 'Household removed.');
    }

}
