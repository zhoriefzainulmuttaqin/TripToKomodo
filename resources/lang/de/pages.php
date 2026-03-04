<?php

return [
    'tours' => [
        'page' => [
            'title' => 'Labuan Bajo Tourpakete | TriptoKomodo',
            'meta' => 'Entdecke Labuan Bajo Tourpakete: Premium-Boote, flexible Routen, lokale Guides und transparente Preise. Schnelle Buchung mit mehrsprachigem Support.',
            'keywords' => 'labuan bajo tourpakete, komodo tour, komodo reise, private tour labuan bajo, komodo liveaboard, komodo nationalpark',
        ],
        'hero' => [
            'tag' => 'Tourpakete',
            'headline' => 'Labuan Bajo Pakete für jeden Reisestil',
            'sub' => 'Wähle Dauer, Bootstyp und das beste Erlebnis für deine Reise.',
        ],
        'filters' => [
            'chip_category' => 'Bootstyp: :value',
            'chip_duration' => 'Dauer: :value',
            'chip_destination' => 'Ziele: :value',
            'reset' => 'Zurücksetzen',
            'total' => 'Pakete gesamt: :count',
            'duration_day' => 'Tage',
            'duration_night' => 'Nächte',
            'card_summary_fallback' => 'Premium Labuan Bajo Tourpaket.',
            'card_cta' => 'Details ansehen →',
            'card_unavailable' => 'Details nicht verfügbar',
        ],
        'cards' => [
            'from' => 'Ab',
            'per_person' => 'pro Person',
            'see_detail' => 'Details ansehen',
        ],
        'detail' => [
            'meta_description_fallback' => 'Premium Labuan Bajo Tourpaket.',
            'price_suffix' => '/ Person',
            'currency_idr' => 'Rp',

            'cta_overview' => 'Details ansehen',
            'cta_consult' => 'Reiseberatung',

            'badge_max' => 'Max. :count',

            'stats' => [
                'price_from' => 'Preis ab',
                'operator' => 'Anbieter',
                'availability' => 'Verfügbarkeit',
            ],

            'availability_not_set' => 'Noch nicht eingestellt',
            'availability_count' => '{1} :count Termin verfügbar|[2,*] :count Termine verfügbar',

            'booking' => [
                'title' => 'Schnell buchen',
                'note' => 'Preise passen sich automatisch an die gewählte Währung an.',
                'min' => 'Min.',
                'max' => 'Max.',
                'pax' => 'Pax',
                'status' => 'Status',
                'cta_consult_book' => 'Beratung & Buchung',
                'cta_check_availability' => 'Verfügbarkeit prüfen',
            ],

            'status' => [
                'published' => 'Veröffentlicht',
                'draft' => 'Entwurf',
                'archived' => 'Archiviert',
                'unknown' => 'Unbekannt',
            ],

            'sections' => [
                'description' => 'Beschreibung',
                'itinerary' => 'Route',
                'itinerary_fallback' => 'Die vollständige Route teilt unser Concierge mit.',
                'included' => 'Inklusive',
                'excluded' => 'Exklusive',
                'included_fallback' => 'Bootsunterkunft, Mahlzeiten, Crew und Dokumentation.',
                'excluded_fallback' => 'Flüge, persönliche Versicherung und persönliche Ausgaben.',
                'transportation' => 'Transport',
                'destinations' => 'Ziele',
                'destinations_fallback' => 'Highlights, die du während der Reise besuchst.',
                'view_on_maps' => 'In Maps ansehen',
            ],

            'availability' => [
                'title' => 'Verfügbarkeit',
                'empty' => 'Der Verfügbarkeitskalender ist für dieses Paket noch nicht eingerichtet. Kontaktiere unseren Concierge für Termine.',
                'calendar_days' => ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
                'slot_count' => '{1} :count Platz|[2,*] :count Plätze',
                'legend_available' => 'Verfügbar',
                'legend_closed' => 'Voll/Geschlossen',
            ],

            'actions' => [
                'ask_schedule' => 'Termine anfragen & buchen',
                'view_other_packages' => 'Andere Pakete ansehen',
            ],

            'faq' => [
                'title' => 'FAQ',
                'empty' => 'FAQ sind bald verfügbar.',
            ],

            'reviews' => [
                'title' => 'Bewertungen',
                'summary' => 'Bewertung :rating/5 • :count Bewertungen',
                'reviewer_fallback' => 'Reisender',
                'rating_label' => 'Bewertung:',
                'empty' => 'Noch keine Bewertungen.',
            ],

            'summary' => [
                'title' => 'Zusammenfassung',
                'duration' => 'Dauer',
                'capacity' => 'Kapazität',
                'category' => 'Kategorie',
                'operator' => 'Anbieter',
                'cta_consult' => 'Jetzt beraten lassen',
                'cta_availability' => 'Verfügbarkeit ansehen',
            ],
        ],
    ],

    'rental' => [
        'page' => [
            'title' => 'Mietwagen in Labuan Bajo | TriptoKomodo',
            'meta' => 'Mietwagen in Labuan Bajo: professioneller Fahrer, komfortable Fahrzeuge und flexible Routen für Flores und Umgebung. Schnelle Beratung per WhatsApp.',
            'keywords' => 'mietwagen labuan bajo, auto mieten flores, fahrer labuan bajo, transport flores',
        ],
        'hero' => [
            'tag' => 'Mietwagen',
            'title' => 'Mietwagen in Labuan Bajo',
            'desc' => 'Diese Seite ist bereit für Mietpakete (Fahrzeuge, Preise, Dauer, Fahrer). Bis dahin kontaktiere unser Team für schnelle Empfehlungen.',
        ],
        'cars' => [
            'title' => 'Verfügbare Fahrzeuge',
            'subtitle' => 'Wähle ein Fahrzeug passend zu Route und Gruppengröße.',
            'empty' => 'Aktuell sind keine Mietwagen verfügbar.',
            'from' => 'Ab',
            'per_day' => 'pro Tag',
            'see_detail' => 'Details ansehen',
        ],
        'cta' => [
            'title' => 'Schnelle Empfehlung nötig?',
            'desc' => 'Klicke auf Beratung, um Fahrzeugoptionen für deine Flores-Route zu erhalten.',
            'button' => 'Beratung',
        ],
    ],

    'blog' => [
        'page' => [
            'title' => 'Komodo Insider | TriptoKomodo',
            'meta' => 'Komodo Insider: Artikel, Routen und die besten Insights für Labuan Bajo, Komodo und Flores.',
            'keywords' => 'komodo insider, labuan bajo tipps, komodo itinerary, komodo reiseführer',
        ],
        'hero' => [
            'tag' => 'Komodo Insider',
            'title' => 'Blog & Insights',
            'desc' => 'Dieser Bereich enthält künftig Artikel (Tipps, Routen, Spots). Aktuell im Setup.',
        ],
        'card' => [
            'tag' => 'Komodo Insider',
            'title' => 'Komodo Insider Artikel',
            'desc' => 'Blog-Inhalte erscheinen hier, sobald der Admin Artikel hinzufügt.',
        ],
    ],

    'contact' => [
        'page' => [
            'keywords' => 'kontakt triptokomodo, buchung labuan bajo, komodo beratung, whatsapp labuan bajo',
        ],
    ],
];
