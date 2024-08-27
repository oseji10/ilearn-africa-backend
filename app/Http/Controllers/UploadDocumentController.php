<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documents;
use App\Models\CourseMaterial;

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

public function uploadCourseMaterial(Request $request)
{
   $validated = $request->validate([
        // 'document' => 'required|file|mimes:png,jpg,jpeg,JPG,pdf,doc,docx|max:5048', // Adjust validation rules as needed
        // 'material_type' => 'string',
        // 'course_id' => 'string',
        // 'material_link' => 'string',
    ]);

    if ($request->file('document')) {
        $file = $request->file('document');
        $path = $file->store('documents', 'public'); // Store in the 'public/documents' directory

        $validated['material_link'] = $path;
        $validated['course_id'] = $request->course_id2;
        $validated['course_material'] = $request->course_material;
        $validated['material_type'] = $request->material_type;
        // Save the file path or other related information to the database if needed
        $save = CourseMaterial::create($validated);

        

        return response()->json([
            'message' => 'File uploaded successfully',
            'path' => $path
        ], 200);
    }

    return response()->json(['message' => 'File not uploaded'], 400);
}

}
