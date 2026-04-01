<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BloodType;
use Illuminate\Database\Seeder;

final class BloodTypeSeeder extends Seeder
{
    public function run(): void
    {
        $bloodTypes = [
            [
                'code' => 'A+',
                'name' => 'A positivo',
                'abo_group' => 'A',
                'rh_factor' => '+',
                'can_donate_to' => ['A+', 'AB+'],
                'can_receive_from' => ['A+', 'A-', 'O+', 'O-'],
                'is_universal_donor' => false,
                'is_universal_recipient' => false,
            ],
            [
                'code' => 'A-',
                'name' => 'A negativo',
                'abo_group' => 'A',
                'rh_factor' => '-',
                'can_donate_to' => ['A+', 'A-', 'AB+', 'AB-'],
                'can_receive_from' => ['A-', 'O-'],
                'is_universal_donor' => false,
                'is_universal_recipient' => false,
            ],
            [
                'code' => 'B+',
                'name' => 'B positivo',
                'abo_group' => 'B',
                'rh_factor' => '+',
                'can_donate_to' => ['B+', 'AB+'],
                'can_receive_from' => ['B+', 'B-', 'O+', 'O-'],
                'is_universal_donor' => false,
                'is_universal_recipient' => false,
            ],
            [
                'code' => 'B-',
                'name' => 'B negativo',
                'abo_group' => 'B',
                'rh_factor' => '-',
                'can_donate_to' => ['B+', 'B-', 'AB+', 'AB-'],
                'can_receive_from' => ['B-', 'O-'],
                'is_universal_donor' => false,
                'is_universal_recipient' => false,
            ],
            [
                'code' => 'AB+',
                'name' => 'AB positivo',
                'abo_group' => 'AB',
                'rh_factor' => '+',
                'can_donate_to' => ['AB+'],
                'can_receive_from' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
                'is_universal_donor' => false,
                'is_universal_recipient' => true,
            ],
            [
                'code' => 'AB-',
                'name' => 'AB negativo',
                'abo_group' => 'AB',
                'rh_factor' => '-',
                'can_donate_to' => ['AB+', 'AB-'],
                'can_receive_from' => ['A-', 'B-', 'AB-', 'O-'],
                'is_universal_donor' => false,
                'is_universal_recipient' => false,
            ],
            [
                'code' => 'O+',
                'name' => 'O positivo',
                'abo_group' => 'O',
                'rh_factor' => '+',
                'can_donate_to' => ['A+', 'B+', 'AB+', 'O+'],
                'can_receive_from' => ['O+', 'O-'],
                'is_universal_donor' => false,
                'is_universal_recipient' => false,
            ],
            [
                'code' => 'O-',
                'name' => 'O negativo',
                'abo_group' => 'O',
                'rh_factor' => '-',
                'can_donate_to' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
                'can_receive_from' => ['O-'],
                'is_universal_donor' => true,
                'is_universal_recipient' => false,
            ],

        ];
        foreach ($bloodTypes as $type) {
            BloodType::create($type);
        }
    }
}
