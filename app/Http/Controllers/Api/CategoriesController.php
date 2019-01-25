<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Transformers\CategoryTransformer;

class CategoriesController extends Controller
{
    public function index()
    {
    	return $this->response->item(Category::all(), new CategoryTransformer());
    }
}
