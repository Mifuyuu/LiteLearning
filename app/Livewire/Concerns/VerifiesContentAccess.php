<?php

namespace App\Livewire\Concerns;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\ClassworkItem;
use App\Models\Material;
use Illuminate\Database\Eloquent\Model;

trait VerifiesContentAccess
{
    /**
     * Verify that a content model belongs to the given classroom and
     * that the authenticated user has access to the classroom.
     *
     * @param  string  $fkColumn  The foreign-key column on $content that references classrooms.id
     */
    protected function verifyContentAccess(Classroom $classroom, Model $content, string $fkColumn = 'classroom_id'): void
    {
        // For CTI models (Assignment, Material), classroom_id lives on classwork_items
        if ($content instanceof Assignment || $content instanceof Material) {
            abort_unless(
                ClassworkItem::where('id', $content->classwork_item_id)
                    ->where('classroom_id', $classroom->id)
                    ->exists(),
                404
            );
        } else {
            abort_unless($content->{$fkColumn} === $classroom->id, 404);
        }

        abort_unless($classroom->hasAccess(auth()->user()), 403);
    }
}
