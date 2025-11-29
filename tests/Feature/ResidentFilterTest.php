<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResidentFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_listing_can_be_filtered_by_status_purok_and_voter_flag(): void
    {
        $clerk = User::factory()->create(['role' => UserRole::Clerk->value]);

        $matchingResident = Resident::factory()->create([
            'residency_status' => 'inactive',
            'purok' => 'Purok 9',
            'is_voter' => true,
        ]);

        $differentStatus = Resident::factory()->create([
            'residency_status' => 'active',
            'purok' => 'Purok 9',
            'is_voter' => true,
        ]);

        $differentPurok = Resident::factory()->create([
            'residency_status' => 'inactive',
            'purok' => 'Purok 2',
            'is_voter' => true,
        ]);

        $nonVoter = Resident::factory()->create([
            'residency_status' => 'inactive',
            'purok' => 'Purok 9',
            'is_voter' => false,
        ]);

        $response = $this->actingAs($clerk)->get(route('residents.index', [
            'status' => 'inactive',
            'purok' => 'Purok 9',
            'voter' => 'yes',
        ]));

        $response->assertOk();
        /** @var \Illuminate\Pagination\LengthAwarePaginator $residents */
        $residents = $response->viewData('residents');
        $this->assertNotNull($residents);

        $collection = $residents->getCollection();
        $this->assertSame(1, $residents->total());
        $this->assertTrue($collection->first()->is($matchingResident));
        $this->assertFalse($collection->contains(fn ($resident) => $resident->is($differentStatus)));
        $this->assertFalse($collection->contains(fn ($resident) => $resident->is($differentPurok)));
        $this->assertFalse($collection->contains(fn ($resident) => $resident->is($nonVoter)));
    }

    public function test_non_voter_filter_includes_false_flagged_records(): void
    {
        $clerk = User::factory()->create(['role' => UserRole::Clerk->value]);

        $nonVoter = Resident::factory()->create(['is_voter' => false, 'purok' => 'Purok 3']);
        $trueVoter = Resident::factory()->create(['is_voter' => true, 'purok' => 'Purok 3']);

        $response = $this->actingAs($clerk)->get(route('residents.index', [
            'voter' => 'no',
        ]));

        $response->assertOk();
        /** @var \Illuminate\Pagination\LengthAwarePaginator $residents */
        $residents = $response->viewData('residents');
        $this->assertNotNull($residents);

        $collection = $residents->getCollection();
        $this->assertTrue($collection->contains(fn ($resident) => $resident->is($nonVoter)));
        $this->assertFalse($collection->contains(fn ($resident) => $resident->is($trueVoter)));
    }
}
