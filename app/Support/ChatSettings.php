<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;

class ChatSettings
{
    public const KEY = 'chat.api_key';

    public function apiKey(): string
    {
        $encrypted = Setting::where('key', self::KEY)->value('value');

        return $encrypted === null ? (string) config('chat.key', '') : Crypt::decryptString($encrypted);
    }
}
