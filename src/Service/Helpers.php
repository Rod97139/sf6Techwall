<?php

namespace App\Service;

class Helpers
{
    private $langue;

    public function __construct($langue) {
        $this->languer = $langue;
    }
    public function SayCc()
    {
        return 'cc';
    }
}
