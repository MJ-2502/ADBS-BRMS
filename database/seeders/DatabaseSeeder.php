<?php

namespace Database\Seeders;

use App\Enums\CertificateStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\ActivityLog;
use App\Models\BackupJob;
use App\Models\CertificateRequest;
use App\Models\Household;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Barangay Admin',
            'email' => 'admin@brms.local',
            'role' => UserRole::Admin->value,
            'password' => Hash::make('password'),
            'verification_status' => VerificationStatus::Approved->value,
            'verified_at' => now(),
        ]);

        $clerk = User::factory()->create([
            'name' => 'Records Clerk',
            'email' => 'clerk@brms.local',
            'role' => UserRole::Clerk->value,
            'password' => Hash::make('password'),
            'verification_status' => VerificationStatus::Approved->value,
            'verified_at' => now(),
        ]);

        $residentUser = User::factory()->create([
            'name' => 'Maria Dela Cruz',
            'email' => 'resident@brms.local',
            'role' => UserRole::Resident->value,
            'password' => Hash::make('password'),
            'verification_status' => VerificationStatus::Approved->value,
            'verified_at' => now(),
        ]);

        $household = Household::factory()->create([
            'head_name' => $residentUser->name,
        ]);

        $resident = Resident::factory()->create([
            'household_id' => $household->id,
            'user_id' => $residentUser->id,
            'first_name' => 'Maria',
            'last_name' => 'Dela Cruz',
            'gender' => 'female',
            'residency_status' => 'active',
        ]);

        $additionalResidentUsers = User::factory()
            ->count(8)
            ->state([
                'role' => UserRole::Resident->value,
                'password' => Hash::make('password'),
                'verification_status' => VerificationStatus::Approved->value,
                'verified_at' => now(),
            ])
            ->create();

        $demoProofPath = 'resident-proofs/demo-proof.pdf';
        Storage::disk('local')->put($demoProofPath, 'Sample proof document for pending verification.');

        $pendingUser = User::factory()->create([
            'name' => 'Pending Resident',
            'email' => 'pending@brms.local',
            'role' => UserRole::Resident->value,
            'password' => Hash::make('password'),
            'verification_status' => VerificationStatus::Pending->value,
            'verification_proof_path' => $demoProofPath,
            'verified_at' => null,
        ]);

        $rejectedUser = User::factory()->create([
            'name' => 'Rejected Resident',
            'email' => 'rejected@brms.local',
            'role' => UserRole::Resident->value,
            'password' => Hash::make('password'),
            'verification_status' => VerificationStatus::Rejected->value,
            'verification_notes' => 'Document was unreadable.',
            'verified_at' => now()->subDay(),
            'verified_by' => $clerk->id,
        ]);

        $households = Household::factory()->count(6)->create();

        $additionalResidents = collect();

        foreach ($households as $generatedHousehold) {
            $members = Resident::factory()
                ->count(rand(2, 4))
                ->create([
                    'household_id' => $generatedHousehold->id,
                    'purok' => $generatedHousehold->purok,
                ]);

            $additionalResidents = $additionalResidents->merge($members);
        }

        $additionalResidents
            ->values()
            ->take($additionalResidentUsers->count())
            ->each(function (Resident $resident, int $index) use ($additionalResidentUsers): void {
                $resident->update(['user_id' => $additionalResidentUsers[$index]->id]);
            });

        $pendingResident = Resident::factory()->create([
            'user_id' => $pendingUser->id,
            'first_name' => 'Jonas',
            'last_name' => 'Villanueva',
            'purok' => 'Purok 6',
            'residency_status' => 'active',
        ]);

        Resident::factory()->create([
            'user_id' => $rejectedUser->id,
            'first_name' => 'Lucas',
            'last_name' => 'Marquez',
            'purok' => 'Purok 2',
            'residency_status' => 'active',
        ]);

        $allResidents = collect([$resident, $pendingResident])->merge($additionalResidents);

        CertificateRequest::factory()
            ->count(12)
            ->make()
            ->each(function (CertificateRequest $request) use ($allResidents, $residentUser, $admin): void {
                $selectedResident = $allResidents->random();
                $request->resident_id = $selectedResident->id;
                $request->requested_by = $selectedResident->user_id ?? $residentUser->id;

                if (in_array($request->status, [CertificateStatus::Approved, CertificateStatus::Released], true)) {
                    $request->approved_by = $admin->id;
                    $request->approved_at = now()->subDays(rand(1, 5));
                }

                if ($request->status === CertificateStatus::Released) {
                    $request->released_at = now()->subDays(rand(0, 2));
                }

                $request->save();
            });

        CertificateRequest::factory()->count(3)->create([
            'resident_id' => $resident->id,
            'requested_by' => $residentUser->id,
        ]);

        CertificateRequest::factory()->create([
            'resident_id' => $resident->id,
            'requested_by' => $residentUser->id,
            'approved_by' => $admin->id,
            'status' => CertificateStatus::Approved,
        ]);

        $activityEntries = [
            ['event' => 'Manual backup completed', 'description' => 'Nightly job finished successfully.'],
            ['event' => 'Certificate approved', 'description' => 'Approved residency certificate for Maria Dela Cruz.'],
            ['event' => 'Resident record updated', 'description' => 'Updated household information for Purok 4.'],
            ['event' => 'New user invited', 'description' => 'Invitation sent to the barangay treasurer.'],
        ];

        foreach ($activityEntries as $index => $entry) {
            ActivityLog::create([
                'user_id' => $index % 2 === 0 ? $admin->id : $clerk->id,
                'event' => $entry['event'],
                'description' => $entry['description'],
                'ip_address' => '127.0.0.1',
                'context' => ['source' => 'seeder'],
            ])->forceFill([
                'created_at' => now()->subMinutes(($index + 1) * 10),
                'updated_at' => now()->subMinutes(($index + 1) * 10),
            ])->save();
        }

        BackupJob::create([
            'file_path' => 'backups/brms-' . now()->format('Ymd-His') . '.zip',
            'status' => 'completed',
            'ran_by' => $clerk->id,
            'notes' => 'Automatic nightly run',
            'metadata' => ['size_mb' => 12.4],
            'started_at' => now()->subHours(2),
            'completed_at' => now()->subHours(2)->addMinutes(3),
        ]);

        BackupJob::create([
            'file_path' => 'backups/brms-failed-' . now()->format('Ymd-His') . '.zip',
            'status' => 'failed',
            'ran_by' => $admin->id,
            'notes' => 'Disk quota reached',
            'metadata' => ['size_mb' => 0],
            'started_at' => now()->subDay(),
            'completed_at' => now()->subDay()->addMinutes(1),
        ]);
    }
}
