<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use JsonException;

class SearchIndexTreeService
{
    private mixed $root;
    public int $comparisons;

    public function __construct() {
        $this->root = null;
    }

    private function height($node)
    {
        return $node ? $node->height : 0;
    }

    private function balance($node)
    {
        return $node ? $this->height($node->left) - $this->height($node->right) : 0;
    }

    private function rotateRight($y)
    {
        $x = $y->left;
        $T2 = $x->right;

        $x->right = $y;
        $y->left = $T2;

        $y->height = max($this->height($y->left), $this->height($y->right)) + 1;
        $x->height = max($this->height($x->left), $this->height($x->right)) + 1;

        return $x;
    }

    private function rotateLeft($x)
    {
        $y = $x->right;
        $T2 = $y->left;

        $y->left = $x;
        $x->right = $T2;

        $x->height = max($this->height($x->left), $this->height($x->right)) + 1;
        $y->height = max($this->height($y->left), $this->height($y->right)) + 1;

        return $y;
    }

    private function insertNode($node, $key, $value)
    {
        if (!$node) {
            return new TreeNode($key, $value);
        }

        if ($key < $node->key) {
            $node->left = $this->insertNode($node->left, $key, $value);
        } elseif ($key > $node->key) {
            $node->right = $this->insertNode($node->right, $key, $value);
        } else {
            $node->value = $value;
        }

        $node->height = 1 + max($this->height($node->left), $this->height($node->right));

        $balance = $this->balance($node);

        if ($balance > 1 && $key < $node->left->key) {
            return $this->rotateRight($node);
        }

        if ($balance < -1 && $key > $node->right->key) {
            return $this->rotateLeft($node);
        }

        if ($balance > 1 && $key > $node->left->key) {
            $node->left = $this->rotateLeft($node->left);
            return $this->rotateRight($node);
        }

        if ($balance < -1 && $key < $node->right->key) {
            $node->right = $this->rotateRight($node->right);
            return $this->rotateLeft($node);
        }

        return $node;
    }

    public function insert($key, $value): void
    {
        $this->root = $this->insertNode($this->root, $key, $value);
    }

    public function search($key): array
    {
        $this->comparisons = 0;
        return $this->_search($this->root, $key);
    }

    private function _search($node, $key)
    {
        if ($node === null) {
            return ['value' => null, 'comparisons' => $this->comparisons];
        }

        $this->comparisons++;
        if ($key == $node->key) {
            return ['value' => $node->value, 'comparisons' => $this->comparisons];
        } elseif ($key < $node->key) {
            return $this->_search($node->left, $key);
        } else {
            return $this->_search($node->right, $key);
        }
    }

    public function saveToFile($filename): void
    {
        $data = $this->toArray($this->root);
        try {
            file_put_contents($filename, json_encode($data, JSON_THROW_ON_ERROR));
        } catch (JsonException $e) {
            Log::error($e->getMessage());
        }
    }

    public function toArray($node): array
    {
        if (!$node) {
            return [];
        }

        return [
            'key' => $node->key,
            'value' => $node->value,
            'left' => $this->toArray($node->left),
            'right' => $this->toArray($node->right)
        ];
    }
}
