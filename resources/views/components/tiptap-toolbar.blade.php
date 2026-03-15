<div class="flex flex-wrap gap-1 p-2 border border-gray-200 border-b-0 rounded-t-lg bg-gray-50">

    <div class="flex gap-0.5">
        <button type="button" @click="toggleBold()"
            :class="isActive('bold') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Bold">
            <i class="fas fa-bold text-xs"></i>
        </button>
        <button type="button" @click="toggleItalic()"
            :class="isActive('italic') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Italic">
            <i class="fas fa-italic text-xs"></i>
        </button>
        <button type="button" @click="toggleUnderline()"
            :class="isActive('underline') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Underline">
            <i class="fas fa-underline text-xs"></i>
        </button>
        <button type="button" @click="toggleStrike()"
            :class="isActive('strike') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Strikethrough">
            <i class="fas fa-strikethrough text-xs"></i>
        </button>
    </div>

    <div class="w-px bg-gray-300 self-stretch mx-0.5"></div>

    <div class="flex gap-0.5">
        <button type="button" @click="setAlign('left')"
            :class="isActive({ textAlign: 'left' }) ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Align Left">
            <i class="fas fa-align-left text-xs"></i>
        </button>
        <button type="button" @click="setAlign('center')"
            :class="isActive({ textAlign: 'center' }) ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Align Center">
            <i class="fas fa-align-center text-xs"></i>
        </button>
        <button type="button" @click="setAlign('right')"
            :class="isActive({ textAlign: 'right' }) ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Align Right">
            <i class="fas fa-align-right text-xs"></i>
        </button>
    </div>

    <div class="w-px bg-gray-300 self-stretch mx-0.5"></div>

    <div class="flex gap-0.5">
        <button type="button" @click="toggleOrdered()"
            :class="isActive('orderedList') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Ordered List">
            <i class="fas fa-list-ol text-xs"></i>
        </button>
        <button type="button" @click="toggleBullet()"
            :class="isActive('bulletList') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Bullet List">
            <i class="fas fa-list-ul text-xs"></i>
        </button>
    </div>

    <div class="w-px bg-gray-300 self-stretch mx-0.5"></div>

    <div class="flex gap-0.5">
        <button type="button" @click="openLinkModal()"
            :class="isActive('link') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="w-7 h-7 rounded flex items-center justify-center transition-colors" title="Link">
            <i class="fas fa-link text-xs"></i>
        </button>
        <button type="button" @click="clearFormat()"
            class="w-7 h-7 rounded flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors" title="Clear Formatting">
            <i class="fas fa-remove-format text-xs"></i>
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
                        <i class="fas fa-link text-indigo-500"></i> {{ __('Insert Link') }}
                    </h3>
                    <button type="button" @click="showLinkModal = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- URL Input --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">URL</label>
                    <input x-ref="linkInput" x-model="linkUrl" type="url"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="https://example.com"
                        @keydown.enter.prevent="saveLink()">
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-2.5">
                    <button type="button" @click="saveLink()"
                        class="btn-3d btn-3d--indigo w-full py-2.5 font-bold rounded-lg text-sm transition-all">
                        <i class="fas fa-check mr-1.5"></i> {{ __('Save') }}
                    </button>
                    <div class="flex gap-2.5">
                        <button type="button" @click="removeLink()" x-show="isActive('link')"
                            class="flex-1 py-2.5 text-sm font-medium text-red-600 border border-red-200 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="fas fa-unlink mr-1"></i> {{ __('Remove') }}
                        </button>
                        <button type="button" @click="showLinkModal = false"
                            class="flex-1 py-2.5 text-sm font-medium text-gray-600 border border-gray-300 hover:bg-gray-100 rounded-lg transition-colors">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
