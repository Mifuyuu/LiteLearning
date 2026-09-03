@props(['topics', 'value' => ''])

@php
    $topicOptions = collect($topics)->map(fn ($t) => ['id' => $t->id, 'name' => $t->name])->values();
@endphp

<div
    x-data="{
        open: false,
        query: @js((string) $value),
        options: @js($topicOptions),
        showDeleteModal: false,
        deleteId: null,
        deleteName: '',
        get filtered() {
            const q = this.query.trim().toLowerCase();
            return q === '' ? this.options : this.options.filter(o => o.name.toLowerCase().includes(q));
        },
        select(name) {
            this.$refs.topicInput.value = name;
            this.$refs.topicInput.dispatchEvent(new Event('input', { bubbles: true }));
            this.$refs.topicInput.dispatchEvent(new Event('change', { bubbles: true }));
            this.query = name;
            this.open = false;
        },
        confirmDelete(topic) {
            this.deleteId = topic.id;
            this.deleteName = topic.name;
            this.showDeleteModal = true;
        },
    }"
    @click.outside="open = false"
    @topic-deleted.window="options = options.filter(o => o.id !== $event.detail.id)"
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
        maxlength="50"
        {{ $attributes }}
    >

    <ul
        x-show="open && filtered.length"
        x-cloak
        class="absolute z-10 mt-1 w-full max-h-56 overflow-y-auto no-scrollbar bg-white rounded-lg shadow-lg border border-gray-200 py-1"
    >
        <template x-for="topic in filtered" :key="topic.id">
            <li class="flex items-center hover:bg-gray-50 transition-colors">
                <button type="button" @click="select(topic.name)"
                    class="flex-1 min-w-0 truncate text-left px-3.5 py-2 text-sm text-[#484b5e]"
                    x-text="topic.name"></button>
                <button type="button"
                    @click.stop="confirmDelete(topic)"
                    class="shrink-0 px-2.5 py-2 text-gray-400 hover:text-rose-500 transition"
                    title="ลบหัวข้อ">
                    <x-icon name="x-mark" class="h-3.5 w-3.5" />
                </button>
            </li>
        </template>
    </ul>

    <template x-teleport="body">
        <x-confirm-modal show="showDeleteModal" cancel="showDeleteModal = false" heading="ยืนยันการลบหัวข้อ">
            <x-slot:message>
                คุณแน่ใจหรือไม่ว่าต้องการลบหัวข้อ <span class="font-semibold text-[#101114]" x-text="deleteName"></span>?
            </x-slot:message>
            <button type="button" @click="$wire.deleteTopic(deleteId); showDeleteModal = false"
                class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700">
                ลบ
            </button>
        </x-confirm-modal>
    </template>
</div>
