<?php

namespace App\Services;

use App\Models\Membresia;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MembresiaPeriodService
{
    /**
     * @param  Collection<int, Membresia>  $memberships
     * @return array{fecha_inicio: Carbon, fecha_vencimiento: Carbon}
     */
    public function accumulatedPeriod(Membresia $anchor, Collection $memberships): array
    {
        $start = $anchor->fecha_inicio->copy()->startOfDay();
        $end = $anchor->fecha_vencimiento->copy()->endOfDay();
        $extendPeriod = true;

        while ($extendPeriod) {
            $extendPeriod = false;

            foreach ($memberships as $membership) {
                if (! in_array($membership->estado, ['activa', 'vencida'], true)) {
                    continue;
                }

                $membershipStart = $membership->fecha_inicio->copy()->startOfDay();
                $membershipEnd = $membership->fecha_vencimiento->copy()->endOfDay();
                $latestContiguousStart = $end->copy()->startOfDay()->addDay();

                if ($membershipStart->greaterThan($latestContiguousStart) || $membershipEnd->lessThan($start)) {
                    continue;
                }

                if ($membershipStart->lessThan($start)) {
                    $start = $membershipStart;
                    $extendPeriod = true;
                }

                if ($membershipEnd->greaterThan($end)) {
                    $end = $membershipEnd;
                    $extendPeriod = true;
                }
            }
        }

        return [
            'fecha_inicio' => $start,
            'fecha_vencimiento' => $end,
        ];
    }
}
