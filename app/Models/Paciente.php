<?php

namespace App\Models;

use Database\Factories\PacienteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paciente extends Model
{
    /** @use HasFactory<PacienteFactory> */
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'numero_expediente',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'genero',
        'direccion',
        'telefono',
        'email',
        'tipo_sangre',
        'alergias_conocidas',
        'contacto_emergencia_nombre',
        'contacto_emergencia_telefono',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }
}
