<?php

namespace App\Controllers;

class JWT
{
    public function __construct(private string $key, private readonly array $header = ['typ' => 'JWT', 'alg' => 'HS256'])
    {
    }

    private function base64URLEncode(string $text): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    public function encode(array $payload): string
    {
        $header = json_encode($this->header);
        $header = $this->base64URLEncode($header);
        $payload = json_encode($payload);
        $payload = $this->base64URLEncode($payload);
        $data = $header . "." . $payload;

        $signature = hash_hmac("sha256", $data, $this->key, true);
        $signature = $this->base64URLEncode($signature);
        return $data . "." . $signature;
    }
}