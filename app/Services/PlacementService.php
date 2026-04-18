<?php

namespace App\Services;

use App\Models\User;
use Exception;

class PlacementService
{
    /**
     * Find the matrix placement parent ID and position for a new user.
     *
     * @param User $sponsor
     * @param string $pref 'extreme_left', 'extreme_right', 'left', 'right'
     * @return array ['parent_id' => int, 'position' => string]
     * @throws Exception
     */
    public function findPlacement(User $sponsor, string $pref): array
    {
        if ($pref === 'extreme_left') {
            return $this->findExtreme($sponsor, 'left', 1);
        } elseif ($pref === 'extreme_right') {
            return $this->findExtreme($sponsor, 'right', 1);
        } elseif ($pref === 'left') {
            return $this->findBalanced($sponsor, 'left');
        } elseif ($pref === 'right') {
            return $this->findBalanced($sponsor, 'right');
        }

        // Default fallback
        return $this->findExtreme($sponsor, 'left', 1);
    }

    private function findExtreme(User $node, string $direction, int $depth): array
    {
        if ($depth > 20) {
            throw new Exception("Matrix depth limit of 20 reached");
        }

        $child = User::where('parent_id', $node->id)->where('position', $direction)->first();

        if (!$child) {
            return ['parent_id' => $node->id, 'position' => $direction];
        }

        return $this->findExtreme($child, $direction, $depth + 1);
    }

    private function findBalanced(User $sponsor, string $direction): array
    {
        // For balanced left/right, start the BFS ONLY from the sponsor's immediate left/right child
        $rootChild = User::where('parent_id', $sponsor->id)->where('position', $direction)->first();

        // If the immediate directed child is empty, place them right there
        if (!$rootChild) {
            return ['parent_id' => $sponsor->id, 'position' => $direction];
        }

        // BFS Queue - stores elements as array ['user' => User, 'depth' => int]
        // Since $rootChild is on level 1, depth starts at 1
        $queue = [['user' => $rootChild, 'depth' => 1]];

        while (!empty($queue)) {
            $item = array_shift($queue);
            $current = $item['user'];
            $depth = $item['depth'];

            if ($depth >= 20) {
                // If the children of this node would be at depth 21, skip them,
                // but we should probably throw an error if the entire tree is full up to 20.
                continue;
            }

            // Fetch BOTH children in a single query to prevent N+1 queries
            $children = User::where('parent_id', $current->id)->get()->keyBy('position');

            // Check Left
            if (!$children->has('left')) {
                return ['parent_id' => $current->id, 'position' => 'left'];
            }
            array_push($queue, ['user' => $children->get('left'), 'depth' => $depth + 1]);

            // Check Right
            if (!$children->has('right')) {
                return ['parent_id' => $current->id, 'position' => 'right'];
            }
            array_push($queue, ['user' => $children->get('right'), 'depth' => $depth + 1]);
        }

        // If the queue empties without finding a spot, the matrix is full
        throw new Exception("Matrix depth limit of 20 reached");
    }
}
