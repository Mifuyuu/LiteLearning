<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassroomSidebarPreference extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $primaryKey = 'user_id'; // Eloquent ต้องการค่านี้ แต่ save/update ถูก override ด้านล่าง

    protected $fillable = [
        'user_id',
        'classroom_id',
        'is_pinned',
        'position',
        'pinned_at',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'position' => 'integer',
            'pinned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Override save to use composite PK (user_id + classroom_id).
     * Eloquent ไม่รองรับ composite PK โดยตรง จึงต้อง upsert ด้วยตัวเอง.
     */
    public function save(array $options = []): bool
    {
        if (!$this->exists) {
            // INSERT new record
            $this->exists = (bool) static::query()->insert($this->getAttributes());
            $this->syncOriginal();
            return $this->exists;
        }

        // UPDATE using composite PK
        $dirty = $this->getDirty();
        if (empty($dirty)) {
            return true;
        }

        static::query()
            ->where('user_id', $this->user_id)
            ->where('classroom_id', $this->classroom_id)
            ->update($dirty);

        $this->syncOriginal();
        return true;
        $this->syncOriginal();
        return true;
    }
}
