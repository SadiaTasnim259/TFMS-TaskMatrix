<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkloadThresholdController extends Controller
{
    /**
     * Show the workload threshold editing form.
     */
    public function edit()
    {
        $thresholds = [
            'min' => Configuration::getValue('min_weightage', 2.0),
            'max' => Configuration::getValue('max_weightage', 8.0),
        ];

        return view('admin.workload.thresholds', compact('thresholds'));
    }

    /**
     * Update the workload thresholds.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'min_weightage' => 'required|numeric|min:0|max:20',
            'max_weightage' => 'required|numeric|min:0|max:20|gt:min_weightage',
        ], [
            'max_weightage.gt' => 'Maximum workload must be greater than minimum workload.',
        ]);

        try {
            DB::beginTransaction();

            $oldValues = [
                'min_weightage' => Configuration::getValue('min_weightage'),
                'max_weightage' => Configuration::getValue('max_weightage'),
            ];

            Configuration::setValue('min_weightage', $validated['min_weightage'], auth()->id(), 'Decimal');
            Configuration::setValue('max_weightage', $validated['max_weightage'], auth()->id(), 'Decimal');

            AuditLog::log(
                'UPDATE',
                'Configuration',
                0,
                $oldValues,
                $validated,
                'Updated Workload Threshold Range',
                'Workload Thresholds'
            );

            DB::commit();

            return redirect()->route('admin.workload.thresholds.edit')
                ->with('success', 'Workload thresholds updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating thresholds: ' . $e->getMessage());
        }
    }
}
