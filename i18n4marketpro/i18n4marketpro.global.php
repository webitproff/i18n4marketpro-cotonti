<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Loads required data
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Load structure i18n4marketpro
$i18n4marketpro_structure = null;

if (!empty(Cot::$cache)) {
    $i18n4marketpro_structure = Cot::$cache->db->get('structure', 'i18n4marketpro');
}

if (!$i18n4marketpro_structure) {
	cot_i18n4marketpro_load_structure();
    if (!empty(Cot::$cache)) {
        Cot::$cache->db->store('structure', $i18n4marketpro_structure, 'i18n4marketpro');
    }
}
