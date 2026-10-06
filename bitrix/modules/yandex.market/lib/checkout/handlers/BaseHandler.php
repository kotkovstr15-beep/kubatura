<?php
namespace Yandex\Market\Checkout\Handlers;

use Bitrix\Main\Application;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Catalog\StoreTable;
use Bitrix\Catalog\StoreProductTable;
use Yandex\Market\Checkout\Api;

abstract class BaseHandler
{
    const ERROR_INVALID_INPUT = 'INVALID_INPUT';
    const ERROR_UNAUTHORIZED = 'UNAUTHORIZED';
    const ERROR_NOT_FOUND = 'NOT_FOUND';
    const ERROR_CONFLICT = 'CONFLICT';
    const ERROR_INTERNAL = 'INTERNAL_ERROR';
    const ERROR_PRODUCT_NOT_FOUND = 'PRODUCT_NOT_FOUND';
    const ERROR_INVENTORY_CONFLICT = 'INVENTORY_CONFLICT';

    protected $request;
    protected $moduleId = 'yandex.market';

    public function __construct()
    {
        $this->request = Application::getInstance()->getContext()->getRequest();
    }

    abstract public function handle($orderId = null);

    protected function sendResponse($data, $httpCode = 200)
    {
        http_response_code($httpCode);
        echo Json::encode($data);
    }

    protected function sendError($message, $httpCode = 500, $code = null)
    {
        http_response_code($httpCode);
        echo Json::encode(['error' => $message]);
    }

    protected function sendErrorWithData($message, $httpCode = 500, $code = null, array $extra = [])
    {
        http_response_code($httpCode);
        $body = array_merge(['error' => $message], $extra);
        echo Json::encode($body);
    }

    protected function authorizeRequest()
    {
        $authHeader = Api::getAuthorizationHeader();

        if ($authHeader === '' || stripos($authHeader, 'Bearer ') !== 0) {
            return false;
        }

        $token = trim(substr($authHeader, 7));
        $validToken = trim((string) Option::get($this->moduleId, 'JWT_TOKEN', ''));

        if ($token === '' || $validToken === '') {
            return false;
        }

        return hash_equals($validToken, $token);
    }

    protected function requireAuthorized()
    {
        if ($this->authorizeRequest()) {
            return true;
        }

        $this->sendError('Unauthorized', 401, self::ERROR_UNAUTHORIZED);

        return false;
    }

    protected function useGeneralStockOnly()
    {
        return Option::get($this->moduleId, 'USE_GENERAL_STOCK_ONLY', 'N') === 'Y';
    }

    protected function getGeneralWarehouseForApi()
    {
        return $this->getVirtualWarehouse();
    }

    protected function getVirtualWarehouse()
    {
        return [
            'id' => '1',
            'name' => 'Виртуальный склад',
        ];
    }

    protected function hasWarehouseStock($productId)
    {
        if (!Loader::includeModule('catalog')) {
            return false;
        }

        $storeProduct = StoreProductTable::getList([
            'filter' => [
                'PRODUCT_ID' => $productId,
                '>AMOUNT' => 0
            ],
            'select' => ['ID'],
            'limit' => 1
        ])->fetch();

        return (bool)$storeProduct;
    }

    protected function hasWarehouses()
    {
        if (!Loader::includeModule('catalog')) {
            return false;
        }

        $warehouse = StoreTable::getList([
            'filter' => ['ACTIVE' => 'Y'],
            'select' => ['ID'],
            'limit' => 1
        ])->fetch();

        return (bool)$warehouse;
    }

    protected function isStoreControlEnabled()
    {
        if (!Loader::includeModule('sale')) {
            return false;
        }

        if (method_exists('Bitrix\Sale\Configuration', 'useStoreControl')) {
            return \Bitrix\Sale\Configuration::useStoreControl();
        }

        return \Bitrix\Main\Config\Option::get('catalog', 'default_use_store_control', 'N') === 'Y';
    }
}
