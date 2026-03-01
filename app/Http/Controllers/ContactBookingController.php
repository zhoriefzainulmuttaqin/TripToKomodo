<?php

namespace App\Http\Controllers;

use App\Models\WebSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactBookingController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['required', 'string', 'max:50'],
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'traveler_count' => ['nullable', 'integer', 'min:1', 'max:100'],
            'budget' => ['nullable', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:1000'],
            'channel' => ['required', 'in:email,whatsapp'],
            'website' => ['nullable', 'string', 'max:0'],
        ]);

        $contactEmail = WebSetting::get(WebSetting::KEY_CONTACT_EMAIL, config('mail.from.address')) ?? 'hello@triptokomodo.com';
        $contactWhatsapp = WebSetting::get(WebSetting::KEY_CONTACT_WHATSAPP, '');
        $whatsappDigits = preg_replace('/\D+/', '', (string) $contactWhatsapp);

        $labels = trans('home.booking.labels');

        $lines = [
            trans('home.booking.email_intro'),
            ($labels['name'] ?? 'Nama') . ': ' . $validated['name'],
            ($labels['email'] ?? 'Email') . ': ' . ($validated['email'] ?? '-'),
            ($labels['phone'] ?? 'Telepon') . ': ' . $validated['phone'],
            ($labels['travel_date'] ?? 'Tanggal Trip') . ': ' . ($validated['travel_date'] ?? '-'),
            ($labels['traveler_count'] ?? 'Jumlah Orang') . ': ' . ($validated['traveler_count'] ?? '-'),
            ($labels['budget'] ?? 'Budget') . ': ' . ($validated['budget'] ?? '-'),
            ($labels['message'] ?? 'Catatan') . ': ' . ($validated['message'] ?? '-'),
        ];


        $compiledMessage = implode("\n", $lines);

        if ($validated['channel'] === 'whatsapp') {
            if (empty($whatsappDigits)) {
                return back()->withErrors(['form' => trans('home.booking.messages.whatsapp_unset')])->withInput();
            }


            $whatsappUrl = 'https://wa.me/' . $whatsappDigits . '?text=' . urlencode($compiledMessage);

            return redirect()->away($whatsappUrl);
        }

        try {
            Mail::raw($compiledMessage, function ($message) use ($contactEmail, $validated) {
                $message->to($contactEmail)
                    ->subject(trans('home.booking.email_subject', ['name' => $validated['name']]));


                if (!empty($validated['email'])) {
                    $message->replyTo($validated['email'], $validated['name']);
                }
            });
        } catch (\Throwable) {
            return back()->withErrors(['form' => trans('home.booking.messages.email_failed')])->withInput();
        }

        return back()->with('status', trans('home.booking.messages.sent'));

    }
}
