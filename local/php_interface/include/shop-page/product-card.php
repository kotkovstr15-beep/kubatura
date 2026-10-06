<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Kubatura\ShopPage\Service as S;
$picture=$product['PREVIEW_PICTURE'];
// GetNext() already HTML-escaped NAME; decode once so S::e() does not double-escape (&amp;quot;).
$productName=htmlspecialchars_decode((string)$product['NAME'],ENT_QUOTES);
?>
<div class="similar-item shop-page__product">
<a class="shop-page__product-image" href="<?=S::e($product['DETAIL_PAGE_URL'])?>"><img src="<?=S::e(S::image($picture,520,360))?>" width="520" height="360" loading="lazy" alt="<?=S::e($productName)?>"></a>
<div class="similar-item__content">
<h3 class="similar-item__head"><a href="<?=S::e($product['DETAIL_PAGE_URL'])?>"><?=S::e($productName)?></a></h3>
<div class="price">
<?php if($old=S::value($product,'SALE_PRICE')): ?><p class="price__old"><?=S::e($old)?> ₽</p><?php endif ?>
<?php if($price=S::value($product,'PRICE')): ?><p class="price__new"><?=S::e($price)?> ₽</p><?php endif ?>
</div></div></div>