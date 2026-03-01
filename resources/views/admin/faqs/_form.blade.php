@php
    $faq = $faq ?? null;
@endphp

<div class="grid gap-6 md:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-slate-700" for="language_code">Bahasa</label>
        <select name="language_code" id="language_code" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm">
            @foreach ($activeLanguages as $language)
                <option value="{{ $language->code }}" @selected(old('language_code', $faq?->language_code) === $language->code)>
                    {{ strtoupper($language->code) }} - {{ $language->name ?? $language->native_name ?? '' }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold text-slate-700" for="sort_order">Urutan</label>
        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $faq?->sort_order ?? 0) }}" min="0" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm" />
    </div>
</div>

<div class="mt-6">
    <label class="block text-sm font-semibold text-slate-700" for="question">Pertanyaan</label>
    <input type="text" name="question" id="question" value="{{ old('question', $faq?->question) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm" required />
</div>

<div class="mt-6">
    <label class="block text-sm font-semibold text-slate-700" for="answer">Jawaban</label>
    <textarea name="answer" id="answer" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm" required>{{ old('answer', $faq?->answer) }}</textarea>
</div>

<div class="mt-6 flex items-center gap-3">
    <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4" @checked(old('is_active', $faq?->is_active ?? true))>
    <label for="is_active" class="text-sm text-slate-700">Aktifkan FAQ ini</label>
</div>
