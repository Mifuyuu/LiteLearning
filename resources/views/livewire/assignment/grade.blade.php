@section('page-title', 'Grade Submission')

<div class="max-w-4xl mx-auto">
    <!-- Back -->
    <a href="{{ route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment]) }}"
       class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i> Back to {{ $assignment->title }}
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Submission Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-5 border-b border-gray-200">
                    <div class="flex items-center">
                        <img src="{{ $submission->user->avatar_url }}" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $submission->user->name }}</p>
                            <p class="text-xs text-gray-500">
                                Turned in {{ $submission->turned_in_at?->format('M j, Y \a\t g:i A') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Student's Response</h3>
                    @if($submission->content)
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $submission->content }}</p>
                    </div>
                    @else
                    <p class="text-sm text-gray-400 italic">No text response provided.</p>
                    @endif

                    @if($submission->attachments->count())
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Attachments</h3>
                        <div class="space-y-2">
                            @foreach($submission->attachments as $attachment)
                            <a href="{{ $attachment->url }}" target="_blank" class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100">
                                <i class="fas {{ $attachment->icon }} text-gray-400 mr-3"></i>
                                <span class="text-sm text-gray-700">{{ $attachment->file_name }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Grading Panel -->
        <div>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden sticky top-24">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Grade</h3>
                </div>

                <div class="p-4 space-y-4">
                    <!-- Score -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Score</label>
                        <div class="flex items-center gap-2">
                            <input wire:model="score" type="number" min="0" max="{{ $assignment->max_score }}"
                                   class="w-24 border border-gray-300 rounded-lg px-3 py-2.5 text-center text-lg font-bold focus:ring-2 focus:ring-indigo-500">
                            <span class="text-gray-500 text-lg">/</span>
                            <span class="text-lg font-bold text-gray-900">{{ $assignment->max_score }}</span>
                        </div>
                        @error('score') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Feedback -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Feedback</label>
                        <textarea wire:model="feedback" rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500"
                                  placeholder="Add feedback for the student..."></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2 pt-2">
                        <button wire:click="grade"
                                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <span wire:loading.remove wire:target="grade">
                                {{ $submission->isGraded() ? 'Update Grade' : 'Submit Grade' }}
                            </span>
                            <span wire:loading wire:target="grade"><i class="fas fa-spinner fa-spin mr-1"></i> Saving...</span>
                        </button>

                        @if($submission->isTurnedIn())
                        <button wire:click="returnSubmission"
                                class="w-full py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            Return to Student
                        </button>
                        @endif
                    </div>

                    @if($submission->isGraded())
                    <div class="mt-2 p-3 bg-green-50 rounded-lg text-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mb-1"></i>
                        <p class="text-sm text-green-700 font-medium">Graded on {{ $submission->graded_at?->format('M j, g:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
