<?php

namespace App\Livewire\Concerns;

use App\Models\Classroom;
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
        abort_unless($content->{$fkColumn} === $classroom->id, 404);
        abort_unless($classroom->hasAccess(auth()->user()), 403);
    }
}
