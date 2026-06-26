<?php

declare(strict_types=1);

namespace Amazon3DModelParser;

final class AmazonPageClient
{
    public function fetch(AmazonProductUrl $url): FetchResult
    {
        $curl = curl_init($url->original);
        if ($curl === false) {
            return FetchResult::failure('Could not initialize cURL.');
        }

        curl_setopt_array($curl, [
            CURLOPT_ENCODING => '',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => $this->headers($url),
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $body = curl_exec($curl);
        if ($body === false) {
            $message = curl_error($curl);

            return FetchResult::failure($message);
        }

        $statusCode = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        if ($statusCode >= 400) {
            return FetchResult::failure('Amazon returned HTTP ' . $statusCode . '.');
        }

        return FetchResult::success((string) $body);
    }

    /**
     * Amazon often exposes 3D links only for app-like clients.
     *
     * @return list<string>
     */
    private function headers(AmazonProductUrl $url): array
    {
        return [
            'user-agent: Amazon.com/28.4.0.100 (Android/14/SomeModel)',
            'cookie: ' . implode('; ', [
                'mobile-device-info=dpi:420.0|w:1080|h:2135',
                'amzn-app-id=Amazon.com/28.4.0.100/18.0.357239.0',
                'amzn-app-ctxt=' . rawurlencode($this->appContext($url)),
            ]),
        ];
    }

    private function appContext(AmazonProductUrl $url): string
    {
        return '1.8 {"an":"Amazon.com","av":"28.4.0.100","xv":"1.15.0","os":"Android","ov":"14","cp":788760,"uiv":4,"ast":3,"nal":"1","di":{"pr":"OnePlus7","md":"GM1901","v":"OnePlus7","mf":"OnePlus","dsn":"45ae2d3b4efa48a399e0f0a324adbaa7","dti":"A1MPSLFC7L5AFK","ca":"Carrier","ct":"WIFI"},"dm":{"w":1080,"h":2135,"ld":2.625,"dx":403.4110107421875,"dy":409.90301513671875,"pt":0,"pb":78},"is":"com.android.vending","msd":"' . $url->cookieDomain() . '"}';
    }
}
