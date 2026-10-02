<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\Pages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Show a public page: its sections come from the admin panel (Site pages).
     */
    public function show(string $page = 'home'): View
    {
        $current = Pages::find($page);

        abort_unless($current, 404);

        return view('page', ['current' => $current]);
    }

    public function index(): View
    {
        return view('admin.pages', ['pages' => Pages::all()]);
    }

    public function edit(string $page): View
    {
        $current = Pages::find($page);

        abort_unless($current, 404);

        return view('admin.page-form', ['current' => $current, 'sections' => config('site.sections')]);
    }

    public function update(Request $request, string $page): RedirectResponse
    {
        $current = Pages::find($page);

        abort_unless($current, 404);

        $data = $request->validate([
            'label' => ['required', 'string', 'max:40'],
            'title' => ['nullable', 'string', 'max:80'],
            'nav' => ['nullable', 'boolean'],
            'sections' => ['required', 'array', 'min:1'],
            'sections.*' => [Rule::in(array_keys(config('site.sections')))],
        ], ['sections.required' => 'Pick at least one section for this page.'], ['label' => 'Menu name', 'title' => 'Browser title']);

        $prefix = Pages::PREFIX.$page.'.';

        Setting::updateOrCreate(['key' => $prefix.'label'], ['value' => $data['label']]);
        Setting::updateOrCreate(['key' => $prefix.'title'], ['value' => (string) ($data['title'] ?? '')]);
        Setting::updateOrCreate(['key' => $prefix.'nav'], ['value' => $request->boolean('nav') ? '1' : '0']);
        Setting::updateOrCreate(['key' => $prefix.'sections'], ['value' => implode(',', $data['sections'])]);

        Pages::forget();

        return redirect()->route('admin.pages')->with('status', $data['label'].' page saved.');
    }
}
