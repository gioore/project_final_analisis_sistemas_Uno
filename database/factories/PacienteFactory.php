<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paciente>
 */
class PacienteFactory extends Factory
{
    public function definition(): array
    {
        $generos = ['M', 'F', 'Otro'];
        $tiposSangre = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

        return [
            'tenant_id' => Tenant::factory(),
            'numero_expediente' => 'EXP-' . strtoupper(fake()->bothify('####-???')),
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'fecha_nacimiento' => fake()->date('Y-m-d', '-1 year'),
            'genero' => fake()->randomElement($generos),
            'direccion' => fake()->optional()->address(),
            'telefono' => fake()->optional()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'tipo_sangre' => fake()->optional()->randomElement($tiposSangre),
            'alergias_conocidas' => fake()->optional()->sentence(),
            'contacto_emergencia_nombre' => fake()->optional()->name(),
            'contacto_emergencia_telefono' => fake()->optional()->phoneNumber(),
        ];
    }

    public function withTenant(string $tenantId): static
    {
        return $this->state(fn(array $attributes) => [
            'tenant_id' => $tenantId,
        ]);
    }
}
