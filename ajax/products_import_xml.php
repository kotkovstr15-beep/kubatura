<?php
/** @noinspection SpellCheckingInspection */
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $USER;

$response = [
    'success' => '',
    'error' => ''
];

if (CModule::IncludeModule('iblock')) {
    $shopId = $_POST['SHOP'];
    $element = new CIBlockElement;
    $xmlCategoriesTree = json_decode($_POST['XML_CATEGORIES_TREE']);
    if ($shopId === "null") {
        $response['error'] = 'Выберите магазин';
        echo json_encode($response);
        die();
    }
    $allProperties = getAllIblockProperties();
    $xmlFile = $_FILES['XML'];
    $xml = file_get_contents($xmlFile['tmp_name']);
    $xml = new SimpleXMLElement($xml);
    $xmlCategoriesAttributesTree = getCategoriesAttributesIds($xmlCategoriesTree, $xml);
    foreach ($xml->shop->products->product as $product) {
        createProduct($product, $shopId, $xmlCategoriesAttributesTree, $allProperties);
    }
}
$response['success']='Товары загруженны!';
echo json_encode($response);

function getAllCategories(array $shop): array
{

    $categroies = $shop['categories']['category'];
    if (empty($categroies)) {
        throw new Error('В файле отсутствуют категории.');
    }
    unset($categroies[0]);
    if (empty($categroies)) {
        throw new Error('В файле отсутствуют категории.');
    }
    return $categroies;


}

/** получаем дерево категорий xml из аттрибутов и то, что пришло в post
 * @param stdClass $xmlCategoriesTree
 * @param SimpleXMLElement $xml
 * @return array
 */
function getCategoriesAttributesIds(stdClass $xmlCategoriesTree, SimpleXMLElement $xml): array
{
    $newCategoryTree = [];
    $categoryIndex = 0;
    foreach ($xml->shop->categories->category as $category) {
        if ($categoryIndex === 0) {
            $categoryIndex++;
            continue;
        }
        foreach ($category->attributes() as $attributeName => $attribute) {
            if ($attributeName === 'id') {
                $newCategoryTree[(string)$attribute] = $xmlCategoriesTree->{$categoryIndex};
            }
        }
        $categoryIndex++;
    }
    return $newCategoryTree;
}

function getAllIblockProperties(): array
{
    $IBLOCK_ID = 7;
    $allProperties = [];
    $res = CIBlock::GetProperties($IBLOCK_ID);
    while ($res_arr = $res->Fetch()) {
        if ($res_arr['PROPERTY_TYPE'] === 'L') {
            $res_arr['PROPERTY_ENUMS'] = getAllPropertyEnum($res_arr['CODE']);
        }
        $allProperties[] = $res_arr;

    }
    return $allProperties;
}

function getAllPropertyEnum(string $code): array
{
    $IBLOCK_ID = 7;
    $allEnums = [];
    $property_enums = CIBlockPropertyEnum::GetList(Array(),
        Array("IBLOCK_ID" => $IBLOCK_ID, "CODE" => "$code"));
    while ($enum_fields = $property_enums->GetNext()) {
        $allEnums[] = $enum_fields;
    }
    return $allEnums;
}

function createProduct(SimpleXMLElement $product, string $shopId, array $categories, array $allPropertiesInfo)
{
    $element = new CIBlockElement;
    $attributeCategoryId = (string)$product->categoryId;
    $categoryId = $categories[$attributeCategoryId];
    $name = (string)$product->name;
    $code = slugTranslit($name);
    $detailPhoto = uploadPhotoByUrl((string)$product->detail_picture);
    $previewPhoto = uploadPhotoByUrl((string)$product->preview_picture);
    /** Свойства*/
    $properties = setProperties($product, $allPropertiesInfo);
    $properties['SHOP'] = $shopId;
    $arLoadProductArray = Array(
        'MODIFIED_BY' => $USER->GetID(), // элемент изменен текущим пользователем
        'IBLOCK_SECTION_ID' => $categoryId,          // элемент лежит в корне раздела
        'CODE' => $code,
        'IBLOCK_ID' => 7,
        'NAME' => $name,
        'ACTIVE' => 'Y',            // активен
        'DETAIL_TEXT' => (string)$product->detail_text,
        'PREVIEW_TEXT' => (string)$product->preview_text,
        'PROPERTY_VALUES' => $properties,
        "DETAIL_PICTURE" => $detailPhoto,
        "PREVIEW_PICTURE" => $previewPhoto,
    );
    $element->Add($arLoadProductArray);
}

function slugTranslit(string $name): string
{
    $arParams = array(
        "max_len" => "100", // обрезаем символьный код до 100 символов
        "change_case" => "L", // приводим к нижнему регистру
        "replace_space" => "-", // меняем пробелы на тире
        "replace_other" => "-", // меняем плохие символы на тире
        "delete_repeat_replace" => "true", // удаляем повторяющиеся тире
        "use_google" => "false", // отключаем использование google
    );
    return Cutil::translit($name, "ru", $arParams);
}

function uploadPhotoByUrl(string $url): array
{

    $ch = curl_init($url);
    $time = (float)microtime(true);
    $number = $time * rand(2, 9);
    $uniqueName = 'img_' . $number;
    $name = "$uniqueName.png";
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . "/upload/tmp/$name";
    $fp = fopen($fullPath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    $img = [
        "name" => '',
        "tmp_name" => '',
        "type" => "image/jpg",
    ];
    $img['name'] = $name;
    $img['tmp_name'] = $fullPath;
    $fileId = CFile::SaveFile($img, 'iblock');
    unlink($fullPath);
    return CFile::MakeFileArray($fileId);
}

/**
 * $allPropertiesInfo - вся информация по свойствам из инфоблока
 * @param SimpleXMLElement $product
 * @param array $allPropertiesInfo
 *
 * @return array
 */
function setProperties(SimpleXMLElement $product, array $allPropertiesInfo): array
{
    $properties = [];
    foreach ((array)$product->properties as $code => $property) {
        $code = strtoupper($code);
        $propertyInfo = getPropertyInfo($allPropertiesInfo, $code);
        if ($propertyInfo['PROPERTY_TYPE'] === 'L') {
            //выбор из списка
            $propertyValueId = findProepertyValueIDInEnumFromXmlPropertyValue($propertyInfo, $property);
            $properties[$propertyInfo['CODE']] = $propertyValueId;
        } elseif ($propertyInfo['PROPERTY_TYPE'] === 'F') {
            // тип свойства файл
            if ($propertyInfo['MULTIPLE'] === 'Y') {
                //массовая загрузка
                $properties[$propertyInfo['CODE']] = uploadManyImgs((array)$property->url);
            } else {
                //одиночная
                $properties[$propertyInfo['CODE']] = uploadPhotoByUrl($property);
            }
        } else {
            $properties[$propertyInfo['CODE']] = $property;
        }
    }
    return $properties;
}

function uploadManyImgs(array $urls):array
{
    $imgs = [];
    foreach ($urls as $index => $url) {
        $imgs[] = uploadPhotoByUrl($url);
    }
    return $imgs;

}

function findProepertyValueIDInEnumFromXmlPropertyValue(array $propertyInfo, string $propertyValue): string
{
    foreach ($propertyInfo['PROPERTY_ENUMS'] as $property) {
        if ($property['VALUE'] === $propertyValue) {
            return $property['ID'];
        }
    }
    return '0';


}

function getPropertyInfo(array $allPropertiesInfo, string $code): array
{
    foreach ($allPropertiesInfo as $index => $propety) {
        if ($propety['CODE'] === $code) {
            return $propety;
        }
    }
    return [];

}