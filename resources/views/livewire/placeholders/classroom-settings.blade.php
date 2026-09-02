@section('page-title', (isset($classroom) ? ('ตั้งค่า' . ' - ' . $classroom->name) : 'ตั้งค่า'))
@if(isset($classroom))
@section('breadcrumb')
    <nav class="flex items-center gap-1 text-sm">
        <a href="{{ route('classrooms') }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)">
            ...
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <a href="{{ route('classroom.show', $classroom) }}" class="text-[#686b82] transition-colors hover:text-(--ll-blue)" title="{{ $classroom->name }}">
            {{ \Illuminate\Support\Str::limit($classroom->name, 10, '..') }}
        </a>
        <x-icon name="chevron-right" class="h-3 w-3 text-[#9497a9]" />
        <span class="font-semibold text-[#101114]">{{ 'ตั้งค่า' }}</span>
    </nav>
@endsection
@endif

<div class="space-y-5 max-w-4xl mx-auto">
    <section class="rounded-xl border-3 border-[#dedee5] bg-white shadow-[rgba(0,0,0,0.03)_0px_4px_24px] min-h-[calc(100vh-3rem)] flex flex-col">
        <div class="border-b border-[#dedee5] p-5">
            <div class="skeleton h-7 w-56"></div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#dedee5] p-5">
            <div class="space-y-2">
                <div class="skeleton h-3 w-32"></div>
                <div class="skeleton h-7 w-28"></div>
            </div>
            <div class="skeleton h-11 w-24 rounded-[10px]"></div>
        </div>

        <div class="flex-1 flex flex-col space-y-5 p-5">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <div class="skeleton h-4 w-10"></div>
                    <div class="skeleton h-12 w-full rounded-[10px]"></div>
                </div>
                <div class="space-y-2">
                    <div class="skeleton h-4 w-10"></div>
                    <div class="skeleton h-12 w-full rounded-[10px]"></div>
                </div>
            </div>

            <div class="space-y-2">
                <div class="skeleton h-4 w-20"></div>
                <div class="skeleton h-24 w-full rounded-[10px]"></div>
            </div>

            <div>
                <div class="skeleton mb-3 h-4 w-10"></div>
                <div class="grid grid-cols-4 gap-3 sm:grid-cols-7">
                    @for($i = 0; $i < 14; $i++)
                        <div class="skeleton aspect-square rounded-[10px]"></div>
                    @endfor
                </div>
            </div>

            <div class="mt-auto flex flex-wrap justify-end gap-2 border-t border-[#dedee5] pt-5">
                <div class="skeleton h-11 w-28 rounded-[10px]"></div>
                <div class="skeleton h-11 w-24 rounded-[10px]"></div>
            </div>
        </div>
    </section>
</div>
