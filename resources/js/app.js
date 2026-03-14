import './bootstrap';
import Sortable from 'sortablejs';
import Cropper from 'cropperjs';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import { TextAlign } from '@tiptap/extension-text-align';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';

window.Cropper = Cropper;

// Alpine.js is loaded by Livewire automatically

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

            // Re-sync content when Livewire updates (e.g. edit modal open)
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

function setSidebarEmptyState(list) {
	const placeholder = list.querySelector('[data-empty-pinned]');
	if (!placeholder) return;

	const hasItems = list.querySelectorAll('[data-classroom-id]').length > 0;
	placeholder.classList.toggle('hidden', hasItems);
}

function buildSidebarClassroomItem(classroom) {
	const item = document.createElement('a');
	item.href = classroom.url;
	item.dataset.classroomId = String(classroom.classroomId);
	item.className = 'flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors text-gray-700 hover:bg-gray-100';

	try {
		const currentPath = window.location.pathname;
		const itemPath = new URL(classroom.url, window.location.origin).pathname;
		if (currentPath === itemPath) {
			item.className = 'flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors bg-indigo-50 text-indigo-700';
		}
	} catch (_) {
	}

	const dot = document.createElement('div');
	dot.className = 'w-5 h-5 rounded mr-3 shrink-0';
	dot.style.backgroundColor = classroom.themeColor;

	const text = document.createElement('span');
	text.className = 'truncate';
	text.textContent = classroom.name;

	item.appendChild(dot);
	item.appendChild(text);

	return item;
}

function updateSidebarList(list, classroom, shouldInclude) {
	const id = Number(classroom.classroomId ?? classroom.classroom_id);
	const selector = `[data-classroom-id="${id}"]`;
	const existing = list.querySelector(selector);

	if (!shouldInclude) {
		existing?.remove();
		setSidebarEmptyState(list);
		return;
	}

	if (!existing) {
		list.appendChild(buildSidebarClassroomItem({
			...classroom,
			classroomId: id,
			themeColor: classroom.color ?? classroom.themeColor ?? '#8B5CF6',
		}));
	}

	setSidebarEmptyState(list);
}

function initSidebarSortable() {
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
	const lists = document.querySelectorAll('[data-sortable-sidebar]');

	lists.forEach((list) => {
		if (list.dataset.sortableInit === '1') {
			return;
		}

		const items = list.querySelectorAll('[data-classroom-id]');
		if (items.length < 2) {
			return;
		}

		Sortable.create(list, {
			animation: 150,
			ghostClass: 'opacity-60',
			draggable: '[data-classroom-id]',
			onEnd: () => {
				const orderedIds = Array.from(list.querySelectorAll('[data-classroom-id]'))
					.map((item) => Number(item.dataset.classroomId))
					.filter((id) => Number.isFinite(id));

				window.axios.post('/sidebar/classrooms/reorder', {
					_token: csrfToken,
					orderedIds,
				}).catch(() => {
				});
			},
		});

		list.dataset.sortableInit = '1';
	});
}

document.addEventListener('DOMContentLoaded', initSidebarSortable);
document.addEventListener('livewire:navigated', initSidebarSortable);

document.addEventListener('livewire:init', () => {
	Livewire.on('sidebar-classroom-pinned-updated', (payload) => {
		const classroom = Array.isArray(payload) ? payload[0] : payload;
		if (!classroom) return;

		document.querySelectorAll('[data-sidebar-list="enrolled"]').forEach((list) => {
			updateSidebarList(list, classroom, Boolean(classroom.pinned && classroom.enrolled));
		});

		document.querySelectorAll('[data-sidebar-list="teaching"]').forEach((list) => {
			updateSidebarList(list, classroom, Boolean(classroom.pinned && classroom.teaching));
		});

		initSidebarSortable();
	});
});

window.addEventListener('sidebar-classroom-pinned-updated', (event) => {
	const classroom = event?.detail;
	if (!classroom) return;

	document.querySelectorAll('[data-sidebar-list="enrolled"]').forEach((list) => {
		updateSidebarList(list, classroom, Boolean((classroom.pinned ?? classroom.is_pinned) && (classroom.enrolled ?? classroom.is_enrolled)));
	});

	document.querySelectorAll('[data-sidebar-list="teaching"]').forEach((list) => {
		updateSidebarList(list, classroom, Boolean((classroom.pinned ?? classroom.is_pinned) && (classroom.teaching ?? classroom.is_teaching)));
	});

	initSidebarSortable();
});
