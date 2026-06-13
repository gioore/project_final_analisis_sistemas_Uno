<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        $query = Paciente::query()
            ->where('tenant_id', $tenant->id);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search): void {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('numero_expediente', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        if ($request->filled('genero')) {
            $query->where('genero', $request->string('genero'));
        }

        $pacientes = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $pacientes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        $validated = $request->validate([
            'numero_expediente' => ['required', 'string', 'max:50'],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'genero' => ['required', 'string', 'in:M,F,Otro'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'tipo_sangre' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'alergias_conocidas' => ['nullable', 'string', 'max:1000'],
            'contacto_emergencia_nombre' => ['nullable', 'string', 'max:255'],
            'contacto_emergencia_telefono' => ['nullable', 'string', 'max:20'],
        ]);

        $exists = Paciente::query()
            ->where('tenant_id', $tenant->id)
            ->where('numero_expediente', $validated['numero_expediente'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe un paciente con este número de expediente.',
                'errors' => ['numero_expediente' => ['El número de expediente ya está registrado.']],
            ], 422);
        }

        $paciente = Paciente::query()->create([
            'tenant_id' => $tenant->id,
            ...$validated,
        ]);

        return response()->json([
            'data' => $paciente,
            'message' => 'Paciente creado correctamente.',
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        $paciente = Paciente::query()
            ->where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->first();

        if ($paciente === null) {
            return response()->json([
                'message' => 'Paciente no encontrado.',
            ], 404);
        }

        return response()->json([
            'data' => $paciente,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        $paciente = Paciente::query()
            ->where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->first();

        if ($paciente === null) {
            return response()->json([
                'message' => 'Paciente no encontrado.',
            ], 404);
        }

        $validated = $request->validate([
            'numero_expediente' => ['required', 'string', 'max:50'],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'genero' => ['required', 'string', 'in:M,F,Otro'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'tipo_sangre' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'alergias_conocidas' => ['nullable', 'string', 'max:1000'],
            'contacto_emergencia_nombre' => ['nullable', 'string', 'max:255'],
            'contacto_emergencia_telefono' => ['nullable', 'string', 'max:20'],
        ]);

        $exists = Paciente::query()
            ->where('tenant_id', $tenant->id)
            ->where('numero_expediente', $validated['numero_expediente'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe otro paciente con este número de expediente.',
                'errors' => ['numero_expediente' => ['El número de expediente ya está registrado.']],
            ], 422);
        }

        $paciente->update($validated);

        return response()->json([
            'data' => $paciente,
            'message' => 'Paciente actualizado correctamente.',
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        $paciente = Paciente::query()
            ->where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->first();

        if ($paciente === null) {
            return response()->json([
                'message' => 'Paciente no encontrado.',
            ], 404);
        }

        $paciente->delete();

        return response()->json([
            'message' => 'Paciente eliminado correctamente.',
        ]);
    }
}
