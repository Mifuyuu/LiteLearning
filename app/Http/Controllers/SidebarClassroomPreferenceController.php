<?php

namespace App\Http\Controllers;

use App\Models\ClassroomSidebarPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SidebarClassroomPreferenceController extends Controller
{
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'orderedIds' => ['required', 'array'],
            'orderedIds.*' => ['integer'],
        ]);

        $userId = Auth::id();
        $orderedIds = collect($validated['orderedIds'])->unique()->values();

        $validIds = ClassroomSidebarPreference::query()
            ->where('user_id', $userId)
            ->where('is_pinned', true)
            ->whereIn('classroom_id', $orderedIds)
            ->pluck('classroom_id')
            ->all();

        $validSet = array_flip($validIds);

        $position = 1;
        foreach ($orderedIds as $classroomId) {
            if (! isset($validSet[$classroomId])) {
                continue;
            }

            ClassroomSidebarPreference::query()
                ->where('user_id', $userId)
                ->where('classroom_id', $classroomId)
                ->update(['position' => $position]);

            $position++;
        }

        return response()->json(['ok' => true]);
    }
}
