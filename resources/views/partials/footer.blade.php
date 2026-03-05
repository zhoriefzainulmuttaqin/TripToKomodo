<footer class="border-t border-emerald-100 bg-white">
    @php
        $contactEmail = $contactSettings['email'] ?? 'hello@triptokomodo.com';
        $contactPhone = $contactSettings['phone'] ?? '+62 812 0000 0000';
        $contactWhatsapp = $contactSettings['whatsapp'] ?? $contactPhone;
        $contactWhatsappUrl = $contactSettings['whatsapp_url'] ?? 'https://wa.me/6281200000000';

        $locale = app()->getLocale();
        $homeUrl = route('home', ['lang' => $locale]);
        $contactPageUrl = route('contact', ['lang' => $locale]);
        $toursUrl = route('tours.index', ['lang' => $locale]);
        $aboutUrl = route('about', ['lang' => $locale]);
        $blogUrl = route('blog.index', ['lang' => $locale]);

        $footerTitle = $footerSettings['title'] ?? ($siteName ?? 'Trip to Komodo');
        $footerDesc = $footerSettings['description'] ?? '';
        $paymentMethods = is_array($footerSettings['payment_methods'] ?? null) ? $footerSettings['payment_methods'] : [];
        $socialLinks = is_array($footerSettings['social_links'] ?? null) ? $footerSettings['social_links'] : [];
        $footerCopyright = $footerSettings['copyright'] ?? ('© ' . date('Y') . ' ' . ($siteName ?? 'Trip to Komodo') . '. All rights reserved.');

        $socialMeta = [
            'instagram' => ['label' => 'Instagram'],
            'facebook' => ['label' => 'Facebook'],
            'tiktok' => ['label' => 'TikTok'],
            'youtube' => ['label' => 'YouTube'],
        ];
    @endphp

    <div class="mx-auto max-w-6xl px-6 py-12">
        <div class="grid gap-8 md:grid-cols-5">
            <div class="md:col-span-2">
                <h3 class="text-lg font-semibold">{{ $footerTitle }}</h3>
                @if ($footerDesc !== '')
                    <p class="mt-3 text-sm text-slate-600">{{ $footerDesc }}</p>
                @endif
            </div>

            <div>
                <h4 class="text-sm font-semibold text-emerald-600">{{ __('home.footer.contact_title') }}</h4>
                <div class="mt-3 space-y-2 text-sm text-slate-600">
                    <p>{{ __('home.footer.phone_label') }}: {{ $contactPhone }}</p>
                    <a href="{{ $contactWhatsappUrl }}" class="inline-flex hover:text-emerald-700">{{ __('home.footer.whatsapp_label') }}: {{ $contactWhatsapp }}</a>
                    <a href="mailto:{{ $contactEmail }}" class="block hover:text-emerald-700">{{ __('home.footer.email_label') }}: {{ $contactEmail }}</a>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-emerald-600">{{ __('home.footer.quick_links_title') }}</h4>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li><a href="{{ $toursUrl }}" class="hover:text-emerald-700">{{ __('home.footer.quick_links_tours') }}</a></li>
                    <li><a href="{{ $homeUrl }}#faq" class="hover:text-emerald-700">{{ __('home.footer.quick_links_faq') }}</a></li>
                    <li><a href="{{ $aboutUrl }}" class="hover:text-emerald-700">{{ __('home.footer.quick_links_about') }}</a></li>
                    <li><a href="{{ $blogUrl }}" class="hover:text-emerald-700">{{ __('home.footer.quick_links_blog') }}</a></li>
                    <li><a href="{{ $contactPageUrl }}" class="hover:text-emerald-700">{{ __('home.footer.quick_links_consultation') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-emerald-600">{{ __('home.footer.payment_methods_title') }}</h4>
                @if (!empty($paymentMethods))
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($paymentMethods as $method)
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs text-slate-700">
                                <span class="material-symbols-outlined text-[16px] leading-none text-emerald-700" aria-hidden="true">credit_card</span>
                                <span>{{ $method }}</span>
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-500">{{ __('home.footer.not_configured') }}</p>
                @endif

                <h4 class="mt-6 text-sm font-semibold text-emerald-600">{{ __('home.footer.social_media_title') }}</h4>
                @if (!empty($socialLinks))
                    <div class="mt-3 space-y-2 text-sm">
                        @foreach ($socialLinks as $key => $url)
                            @php
                                $label = $socialMeta[$key]['label'] ?? ucfirst((string) $key);
                            @endphp
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="group flex items-center gap-2 text-slate-600 hover:text-emerald-700">
                                @if ($key === 'instagram')
                                    <svg class="h-5 w-5 shrink-0 text-slate-500 group-hover:text-emerald-700" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path fill="currentColor" d="M7.5 2h9A5.5 5.5 0 0 1 22 7.5v9A5.5 5.5 0 0 1 16.5 22h-9A5.5 5.5 0 0 1 2 16.5v-9A5.5 5.5 0 0 1 7.5 2Zm0 2A3.5 3.5 0 0 0 4 7.5v9A3.5 3.5 0 0 0 7.5 20h9a3.5 3.5 0 0 0 3.5-3.5v-9A3.5 3.5 0 0 0 16.5 4h-9Zm4.5 4a4 4 0 1 1 0 8a4 4 0 0 1 0-8Zm0 2a2 2 0 1 0 0 4a2 2 0 0 0 0-4Zm5.25-.9a.85.85 0 1 1 0 1.7a.85.85 0 0 1 0-1.7Z"/>
                                    </svg>
                                @elseif ($key === 'facebook')
                                    <svg class="h-5 w-5 shrink-0 text-slate-500 group-hover:text-emerald-700" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path fill="currentColor" d="M13.5 22v-8h2.7l.5-3h-3.2V9.3c0-.8.3-1.3 1.4-1.3h2V5.2c-.4 0-1.6-.2-3.1-.2c-2.6 0-4.3 1.6-4.3 4.5V11H7v3h2.5v8h4Z"/>
                                    </svg>
                                @elseif ($key === 'tiktok')
                                    <svg class="h-5 w-5 shrink-0 text-slate-500 group-hover:text-emerald-700" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path fill="currentColor" d="M15 2h2.1c.2 1.8 1.3 3.7 3.9 3.9V8c-1.7-.1-3.1-.6-4-1.4V15c0 3.9-3.2 7-7.1 7C6.1 22 3 18.9 3 15.1C3 11.2 6.1 8 10 8c.4 0 .8 0 1.2.1V11c-.4-.1-.8-.2-1.2-.2c-2.2 0-4 1.8-4 4.1c0 2.2 1.8 4.1 4 4.1c2.3 0 3.9-1.8 4-4.2V2Z"/>
                                    </svg>
                                @elseif ($key === 'youtube')
                                    <svg class="h-5 w-5 shrink-0 text-slate-500 group-hover:text-emerald-700" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path fill="currentColor" d="M21.6 7.2a3 3 0 0 0-2.1-2.1C17.7 4.7 12 4.7 12 4.7s-5.7 0-7.5.4A3 3 0 0 0 2.4 7.2a31.4 31.4 0 0 0-.4 4.8c0 1.6.1 3.2.4 4.8a3 3 0 0 0 2.1 2.1c1.8.4 7.5.4 7.5.4s5.7 0 7.5-.4a3 3 0 0 0 2.1-2.1c.3-1.6.4-3.2.4-4.8c0-1.6-.1-3.2-.4-4.8ZM10 15.5v-7l6 3.5-6 3.5Z"/>
                                    </svg>
                                @else
                                    <span class="material-symbols-outlined text-[18px] leading-none text-slate-500 group-hover:text-emerald-700" aria-hidden="true">link</span>
                                @endif

                                <span>{{ $label }}</span>
                                <span class="material-symbols-outlined text-[16px] leading-none text-slate-400" aria-hidden="true">open_in_new</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-500">{{ __('home.footer.not_configured') }}</p>
                @endif
            </div>
        </div>

        <div class="mt-10 border-t border-emerald-100 pt-6 text-xs text-slate-500">{{ $footerCopyright }}</div>
    </div>
</footer>
