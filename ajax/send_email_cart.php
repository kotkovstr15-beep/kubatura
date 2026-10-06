<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Mail\Event;
\Bitrix\Main\Loader::includeModule('iblock');

$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$email = $request->getPost('USER_EMAIL');

$success = false;

if(!empty($email)){
	$productId = $request->getPost('ITEMS');
	$arShopsId = [];
	$arShops = [];
	$totalPrice = 0;

	if(!empty($productId)){
		$rsElem = CIBlockElement::GetList(
			[],
			[
				'ID' => $productId,
				'ACTIVE' => 'Y',
			],
			false,
			false,
			['ID', 'NAME', 'PREVIEW_PICTURE', 'DETAIL_PAGE_URL', 'PROPERTY_PRICE', 'PROPERTY_SHOP']
		);
	
		while($arElem = $rsElem->getNext()){
			$arElem['PREVIEW_PICTURE'] = CFile::GetPath($arElem["PREVIEW_PICTURE"]);;
			$arProducts[] = $arElem;
	
			if(!empty($arElem['PROPERTY_SHOP_VALUE'])){
				$arShopsId[] = $arElem['PROPERTY_SHOP_VALUE'];
			}
	
			if(!empty($arElem['PROPERTY_PRICE_VALUE'])){
				$price = (float) preg_replace('/[^0-9.-]/', '', $arElem['PROPERTY_PRICE_VALUE']);

				$totalPrice += $price;
			}
		}
	
		if(!empty($arShopsId)){
			$rsElem = CIBlockElement::GetList(
				[],
				[
					'IBLOCK_ID' => 6,
					'ID' => $arShopsId,
					'ACTIVE' => 'Y'
				],
				false,
				false,
				['ID', 'NAME', 'PROPERTY_PHONE', 'PROPERTY_SECTION']
			);
		
			while($arElem = $rsElem->GetNext()){
				$arShops[$arElem['ID']] = $arElem;
			}
		}

		$productsTable .= '
			<table style="border-collapse: collapse; border: none; width: 600px;">
				<tr>
					<th colspan="2" style="padding: 5px 10px 5px 0; border-bottom: 1px solid #c1c1c1; text-align: left;">Товар</th>
					<th style="padding: 5px 0 5px 10px; border-bottom: 1px solid #c1c1c1; text-align: left;">Цена</th>
				</tr>
		';

		foreach($arProducts as $arItem){
			$productsTable .= '
				<tr>
					<td style="padding: 10px 10px 10px 0; border-bottom: 1px solid #c1c1c1;">
			';

			if(!empty($arItem['PREVIEW_PICTURE'])){
				$productsTable .= '<img src="https://kubatura.su'.$arItem['PREVIEW_PICTURE'].'" alt="'.$arItem['NAME'].'" style="width: 160px;">';
			}

			$productsTable .= '
					</td>
					<td style="padding: 10px; border-bottom: 1px solid #c1c1c1; font-size:12px;">
			';

			$productsTable .= '<a href="https://kubatura.su'.$arItem['DETAIL_PAGE_URL'].'" style="font-size: 16px; color: #0078bf">'.$arItem['NAME'].'</a>';

			if(!empty($arShops[$arItem['PROPERTY_SHOP_VALUE']])){
				$shop = $arShops[$arItem['PROPERTY_SHOP_VALUE']];

				$productsTable .= '<br>'.$shop['NAME'];

				if(!empty($shop['PROPERTY_SECTION_VALUE'])){
					$productsTable .= ', секция '.$shop['PROPERTY_SECTION_VALUE'];
				}

				if(!empty($shop['PROPERTY_PHONE_VALUE'])){
					$productsTable .= ', '.implode(', ', $shop['PROPERTY_PHONE_VALUE']);
				}
			}

			$productsTable .= '
					</td>
					<td style="padding: 10px 0 10px 10px; border-bottom: 1px solid #c1c1c1; font-size:16px">
			';

			$productsTable .= $arItem['PROPERTY_PRICE_VALUE'].'&nbsp;₽';

			$productsTable .= '
					</td>
				</tr>
			';
		}

		$productsTable .= '
			</table>
		';

		$productsTable .= '<p><b>Итого: '.$totalPrice.' ₽</b></p>';

		/*
		$rsElem = CIBlockElement::GetList(
			[],
			[
				'ID' => $productId,
				'ACTIVE' => 'Y',
			],
			false,
			false,
			['ID', 'NAME', 'PROPERTY_PRICE', 'PROPERTY_OLD_PRICE']
		);
	
		while($arElem = $rsElem->getNext()){
			$arProducts[] = $arElem['NAME'].' - '.$arElem['PROPERTY_PRICE_VALUE'].' ₽';
		}
		*/

		if(!empty($arProducts)){
			Event::sendImmediate([
				'EVENT_NAME' => 'SEND_PRODUCTS',
				'LID' => 's1',
				'C_FIELDS' => [
					'EMAIL' => $email,
					'PRODUCTS' => $productsTable
				]
			]);

			$success = true;
		}
	}
}

echo json_encode([
	'post' => $arProducts,
	'success' => $success,
]);