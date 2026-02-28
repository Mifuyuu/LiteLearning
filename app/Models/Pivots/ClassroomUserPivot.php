<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClassroomUserPivot extends Pivot
{
    public $incrementing = false;

    protected $primaryKey = null;

    protected $table = 'classroom_user';

    protected $fillable = [
        'classroom_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }
}
