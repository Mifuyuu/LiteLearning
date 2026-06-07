@props(['user', 'size' => 'w-10 h-10', 'border' => 'border-4 border-white', 'shadow' => 'shadow-sm'])

<div {{ $attributes->merge(['class' => 'relative inline-block']) }}>
    <div class="{{ $size }} rounded-full {{ $border }} {{ $shadow }} bg-white overflow-hidden relative z-10">
        <img src="{{ $user->avatar_url }}" 
             alt="{{ $user->name }}"
             class="w-full h-full object-cover"
             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=7F9CF5&background=EBF4FF';">
        {{ $slot }}
    </div>
    
    @if($user->active_avatar_frame && !str_starts_with($user->active_avatar_frame, 'border'))
        <div class="absolute inset-0 z-20 pointer-events-none flex items-center justify-center">
            <img src="{{ asset($user->active_avatar_frame) }}"
                class="w-full h-full max-w-none drop-shadow-sm scale-[2.0]">
        </div>
    @elseif($user->active_avatar_frame)
        <div class="absolute inset-0 rounded-full {{ $user->active_avatar_frame }} pointer-events-none z-20"></div>
    @endif
</div>
