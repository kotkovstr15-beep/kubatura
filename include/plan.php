<?php
$schemaHost = isset($_SERVER['HTTP_HOST']) ? preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST']) : 'kubatura.su';
$schemaBase = (function_exists('isHTTPS') && isHTTPS() ? 'https://' : 'https://') . $schemaHost;
echo '<div class="karta-plan-tabs"><a href="/karta-tssr/">Общий план</a><a href="/karta-tssr/one1.php">1 корпус 1 этаж</a><a href="/karta-tssr/one2.php">1 корпус 2 этаж</a><a href="/karta-tssr/2-korpus.php">2 корпус</a><a href="/karta-tssr/3-korpus-santekhnika.php">3 корпус</a></div>';
echo '<div class="karta-plan"><img src="/karta-tssr/map_summ.svg" alt="План центра «Кубатура»" loading="lazy"></div>';
echo '<script type="application/ld+json">'.json_encode(array('@context'=>'https://schema.org','@type'=>'Organization','@id'=>$schemaBase.'/#organization','name'=>'Кубатура','url'=>$schemaBase.'/'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>';
