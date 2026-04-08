<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <script src="/assets/tinymce/tinymce.min.js"></script>
    <div
        x-data="{ state: $wire.$entangle('{{ $getStatePath() }}'), tinyReady: false }"
        x-init="() => {
            const editorId = 'tiny-editor-{{ $getId() }}';

            const initTinyMCE = () => {
                tinymce.init({
                    target: document.getElementById(editorId),
                    plugins: '{{ $getPlugins() }}',
                    toolbar: '{{ $getToolbar() }}',
                    menubar: false,
                    statusbar: false,
                    height: 500,
                    skin_url: '/assets/tinymce/skins/ui/oxide',
                    content_css: document.documentElement.classList.contains('dark') ? '/assets/tinymce/skins/ui/oxide-dark/content.min.css' : '/assets/tinymce/skins/ui/oxide/content.min.css',
                    branding: false,
                    promotion: false,
                    license_key: 'gpl',
{{--                    paste_data_images: false,--}}
                    images_file_types: 'jpg,jpeg,png,gif,webp,svg',
                    relative_urls: false,
                    remove_script_host: true,
                    convert_urls: true,
                    image_advtab: true,
                    image_caption: true,
                    media_live_embeds: true,
                    file_picker_types: 'image',
                    file_picker_callback: (cb, value, meta) => {
                        if (meta.filetype === 'image') {
                            window._tinymceCb = cb;
                            window.dispatchEvent(new CustomEvent('open-media-manager'));
                        }
                    },
                    codesample_languages: [
                        { text: 'HTML/XML', value: 'markup' },
                        { text: 'JavaScript', value: 'javascript' },
                        { text: 'CSS', value: 'css' },
                        { text: 'PHP', value: 'php' },
                        { text: 'Ruby', value: 'ruby' },
                        { text: 'Python', value: 'python' },
                        { text: 'Java', value: 'java' },
                        { text: 'C', value: 'c' },
                        { text: 'C#', value: 'csharp' },
                        { text: 'C++', value: 'cpp' },
                        { text: 'SQL', value: 'sql' },
                        { text: 'JSON', value: 'json' },
                        { text: 'Bash', value: 'bash' },
                    ],
                    setup: (editor) => {
                        editor.on('init', () => {
                            editor.setContent(state || '');
                            tinyReady = true;
                        });
                        editor.on('change keyup NodeChange', () => {
                            state = editor.getContent();
                        });
                    },
                    ...{{ $getExtraOptionsJson() }}
                });
            };

            const waitForTiny = () => {
                if (typeof tinymce !== 'undefined' && document.getElementById(editorId)) {
                    initTinyMCE();
                } else {
                    setTimeout(waitForTiny, 100);
                }
            };
            waitForTiny();

            $watch('state', (val) => {
                if (!tinyReady) return;
                const ed = tinymce.get(editorId);
                if (ed && ed.getContent() !== val) ed.setContent(val || '');
            });

            $wire.on('destroy-tiny-editor', () => tinymce.remove('#' + editorId));
        }"
        wire:ignore
    >
        <textarea id="tiny-editor-{{ $getId() }}" x-ref="editor"></textarea>
    </div>
</x-dynamic-component>
