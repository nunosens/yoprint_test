<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index');
    }

public function getProducts(Request $request)
{
    $search = $request->get('search');
    $sortField = $request->get('sort_field', 'id');
    $sortDirection = $request->get('sort_direction', 'desc');

    $products = Product::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('unique_key', 'like', "%{$search}%")
                  ->orWhere('product_title', 'like', "%{$search}%")
                  ->orWhere('product_description', 'like', "%{$search}%")
                  ->orWhere('style_number', 'like', "%{$search}%")
                  ->orWhere('sanmar_mainframe_color', 'like', "%{$search}%")
                  ->orWhere('color_name', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%");
            });
        })
        ->orderBy($sortField, $sortDirection)
        ->paginate(20);

    return response()->json([
        'products' => $products->items(),
        'current_page' => $products->currentPage(),
        'last_page' => $products->lastPage(),
        'total' => $products->total(),
        'has_more' => $products->hasMorePages(),
    ]);
}
}