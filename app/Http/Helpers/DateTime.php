<?php

namespace App\Http\Helpers;

class DateTime
{
    public static function formatDateTime(string $dateTime): string
    {
        return (new \DateTime($dateTime))->format('Y-m-d H:i:s');
    }
}
