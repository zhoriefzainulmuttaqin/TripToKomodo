<?php

namespace App\Http\Controllers\Admin;

use App\Models\TourImage;
use App\Models\TourPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class TourPackageImageController
{
    public function destroy(TourPackage $tourPackage, TourImage $tourImage): RedirectResponse
    {
        abort_unless($tourImage->tour_package_id === $tourPackage->id, 404);

        // delete underlying file if stored locally
        $url = (string) ($tourImage->url ?? '');
        if (str_starts_with($url, '/storage/')) {
            $relative = ltrim(substr($url, strlen('/storage/')), '/');
            try {
                Storage::disk('public')->delete($relative);
            } catch (\Throwable) {
                // ignore
            }
        }

        $wasPrimary = (bool) $tourImage->is_primary;
        $tourImage->delete();

        if ($wasPrimary) {
            $next = $tourPackage->images()->orderBy('sort_order')->first();
            if ($next) {
                $next->is_primary = true;
                $next->save();
            }
        }

        return redirect()->route('admin.tour-packages.edit', $tourPackage)->with('status', 'Gambar berhasil dihapus.');
    }
}
