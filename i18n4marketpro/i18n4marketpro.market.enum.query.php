<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=market.enum.query
[END_COT_EXT]
==================== */

/**
 * Load translations in cot_page_enum function
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

global $i18n4marketpro_read, $i18n4marketpro_notmain, $i18n4marketpro_locale;

if (isset($i18n4marketpro_notmain) && $i18n4marketpro_notmain && $i18n4marketpro_read) {
    $cns_join_columns .= ',i18n4marketpro.*';
    $cns_join_tables .= ' LEFT JOIN ' . Cot::$db->i18n4marketpro_pages .
        " AS i18n4marketpro ON i18n4marketpro.ipage_id = p.fieldmrkt_id AND i18n4marketpro.ipage_locale = " . Cot::$db->quote($i18n4marketpro_locale) .
        ' AND i18n4marketpro.ipage_id IS NOT NULL';
}
