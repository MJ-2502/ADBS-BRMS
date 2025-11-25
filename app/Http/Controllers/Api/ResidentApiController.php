<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResidentApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $residents = Resident::select(
            'id',
            'reference_id',
            'first_name',
            'last_name',
            'purok',
            'residency_status',
            'updated_at'
        )
            ->when($request->filled('status'), fn ($query) => $query->where('residency_status', $request->string('status')))
            ->paginate(25);

        return response()->json($residents);
    }
}
