<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Return all categories as JSON (for API usage).
     */
    public function index()
    {
        return response()->json(Category::all());
    }
}
