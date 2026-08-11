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
            'license' => 'nullable|file|max:5120',
            'insurance' => 'nullable|file|max:5120',
            'vehicle_license' => 'nullable|file|max:5120',
            'road_worthiness' => 'nullable|file|max:5120',
            'hackney_permit' => 'nullable|file|max:5120',
        ]);

        try {
            $document = DriverDocument::firstOrCreate(['user_id' => $request->user()->id]);

            $filesUpdated = false;
            $fields = ['license', 'insurance', 'vehicle_license', 'road_worthiness', 'hackney_permit'];

            foreach ($fields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = $field . '_' . $request->user()->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                    // Store to the documents directory within the default disk
                    $path = $file->storeAs('documents', $filename);

                    if ($path === false) {
                        throw new \RuntimeException("Failed to store file for field: {$field}");
                    }

                    $document->{$field . '_path'} = $path;
                    $filesUpdated = true;
                }
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
                'file_class' => $e::class,
                'default_disk' => config('filesystems.default'),
                'storage_path' => storage_path('app'),
            ]);

            return response()->json([
                'error' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
