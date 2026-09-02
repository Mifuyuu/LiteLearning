@props(['topics', 'value' => ''])

@php
    $topicNames = collect($topics)->pluck('name')->values();
@endphp

<div
    x-data="{
        open: false,
        query: @js((string) $value),
        options: @js($topicNames),
        get filtered() {
            const q = this.query.trim().toLowerCase();
            return q === '' ? this.options : this.options.filter(o => o.toLowerCase().includes(q));
        },
        select(name) {
            this.$refs.topicInput.value = name;
            this.$refs.topicInput.dispatchEvent(new Event('input', { bubbles: true }));
            this.$refs.topicInput.dispatchEvent(new Event('change', { bubbles: true }));
            this.query = name;
            this.open = false;
        },
    }"
    @click.outside="open = false"
    class="relative"
>
    {{ $slot }}

    <input
        x-ref="topicInput"
        @input="query = $event.target.value; open = true"
        @focus="open = true"
        @keydown.escape="open = false"
        type="text"
        autocomplete="off"
        {{ $attributes }}
    >

    <ul
        x-show="open && filtered.length"
        x-cloak
        class="absolute z-10 mt-1 w-full max-h-56 overflow-y-auto bg-white rounded-lg shadow-lg border border-gray-200 py-1"
    >
        <template x-for="name in filtered" :key="name">
            <li>
                <button type="button" @click="select(name)"
                    class="w-full text-left px-3.5 py-2 text-sm hover:bg-gray-50 transition-colors text-[#484b5e]"
                    x-text="name"></button>
            </li>
        </template>
    </ul>
</div>
