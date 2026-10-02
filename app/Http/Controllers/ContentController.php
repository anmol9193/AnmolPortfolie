<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\Content;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    /**
     * Edit the single texts/files of one content group (hero, about, contact, ...).
     */
    public function edit(string $group = 'site'): View
    {
        abort_unless(config("content.groups.$group"), 404);

        return view('admin.content', [
            'group' => $group,
            'groups' => config('content.groups'),
            'fields' => config("content.groups.$group.fields"),
        ]);
    }

    public function update(Request $request, string $group): RedirectResponse
    {
        $fields = config("content.groups.$group.fields");

        abort_unless($fields, 404);

        $rules = [];

        foreach ($fields as $key => [$label, $type]) {
            $rules[$key] = match ($type) {
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                'favicon' => ['nullable', 'file', 'mimes:png,ico,jpg,jpeg,webp', 'max:1024'],
                'file' => ['nullable', 'file', 'mimes:pdf', 'max:8192'],
                'text' => ['nullable', 'string', 'max:255'],
                default => ['nullable', 'string', 'max:5000'],
            };
        }

        $data = $request->validate($rules, [], collect($fields)->map(fn ($field) => $field[0])->all());

        foreach ($fields as $key => [$label, $type]) {
            $settingKey = Content::PREFIX."$group.$key";

            if (in_array($type, ['image', 'file', 'favicon'], true)) {
                if ($type === 'favicon' && $request->boolean("remove_$key") && ! $request->hasFile($key)) {
                    Uploads::delete(Content::get("$group.$key"));
                    Setting::updateOrCreate(['key' => $settingKey], ['value' => '']);
                }

                if ($request->hasFile($key)) {
                    Uploads::delete(Content::get("$group.$key"));
                    Setting::updateOrCreate(['key' => $settingKey], ['value' => Uploads::store($request->file($key), "$group-$key")]);
                }

                continue;
            }

            Setting::updateOrCreate(['key' => $settingKey], ['value' => (string) ($data[$key] ?? '')]);
        }

        Content::forget();

        return back()->with('status', config("content.groups.$group.label").' saved.');
    }
}
