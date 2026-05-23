<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BloodType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BloodType>
 */
final class BloodTypeFactory extends Factory
{
    private static int $index = 0;

    /** @var array<int, array<string, mixed>> */
    private static array $groups = [
        ['code' => 'A+',  'name' => 'A positivo',  'abo_group' => 'A',  'rh_factor' => '+', 'can_donate_to' => ['A+', 'AB+'],             'can_receive_from' => ['A+', 'A-', 'O+', 'O-'], 'is_universal_donor' => false, 'is_universal_recipient' => false],
        ['code' => 'A-',  'name' => 'A negativo',  'abo_group' => 'A',  'rh_factor' => '-', 'can_donate_to' => ['A+', 'A-', 'AB+', 'AB-'], 'can_receive_from' => ['A-', 'O-'],              'is_universal_donor' => false, 'is_universal_recipient' => false],
        ['code' => 'B+',  'name' => 'B positivo',  'abo_group' => 'B',  'rh_factor' => '+', 'can_donate_to' => ['B+', 'AB+'],             'can_receive_from' => ['B+', 'B-', 'O+', 'O-'], 'is_universal_donor' => false, 'is_universal_recipient' => false],
        ['code' => 'B-',  'name' => 'B negativo',  'abo_group' => 'B',  'rh_factor' => '-', 'can_donate_to' => ['B+', 'B-', 'AB+', 'AB-'], 'can_receive_from' => ['B-', 'O-'],              'is_universal_donor' => false, 'is_universal_recipient' => false],
        ['code' => 'O+',  'name' => 'O positivo',  'abo_group' => 'O',  'rh_factor' => '+', 'can_donate_to' => ['A+', 'B+', 'O+', 'AB+'], 'can_receive_from' => ['O+', 'O-'],              'is_universal_donor' => false, 'is_universal_recipient' => false],
        ['code' => 'O-',  'name' => 'O negativo',  'abo_group' => 'O',  'rh_factor' => '-', 'can_donate_to' => ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'], 'can_receive_from' => ['O-'], 'is_universal_donor' => true, 'is_universal_recipient' => false],
        ['code' => 'AB+', 'name' => 'AB positivo', 'abo_group' => 'AB', 'rh_factor' => '+', 'can_donate_to' => ['AB+'],                   'can_receive_from' => ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'], 'is_universal_donor' => false, 'is_universal_recipient' => true],
        ['code' => 'AB-', 'name' => 'AB negativo', 'abo_group' => 'AB', 'rh_factor' => '-', 'can_donate_to' => ['AB+', 'AB-'],            'can_receive_from' => ['A-', 'B-', 'O-', 'AB-'], 'is_universal_donor' => false, 'is_universal_recipient' => false],
    ];

    public function definition(): array
    {
        $group = self::$groups[self::$index % count(self::$groups)];
        self::$index++;

        return $group;
    }
}
