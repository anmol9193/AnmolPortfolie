<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppearanceController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $hex = 'regex:/^#[0-9a-fA-F]{6}$/';

        $data = $request->validate([
            'mode' => ['required', Rule::in(config('theme.modes'))],
            'color' => ['required', Rule::in([...array_keys(config('theme.colors')), Theme::CUSTOM])],
            'accent' => ['required_if:color,'.Theme::CUSTOM, 'nullable', $hex],
            'accent2' => ['nullable', $hex],
            'background' => ['required', Rule::in(['default', ...array_keys(config('theme.backgrounds')), Theme::CUSTOM])],
            'bg' => ['required_if:background,custom', 'nullable', $hex],
        ], [], ['accent' => 'main color', 'accent2' => 'second color', 'bg' => 'background color']);

        $values = [
            'theme_mode' => $data['mode'],
            'theme_color' => $data['color'],
            'theme_accent' => $data['color'] === Theme::CUSTOM ? $data['accent'] : '',
            'theme_accent2' => $data['color'] === Theme::CUSTOM ? ($data['accent2'] ?? $data['accent']) : '',
            'theme_bg_choice' => $data['background'],
            'theme_bg' => $data['background'] === Theme::CUSTOM ? $data['bg'] : '',
        ];

        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Theme::forget();

        return back()->with('status', 'Appearance saved.');
    }

    public function updateLayout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'layout' => ['required', Rule::in(array_keys(config('theme.layouts')))],
        ]);

        Setting::updateOrCreate(['key' => 'theme_layout'], ['value' => $data['layout']]);

        Theme::forget();

        return back()->with('status', 'Theme applied: '.config("theme.layouts.{$data['layout']}.label").'.');
    }
}
