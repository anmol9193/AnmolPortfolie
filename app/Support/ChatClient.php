<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class ChatClient
{
    public function request(ChatSettings $settings): PendingRequest
    {
        $base = rtrim(config('chat.base_url'), '/');
        if (! in_array($base, ['https://api.scalemax.pro/v1', 'https://api.scalemax.pro/token/v1'], true)) {
            throw new InvalidArgumentException('Unsupported chat API endpoint.');
        }
        $bundle = config('chat.ca_bundle');

        return Http::baseUrl($base)->withToken($settings->apiKey())->acceptJson()
            ->connectTimeout(12)->timeout(25)
            ->withOptions(['allow_redirects' => false, 'verify' => is_string($bundle) && is_file($bundle) ? $bundle : true]);
    }
}
