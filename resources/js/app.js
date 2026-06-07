import './bootstrap';
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
