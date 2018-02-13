<?php
declare(strict_types=1);

namespace App\Utils;


class Sanitizer
{
    /**
     * @param string $query
     * @return string
     */
    public static function sanitizeSearchQuery(string $query): string
    {
        return preg_replace('/[^[:alnum:] ]/', '', trim(preg_replace('/[[:space:]]+/', ' ', $query)));
    }
}