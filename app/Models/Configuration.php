<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'configurations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'config_key',
        'config_value',
        'data_type',
        'description',
        'is_active',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $timestamps = false;

    /**
     * RELATIONSHIPS
     * =====================================================================
     */

    /**
     * Get the user who last updated this configuration.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * SCOPES
     * =====================================================================
     */

    /**
     * Scope to get only active configurations.
     *
     * Usage: Configuration::active()->get()
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to find configuration by key.
     *
     * Usage: Configuration::byKey('academic_year')->first()
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $key
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByKey($query, $key)
    {
        return $query->where('config_key', $key);
    }

    /**
     * STATIC METHODS FOR EASY ACCESS
     * =====================================================================
     * These allow quick access to configuration values
     * Usage: Configuration::get('academic_year')
     */

    /**
     * Get a configuration value by key.
     *
     * Usage: Configuration::getValue('min_weightage')
     * Returns: Value with proper type casting
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $config = static::byKey($key)->active()->first();

        if (!$config) {
            return $default;
        }

        // Type cast based on data_type
        return static::castValue($config->config_value, $config->data_type);
    }

    /**
     * Set a configuration value.
     *
     * Usage: Configuration::setValue('academic_year', '2024/2025', $userId)
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  int     $userId
     * @param  string  $dataType
     * @return \App\Models\Configuration
     */
    public static function setValue($key, $value, $userId, $dataType = 'String')
    {
        return static::updateOrCreate(
            ['config_key' => $key],
            [
                'config_value' => $value,
                'data_type' => $dataType,
                'updated_by' => $userId,
            ]
        );
    }

    /**
     * Cast value to proper type.
     *
     * Internal method to convert values based on data_type
     *
     * @param  mixed   $value
     * @param  string  $dataType
     * @return mixed
     */
    private static function castValue($value, $dataType)
    {
        switch ($dataType) {
            case 'Integer':
                return (int) $value;
            case 'Decimal':
                return (float) $value;
            case 'Boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'Date':
                return \Carbon\Carbon::parse($value);
            default:
                return $value;
        }
    }

    /**
     * Get all configurations as key-value array.
     *
     * Usage: Configuration::allAsArray()
     * Returns: ['academic_year' => '2024/2025', 'min_weightage' => 2.0, ...]
     *
     * @return array
     */
    public static function allAsArray()
    {
        $configs = [];

        foreach (static::active()->get() as $config) {
            $configs[$config->config_key] = static::castValue(
                $config->config_value,
                $config->data_type
            );
        }

        return $configs;
    }
}
