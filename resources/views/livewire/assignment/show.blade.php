@section('page-title', $assignment->title)
@section('breadcrumb')
    <nav class="flex items-center space-x-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-gray-500 hover:text-indigo-600 transition-colors">{{ __('Classrooms') }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('classroom.show', $classroom) }}" class="text-gray-500 hover:text-indigo-600 transition-colors">{{ $classroom->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-semibold">{{ $assignment->title }}</span>
    </nav>
@endsection

<div>
    <!-- Back -->
    <a href="{{ route('classroom.show', $classroom) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i> {{ __('Back to :name', ['name' => $classroom->name]) }}
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            @if(!$isEditTab)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                                 style="background-color: {{ $classroom->theme_color }}; color: white;">
                                @if($assignment->type === 'quiz')
                                    <i class="fas fa-question-circle text-lg"></i>
                                @elseif($assignment->type === 'material')
                                    <i class="fas fa-book text-lg"></i>
                                @else
                                    <i class="fas fa-file-alt text-lg"></i>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h1 class="text-xl font-bold text-gray-900">{{ $assignment->title }}</h1>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-sm text-gray-500">{{ $assignment->user->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $assignment->created_at->translatedFormat('j M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-medium capitalize
                                {{ $assignment->type === 'quiz' ? 'bg-purple-100 text-purple-700' : ($assignment->type === 'material' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                {{ __(ucfirst($assignment->type)) }}
                            </span>
                            @if($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin())
                            <button wire:click="openEditTab" type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-yellow-500 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                                <i class="fas fa-pen mr-1.5"></i>{{ __('Edit') }}
                            </button>
                            <button wire:click="openDeleteModal" type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-500 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                <i class="fas fa-trash-alt mr-1.5"></i>{{ __('Delete') }}
                            </button>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-4 text-sm">
                        <span class="text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            {{ __('Due') }}: {{ $assignment->due_date ? $assignment->due_date->translatedFormat('j M Y, H:i') : __('No due date') }}
                        </span>
                        @if($assignment->type !== 'material')
                        <span class="text-gray-500">
                            <i class="fas fa-star mr-1"></i> {{ $assignment->max_score }} {{ __('Points') }}
                        </span>
                        @endif
                        @if($assignment->isOverdue())
                        <span class="text-red-500 font-medium text-xs px-2 py-0.5 bg-red-50 rounded-full">
                            {{ __('Overdue') }}
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Description / Instructions -->
                <div class="p-6">
                    @if($assignment->description)
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $assignment->description }}</p>
                    </div>
                    @endif

                    @if($assignment->instructions)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2"><i class="fas fa-list-ul mr-1"></i> {{ __('Instructions') }}</h3>
                        <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $assignment->instructions }}</div>
                    </div>
                    @endif

                    <!-- Attachments -->
                    @if($assignment->attachments->count())
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ __('Attachments') }}</h3>
                        <div class="space-y-2">
                            @foreach($assignment->attachments as $attachment)
                            <a href="{{ $attachment->url }}" target="_blank" class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                                <i class="fas {{ $attachment->icon }} text-gray-400 mr-3"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-700 truncate">{{ $attachment->file_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $attachment->formatted_size }}</p>
                                </div>
                                <i class="fas fa-download text-gray-400 ml-2"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Teacher: Submissions Table -->
            @if($submissions !== null && ($classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin()))
            <div class="mt-6 bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Student Work') }}</h3>
                    <div class="flex gap-4 mt-2 text-sm text-gray-500">
                        <span><i class="fas fa-check-circle text-green-500 mr-1"></i> {{ $assignment->submittedCount() }} {{ __('Turned in') }}</span>
                        <span><i class="fas fa-star text-amber-500 mr-1"></i> {{ $assignment->gradedCount() }} {{ __('Graded') }}</span>
                        @if($assignment->averageScore())
                        <span><i class="fas fa-chart-line text-blue-500 mr-1"></i> {{ __('Average') }}: {{ round($assignment->averageScore()) }}/{{ $assignment->max_score }}</span>
                        @endif
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($submissions as $sub)
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50" wire:key="sub-{{ $sub->id }}">
                        <div class="flex items-center">
                            <img src="{{ $sub->user->avatar_url }}" class="w-9 h-9 rounded-full mr-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $sub->user->name }}</p>
                                <p class="text-xs text-gray-500">
                                    @if($sub->status === 'turned_in')
                                        <span class="text-blue-600">{{ __('Turned in') }} {{ $sub->turned_in_at?->diffForHumans() }}</span>
                                    @elseif($sub->status === 'graded')
                                        <span class="text-green-600">{{ __('Graded') }}: {{ $sub->score }}/{{ $assignment->max_score }}</span>
                                    @elseif($sub->status === 'returned')
                                        <span class="text-purple-600">{{ __('Returned') }}</span>
                                    @else
                                        <span class="text-gray-400">{{ __('Not turned in') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($sub->isTurnedIn())
                        <a href="{{ route('assignment.grade', ['classroom' => $classroom, 'assignment' => $assignment, 'submission' => $sub]) }}"
                           class="px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">
                            {{ $sub->isGraded() ? __('View Grade') : __('Grade') }}
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @else
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-5 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Edit Assignment') }}</h3>
                    <button wire:click="cancelEditTab" type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-1.5"></i>{{ __('Back') }}
                    </button>
                </div>

                <form wire:submit="saveAssignment" class="p-5 space-y-4">
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['assignment' => ['icon' => 'fa-file-alt', 'label' => __('Assignment')], 'quiz' => ['icon' => 'fa-question-circle', 'label' => __('Quiz')], 'material' => ['icon' => 'fa-book', 'label' => __('Material')]] as $t => $info)
                        <label class="cursor-pointer">
                            <input wire:model.live="editType" type="radio" value="{{ $t }}" class="peer sr-only">
                            <div class="flex flex-col items-center p-3 border-2 rounded-xl transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50 border-gray-200 hover:bg-gray-50">
                                <i class="fas {{ $info['icon'] }} text-lg mb-1"></i>
                                <span class="text-sm font-medium">{{ $info['label'] }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title *') }}</label>
                        <input wire:model="editTitle" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('editTitle') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                        <textarea wire:model="editDescription" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        @error('editDescription') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Instructions') }}</label>
                        <textarea wire:model="editInstructions" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        @error('editInstructions') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    @if($editType !== 'material')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Points') }}</label>
                            <input wire:model="editMaxScore" type="number" min="0" max="1000" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('editMaxScore') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Due Date') }}</label>
                            <input wire:model="editDueDate" type="datetime-local" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('editDueDate') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Topic') }}</label>
                        <input wire:model="editTopic" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('editTopic') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                        <select wire:model="editStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="draft">{{ __('Draft') }}</option>
                            <option value="published">{{ __('Published') }}</option>
                        </select>
                        @error('editStatus') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button wire:click="cancelEditTab" type="button" class="px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <span wire:loading.remove wire:target="saveAssignment">{{ __('Update Assignment') }}</span>
                            <span wire:loading wire:target="saveAssignment"><i class="fas fa-spinner fa-spin mr-1"></i> {{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        <!-- Sidebar: Student Submission -->
        @if(!$isEditTab && auth()->user()->isStudent() && $assignment->type !== 'material')
        <div>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden sticky top-0">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Your Work') }}</h3>
                        @if($userSubmission)
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium capitalize
                                {{ $userSubmission->status === 'turned_in' ? 'bg-blue-100 text-blue-700' :
                                   ($userSubmission->status === 'graded' ? 'bg-green-100 text-green-700' :
                                   ($userSubmission->status === 'returned' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700')) }}">
                                {{ str_replace('_', ' ', $userSubmission->status) }}
                            </span>
                        @else
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-gray-100 text-gray-600">{{ __('Assigned') }}</span>
                        @endif
                    </div>

                    @if($userSubmission?->isGraded())
                    <div class="mt-3 p-3 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-700">{{ $userSubmission->score }}<span class="text-sm font-normal text-green-600">/{{ $assignment->max_score }}</span></p>
                        @if($userSubmission->feedback)
                        <p class="text-sm text-green-600 mt-1">{{ $userSubmission->feedback }}</p>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="p-4">
                    @if(!$userSubmission || $userSubmission->status === 'assigned' || $userSubmission->status === 'returned')
                    <textarea wire:model="submissionContent" rows="6"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-3"
                              placeholder="{{ __('Type your answer here...') }}"></textarea>

                    <div class="flex flex-col gap-2">
                        <button wire:click="turnIn"
                                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <span wire:loading.remove wire:target="turnIn">{{ __('Turn In') }}</span>
                            <span wire:loading wire:target="turnIn"><i class="fas fa-spinner fa-spin mr-1"></i> {{ __('Submitting...') }}</span>
                        </button>
                        <button wire:click="saveDraft"
                                class="w-full py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            {{ __('Save Draft') }}
                        </button>
                    </div>
                    @elseif($userSubmission->status === 'turned_in')
                    <div class="text-center">
                        <i class="fas fa-check-circle text-blue-500 text-3xl mb-2"></i>
                        <p class="text-sm text-gray-700 font-medium">{{ __('Turned in') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $userSubmission->turned_in_at?->translatedFormat('j M, H:i') }}</p>
                        <button wire:click="unsubmit"
                                class="mt-3 w-full py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                            {{ __('Unsubmit') }}
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Sidebar info for materials or teacher -->
        @if(!$isEditTab && ($assignment->type === 'material' || ($classroom->isOwnedBy(auth()->user()) && $assignment->type !== 'material')))
        <div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 sticky top-0">
                @if($assignment->type !== 'material')
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Max Score') }}</span>
                        <span class="font-semibold text-gray-900">{{ $assignment->max_score }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Turned in') }}</span>
                        <span class="font-semibold text-gray-900">{{ $assignment->submittedCount() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Graded') }}</span>
                        <span class="font-semibold text-gray-900">{{ $assignment->gradedCount() }}</span>
                    </div>
                    @if($assignment->averageScore())
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">{{ __('Average') }}</span>
                        <span class="font-semibold text-gray-900">{{ round($assignment->averageScore(), 1) }}</span>
                    </div>
                    @endif
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-book text-gray-300 text-3xl mb-2"></i>
                    <p class="text-sm text-gray-500">{{ __('This is a material - no submission required.') }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" wire:click="closeDeleteModal">
        <div class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden" wire:click.stop>
            <div class="px-6 py-5 border-b border-gray-100">
                <h4 class="text-base font-semibold text-gray-900">{{ __('Delete Assignment') }}</h4>
                <p class="text-sm text-gray-500 mt-1">{{ __('Are you sure you want to delete this assignment?') }}</p>
            </div>

            <div class="px-6 py-5">
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="closeDeleteModal" class="inline-flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-xmark mr-1.5"></i>{{ __('Cancel') }}
                    </button>
                    <button type="button" wire:click="deleteAssignment" wire:loading.attr="disabled" class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors">
                        <span wire:loading.remove wire:target="deleteAssignment" class="inline-flex items-center">
                            <i class="fas fa-trash-alt mr-1.5"></i>{{ __('Delete') }}
                        </span>
                        <span wire:loading wire:target="deleteAssignment" class="inline-flex items-center">
                            <i class="fas fa-spinner fa-spin mr-1.5"></i>{{ __('Deleting...') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
