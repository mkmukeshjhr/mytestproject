<?php

namespace App\Services;
use GuzzleHttp\Client;

class FCMService
{
     public $serviceAccountKey = [
            "type"=>"service_account",
            "project_id"=> "astro-live",
            "private_key_id"=> "6d32cfb0a169e620e53bb8hj344776d519cea46636",
            "private_key"=> "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgoIBAQDJL3hDVgCjSOk/\ndsuAuMw1ZecakIdiho3cuUFLnSZMEEkQeAWolHfb+aBPT6MtXWbmXOcxsJmIIn\nDHimIl2NA7aSNPmmP35Iqx35lSTkEFe14Vv6jQyg826JaCYW5B9Zv2/iycbrExnc\n7GxiXtX4oOUwAHcpD7SeMKMPGAMUcMRedy7nh2zP/QxBBlNz/R/4DVGNEgFRtqhh\neNocjsffh6oHC8/BAI8a+rYZe0ezTO4EyFUHy8C/xZxe1XMlCZWnmbQE5i8zQ5/s\nJ5zl5rW+F7Mmuqt+9hUx0VtsM4xskBK0srl1E1XmWRmz8/6pmYx8zu4nkm2qaAVE\nJcPdi8ZLAgMBAAECggEASrIlwDZzuSxOcaI9L3ctVpwVoI7NvWVjIzhKKBpD3eJL\nqdLhCp5Svujo1RoHDMgIDfviL7Mb9EWb+TyPpPs4aa1ZwVIK6NdFH1ztHafq0QRi\nVGSuPknVSQLo5Pp2veQH4c4KkBTjlo76eMSM/z7/ybaPMXHSzDuJD3y2Zdm/9vu4\n30anrJz9wT7rKCmqBQzhuASBT+oSZiO6239zdWCo/0sOfA0IduZID7lxmuTzNQjG\nzo6yH2HZfcsriUd/Zn037AQ0dObXAng/Kg1/PpcslIodWJrzgo1ec1S4DWGr9hQM\nutt2un+/McHJUA5t8tFC1OD7GaHSCq28h3DUU4OzJQKBgQDjlHniWwwRhgjKccfr\nwsiXu3EpCPkzxAPDzb60d1l8RK0F+9sar5N6hTPwH/n/SAodFh+6e8owBI5QMYl4\nRqghnJALIxTlebRzVlQj2sK4pVWFs5BXk1rsnFfD3CjjuuP+L9OiJ0LZVjZ93Yo8\nEZW+Sc3fhlsQX8IGjusA6//aNwKBgQDiTy8RljZDDS2jnca+Wx5w0Xp+PxjDSGrR\nxhe906rSCUqd/jkaUm6kpy7pPLZFNy1rFMKSmlCZiSqVt/efFZvjKZgpEVZc1TNE\ncYsDH89MfuobB9wGVXf/ZHYnJqVlmX1hPmUlxUY5WaRw92zZFGQN7vT1VL7yDqe/\n8DYTrsEajQKBgFqdsRZnKFwF4ZQyT+dZDKQV943eS9PH1bPuRWP4LcJkWfyK9wge\nJvve8/pF0TZLifNg7stDJROPjNbzkog4ohOYEmbM1jI1DpvqIOCR53y8IFx3Th8A\nxnB2JCARlppuvP9mLb9gIKcHQ/VD9BnM6rH1EytQhQv1BP21hG8+iQZrAoGBAI3Q\naQJwStiBrZPWDvDCULUJz+TEzVOyUZ3asI330heEUwnmgQoJleQAD9mAfgepqABu\nJe+QW+maluDzQ2yhMeqL6hnyD1hlEZdWP3JZwKGC9lix+CLP9D5KSfAGu6aLJJKH\ncwLUOEk/71gK9F6j9H72uvHxhSZSILSaZoi2/bMRAoGAASxzCTClhoD0lEKRx6Tc\nt3wXB+Wd7zHr1eCPw+6IQT3bZlauP0HRoEpfrE1AJYT9v+d7jkMGs7ZVPlaj7u+N\nTIyFAyVgpJegmdKBOGffthTw8Hm80fuFvsZB/YSieWEs244+UDOlVI26pEuDdtbW\npjbt8SSQIaTdJ7R13nABoro=\n-----END PRIVATE KEY-----\n",
            "client_email"=> "firebase-adminsdk-fbsvc@astro-live.iam.gserviceaccount.com",
            "client_id" => "11819652950087478538317",
            "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
            "token_uri" => "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url"=> "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40astro-live.iam.gserviceaccount.com",
            "universe_domain"=> "googleapis.com"
    ];


public static function send($userDeviceDetail, $notification)
{
    $fcmService = new self();
    $projectId = 'astro-live';
    $serverApiKey = env('FCM_SERVER_KEY');
    $accessToken = $fcmService->getAccessToken($serverApiKey);

    $endpoint = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';

    $responses = []; // Array to store individual responses

    foreach ($userDeviceDetail->pluck('fcmToken')->all() as $token) {
        $notificationType = isset($notification['body']['notificationType']) ? (string) $notification['body']['notificationType'] : null;

        $payload = [
            'message' => [
                'token' => $token,
                'data' => [
                    'title' => $notification['title'],
                     'description' => $notification['body']['description'],
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'body' => json_encode($notification['body']),

                ],
                'android' => [
                    'priority' => 'high',
                ],
            ],
        ];


        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $responses[] = json_decode($response, true);
    }

    return $responses;
}


    private function getAccessToken($serverApiKey)
    {
        $url = 'https://www.googleapis.com/oauth2/v4/token';
        $data = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->generateJwtAssertion($serverApiKey),
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $body = json_decode($response, true);

        return $body['access_token'];
    }


    private function generateJwtAssertion($serverApiKey)
{
    $now = time();
    $exp = $now + 3600; // Token expires in 1 hour

    $jwtClaims = [
        'iss' => $this->serviceAccountKey['client_email'],
        'sub' => $this->serviceAccountKey['client_email'],
        'aud' => 'https://www.googleapis.com/oauth2/v4/token',
        'scope' => 'https://www.googleapis.com/auth/cloud-platform',
        'iat' => $now,
        'exp' => $exp,
    ];

    $jwtHeader = [
        'alg' => 'RS256',
        'typ' => 'JWT',
    ];

    $base64UrlEncodedHeader = $this->base64UrlEncode(json_encode($jwtHeader));
    $base64UrlEncodedClaims = $this->base64UrlEncode(json_encode($jwtClaims));

    $signatureInput = $base64UrlEncodedHeader.'.'.$base64UrlEncodedClaims;

    $privateKey = openssl_pkey_get_private($this->serviceAccountKey['private_key']);
    openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    openssl_free_key($privateKey);

    $base64UrlEncodedSignature = $this->base64UrlEncode($signature);

    return $signatureInput.'.'.$base64UrlEncodedSignature;
}

    private function base64UrlEncode($input)
    {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }

}

