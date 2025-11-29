<?php

namespace Tests\Feature;

use App\Enums\CertificateStatus;
use App\Enums\CertificateType;
use App\Enums\UserRole;
use App\Models\CertificateRequest;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_details_must_be_submitted_during_request(): void
    {
        $residentUser = User::factory()->create(['role' => UserRole::Resident->value]);
        Resident::factory()->create(['user_id' => $residentUser->id]);

        $response = $this->actingAs($residentUser)->post(route('certificates.store'), [
            'certificate_type' => CertificateType::BarangayClearance->value,
            'purpose' => 'Employment onboarding',
            'details' => [
                'id_type' => 'PhilHealth ID',
            ],
        ]);

        $response->assertSessionHasErrors([
            'details.id_number',
            'details.intended_use',
        ]);
    }

    public function test_payload_is_stored_with_request(): void
    {
        $residentUser = User::factory()->create(['role' => UserRole::Resident->value]);
        Resident::factory()->create(['user_id' => $residentUser->id]);

        $payload = [
            'id_type' => 'PhilSys ID',
            'id_number' => 'ABC12345',
            'intended_use' => 'Scholarship requirements',
        ];

        $response = $this->actingAs($residentUser)->post(route('certificates.store'), [
            'certificate_type' => CertificateType::BarangayClearance->value,
            'purpose' => 'Scholarship',
            'details' => $payload,
        ]);

        $response->assertRedirect(route('certificates.index'));

        /** @var CertificateRequest $request */
        $request = CertificateRequest::first();

        $this->assertEquals($payload, $request->payload);
        $this->assertNotNull($request->details_submitted_at);
        $this->assertEquals($residentUser->id, $request->details_submitted_by);
    }

    public function test_release_is_blocked_if_required_details_are_missing(): void
    {
        $staff = User::factory()->create(['role' => UserRole::Clerk->value]);
        $residentUser = User::factory()->create(['role' => UserRole::Resident->value]);
        $resident = Resident::factory()->create(['user_id' => $residentUser->id]);

        $certificate = CertificateRequest::factory()->create([
            'resident_id' => $resident->id,
            'requested_by' => $residentUser->id,
            'certificate_type' => CertificateType::BusinessClearance->value,
            'status' => CertificateStatus::Approved->value,
            'payload' => null,
        ]);

        $response = $this->actingAs($staff)
            ->from(route('certificates.show', $certificate))
            ->put(route('certificates.status', $certificate), [
                'status' => CertificateStatus::Released->value,
                'remarks' => 'Ready to release',
            ]);

        $response->assertRedirect(route('certificates.show', $certificate));
        $response->assertSessionHasErrors('status');
    }
}
