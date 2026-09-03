<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=market.main
Order=5
[END_COT_EXT]
==================== */

/**
 * I18n for pages: redefines page body and title
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$i18n4marketpro_enabled = $i18n4marketpro_read && cot_i18n4marketpro_enabled($item['fieldmrkt_cat']);

if ($i18n4marketpro_enabled && $i18n4marketpro_notmain) {
	$pag_i18n4marketpro = cot_i18n4marketpro_get_page($id, $i18n4marketpro_locale);
	$cat_i18n4marketpro = cot_i18n4marketpro_get_cat($item['fieldmrkt_cat'], $i18n4marketpro_locale);
	if (!$cat_i18n4marketpro) {
		$cat_i18n4marketpro = &Cot::$structure['market'][$item['fieldmrkt_cat']];
	}

	if ($pag_i18n4marketpro) {
		// Override <title>, subtitle and desc
		$title_params = array(
			'TITLE' => $pag_i18n4marketpro['ipage_title'],
			'CATEGORY' => $cat_i18n4marketpro['title']
		);
        Cot::$out['subtitle'] = cot_title(Cot::$cfg['market']['markettitle_page'], $title_params);
        Cot::$out['desc'] = htmlspecialchars(strip_tags($pag_i18n4marketpro['ipage_desc']));

		// Merge with page data
		$item = array_merge($item , $pag_i18n4marketpro);
	}
}
