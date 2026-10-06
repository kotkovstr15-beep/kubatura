<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<section class="s-error s-padding" aria-labelledby="gone-title">
    <div class="container">
        <div class="s-error__content">
            <h1 id="gone-title">Товар снят с продажи</h1>
            <?php if (!empty($PRODUCT_NAME)): ?>
                <p>«<?=htmlspecialcharsbx($PRODUCT_NAME)?>» больше не продаётся.</p>
            <?php endif; ?>
            <p>Посмотрите актуальные предложения в каталоге или воспользуйтесь поиском.</p>
            <form action="/search/" method="get" role="search" class="not-found-search">
                <label for="gone-query" class="visually-hidden">Поиск по сайту</label>
                <input id="gone-query" type="search" name="q" required placeholder="Что вы ищете?">
                <button type="submit" class="btn">Найти</button>
            </form>
            <nav class="not-found-links" aria-label="Полезные разделы">
                <a href="/catalog/">Каталог</a>
                <a href="/shops/">Магазины</a>
                <a href="/all_news/">Акции</a>
                <a href="/kak-dobratsya/">Контакты</a>
            </nav>
            <a href="/catalog/" class="btn btn_big">В каталог</a>
        </div>
    </div>
</section>
