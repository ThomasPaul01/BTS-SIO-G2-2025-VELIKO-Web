<?php

namespace App\Services;

class Token
{
    public function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}