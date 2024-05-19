<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use Illuminate\Support\Facades\Validator;


class CategoryConstroller extends Controller
{
    public function showCategory()
    {
        $category = Categories::count();
        if ($category == 0){
            return response()->json([
                'message' => 'Kategori tidak ada'
            ], 404);
        }
        return response()->json(
            Categories::All(), 200
        );
    }
    public function addCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors
            ], 422);
        }
        $credential = $validator->validated();
        $cek_category = Categories::where('name', $credential['name'])->first();
        if ($cek_category) {
            return response()->json([
                'message' => 'Kategori sudah ada'
            ]);
        }
        $category = Categories::create($credential);
        return response()->json([
            'message' => 'Kategori berhasil dibuat'
        ], 200);
    }
    public function updateCategory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors
            ], 422);
        }
        $category = Categories::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Kategori tidak ada'
            ], 422);
        }
        $credential = $validator->validated();
        $cek_category = Categories::where('name', $credential['name'])->first();
        if ($cek_category) {
            return response()->json([
                'message' => 'Kategori sudah ada'
            ], 422);
        }
        $category->update($credential);
        return response()->json([
            'message' => 'Kategori berhasil diubah'
        ], 200);
    }
    public function deleteCategory($id)
    {
        $category = Categories::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Kategori tidak ada'
            ], 422);
        }
        $category->delete();
        return response()->json([
            'message' => 'Kategori sudah dihapus'
        ], 200);
    }
    
}
