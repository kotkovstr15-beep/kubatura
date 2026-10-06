<?php
namespace Yandex\Market\Checkout;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Config\Option;
use Yandex\Market\Checkout\Handlers\CheckBasketHandler;
use Yandex\Market\Checkout\Handlers\WarehousesHandler;
use Yandex\Market\Checkout\Handlers\OrdersHandler;
use Yandex\Market\Checkout\Handlers\SettingsHandler;

class Api
{
    private $request;
    private $moduleId = 'yandex.market';

    public function __construct()
    {
        Loader::includeModule('yandex.market');
        $this->request = Application::getInstance()->getContext()->getRequest();
        header('Content-Type: application/json; charset=utf-8');
    }

    public function handleRequest()
    {
        try {
            $method = $this->request->get('method');
            if ($method === null || $method === '') {
                $method = ApiRouter::resolveInternalMethod();
            }
            $action = $this->request->get('action');
            $orderId = $this->request->get('orderId');

            $handler = $this->getHandler($method, $action);

            if (!$handler) {
                throw new \Exception('Unknown method', 404);
            }

            if (!$this->validateJwt()) {
                http_response_code(401);
                echo Json::encode(['error' => 'Unauthorized']);
                return;
            }

            if ($method === 'orders' && $this->request->getRequestMethod() === 'POST' && ($action === null || $action === '')) {
                ignore_user_abort(true);
            }

            $handler->handle($orderId);

        } catch (\Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo Json::encode([
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getHandler($method, $action)
    {
        switch ($method) {
            case 'checkBasket':
                if ($this->request->getRequestMethod() === 'POST') {
                    return new CheckBasketHandler();
                }
                throw new \Exception('Method not allowed', 405);

            case 'warehouses':
                if ($this->request->getRequestMethod() === 'GET') {
                    return new WarehousesHandler();
                }
                throw new \Exception('Method not allowed', 405);

            case 'orders':
                if ($this->request->getRequestMethod() === 'POST') {
                    return new OrdersHandler();
                }
                throw new \Exception('Method not allowed', 405);

            case 'settings':
                if ($this->request->getRequestMethod() === 'GET') {
                    return new SettingsHandler();
                }
                throw new \Exception('Method not allowed', 405);

            default:
                return null;
        }
    }

    /**
     * Возвращает значение заголовка Authorization из запроса.
     */
    public static function getAuthorizationHeader()
    {
        if (function_exists('getallheaders')) {
            $headers = array_change_key_case(getallheaders(), CASE_LOWER);
            $auth = $headers['authorization'] ?? '';
            $auth = trim($auth, " !@#$%^&*()_+-=[]{}|\\:;\"'<>,.?/~`");
            if ($auth !== '') {
                return $auth;
            }
        }
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            return $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        if (!empty($_SERVER['REMOTE_USER'])) {
            return $_SERVER['REMOTE_USER'];
        }
        return '';
    }

    private function validateJwt()
    {
        $authHeader = self::getAuthorizationHeader();

        if (empty($authHeader) || strpos($authHeader, 'Bearer ') !== 0) {
            return false;
        }

        $token = substr($authHeader, 7);
        $validToken = Option::get($this->moduleId, 'JWT_TOKEN', '');

        if (empty($validToken)) {
            return false;
        }
        return is_string($token) && hash_equals($validToken, $token);
    }
}
