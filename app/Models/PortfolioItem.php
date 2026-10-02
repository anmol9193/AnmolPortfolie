<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'data', 'position'])]
class PortfolioItem extends Model
{
    protected $table = 'portfolie';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    /**
     * Read one field of the item, e.g. $item->field('title').
     */
    public function field(string $key, mixed $default = ''): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * A comma separated field as a clean list.
     *
     * @return list<string>
     */
    public function list(string $key): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->field($key)))));
    }
}
