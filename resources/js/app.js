import './bootstrap';
import Sortable from 'sortablejs';
import Cropper from 'cropperjs';

window.Cropper = Cropper;

// Alpine.js is loaded by Livewire automatically

document.addEventListener('alpine:init', () => {
	Alpine.data('quillEditor', ({ wireModel, placeholder = '' }) => ({
		_quill: null,

		init() {
			const self = this;
			const el = self.$refs.editorEl;

		self._quill = new window.Quill(el, {
				theme: 'snow',
				modules: {
					toolbar: [
						[{ header: [1, 2, 3, false] }],
						['bold', 'italic', 'underline', 'strike'],
						[{ align: [] }],
						[{ list: 'ordered' }, { list: 'bullet' }],
						['link'],
						['clean'],
					],
					clipboard: { matchVisual: false },
				},
				placeholder,
			});

			// Load initial content
			const initial = self.$wire.get(wireModel) || '';
			if (initial) {
				self._quill.root.innerHTML = initial;
			}

			// Sync to Livewire on blur
			self._quill.root.addEventListener('blur', () => self.flush());

			// Also flush when the parent form submits (before Livewire sends the request)
			const form = self.$el.closest('form');
			if (form) {
				form.addEventListener('submit', () => self.flush(), { capture: true });
			}
		},

		flush() {
			if (!this._quill) return;
			const html = this._quill.root.innerHTML;
			const clean = html === '<p><br></p>' ? '' : html;
			this.$wire.set(wireModel, clean);
		},

		destroy() {
			this._quill = null;
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
			themeColor: classroom.themeColor ?? classroom.theme_color,
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
