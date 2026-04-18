<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class GenealogyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Load up to 3 levels deep recursively using Eloquent
        $user->load('children.children.children');

        return view('genealogy', compact('user'));
    }

    // Drill down method
    public function drillDown(Request $request, $id)
    {
        $user = User::with('children.children.children')->findOrFail($id);

        // Ensure the current user has the right to view this subtree
        // A simple check might just allow anyone to view anyone below them,
        // but for this MVP, we will just return the view.

        return view('genealogy', compact('user'));
    }
}
