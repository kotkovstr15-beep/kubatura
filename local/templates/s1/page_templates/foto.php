<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>
								<?
								// блок 1 - старт
								// картинка - в правой части - можно удалить весь блок
								?>
								<div class="post-img">
									<div class="post-img__wrap" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/img/post/post-img-1.jpg');"></div>
								</div>
								<?
								// картинка - в правой части
								// блок 1 - конец
								?>
								
								<?
								// блок 2 - старт
								// левая колонка - можно удалить весь блок
								?>
								<div class="post-info">
									<a href="#" class="post-info__img">
										<img src="<?=SITE_TEMPLATE_PATH?>/img/post/post-1.png" loading="eager" alt="">
									</a>
									<div class="post-info__main">
										<h5 class="post-info__head"><a href="#">Antica Signoria</a></h5>
										<div class="post-place">
											<span>Корпус 1</span>
											<span>Этаж 2</span>
										</div>
										<a href="#" class="btn btn_little"><i class="ic ic-map"></i> На карте</a>
									</div>
									<div class="social">
										<div class="social__wrap">
											<a href="#" class="social-item ic ic-globe"></a>
											<a href="#" class="social-item ic ic-instagram"></a>
											<a href="#" class="social-item ic ic-vk"></a>
										</div>
									</div>
								</div>
								<?
								// левая колонка
								// блок 2 - конец
								?>
								
								<?
								// блок 3 - старт
								// основное содержимое страницы
								?>
								<div class="post-content">
									<div class="post-title">
										<p class="post-title__date">8 августа</p>
										<h2 class="post-title__head">Умные краски</h2>
									</div>
									<div class="content">
										<p><b>Страницы можно редактировать только в режиме правки php кода</b></p>
										<p><b>Правка в режиме html или виз редакторе - приведет к проблемам</b></p>
										<p><b><u>Пример страницы по умолчанию.</u></b></p>
										<p>13 апреля в ЦСР Кубатура салон-студия <a href="#">Интерио</a> представила необыкновенную продукцию лакокрасочных и декоративных покрытий концерна DiDonato. <br><br>Концерн DiDonato представляет собой одно из крупнейших
											производств по изготовлению красок и окрашивающих покрытий для отделочных строительных работ. Их производство ориентированно на национальный и интернациональный рынок B2B.<br>Объединение DiDonato очень внимательно относится к
											тому, какое влияние оказывает его производство и производимая им продукция на состояние окружающей среды, поэтому были разработаны циклы высокой эффективности и в соответствии с критериями экологии.<br>В производстве линии
											используются: красители Ecolabel Bioline, которые сочетают превосходные эксплуатационные показатели и абсолютную не токсичность. Смесь Biolineс естественным отвердителем, не содержащим цемента.<br><br>Материал, который вы
											покупаете в салоне-студии Интерио, выпускается для всего мира. А это большой показатель качества. <br><br>Приглашаем всех посетить салон-студию <a href="#">Интерио</a> в ЦСР Кубатура. </p>
									</div>
								</div>
								<?
								// основное содержимое
								// блок 3 - конец
								?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
