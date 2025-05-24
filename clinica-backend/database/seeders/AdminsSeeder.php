<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use Spatie\Permission\Models\Role;

class AdminsSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('name', 'administrador')->first();

        if (!$adminRole) {
            $this->command->error('No existe el rol administrador.');
            return;
        }

        $usuariosAdmin = User::role('administrador')->get();

        foreach ($usuariosAdmin as $usuario) {
            $adminExistente = Admin::where('user_id', $usuario->id)->first();
            if (!$adminExistente) {
                Admin::create([
                    'user_id' => $usuario->id,
                ]);
            }
        }
    }
}
