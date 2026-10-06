<?global $USER;?>
<header class="header js-header-height">
    <div class="header__wrapper js-header-height">
        <div class="container">
            <div class="header__grid">
                <div class="header__logo">
                    <a href="/" class="logo">
                        <?$siteLogoSrc = kubaturaWebpUrl(SITE_TEMPLATE_PATH . '/img/logo.png', 64);?>
                        <?$siteLogoMobileSrc = kubaturaWebpUrl(SITE_TEMPLATE_PATH . '/img/logo-mobile.png', 64);?>
                        <picture>
                            <source media="(max-width: 1199px)" srcset="<?=htmlspecialcharsbx($siteLogoMobileSrc)?>" width="193" height="53">
                            <img src="<?=htmlspecialcharsbx($siteLogoSrc)?>" width="290" height="79" alt="Кубатура, квартал дизайна и интерьера">
                        </picture>
                    </a>
                </div>

                <p class="header__clock">
                    <i class="ic ic-clock"></i>
                    <? $APPLICATION->IncludeFile(SITE_DIR . "include/working_hours_link.php", Array(),
                        Array("MODE" => "php")); ?>
                </p>
                <span class="togler"><span class="m3lr l"> Кубатура</span>
                <label class="switch l">
                  <input type="checkbox" >

                  <span class="slider round"></span>
                </label>
                <span class="l m3lr">LUX Кубатура</span></span>
                <a href="/cart/" class="header__cart cart-btn active">
                    Корзина
                    <span class="cart-btn__icon">
											<i class="ic ic-cart"></i>
											<span class="cart-btn__count">0</span>
										</span>
                </a>
                <ul class="header__icons">
                    <li><a href="/karta-tssr/" class="header__icon"><i class="ic ic-map"></i><span>Навигация</span></a>
                    </li>
                    <li><a href="#" class="header__icon header__icon-pc" aria-label="Открыть поиск по сайту"><i
                                    class="ic ic-search search"></i><span>Поиск</span></a></li>
                    <li><a href="/kak-dobratsya/" class="header__icon"><i
                                    class="ic ic-route"></i><span>Контакты</span></a></li>
                    <?php if ($USER->GetID()): ?>
                        <li class="menu-profile">
                            <span class="header__icon">
                                <i class="img-account"></i>
                            </span>
                            <ul class="sub-menu">
                                <li class="menu-element">
                                    <a href="/personal/">
                                        <span>Профиль</span>
                                    </a>
                                </li>
                                <li class="menu-element">
                                    <a href="/?logout=yes">
                                        <span>Выйти</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                <button class="header__toggle sandwich">
                    <span class="sandwich__text">Меню</span>
                    <span class="sandwich__main">
											<span></span>
											<span></span>
											<span></span>
										</span>
                </button>
            </div>
        </div>
    </div>
</header>
