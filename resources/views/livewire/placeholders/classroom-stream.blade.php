@section('page-title', (isset($classroom) ? ('กระดานสนทนา' . ' - ' . $classroom->name) : 'กระดานสนทนา'))
<div class="space-y-5">
    @if(isset($classroom))
        @include('livewire.classroom.partials.subnav', ['classroom' => $classroom])
    @endif

    <div class="rounded-[12px] border border-[#dedee5] bg-white p-5 shadow-[rgba(0,0,0,0.03)_0px_4px_24px] space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="skeleton h-3 w-16"></div>
                <div class="skeleton h-6 w-32"></div>
            </div>
            @if(auth()->user()->isTeacher())
                <div class="skeleton h-10 w-32 rounded-[10px]"></div>
            @endif
        </div>

        <div class="mt-5 space-y-3">
            @for($i = 0; $i < 3; $i++)
                <div class="rounded-[12px] border border-[#dedee5] bg-[rgba(37,99,235,0.02)] p-4 space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="skeleton h-10 w-10 shrink-0 rounded-[10px]"></div>
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="skeleton h-4 w-32"></div>
                            <div class="skeleton h-3 w-20"></div>
                        </div>
                    </div>
                    <div class="space-y-2 mt-3">
                        <div class="skeleton h-4 w-full"></div>
                        <div class="skeleton h-4 w-5/6"></div>
                        <div class="skeleton h-4 w-2/3"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
