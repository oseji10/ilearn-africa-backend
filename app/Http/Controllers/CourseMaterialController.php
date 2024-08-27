<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseMaterial;

class CourseMaterialController extends Controller
{
    public function showMaterials(Request $request){
        $show_materials = CourseMaterial::where('course_id', $request->course_id)
        ->get();

        
        return response()->json([
        'message' => 'Materials retrieved succesfully',
        'course_materials' => $show_materials,
        ]);
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
        $validated['material_name'] = $request->material_name;
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
