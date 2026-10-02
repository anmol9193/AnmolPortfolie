<?php

namespace App\Support;

use App\Models\PortfolioItem;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Throwable;

class Content
{
    /** Settings keys of page texts are stored as "content.<group>.<field>". */
    public const PREFIX = 'content.';

    protected static ?Collection $values = null;

    protected static ?Collection $items = null;

    /**
     * A single text/file of the site, e.g. Content::get('hero.first_name').
     * Falls back to the default from config/content.php until the admin saves one.
     */
    public static function get(string $key): string
    {
        static::$values ??= static::loadValues();

        if (static::$values->has($key)) {
            return (string) static::$values->get($key);
        }

        [$group, $field] = explode('.', $key, 2) + [null, null];

        return (string) (config("content.groups.$group.fields.$field.2") ?? '');
    }

    /**
     * All items of one collection (projects, experiences, ...), in admin order.
     *
     * @return Collection<int, PortfolioItem>
     */
    public static function items(string $type): Collection
    {
        static::$items ??= static::loadItems();

        return static::$items->get($type, collect());
    }

    /**
     * A "lines" field as a clean list.
     *
     * @return list<string>
     */
    public static function lines(string $key): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', static::get($key)))));
    }

    /**
     * A "number | label" lines field as rows of [number, suffix, label].
     *
     * @return list<array{0: string, 1: string, 2: string}>
     */
    public static function stats(string $key): array
    {
        return array_map(function (string $line) {
            [$value, $label] = array_map('trim', explode('|', $line, 2)) + ['', ''];
            preg_match('/^(\d+)(.*)$/', $value, $m);

            return [$m[1] ?? $value, trim($m[2] ?? ''), $label];
        }, static::lines($key));
    }

    /**
     * Escape a text, then turn **bold**, *accent* and new lines into markup.
     */
    public static function rich(string $text, string $accentClass = 'serif'): HtmlString
    {
        $html = e($text);
        $html = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.+?)\*/s', '<span class="'.$accentClass.'">$1</span>', $html);

        return new HtmlString(nl2br($html, false));
    }

    /**
     * Public URL of an uploaded or bundled file ("uploads/x.jpg", "assets/...", or a full link).
     */
    public static function url(?string $path): string
    {
        if (blank($path)) {
            return '';
        }

        return preg_match('#^(https?:)?//#', $path) ? $path : asset($path);
    }

    /**
     * The inside of an icon from config/icons.php.
     */
    public static function icon(?string $name, string $fallback = 'star'): HtmlString
    {
        return new HtmlString(config("icons.$name") ?? config("icons.$fallback"));
    }

    public static function forget(): void
    {
        static::$values = null;
        static::$items = null;
    }

    protected static function loadValues(): Collection
    {
        try {
            return Setting::where('key', 'like', static::PREFIX.'%')
                ->pluck('value', 'key')
                ->mapWithKeys(fn ($value, $key) => [substr($key, strlen(static::PREFIX)) => $value]);
        } catch (Throwable) {
            // Tables not migrated yet: use the defaults.
            return collect();
        }
    }

    protected static function loadItems(): Collection
    {
        try {
            return PortfolioItem::orderBy('position')->orderBy('id')->get()->groupBy('type');
        } catch (Throwable) {
            return collect();
        }
    }
}
