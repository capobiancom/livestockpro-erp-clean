<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Illuminate\Support\Str;

abstract class BaseData extends Data
{
    protected static function keysToCamel(array $arr): array
    {
        $res = [];
        foreach ($arr as $key => $value) {
            $newKey = Str::camel($key);
            if (is_array($value)) {
                $value = self::keysToCamel($value);
            }
            $res[$newKey] = $value;
        }

        return $res;
    }

    protected static function keysToSnake(array $arr): array
    {
        $res = [];
        foreach ($arr as $key => $value) {
            $newKey = Str::snake($key);
            if (is_array($value)) {
                $value = self::keysToSnake($value);
            }
            $res[$newKey] = $value;
        }

        return $res;
    }

    /**
     * Construct DTO from validated (snake_case) input
     */
    public static function fromValidated(array $validated): static
    {
        return self::from(self::keysToCamel($validated));
    }

    /**
     * Return array with snake_case keys suitable for model mass assignment
     */
    public function toArray(): array
    {
        return self::keysToSnake(parent::toArray());
    }
}
