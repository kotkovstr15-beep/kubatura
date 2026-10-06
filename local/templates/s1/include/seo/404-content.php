<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<section class="s-error s-padding" aria-labelledby="not-found-title">
    <div class="container">
        <div class="s-error__content">
            <div class="s-error__img">
                <img src="<?=SITE_TEMPLATE_PATH?>/img/404.png" width="870" height="390" loading="eager" alt="Ошибка 404 — страница не найдена">
            </div>
            <h1 id="not-found-title">Страница не найдена</h1>
            <p>Попробуйте найти нужный раздел через поиск или перейдите по одной из ссылок.</p>
            <form action="/search/" method="get" role="search" class="not-found-search">
                <label for="not-found-query" class="visually-hidden">Поиск по сайту</label>
                <input id="not-found-query" type="search" name="q" required placeholder="Что вы ищете?">
                <button type="submit" class="btn">Найти</button>
            </form>
            <nav class="not-found-links" aria-label="Полезные разделы">
                <a href="/catalog/">Каталог</a>
                <a href="/shops/">Магазины</a>
                <a href="/all_news/">Акции</a>
                <a href="/kontakty/">Контакты</a>
            </nav>
            <a href="/" class="btn btn_big">На главную</a>
        </div>
    </div>
</section>
