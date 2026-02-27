<?php

namespace App\Services;

use App\Models\InternalLinkRule;

class InternalLinkService
{
    public function inject(string $html, ?string $languageCode = null): string
    {
        $rules = InternalLinkRule::query()
            ->where('is_active', true)
            ->when($languageCode, fn ($query) => $query->where('language_code', $languageCode))
            ->orderByDesc('priority')
            ->get();

        foreach ($rules as $rule) {
            $pattern = sprintf('/\b(%s)\b/i', preg_quote($rule->keyword, '/'));
            $replacement = sprintf('<a href="%s" class="text-emerald-600 underline underline-offset-4">$1</a>', $rule->target_url);
            $html = preg_replace($pattern, $replacement, $html, 1) ?? $html;
        }

        return $html;
    }
}
