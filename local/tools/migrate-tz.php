<?php
// Запуск после загрузки файлов: php local/tools/migrate-tz.php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/../..');
$_SERVER['REQUEST_URI'] = '/local/tools/migrate-tz.php';
define('NO_KEEP_STATISTIC', true); define('NOT_CHECK_PERMISSIONS', true);
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';
\Bitrix\Main\Loader::includeModule('iblock');

function tzEnsureUserField($entityId, $fieldName, $label, $userType, $hint)
{
    $existing = \CUserTypeEntity::GetList(array(), array('ENTITY_ID'=>$entityId, 'FIELD_NAME'=>$fieldName))->Fetch();
    if ($existing) {
        if ($existing['USER_TYPE_ID'] === $userType) return 'exists';
        $entity = new \CUserTypeEntity();
        $updated = $entity->Update((int)$existing['ID'], array(
            'USER_TYPE_ID'=>$userType,
            'SETTINGS'=>array('ROWS'=>12, 'SIZE'=>80, 'MIN_LENGTH'=>0, 'MAX_LENGTH'=>0),
        ));
        if (!$updated) {
            $error = isset($entity->LAST_ERROR) ? trim((string)$entity->LAST_ERROR) : '';
            throw new \RuntimeException('Не удалось обновить '.$fieldName.($error ? ': '.$error : ''));
        }
        return 'updated';
    }
    $entity = new \CUserTypeEntity();
    $id = $entity->Add(array(
        'ENTITY_ID'=>$entityId, 'FIELD_NAME'=>$fieldName, 'USER_TYPE_ID'=>$userType,
        'SETTINGS'=>array('ROWS'=>12, 'SIZE'=>80, 'MIN_LENGTH'=>0, 'MAX_LENGTH'=>0),
        'SORT'=>700, 'MULTIPLE'=>'N', 'MANDATORY'=>'N', 'SHOW_FILTER'=>'N',
        'SHOW_IN_LIST'=>'Y', 'EDIT_IN_LIST'=>'Y', 'IS_SEARCHABLE'=>'N',
        'EDIT_FORM_LABEL'=>array('ru'=>$label, 'en'=>$label),
        'LIST_COLUMN_LABEL'=>array('ru'=>$label, 'en'=>$label),
        'HELP_MESSAGE'=>array('ru'=>$hint, 'en'=>$hint),
    ));
    if (!$id) {
        $error = isset($entity->LAST_ERROR) ? trim((string)$entity->LAST_ERROR) : '';
        throw new \RuntimeException('Не удалось создать '.$fieldName.($error ? ': '.$error : ''));
    }
    return 'created';
}

$result = array('section_fields'=>array(), 'shop_properties'=>array(), 'facet'=>'unchanged');
$result['section_fields']['UF_INTRO'] = tzEnsureUserField(
    'IBLOCK_7_SECTION', 'UF_INTRO', 'Подводящий абзац', 'html',
    'HTML-текст над товарами раздела. Поддерживаются абзацы, ссылки, жирный текст и списки.'
);

$definitions = array(
    'DESC_MAIN'=>array('Общее описание','Что за бренд, чем занимается, сколько лет на рынке, чем салон отличается. 400–700 знаков, без заголовка.'),
    'DESC_ASSORT'=>array('Что есть в салоне','Товарные группы списком, без цен и характеристик. 400–900 знаков.'),
    'DESC_HELP'=>array('Чем помогают','Услуги салона: подбор, замер, расчёт, доставка, монтаж, гарантия. 300–600 знаков.'),
);
foreach ($definitions as $code=>$definition) {
    $old = \CIBlockProperty::GetList(array(), array('IBLOCK_ID'=>6, 'CODE'=>$code))->Fetch();
    if ($old) { $result['shop_properties'][$code] = 'exists'; continue; }
    $property = new \CIBlockProperty();
    if (!$property->Add(array('IBLOCK_ID'=>6,'CODE'=>$code,'NAME'=>$definition[0],'HINT'=>$definition[1],'ACTIVE'=>'Y','PROPERTY_TYPE'=>'S','USER_TYPE'=>'HTML','MULTIPLE'=>'N','SORT'=>700))) {
        throw new \RuntimeException($property->LAST_ERROR);
    }
    $result['shop_properties'][$code] = 'created';
}

$manufacturer = \CIBlockProperty::GetList(array(), array('IBLOCK_ID'=>7, 'CODE'=>'MANUFACTURER'))->Fetch();
if ($manufacturer) {
    \CIBlockSectionPropertyLink::Set(0, $manufacturer['ID'], array('SMART_FILTER'=>'Y','DISPLAY_TYPE'=>'F','DISPLAY_EXPANDED'=>'N','FILTER_HINT'=>''));
    $result['facet'] = 'MANUFACTURER enabled';
}
\CIBlock::clearIblockTagCache(7); \CIBlock::clearIblockTagCache(6);
echo json_encode($result, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT).PHP_EOL;
