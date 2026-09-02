import './bootstrap';
import Cropper from 'cropperjs';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import { TextAlign } from '@tiptap/extension-text-align';
import Placeholder from '@tiptap/extension-placeholder';
import flatpickr from 'flatpickr';
import { Thai } from 'flatpickr/dist/l10n/th.js';

window.Cropper = Cropper;
window.flatpickr = flatpickr;

document.addEventListener('alpine:init', () => {
    Alpine.data('otpCountdown', (wireModel = 'resendCooldown') => ({
        cooldown: null,
        timer: null,

        init() {
            this.cooldown = this.$wire.entangle(wireModel);
            this.startTimer();
            this.$watch('cooldown', (value) => {
                if (value > 0 && !this.timer) this.startTimer();
            });
        },

        startTimer() {
            if (this.timer) clearInterval(this.timer);
            this.timer = setInterval(() => {
                if (this.cooldown > 0) {
                    this.cooldown--;
                } else {
                    clearInterval(this.timer);
                    this.timer = null;
                }
            }, 1000);
        },
    }));

    Alpine.data('datetimePicker', ({ wireModel, placeholder = 'เลือกวันและเวลา' }) => {
        let picker = null;

        return {
            init() {
                const self = this;
                const inputEl = self.$refs.inputEl;
                if (!inputEl) return;

                picker = flatpickr(inputEl, {
                    locale: Thai,
                    disableMobile: true,
                    enableTime: true,
                    time_24hr: true,
                    dateFormat: 'Y-m-d H:i',
                    altInput: true,
                    altFormat: 'j M Y เวลา H:i น.',
                    altInputClass: inputEl.className,
                    placeholder: placeholder,
                    defaultDate: self.$wire.get(wireModel) || null,
                    onChange(selectedDates, dateStr) {
                        self.$wire.set(wireModel, dateStr || null);
                    },
                });

                self.$watch('$wire.' + wireModel, (val) => {
                    if (picker && val !== picker.input.value) {
                        picker.setDate(val || '', false);
                    }
                });

                (picker.altInput || inputEl).addEventListener('mousedown', () => {
                    if (picker.isOpen) picker.close();
                });
            },

            clear() {
                picker?.clear();
            },

            destroy() {
                picker?.destroy();
                picker = null;
            }
        };
    });
    // Hand-rolled sparkline: plain SVG path + hover math, no charting library
    // for one small line chart.
    Alpine.data('rankChart', (points) => ({
        points,
        hover: false,
        activePoint: points.length - 1,
        viewH: 40,
        padY: 4,

        get activeRank() { return Number(this.points[this.activePoint].rank).toLocaleString(); },
        get activeDay() { return this.points[this.activePoint].day; },

        x(i) { return this.points.length > 1 ? (i / (this.points.length - 1)) * 100 : 50; },
        y(rank) {
            const ranks = this.points.map((p) => p.rank);
            const min = Math.min(...ranks), max = Math.max(...ranks);
            const usable = this.viewH - this.padY * 2;
            return max === min ? this.viewH / 2 : this.padY + ((rank - min) / (max - min)) * usable;
        },
        get linePath() {
            const coords = this.points.map((p, i) => [this.x(i), this.y(p.rank)]);
            let path = `M${coords[0][0].toFixed(2)},${coords[0][1].toFixed(2)}`;
            for (let i = 1; i < coords.length; i++) {
                const [x0, y0] = coords[i - 1];
                const [x1, y1] = coords[i];
                const xMid = (x0 + x1) / 2;
                path += ` C${xMid.toFixed(2)},${y0.toFixed(2)} ${xMid.toFixed(2)},${y1.toFixed(2)} ${x1.toFixed(2)},${y1.toFixed(2)}`;
            }
            return path;
        },
        get areaPath() {
            return `${this.linePath} L${this.x(this.points.length - 1).toFixed(2)},${this.viewH} L${this.x(0).toFixed(2)},${this.viewH} Z`;
        },

        onMove(e) {
            const rect = this.$refs.chart.getBoundingClientRect();
            const ratio = (e.clientX - rect.left) / rect.width;
            const i = Math.round(ratio * (this.points.length - 1));
            this.activePoint = Math.min(Math.max(i, 0), this.points.length - 1);
        },
    }));

    Alpine.data('tiptapEditor', ({ wireModel, placeholder = '' }) => {
        let editor = null;

        return {
            updatedAt: 0,

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
                    onTransaction() { self.updatedAt++; },
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
                void this.updatedAt;
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

            showLinkModal: false,
            linkUrl: '',

            openLinkModal() {
                this.linkUrl = editor?.getAttributes('link').href || '';
                this.showLinkModal = true;
                this.$nextTick(() => {
                    this.$refs.linkInput?.focus();
                });
            },

            saveLink() {
                const url = this.linkUrl.trim();
                this.showLinkModal = false;
                if (!url) {
                    editor?.chain().focus().extendMarkRange('link').unsetLink().run();
                } else {
                    editor?.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
                }
                this.linkUrl = '';
            },

            removeLink() {
                this.showLinkModal = false;
                editor?.chain().focus().extendMarkRange('link').unsetLink().run();
                this.linkUrl = '';
            },

            destroy() {
                editor?.destroy();
                editor = null;
            },
        };
    });
});
