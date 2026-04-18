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
        $targetUser = User::with('children.children.children')->findOrFail($id);

        // Verify Downline Privacy
        if (!$this->isDescendant($request->user(), $targetUser)) {
            abort(403, 'Unauthorized access. You can only view your own downline.');
        }

        $user = $targetUser;
        return view('genealogy', compact('user'));
    }

    /**
     * Check if the target user is within the authenticated user's downline tree.
     */
    private function isDescendant(User $authUser, User $targetUser): bool
    {
        if ($authUser->id === $targetUser->id) {
            return true;
        }

        $currentSearchNode = $targetUser;

        while ($currentSearchNode && $currentSearchNode->parent_id) {
            if ($currentSearchNode->parent_id === $authUser->id) {
                return true;
            }
            $currentSearchNode = User::find($currentSearchNode->parent_id);
        }

        return false;
    }
}
