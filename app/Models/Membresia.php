<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\MembresiaFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membresia extends Model
{
    /** @use HasFactory<MembresiaFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'tipo_membresia_id',
        'fecha_inicio',
        'fecha_vencimiento',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_vencimiento' => 'datetime',
    ];

    /**
     * Apply the membership overview filters to the query.
     *
     * @param  array{estado?: string|null, tipo_membresia?: int|string|null, fecha_desde?: string|null, fecha_hasta?: string|null, busqueda?: string|null}  $filters
     */
    #[Scope]
    protected function matchingOverviewFilters(Builder $query, array $filters, Carbon $now): void
    {
        $states = preg_split('/\s+/', trim((string) ($filters['estado'] ?? 'todas'))) ?: [];
        $states = array_values(array_filter($states));

        if ($states !== [] && ! in_array('todas', $states, true)) {
            $query->where(function (Builder $query) use ($states, $now): void {
                foreach ($states as $state) {
                    $query->orWhere(function (Builder $query) use ($state, $now): void {
                        match ($state) {
                            'activas' => $query->where('estado', 'activa')
                                ->where('fecha_inicio', '<=', $now)
                                ->where('fecha_vencimiento', '>=', $now),
                            'recien_compradas' => $query->where('estado', 'activa')
                                ->whereBetween('fecha_inicio', [$now->copy()->subDays(7), $now])
                                ->where('fecha_vencimiento', '>=', $now),
                            'por_vencer' => $query->where('estado', 'activa')
                                ->where('fecha_inicio', '<=', $now)
                                ->whereBetween('fecha_vencimiento', [$now, $now->copy()->addDays(7)]),
                            'vencidas' => $query->where(function (Builder $query) use ($now): void {
                                $query->where('estado', 'vencida')
                                    ->orWhere('fecha_vencimiento', '<', $now);
                            }),
                            default => null,
                        };
                    });
                }
            });
        }

        if (! empty($filters['tipo_membresia'])) {
            $query->where('tipo_membresia_id', $filters['tipo_membresia']);
        }

        if (! empty($filters['fecha_desde'])) {
            $query->whereDate('fecha_inicio', '>=', $filters['fecha_desde']);
        }

        if (! empty($filters['fecha_hasta'])) {
            $query->whereDate('fecha_vencimiento', '<=', $filters['fecha_hasta']);
        }

        $terms = preg_split('/\s+/', trim((string) ($filters['busqueda'] ?? ''))) ?: [];

        foreach (array_filter($terms) as $term) {
            $query->whereHas('cliente', function (Builder $query) use ($term): void {
                $query->where('nombre', 'like', "%{$term}%")
                    ->orWhere('cedula', 'like', "%{$term}%")
                    ->orWhere('telefono', 'like', "%{$term}%");
            });
        }
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tipoMembresia()
    {
        return $this->belongsTo(TipoMembresia::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
