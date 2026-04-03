<?php

namespace Database\Seeders;

use App\Models\VaultCategory;
use App\Models\VaultEntry;
use App\Services\VaultEncryptionService;
use Illuminate\Database\Seeder;

class DemoVaultSeeder extends Seeder
{
    use DemoFamilyContext;

    public function run(): void
    {
        $this->loadDemoContext();

        $vault = new VaultEncryptionService;

        // ─────────────────────────────────────────────
        //  VAULT ENTRIES
        // ─────────────────────────────────────────────

        $categories = VaultCategory::where('family_id', $this->familyId())->get()->keyBy('slug');

        // Medical
        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['medical']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Family Pediatrician',
            'encrypted_data' => $vault->encrypt([
                'Doctor' => 'Dr. Rebecca Chen',
                'Practice' => 'Sunshine Pediatrics',
                'Phone' => '(555) 234-5678',
                'Address' => '4521 Medical Center Dr, Suite 200',
                'Patient Portal' => 'sunshinepeds.myportal.com',
            ]),
            'notes' => 'Annual checkups in August. Lily has a follow-up in April.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['medical']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Emma — Allergies & Medications',
            'encrypted_data' => $vault->encrypt([
                'Allergies' => 'Penicillin, tree nuts',
                'EpiPen Rx' => 'EpiPen Jr Auto-Injector',
                'Pharmacy' => 'CVS #4892 — (555) 345-6789',
                'Allergist' => 'Dr. Alan Park — (555) 456-7890',
            ]),
            'notes' => 'EpiPen expires Sept 2026. Refill in August.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['medical']->id,
            'created_by' => $this->mike->id,
            'title' => 'Family Dentist',
            'encrypted_data' => $vault->encrypt([
                'Dentist' => 'Dr. Maria Lopez',
                'Practice' => 'Bright Smiles Family Dental',
                'Phone' => '(555) 567-8901',
                'Next Appointments' => 'Jake & Lily: April 15. Emma: May 3.',
            ]),
            'notes' => 'Jake may need braces evaluation this summer.',
        ]);

        // Financial
        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['financial']->id,
            'created_by' => $this->mike->id,
            'title' => 'Joint Checking Account',
            'encrypted_data' => $vault->encrypt([
                'Bank' => 'First National Bank',
                'Account Number' => '****4829',
                'Routing Number' => '****7631',
                'Online Banking' => 'fnb.com',
                'Login' => 'mjohnson84',
            ]),
            'notes' => 'Primary household account. Auto-pay for mortgage and utilities.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['financial']->id,
            'created_by' => $this->mike->id,
            'title' => 'College Savings — 529 Plans',
            'encrypted_data' => $vault->encrypt([
                'Provider' => 'Vanguard 529',
                'Emma Account' => '****8812',
                'Jake Account' => '****8813',
                'Lily Account' => '****8814',
                'Login' => 'vanguard.com — mjohnson',
            ]),
            'notes' => 'Contributing $200/month per child. Review allocation annually.',
        ]);

        // Insurance
        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['insurance']->id,
            'created_by' => $this->mike->id,
            'title' => 'Health Insurance',
            'encrypted_data' => $vault->encrypt([
                'Provider' => 'Blue Cross Blue Shield',
                'Policy Number' => 'BCBS-****3947',
                'Group Number' => 'GRP-****2281',
                'Member Services' => '1-800-555-0199',
                'Portal' => 'bcbs.com/members',
            ]),
            'notes' => 'Through Mike\'s employer. Open enrollment in November.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['insurance']->id,
            'created_by' => $this->mike->id,
            'title' => 'Auto Insurance',
            'encrypted_data' => $vault->encrypt([
                'Provider' => 'State Farm',
                'Policy Number' => 'SF-****7722',
                'Agent' => 'Tom Bradley — (555) 678-9012',
                'Vehicles' => '2022 Honda Odyssey, 2020 Toyota RAV4',
            ]),
            'notes' => 'Renewal in July. Emma will need to be added when she gets her license.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['insurance']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Homeowners Insurance',
            'encrypted_data' => $vault->encrypt([
                'Provider' => 'State Farm',
                'Policy Number' => 'SF-HOME-****3318',
                'Agent' => 'Tom Bradley — (555) 678-9012',
                'Coverage' => '$350,000 dwelling / $100,000 personal property',
            ]),
            'notes' => 'Renewal in September. Consider increasing coverage.',
        ]);

        // Legal
        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['legal']->id,
            'created_by' => $this->mike->id,
            'title' => 'Wills & Estate Plan',
            'encrypted_data' => $vault->encrypt([
                'Attorney' => 'Jennifer Walsh, Esq.',
                'Firm' => 'Walsh & Associates',
                'Phone' => '(555) 789-0123',
                'Last Updated' => 'October 2025',
                'Guardian Designee' => 'Uncle David & Aunt Karen Johnson',
            ]),
            'notes' => 'Review and update in 2027. Copies in safe deposit box at First National.',
        ]);

        // Education
        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['education']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Emma — Westfield High School',
            'encrypted_data' => $vault->encrypt([
                'School' => 'Westfield High School',
                'Student ID' => '2024-****8831',
                'Counselor' => 'Mrs. Patricia Adams',
                'Parent Portal' => 'westfield.powerschool.com',
                'Portal Login' => 'sjohnson_parent',
                'GPA' => '3.8',
            ]),
            'notes' => 'Junior year. SAT scheduled for May. Looking at colleges this summer.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['education']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Jake — Lincoln Middle School',
            'encrypted_data' => $vault->encrypt([
                'School' => 'Lincoln Middle School',
                'Student ID' => '2024-****5547',
                'Counselor' => 'Mr. James Rivera',
                'Parent Portal' => 'lincoln.powerschool.com',
                'Portal Login' => 'sjohnson_parent',
            ]),
            'notes' => '7th grade. Soccer team tryouts in August. Might need math tutor.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['education']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Lily — Meadowbrook Elementary',
            'encrypted_data' => $vault->encrypt([
                'School' => 'Meadowbrook Elementary',
                'Student ID' => '2024-****2293',
                'Teacher' => 'Mrs. Amanda Foster (4th grade)',
                'Parent Portal' => 'meadowbrook.powerschool.com',
                'Portal Login' => 'sjohnson_parent',
            ]),
            'notes' => '4th grade. Piano recital in May. Spelling bee champion!',
        ]);

        // Personal
        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['personal']->id,
            'created_by' => $this->mike->id,
            'title' => 'Wi-Fi & Home Network',
            'encrypted_data' => $vault->encrypt([
                'Network Name' => 'JohnsonFamily5G',
                'Password' => 'B1scu1t-Loves-Treats!',
                'Router Admin' => '192.168.1.1 — admin / ****router',
                'ISP' => 'Comcast Xfinity — account ****4412',
                'ISP Support' => '1-800-XFINITY',
            ]),
            'notes' => 'Guest network: JohnsonGuest / Welcome2024',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['personal']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Streaming Services',
            'encrypted_data' => $vault->encrypt([
                'Netflix' => 'sarah.johnson@email.com',
                'Disney+' => 'mike.johnson@email.com',
                'Spotify Family' => 'mike.johnson@email.com',
                'YouTube Premium' => 'mike.johnson@email.com',
            ]),
            'notes' => 'Netflix and Disney+ are annual plans. Spotify renews monthly on the 15th.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['personal']->id,
            'created_by' => $this->mike->id,
            'title' => 'Emergency Contacts',
            'encrypted_data' => $vault->encrypt([
                'Grandma Joan' => '(555) 111-2222',
                'Uncle David' => '(555) 333-4444',
                'Aunt Karen' => '(555) 555-6666',
                'Neighbor (Sue Miller)' => '(555) 777-8888',
                'Poison Control' => '1-800-222-1222',
            ]),
            'notes' => 'Grandma Joan is 15 min away. David & Karen are designated guardians.',
        ]);
    }
}
