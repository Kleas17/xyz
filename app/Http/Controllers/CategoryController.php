<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Track;

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories disponibles.
     */
    public function index()
    {
        $categories = Category::all();

        return view('app.categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Affiche les contributions associées à une catégorie spécifique avec pagination.
     */
    public function show(Category $category)
    {
        $tracks = Track::where('category_id', $category->id)
            ->with(['user', 'category', 'week'])
            ->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->paginate(10); // Nombre d'éléments par page

        return view('app.categories.show', [
            'category' => $category,
            'tracks' => $tracks,
        ]);
    }
}