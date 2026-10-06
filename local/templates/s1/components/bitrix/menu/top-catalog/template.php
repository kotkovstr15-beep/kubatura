<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

				<nav class="cat-menu">
					<div class="cat-menu-mob">
						<div class="container">
							<div class="cat-menu-mob__grid">
								<button class="cat-menu-mob__toggle"><i class="ic ic-chevron-bottom"></i> Категории</button>
								<a href="#" class="cart-btn">
									Корзина
									<span class="cart-btn__icon">
										<i class="ic ic-cart"></i>
										<span class="cart-btn__count">0</span>
									</span>
								</a>
							</div>
						</div>
					</div>
					<div id="cat-menu" class="cat-menu__wrapper">
						<div class="cat-menu__wrap">
							<div class="cat-menu__grid">
								<div class="cat-menu-main">
									<div class="container">
										<ul class="cat-menu-main__list">
										<?foreach($arResult as $arItem):?>
											<?if($arItem["SELECTED"]):?>
												<li><a href="<?=$arItem["LINK"]?>" class="selected"><?=$arItem["TEXT"]?></a></li>
											<?else:?>
												<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
											<?endif?>
										<?endforeach;?>
										</ul>
									</div>
								</div><!-- .cat-menu-main -->
								<div class="cat-menu-dropdowns">
									<?foreach($arResult as $arItem):?>
									<div class="cat-menu-dropdown">
										<div class="container">
											<ul class="cat-menu-dropdown__list">
												<?foreach($arItem["CHILDREN"] as $arChild):?>
													<?if($arChild["SELECTED"]):?>
														<li><a href="<?=$arChild["LINK"]?>" class="selected"><?=$arChild["TEXT"]?></a></li>
													<?else:?>
														<li><a href="<?=$arChild["LINK"]?>"><?=$arChild["TEXT"]?></a></li>
													<?endif?>
												<?endforeach;?>
											</ul>
										</div>
									</div>
									<?endforeach;?>
								</div><!-- .cat-menu-dropdowns -->
							</div>
						</div>
					</div>
				</nav>
<?endif?>