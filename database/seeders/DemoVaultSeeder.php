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

        $categories = VaultCategory::where('family_id', $this->familyId())->get()->keyBy('slug');

        // ─────────────────────────────────────────────
        //  MEDICAL
        // ─────────────────────────────────────────────

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
            'notes' => 'Annual checkups in August. Naia has a follow-up in April.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['medical']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Zara — Allergies & Medications',
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
                'Next Appointments' => 'Kenji & Naia: April 15. Zara: May 3.',
            ]),
            'notes' => 'Kenji may need braces evaluation this summer.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['medical']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Kenji — Medications',
            'encrypted_data' => $vault->encrypt([
                'Medication' => 'Concerta 36mg (daily)',
                'Prescribing Doctor' => 'Dr. Priya Sharma — (555) 678-1234',
                'Pharmacy' => 'Walgreens #2291 — (555) 234-8765',
                'Refill Due' => 'First of each month',
            ]),
            'notes' => 'Do not skip on school days. 3-month supply requested at each visit.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['medical']->id,
            'created_by' => $this->mike->id,
            'title' => 'Vision — Eye Prescriptions',
            'encrypted_data' => $vault->encrypt([
                'Zara (right)' => '-1.25 / -0.50 x 180',
                'Zara (left)' => '-1.50 / -0.25 x 175',
                'Naia (right)' => '-0.75 sphere',
                'Naia (left)' => '-0.75 sphere',
                'Optometrist' => 'Dr. Kim — Clear Vision Center (555) 789-3210',
            ]),
            'notes' => 'Both need glasses updated. Next exam: September.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['medical']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Family Doctor (Adults)',
            'encrypted_data' => $vault->encrypt([
                'Doctor' => 'Dr. James Okafor',
                'Practice' => 'Westside Primary Care',
                'Phone' => '(555) 321-4567',
                'Portal' => 'westsideprimary.com/portal',
                'Adaeze Login' => 'aellis_patient',
                'Marcus Login' => 'mellis_patient',
            ]),
            'notes' => 'Annual physicals in October for both adults.',
        ]);

        // ─────────────────────────────────────────────
        //  FINANCIAL
        // ─────────────────────────────────────────────

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
                'Login' => 'aellis_primary',
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
                'Zara Account' => '****8812',
                'Kenji Account' => '****8813',
                'Naia Account' => '****8814',
                'Login' => 'vanguard.com — aellis',
            ]),
            'notes' => 'Contributing $200/month per child. Review allocation annually.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['financial']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Car Loan — Honda Odyssey',
            'encrypted_data' => $vault->encrypt([
                'Lender' => 'Chase Auto Finance',
                'Account Number' => '****3341',
                'Monthly Payment' => '$487',
                'Payoff Date' => 'March 2027',
                'Online Portal' => 'chase.com/auto',
            ]),
            'notes' => 'Auto-pay from joint checking on the 3rd.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['financial']->id,
            'created_by' => $this->mike->id,
            'title' => 'Emergency Fund — High-Yield Savings',
            'encrypted_data' => $vault->encrypt([
                'Bank' => 'Marcus by Goldman Sachs',
                'Account Number' => '****9201',
                'APY' => '4.5%',
                'Login' => 'marcus.com — aellis@email.com',
            ]),
            'notes' => 'Goal: 6 months of expenses ($28,000). Currently at $22,500.',
        ]);

        // ─────────────────────────────────────────────
        //  INSURANCE
        // ─────────────────────────────────────────────

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
            'notes' => 'Through Adaeze\'s employer. Open enrollment in November.',
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
            'notes' => 'Renewal in July. Zara will need to be added when she gets her license.',
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

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['insurance']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Dental Insurance',
            'encrypted_data' => $vault->encrypt([
                'Provider' => 'Delta Dental',
                'Policy Number' => 'DD-****6614',
                'Group Number' => 'GRP-****5590',
                'Member Services' => '1-800-555-3344',
                'Annual Max' => '$1,500 per person',
            ]),
            'notes' => 'Covers 100% preventive, 80% basic, 50% major. Orthodontia has separate lifetime max.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['insurance']->id,
            'created_by' => $this->mike->id,
            'title' => 'Life Insurance',
            'encrypted_data' => $vault->encrypt([
                'Adaeze Policy' => 'Northwestern Mutual — ****2210 — $500k term',
                'Marcus Policy' => 'Northwestern Mutual — ****2211 — $500k term',
                'Agent' => 'Sandra Reyes — (555) 456-0011',
                'Beneficiaries' => 'Each other (primary), kids (contingent)',
            ]),
            'notes' => 'Both 20-year term, expire 2038. Review coverage when Naia turns 18.',
        ]);

        // ─────────────────────────────────────────────
        //  LEGAL
        // ─────────────────────────────────────────────

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
                'Guardian Designee' => 'Uncle David & Aunt Karen',
            ]),
            'notes' => 'Review and update in 2027. Copies in safe deposit box at First National.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['legal']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Passports',
            'encrypted_data' => $vault->encrypt([
                'Adaeze' => 'Expires June 2030 — stored in fireproof safe',
                'Marcus' => 'Expires February 2029 — stored in fireproof safe',
                'Zara' => 'Expires November 2028',
                'Kenji' => 'Expires March 2031',
                'Naia' => 'Expires August 2030',
            ]),
            'notes' => 'Marcus passport needs renewal before the 2029 family trip. Naia may need updated photo.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['legal']->id,
            'created_by' => $this->mike->id,
            'title' => 'Property Deed & Mortgage',
            'encrypted_data' => $vault->encrypt([
                'Lender' => 'Wells Fargo Home Mortgage',
                'Loan Number' => '****8847',
                'Rate' => '3.25% fixed (30-year)',
                'Monthly Payment' => '$1,842',
                'Maturity Date' => 'April 2051',
            ]),
            'notes' => 'Deed copy in fireproof safe. Digital copy in this vault.',
        ]);

        // ─────────────────────────────────────────────
        //  EDUCATION
        // ─────────────────────────────────────────────

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['education']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Zara — Lakewood High School',
            'encrypted_data' => $vault->encrypt([
                'School' => 'Lakewood High School',
                'Student ID' => '2024-****8831',
                'Counselor' => 'Ms. Patricia Adams',
                'Parent Portal' => 'lakewood.powerschool.com',
                'Portal Login' => 'aellis_parent',
                'GPA' => '3.8',
            ]),
            'notes' => 'Junior year. SAT scheduled for May. Looking at colleges this summer.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['education']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Kenji — Riverside Middle School',
            'encrypted_data' => $vault->encrypt([
                'School' => 'Riverside Middle School',
                'Student ID' => '2024-****5547',
                'Counselor' => 'Mr. James Rivera',
                'Parent Portal' => 'riverside.powerschool.com',
                'Portal Login' => 'aellis_parent',
            ]),
            'notes' => '7th grade. Soccer team tryouts in August. IEP meeting in March.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['education']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Naia — Cedar Ridge Elementary',
            'encrypted_data' => $vault->encrypt([
                'School' => 'Cedar Ridge Elementary',
                'Student ID' => '2024-****2293',
                'Teacher' => 'Mrs. Amanda Foster (4th grade)',
                'Parent Portal' => 'cedarridge.powerschool.com',
                'Portal Login' => 'aellis_parent',
            ]),
            'notes' => '4th grade. Art show in May. Spelling bee champion 2025!',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['education']->id,
            'created_by' => $this->mike->id,
            'title' => 'Zara — SAT & College Prep',
            'encrypted_data' => $vault->encrypt([
                'SAT Test Date' => 'May 2026',
                'Test Center' => 'Lakewood High School',
                'Khan Academy Login' => 'zara.ellis.sat',
                'College Board Login' => 'zellis2026',
                'Target Schools' => 'U of Michigan, Northwestern, UVA, Howard',
            ]),
            'notes' => 'Practice scores: 1320 composite. Target 1400+. Retake in October if needed.',
        ]);

        // ─────────────────────────────────────────────
        //  PERSONAL
        // ─────────────────────────────────────────────

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['personal']->id,
            'created_by' => $this->mike->id,
            'title' => 'Wi-Fi & Home Network',
            'encrypted_data' => $vault->encrypt([
                'Network Name' => 'EllisFam5G',
                'Password' => 'Tr33house-2025!',
                'Router Admin' => '192.168.1.1 — admin / ****router',
                'ISP' => 'Comcast Xfinity — account ****4412',
                'ISP Support' => '1-800-XFINITY',
            ]),
            'notes' => 'Guest network: EllisGuest / Welcome2025',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['personal']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Streaming Services',
            'encrypted_data' => $vault->encrypt([
                'Netflix' => 'adaeze.ellis@email.com (Premium)',
                'Disney+' => 'marcus.ellis@email.com (annual)',
                'Spotify Family' => 'marcus.ellis@email.com (6 members)',
                'YouTube Premium' => 'family plan',
                'Apple TV+' => 'adaeze.ellis@email.com',
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

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['personal']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Vehicle Records — Honda Odyssey',
            'encrypted_data' => $vault->encrypt([
                'VIN' => '5FNRL6H79NB****312',
                'License Plate' => '****-7RK',
                'Oil Change Due' => 'Every 5,000 miles — next at 62,400',
                'Tire Rotation' => 'Every 7,500 miles',
                'Mechanic' => 'Garcia Auto — (555) 210-4488',
            ]),
            'notes' => 'Registration renews in August. Insurance card in glove box.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['personal']->id,
            'created_by' => $this->mike->id,
            'title' => 'Vehicle Records — Toyota RAV4',
            'encrypted_data' => $vault->encrypt([
                'VIN' => 'JTMRWRFV4LD****889',
                'License Plate' => '****-4WP',
                'Oil Change Due' => 'Every 5,000 miles — next at 48,200',
                'Mechanic' => 'Garcia Auto — (555) 210-4488',
            ]),
            'notes' => 'Zara using it for driving practice. Note any new scratches.',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyId(),
            'vault_category_id' => $categories['personal']->id,
            'created_by' => $this->sarah->id,
            'title' => 'Appliance Warranties',
            'encrypted_data' => $vault->encrypt([
                'Refrigerator' => 'Samsung RF28 — warranty until Dec 2026 — (800) 726-7864',
                'Washer/Dryer' => 'LG WM4000H — warranty until Oct 2025 — (800) 243-0000',
                'Dishwasher' => 'Bosch SHPM88Z — warranty until Aug 2026 — (800) 944-2904',
                'HVAC' => 'Carrier — 10-year parts — (800) 227-7437',
            ]),
            'notes' => 'Washer warranty expired. Consider extended plan — LG has been making a noise.',
        ]);
    }
}
