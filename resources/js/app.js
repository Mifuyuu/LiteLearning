import './bootstrap';
import Sortable from 'sortablejs';
import Cropper from 'cropperjs';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import { TextAlign } from '@tiptap/extension-text-align';
import Placeholder from '@tiptap/extension-placeholder';

window.Cropper = Cropper;

document.addEventListener('alpine:init', () => {
    Alpine.data('tiptapEditor', ({ wireModel, placeholder = '' }) => {
        let editor = null;

        return {
            init() {
                const self = this;
                editor = new Editor({
                    element: self.$refs.editorEl,
                    extensions: [
                        StarterKit.configure({
                            link: { openOnClick: false },
                        }),
                        TextAlign.configure({ types: ['heading', 'paragraph'] }),
                        Placeholder.configure({ placeholder }),
                    ],
                    content: self.$wire.get(wireModel) || '',
                    onBlur() { self.flush(); },
                });

                const form = self.$el.closest('form');
                if (form) {
                    form.addEventListener('submit', () => self.flush(), { capture: true });
                }

                self.$watch('$wire.' + wireModel, (val) => {
                    if (editor && val !== editor.getHTML()) {
                        editor.commands.setContent(val || '', false);
                    }
                });
            },

            flush() {
                if (!editor) return;
                const html = editor.getHTML();
                this.$wire.set(wireModel, html === '<p></p>' ? '' : html);
            },

            isActive(type, opts = {}) {
                return editor?.isActive(type, opts) ?? false;
            },

            toggleBold()       { editor?.chain().focus().toggleBold().run(); },
            toggleItalic()     { editor?.chain().focus().toggleItalic().run(); },
            toggleUnderline()  { editor?.chain().focus().toggleUnderline().run(); },
            toggleStrike()     { editor?.chain().focus().toggleStrike().run(); },
            toggleHeading(lvl) { editor?.chain().focus().toggleHeading({ level: lvl }).run(); },
            setParagraph()     { editor?.chain().focus().setParagraph().run(); },
            setAlign(val)      { editor?.chain().focus().setTextAlign(val).run(); },
            toggleOrdered()    { editor?.chain().focus().toggleOrderedList().run(); },
            toggleBullet()     { editor?.chain().focus().toggleBulletList().run(); },
            clearFormat()      { editor?.chain().focus().unsetAllMarks().clearNodes().run(); },

            setLink() {
                const url = window.prompt('URL');
                if (url === null) return;
                if (url === '') {
                    editor?.chain().focus().extendMarkRange('link').unsetLink().run();
                } else {
                    editor?.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
                }
            },

            destroy() {
                editor?.destroy();
                editor = null;
            },
        };
    });
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
