<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=structure.delete.first
[END_COT_EXT]
==================== */

/**
 * Removes category translations
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 *
 * @var array $category Category data
 */

defined('COT_CODE') or die('Wrong URL');

if ($category['structure_area'] !== 'market') {
    return;
}

Cot::$db->delete(Cot::$db->i18n4marketpro_structure, 'istructure_code = ?', $category['structure_code']);
cot_log(
    'Deleted translate for category "' . $category['structure_code'] . '"',
    'i18n4marketpro',
    'structure',
    'delete'
);
