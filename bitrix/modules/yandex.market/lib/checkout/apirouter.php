<?php
namespace Yandex\Market\Checkout;

/**
 * Сопоставление путей YCP (api/v1/...) с внутренними именами методов API (?method=).
 */
class ApiRouter
{
    /**
     * @return string|null внутреннее имя (warehouses, checkBasket, …) или null — использовать query method=
     */
    public static function resolveInternalMethod(): ?string
    {
        $path = self::getPathRelativeToEntry();
        if ($path === '') {
            return null;
        }
        $path = strtolower($path);
        $httpMethod = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

        $routes = [
            'GET' => [
                'api/v1/warehouses' => 'warehouses',
                'api/v1/settings' => 'settings',
            ],
            'POST' => [
                'api/v1/checkout/basket/check' => 'checkBasket',
                'api/v1/orders' => 'orders',
            ],
        ];

        return $routes[$httpMethod][$path] ?? null;
    }

    private static function getPathRelativeToEntry(): string
    {
        $uriPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $uriPath = '/' . trim(str_replace('\\', '/', $uriPath), '/');

        $publicPrefix = '/yastore.checkout/';
        if (stripos($uriPath, $publicPrefix) === 0) {
            $rel = substr($uriPath, strlen(rtrim($publicPrefix, '/')));
            $rel = ltrim($rel, '/');
        } else {
            $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
            $baseDir = rtrim(dirname($scriptName), '/');
            if ($baseDir !== '' && strpos($uriPath, $baseDir) === 0) {
                $rel = trim(substr($uriPath, strlen($baseDir)), '/');
            } else {
                $rel = trim($uriPath, '/');
            }
        }

        $rel = (string) preg_replace('#^index\.php/?#i', '', $rel);

        return trim($rel, '/');
    }
}
