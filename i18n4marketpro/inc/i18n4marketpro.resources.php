<?php
/**
 * I18n plugin markup snippets
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (!isset($L['Edit'])) {
    include cot_langfile('main', 'core');
}

$R['i18n4marketpro_structure_translations_begin'] = '<ul>';
$R['i18n4marketpro_structure_translations_end'] = '</ul>';
$R['i18n4marketpro_structure_translations_item'] = '<li><a href="{$url}" title="' . $L['Edit'] . '">{$title}</a></li>';
