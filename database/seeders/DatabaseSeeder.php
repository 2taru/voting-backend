<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Створення Адміна
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@mail.com',
            'password' => Hash::make('123456'),
            'national_id' => 'ADMIN001',
            'role' => 'admin',
            'is_verified' => true,
        ]);

        // Створення Кандидатів
        $candidatesData = [
            ['Petrenko_Candidate', 'cand1@mail.com', 'CAND001'],
            ['Shevchenko_Candidate', 'cand2@mail.com', 'CAND002'],
            ['Kovalenko_Candidate', 'cand3@mail.com', 'CAND003'],
            ['Bondar_Candidate', 'cand4@mail.com', 'CAND004'],
            ['Tkachenko_Candidate', 'cand5@mail.com', 'CAND005'],
        ];

        foreach ($candidatesData as $data) {
            User::create([
                'name' => $data[0],
                'email' => $data[1],
                'password' => Hash::make('123456'),
                'national_id' => $data[2],
                'role' => 'voter',
                'is_verified' => true,
            ]);
        }

        // Створення звичайних виборців
        $votersData = [
            ['Ivan', 'voter1@mail.com', 'VOTE001'],
            ['Sasha', 'voter2@mail.com', 'VOTE002'],
            ['Dima', 'voter3@mail.com', 'VOTE003'],
            ['Roma', 'voter4@mail.com', 'VOTE004'],
            ['Ivan', 'voter5@mail.com', 'VOTE005'],
            ['Max', 'voter6@mail.com', 'VOTE006'],
            ['Emma', 'voter7@mail.com', 'VOTE007'],
            ['Ginny', 'voter8@mail.com', 'VOTE008'],
            ['Walter', 'voter9@mail.com', 'VOTE009'],
            ['Lara', 'voter10@mail.com', 'VOTE0010'],
        ];

        foreach ($votersData as $data) {
            User::create([
                'name' => $data[0],
                'email' => $data[1],
                'password' => Hash::make('123456'),
                'national_id' => $data[2],
                'role' => 'voter',
                'is_verified' => true,
            ]);
        }
    }
}