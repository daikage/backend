<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Models\DriverDocument;

class DocumentController extends Controller
{
    public function show(Request $request)
    {
        $document = DriverDocument::where('user_id', $request->user()->id)->first();
        return response()->json(['document' => $document]);
    }

    public function upload(Request $request)
    {
        if (!$request->hasAny(['license', 'insurance', 'vehicle_license', 'road_worthiness', 'hackney_permit'])) {
            return response()->json(['error' => 'Please upload at least one document.'], 400);
        }

        $request->validate([
            'license' => 'nullable|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'insurance' => 'nullable|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'vehicle_license' => 'nullable|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'road_worthiness' => 'nullable|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'hackney_permit' => 'nullable|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        try {
            $document = DriverDocument::firstOrCreate(['user_id' => $request->user()->id]);

            // Use the configured disk – defaults to 'public' locally,
            // set DOCUMENT_DISK=s3 on Laravel Cloud for persistent storage.
            $disk = env('DOCUMENT_DISK', 'public');

            $filesUpdated = false;

            if ($request->hasFile('license')) {
                $document->license_path = $request->file('license')->store('documents', $disk);
                $filesUpdated = true;
            }
            if ($request->hasFile('insurance')) {
                $document->insurance_path = $request->file('insurance')->store('documents', $disk);
                $filesUpdated = true;
            }
            if ($request->hasFile('vehicle_license')) {
                $document->vehicle_license_path = $request->file('vehicle_license')->store('documents', $disk);
                $filesUpdated = true;
            }
            if ($request->hasFile('road_worthiness')) {
                $document->road_worthiness_path = $request->file('road_worthiness')->store('documents', $disk);
                $filesUpdated = true;
            }
            if ($request->hasFile('hackney_permit')) {
                $document->hackney_permit_path = $request->file('hackney_permit')->store('documents', $disk);
                $filesUpdated = true;
            }

            if ($filesUpdated) {
                $document->status = 'pending';
            }

            $document->save();

            return response()->json(['message' => 'Documents uploaded successfully', 'document' => $document]);
        } catch (\Exception $e) {
            Log::error('Document upload failed', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to upload document. Please try again later.',
                'detail' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
