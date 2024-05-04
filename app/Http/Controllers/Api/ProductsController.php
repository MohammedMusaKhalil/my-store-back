<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{

    /**
     * @param name
     * @param category
     *
     * @return Array<Product>
     */
    public function GetProducts(Request $request): JsonResponse
    {
        // init request parameters
        $name = $request->name;
        $category = $request->category;

        $proudcts = Product::get();

        // filter by name
        if (!empty($name)) {
            $proudcts = $proudcts->where('name', $name);
        }

        // filter by category
        if (!empty($category)) {
            $proudcts = $proudcts->where('category', $category);
        }

        return response()->json([
            'message' => 'products has been retreived successfully',
            'data' => $proudcts,
        ], 200);
    }

    /**
     * @return List of Category
     *
     */

    public function  GetCategories(): JsonResponse
    {
        $categories = Category::all();

        return response()->json([
            'message' => 'Categroies has been retreived successfully',
            'data' => $categories,
        ], 200);
    }


    public function createCategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'desc' => [ 'nullable','string'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $categories = Category::create([
                'name' => $request->input('name'),
                'desc' => $request->input('desc'),
            ]);
            return  response()->json([
                "message" => "Category created Successfully",
                "data" => $categories
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        };
    }

    public function updateCategory(Request $request, $id)
{
    // Validate the incoming request data
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'desc' => 'nullable|string',
    ]);

    // If validation fails, return error response
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    // Find the category by ID
    $category = Category::find($id);

    // If category not found, return error response
    if (!$category) {
        return response()->json(['message' => 'Category not found'], 404);
    }

    // Update category data
    $category->name = $request->input('name');
    $category->desc = $request->input('desc', ''); // Use empty string as default value if desc is not provided
    $category->save();

    // Return success response
    return response()->json(['message' => 'Category updated successfully', 'category' => $category], 200);
}



    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required',
            'image' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        try {
            $product = Product::create([
                'name' => $request->input('name'),
                'category' => $request->input('category'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'image' => $request->input('image'),
            ]);

            return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }


    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 400);
            }
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete product'], 500);
        }
    }
    public function deleteCategory($id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        try {
            $category->delete();
            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete category'], 500);
        }

}

public function update(Request $request, $id)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric',
        'image' => 'required|string',
    ]);

    // Find the product by ID
    $product = Product::find($id);

    // If product not found, return error response
    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    // Update product data
    $product->name = $validatedData['name'];
    $product->description = $validatedData['description'];
    $product->price = $validatedData['price'];
    $product->image = $validatedData['image'];
    $product->save();

    // Return success response
    return response()->json(['message' => 'Product updated successfully', 'product' => $product], 200);
}
public function statistics()
    {   


        try {
            $data ['category'] = Category::count();
            $data ['product'] = Product::count();
            $data ['order'] = Order::count();
            $data ['user'] = User::count();
            return response()->json([ 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed'], 500);
        }
    }

}
