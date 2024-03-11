<?php
class JWTHandler
{

    public static function encodeJWT($data, $secret)
    {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode($data));
        $signature = hash_hmac('sha256', "$header.$payload", $secret, true);
        $encoded_signature = base64_encode($signature);

        return "$header.$payload.$encoded_signature";
    }

    public static function decodeJWT($token, $secret)
    {
        list($header, $payload, $encoded_signature) = explode('.', $token);

        $decoded_signature = base64_decode($encoded_signature);
        $calculated_signature = hash_hmac('sha256', "$header.$payload", $secret, true);

        if (hash_equals($decoded_signature, $calculated_signature)) {
            return json_decode(base64_decode($payload), true);
        }

        return null;
    }
}

?>