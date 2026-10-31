<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\ChatClient;
use App\Support\ChatSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use Throwable;

class ChatSettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.chat', [
            'hasSavedKey' => Setting::where('key', ChatSettings::KEY)->exists(),
            'hasEnvironmentKey' => filled(config('chat.key')),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'api_key' => ['required', 'string', 'max:512', 'regex:/\A[A-Za-z0-9_\-]+\z/'],
        ], ['api_key.regex' => 'Enter only the API key, without spaces or a prefix.']);

        Setting::updateOrCreate(['key' => ChatSettings::KEY], ['value' => Crypt::encryptString($data['api_key'])]);

        return redirect()->route('admin.chat')->with('status', 'ScaleMax API key saved. Use Test connection to verify it.');
    }

    public function test(ChatSettings $settings, ChatClient $client): RedirectResponse
    {
        try {
            $key = $settings->apiKey();
            if (blank($key)) {
                return back()->withErrors(['connection' => 'Save a ScaleMax API key first.']);
            }
            $response = $client->request($settings)->get('/models');
            if ($response->successful()) {
                if (! collect($response->json('data', []))->pluck('id')->contains(config('chat.model'))) {
                    return back()->withErrors(['connection' => 'Key accepted, but the configured AI_MODEL is not in your available model catalog.']);
                }

                return back()->with('status', 'Connected to ScaleMax. Your key and model are accessible.');
            }
            $message = $response->json('error.code') === 'wrong_billing_surface'
                ? 'This key needs a different billing endpoint. Use the token or API credit base URL that matches your ScaleMax plan.'
                : (in_array($response->status(), [401, 403], true)
                ? 'ScaleMax did not accept this key or its access scope. Check your ScaleMax dashboard.'
                : match ($response->status()) {
                    404 => 'The configured model is not available for this key. Check AI_MODEL on the server.',
                    429 => 'ScaleMax quota exceeded. Check your plan balance and limits.',
                    default => 'ScaleMax could not validate this connection. Check the key and try again.',
                });
        } catch (Throwable) {
            $message = 'Could not connect to ScaleMax. Check the server internet connection and CA certificate configuration.';
        }

        return back()->withErrors(['connection' => $message]);
    }

    public function destroy(): RedirectResponse
    {
        Setting::where('key', ChatSettings::KEY)->delete();

        return redirect()->route('admin.chat')->with('status', 'Saved API key removed. The chatbot will use the environment key if configured.');
    }
}
