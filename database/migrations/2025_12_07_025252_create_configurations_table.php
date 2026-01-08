<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * PURPOSE: Create configurations table for dynamic system settings
     *
     * Design Pattern: Key-Value Store
     * 
     * Instead of hardcoding settings in code:
     * - Bad: define('MIN_WEIGHTAGE', 2.0); // Have to redeploy code to change
     * - Good: Store in database, change without deployment
     *
     * Fields Explained:
     * - id: Primary key
     * - config_key: Setting name (e.g., 'academic_year', 'min_weightage')
     * - config_value: Setting value (e.g., '2024/2025', '2.0')
     * - data_type: Type of value (String, Integer, Decimal, Boolean)
     * - description: What this setting does
     * - is_active: Can disable settings without deleting
     * - updated_by: Track who changed this setting
     * - updated_at: When setting was last changed
     *
     * Example Configuration Records:
     * | config_key | config_value | data_type | description |
     * |------------|--------------|-----------|-------------|
     * | academic_year | 2024/2025 | String | Current academic year |
     * | current_semester | 1 | Integer | Current semester (1 or 2) |
     * | min_weightage | 2.0 | Decimal | Minimum workload threshold |
     * | max_weightage | 8.0 | Decimal | Maximum workload threshold |
     * | weightage_academic | 1.0 | Decimal | Default weightage for Academic TF |
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();

            // Configuration Key and Value
            $table->string('config_key', 100)->unique()->comment('Configuration key/name');
            $table->text('config_value')->comment('Configuration value');

            // Type Information
            $table->enum('data_type', [
                'String',
                'Integer',
                'Decimal',
                'Boolean',
                'Date'
            ])->default('String')->comment('Data type of value');

            // Additional Info
            $table->text('description')->nullable()->comment('Description of setting');

            // Status
            $table->boolean('is_active')->default(true)->comment('Is setting active');

            // Audit Trail
            $table->unsignedBigInteger('updated_by')->nullable()->comment('User who updated');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()
                ->comment('When setting was updated');

            // Foreign Key
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Indexes
            $table->index('config_key');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * PURPOSE: Drop configurations table when rolling back
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configurations');
    }
}
