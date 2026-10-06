<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}


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

$this->setFrameMode(false);

$totalPrice = 0;
?>
<div class="cart-box main_container">
    <div class="cart-box__head">
        <p>Товар</p>
        <p>Цена</p>
    </div>
    <div class="cart-box__wrap">
        <? foreach ($arResult['ITEMS'] as $item) : ?>
			<?
			if(!empty($item['PRICE'])){
				$price = (float) preg_replace('/[^0-9.-]/', '', $item['PRICE']);

				$totalPrice += $price;
			}
			?>
            <div class="cart-item" data-item-id="<?= $item['ID'] ?>" data-price="<?=$item['PRICE']?>">
                <?php
                $fileID = '';
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
                <a href="<?= $item['DETAIL_PAGE_URL'] ?>" class="cart-item__img"
                   style="background-image: url('<?= $file['src'] ?>">
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
                </a>
                <div class="cart-item__info">
                    <p class="cart-item__category"><a
                                href="<?= $arResult['SHOP'][$item['SHOP']]['FIELD']['DETAIL_PAGE_URL'] ?>"><?= $arResult['SHOP'][$item['SHOP']]['FIELD']['NAME'] ?></a>
                    </p>
                    <h5 class="cart-item__head"><?= $item['NAME'] ?></h5>
                    <p class="cart-item__category">
                        <a href="<?= $item['SHOP_INFO']['FIELD']['DETAIL_PAGE_URL'] ?>">
                            <span><?= $item['SHOP_INFO']['FIELD']['NAME'] ?></span>
                        </a>

						<?if(!empty($item['SHOP_INFO']['PROP']['SECTION']['VALUE'])):?>
							, СЕКЦИЯ <?=$item['SHOP_INFO']['PROP']['SECTION']['VALUE']?>
						<?endif?>

						<?if(!empty($item['SHOP_INFO']['PROP']['PHONE']['VALUE'])):?>
							, <?=implode(', ', $item['SHOP_INFO']['PROP']['PHONE']['VALUE'])?>
						<?endif?>
                    </p>
                    <!--<ul class="cart-specs">
                        <li><b>Код:</b> 2021431364</li>
                    </ul>-->

                </div>
                <div class="cart-item__w-100"></div>
                <div class="cart-item__price">
                    <div class="price">
						<?if(!empty($item['SALE_PRICE'])):?>
                           <p class="price__old"><?= $item['SALE_PRICE'] ?> ₽</p>
						<?endif?>
                            <p class="price__new"><?= $item['PRICE'] ?> ₽</p>
                    </div>
                    <?
                    $avail = '<p class="avail"><i class="ic ic-check-full"></i> В наличии</p>';
                    if ($item['NAL']):
                        $avail = '<p class="avail avail_gray"><i class="ic ic-clock-full"></i> Под заказ</p>';
                    endif;
                    echo $avail;
                    ?>
                </div>
                <div class="cart-item__w-100"></div>
                <a href="#" class="cart-item__del"><i class="ic ic-bin"></i></a>
            </div>
        <? endforeach; ?>
    </div>

	<div class="cart-box__total">
		Итого: <span><?=$totalPrice?></span> ₽
	</div>

    <div class="cart-box__action">
        <div class="cart-box__action-col">
            <div class="cart-box__action-btns">
                <a href="#" class="btn send_email">отправить на e-mail</a>
				<button class="btn btn_bd print">распечатать</button>
				<?/*<a href="/generatePDF.php" target="_blank" class="btn btn_bd">распечатать</a>*/?>
            </div>
        </div>
        <div class="cart-box__action-col">
            <div class="cart-box__action-btns">
                <a href="#" class="btn btn_bd btn_red clear_all_items">очистить список</a>
            </div>
        </div>
    </div>
</div>
<div class="empty_container">Ваш cписок покупок пуст.</div>
