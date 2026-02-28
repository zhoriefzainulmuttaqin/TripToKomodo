<?php

namespace App\Http\Controllers\Admin;

use App\Models\TourPackage;
use App\Models\TourPackageAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TourPackageAvailabilityController
{
    public function store(Request $request, TourPackage $tourPackage): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'is_available' => ['nullable', 'boolean'],
            'available_slots' => ['nullable', 'integer', 'min:0'],
            'price_idr_override' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $availability = TourPackageAvailability::withTrashed()->updateOrCreate(
            [
                'tour_package_id' => $tourPackage->id,
                'date' => $validated['date'],
            ],
            [
                'is_available' => (bool) ($validated['is_available'] ?? true),
                'available_slots' => $validated['available_slots'] ?? null,
                'price_idr_override' => $validated['price_idr_override'] ?? null,
                'note' => $validated['note'] ?? null,
            ]
        );

        if ($availability->trashed()) {
            $availability->restore();
        }

        return redirect()->route('admin.tour-packages.edit', $tourPackage)->with('status', 'Ketersediaan berhasil disimpan.');
    }

    public function destroy(TourPackage $tourPackage, TourPackageAvailability $availability): RedirectResponse
    {
        abort_unless($availability->tour_package_id === $tourPackage->id, 404);

        $availability->delete();

        return redirect()->route('admin.tour-packages.edit', $tourPackage)->with('status', 'Ketersediaan berhasil dihapus.');
    }
}
