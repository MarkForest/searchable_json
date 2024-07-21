<?php

namespace App\Services;

class TreeNode
{
    public $key;
    public $value;
    public $height;
    public $left;
    public $right;

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
        $this->height = 1;
        $this->left = null;
        $this->right = null;
    }
}
