<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $request->validate([
            'license' => 'nullable|image|max:5120',
            'insurance' => 'nullable|image|max:5120',
        ]);

        $document = DriverDocument::firstOrCreate(['user_id' => $request->user()->id]);

        if ($request->hasFile('license')) {
            $path = $request->file('license')->store('documents', 'public');
            $document->license_path = $path;
        }

        if ($request->hasFile('insurance')) {
            $path = $request->file('insurance')->store('documents', 'public');
            $document->insurance_path = $path;
        }

        // Reset status to pending if they uploaded a new document
        if ($request->hasFile('license') || $request->hasFile('insurance')) {
            $document->status = 'pending';
        }

        $document->save();

        return response()->json(['message' => 'Documents uploaded successfully', 'document' => $document]);
    }
}
