<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
				<?$this->SetViewTarget('cat_menu');?>
				<nav class="cat-menu">
					<div class="cat-menu-mob">
						<div class="container">
							<div class="cat-menu-mob__grid">
								<button class="cat-menu-mob__toggle"><i class="ic ic-chevron-bottom"></i> Категории</button>
								<a href="/cart/" class="cart-btn">
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
											<li><a href="<?=htmlspecialcharsbx(rtrim($arItem["SECTION_PAGE_URL"], '/').'/')?>"><?=$arItem["NAME"]?></a></li>
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
													<li><a href="<?=htmlspecialcharsbx(rtrim($arChild["SECTION_PAGE_URL"], '/').'/')?>"><?=$arChild["NAME"]?> <?=$arChild["ELEMENT_CNT"]?></a></li>
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
				<?$this->EndViewTarget();?> 
<?endif?>
