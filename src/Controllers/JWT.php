<?php

namespace App\Controllers;
//const JWT_SECRET = 'cloud-storage-secret-32-char-key';
//const JWT_EXPIRES_IN = 90;

use DateTimeImmutable;

class JWT
{
//    public function __construct(private readonly string $key = JWT_SECRET, private readonly array $header = ['typ' => 'JWT', 'alg' => 'HS256'])
//    {
//    }

    private static function base64URLEncode(string $text): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    private static function base64URLDecode(string $text): string
    {
        return str_replace(['-', '_', ''],['+', '/', '='], base64_decode($text));
    }

// Encode Header to Base64Url String
// Encode Payload to Base64Url String
// Create Signature Hash
// Encode Signature to Base64Url String
// Create JWT
    private static function encode(array $payload): string
    {
        $header = json_encode(HEADER);
        $header = self::base64URLEncode($header);

        $payload = json_encode($payload);
        $payload = self::base64URLEncode($payload);
        $data = $header . "." . $payload;

        $signature = hash_hmac("sha256", $data,  JWT_SECRET, true);
        $signature = self::base64URLEncode($signature);

        return $data . "." . $signature;
    }

    private static function decode(string $token): array
    {
        list($header, $payload, $signature) = explode('.', $token);

        $header = self::base64URLdecode($header);
        $header = json_decode($header, true);

        $payload = self::base64URLdecode($payload);
        $payload = json_decode($payload, true);

        $signature = self::base64URLdecode($signature);
        return ['header' => $header, 'payload' => $payload, 'signature'=> $signature];

    }

    public static function createRefreshToken(string $userID, bool $isAdmin): string
    {
        $issuedAt   = new DateTimeImmutable();

        $payload = [
            'iss' => 'cloudstorage',
            'sub' => $userID,
            'iat' => $issuedAt->getTimestamp(),
            'exp' => $issuedAt->modify('+60 days')->getTimestamp(),
            'type' => 'refresh',
        ];

        return self::encode($payload);
    }

    public static function createAccessToken(string $userID, bool $isAdmin): string
    {
        $issuedAt   = new DateTimeImmutable();

        $payload = [
            'iss' => 'cloudstorage',
            'sub' => $userID,
            'iat' => $issuedAt->getTimestamp(),
            'exp' => $issuedAt->modify('+15 minutes')->getTimestamp(),
            'type' => 'access',
            'admin' => $isAdmin,
        ];

        return self::encode($payload);
    }

    public static function checkTokenExpired(string $token): bool
    {

        $data = self::decode($token);

        if ($data['payload']->iat >= (time()-5)) {
            return true;
        }

        return false;
    }

    public static function verifyToken(string $token, string $type)
    {
        $data = self::decode($token);
        if ($data['header'] !== HEADER) {
            dd('header is not ok');
        }

        $validJWT = self::encode($data['payload']);

        if ($validJWT !== $token) {
            dd('invalid token');
        }

        if ($data['payload']['type'] !== $type)
        {
            dd('Wrong token type');
        }

        if ($data['payload']['type'] != 'refresh' & $data['payload']['exp'] >= (time()-5)) {
           return false;
        }

        if ($data['payload']['type'] == 'refresh' & $data['payload']['exp'] <= (time()-5))
        {
            dd('both tokens are invalid');
            return false;
        }


        return $data;


    }

}