<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=structure.update.done
[END_COT_EXT]
==================== */

/**
 * Update category translations
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

global $db_i18n4marketpro_structure;

if ($extension === 'market' && $old_data['structure_code'] != $new_data['structure_code']) {
    Cot::$db->update($db_i18n4marketpro_structure, ['istructure_code' => $new_data['structure_code']],
        "istructure_code=".Cot::$db->quote($old_data['structure_code']));
    cot_log('Move translate from category "' . $old_data['structure_code'] . '" to category "' .
        $new_data['structure_code'] . '"', 'i18n4marketpro', 'structure', 'edit');
}
