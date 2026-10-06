<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

/**
 * Adds four parent-section fields used as meta templates for direct child pages.
 * The check is persisted, so public requests do not query the user-field table
 * after the one-time installation succeeds.
 */
function kubaturaEnsureCatalogSeoFields()
{
    if (Option::get('main', 'kubatura_catalog_child_seo_fields_v1', 'N') === 'Y') {
        return;
    }

    $iblockIds = defined('KUBATURA_PRODUCT_IBLOCK_IDS')
        ? array_filter(array_map('intval', explode(',', KUBATURA_PRODUCT_IBLOCK_IDS)))
        : array(7, 11);
    $definitions = array(
        'UF_CHILD_TITLE_TPL' => 'Title дочерней страницы',
        'UF_CHILD_DESC_TPL' => 'Description дочерней страницы',
        'UF_CHILD_KEYS_TPL' => 'Keywords дочерней страницы',
        'UF_CHILD_H1_TPL' => 'H1 дочерней страницы',
    );
    $userType = new \CUserTypeEntity();
    $success = true;

    foreach ($iblockIds as $iblockId) {
        $entityId = 'IBLOCK_' . (int)$iblockId . '_SECTION';
        foreach ($definitions as $fieldName => $label) {
            $existing = \CUserTypeEntity::GetList(
                array(),
                array('ENTITY_ID' => $entityId, 'FIELD_NAME' => $fieldName)
            )->Fetch();
            if ($existing) {
                continue;
            }

            $added = $userType->Add(array(
                'ENTITY_ID' => $entityId,
                'FIELD_NAME' => $fieldName,
                'USER_TYPE_ID' => 'string',
                'SORT' => 700,
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'EDIT_FORM_LABEL' => array('ru' => $label, 'en' => $label),
                'LIST_COLUMN_LABEL' => array('ru' => $label, 'en' => $label),
                'HELP_MESSAGE' => array(
                    'ru' => 'Переменные: #SECTION_NAME#, #SECTION_CODE#, #PARENT_NAME#, #IBLOCK_NAME#, #SITE_NAME# и любые поля раздела, например #UF_CUSTOM#.',
                    'en' => 'Supports section fields in #FIELD_NAME# format.',
                ),
            ));
            if (!$added) {
                $success = false;
            }
        }
    }

    if ($success) {
        Option::set('main', 'kubatura_catalog_child_seo_fields_v1', 'Y');
    }
}

/** Replaces #BITRIX_FIELD# variables with scalar values of the child section. */
function kubaturaRenderSectionMetaTemplate($template, array $variables)
{
    return trim(preg_replace_callback(
        '/#([A-Z0-9_]+)#/',
        static function ($match) use ($variables) {
            $value = isset($variables[$match[1]]) ? $variables[$match[1]] : '';
            return is_scalar($value) ? trim(strip_tags((string)$value)) : '';
        },
        (string)$template
    ));
}

/** Applies templates stored in the immediate parent section to the current child. */
function kubaturaApplyChildSectionMeta($iblockId, $sectionId = 0, $sectionCode = '')
{
    global $APPLICATION;

    if (!Loader::includeModule('iblock')) {
        return;
    }

    $filter = array('IBLOCK_ID' => (int)$iblockId, 'ACTIVE' => 'Y');
    if ((int)$sectionId > 0) {
        $filter['ID'] = (int)$sectionId;
    } elseif ((string)$sectionCode !== '') {
        $filter['=CODE'] = (string)$sectionCode;
    } else {
        return;
    }

    $select = array(
        'ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME', 'CODE', 'DESCRIPTION',
        'UF_CHILD_TITLE_TPL', 'UF_CHILD_DESC_TPL', 'UF_CHILD_KEYS_TPL', 'UF_CHILD_H1_TPL', 'UF_*',
    );
    $child = \CIBlockSection::GetList(array(), $filter, false, $select)->GetNext(false, false);
    if (!$child || empty($child['IBLOCK_SECTION_ID'])) {
        return;
    }

    $parent = \CIBlockSection::GetList(
        array(),
        array('IBLOCK_ID' => (int)$iblockId, 'ID' => (int)$child['IBLOCK_SECTION_ID']),
        false,
        $select
    )->GetNext(false, false);
    if (!$parent) {
        return;
    }

    $iblock = \CIBlock::GetByID((int)$iblockId)->Fetch();
    $variables = $child;
    $variables['SECTION_NAME'] = $child['NAME'];
    $variables['SECTION_CODE'] = $child['CODE'];
    $variables['PARENT_NAME'] = $parent['NAME'];
    $variables['IBLOCK_NAME'] = $iblock ? $iblock['NAME'] : '';
    $variables['SITE_NAME'] = Option::get('main', 'site_name', 'Кубатура');

    $map = array(
        'UF_CHILD_TITLE_TPL' => 'title',
        'UF_CHILD_DESC_TPL' => 'description',
        'UF_CHILD_KEYS_TPL' => 'keywords',
    );
    foreach ($map as $field => $property) {
        if (!empty($parent[$field])) {
            $value = kubaturaRenderSectionMetaTemplate($parent[$field], $variables);
            if ($value !== '') {
                $APPLICATION->SetPageProperty($property, $value);
            }
        }
    }

    if (!empty($parent['UF_CHILD_H1_TPL'])) {
        $h1 = kubaturaRenderSectionMetaTemplate($parent['UF_CHILD_H1_TPL'], $variables);
        if ($h1 !== '') {
            $APPLICATION->SetTitle($h1);
        }
    }
}

kubaturaEnsureCatalogSeoFields();

