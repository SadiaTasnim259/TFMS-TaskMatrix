<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerformanceScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'academic_year',
        'semester',
        'teaching_score',
        'research_score',
        'admin_score',
        'student_support_score',
        'overall_score',
        'teaching_weight',
        'research_weight',
        'admin_weight',
        'support_weight',
        'rating',
        'comments',
        'evaluated_by',
    ];

    protected $casts = [
        'teaching_score' => 'decimal:2',
        'research_score' => 'decimal:2',
        'admin_score' => 'decimal:2',
        'student_support_score' => 'decimal:2',
        'overall_score' => 'decimal:2',
        'teaching_weight' => 'decimal:2',
        'research_weight' => 'decimal:2',
        'admin_weight' => 'decimal:2',
        'support_weight' => 'decimal:2',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the staff member this score is for
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get who evaluated this
     */
    public function evaluatedBy()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    // ========== SCOPES ==========

    /**
     * Filter by year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Filter by rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Get high performers (score > 80)
     */
    public function scopeHighPerformers($query)
    {
        return $query->where('overall_score', '>', 80);
    }

    /**
     * Get low performers (score < 50)
     */
    public function scopeLowPerformers($query)
    {
        return $query->where('overall_score', '<', 50);
    }

    /**
     * Filter by department through staff
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->whereHas('staff', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        });
    }

    // ========== METHODS ==========

    /**
     * Calculate overall score from components
     */
    public function calculateOverallScore()
    {
        $total = 0;
        $total += $this->teaching_score * ($this->teaching_weight / 100);
        $total += $this->research_score * ($this->research_weight / 100);
        $total += $this->admin_score * ($this->admin_weight / 100);
        $total += $this->student_support_score * ($this->support_weight / 100);

        $this->overall_score = round($total, 2);

        // Set rating based on score
        if ($this->overall_score >= 85) {
            $this->rating = 'excellent';
        } elseif ($this->overall_score >= 70) {
            $this->rating = 'good';
        } elseif ($this->overall_score >= 55) {
            $this->rating = 'satisfactory';
        } else {
            $this->rating = 'needs_improvement';
        }

        return $this;
    }

    /**
     * Get rating badge color
     */
    public function getRatingColor()
    {
        $colors = [
            'excellent' => 'success',
            'good' => 'info',
            'satisfactory' => 'warning',
            'needs_improvement' => 'danger',
            'unrated' => 'secondary',
        ];

        return $colors[$this->rating] ?? 'secondary';
    }

    /**
     * Get rating display text
     */
    public function getRatingDisplay()
    {
        return ucwords(str_replace('_', ' ', $this->rating));
    }
}
