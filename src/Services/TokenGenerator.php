<?php
namespace App\Services;

class TokenGenerator
{
    public function generate(?int $length = 60):string
    {
        return substr(
            str_shuffle(str_repeat('azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN0123456789', 6)),
            0,
            $length
        );
    }
}