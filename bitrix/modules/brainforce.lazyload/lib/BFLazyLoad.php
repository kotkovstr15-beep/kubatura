<?

use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;

class BFLazyLoad{

	public function appendScriptsToPage(){

		if(!defined("ADMIN_SECTION") && $ADMIN_SECTION !== true){

			$module_id = pathinfo(dirname(__DIR__))["basename"];

			$UseModule = Option::get($module_id, "switch_on");

			//Если модуль включен, то подгружаем нужные скрипты
			if ($UseModule == 'Y') {
				$LoadjQuery = Option::get($module_id, "jquery_on");	//Нужно ли подгружать jQuery?
				$ClassName = Option::get($module_id, "classname");	//Для каких картинок будет работать модуль?
				$ConsoleOn = Option::get($module_id, "console_on");	//Выводим отладочную инфу в консоль JS?
				if ($ClassName) {

					$ClassName = str_replace('.', '', $ClassName); // PHP код
					Option::set($module_id, "classname",$ClassName);
					$ClassName = 'img.'.$ClassName;
			
				}
				else {
					$ClassName = 'img';
				}
				if ($LoadjQuery == 'Y') {
					Asset::getInstance()->addString("<script src='https://code.jquery.com/jquery-2.2.4.min.js' integrity='sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=' crossorigin='anonymous'></script>");
				}
				
				Asset::getInstance()->addString("<script>
									(function($) {
									    $(function() {
									        $('".$ClassName."').each(function() {
											
									            var link = $(this).attr('src');
											//alert (link);
									             $(this).attr('data-src', link);
											$(this).attr('src','/brainforce_lazyloadimg/lazy.png');
											$('img').addClass('lzy_img');
									
									        });
									    });
									})(jQuery);
									
									        document.addEventListener('DOMContentLoaded', function() { 
									            const imageObserver = new IntersectionObserver((entries, imgObserver) => {
									                entries.forEach((entry) => {
									                    if (entry.isIntersecting) {
									                        const lazyImage = entry.target;");
				if ($ConsoleOn == 'Y') Asset::getInstance()->addString("	console.log('lazy loading ', lazyImage)");

				Asset::getInstance()->addString("	                        lazyImage.src = lazyImage.dataset.src
									                        lazyImage.classList.remove('lzy_img');
									                        imgObserver.unobserve(lazyImage);
									                    }
									                })
									            });
									            const arr = document.querySelectorAll('".$ClassName."')
									            arr.forEach((v) => {
									                imageObserver.observe(v);
									            })
									        })
								</script>",
					true
				);
			
			}
		}

		return false;
	}
}