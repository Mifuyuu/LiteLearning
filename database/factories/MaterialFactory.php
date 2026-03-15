<?php

namespace Database\Factories;

use App\Models\ClassworkItem;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition(): array
    {
        return [
            'classwork_item_id' => ClassworkItem::factory()->forMaterial(),
        ];
    }
}
