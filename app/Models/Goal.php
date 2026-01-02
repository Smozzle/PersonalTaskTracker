<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'progress',
        'target_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all milestones for the goal
     */
    public function milestones()
    {
        return $this->hasMany(Milestone::class)->orderBy('order');
    }

    /**
     * Calculate progress based on completed milestones
     */
    public function updateProgressFromMilestones()
    {
        $completedMilestones = $this->milestones()->where('is_completed', true)->get();

        if ($this->milestones()->count() === 0) {
            // No milestones, keep manual progress
            return;
        }

        if ($completedMilestones->isEmpty()) {
            $this->progress = 0;
        } else {
            $totalPercentage = $this->milestones()->sum('target_percentage');
            $completedPercentage = $completedMilestones->sum('target_percentage');

            if ($totalPercentage > 0) {
                $this->progress = round(($completedPercentage / $totalPercentage) * 100);
            }
        }

        // Auto-complete goal when reaching 100%
        if ($this->progress >= 100) {
            $this->status = 'Completed';
        } elseif ($this->status === 'Completed' && $this->progress < 100) {
            $this->status = 'Ongoing';
        }

        $this->save();
    }
}