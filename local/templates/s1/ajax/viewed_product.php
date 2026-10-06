<?

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

$application = Application::getInstance();
$context = $application->getContext();
$request = $context->getRequest();

if(!$request->isPost())
	die();
if(!$request->isAjaxRequest())
	die();
if(!\Bitrix\Main\Loader::includeModule('iblock'))
	die();

function setCookies($name,$value,$request,$context){
	$cookie = new Cookie($name, base64_encode($value), time() + 60*60*24*60);
	$cookie->setDomain($context->getServer()->getHttpHost());
	if(!$request->isHttps()) {
		$cookie->setHttpOnly(false);
		$cookie->setSecure(false);
	}
	$context->getResponse()->addCookie($cookie);
	$context->getResponse()->flush("");
}

$VIEWED_PRODUCT = $request -> getCookie("VIEWED_PRODUCT_CUSTOM");
if(!$VIEWED_PRODUCT) {
	$VIEWED_PRODUCT = array();
}
else
{
	$VIEWED_PRODUCT = explode(",", base64_decode($VIEWED_PRODUCT));
}

$id = $request->get("id");
if($id)
{
	$arSort = array('ID' => 'ASC');
	$arFilter = array('=ID' => $id, 'IBLOCK_ID' => 7, "ACTIVE"=>"Y");
	$arSelect = array('ID');
	$res = CIBlockElement::getList($arSort, $arFilter, false, array('nTopCount' => 1), $arSelect);
	if($ar = $res->fetch())
	{
		if(!in_array($ar['ID'],$VIEWED_PRODUCT))
		{
			$VIEWED_PRODUCT[] = $ar['ID'];
			setCookies('VIEWED_PRODUCT_CUSTOM',implode(",", $VIEWED_PRODUCT),$request,$context);
		}
	}

	$flipped = array_flip($VIEWED_PRODUCT);
	unset($flipped[$id]);
	if(!empty($VIEWED_PRODUCT)) {
		$VIEWED_PRODUCT=array_flip($flipped);
	}
}

foreach($VIEWED_PRODUCT as $k => $v) {
	$v = (int)$v;
	if($v > 0)
	{
		$VIEWED_PRODUCT[$k] = $v;
	}
	else
	{
		unset($VIEWED_PRODUCT[$k]);
	}
}




$count = count($VIEWED_PRODUCT);
if( $count > 0 )
{
	if($count > 10) {
		unset($VIEWED_PRODUCT[0]);
		setCookies('VIEWED_PRODUCT_CUSTOM',implode(",", $VIEWED_PRODUCT),$request,$context);
	}

	$arProduct = array();

	$arSort = array('ID' => 'ASC');
	$arFilter = array('=ID' => $VIEWED_PRODUCT, 'IBLOCK_ID' => 7, "ACTIVE"=>"Y");
	$arSelect = array();
	$res = CIBlockElement::getList($arSort, $arFilter, false, array('nTopCount' => 11), $arSelect);
	while($ob = $res->GetNextElement()):
		$arFields = $ob->GetFields();
		$arProps = $ob->GetProperties();

		if(in_array($arFields['ID'],$VIEWED_PRODUCT)) {
			$SHOP_LINK_IBLOCK_ID = $arProps['SHOP']['LINK_IBLOCK_ID'];
			$arProduct[$arFields['ID']] = array(
				'NAME'                => $arFields['NAME'],
				'DETAIL_PAGE_URL'     => $arFields['DETAIL_PAGE_URL'],
				'DETAIL_PICTURE'      => $arFields['DETAIL_PICTURE'],
				'PREVIEW_PICTURE'     => $arFields['PREVIEW_PICTURE'],
				'SHOP'                => $arProps['SHOP']['VALUE'],
				'SHOP_LINK_IBLOCK_ID' => $arProps['SHOP']['LINK_IBLOCK_ID'],
				'SALE_PRICE'          => $arProps['SALE_PRICE']['VALUE'],
				'PRICE'               => $arProps['PRICE']['VALUE'],
				'NEW'                 => $arProps['NEW']['VALUE'],
				'SALE'                => $arProps['SALE']['VALUE'],
			);
		}
	endwhile;

	if( count($arProduct) > 0 ) :
		$arShopInfo = array();
		foreach($arProduct as $item) {
			$arShopInfo[$item['SHOP']] = array();
		}

		$arSort = array('ID' => 'ASC');
		$arFilter = array('ID' => array_keys($arShopInfo), 'IBLOCK_ID' => $SHOP_LINK_IBLOCK_ID);
		$arSelect = array();

		$res = CIBlockElement::getList($arSort, $arFilter, false, false, $arSelect);
		while($ob = $res->GetNextElement()):
			$arShop = $ob->GetFields();
			$arShopInfo[$arShop['ID']] = array(
				'FIELD' => $arShop,
				'PROP'  => $ob->GetProperties()
			);
		endwhile;
		?>
				<section class="similar s-margin">
					<div class="container">

						<div class="title title_center">
							<div class="title__grid">
								<div class="title__main">
									<h2>Просмотренные товары</h2>
								</div>
								<div class="title__nav">
									<div class="similar-nav swiper-nav">
										<div class="swiper-button-prev"><i class="ic ic-prev"></i></div>
										<div class="swiper-button-next"><i class="ic ic-next"></i></div>
									</div>
								</div>
							</div>
						</div>

						<div class="similar__slider swiper-container">
							<div class="swiper-wrapper">

							<?foreach($arProduct as $item):?>

								<div class="swiper-slide">
									<div class="similar-item">
										<div class="labels">
											<?if($item['NEW']):?>
											<div class="label label_blue">
												<div class="label__wrap">Новинка</div>
											</div>
											<?endif;?>
											<?if($item['SALE']):?>
											<div class="label">
												<div class="label__wrap"><?=$item['SALE']?></div>
											</div>
											<?endif;?>
										</div>
										<a href="<?=htmlspecialcharsbx(rtrim($item['DETAIL_PAGE_URL'], '/'))?>" class="similar-item__img"><?

											$fileID = '';
											$file = array();
											if($item['PREVIEW_PICTURE']){
												$fileID = $item['PREVIEW_PICTURE'];
											}
											else if($item['DETAIL_PICTURE']){
												$fileID = $item['DETAIL_PICTURE'];
											}
											else if($arShopInfo[$item['SHOP']]){
												$fileID = $arShopInfo[$item['SHOP']]['PROP']['LOGO_2']['VALUE'];
											}

											if(!empty($fileID))
											{
												$file = CFile::ResizeImageGet($fileID, array('width'=>400, 'height'=>280), BX_RESIZE_IMAGE_PROPORTIONAL, false, array(), false, 100);
												if(!$file['src'])
													$file['src'] = CFile::GetPath($fileID);
											}

										?>
											<?if(!empty($file['src'])):?>
											<div><img src="<?=htmlspecialcharsbx($file['src'])?>" width="400" height="280" loading="lazy" alt="<?=htmlspecialcharsbx(trim($item['NAME'] . ' ' . $arShopInfo[$item['SHOP']]['FIELD']['NAME']))?>"></div>
											<?endif;?>
										</a>
										<div class="similar-item__content">
											<p class="similar-item__category"><a href="<?=$arShopInfo[$item['SHOP']]['FIELD']['DETAIL_PAGE_URL']?>"><?=$arShopInfo[$item['SHOP']]['FIELD']['NAME']?></a></p>
											<h5 class="similar-item__head">
												<span class="similar-item__head-wrap">
													<a href="<?=htmlspecialcharsbx(rtrim($item['DETAIL_PAGE_URL'], '/'))?>" class="link-head"><?=$item['NAME']?></a>
												</span>
											</h5>
											<div class="price">
												<?if($item['SALE_PRICE']):?><p class="price__old"><?=$item['SALE_PRICE']?> ₽</p><?endif;?>
												<p class="price__new"><?=$item['PRICE']?> ₽</p>
											</div>
										</div>
									</div>
								</div>

							<?endforeach;?>

							</div>
						</div>

					</div>
				</section>
		<?
	else:
		setCookies('VIEWED_PRODUCT_CUSTOM','',$request,$context);
	endif;
}
?>
