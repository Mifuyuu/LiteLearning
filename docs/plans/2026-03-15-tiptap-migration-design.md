# Tiptap Migration Design

**Date**: 2026-03-15  
**Status**: Approved  
**Scope**: Replace Quill.js with Tiptap across all rich-text editor usages in LiteLearning

---

## Context

Quill.js is currently used as the rich text editor in 4 create/edit views and rendered in 2 display views. The migration to Tiptap eliminates CSS scoping issues (`.ql-editor` padding overrides, `prose` conflicts) and provides a headless, Tailwind-native toolbar.

---

## Current State

### Quill Usage

**Editor (input) — 4 locations:**
| File | `wireModel` | Placeholder |
|------|-------------|-------------|
| `resources/views/livewire/assignment/create.blade.php` | `description` | Add a description or instructions... |
| `resources/views/livewire/assignment/show.blade.php` | `editDescription` | Add a description or instructions... |
| `resources/views/livewire/material/create.blade.php` | `description` | Add a description... |
| `resources/views/livewire/material/show.blade.php` | `editDescription` | Add a description... |

**Display (output) — 2 locations:**
| File | Wrapper classes |
|------|-----------------|
| `resources/views/livewire/classroom/show.blade.php` | `.ql-snow > .ql-editor.prose` |
| `resources/views/livewire/assignment/show.blade.php` | `.prose` |

**New editor — 1 location (to be added):**
| File | `wireModel` | Note |
|------|-------------|------|
| `resources/views/livewire/classroom/show.blade.php` | `announcementBody` | Currently plain `<textarea>` |

### Quill Infrastructure
- **CDN**: loaded in `resources/views/layouts/app.blade.php` lines 58–60 (quill.snow.css + quill.js)
- **npm**: `quill: ^2.0.3` in `package.json` (not used by Vite currently — CDN-only)
- **Alpine component**: `quillEditor` in `resources/js/app.js` lines 10–59
- **CSS overrides**: `.ql-toolbar.ql-snow` styles in `resources/css/app.css` lines 241–250

---

## Design

### Approach: Tiptap Vanilla + Alpine.js

Mirror the existing `quillEditor` Alpine pattern with a `tiptapEditor` component. No PHP packages. Toolbar built with Tailwind.

### npm Dependencies

**Add:**
```
@tiptap/core
@tiptap/starter-kit       ← Bold, Italic, Strike, Heading (H1-H3), BulletList, OrderedList, Blockquote, Code, HardBreak, HorizontalRule, Paragraph
@tiptap/extension-underline
@tiptap/extension-text-align
@tiptap/extension-link
@tiptap/extension-placeholder
```

**Remove:** `quill`

### Alpine Component (`resources/js/app.js`)

Replace `quillEditor` with `tiptapEditor`:

```js
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import { TextAlign } from '@tiptap/extension-text-align';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';

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
    },

    flush() {
        if (!this._editor) return;
        const html = this._editor.getHTML();
        const clean = html === '<p></p>' ? '' : html;
        this.$wire.set(wireModel, clean);
    },

    destroy() {
        this._editor?.destroy();
        this._editor = null;
    },
}));
```

### Toolbar

Create `resources/views/components/tiptap-toolbar.blade.php` — a Tailwind-styled toolbar using Alpine `$dispatch` or direct editor method calls via `x-on` events.

Toolbar buttons (matching current Quill toolbar):
- **Text style**: H1, H2, H3, Normal
- **Inline**: Bold, Italic, Underline, Strike
- **Align**: Left, Center, Right
- **Lists**: Ordered, Bullet
- **Other**: Link, Clear formatting

### Blade Usage (all 5 editor locations)

```html
<div wire:ignore x-data="tiptapEditor({ wireModel: 'description', placeholder: '...' })">
    <x-tiptap-toolbar />
    <div x-ref="editorEl" class="..."></div>
</div>
```

### Display (render HTML)

Remove `.ql-snow` / `.ql-editor` wrappers. Use `prose` Tailwind directly:

```html
<div class="prose prose-sm max-w-none text-gray-700 [&_p]:my-0 [&_p]:leading-relaxed">
    {!! $content !!}
</div>
```

### Infrastructure Cleanup

- Remove Quill CDN from `resources/views/layouts/app.blade.php`
- Remove `.ql-toolbar` CSS overrides from `resources/css/app.css`
- Add Tiptap placeholder CSS (`.tiptap p.is-editor-empty:first-child::before`)

---

## Files Modified

| File | Change |
|------|--------|
| `package.json` | Remove `quill`, add `@tiptap/*` packages |
| `resources/js/app.js` | Replace `quillEditor` Alpine component with `tiptapEditor` |
| `resources/views/components/tiptap-toolbar.blade.php` | **Create new** — Tailwind toolbar component |
| `resources/views/layouts/app.blade.php` | Remove Quill CDN (lines 58–60) |
| `resources/css/app.css` | Remove `.ql-toolbar` overrides, add Tiptap placeholder style |
| `resources/views/livewire/assignment/create.blade.php` | `quillEditor` → `tiptapEditor` + toolbar |
| `resources/views/livewire/assignment/show.blade.php` | Editor + display: remove `.ql-snow`/`.ql-editor` wrappers |
| `resources/views/livewire/material/create.blade.php` | `quillEditor` → `tiptapEditor` + toolbar |
| `resources/views/livewire/material/show.blade.php` | `quillEditor` → `tiptapEditor` + toolbar |
| `resources/views/livewire/classroom/show.blade.php` | Add `tiptapEditor` to announcement form + update display |

---

## Constraints

- Output format remains HTML (no JSON) — no backend changes needed
- Toolbar features identical to current Quill toolbar
- Alpine.js pattern preserved (no new JS framework)
- Tailwind v4 (`@theme` in `app.css`, no `tailwind.config.js`)
