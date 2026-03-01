@php
    /** @var \App\Models\TourPackage|null $package */
    $package = $package ?? null;

    $selectedDestinations = old('destinations', $package?->destinations?->pluck('id')?->toArray() ?? []);

    $translationsOld = old('translations', []);
    $translationsExisting = $package?->translations?->keyBy('language_code') ?? collect();

    $getTrans = function ($code, $key, $default = '') use ($translationsOld, $translationsExisting) {
        if (isset($translationsOld[$code]) && array_key_exists($key, $translationsOld[$code])) {
            return $translationsOld[$code][$key];
        }

        return data_get($translationsExisting->get($code), $key, $default);
    };
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

<div class="space-y-10">
    <div>
        <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Basic</p>
        <h2 class="mt-2 text-xl font-semibold text-slate-900">Informasi Paket</h2>

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-slate-900">Operator</label>
                <select name="tour_operator_id" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required>
                    <option value="">Pilih operator</option>
                    @foreach($operators as $op)
                        <option value="{{ $op->id }}" @selected(old('tour_operator_id', $package->tour_operator_id ?? '') == $op->id)>{{ $op->name }}</option>
                    @endforeach
                </select>
                @error('tour_operator_id')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-900">Category (optional)</label>
                <select name="tour_category_id" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3">
                    <option value="">-</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('tour_category_id', $package->tour_category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('tour_category_id')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-900">Code</label>
                <input name="code" value="{{ old('code', $package->code ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" placeholder="LBJ-3D2N-LUX" required />
                @error('code')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-900">Base price (IDR)</label>
                <input type="number" step="0.01" name="base_price_idr" value="{{ old('base_price_idr', $package->base_price_idr ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required />
                @error('base_price_idr')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-900">Duration days</label>
                <input type="number" name="duration_days" min="1" value="{{ old('duration_days', $package->duration_days ?? 1) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required />
                @error('duration_days')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-900">Duration nights</label>
                <input type="number" name="duration_nights" min="0" value="{{ old('duration_nights', $package->duration_nights ?? 0) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                @error('duration_nights')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-900">Min people</label>
                <input type="number" name="min_people" min="1" value="{{ old('min_people', $package->min_people ?? 1) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required />
                @error('min_people')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-900">Max people (capacity)</label>
                <input type="number" name="max_people" min="1" value="{{ old('max_people', $package->max_people ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                @error('max_people')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-900">Status</label>
                <select name="status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required>
                    @foreach(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $k => $label)
                        <option value="{{ $k }}" @selected(old('status', $package->status ?? 'draft') === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-900">Difficulty (optional)</label>
                <input name="difficulty" value="{{ old('difficulty', $package->difficulty ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" placeholder="easy / moderate" />
                @error('difficulty')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-900">Starts from (optional)</label>
                <input type="date" name="starts_from" value="{{ old('starts_from', optional($package?->starts_from)->format('Y-m-d')) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-900">Ends at (optional)</label>
                <input type="date" name="ends_at" value="{{ old('ends_at', optional($package?->ends_at)->format('Y-m-d')) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
            </div>

            <div class="md:col-span-2 flex items-center gap-3">
                <input id="is_featured" name="is_featured" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300" @checked(old('is_featured', $package->is_featured ?? false)) />
                <label for="is_featured" class="text-sm text-slate-700">Featured package</label>
            </div>
        </div>
    </div>

    <div>
        <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Media</p>
        <h2 class="mt-2 text-xl font-semibold text-slate-900">Gambar Utama & Galeri</h2>

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-slate-900">Primary image @if(!$package) <span class="text-rose-600">*</span> @endif</label>
                <input type="file" name="primary_image" accept="image/*" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" @if(!$package) required @endif />
                @error('primary_image')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-900">Gallery images (optional)</label>
                <input type="file" name="gallery_images[]" accept="image/*" multiple class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                @error('gallery_images.*')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <p class="mt-3 text-xs text-slate-500">Gambar disimpan di <span class="font-mono">storage/app/public/tour-images</span> dan diakses via <span class="font-mono">/storage</span>.</p>
    </div>

    <div>
        <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Destinations</p>
        <h2 class="mt-2 text-xl font-semibold text-slate-900">Relasi Destinasi</h2>

        <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($destinations as $d)
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                        <input type="checkbox" name="destinations[]" value="{{ $d->id }}" class="h-4 w-4 rounded border-slate-300" @checked(in_array($d->id, $selectedDestinations)) />
                        <span class="text-slate-800">{{ $d->display_name ?? $d->name }}</span>

                    </label>
                @empty
                    <div class="text-sm text-slate-600">Belum ada destinasi. Tambahkan di menu Destinasi.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div>
        <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Multi-language & SEO</p>
        <h2 class="mt-2 text-xl font-semibold text-slate-900">Konten per Bahasa</h2>

        <div class="mt-6 space-y-6">
            @foreach($languages as $lang)
                @php $code = $lang->code; @endphp
                <details class="rounded-3xl border border-slate-200 bg-white p-5" @if($code === app()->getLocale()) open @endif>
                    <summary class="cursor-pointer list-none">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ strtoupper($code) }} <span class="text-slate-500">{{ $lang->name ?? '' }}</span></p>
                                <p class="mt-1 text-xs text-slate-500">Judul + slug + meta diperlukan untuk SEO.</p>
                            </div>
                            <span class="text-xs text-slate-500">Klik untuk buka/tutup</span>
                        </div>
                    </summary>

                    <div class="mt-5 grid gap-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-slate-900">Package name (title) @if($code === app()->getLocale())<span class="text-rose-600">*</span>@endif</label>
                            <input name="translations[{{ $code }}][title]" value="{{ $getTrans($code, 'title') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-900">Slug (optional)</label>
                            <input name="translations[{{ $code }}][slug]" value="{{ $getTrans($code, 'slug') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" placeholder="auto-from-title" />
                            <p class="mt-2 text-xs text-slate-500">Jika kosong, slug dibuat otomatis dan dibuat unik per bahasa.</p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-900">Short description (summary)</label>
                            <input name="translations[{{ $code }}][summary]" maxlength="300" value="{{ $getTrans($code, 'summary') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-slate-900">Long description</label>
                            @php $idDesc = 'quill_description_' . $code; @endphp
                            <input id="{{ $idDesc }}" type="hidden" name="translations[{{ $code }}][description]" value="{{ $getTrans($code, 'description') }}" />
                            <div class="js-quill mt-2" data-input-id="{{ $idDesc }}"><div class="js-quill-editor"></div></div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-slate-900">Itinerary</label>
                            @php $idIt = 'quill_itinerary_' . $code; @endphp
                            <input id="{{ $idIt }}" type="hidden" name="translations[{{ $code }}][itinerary]" value="{{ $getTrans($code, 'itinerary') }}" />
                            <div class="js-quill mt-2" data-input-id="{{ $idIt }}"><div class="js-quill-editor"></div></div>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-900">Included</label>
                            @php $idInc = 'quill_includes_' . $code; @endphp
                            <input id="{{ $idInc }}" type="hidden" name="translations[{{ $code }}][includes]" value="{{ $getTrans($code, 'includes') }}" />
                            <div class="js-quill mt-2" data-input-id="{{ $idInc }}" data-min-height="8"><div class="js-quill-editor"></div></div>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-900">Excluded</label>
                            @php $idExc = 'quill_excludes_' . $code; @endphp
                            <input id="{{ $idExc }}" type="hidden" name="translations[{{ $code }}][excludes]" value="{{ $getTrans($code, 'excludes') }}" />
                            <div class="js-quill mt-2" data-input-id="{{ $idExc }}" data-min-height="8"><div class="js-quill-editor"></div></div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-slate-900">Transportation</label>
                            <textarea name="translations[{{ $code }}][transportation]" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3">{{ $getTrans($code, 'transportation') }}</textarea>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-900">Meta title (optional)</label>
                            <input name="translations[{{ $code }}][meta_title]" value="{{ $getTrans($code, 'meta_title') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-900">Meta description (optional)</label>
                            <input name="translations[{{ $code }}][meta_description]" maxlength="300" value="{{ $getTrans($code, 'meta_description') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-slate-900">Meta keywords (optional)</label>
                            <input name="translations[{{ $code }}][meta_keywords]" value="{{ $getTrans($code, 'meta_keywords') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" placeholder="komodo, labuan bajo, phinisi" />
                        </div>

                        <div class="md:col-span-2 flex items-center gap-3">
                            <input id="is_active_{{ $code }}" name="translations[{{ $code }}][is_active]" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300" @checked((bool) $getTrans($code, 'is_active', true)) />
                            <label for="is_active_{{ $code }}" class="text-sm text-slate-700">Aktifkan bahasa ini</label>
                        </div>
                    </div>
                </details>
            @endforeach
        </div>

        @error('translations')
            <p class="mt-3 text-xs text-rose-600">{{ $message }}</p>
        @enderror
        @error('translations.' . app()->getLocale() . '.title')
            <p class="mt-3 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>
