<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController
{
    public function index(Request $request): View
    {
        $query = Faq::query();

        if ($request->filled('q')) {
            $q = trim((string) $request->string('q'));
            $query->where('question', 'like', "%{$q}%");
        }

        if ($request->filled('language')) {
            $query->where('language_code', $request->string('language')->toString());
        }

        if ($request->filled('status')) {
            if ($request->string('status')->toString() === 'active') {
                $query->where('is_active', true);
            }

            if ($request->string('status')->toString() === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $faqs = $query
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.faqs.index', [
            'faqs' => $faqs,
        ]);
    }

    public function create(): View
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'language_code' => ['required', 'string', 'max:5'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $faq = new Faq();
        $faq->language_code = $validated['language_code'];
        $faq->question = $validated['question'];
        $faq->answer = $validated['answer'];
        $faq->sort_order = (int) ($validated['sort_order'] ?? 0);
        $faq->is_active = (bool) ($validated['is_active'] ?? true);
        $faq->save();

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ berhasil ditambahkan.');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.edit', [
            'faq' => $faq,
        ]);
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'language_code' => ['required', 'string', 'max:5'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $faq->language_code = $validated['language_code'];
        $faq->question = $validated['question'];
        $faq->answer = $validated['answer'];
        $faq->sort_order = (int) ($validated['sort_order'] ?? 0);
        $faq->is_active = (bool) ($validated['is_active'] ?? false);
        $faq->save();

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ berhasil diperbarui.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ berhasil dihapus.');
    }
}
