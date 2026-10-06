<?php
/** @noinspection SpellCheckingInspection */
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $USER;

$response = [
    'success' => '',
    'error' => ''
];

if (CModule::IncludeModule('iblock')) {
    $xmlFile = $_FILES['XML'];
    $xml = file_get_contents($xmlFile['tmp_name']);
    $xml = simplexml_load_string($xml);
    $json = json_encode($xml);
    /** @var array $xml */
    $xml = json_decode($json, true);
    try {
        $allCategories = getAllCategories($xml['shop']);
        $response['success'] = $allCategories;
    } catch (Error $error) {
        $response['error'] = $error->getMessage();
    }
}
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