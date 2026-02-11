@section('page-title', 'Create Assignment')

<div class="max-w-3xl mx-auto">
    <!-- Back -->
    <a href="{{ route('classroom.show', $classroom) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i> Back to {{ $classroom->name }}
    </a>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Create Assignment</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $classroom->name }}</p>
        </div>

        <form wire:submit="save" class="p-6 space-y-5">
            <!-- Type selector -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['assignment' => ['icon' => 'fa-file-alt', 'label' => 'Assignment'], 'quiz' => ['icon' => 'fa-question-circle', 'label' => 'Quiz'], 'material' => ['icon' => 'fa-book', 'label' => 'Material']] as $t => $info)
                    <label class="cursor-pointer">
                        <input wire:model.live="type" type="radio" value="{{ $t }}" class="peer sr-only">
                        <div class="flex flex-col items-center p-4 border-2 rounded-xl transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50 border-gray-200 hover:bg-gray-50">
                            <i class="fas {{ $info['icon'] }} text-xl mb-2"></i>
                            <span class="text-sm font-medium">{{ $info['label'] }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input wire:model="title" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Assignment title">
                @error('title') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea wire:model="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Add description..."></textarea>
            </div>

            <!-- Instructions -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Instructions</label>
                <textarea wire:model="instructions" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Add instructions for students..."></textarea>
            </div>

            @if($type !== 'material')
            <div class="grid grid-cols-2 gap-4">
                <!-- Points -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                    <input wire:model="max_score" type="number" min="0" max="1000" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Due date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input wire:model="due_date" type="datetime-local" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            @endif

            <!-- Topic -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Topic</label>
                <input wire:model="topic" type="text" list="topics-list" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Select or create a topic">
                <datalist id="topics-list">
                    @foreach($topics as $t)
                    <option value="{{ $t->name }}">
                    @endforeach
                </datalist>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex gap-2">
                    <button type="button" wire:click="$set('status', 'draft')" wire:click="save"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Save as Draft
                    </button>
                </div>
                <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <span wire:loading.remove wire:target="save">Assign</span>
                    <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin mr-1"></i> Assigning...</span>
                </button>
            </div>
        </form>
    </div>
</div>
