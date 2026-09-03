<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=market.list.tags
Tags=market.list.tpl:{I18N4MARKETPRO_LANG_ROW_URL},{I18N4MARKETPRO_LANG_ROW_CODE},{I18N4MARKETPRO_LANG_ROW_TITLE},{I18N4MARKETPRO_LANG_ROW_CLASS},{I18N4MARKETPRO_LANG_ROW_SELECTED}
[END_COT_EXT]
==================== */

/**
 * Redefines category tags and assings i18n4marketpro tags
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 *
 * @var XTemplate $t
 */

defined('COT_CODE') or die('Wrong URL');

if ($i18n4marketpro_enabled) {
/* 
	if (!empty($cat_i18n4marketpro) && $i18n4marketpro_notmain) {
		// Override category tags
		$catpath = cot_breadcrumbs(cot_i18n4marketpro_build_catpath('market', $c, $i18n4marketpro_locale), Cot::$cfg['homebreadcrumb']);
		$urlparams = (!Cot::$cfg['plugin']['i18n4marketpro']['omitmain'] || $i18n4marketpro_locale != Cot::$cfg['defaultlang'])
			? "c=$c&l=$i18n4marketpro_locale" : "c=$c";

		$t->assign([
			'LIST_CAT_TITLE' => htmlspecialchars($cat_i18n4marketpro['title']),
			'LIST_CAT_RSS' => cot_url('rss', $urlparams),
            'LIST_BREADCRUMBS' => $catpath,
            'LIST_BREADCRUMBS_FULL' => $catpath,
			'LIST_CAT_PATH' => $catpath,
			'LIST_CAT_DESCRIPTION' => $cat_i18n4marketpro['desc'],
		]);
	} 
*/
if (!empty($cat_i18n4marketpro) && $i18n4marketpro_notmain) {
    // Override category tags

    // Оригинальный путь — только переведённые категории (без Главной и Market)
    $catpath = cot_breadcrumbs(cot_i18n4marketpro_build_catpath('market', $c, $i18n4marketpro_locale), Cot::$cfg['homebreadcrumb']);

    // ДОПОЛНЕНИЕ: создаём полный путь с добавлением "Главная" и "Market" из $L[] (переведённые)
    $fullpath = [
        [cot_url('index'), Cot::$L['Home']],          // "Главная" на текущем языке
        [cot_url('market'), Cot::$L['market_Market']] // "Market" на текущем языке
    ];

    // Получаем переведённый путь категорий от i18n4marketpro
    $i18n_catpath = cot_i18n4marketpro_build_catpath('market', $c, $i18n4marketpro_locale);

    // Если есть категории — добавляем их к базовому пути
    if (is_array($i18n_catpath) && !empty($i18n_catpath)) {
        $fullpath = array_merge($fullpath, $i18n_catpath);
    }

    // Формируем полные крошки (Главная → Market → Категория1 → Категория2)
    $catpath_full = cot_breadcrumbs($fullpath, Cot::$cfg['homebreadcrumb'], true);

    $urlparams = (!Cot::$cfg['plugin']['i18n4marketpro']['omitmain'] || $i18n4marketpro_locale != Cot::$cfg['defaultlang'])
        ? "c=$c&l=$i18n4marketpro_locale" : "c=$c";

    $t->assign([
        'LIST_CAT_TITLE'       => htmlspecialchars($cat_i18n4marketpro['title']),
        'LIST_CAT_RSS'         => cot_url('rss', $urlparams),
        'LIST_BREADCRUMBS'     => $catpath,          // оставляем как было — только категории
        'LIST_BREADCRUMBS_FULL'=> $catpath_full,     // новый полный путь с Главная + Market
        'LIST_CAT_PATH'        => $catpath,
        'LIST_CAT_DESCRIPTION' => $cat_i18n4marketpro['desc'],
    ]);
}
	// Render language selection
	$cat_i18n4marketpro_locales = cot_i18n4marketpro_list_cat_locales($c);
	if (count($cat_i18n4marketpro_locales) > 0) {
		array_unshift($cat_i18n4marketpro_locales, Cot::$cfg['defaultlang']);
		foreach ($cat_i18n4marketpro_locales as $lc) {
			if ($lc == $i18n4marketpro_locale) {
				$lc_class = 'selected';
				$lc_selected = 'selected="selected"';
			} else {
				$lc_class = '';
				$lc_selected = '';
			}
			$urlparams = $list_url_path;
			if (!Cot::$cfg['plugin']['i18n4marketpro']['omitmain'] || $lc != $i18n4marketpro_fallback) {
				$urlparams['l'] = $lc;
			} else {
				unset($urlparams['l']);
			}
			$t->assign(array(
				'I18N4MARKETPRO_LANG_ROW_URL' => cot_url('market', $urlparams, '', false, true),
				'I18N4MARKETPRO_LANG_ROW_CODE' => $lc,
				'I18N4MARKETPRO_LANG_ROW_TITLE' => $i18n4marketpro_locales[$lc],
				'I18N4MARKETPRO_LANG_ROW_CLASS' => $lc_class,
				'I18N4MARKETPRO_LANG_ROW_SELECTED' => $lc_selected
			));
			$t->parse('MAIN.I18N4MARKETPRO_LANG.I18N4MARKETPRO_LANG_ROW');
		}
		$t->parse('MAIN.I18N4MARKETPRO_LANG');
	}
}
