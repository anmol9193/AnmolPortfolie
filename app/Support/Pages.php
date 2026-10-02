<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Throwable;

class Pages
{
    /** Settings keys of a page are stored as "page.<key>.<field>". */
    public const PREFIX = 'page.';

    protected static ?Collection $pages = null;

    /**
     * Every public page with the admin's choices merged over the defaults from config/site.php.
     *
     * @return Collection<string, array{key: string, path: string, label: string, title: string, nav: bool, sections: list<string>}>
     */
    public static function all(): Collection
    {
        if (static::$pages !== null) {
            return static::$pages;
        }

        try {
            $saved = Setting::where('key', 'like', static::PREFIX.'%')->pluck('value', 'key');
        } catch (Throwable) {
            // Settings table not migrated yet: use the defaults.
            $saved = collect();
        }

        $catalog = array_keys(config('site.sections'));

        return static::$pages = collect(config('site.pages'))->map(function (array $page, string $key) use ($saved, $catalog) {
            $get = fn (string $field) => $saved->get(static::PREFIX."$key.$field");

            $sections = $get('sections') === null
                ? $page['sections']
                : array_filter(explode(',', (string) $get('sections')));

            return [
                'key' => $key,
                'path' => $page['path'],
                'label' => $get('label') ?: $page['label'],
                'title' => $get('title') ?? $page['title'],
                'nav' => $get('nav') === null ? $page['nav'] : (bool) $get('nav'),
                // keep only known sections, always in catalog order
                'sections' => array_values(array_intersect($catalog, $sections)),
            ];
        });
    }

    /**
     * @return array{key: string, path: string, label: string, title: string, nav: bool, sections: list<string>}|null
     */
    public static function find(string $key): ?array
    {
        return static::all()->get($key);
    }

    public static function forget(): void
    {
        static::$pages = null;
    }
}
