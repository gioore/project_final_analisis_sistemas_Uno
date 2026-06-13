<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PacienteTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::query()->create([
            'id' => '00000000-0000-4000-8000-000000000001',
            'name' => 'Test Hospital',
            'slug' => 'test-hospital',
            'data' => [],
        ]);

        $this->user = User::query()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->token = JWTAuth::fromUser($this->user);

        Paciente::query()->create([
            'tenant_id' => $this->tenant->id,
            'numero_expediente' => 'EXP-001',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'fecha_nacimiento' => '1990-01-15',
            'genero' => 'M',
        ]);

        Paciente::query()->create([
            'tenant_id' => $this->tenant->id,
            'numero_expediente' => 'EXP-002',
            'nombre' => 'María',
            'apellido' => 'García',
            'fecha_nacimiento' => '1985-05-20',
            'genero' => 'F',
        ]);
    }

    private function headers(): array
    {
        return [
            'X-Tenant-ID' => $this->tenant->id,
            'Authorization' => 'Bearer ' . $this->token,
        ];
    }

    public function test_list_pacientes(): void
    {
        $response = $this->getJson('/api/v1/pacientes', $this->headers());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_search_pacientes(): void
    {
        $response = $this->getJson('/api/v1/pacientes?search=Juan', $this->headers());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nombre', 'Juan');
    }

    public function test_filter_pacientes_by_genero(): void
    {
        $response = $this->getJson('/api/v1/pacientes?genero=F', $this->headers());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.genero', 'F');
    }

    public function test_create_paciente(): void
    {
        $payload = [
            'numero_expediente' => 'EXP-003',
            'nombre' => 'Carlos',
            'apellido' => 'López',
            'fecha_nacimiento' => '1978-11-30',
            'genero' => 'M',
            'telefono' => '555-0103',
            'tipo_sangre' => 'O+',
        ];

        $response = $this->postJson('/api/v1/pacientes', $payload, $this->headers());

        $response->assertStatus(201)
            ->assertJsonPath('data.nombre', 'Carlos')
            ->assertJsonPath('data.tenant_id', $this->tenant->id);

        $this->assertDatabaseHas('pacientes', [
            'numero_expediente' => 'EXP-003',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_create_paciente_duplicate_expediente(): void
    {
        $payload = [
            'numero_expediente' => 'EXP-001',
            'nombre' => 'Duplicado',
            'apellido' => 'Test',
            'fecha_nacimiento' => '2000-01-01',
            'genero' => 'M',
        ];

        $response = $this->postJson('/api/v1/pacientes', $payload, $this->headers());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['numero_expediente']);
    }

    public function test_show_paciente(): void
    {
        $paciente = Paciente::query()->where('numero_expediente', 'EXP-001')->first();

        $response = $this->getJson("/api/v1/pacientes/{$paciente->id}", $this->headers());

        $response->assertStatus(200)
            ->assertJsonPath('data.numero_expediente', 'EXP-001');
    }

    public function test_show_paciente_not_found(): void
    {
        $response = $this->getJson('/api/v1/pacientes/99999', $this->headers());

        $response->assertStatus(404);
    }

    public function test_update_paciente(): void
    {
        $paciente = Paciente::query()->where('numero_expediente', 'EXP-001')->first();

        $payload = [
            'numero_expediente' => 'EXP-001',
            'nombre' => 'Juan Actualizado',
            'apellido' => 'Pérez',
            'fecha_nacimiento' => '1990-01-15',
            'genero' => 'M',
        ];

        $response = $this->putJson("/api/v1/pacientes/{$paciente->id}", $payload, $this->headers());

        $response->assertStatus(200)
            ->assertJsonPath('data.nombre', 'Juan Actualizado');
    }

    public function test_delete_paciente(): void
    {
        $paciente = Paciente::query()->where('numero_expediente', 'EXP-001')->first();

        $response = $this->deleteJson("/api/v1/pacientes/{$paciente->id}", [], $this->headers());

        $response->assertStatus(200);

        $this->assertDatabaseMissing('pacientes', [
            'id' => $paciente->id,
        ]);
    }

    public function test_requires_jwt_token(): void
    {
        $response = $this->getJson('/api/v1/pacientes', [
            'X-Tenant-ID' => $this->tenant->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_requires_tenant_header(): void
    {
        $response = $this->getJson('/api/v1/pacientes', [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(400);
    }
}
