<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resident\StoreResidentRequest;
use App\Http\Requests\Resident\UpdateResidentRequest;
use App\Models\Household;
use App\Models\Resident;
use App\Models\User;
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
        $filters = $request->only(['search', 'purok', 'link']);

        $residents = Resident::with(['household', 'user'])
            ->whereNull('archived_at')
            ->when($filters['link'] ?? null, function (Builder $query, string $link): void {
                if ($link === 'linked') {
                    $query->whereNotNull('user_id');
                } elseif ($link === 'unlinked') {
                    $query->whereNull('user_id');
                }
            })
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $query->where(function (Builder $subQuery) use ($request): void {
                    $keyword = '%' . $request->string('search') . '%';
                    $subQuery->where('first_name', 'like', $keyword)
                        ->orWhere('last_name', 'like', $keyword)
                        ->orWhere('purok', 'like', $keyword)
                        ->orWhere('reference_id', 'like', $keyword);
                });
            })
            ->when($request->filled('purok'), fn (Builder $query) => $query->where('purok', $request->input('purok')))
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

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

    public function linkAccount(Request $request, Resident $resident): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        // Check if resident already has a linked account
        if ($resident->user_id) {
            return back()->with('error', 'Resident already has a linked account.');
        }

        // Check if user already has a linked resident record
        $user = User::find($data['user_id']);
        if ($user->resident) {
            return back()->with('error', 'This account is already linked to another resident.');
        }

        $resident->update(['user_id' => $data['user_id']]);
        $this->activityLogger->log('resident.linked', 'User account linked to resident', [
            'resident_id' => $resident->id,
            'user_id' => $data['user_id'],
        ]);

        return back()->with('status', 'Account successfully linked to resident record.');
    }

    public function unlinkAccount(Request $request, Resident $resident): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if (!$resident->user_id) {
            return back()->with('error', 'Resident has no linked account.');
        }

        $userId = $resident->user_id;
        $resident->update(['user_id' => null]);
        $this->activityLogger->log('resident.unlinked', 'User account unlinked from resident', [
            'resident_id' => $resident->id,
            'user_id' => $userId,
        ]);

        return back()->with('status', 'Account successfully unlinked from resident record.');
    }
}
