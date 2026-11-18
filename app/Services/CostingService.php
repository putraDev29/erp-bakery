<?php

namespace App\Services;

class CostingService
{
    /**
     * Compute New Moving Weighted Average Cost:
     * ((old_qty * old_avg) + (in_qty * in_cost)) / (old_qty + in_qty)
     */
    public function computeNewMovingAverage(float $currentQty, float $currentAvgCost, float $incomingQty, float $incomingCost): float
    {
        $currentQty = max(0.0, $currentQty);
        $incomingQty = max(0.0, $incomingQty);
        $totalQty = $currentQty + $incomingQty;

        if ($totalQty <= 0) {
            return round($incomingCost, 2);
        }

        $totalValuation = ($currentQty * $currentAvgCost) + ($incomingQty * $incomingCost);
        return round($totalValuation / $totalQty, 2);
    }

    /**
     * Compute production batch unit cost based on:
     * (material_cost + labor_cost + overhead_cost) / good_output_quantity
     */
    public function computeBatchUnitCost(float $materialCost, float $laborCost, float $overheadCost, float $goodQuantity): float
    {
        if ($goodQuantity <= 0) {
            return 0.0;
        }

        $totalCost = $materialCost + $laborCost + $overheadCost;
        return round($totalCost / $goodQuantity, 2);
    }
}
