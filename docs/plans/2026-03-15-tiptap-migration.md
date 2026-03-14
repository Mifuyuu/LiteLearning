# Tiptap Migration Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Replace Quill.js with Tiptap across all 4 editor inputs and 2 display locations in LiteLearning.

**Architecture:** Install Tiptap via npm, write a single `tiptapEditor` Alpine.js component (mirrors existing `quillEditor` pattern), create a reusable Blade toolbar component, swap all 4 `quillEditor` usages, update 2 display locations, remove Quill CDN and CSS.

**Tech Stack:** Tiptap v2 (vanilla), Alpine.js (Livewire-bundled), Tailwind v4, Laravel Blade components

---

## Task 1: Install Tiptap packages & remove Quill

**Files:**
- Modify: `package.json`

**Step 1: Install Tiptap packages**

```bash
npm install @tiptap/core @tiptap/starter-kit @tiptap/extension-underline @tiptap/extension-text-align @tiptap/extension-link @tiptap/extension-placeholder
```

Expected: packages added to `node_modules/` and `package.json` updated.

**Step 2: Remove Quill npm package**

```bash
npm uninstall quill
```

Expected: `quill` removed from `package.json` dependencies.

**Step 3: Verify**

```bash
cat package.json | grep -E "(tiptap|quill)"
```

Expected: 6 `@tiptap/*` entries, zero `quill` entries.

---

## Task 2: Replace `quillEditor` Alpine component with `tiptapEditor`

**Files:**
- Modify: `resources/js/app.js` lines 9–59

**Step 1: Replace the Alpine component**

Remove the entire `quillEditor` block (lines 9–59) and replace with:

```js
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import { TextAlign } from '@tiptap/extension-text-align';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';

document.addEventListener('alpine:init', () => {
    Alpine.data('tiptapEditor', ({ wireModel, placeholder = '' }) => ({
        _editor: null,

        init() {
            const self = this;
            self._editor = new Editor({
                element: self.$refs.editorEl,
                extensions: [
                    StarterKit,
                    Underline,
                    TextAlign.configure({ types: ['heading', 'paragraph'] }),
                    Link.configure({ openOnClick: false }),
                    Placeholder.configure({ placeholder }),
                ],
                content: self.$wire.get(wireModel) || '',
                onBlur() { self.flush(); },
            });

            // Flush before Livewire form submit
            const form = self.$el.closest('form');
            if (form) {
                form.addEventListener('submit', () => self.flush(), { capture: true });
            }

            // Re-sync content when Livewire updates (e.g. modal open)
            self.$watch('$wire.' + wireModel, (val) => {
                if (self._editor && val !== self._editor.getHTML()) {
                    self._editor.commands.setContent(val || '', false);
                }
            });
        },

        flush() {
            if (!this._editor) return;
            const html = this._editor.getHTML();
            const clean = html === '<p></p>' ? '' : html;
            this.$wire.set(wireModel, clean);
        },

        isActive(type, opts = {}) {
            return this._editor?.isActive(type, opts) ?? false;
        },

        toggleBold()       { this._editor?.chain().focus().toggleBold().run(); },
        toggleItalic()     { this._editor?.chain().focus().toggleItalic().run(); },
        toggleUnderline()  { this._editor?.chain().focus().toggleUnderline().run(); },
        toggleStrike()     { this._editor?.chain().focus().toggleStrike().run(); },
        toggleHeading(lvl) { this._editor?.chain().focus().toggleHeading({ level: lvl }).run(); },
        setParagraph()     { this._editor?.chain().focus().setParagraph().run(); },
        setAlign(val)      { this._editor?.chain().focus().setTextAlign(val).run(); },
        toggleOrdered()    { this._editor?.chain().focus().toggleOrderedList().run(); },
        toggleBullet()     { this._editor?.chain().focus().toggleBulletList().run(); },
        clearFormat()      { this._editor?.chain().focus().unsetAllMarks().clearNodes().run(); },

        setLink() {
            const url = window.prompt('URL');
            if (url === null) return;
            if (url === '') {
                this._editor?.chain().focus().extendMarkRange('link').unsetLink().run();
            } else {
                this._editor?.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
            }
        },

        destroy() {
            this._editor?.destroy();
            this._editor = null;
        },
    }));
});
```

The imports go at the **top** of the file (after existing imports on lines 1–5). The `alpine:init` listener replaces the existing one (lines 9–60).

**Step 2: Build to verify no import errors**

```bash
npm run build
```

Expected: build succeeds with no errors.

---

## Task 3: Create the Tiptap toolbar Blade component

**Files:**
- Create: `resources/views/components/tiptap-toolbar.blade.php`

Create the file with this content:

```blade
{{-- Tiptap Toolbar — used inside x-data="tiptapEditor(...)" scope --}}
<div class="flex flex-wrap gap-1 p-2 border border-gray-200 border-b-0 rounded-t-lg bg-gray-50">

    {{-- Heading / Paragraph --}}
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

    {{-- Inline formatting --}}
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

    {{-- Alignment --}}
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

    {{-- Lists --}}
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

    {{-- Link & Clear --}}
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
```

---

## Task 4: Add Tiptap editor CSS, remove Quill CSS

**Files:**
- Modify: `resources/css/app.css` (lines 241–250 — Quill overrides)
- Modify: `resources/views/layouts/app.blade.php` (lines 58–60 — Quill CDN)

**Step 1: Remove Quill CSS overrides from `app.css`**

Delete lines 241–250:
```css
.ql-toolbar.ql-snow {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 8px;
}

.ql-toolbar.ql-snow .ql-formats {
    display: contents;
}
```

Replace with Tiptap editor styles:

```css
/* Tiptap editor */
.tiptap {
    outline: none;
}

.tiptap p.is-editor-empty:first-child::before {
    color: #9ca3af;
    content: attr(data-placeholder);
    float: left;
    height: 0;
    pointer-events: none;
}
```

**Step 2: Remove Quill CDN from `app.blade.php`**

Delete lines 58–60:
```html
<!-- Quill CDN -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
```

---

## Task 5: Update `assignment/create.blade.php` editor

**Files:**
- Modify: `resources/views/livewire/assignment/create.blade.php` lines 77–80

**Step 1: Replace Quill wrapper with Tiptap**

Old (lines 77–80):
```html
<div wire:ignore
    x-data="quillEditor({ wireModel: 'description', placeholder: '{{ __('Add a description or instructions for this assignment...') }}' })">
    <div x-ref="editorEl" class="min-h-[150px]"></div>
</div>
```

New:
```html
<div wire:ignore
    x-data="tiptapEditor({ wireModel: 'description', placeholder: '{{ __('Add a description or instructions for this assignment...') }}' })">
    <x-tiptap-toolbar />
    <div x-ref="editorEl"
        class="min-h-[150px] border border-gray-200 rounded-b-lg p-3 focus:outline-none prose prose-sm max-w-none">
    </div>
</div>
```

---

## Task 6: Update `assignment/show.blade.php` editor + display

**Files:**
- Modify: `resources/views/livewire/assignment/show.blade.php`
  - Editor: lines 309–312
  - Display: lines 122–128

**Step 1: Replace editor (lines 309–312)**

Old:
```html
<div wire:ignore
    x-data="quillEditor({ wireModel: 'editDescription', placeholder: '{{ __('Add a description or instructions for this assignment...') }}' })">
    <div x-ref="editorEl" class="min-h-[150px]"></div>
</div>
```

New:
```html
<div wire:ignore
    x-data="tiptapEditor({ wireModel: 'editDescription', placeholder: '{{ __('Add a description or instructions for this assignment...') }}' })">
    <x-tiptap-toolbar />
    <div x-ref="editorEl"
        class="min-h-[150px] border border-gray-200 rounded-b-lg p-3 focus:outline-none prose prose-sm max-w-none">
    </div>
</div>
```

**Step 2: Update display (line 125)**

Old:
```html
<div class="prose prose-sm max-w-none text-gray-700 [&_p]:my-0 [&_p]:leading-relaxed">
```

New (already correct — no ql-* wrappers needed):
```html
<div class="prose prose-sm max-w-none text-gray-700 [&_p]:my-0 [&_p]:leading-relaxed">
```

(No change needed here — display is already clean.)

---

## Task 7: Update `material/create.blade.php` editor

**Files:**
- Modify: `resources/views/livewire/material/create.blade.php` line 36–38

**Step 1: Replace Quill wrapper with Tiptap**

Old (lines 36–38):
```html
<div wire:ignore x-data="quillEditor({ wireModel: 'description', placeholder: '{{ __('Add a description...') }}' })">
    <div x-ref="editorEl" class="min-h-[150px]"></div>
</div>
```

New:
```html
<div wire:ignore x-data="tiptapEditor({ wireModel: 'description', placeholder: '{{ __('Add a description...') }}' })">
    <x-tiptap-toolbar />
    <div x-ref="editorEl"
        class="min-h-[150px] border border-gray-200 rounded-b-lg p-3 focus:outline-none prose prose-sm max-w-none">
    </div>
</div>
```

---

## Task 8: Update `material/show.blade.php` editor

**Files:**
- Modify: `resources/views/livewire/material/show.blade.php` lines 120–122

**Step 1: Replace Quill wrapper with Tiptap**

Old (lines 120–122):
```html
<div wire:ignore x-data="quillEditor({ wireModel: 'editDescription', placeholder: '{{ __('Add a description...') }}' })">
    <div x-ref="editorEl" class="min-h-[150px]"></div>
</div>
```

New:
```html
<div wire:ignore x-data="tiptapEditor({ wireModel: 'editDescription', placeholder: '{{ __('Add a description...') }}' })">
    <x-tiptap-toolbar />
    <div x-ref="editorEl"
        class="min-h-[150px] border border-gray-200 rounded-b-lg p-3 focus:outline-none prose prose-sm max-w-none">
    </div>
</div>
```

---

## Task 9: Update `classroom/show.blade.php` display

**Files:**
- Modify: `resources/views/livewire/classroom/show.blade.php` lines 110–114

**Step 1: Remove `.ql-snow` / `.ql-editor` wrappers from announcement display**

Old (lines 110–114):
```html
<div class="ql-snow mt-2">
    <div
        class="ql-editor prose prose-sm max-w-none text-gray-700 [&_p]:my-0 [&_p]:leading-relaxed p-0!">
        {!! $announcement->content !!}</div>
</div>
```

New:
```html
<div class="prose prose-sm max-w-none text-gray-700 mt-2 [&_p]:my-0 [&_p]:leading-relaxed">
    {!! $announcement->content !!}
</div>
```

---

## Task 10: Build & smoke test

**Step 1: Build assets**

```bash
npm run build
```

Expected: zero errors.

**Step 2: Start dev server and verify**

```bash
composer run dev
```

Open browser and verify:
1. `/c/{classroom}` — announcement display renders without padding/whitespace issues, no `.ql-snow` border visible
2. `/c/{classroom}/a/create` — Tiptap editor loads with toolbar, typing works, H1/H2/H3/Bold/etc work, form submits and saves HTML
3. `/c/{classroom}/a/{assignment}` — assignment description displays correctly; edit modal opens with content pre-loaded, saves correctly
4. Material create/edit — same editor verification

**Step 3: Check no Quill references remain**

```bash
grep -r "quillEditor\|quill\.snow\|quill\.js\|window\.Quill" resources/
```

Expected: zero results.

---

## Task 11: Commit

```bash
git add resources/js/app.js resources/css/app.css resources/views/layouts/app.blade.php resources/views/components/tiptap-toolbar.blade.php resources/views/livewire/assignment/create.blade.php resources/views/livewire/assignment/show.blade.php resources/views/livewire/material/create.blade.php resources/views/livewire/material/show.blade.php resources/views/livewire/classroom/show.blade.php package.json package-lock.json
git commit -m "feat: migrate rich text editor from Quill.js to Tiptap"
```
