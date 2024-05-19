<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Categories;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ProductController extends Controller
{
    public function showProduct()
    {
        $product = Products::count();
        if ($product == 0){
            return response()->json([
                'message' => 'Produk tidak ada'
            ], 404);
        }
        return response()->json(
            Products::All(), 200
        );
    }
    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'expired_at' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);
        $jwt = $request->bearerToken();
        $decode = JWT::decode($jwt, new Key(env('JWT_SECRET_KEY'), 'HS256'));
        $email = $decode->email;
        $cek_category = Categories::where('name', $request->category_id)->first();
        if (!$cek_category) {
            return response()->json([
                'message' => 'Kategori tidak ada'
            ]);
        }
        $id_category = Categories::where('name', $request->category_id)->value('id');
        $validator->setData(array_merge($validator->getData(), ['category_id'=>$id_category]));
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 422);
        }
        $imagePath = $request->file('image')->store('images','public');
        $product = new Products($validator->validated());
        $product->image = $imagePath;
        $product->modified_by = $email;
        $product->save();
        return response()->json([
            'message' => 'Produk berhasil dibuat'
        ], 200);
    }
    public function updateProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'expired_at' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);
        $jwt = $request->bearerToken();
        $decode = JWT::decode($jwt, new Key(env('JWT_SECRET_KEY'), 'HS256'));
        $email = $decode->email;
        $cek_category = Categories::where('name', $request->category_id)->first();
        if (!$cek_category) {
            return response()->json([
                'message' => 'Kategori tidak ada'
            ]);
        }
        $id_category = Categories::where('name', $request->category_id)->value('id');
        $validator->setData(array_merge($validator->getData(), ['category_id'=>$id_category]));
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 422);
        }
        $imagePath = $request->file('image')->store('images','public');
        $product = Products::find($id);
        if ($product){
            $product->update($validator->validated());
            $product->image = $imagePath;
        $product->modified_by = $email;
        $product->save();
        }
        return response()->json([
            'message' => 'Produk berhasil diubah'
        ], 200);
    }
    public function deleteProduct($id)
    {
        $product = Products::find($id);
        if ($product) {
            $product->delete();
            return response()->json([
                'message' => 'Produk berhasil terhapus'
            ], 200);
        }
        return response()->json([
            'message' => 'Produk tidak tersedia'
        ], 404);
    }
}