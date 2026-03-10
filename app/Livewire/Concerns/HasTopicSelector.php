<?php

namespace App\Livewire\Concerns;

use App\Models\Topic;
use Illuminate\Database\Eloquent\Collection;

trait HasTopicSelector
{
    public function getTopicsProperty(): Collection
    {
        return Topic::where('classroom_id', $this->classroom->id)->get();
    }

    /**
     * Find or create a topic by name for the given classroom.
     *
     * @return int|null The topic ID, or null if the name was empty.
     */
    protected function resolveOrCreateTopic(string $topicName, int $classroomId): ?int
    {
        $topicName = trim($topicName);

        if ($topicName === '') {
            return null;
        }

        $topic = Topic::firstOrCreate([
            'classroom_id' => $classroomId,
            'name' => $topicName,
        ]);

        return $topic->id;
    }
}
