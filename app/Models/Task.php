<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_name',
        'color',
        'scan_models',
        '3d_models',
        '3d_models_full',
        'total_price',
    ];

    protected $casts = [
        '3d_models_full' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function set3dModelsAttribute($value): void
    {
        $this->attributes['3d_models'] = $value;
        if ($value > 0) {
            $this->attributes['scan_models'] = 0;
        }
    }

    public function setScanModelsAttribute($value): void
    {
        $this->attributes['scan_models'] = $value;
        if ($value > 0) {
            $this->attributes['3d_models'] = 0;
            $this->attributes['3d_models_full'] = 0;
        }
    }
}
