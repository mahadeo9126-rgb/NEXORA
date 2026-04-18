<?php

namespace App\Services;

use App\Models\User;

class PlacementService
{
    /**
     * Find the matrix placement parent ID and position for a new user.
     *
     * @param User $sponsor
     * @param string $pref 'extreme_left', 'extreme_right', 'left', 'right'
     * @return array ['parent_id' => int, 'position' => string]
     */
    public function findPlacement(User $sponsor, string $pref): array
    {
        if ($pref === 'extreme_left') {
            return $this->findExtreme($sponsor, 'left');
        } elseif ($pref === 'extreme_right') {
            return $this->findExtreme($sponsor, 'right');
        } elseif ($pref === 'left') {
            return $this->findBalanced($sponsor, 'left');
        } elseif ($pref === 'right') {
            return $this->findBalanced($sponsor, 'right');
        }

        // Default fallback
        return $this->findExtreme($sponsor, 'left');
    }

    private function findExtreme(User $node, string $direction): array
    {
        $child = User::where('parent_id', $node->id)->where('position', $direction)->first();

        if (!$child) {
            return ['parent_id' => $node->id, 'position' => $direction];
        }

        return $this->findExtreme($child, $direction);
    }

    private function findBalanced(User $sponsor, string $direction): array
    {
        // For balanced left/right, start the BFS ONLY from the sponsor's immediate left/right child
        $rootChild = User::where('parent_id', $sponsor->id)->where('position', $direction)->first();

        // If the immediate directed child is empty, place them right there
        if (!$rootChild) {
            return ['parent_id' => $sponsor->id, 'position' => $direction];
        }

        // BFS Queue
        $queue = [$rootChild];

        while (!empty($queue)) {
            $current = array_shift($queue);

            // Check Left
            $leftChild = User::where('parent_id', $current->id)->where('position', 'left')->first();
            if (!$leftChild) {
                return ['parent_id' => $current->id, 'position' => 'left'];
            }
            array_push($queue, $leftChild);

            // Check Right
            $rightChild = User::where('parent_id', $current->id)->where('position', 'right')->first();
            if (!$rightChild) {
                return ['parent_id' => $current->id, 'position' => 'right'];
            }
            array_push($queue, $rightChild);
        }

        // Fallback (Should theoretically not be reached if tree is unbound)
        return ['parent_id' => $sponsor->id, 'position' => $direction];
    }
}
