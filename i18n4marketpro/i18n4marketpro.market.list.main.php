<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=market.list.main
[END_COT_EXT]
==================== */


/**
 * Category preload and title setup
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
global $i18n4marketpro_enabled, $i18n4marketpro_notmain, $i18n4marketpro_locale, $c;
if ($i18n4marketpro_enabled && $i18n4marketpro_notmain) {
	$cat_i18n4marketpro = cot_i18n4marketpro_get_cat($c, $i18n4marketpro_locale);

	if ($cat_i18n4marketpro) {
		Cot::$out['desc'] = htmlspecialchars(strip_tags($cat_i18n4marketpro['desc']));
        Cot::$out['subtitle'] = $cat_i18n4marketpro['title'];
	}
}
