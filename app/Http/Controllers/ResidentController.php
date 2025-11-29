<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resident\StoreResidentRequest;
use App\Http\Requests\Resident\UpdateResidentRequest;
use App\Models\Household;
use App\Models\Resident;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    public function __construct(private readonly ActivityLogger $activityLogger)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'purok', 'voter']);

        $residents = Resident::with(['household', 'user'])
            ->whereNull('archived_at')
            ->whereNull('user_id')
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $query->where(function (Builder $subQuery) use ($request): void {
                    $keyword = '%' . $request->string('search') . '%';
                    $subQuery->where('first_name', 'like', $keyword)
                        ->orWhere('last_name', 'like', $keyword)
                        ->orWhere('purok', 'like', $keyword)
                        ->orWhere('reference_id', 'like', $keyword);
                });
            })
            ->when($request->filled('status'), fn (Builder $query) => $query->where('residency_status', $request->input('status')))
            ->when($request->filled('purok'), fn (Builder $query) => $query->where('purok', $request->input('purok')))
            ->when($request->filled('voter'), function (Builder $query) use ($request): void {
                $value = $request->input('voter');

                if ($value === 'yes') {
                    $query->where('is_voter', true);
                } elseif ($value === 'no') {
                    $query->where(function (Builder $subQuery): void {
                        $subQuery->whereNull('is_voter')->orWhere('is_voter', false);
                    });
                }
            })
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

        $statusOptions = Resident::query()
            ->whereNull('archived_at')
            ->whereNotNull('residency_status')
            ->distinct()
            ->orderBy('residency_status')
            ->pluck('residency_status')
            ->all();

        $purokOptions = Resident::query()
            ->whereNull('archived_at')
            ->whereNotNull('purok')
            ->distinct()
            ->orderBy('purok')
            ->pluck('purok')
            ->all();

        return view('residents.index', [
            'residents' => $residents,
            'filters' => $filters,
            'statusOptions' => $statusOptions,
            'purokOptions' => $purokOptions,
        ]);
    }

    public function create(): View
    {
        return view('residents.create', [
            'households' => Household::orderBy('household_number')->get(),
        ]);
    }

    public function store(StoreResidentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['residency_status'] ??= 'active';

        $resident = Resident::create($data);
        $this->activityLogger->log('resident.created', 'New resident added', ['resident_id' => $resident->id]);

        return redirect()->route('residents.show', $resident)->with('status', 'Resident record created.');
    }

    public function show(Resident $resident): View
    {
        return view('residents.show', [
            'resident' => $resident->load(['household', 'certificateRequests' => fn ($query) => $query->latest()]),
        ]);
    }

    public function edit(Resident $resident): View
    {
        return view('residents.edit', [
            'resident' => $resident,
            'households' => Household::orderBy('household_number')->get(),
        ]);
    }

    public function update(UpdateResidentRequest $request, Resident $resident): RedirectResponse
    {
        $resident->update($request->validated());
        $this->activityLogger->log('resident.updated', 'Resident profile updated', ['resident_id' => $resident->id]);

        return redirect()->route('residents.show', $resident)->with('status', 'Resident updated.');
    }

    public function destroy(Resident $resident): RedirectResponse
    {
        $resident->update(['archived_at' => now()]);
        $this->activityLogger->log('resident.archived', 'Resident archived', ['resident_id' => $resident->id]);

        return redirect()->route('residents.index')->with('status', 'Resident record archived.');
    }
}
