<?php
namespace Yandex\Market\Checkout;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Event;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Web\Json;
use Bitrix\Sale\Notify;
use Bitrix\Sale\Order;
use Bitrix\Sale\Payment;
use Bitrix\Sale\Shipment;


class Handlers
{
    static $MODULE_ID = "yandex.market";

    /**
     * Ранний обработчик (sort=1) на событиях Sale.
     * Отключает письма модуля Sale только для заказов YCP.
     */
    public static function onSaleOrderSavedMailGate($event)
    {
        if (!Loader::includeModule('sale')) {
            return;
        }

        $order = self::getOrderFromNotifyEvent($event);
        if (!$order instanceof Order) {
            return;
        }

        if (!self::isYandexKitOrder($order)) {
            return;
        }

        if (Option::get(self::$MODULE_ID, 'SEND_ORDER_EMAILS', 'N') === 'Y') {
            return;
        }

        Notify::setNotifyDisable(true);
    }

    private static function getOrderFromNotifyEvent($event): ?Order
    {
        $entity = null;
        if ($event instanceof Event) {
            $entity = $event->getParameter('ENTITY');
        } elseif (is_array($event) && isset($event['ENTITY'])) {
            $entity = $event['ENTITY'];
        }

        if ($entity instanceof Order) {
            return $entity;
        }
        if ($entity instanceof Shipment) {
            $order = $entity->getParentOrder();

            return $order instanceof Order ? $order : null;
        }
        if ($entity instanceof Payment) {
            try {
                $order = $entity->getOrder();
            } catch (\Throwable $e) {
                return null;
            }

            return $order instanceof Order ? $order : null;
        }

        return null;
    }

    private static function isYandexKitOrder(Order $order): bool
    {
        $deliveryId = (int) Option::get(self::$MODULE_ID, 'YANDEX_KIT_DELIVERY_ID', 0);
        if ($deliveryId > 0) {
            foreach ($order->getShipmentCollection() as $shipment) {
                if ($shipment->isSystem()) {
                    continue;
                }
                if ((int) $shipment->getField('DELIVERY_ID') === $deliveryId) {
                    return true;
                }
            }
        }

        $paySystemId = (int) Option::get(self::$MODULE_ID, 'YANDEX_KIT_PAY_SYSTEM_ID', 0);
        if ($paySystemId > 0) {
            foreach ($order->getPaymentCollection() as $payment) {
                if ((int) $payment->getPaymentSystemId() === $paySystemId) {
                    return true;
                }
            }
        }

        if (self::getOrderPropertyStringByCode($order, 'YANDEX_ORDER_ID') !== null) {
            return true;
        }

        $yastoreUserId = (int) Option::get(self::$MODULE_ID, 'YASTORE_USER_ID', 0);
        if ($yastoreUserId > 0 && (int) $order->getUserId() === $yastoreUserId) {
            return true;
        }

        return false;
    }

    private static function getOrderPropertyStringByCode(Order $order, string $code): ?string
    {
        try {
            $propertyCollection = $order->getPropertyCollection();
            if (!method_exists($propertyCollection, 'getItemByOrderPropertyCode')) {
                return null;
            }
            $propItem = $propertyCollection->getItemByOrderPropertyCode($code);
            if (!$propItem) {
                return null;
            }
            $value = $propItem->getValue();
            if ($value === null || $value === '' || (is_array($value) && count($value) === 0)) {
                return null;
            }

            return is_array($value) ? (string) reset($value) : (string) $value;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Подключение JS/CSS кнопки «Купить в 1 клик» на странице корзины.
     */
    public static function appendYandexCheckoutJs()
    {
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        $requestPage = $request->getRequestedPage();

        $basketPath = trim((string) Option::get(self::$MODULE_ID, 'YAKIT_BASKET_PAGE_PATH', '/personal/cart/'));
        if ($basketPath === '') {
            $basketPath = '/personal/cart/';
        }
        $basketPathNorm = trim(trim($basketPath), '/');
        $showButton = Option::get(self::$MODULE_ID, 'SHOW_BUTTON', 'N');
        // Временно отключено: функционал кнопки в карточке товара.
        // $showProductButton = Option::get(self::$MODULE_ID, 'SHOW_PRODUCT_BUTTON', 'N');
        $showProductButton = 'N';

        $onBasketPage = ($basketPathNorm !== '' && (strpos($requestPage, $basketPathNorm) !== false));
        $enableBasketButton = ($showButton === 'Y' && $onBasketPage);
        $enableProductButton = ($showProductButton === 'Y');

        if (!$enableBasketButton && !$enableProductButton) {
            return;
        }

        $buttonAnchor = Option::get(self::$MODULE_ID, 'BUTTON_ANCHOR', '.basket-checkout-section-inner');
        $buttonInsertAfter = Option::get(self::$MODULE_ID, 'YAKIT_BUTTON_INSERT_AFTER', '');
        $buttonCss = trim(Option::get(self::$MODULE_ID, 'YAKIT_BUTTON_CSS', ''));
        if ($buttonCss === '') {
            $buttonCss = self::getDefaultButtonCss();
        }
        $buttonText = (string) Option::get(self::$MODULE_ID, 'YAKIT_BUTTON_TEXT', 'Купить в один клик');
        $productButtonAnchor = (string) Option::get(self::$MODULE_ID, 'PRODUCT_BUTTON_ANCHOR', '');
        $productButtonInsertAfter = (string) Option::get(self::$MODULE_ID, 'PRODUCT_BUTTON_INSERT_AFTER', '');
        $productButtonCss = trim((string) Option::get(self::$MODULE_ID, 'PRODUCT_BUTTON_CSS', ''));
        if ($productButtonCss === '') {
            $productButtonCss = self::getDefaultProductButtonCss();
        }
        $productButtonText = (string) Option::get(self::$MODULE_ID, 'PRODUCT_BUTTON_TEXT', 'Купить в 1 клик');
        $productIdSelector = (string) Option::get(self::$MODULE_ID, 'PRODUCT_ID_SELECTOR', '[data-product-id], [name=PRODUCT_ID], [name=PRODUCT_ID_INPUT]');

        $jsConfig = [
            'YAKIT_BASKET_BUTTON_ENABLED' => $enableBasketButton ? 'Y' : 'N',
            'BUTTON_ANCHOR' => (string)$buttonAnchor,
            'YAKIT_BUTTON_INSERT_AFTER' => (string)$buttonInsertAfter,
            'YAKIT_BUTTON_CSS' => (string)str_replace('</style>', '', $buttonCss),
            'YAKIT_BUTTON_TEXT' => (string)$buttonText,
            'YAKIT_PRODUCT_BUTTON_ENABLED' => $enableProductButton ? 'Y' : 'N',
            'YAKIT_PRODUCT_BUTTON_ANCHOR' => (string)$productButtonAnchor,
            'YAKIT_PRODUCT_BUTTON_INSERT_AFTER' => (string)$productButtonInsertAfter,
            'YAKIT_PRODUCT_BUTTON_CSS' => (string)str_replace('</style>', '', $productButtonCss),
            'YAKIT_PRODUCT_BUTTON_TEXT' => (string)$productButtonText,
            'YAKIT_PRODUCT_ID_SELECTOR' => (string)$productIdSelector,
        ];

        Asset::getInstance()->addString(
            '<script>window.YaKitCheckout=' . Json::encode(
                $jsConfig,
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            ) . ';</script>'
        );
        Asset::getInstance()->addJs('/bitrix/js/yastore.checkout/script.js');
    }

    private static function getDefaultButtonCss()
    {
        return "#yastore-checkout-button {\n"
            . "    border: none;\n"
            . "    outline: none;\n"
            . "    border-radius: 12px;\n"
            . "    background-color: rgb(255, 99, 41);\n"
            . "    color: #ffffff;\n"
            . "    padding: 0 12px;\n"
            . "    margin: 12px 0 0 10px;\n"
            . "    height: 42px;\n"
            . "    width: 100%;\n"
            . "    font-weight: 600;\n"
            . "    font-family: inherit;\n"
            . "    line-height: 16px;\n"
            . "    display: flex;\n"
            . "    gap: 6px;\n"
            . "    align-items: center;\n"
            . "    justify-content: center;\n"
            . "    transition: transform .12s ease-out, filter .12s ease-out;\n"
            . "}\n"
            . "#yastore-checkout-button:hover {\n"
            . "    background: linear-gradient(245deg, rgba(255, 99, 41, 0) 85%, #FFC002 109%), linear-gradient(77deg, rgb(255, 192, 2, .5) .5%, rgba(255, 99, 41, .18) 24%), radial-gradient(57% 134.79% at 0% 0%, #FF27F5 0%, #FF6329 100%);\n"
            . "}\n"
            . "#yastore-checkout-button:active {\n"
            . "    transform: scale(.97);\n"
            . "    filter: brightness(0.9);\n"
            . "}";
    }

    private static function getDefaultProductButtonCss()
    {
        return "#yastore-checkout-product-button {\n"
            . "    border: none;\n"
            . "    outline: none;\n"
            . "    border-radius: 12px;\n"
            . "    background-color: rgb(255, 99, 41);\n"
            . "    color: #ffffff;\n"
            . "    padding: 0 12px;\n"
            . "    margin: 12px 0 0 10px;\n"
            . "    height: 42px;\n"
            . "    width: 100%;\n"
            . "    font-weight: 600;\n"
            . "    font-family: inherit;\n"
            . "    line-height: 16px;\n"
            . "    display: flex;\n"
            . "    gap: 6px;\n"
            . "    align-items: center;\n"
            . "    justify-content: center;\n"
            . "    transition: transform .12s ease-out, filter .12s ease-out;\n"
            . "}\n"
            . "#yastore-checkout-product-button:hover {\n"
            . "    background: linear-gradient(245deg, rgba(255, 99, 41, 0) 85%, #FFC002 109%), linear-gradient(77deg, rgb(255, 192, 2, .5) .5%, rgba(255, 99, 41, .18) 24%), radial-gradient(57% 134.79% at 0% 0%, #FF27F5 0%, #FF6329 100%);\n"
            . "}\n"
            . "#yastore-checkout-product-button:active {\n"
            . "    transform: scale(.97);\n"
            . "    filter: brightness(0.9);\n"
            . "}";
    }

    /**
     * Обработчик события OnSaleOrderSaved.
     */
    public static function onSaleOrderSaved($eventOrOrder)
    {
        try {
            $autoCancelEnabled = Option::get(self::$MODULE_ID, 'AUTO_CANCEL_ON_STATUS_CHANGE', 'N');

            if ($autoCancelEnabled !== 'Y') {
                return;
            }

            if ($eventOrOrder instanceof \Bitrix\Main\Event) {
                $parameters = $eventOrOrder->getParameters();
                $order = $parameters['ENTITY'] ?? null;
            } elseif ($eventOrOrder instanceof Order) {
                $order = $eventOrOrder;
            } else {
                return;
            }

            if (!$order instanceof Order) {
                return;
            }

            $orderId = $order->getId();

            if ($orderId <= 0) {
                return;
            }

            $autoCancelStatus = Option::get(self::$MODULE_ID, 'AUTO_CANCEL_STATUS', 'C');
            $currentStatus = $order->getField('STATUS_ID');

            if ($currentStatus === $autoCancelStatus) {
                if (class_exists('\Bitrix\Sale\OrderHistory')) {
                    \Bitrix\Sale\OrderHistory::addAction(
                        'ORDER',
                        $orderId,
                        'ORDER_COMMENTED',
                        $orderId,
                        $order,
                        ['COMMENTS' => 'Статус заказа изменен на статус отмены (' . $currentStatus . '). Инициирована автоматическая отмена через YCP API.']
                    );
                }

                self::sendCancelRequest($orderId, $order);
            }

            $autoCompleteEnabled = Option::get(self::$MODULE_ID, 'AUTO_COMPLETE_ON_STATUS_CHANGE', 'N');
            if ($autoCompleteEnabled === 'Y') {
                $autoCompleteStatus = Option::get(self::$MODULE_ID, 'AUTO_COMPLETE_STATUS', 'F');

                if ($currentStatus === $autoCompleteStatus) {
                    if (class_exists('\Bitrix\Sale\OrderHistory')) {
                        \Bitrix\Sale\OrderHistory::addAction(
                            'ORDER',
                            $orderId,
                            'ORDER_COMMENTED',
                            $orderId,
                            $order,
                            ['COMMENTS' => 'Статус заказа изменен на статус завершения (' . $currentStatus . '). Инициировано автоматическое завершение через YCP API.']
                        );
                    }

                    self::sendCompleteRequest($orderId, $order);
                }
            }

        } catch (\Exception $e) {
            \Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog($e);
        }
    }

    /**
     * Обработчик события OnSaleStatusOrderChange.
     */
    public static function onSaleStatusOrderChange($eventOrOrder)
    {
        try {
            $autoCancelEnabled = Option::get(self::$MODULE_ID, 'AUTO_CANCEL_ON_STATUS_CHANGE', 'N');

            if ($autoCancelEnabled !== 'Y') {
                return;
            }

            if ($eventOrOrder instanceof \Bitrix\Main\Event) {
                $parameters = $eventOrOrder->getParameters();
                $order = $parameters['ENTITY'] ?? null;
            } elseif ($eventOrOrder instanceof Order) {
                $order = $eventOrOrder;
            } else {
                return;
            }

            if (!$order instanceof Order) {
                return;
            }

            $orderId = $order->getId();

            $autoCancelStatus = Option::get(self::$MODULE_ID, 'AUTO_CANCEL_STATUS', 'C');
            $currentStatus = $order->getField('STATUS_ID');

            if ($currentStatus === $autoCancelStatus) {
                if ($orderId > 0) {
                    self::sendCancelRequest($orderId, $order);
                }
            }

            $autoCompleteEnabled = Option::get(self::$MODULE_ID, 'AUTO_COMPLETE_ON_STATUS_CHANGE', 'N');
            if ($autoCompleteEnabled === 'Y') {
                $autoCompleteStatus = Option::get(self::$MODULE_ID, 'AUTO_COMPLETE_STATUS', 'F');

                if ($currentStatus === $autoCompleteStatus && $orderId > 0) {
                    if (class_exists('\Bitrix\Sale\OrderHistory')) {
                        \Bitrix\Sale\OrderHistory::addAction(
                            'ORDER',
                            $orderId,
                            'ORDER_COMMENTED',
                            $orderId,
                            $order,
                            ['COMMENTS' => 'Статус заказа изменен на статус завершения (' . $currentStatus . '). Инициировано автоматическое завершение через YCP API.']
                        );
                    }

                    self::sendCompleteRequest($orderId, $order);
                }
            }

        } catch (\Exception $e) {
            \Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog($e);
        }
    }

    private static function sendCancelRequest($orderId, Order $order)
    {
        $credentials = Option::get(self::$MODULE_ID, 'YANDEX_KIT_CREDENTIALS', '');

        if (empty($credentials)) {
            $storeId = Option::get(self::$MODULE_ID, 'YANDEX_KIT_STORE_ID', '');
            $apiToken = Option::get(self::$MODULE_ID, 'YANDEX_KIT_API_TOKEN', '');
        } else {
            $parts = explode('#', $credentials, 2);
            if (count($parts) === 2) {
                $storeId = trim($parts[0]);
                $apiToken = trim($parts[1]);
            } else {
                $storeId = '';
                $apiToken = '';
            }
        }

        if (empty($apiToken) || empty($storeId)) {
            return;
        }

        $externalOrderId = self::getExternalOrderId($order);

        if (empty($externalOrderId)) {
            return;
        }

        $apiUrl = rtrim(Option::get(self::$MODULE_ID, 'YANDEX_KIT_API_URL', 'https://integration.yastore.yandex.net/'), '/');
        $url = "{$apiUrl}/api/public/v1/orders/{$externalOrderId}/cancel";

        $headers = [
            'authorization: Bearer ' . $apiToken,
            'yandex-kit-store-id: ' . $storeId,
            'Content-Type: application/json'
        ];

        $logFile = sys_get_temp_dir() . '/yastore_checkout_cancel.log';
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'order_id' => $orderId,
            'external_order_id' => $externalOrderId,
            'request' => ['url' => $url, 'method' => 'POST']
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        $logData['response'] = ['http_code' => $httpCode, 'error' => $error ?: null, 'body' => $response ?: null];
        file_put_contents($logFile, json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);

        if ($error) {
            \Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog(
                new \Exception("Curl error for order cancel: {$error}")
            );
            if (class_exists('\Bitrix\Sale\OrderHistory')) {
                \Bitrix\Sale\OrderHistory::addAction('ORDER', $orderId, 'ORDER_COMMENTED', $orderId, $order,
                    ['COMMENTS' => 'Ошибка отправки отмены заказа в YCP API: ' . $error]);
            }
        } elseif ($httpCode < 200 || $httpCode >= 300) {
            \Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog(
                new \Exception("Failed to cancel order {$externalOrderId}. HTTP code: {$httpCode}, Response: {$response}")
            );
            if (class_exists('\Bitrix\Sale\OrderHistory')) {
                \Bitrix\Sale\OrderHistory::addAction('ORDER', $orderId, 'ORDER_COMMENTED', $orderId, $order,
                    ['COMMENTS' => 'Ошибка отправки отмены заказа в YCP API. HTTP код: ' . $httpCode . ', Внешний ID: ' . $externalOrderId]);
            }
        } else {
            if (class_exists('\Bitrix\Sale\OrderHistory')) {
                \Bitrix\Sale\OrderHistory::addAction('ORDER', $orderId, 'ORDER_COMMENTED', $orderId, $order,
                    ['COMMENTS' => 'Отмена заказа успешно отправлена в YCP API. Внешний ID: ' . $externalOrderId]);
            }
        }
    }

    private static function sendCompleteRequest($orderId, Order $order)
    {
        $credentials = Option::get(self::$MODULE_ID, 'YANDEX_KIT_CREDENTIALS', '');

        if (empty($credentials)) {
            $storeId = Option::get(self::$MODULE_ID, 'YANDEX_KIT_STORE_ID', '');
            $apiToken = Option::get(self::$MODULE_ID, 'YANDEX_KIT_API_TOKEN', '');
        } else {
            $parts = explode('#', $credentials, 2);
            if (count($parts) === 2) {
                $storeId = trim($parts[0]);
                $apiToken = trim($parts[1]);
            } else {
                $storeId = '';
                $apiToken = '';
            }
        }

        if (empty($apiToken) || empty($storeId)) {
            return;
        }

        $externalOrderId = self::getExternalOrderId($order);

        if (empty($externalOrderId)) {
            return;
        }

        $apiUrl = rtrim(Option::get(self::$MODULE_ID, 'YANDEX_KIT_API_URL', 'https://integration.yastore.yandex.net/'), '/');
        $url = "{$apiUrl}/api/public/v1/orders/{$externalOrderId}/delivery/total_complete/self_pick_up";

        $headers = [
            'authorization: Bearer ' . $apiToken,
            'yandex-kit-store-id: ' . $storeId,
            'Content-Type: application/json'
        ];

        $logFile = sys_get_temp_dir() . '/yastore_checkout_complete.log';
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'order_id' => $orderId,
            'external_order_id' => $externalOrderId,
            'request' => ['url' => $url, 'method' => 'POST']
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        $logData['response'] = ['http_code' => $httpCode, 'error' => $error ?: null, 'body' => $response ?: null];
        file_put_contents($logFile, json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);

        if ($error) {
            \Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog(
                new \Exception("Curl error for order complete: {$error}")
            );
            if (class_exists('\Bitrix\Sale\OrderHistory')) {
                \Bitrix\Sale\OrderHistory::addAction('ORDER', $orderId, 'ORDER_COMMENTED', $orderId, $order,
                    ['COMMENTS' => 'Ошибка отправки завершения заказа в YCP API: ' . $error]);
            }
        } elseif ($httpCode < 200 || $httpCode >= 300) {
            \Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog(
                new \Exception("Failed to complete order {$externalOrderId}. HTTP code: {$httpCode}, Response: {$response}")
            );
            if (class_exists('\Bitrix\Sale\OrderHistory')) {
                \Bitrix\Sale\OrderHistory::addAction('ORDER', $orderId, 'ORDER_COMMENTED', $orderId, $order,
                    ['COMMENTS' => 'Ошибка отправки завершения заказа в YCP API. HTTP код: ' . $httpCode . ', Внешний ID: ' . $externalOrderId]);
            }
        } else {
            if (class_exists('\Bitrix\Sale\OrderHistory')) {
                \Bitrix\Sale\OrderHistory::addAction('ORDER', $orderId, 'ORDER_COMMENTED', $orderId, $order,
                    ['COMMENTS' => 'Завершение заказа успешно отправлено в YCP API. Внешний ID: ' . $externalOrderId]);
            }
        }
    }

    private static function getExternalOrderId(Order $order)
    {
        try {
            $propertyCollection = $order->getPropertyCollection();

            foreach ($propertyCollection as $property) {
                $code = $property->getField('CODE');
                if ($code === 'YANDEX_ORDER_ID') {
                    $value = $property->getValue();
                    if (!empty($value)) {
                        return $value;
                    }
                }
            }

            foreach ($propertyCollection as $property) {
                $code = $property->getField('CODE');
                if ($code === 'EXTERNAL_ORDER_ID') {
                    $value = $property->getValue();
                    if (!empty($value)) {
                        return $value;
                    }
                }
            }

        } catch (\Exception $e) {
            \Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog($e);
        }

        return null;
    }
}
