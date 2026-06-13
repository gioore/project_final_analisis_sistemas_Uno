<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $tenant = Tenant::query()->first();

        if ($tenant === null) {
            return;
        }

        Paciente::factory()
            ->count(20)
            ->withTenant($tenant->id)
            ->create();
    }
}
