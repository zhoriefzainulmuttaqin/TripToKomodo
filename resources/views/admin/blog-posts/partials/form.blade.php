@php
    $featuredUrl = !empty($post->featured_image_path) ? '/storage/' . ltrim($post->featured_image_path, '/') : null;
    $ogUrl = !empty($post->og_image_path) ? '/storage/' . ltrim($post->og_image_path, '/') : null;
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <style>
        .js-quill {
            border: 1px solid #e2e8f0; /* slate-200 */
            border-radius: 1rem; /* rounded-2xl */
            overflow: hidden;
            background: #fff;
        }

        .js-quill .ql-toolbar.ql-snow {
            border: 0;
            border-bottom: 1px solid #e2e8f0; /* slate-200 */
        }

        .js-quill .ql-container.ql-snow {
            border: 0;
        }

        .js-quill .ql-editor {
            font-size: 0.875rem;
            line-height: 1.5;
            min-height: 10rem;
        }

        .js-quill[data-min-height="8"] .ql-editor {
            min-height: 8rem;
        }


        /* Tailwind preflight menghilangkan bullet/number list. Balikin khusus untuk konten Quill. */
        .js-quill .ql-editor ul {
            list-style: disc;
            padding-left: 1.25rem;
        }

        .js-quill .ql-editor ol {
            list-style: decimal;
            padding-left: 1.25rem;
        }

        .js-quill .ql-editor h1 {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin: 0.75rem 0 0.5rem;
        }

        .js-quill .ql-editor h2 {
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.25;
            margin: 0.75rem 0 0.5rem;
        }

        .js-quill .ql-editor h3 {
            font-size: 1.125rem;
            font-weight: 700;
            line-height: 1.3;
            margin: 0.75rem 0 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.Quill) return;

            const editors = document.querySelectorAll('.js-quill');
            editors.forEach((container) => {
                const inputId = container.getAttribute('data-input-id');
                if (!inputId) return;

                const input = document.getElementById(inputId);
                if (!input) return;

                const editorEl = container.querySelector('.js-quill-editor');
                if (!editorEl) return;

                const quill = new Quill(editorEl, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ header: [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['blockquote', 'code-block'],
                            ['link'],
                            ['clean'],
                        ],
                    },
                });

                // Set initial HTML
                const initial = (input.value || '').trim();
                if (initial !== '') {
                    quill.clipboard.dangerouslyPasteHTML(initial);
                }

                const syncToInput = () => {
                    const text = (quill.getText() || '').trim();
                    if (text === '') {
                        input.value = '';
                        return;
                    }

                    input.value = quill.root.innerHTML;
                };

                quill.on('text-change', syncToInput);

                // ensure sync on load
                syncToInput();
            });
        });
    </script>
@endpush

<div class="grid gap-6">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Bahasa</label>
            <select name="language_code" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                @foreach ($supportedLocales as $code)
                    <option value="{{ $code }}" {{ old('language_code', $post->language_code ?? 'id') === $code ? 'selected' : '' }}>{{ strtoupper($code) }}</option>
                @endforeach
            </select>
            @error('language_code')
                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Group Key</label>
            <input type="text" value="{{ $post->group_key ?? ($translateFrom->group_key ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-mono" disabled />
            <p class="mt-2 text-xs text-slate-500">Otomatis. Terjemahan menggunakan Group Key yang sama.</p>
        </div>
    </div>

    @if (!empty($translateFrom))
        <input type="hidden" name="translate_from" value="{{ $translateFrom->id }}" />
        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/50 p-4 text-sm text-indigo-900">
            Terjemahan dari: <span class="font-semibold">{{ strtoupper($translateFrom->language_code) }} — {{ $translateFrom->title }}</span>
        </div>
    @endif

    <div>
        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Judul</label>
        <input type="text" name="title" value="{{ old('title', $post->title ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
        @error('title')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $post->slug ?? '') }}" placeholder="kosongkan untuk auto dari judul" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-mono" />
        @error('slug')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Excerpt (ringkasan)</label>
        <textarea name="excerpt" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
        @error('excerpt')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Konten</label>
        <textarea id="post-content" name="content" class="hidden">{{ old('content', $post->content ?? '') }}</textarea>
        <div class="mt-2 js-quill" data-input-id="post-content">
            <div class="js-quill-editor"></div>
        </div>
        <p class="mt-2 text-xs text-slate-500">Konten disimpan sebagai HTML. Internal link rule akan otomatis injeksi 1x per keyword.</p>
        @error('content')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <p class="text-sm font-semibold text-slate-900">Featured Image</p>
            @if (!empty($featuredUrl))
                <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                    <img src="{{ $featuredUrl }}" alt="Preview" class="h-44 w-full object-cover" />
                </div>
                <label class="mt-3 flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="remove_featured_image" value="1" class="rounded border-slate-300" />
                    Hapus featured image
                </label>
            @endif
            <input type="file" name="featured_image" accept="image/*" class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
            @error('featured_image')
                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <p class="text-sm font-semibold text-slate-900">OG Image (opsional)</p>
            @if (!empty($ogUrl))
                <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                    <img src="{{ $ogUrl }}" alt="Preview" class="h-44 w-full object-cover" />
                </div>
                <label class="mt-3 flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="remove_og_image" value="1" class="rounded border-slate-300" />
                    Hapus OG image
                </label>
            @endif
            <input type="file" name="og_image" accept="image/*" class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
            @error('og_image')
                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <p class="text-sm font-semibold text-slate-900">SEO</p>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Canonical URL (opsional)</label>
                <input type="text" name="canonical_url" value="{{ old('canonical_url', $post->canonical_url ?? '') }}" placeholder="kosongkan untuk auto" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Meta Description</label>
            <textarea name="meta_description" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
        </div>

        <div class="mt-4">
            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Meta Keywords</label>
            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $post->meta_keywords ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Robots (opsional)</label>
                <input type="text" name="meta_robots" value="{{ old('meta_robots', $post->meta_robots ?? '') }}" placeholder="index,follow" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-mono" />
                <p class="mt-2 text-xs text-slate-500">Contoh: <span class="font-mono">index,follow</span> / <span class="font-mono">noindex,follow</span>.</p>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Schema JSON-LD (opsional)</label>
                <textarea name="schema_json_ld" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs font-mono">{{ old('schema_json_ld', $post->schema_json_ld ?? '') }}</textarea>
            </div>
        </div>

        <div class="mt-6">
            <p class="text-sm font-semibold text-slate-900">Open Graph (opsional)</p>
            <div class="mt-3 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">OG Title</label>
                    <input type="text" name="og_title" value="{{ old('og_title', $post->og_title ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">OG Description</label>
                    <input type="text" name="og_description" value="{{ old('og_description', $post->og_description ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-5">
        <p class="text-sm font-semibold text-slate-900">Statistik</p>
        @php
            $reading = $post->readingTimeMinutesComputed();
            $views = (int) ($post->view_count ?? 0);
        @endphp
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-600">Views</p>
                <p class="mt-1 text-lg font-semibold text-slate-900">{{ $post->exists ? number_format($views) : '—' }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-600">Waktu Baca</p>
                <p class="mt-1 text-lg font-semibold text-slate-900">{{ !empty($reading) ? ($reading . ' min') : '—' }}</p>
            </div>
        </div>
        <p class="mt-3 text-xs text-slate-500">Views dihitung 1x per session untuk menghindari double count saat refresh.</p>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-5">
        <p class="text-sm font-semibold text-slate-900">Publish</p>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <label class="flex items-center gap-2 text-sm text-slate-800">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $post->is_published ?? false) ? 'checked' : '' }} class="rounded border-slate-300" />
                Publish
            </label>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Published At (opsional)</label>
                <input type="datetime-local" name="published_at" value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\\TH:i')) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
            </div>
        </div>
    </div>
</div>
