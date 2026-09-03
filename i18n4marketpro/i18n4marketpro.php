<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Standalone item translation tool
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

cot_block($i18n4marketpro_write);

require_once cot_incfile('forms');

if ($m == 'structure') {
	include cot_incfile('i18n4marketpro', 'plug', 'structure');

} elseif ($m == 'market') {
	include cot_incfile('i18n4marketpro', 'plug', 'market');

} else {
	/* === Hook === */
	foreach (cot_getextplugins('i18n4marketpro.standalone') as $pl) {
		include $pl;
	}
	/* =============*/
}
