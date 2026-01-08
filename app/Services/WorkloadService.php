<?php

namespace App\Services;

use App\Models\Configuration;
use App\Models\WorkloadSubmission;

class WorkloadService
{
    /**
     * Calculate workload status based on total hours
     * 
     * @param float $totalHours
     * @param float|null $min Optional min threshold
     * @param float|null $max Optional max threshold
     * @return string 'Under-loaded', 'Balanced', 'Overloaded'
     */
    public function calculateStatus($totalHours, $min = null, $max = null)
    {
        $minWeightage = $min ?? (float) (Configuration::where('config_key', 'min_weightage')->value('config_value') ?? 10);
        $maxWeightage = $max ?? (float) (Configuration::where('config_key', 'max_weightage')->value('config_value') ?? 20);

        if ($totalHours < $minWeightage) {
            return 'Under-loaded';
        } elseif ($totalHours > $maxWeightage) {
            return 'Overloaded';
        } else {
            return 'Balanced';
        }
    }

    /**
     * Get status color class
     */
    public function getStatusColor($status)
    {
        return match($status) {
            'Under-loaded' => 'text-yellow-600 bg-yellow-100',
            'Balanced' => 'text-green-600 bg-green-100',
            'Overloaded' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }
}
