<x-confirm-modal show="$wire.showModal" cancel="$wire.set('showModal', false)" icon="arrow-right-on-rectangle"
    heading="ออกจากห้องเรียน" message="คุณแน่ใจหรือว่าต้องการออกจากห้องเรียนนี้? คุณจะไม่สามารถเข้าถึงงาน ประกาศ และคะแนนในห้องนี้ได้อีก จนกว่าจะเข้าร่วมใหม่">
    <button type="button" wire:click="leave" wire:loading.attr="disabled" wire:target="leave"
        class="flex-1 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700 disabled:opacity-60">
        {{ 'ออกจากห้องเรียน' }}
    </button>
</x-confirm-modal>
