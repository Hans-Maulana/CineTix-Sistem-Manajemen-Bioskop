<?php

namespace App\Support;

class RedirectAfterAuth
{
    private const BLOCKED_PATHS = ['/login', '/register', '/logout', '/password'];

    /**
     * Simpan URL tujuan setelah login/register (query ?redirect= atau referer aman).
     */
    public static function remember(?string $url = null): void
    {
        $candidate = $url ?? request()->query('redirect');

        if (!$candidate) {
            $referer = request()->headers->get('referer');
            if ($referer && self::isAllowed($referer)) {
                $candidate = $referer;
            }
        }

        if ($candidate && self::isAllowed($candidate)) {
            session(['url.intended' => self::normalize($candidate)]);
        }
    }

    public static function fallback(): string
    {
        return route('landing-page');
    }

    private static function normalize(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $query = parse_url($url, PHP_URL_QUERY);

        return $query ? $path . '?' . $query : $path;
    }

    private static function isAllowed(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';

        return !self::containsBlockedPath($path);
    }

    private static function containsBlockedPath(string $path): bool
    {
        foreach (self::BLOCKED_PATHS as $blocked) {
            if (str_contains($path, $blocked)) {
                return true;
            }
        }

        return false;
    }
}
