<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigurationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware is handled in routes
    }

    /**
     * Display the configuration dashboard.
     */
    public function index()
    {
        $academicSession = [
            'year' => Configuration::getValue('academic_year', '2024/2025'),
            'semester' => Configuration::getValue('current_semester', 1),
            'start_date' => Configuration::getValue('session_start_date', null),
            'end_date' => Configuration::getValue('session_end_date', null),
        ];

        $thresholds = [
            'min' => Configuration::getValue('min_weightage', 2.0),
            'max' => Configuration::getValue('max_weightage', 8.0),
        ];

        $categories = $this->getCategories();
        $weightages = [];

        foreach ($categories as $code => $name) {
            $weightages[$code] = Configuration::getValue("weightage_{$code}", $this->getDefaultWeightage($code));
        }

        return view('admin.configuration.index', compact('academicSession', 'thresholds', 'categories', 'weightages'));
    }

    /**
     * Show academic session configuration form.
     */
    public function editAcademicSession()
    {
        $config = [
            'year' => Configuration::getValue('academic_year', '2024/2025'),
            'semester' => Configuration::getValue('current_semester', 1),
            'start_date' => Configuration::getValue('session_start_date', null),
            'end_date' => Configuration::getValue('session_end_date', null),
        ];

        return view('admin.configuration.academic-session', compact('config'));
    }

    /**
     * Update academic session configuration.
     */
    public function updateAcademicSession(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'current_semester' => 'required|integer|in:1,2',
            'session_start_date' => 'required|date',
            'session_end_date' => 'required|date|after_or_equal:session_start_date',
        ], [
            'academic_year.regex' => 'Academic year must be in format: YYYY/YYYY (e.g., 2024/2025)',
            'session_end_date.after_or_equal' => 'End date must be equal to or after start date.',
        ]);

        try {
            DB::beginTransaction();

            $oldValues = [
                'academic_year' => Configuration::getValue('academic_year'),
                'current_semester' => Configuration::getValue('current_semester'),
                'session_start_date' => Configuration::getValue('session_start_date'),
                'session_end_date' => Configuration::getValue('session_end_date'),
            ];

            // Update configurations
            Configuration::setValue('academic_year', $validated['academic_year']);
            Configuration::setValue('current_semester', $validated['current_semester']);
            Configuration::setValue('session_start_date', $validated['session_start_date']);
            Configuration::setValue('session_end_date', $validated['session_end_date']);

            // Log audit
            AuditLog::log(
                'UPDATE',
                'Configuration',
                0,
                $oldValues,
                $validated,
                'Updated academic session configuration',
                'Academic Session'
            );

            DB::commit();

            return redirect()
                ->route('admin.configuration.index')
                ->with('success', 'Academic session updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error updating configuration: ' . $e->getMessage());
        }
    }

    /**
     * Show workload thresholds configuration form.
     */
    public function editThresholds()
    {
        $thresholds = [
            'min' => Configuration::getValue('min_weightage', 2.0),
            'max' => Configuration::getValue('max_weightage', 8.0),
        ];

        return view('admin.configuration.thresholds', compact('thresholds'));
    }

    /**
     * Update workload thresholds.
     */
    public function updateThresholds(Request $request)
    {
        $validated = $request->validate([
            'min_weightage' => 'required|numeric|min:0.5|max:10',
            'max_weightage' => 'required|numeric|min:0.5|max:10|gt:min_weightage',
        ], [
            'max_weightage.gt' => 'Maximum weightage must be greater than minimum weightage.',
        ]);

        try {
            DB::beginTransaction();

            $oldValues = [
                'min_weightage' => Configuration::getValue('min_weightage'),
                'max_weightage' => Configuration::getValue('max_weightage'),
            ];

            // Update thresholds
            Configuration::setValue('min_weightage', $validated['min_weightage']);
            Configuration::setValue('max_weightage', $validated['max_weightage']);

            // Log audit
            AuditLog::log(
                'UPDATE',
                'Configuration',
                0,
                $oldValues,
                $validated,
                'Updated workload thresholds',
                'Workload Thresholds'
            );

            DB::commit();

            return redirect()
                ->route('admin.configuration.index')
                ->with('success', 'Workload thresholds updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error updating thresholds: ' . $e->getMessage());
        }
    }

    /**
     * Show default weightages configuration form.
     */
    public function editWeightages()
    {
        $categories = $this->getCategories();
        $weightages = [];

        foreach ($categories as $code => $name) {
            $weightages[$code] = Configuration::getValue("weightage_{$code}", $this->getDefaultWeightage($code));
        }

        return view('admin.configuration.weightages', compact('categories', 'weightages'));
    }

    /**
     * Update default weightages by category.
     */
    public function updateWeightages(Request $request)
    {
        $categories = $this->getCategories();
        $rules = [];

        foreach ($categories as $code => $name) {
            $rules["weightage_{$code}"] = 'required|numeric|min:0.5|max:10';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            $oldValues = [];
            $newValues = [];

            foreach ($categories as $code => $name) {
                $key = "weightage_{$code}";
                $oldValues[$key] = Configuration::getValue($key);
                $newValues[$key] = $validated[$key];

                Configuration::setValue($key, $validated[$key]);
            }

            // Log audit
            AuditLog::log(
                'UPDATE',
                'Configuration',
                0,
                $oldValues,
                $newValues,
                'Updated default weightages by category',
                'Category Weightages'
            );

            DB::commit();

            return redirect()
                ->route('admin.configuration.index')
                ->with('success', 'Default weightages updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error updating weightages: ' . $e->getMessage());
        }
    }

    /**
     * Show performance weights configuration form.
     */
    public function editPerformanceWeights()
    {
        $weights = [
            'teaching' => Configuration::getValue('perf_weight_teaching', 40),
            'research' => Configuration::getValue('perf_weight_research', 30),
            'admin' => Configuration::getValue('perf_weight_admin', 20),
            'support' => Configuration::getValue('perf_weight_support', 10),
        ];

        return view('admin.configuration.performance-weights', compact('weights'));
    }

    /**
     * Update performance weights.
     */
    public function updatePerformanceWeights(Request $request)
    {
        $validated = $request->validate([
            'perf_weight_teaching' => 'required|integer|min:0|max:100',
            'perf_weight_research' => 'required|integer|min:0|max:100',
            'perf_weight_admin' => 'required|integer|min:0|max:100',
            'perf_weight_support' => 'required|integer|min:0|max:100',
        ]);

        // Validate sum is 100
        $sum = $validated['perf_weight_teaching'] + 
               $validated['perf_weight_research'] + 
               $validated['perf_weight_admin'] + 
               $validated['perf_weight_support'];
        
        if ($sum !== 100) {
            return back()
                ->withInput()
                ->withErrors(['total' => "Total percentage must be 100%. Current total: {$sum}%"]);
        }

        try {
            DB::beginTransaction();

            $oldValues = [];
            $newValues = [];

            foreach ($validated as $key => $value) {
                $oldValues[$key] = Configuration::getValue($key);
                $newValues[$key] = $value;
                Configuration::setValue($key, $value);
            }

            // Log audit
            AuditLog::log(
                'UPDATE',
                'Configuration',
                0,
                $oldValues,
                $newValues,
                'Updated performance evaluation weights',
                'Performance Weights'
            );

            DB::commit();

            return redirect()
                ->route('admin.configuration.index')
                ->with('success', 'Performance weights updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating weights: ' . $e->getMessage());
        }
    }

    /**
     * Reset configuration to defaults.
     */
    public function resetDefaults()
    {
        // This requires user confirmation
        return view('admin.configuration.confirm-reset');
    }

    /**
     * Confirm reset to defaults.
     */
    public function confirmReset(Request $request)
    {
        $request->validate(['confirm' => 'required|accepted']);

        try {
            DB::beginTransaction();

            // Store old values before reset
            $oldValues = [];
            foreach (['academic_year', 'current_semester', 'min_weightage', 'max_weightage'] as $key) {
                $oldValues[$key] = Configuration::getValue($key);
            }

            // Reset to defaults
            Configuration::where('config_key', 'like', 'weightage_%')->delete();
            Configuration::setValue('academic_year', '2024/2025');
            Configuration::setValue('current_semester', 1);
            Configuration::setValue('min_weightage', 2.0);
            Configuration::setValue('max_weightage', 8.0);

            // Log audit
            AuditLog::log(
                'UPDATE',
                'Configuration',
                0,
                $oldValues,
                ['status' => 'reset_to_defaults'],
                'Reset all configurations to default values',
                'Configuration Reset'
            );

            DB::commit();

            return redirect()
                ->route('admin.configuration.index')
                ->with('success', 'Configuration reset to defaults successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error resetting configuration: ' . $e->getMessage());
        }
    }

    /**
     * Get available task force categories.
     */
    private function getCategories()
    {
        return [
            'Academic' => 'Academic',
            'Research' => 'Research',
            'Accreditation' => 'Accreditation',
            'Quality' => 'Quality Assurance',
            'Strategic' => 'Strategic Planning',
            'Administrative' => 'Administrative',
        ];
    }

    /**
     * Get default weightage for a category.
     */
    private function getDefaultWeightage($category)
    {
        $defaults = [
            'Academic' => 1.0,
            'Research' => 1.5,
            'Accreditation' => 2.0,
            'Quality' => 1.5,
            'Strategic' => 2.5,
            'Administrative' => 1.0,
        ];

        return $defaults[$category] ?? 1.0;
    }
}
