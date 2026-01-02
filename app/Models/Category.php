<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'color_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    /**
     * ✅ Automatically generate a readable color name from the hex code.
     */
    public function getColorNameAttribute()
    {
        $hex = strtoupper($this->color_code);

        if (!$hex) {
            return 'Default (Gray)';
        }

        // Remove "#" if present
        $hex = ltrim($hex, '#');

        // Convert HEX to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Approximate readable color names
        $colors = [
            'Black' => [0, 0, 0],
            'White' => [255, 255, 255],
            'Red' => [255, 0, 0],
            'Lime' => [0, 255, 0],
            'Blue' => [0, 0, 255],
            'Yellow' => [255, 255, 0],
            'Cyan' => [0, 255, 255],
            'Magenta' => [255, 0, 255],
            'Silver' => [192, 192, 192],
            'Gray' => [128, 128, 128],
            'Maroon' => [128, 0, 0],
            'Olive' => [128, 128, 0],
            'Green' => [0, 128, 0],
            'Purple' => [128, 0, 128],
            'Teal' => [0, 128, 128],
            'Navy' => [0, 0, 128],
            'Coral' => [255, 127, 80],
            'Salmon' => [250, 128, 114],
            'Orange' => [255, 165, 0],
            'Gold' => [255, 215, 0],
            'Sky Blue' => [135, 206, 235],
            'Royal Blue' => [65, 105, 225],
            'Forest Green' => [34, 139, 34],
            'Dark Slate' => [47, 79, 79],
            'Indigo' => [75, 0, 130],
            'Violet' => [238, 130, 238],
        ];

        // Find the nearest named color (smallest Euclidean distance)
        $closest = null;
        $closestDistance = PHP_INT_MAX;

        foreach ($colors as $name => [$cr, $cg, $cb]) {
            $distance = sqrt(pow($r - $cr, 2) + pow($g - $cg, 2) + pow($b - $cb, 2));
            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closest = $name;
            }
        }

        return $closest ?? $this->color_code;
    }

}
