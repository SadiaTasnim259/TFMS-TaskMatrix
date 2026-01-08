<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuration;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            [
                'config_key' => 'current_session',
                'config_value' => '2024/2025',
                'data_type' => 'String',
                'description' => 'Current academic session',
                'is_active' => true,
            ],
            [
                'config_key' => 'min_weightage',
                'config_value' => '10',
                'data_type' => 'Integer',
                'description' => 'Minimum workload weightage for balanced status',
                'is_active' => true,
            ],
            [
                'config_key' => 'max_weightage',
                'config_value' => '20',
                'data_type' => 'Integer',
                'description' => 'Maximum workload weightage for balanced status',
                'is_active' => true,
            ],
            // Performance Evaluation Default Weights
            [
                'config_key' => 'perf_weight_teaching',
                'config_value' => '40',
                'data_type' => 'Integer',
                'description' => 'Default teaching weightage (%)',
                'is_active' => true,
            ],
            [
                'config_key' => 'perf_weight_research',
                'config_value' => '30',
                'data_type' => 'Integer',
                'description' => 'Default research weightage (%)',
                'is_active' => true,
            ],
            [
                'config_key' => 'perf_weight_admin',
                'config_value' => '20',
                'data_type' => 'Integer',
                'description' => 'Default admin weightage (%)',
                'is_active' => true,
            ],
            [
                'config_key' => 'perf_weight_support',
                'config_value' => '10',
                'data_type' => 'Integer',
                'description' => 'Default student support weightage (%)',
                'is_active' => true,
            ],
        ];

        foreach ($configs as $config) {
            Configuration::updateOrCreate(
                ['config_key' => $config['config_key']],
                $config
            );
        }
    }
}
