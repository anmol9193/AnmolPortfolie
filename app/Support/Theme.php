<?php

namespace App\Support;

use App\Models\Setting;
use Throwable;

class Theme
{
    /** Value of "theme_color" when the accent comes from the color pickers instead of a preset. */
    public const CUSTOM = 'custom';

    protected static ?array $current = null;

    /**
     * The site-wide appearance chosen in the admin panel.
     *
     * "mode" is the mode actually used: with a custom background it follows how bright that
     * background is, so text always stays readable. "saved_mode" is what the admin picked.
     *
     * @return array<string, mixed>
     */
    public static function current(): array
    {
        if (static::$current !== null) {
            return static::$current;
        }

        try {
            $saved = Setting::where('key', 'like', 'theme\_%')->pluck('value', 'key');
        } catch (Throwable) {
            // Settings table not migrated yet: fall back to the defaults.
            $saved = collect();
        }

        $savedMode = $saved->get('theme_mode');

        if (! in_array($savedMode, config('theme.modes'), true)) {
            $savedMode = config('theme.default_mode');
        }

        // background: "default", one of config('theme.backgrounds'), or "custom" (color picker)
        $bgChoice = $saved->get('theme_bg_choice') ?: (static::hex($saved->get('theme_bg')) ? static::CUSTOM : 'default');
        $bg = match (true) {
            $bgChoice === static::CUSTOM => static::hex($saved->get('theme_bg')),
            array_key_exists($bgChoice, config('theme.backgrounds')) => config("theme.backgrounds.$bgChoice.color"),
            default => null,
        };

        if (! $bg) {
            $bgChoice = 'default';
        }
        $mode = $bg ? (static::luminance($bg) < 0.4 ? 'dark' : 'light') : $savedMode;

        $color = $saved->get('theme_color');
        $customAccent = static::hex($saved->get('theme_accent'));

        if ($color === static::CUSTOM && $customAccent) {
            $accent = $customAccent;
            $accent2 = static::hex($saved->get('theme_accent2')) ?? $customAccent;
            // dark text on bright accents, white text on deep ones
            $ink = static::luminance($accent) > 0.55 ? '#16110d' : '#ffffff';
        } else {
            if (! array_key_exists((string) $color, config('theme.colors'))) {
                $color = config('theme.default_color');
            }

            [$accent, $accent2, $ink] = config("theme.colors.$color.$mode");
        }

        $layout = $saved->get('theme_layout');

        if (! array_key_exists((string) $layout, config('theme.layouts'))) {
            $layout = config('theme.default_layout');
        }

        [$r, $g, $b] = sscanf($accent, '#%02x%02x%02x');

        return static::$current = [
            'mode' => $mode,
            'saved_mode' => $savedMode,
            'color' => $color,
            'color_label' => $color === static::CUSTOM ? 'Custom' : config("theme.colors.$color.label"),
            'layout' => $layout,
            'background' => config("theme.layouts.$layout.background"),
            'radius' => config("theme.layouts.$layout.radius"),
            'order' => config("theme.layouts.$layout.order"),
            'accent' => $accent,
            'accent2' => $accent2,
            'ink' => $ink,
            'soft' => "rgba($r,$g,$b,".($mode === 'dark' ? '.12' : '.10').')',
            'bg' => $bg,
            'bg_choice' => $bgChoice,
        ];
    }

    public static function forget(): void
    {
        static::$current = null;
    }

    /**
     * A clean "#rrggbb" value, or null when the input is not one.
     */
    public static function hex(?string $value): ?string
    {
        return is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? strtolower($value) : null;
    }

    /**
     * Perceived brightness of a color, from 0 (black) to 1 (white).
     */
    public static function luminance(string $hex): float
    {
        [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    }
}
