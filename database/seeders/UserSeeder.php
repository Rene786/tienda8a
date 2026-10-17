<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Administrador TechStore', 'email' => 'admin@techstore.test', 'role' => 'admin'],
            ['name' => 'Test User', 'email' => 'test@example.com', 'role' => 'cliente'],
            ['name' => 'Ana García', 'email' => 'cliente01@techstore.test', 'role' => 'cliente'],
            ['name' => 'Luis Hernández', 'email' => 'cliente02@techstore.test', 'role' => 'cliente'],
            ['name' => 'María López', 'email' => 'cliente03@techstore.test', 'role' => 'cliente'],
            ['name' => 'Carlos Martínez', 'email' => 'cliente04@techstore.test', 'role' => 'cliente'],
            ['name' => 'Sofía Ramírez', 'email' => 'cliente05@techstore.test', 'role' => 'cliente'],
            ['name' => 'Diego Torres', 'email' => 'cliente06@techstore.test', 'role' => 'cliente'],
            ['name' => 'Valentina Cruz', 'email' => 'cliente07@techstore.test', 'role' => 'cliente'],
        ];

        for ($customerNumber = 8; $customerNumber <= 24; $customerNumber++) {
            $customerNumberPadded = str_pad((string) $customerNumber, 2, '0', STR_PAD_LEFT);
            $users[] = [
                'name' => 'Cliente Demo '.$customerNumberPadded,
                'email' => 'cliente'.$customerNumberPadded.'@techstore.test',
                'role' => 'cliente',
            ];
        }

        foreach ($users as $userData) {
            $user = User::withTrashed()->firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => 'password',
                    'email_verified_at' => now(),
                ],
            );

            if ($user->trashed()) {
                $user->restore();
            }

            $user->forceFill(['role' => $userData['role']])->save();
        }
    }
}
