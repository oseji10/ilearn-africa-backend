<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documents;

class UploadDocumentController extends Controller
{
    public function uploadDocument(Request $request)
{
   $validated = $request->validate([
        'document' => 'required|file|mimes:png,jpg,jpeg,JPG,pdf,doc,docx|max:2048', // Adjust validation rules as needed
    ]);

    if ($request->file('document')) {
        $file = $request->file('document');
        $path = $file->store('documents', 'public'); // Store in the 'public/documents' directory

        $validated['file_path'] = $path;
        $validated['client_id'] = auth()->user()->client_id;

        // Save the file path or other related information to the database if needed
        $save = Documents::create($validated);

        

        return response()->json([
            'message' => 'File uploaded successfully',
            'path' => $path
        ], 200);
    }

    return response()->json(['message' => 'File not uploaded'], 400);
}

}
