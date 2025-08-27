<?php

namespace App\Casts;

use App\ValueObjects\HourlyRate;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class HourlyRateCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?HourlyRate
    {
        if (is_null($value)) {
            return null;
        }

        $data = is_string($value) ? json_decode($value, true) : $value;
        
        if (!is_array($data) || !isset($data['amount'], $data['currency'])) {
            return null;
        }

        return HourlyRate::make((float) $data['amount'], $data['currency']);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof HourlyRate) {
            return json_encode($value->toArray());
        }

        return $value;
    }
}
