<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserStoreItemPivot extends Pivot
{
    public $incrementing = false;

    protected $primaryKey = null;

    protected $table = 'user_store_items';

    protected $fillable = [
        'user_id',
        'store_item_id',
    ];
}
