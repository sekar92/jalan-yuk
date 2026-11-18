<?php

namespace App\Helpers;

class MapsHelper
{
    /**
     * Extracts coordinates (lat, lng) from a Google Maps URL.
     * Supports standard URLs, embed URLs, and expands shortlinks (maps.app.goo.gl, goo.gl).
     *
     * @param string $url
     * @return array{latitude: ?float, longitude: ?float, resolved_url: string, error: ?string}
     */
    public static function extractCoordinates(string $url): array
    {
        $url = trim($url);
        if (empty($url)) {
            return ['latitude' => null, 'longitude' => null, 'resolved_url' => '', 'error' => 'URL peta tidak boleh kosong.'];
        }

        // Add scheme if missing
        if (!preg_match('~^https?://~i', $url)) {
            $url = 'https://' . $url;
        }

        $resolvedUrl = $url;

        // If shortlink, expand via HTTP HEAD/GET request
        if (self::isShortlink($url)) {
            $expanded = self::expandShortlink($url);
            if ($expanded) {
                $resolvedUrl = $expanded;
            }
        }

        $coords = self::parseCoordsFromString($resolvedUrl);
        if ($coords) {
            return [
                'latitude' => $coords['lat'],
                'longitude' => $coords['lng'],
                'resolved_url' => $resolvedUrl,
                'error' => null
            ];
        }

        // If not found in resolvedUrl, check original url
        if ($resolvedUrl !== $url) {
            $coords = self::parseCoordsFromString($url);
            if ($coords) {
                return [
                    'latitude' => $coords['lat'],
                    'longitude' => $coords['lng'],
                    'resolved_url' => $resolvedUrl,
                    'error' => null
                ];
            }
        }

        return [
            'latitude' => null,
            'longitude' => null,
            'resolved_url' => $resolvedUrl,
            'error' => 'Koordinat tidak ditemukan secara otomatis dari URL. Anda dapat memasukkan Latitude dan Longitude secara manual.'
        ];
    }

    public static function isShortlink(string $url): bool
    {
        return (bool)preg_match('~^https?://(maps\.app\.goo\.gl|goo\.gl/maps|g\.co/kgs)~i', $url);
    }

    public static function expandShortlink(string $url): ?string
    {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_HEADER => true,
                CURLOPT_NOBODY => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_TIMEOUT => 4,
                CURLOPT_CONNECTTIMEOUT => 3,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($ch);
            $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);

            if ($effectiveUrl && $effectiveUrl !== $url) {
                return $effectiveUrl;
            }

            // Also search response body for coordinates in case of JS redirect or meta refresh
            if ($response && is_string($response)) {
                $coords = self::parseCoordsFromString($response);
                if ($coords) {
                    return "https://www.google.com/maps?q={$coords['lat']},{$coords['lng']}";
                }
            }
        } else {
            // Fallback to get_headers
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'follow_location' => 1,
                    'max_redirects' => 5,
                    'timeout' => 4,
                    'header' => "User-Agent: Mozilla/5.0\r\n"
                ]
            ]);
            $headers = @get_headers($url, true, $context);
            if ($headers && isset($headers['Location'])) {
                $location = is_array($headers['Location']) ? end($headers['Location']) : $headers['Location'];
                return $location;
            }
        }

        return null;
    }

    public static function parseCoordsFromString(string $str): ?array
    {
        // 1. @-6.123456,106.123456
        if (preg_match('~@(-?\d+\.\d{3,}),(-?\d+\.\d{3,})~', $str, $matches)) {
            return ['lat' => (float)$matches[1], 'lng' => (float)$matches[2]];
        }

        // 2. !3d-6.123456!4d106.123456 (Google Maps data string)
        if (preg_match('~!3d(-?\d+\.\d{3,})!4d(-?\d+\.\d{3,})~', $str, $matches)) {
            return ['lat' => (float)$matches[1], 'lng' => (float)$matches[2]];
        }

        // 3. ?q=-6.123456,106.123456 or query=-6.123456,106.123456 or ll=-6.123456,106.123456
        if (preg_match('~[?&](?:q|query|ll|center|sll)=(-?\d+\.\d{3,})(?:%2C|,)(-?\d+\.\d{3,})~i', $str, $matches)) {
            return ['lat' => (float)$matches[1], 'lng' => (float)$matches[2]];
        }

        // 4. /place/[^/]+/-?\d+\.\d+,-?\d+\.\d+
        if (preg_match('~/place/[^/]+/(-?\d+\.\d{3,}),(-?\d+\.\d{3,})~', $str, $matches)) {
            return ['lat' => (float)$matches[1], 'lng' => (float)$matches[2]];
        }

        // 5. Raw coordinate pair in URL path or query
        if (preg_match('~(-?\d{1,2}\.\d{4,})\s*[,|\/]\s*(-?\d{1,3}\.\d{4,})~', $str, $matches)) {
            $lat = (float)$matches[1];
            $lng = (float)$matches[2];
            // Validate plausible lat/lng range
            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                return ['lat' => $lat, 'lng' => $lng];
            }
        }

        return null;
    }
}
