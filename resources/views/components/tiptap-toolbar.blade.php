<div class="flex flex-wrap gap-1 p-2 border border-gray-200 border-b-0 rounded-t-lg bg-gray-50">

    <div class="flex gap-0.5">
        <button type="button" @click="toggleBold()"
            :class="isActive('bold') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Bold">
            <x-icon name="bold" class="h-4 w-4" />
        </button>
        <button type="button" @click="toggleItalic()"
            :class="isActive('italic') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Italic">
            <x-icon name="italic" class="h-4 w-4" />
        </button>
        <button type="button" @click="toggleUnderline()"
            :class="isActive('underline') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Underline">
            <x-icon name="underline" class="h-4 w-4" />
        </button>
        <button type="button" @click="toggleStrike()"
            :class="isActive('strike') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Strikethrough">
            <x-icon name="strikethrough" class="h-4 w-4" />
        </button>
    </div>

    <div class="w-px bg-gray-300 self-stretch mx-0.5"></div>

    <div class="flex gap-0.5">
        <button type="button" @click="setAlign('left')"
            :class="isActive({ textAlign: 'left' }) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Align Left">
            <x-icon name="align-left" class="h-4 w-4" />
        </button>
        <button type="button" @click="setAlign('center')"
            :class="isActive({ textAlign: 'center' }) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Align Center">
            <x-icon name="align-center" class="h-4 w-4" />
        </button>
        <button type="button" @click="setAlign('right')"
            :class="isActive({ textAlign: 'right' }) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Align Right">
            <x-icon name="align-right" class="h-4 w-4" />
        </button>
    </div>

    <div class="w-px bg-gray-300 self-stretch mx-0.5"></div>

    <div class="flex gap-0.5">
        <button type="button" @click="toggleOrdered()"
            :class="isActive('orderedList') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Ordered List">
            <x-icon name="list-ordered" class="h-4 w-4" />
        </button>
        <button type="button" @click="toggleBullet()"
            :class="isActive('bulletList') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Bullet List">
            <x-icon name="list-bullet" class="h-4 w-4" />
        </button>
    </div>

    <div class="w-px bg-gray-300 self-stretch mx-0.5"></div>

    <div class="flex gap-0.5">
        <button type="button" @click="openLinkModal()"
            :class="isActive('link') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Link">
            <x-icon name="link" class="h-4 w-4" />
        </button>
        <button type="button" @click="clearFormat()"
            class="w-7 h-7 rounded flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors" title="Clear Formatting">
            <x-icon name="eraser" class="h-4 w-4" />
        </button>
    </div>
</div>

{{-- Link Modal --}}
<template x-teleport="body">
    <div x-show="showLinkModal" class="fixed inset-0 z-70 flex items-center justify-center p-4 bg-black/60" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @keydown.escape.window="showLinkModal = false" @click="showLinkModal = false">

        <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform"
            @click.stop
            x-show="showLinkModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <div class="p-6">
                {{-- Header --}}
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <x-icon name="link" class="h-5 w-5 text-blue-500" /> {{ 'แทรกลิงก์' }}
                    </h3>
                    <button type="button" @click="showLinkModal = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <x-icon name="x-mark" class="h-5 w-5" />
                    </button>
                </div>

                {{-- URL Input --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">URL</label>
                    <input x-ref="linkInput" x-model="linkUrl" type="url"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="https://example.com"
                        @keydown.enter.prevent="saveLink()">
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-2.5">
                    <button type="button" @click="saveLink()"
                        class="btn-3d btn-3d--blue w-full py-2.5 font-bold rounded-lg text-sm transition-all">
                        <x-icon name="check" class="h-4 w-4 mr-1.5" /> {{ 'บันทึก' }}
                    </button>
                    <div class="flex gap-2.5">
                        <button type="button" @click="removeLink()" x-show="isActive('link')"
                            class="flex-1 py-2.5 text-sm font-medium text-red-600 border border-red-200 hover:bg-red-50 rounded-lg transition-colors">
                            <x-icon name="link-slash" class="h-4 w-4 mr-1" /> {{ 'ลบ' }}
                        </button>
                        <button type="button" @click="showLinkModal = false"
                            class="flex-1 py-2.5 text-sm font-medium text-gray-600 border border-gray-300 hover:bg-gray-100 rounded-lg transition-colors">
                            {{ 'ยกเลิก' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
