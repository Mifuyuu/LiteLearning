<div class="flex flex-wrap gap-1 p-2 border border-gray-200 border-b-0 rounded-t-lg bg-gray-50">

    <div class="flex gap-0.5">
        <button type="button" @click="setParagraph()"
            :class="isActive('paragraph') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="px-2 py-1 rounded text-xs font-medium transition-colors" title="Normal">
            ¶
        </button>
        <button type="button" @click="toggleHeading(1)"
            :class="isActive('heading', { level: 1 }) ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="px-2 py-1 rounded text-xs font-bold transition-colors" title="Heading 1">
            H1
        </button>
        <button type="button" @click="toggleHeading(2)"
            :class="isActive('heading', { level: 2 }) ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="px-2 py-1 rounded text-xs font-bold transition-colors" title="Heading 2">
            H2
        </button>
        <button type="button" @click="toggleHeading(3)"
            :class="isActive('heading', { level: 3 }) ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200'"
            class="px-2 py-1 rounded text-xs font-bold transition-colors" title="Heading 3">
            H3
        </button>
    </div>

    <div class="w-px bg-gray-300 self-stretch mx-0.5"></div>

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
        <button type="button" @click="setLink()"
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
