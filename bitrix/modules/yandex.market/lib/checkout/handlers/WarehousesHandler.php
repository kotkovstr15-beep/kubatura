<?php
namespace Yandex\Market\Checkout\Handlers;

use Bitrix\Main\Loader;
use Bitrix\Catalog\StoreTable;

class WarehousesHandler extends BaseHandler
{
    public function handle($orderId = null)
    {
        if (!$this->requireAuthorized()) {
            return;
        }

        try {
            if (!Loader::includeModule('catalog')) {
                $this->sendError('Catalog module not available', 500);
                return;
            }

            $offset = max(0, (int)$this->request->getQuery('offset'));
            $rawLimit = $this->request->getQuery('limit');
            $limit = ($rawLimit !== null && $rawLimit !== '') ? max(0, (int)$rawLimit) : null;

            if ($this->useGeneralStockOnly()) {
                $gw = $this->getGeneralWarehouseForApi();
                $warehousesList = [[
                    'id' => $gw['id'],
                    'title' => $gw['name'],
                    'xml_id' => $gw['id'],
                    'active' => 'Y',
                ]];
            } else {
                $warehouses = StoreTable::getList([
                    'filter' => ['ACTIVE' => 'Y'],
                    'select' => ['*'],
                    'order' => ['ID' => 'ASC']
                ]);

                $warehousesList = [];
                while ($warehouse = $warehouses->fetch()) {
                    $warehouseLower = array_change_key_case($warehouse, CASE_LOWER);
                    $warehouseLower['id'] = (string)$warehouse['ID'];
                    $warehousesList[] = $warehouseLower;
                }

                if (empty($warehousesList)) {
                    $virtualWarehouse = $this->getVirtualWarehouse();
                    $warehousesList[] = [
                        'id' => (string)$virtualWarehouse['id'],
                        'title' => $virtualWarehouse['name'],
                        'xml_id' => $virtualWarehouse['id'],
                        'active' => 'Y'
                    ];
                }
            }

            $totalCount = count($warehousesList);
            if ($limit === null) {
                $warehousesList = array_slice($warehousesList, $offset);
            } else {
                $warehousesList = array_slice($warehousesList, $offset, $limit);
            }

            $this->sendResponse([
                'warehouses' => array_values($warehousesList),
                'total_count' => $totalCount,
            ]);

        } catch (\Exception $e) {
            $this->sendError('Failed to get warehouses: ' . $e->getMessage(), 500);
        }
    }
}
