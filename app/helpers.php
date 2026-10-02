<?php

use App\Models\PortfolioItem;
use App\Support\Content;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

if (! function_exists('content')) {
    /**
     * A single editable text/file of the site, e.g. content('hero.first_name').
     */
    function content(string $key): string
    {
        return Content::get($key);
    }
}

if (! function_exists('rich')) {
    /**
     * Escaped text with **bold**, *accent* and line breaks turned into markup.
     */
    function rich(string $text, string $accentClass = 'serif'): HtmlString
    {
        return Content::rich($text, $accentClass);
    }
}

if (! function_exists('items')) {
    /**
     * All items of one content collection, e.g. items('projects').
     *
     * @return Collection<int, PortfolioItem>
     */
    function items(string $type): Collection
    {
        return Content::items($type);
    }
}

if (! function_exists('media')) {
    /**
     * Public URL of an uploaded or bundled file.
     */
    function media(?string $path): string
    {
        return Content::url($path);
    }
}

if (! function_exists('icon')) {
    /**
     * The inside of an icon from config/icons.php.
     */
    function icon(?string $name, string $fallback = 'star'): HtmlString
    {
        return Content::icon($name, $fallback);
    }
}
