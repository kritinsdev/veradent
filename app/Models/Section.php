<?php

namespace App\Models;

use App\Enums\Color;
use App\Enums\Material;
use App\Enums\TeethPosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'type',
        'teeth_position',
        'color',
        'material',
        'price',
    ];

    protected $casts = [
        'teeth_position' => TeethPosition::class,
        'material' => Material::class,
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
