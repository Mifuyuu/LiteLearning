import './bootstrap';
import Cropper from 'cropperjs';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import { TextAlign } from '@tiptap/extension-text-align';
import Placeholder from '@tiptap/extension-placeholder';
import ApexCharts from 'apexcharts';
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
    Alpine.data('rankChart', (points) => ({
        activePoint: points.length - 1,

        get activeRank() { return Number(points[this.activePoint].rank).toLocaleString(); },

        init() {
            // ponytail: Livewire evaluates x-data on the morphed fragment before it's
            // attached to the document, so $refs.chart is briefly undefined here.
            // requestAnimationFrame runs after that attach completes; look it up
            // again inside the callback rather than closing over the stale value.
            requestAnimationFrame(() => {
                const el = this.$refs.chart;
                // Livewire's lazy-load hydration calls init() more than once for the
                // same element; guard against mounting a second chart onto it.
                if (!el || el.dataset.chartMounted) return;
                el.dataset.chartMounted = '1';
                new ApexCharts(el, {
                    chart: {
                        type: 'area',
                        height: el.clientHeight,
                        sparkline: { enabled: true },
                        animations: { enabled: false },
                        events: {
                            dataPointMouseEnter: (_e, _ctx, { dataPointIndex }) => { this.activePoint = dataPointIndex; },
                        },
                    },
                    series: [{ name: 'อันดับ', data: points.map((p) => p.rank) }],
                    yaxis: { reversed: true },
                    colors: ['#2563eb'],
                    stroke: { curve: 'smooth', width: 2.5 },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.15, opacityTo: 0 } },
                    tooltip: {
                        x: { formatter: (_val, { dataPointIndex }) => points[dataPointIndex].day },
                        y: { formatter: (val) => '#' + Number(val).toLocaleString() },
                    },
                }).render();
            });
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
