<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);

Loader::includeModule('iblock');

$arPhotoID = array();

if ($arResult['DETAIL_PICTURE']['ID']) {
    $arPhotoID[] = $arResult['DETAIL_PICTURE']['ID'];
} else {
    if ($arResult['PREVIEW_PICTURE']['ID']) {
        $arPhotoID[] = $arResult['PREVIEW_PICTURE']['ID'];
    }
}

if ($arResult['PROPERTIES']['MORE_PHOTO']['VALUE']) {
    foreach ($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $photo) {
        $arPhotoID[] = $photo;
    }
}

$arPhoto = array(
    'BIG' => array(),
    'THUMB' => array(),
);
foreach ($arPhotoID as $id) {
    $file = CFile::ResizeImageGet($id, array('width' => 1000, 'height' => 1000), BX_RESIZE_IMAGE_PROPORTIONAL, false,
        array(), false, 100);
    if (!$file['src']) {
        $file['src'] = CFile::GetPath($id);
    }

    $arPhoto['BIG'][] = $file['src'];

    $file = CFile::ResizeImageGet($id, array('width' => 80, 'height' => 80), BX_RESIZE_IMAGE_PROPORTIONAL, false,
        array(), false, 100);
    if (!$file['src']) {
        $file['src'] = CFile::GetPath($id);
    }

    $arPhoto['THUMB'][] = $file['src'];
}

$arSort = array('ID' => 'ASC');
$arFilter = array(
    '=ID' => $arResult["PROPERTIES"]["SHOP"]["VALUE"],
    'IBLOCK_ID' => $arResult["PROPERTIES"]["SHOP"]['LINK_IBLOCK_ID']
);
$arSelect = array();
$res = CIBlockElement::getList($arSort, $arFilter, false, array('nTopCount' => 1), $arSelect);
if ($ob = $res->GetNextElement()):
    $arFieldsShop = $ob->GetFields();
    $arPropsShop = $ob->GetProperties();
endif;

if (count($arPhotoID) < 1) {
    if ($arPropsShop['LOGO_2']['VALUE']) {
        $id = $arPropsShop['LOGO_2']['VALUE'];
        $file = CFile::ResizeImageGet($id, array('width' => 400, 'height' => 400), BX_RESIZE_IMAGE_PROPORTIONAL, false,
            array(), false, 100);
        if (!$file['src']) {
            $file['src'] = CFile::GetPath($id);
        }

        $arPhoto['BIG'][] = $file['src'];

        $file = CFile::ResizeImageGet($id, array('width' => 80, 'height' => 80), BX_RESIZE_IMAGE_PROPORTIONAL, false,
            array(), false, 100);
        if (!$file['src']) {
            $file['src'] = CFile::GetPath($id);
        }

        $arPhoto['THUMB'][] = $file['src'];
    }
}
unset($arPhotoID);
unset($id);

$shopActive = isset($arFieldsShop['ACTIVE']) && $arFieldsShop['ACTIVE'] === 'Y';
$altImg = trim($arResult['NAME'] . ' ' . (isset($arFieldsShop['NAME']) ? $arFieldsShop['NAME'] : ''));
?>
<div class="product-box">
	<div class="product-gallery swiper-container">
		<div class="labels">
			<? if ($arResult["PROPERTIES"]["NEW"]["VALUE"]): ?>
				<div class="label label_blue">
					<div class="label__wrap">Новинка</div>
				</div>
			<? endif; ?>
			<? if ($arResult["PROPERTIES"]["SALE"]["VALUE"]): ?>
				<div class="label">
					<div class="label__wrap"><?= $arResult["PROPERTIES"]["SALE"]["VALUE"] ?></div>
				</div>
			<? endif; ?>
		</div>
		<div class="swiper-wrapper">

			<? foreach ($arPhoto['BIG'] as $src): ?>
				<div class="product-gallery__item swiper-slide">
					<div class="product-gallery__img img-box">
						<img src="<?=htmlspecialcharsbx($src)?>" width="1000" height="1000" alt="<?=htmlspecialcharsbx($altImg)?>">
					</div>
				</div>
			<? endforeach; ?>

		</div>
		<div class="swiper-pagination"></div>
	</div>
	<div class="product-thumbs swiper-container">
		<div class="swiper-wrapper">

			<? foreach ($arPhoto['THUMB'] as $src): ?>
				<div class="product-thumbs__item swiper-slide">
					<div class="product-thumbs__img img-box">
						<img src="<?=htmlspecialcharsbx($src)?>" width="80" height="80" loading="lazy" alt="<?=htmlspecialcharsbx($altImg)?>">
					</div>
				</div>
			<? endforeach; ?>

		</div>
	</div>
	<div class="product-info">
		<div class="product-info__first">
			<div class="zag4"><?=\Kubatura\ShopPage\Service::shopLabel(isset($arFieldsShop)?$arFieldsShop:array("NAME"=>""))?></div>
			<div class="post-place">
				<? if ($arPropsShop['CORPUS']["VALUE"]): ?>
					<span>Корпус <?= $arPropsShop['CORPUS']["VALUE"] ?></span>
				<? endif; ?>
				<? if ($arPropsShop['ETAG']["VALUE"]): ?>
					<span>Этаж <?= $arPropsShop['ETAG']["VALUE"] ?></span>
				<? endif; ?>
				<? if ($arPropsShop['SECTION']["VALUE"]): ?>
					<span>Секция <?= $arPropsShop['SECTION']["VALUE"] ?></span>
				<? endif; ?>
			</div>
			<? if ($arPropsShop['PHONE']["VALUE"]):
				?>
				<div class="phones phones_small">
					<i class="phones__icon ic ic-phone"></i>
					<?
					$arPhone = array();
					foreach ($arPropsShop['PHONE']["VALUE"] as $v):
						$vv = preg_replace('/[^0-9]/', '', $v);
						$arPhone[] = '<a href="tel:' . $vv . '">' . $v . '</a>';
					endforeach;
					echo implode("<br />", $arPhone); ?>
				</div>
			<? endif; ?>
		</div>
  <div class="product-info__second">
			<div class="product-info__main">
				<div class="product-info__title">
					<h1 class="product-info__head_z1"><?= $arResult["NAME"] ?></h1>
					<?
					$avail = '<p class="avail"><i class="ic ic-check-full"></i> В наличии</p>';
					if ($arResult["PROPERTIES"]['NAL']["VALUE"]):
						$avail = '<p class="avail avail_gray"><i class="ic ic-clock-full"></i> Под заказ</p>';
					endif;
					if ($shopActive) echo $avail;
					?>
				</div>
										<ul class="product-info__list">
											<?foreach($arResult["PROPERTIES"] as $pid=>$arProperty):?>
<?if($arProperty["CODE"] == "PRICE" || $arProperty["CODE"] == "SALE_PRICE" || $arProperty["CODE"] == "SHOP" || $arProperty["CODE"] == "NAL"
|| $arProperty["CODE"] == "MORE_PHOTO"|| $arProperty["CODE"] == "NEW"|| $arProperty["CODE"] == "SALE"|| $arProperty["CODE"] == "RECOMMEND"|| $arProperty["CODE"] == "MORE_PHOTO"|| $arProperty["CODE"] == "MORE_PHOTO"
|| $arProperty["CODE"] == "FILTER_PRICE" || $arProperty["CODE"] == "RECOMEND" || $arProperty["CODE"] == "SPECIALOFFER"
|| $arProperty["CODE"] == "FEATURE" || $arProperty["CODE"] == "IS_EXPORT" || $arProperty["CODE"] == "MAGAZIN_ATR_ID"|| $arProperty["CODE"] == "MAGAZIN_ATR_URL"):?>

<? else: ?>
<?if($arProperty["VALUE"]):?>
<li><b><?echo $arProperty["NAME"];?>:</b> <?echo $arProperty["VALUE"];?></li>
<? endif; ?>
<? endif; ?>
<?endforeach;?>
												</ul>

				<div class="price">
											<?if($arResult["PROPERTIES"]['SALE_PRICE']["VALUE"]):?>
					<p class="price__old"><?= $arResult["PROPERTIES"]['SALE_PRICE']["VALUE"] ?> ₽</p>
												<? endif; ?>
					<p class="price__new l"><?= $arResult["PROPERTIES"]['PRICE']["VALUE"] ?> ₽</p>
					<? $isCatalog = CSite::InDir('/catalog/'); if($isCatalog && $shopActive):?>
					<a href="<?= $arFieldsShop['DETAIL_PAGE_URL'] ?>" class="btn btn_small r">Уточнить цену,</br> возможна скидка</a>
					<? endif; ?>
				</div>
				<?php if ($shopActive): ?><span class="btn btn_middle_size btn_buy" data-item-id="<?= $arResult['ID'] ?>">Добавить в корзину</span>
				<span class="btn btn_middle_size btn_in-cart" data-item-id="<?= $arResult['ID'] ?>">Товар в корзине</span>
				<?php endif ?><span class="btn btn_middle_size btn_delete" data-item-id="<?= $arResult['ID'] ?>"
					  title="Убрать из корзины">Убрать из корзины</span>
			</div>
			<div class="product-info__descr">
				<div class="product-info__descr-wrap">
					<div class="content"><?= $arResult["DETAIL_TEXT"] ?></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="product-bottom">
	<?php if ($shopActive): ?><a href="<?=htmlspecialcharsbx($arFieldsShop['DETAIL_PAGE_URL'])?>" class="product-bottom__btn">Каталог магазина <?=htmlspecialcharsbx($arFieldsShop['NAME'])?></a><?php else: ?><span><?=htmlspecialcharsbx(isset($arFieldsShop['NAME'])?$arFieldsShop['NAME']:'')?></span><?php endif ?>
</div>

<?
$arSort = array('RAND' => 'ASC');
$arFilter = array(
    '!ID' => $arResult["ID"],
    'IBLOCK_ID' => $arResult["IBLOCK_ID"],
    "ACTIVE" => "Y",
    "INCLUDE_SUBSECTIONS" => "Y",
    'SECTION_ID' => $arResult["SECTION"]['ID']
);
$arSelect = array();
$res = CIBlockElement::getList($arSort, $arFilter, false, array('nTopCount' => 4), $arSelect);

$arAnalogProduct = array();
if (intval($res->SelectedRowsCount()) > 0):
    $res->SetUrlTemplates($arParams["DETAIL_URL"]);
    while ($ob = $res->GetNextElement()):
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();

        $arAnalogProduct[] = array(
            'NAME' => $arFields['NAME'],
            'DETAIL_PAGE_URL' => $arFields['DETAIL_PAGE_URL'],
            'DETAIL_PICTURE' => $arFields['DETAIL_PICTURE'],
            'PREVIEW_PICTURE' => $arFields['PREVIEW_PICTURE'],
            'SHOP' => $arProps['SHOP']['VALUE'],
            'SALE_PRICE' => $arProps['SALE_PRICE']['VALUE'],
            'PRICE' => $arProps['PRICE']['VALUE'],
            'NEW' => $arProps['NEW']['VALUE'],
            'SALE' => $arProps['SALE']['VALUE'],
        );
    endwhile;
else:
    $res = CIBlockSection::GetByID($arResult["SECTION"]['ID']);
    if ($ar_res = $res->fetch()) {
        if ($ar_res["IBLOCK_SECTION_ID"]) {
            $arFilter = array(
                '!ID' => $arResult["ID"],
                'IBLOCK_ID' => $arResult["IBLOCK_ID"],
                "ACTIVE" => "Y",
                "INCLUDE_SUBSECTIONS" => "Y",
                'SECTION_ID' => $ar_res["IBLOCK_SECTION_ID"]
            );
            $res = CIBlockElement::getList($arSort, $arFilter, false, array('nTopCount' => 3), $arSelect);
            if (intval($res->SelectedRowsCount()) > 0):
                $res->SetUrlTemplates($arParams["DETAIL_URL"]);
                while ($ob = $res->GetNextElement()):
                    $arFields = $ob->GetFields();
                    $arProps = $ob->GetProperties();

                    $arAnalogProduct[] = array(
                        'NAME' => $arFields['NAME'],
                        'DETAIL_PAGE_URL' => $arFields['DETAIL_PAGE_URL'],
                        'DETAIL_PICTURE' => $arFields['DETAIL_PICTURE'],
                        'PREVIEW_PICTURE' => $arFields['PREVIEW_PICTURE'],
                        'SHOP' => $arProps['SHOP']['VALUE'],
                        'NEW' => $arProps['NEW']['VALUE'],
                        'SALE' => $arProps['SALE']['VALUE'],
                        'SALE_PRICE' => $arProps['SALE_PRICE']['VALUE'],
                        'PRICE' => $arProps['PRICE']['VALUE'],
                    );
                endwhile;
            endif;
        }
    }
endif;

if (count($arAnalogProduct) > 0) :

    $arShopInfo = array();
    foreach ($arAnalogProduct as $item) {
        $arShopInfo[$item['SHOP']] = array();
    }

    $arSort = array('ID' => 'ASC');
    $arFilter = array(
        'ID' => array_keys($arShopInfo),
        'IBLOCK_ID' => $arResult["PROPERTIES"]["SHOP"]['LINK_IBLOCK_ID']
    );
    $arSelect = array();
    $res = CIBlockElement::getList($arSort, $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()):
        $arShop = $ob->GetFields();
        $arShopInfo[$arShop['ID']] = array(
            'FIELD' => $arShop,
            'PROP' => $ob->GetProperties()
        );
    endwhile;

    ?>
    <div class="line">
    <section class="similar s-margin">
        <div class="container">

            <div class="title title_center">
                <div class="title__grid">
                    <div class="title__main">
                        <div class="zag2">Похожие товары</div>
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

                    <? foreach ($arAnalogProduct as $item): ?>

                        <div class="swiper-slide">
                            <div class="similar-item">
                                <div class="labels">
                                    <? if ($item['NEW']): ?>
                                        <div class="label label_blue">
                                            <div class="label__wrap">Новинка</div>
                                        </div>
                                    <? endif; ?>
                                    <? if ($item['SALE']): ?>
                                        <div class="label">
                                            <div class="label__wrap"><?= $item['SALE'] ?></div>
                                        </div>
                                    <? endif; ?>
                                </div>
                                <a href="<?=htmlspecialcharsbx(rtrim($item['DETAIL_PAGE_URL'], '/'))?>" class="similar-item__img"><?

                                    $fileID = '';
                                    $file = array();
                                    if ($item['PREVIEW_PICTURE']) {
                                        $fileID = $item['PREVIEW_PICTURE'];
                                    } else {
                                        if ($item['DETAIL_PICTURE']) {
                                            $fileID = $item['DETAIL_PICTURE'];
                                        } else {
                                            if ($arShopInfo[$item['SHOP']]) {
                                                $fileID = $arShopInfo[$item['SHOP']]['PROP']['LOGO_2']['VALUE'];
                                            }
                                        }
                                    }

                                    if (!empty($fileID)) {
                                        $file = CFile::ResizeImageGet($fileID, array('width' => 400, 'height' => 280),
                                            BX_RESIZE_IMAGE_PROPORTIONAL, false, array(), false, 100);
                                        if (!$file['src']) {
                                            $file['src'] = CFile::GetPath($fileID);
                                        }
                                    }

                                    ?>
                                    <?if(!empty($file['src'])):?>
                                        <div><img src="<?=htmlspecialcharsbx($file['src'])?>" width="400" height="280" loading="lazy" alt="<?=htmlspecialcharsbx(trim($item['NAME'] . ' ' . $arShopInfo[$item['SHOP']]['FIELD']['NAME']))?>"></div>
                                    <?endif;?>
                                </a>
                                <div class="similar-item__content">
                                    <p class="similar-item__category"><?=\Kubatura\ShopPage\Service::shopLabel(isset($arShopInfo[$item['SHOP']]['FIELD'])?$arShopInfo[$item['SHOP']]['FIELD']:array('NAME'=>''))?>
                                    </p>
									<div class="zag5">
									<div class="similar-item__head">
												<span class="similar-item__head-wrap">
											<a href="<?=htmlspecialcharsbx(rtrim($item['DETAIL_PAGE_URL'], '/'))?>"
                                                       class="link-head"><?= $item['NAME'] ?></a>
												</span>
                                    </div>
									</div>
                                    <div class="price">
                                        <?if($item['SALE_PRICE']):?><p class="price__old"><?= $item['SALE_PRICE'] ?> ₽</p><? endif;?>
                                        <p class="price__new"><?= $item['PRICE'] ?> ₽</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <? endforeach; ?>

                </div>
            </div>

        </div>
    </section>
  </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.similar').each(function (index) {
                $(this).attr('data-index', index);

                new Swiper('[data-index="' + index + '"] .similar__slider', {
                    slidesPerView: 4,
                    centerInsufficientSlides: true,
                    navigation: {
                        prevEl: '[data-index="' + index + '"] .similar-nav .swiper-button-prev',
                        nextEl: '[data-index="' + index + '"] .similar-nav .swiper-button-next',
                    },
                    breakpoints: {
                        1699: {
                            slidesPerView: 3,
                        },
                        767: {
                            freeMode: true,
                            slidesPerView: 'auto',
                        },
                    }
                });
            });
        });
    </script>
<? endif; ?>

<div class='js_viewed_product_box' data-id="<?= $arResult['ID'] ?>" data-path="<?= SITE_TEMPLATE_PATH ?>">

</div>

<script>
    $(document).ready(function () {
        const itemId = Number.parseInt($('.btn_buy').eq(0).attr('data-item-id'));
        chooseButton();
        $(".btn_buy").on("click", function () {
            const itemId = Number.parseInt($(this).attr('data-item-id'));
            cart.AddItem(itemId);
            chooseButton();
        });
        $(".btn_delete").on("click", function () {
            const itemId = Number.parseInt($(this).attr('data-item-id'));
            cart.deleteItem(itemId);
            chooseButton();
        });
        const intervalID = setInterval(chooseButton, 1000);
        const oldIntervalID = localStorage.getItem('intervalId');
        if (oldIntervalID !== undefined) {
            clearInterval(oldIntervalID);
        }
        localStorage.removeItem('intervalId');
        localStorage.setItem('intervalId', intervalID);

        function chooseButton() {
            if (cart.itemInCart(itemId)) {
                $('.btn_delete').show();
                $('.btn_in-cart').show();
                $('.btn_buy').hide();
            } else {
                $('.btn_delete').hide();
                $('.btn_in-cart').hide();
                $('.btn_buy').show();
            }
        }
    });
</script>

<?php
$schemaHost = isset($_SERVER['HTTP_HOST']) ? preg_replace('/[^a-z0-9.:-]/i', '', $_SERVER['HTTP_HOST']) : 'kubatura.su';
$schemaBase = (\CMain::IsHTTPS() ? 'https://' : 'http://') . ($schemaHost ?: 'kubatura.su');
$schemaUrl = $schemaBase . '/' . ltrim((string)$arResult['DETAIL_PAGE_URL'], '/');
$schemaImages = array();
foreach ($arPhoto['BIG'] as $imagePath) {
    if (!$imagePath) {
        continue;
    }
    $schemaImages[] = preg_match('#^https?://#i', $imagePath)
        ? $imagePath
        : $schemaBase . '/' . ltrim($imagePath, '/');
}
$schemaPrice = str_replace(',', '.', (string)$arResult['PROPERTIES']['PRICE']['VALUE']);
$schemaPrice = preg_replace('/[^0-9.]/', '', $schemaPrice);
$productSchema = array(
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    '@id' => $schemaUrl . '#product',
    'name' => (string)$arResult['NAME'],
    'image' => array_values(array_unique($schemaImages)),
    'url' => $schemaUrl,
    'offers' => array(
        '@type' => 'Offer',
        'url' => $schemaUrl,
        'priceCurrency' => 'RUB',
        'price' => $schemaPrice,
        'availability' => !empty($arResult['PROPERTIES']['NAL']['VALUE'])
            ? 'https://schema.org/BackOrder'
            : 'https://schema.org/InStock',
        'itemCondition' => 'https://schema.org/NewCondition',
    ),
);
if (!empty($arFieldsShop['NAME'])) {
    $productSchema['brand'] = array('@type' => 'Brand', 'name' => (string)$arFieldsShop['NAME']);
}
if (!empty($arResult['DETAIL_TEXT'])) {
    $productSchema['description'] = trim(strip_tags($arResult['DETAIL_TEXT']));
}
?>
<script type="application/ld+json"><?=json_encode($productSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?></script>
