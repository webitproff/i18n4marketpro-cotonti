<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=selectBox.structure
[END_COT_EXT]
==================== */

/**
 * Categories translation in cot_selectbox_structure() function
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 * @see cot_selectbox_structure()
 *
 * @var string $extension
 * @var array<string, string> $categoryList
 */

defined('COT_CODE') or die('Wrong URL');

global $i18n4marketpro_read, $i18n4marketpro_notmain, $i18n4marketpro_locale;

if (isset($i18n4marketpro_notmain) && $i18n4marketpro_notmain && $i18n4marketpro_read && !empty($categoryList)) {
    $separator = Cot::$cfg['separator'] === strip_tags(Cot::$cfg['separator'])
        ? ' ' . Cot::$cfg['separator'] . ' '
        : ' / ';

    foreach ($categoryList as $code => $category) {
        $translatedPath = cot_i18n4marketpro_build_catpath($extension, $code, $i18n4marketpro_locale);
        $items = [];
        foreach ($translatedPath as $item) {
            $items[] = $item[1];
        }
        $categoryList[$code] = implode($separator, $items);
    }
}
