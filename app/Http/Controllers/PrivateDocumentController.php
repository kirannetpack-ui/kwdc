<?php

namespace App\Http\Controllers;

use App\Models\SecurityAgency;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PrivateDocumentController extends Controller
{
    public function show(Request $request, string $path)
    {
        abort_if(str_contains($path, '..') || str_starts_with($path, '/'), Response::HTTP_BAD_REQUEST);

        $user = $request->user();
        abort_unless($user && $this->canAccess($user, $path), Response::HTTP_FORBIDDEN);
        abort_unless(Storage::disk('private_uploads')->exists($path), Response::HTTP_NOT_FOUND);

        return Storage::disk('private_uploads')->response($path);
    }

    private function canAccess($user, string $path): bool
    {
        if (($user->role ?? null) === 'admin') {
            return $this->documentExistsInRecords($path);
        }

        $ownsWarehouseDocument = Warehouse::where('user_id', $user->id)
            ->where(function ($query) use ($path) {
                $query->where('ownership_document', $path)
                    ->orWhere('tax_document', $path)
                    ->orWhere('fire_safety_document', $path)
                    ->orWhere('building_approval_document', $path);
            })
            ->exists();

        if ($ownsWarehouseDocument) {
            return true;
        }

        return SecurityAgency::where('user_id', $user->id)
            ->where(function ($query) use ($path) {
                $query->where('registration_certificate_path', $path)
                    ->orWhere('license_certificate_path', $path)
                    ->orWhere('pan_vat_certificate_path', $path);
            })
            ->exists();
    }

    private function documentExistsInRecords(string $path): bool
    {
        return Warehouse::where(function ($query) use ($path) {
                $query->where('ownership_document', $path)
                    ->orWhere('tax_document', $path)
                    ->orWhere('fire_safety_document', $path)
                    ->orWhere('building_approval_document', $path);
            })
            ->exists()
            || SecurityAgency::where(function ($query) use ($path) {
                $query->where('registration_certificate_path', $path)
                    ->orWhere('license_certificate_path', $path)
                    ->orWhere('pan_vat_certificate_path', $path);
            })
            ->exists();
    }
}
