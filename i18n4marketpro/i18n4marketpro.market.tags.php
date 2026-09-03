<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=market.tags
Tags=market.tpl:{I18N4MARKETPRO_LANG_ROW_URL},{I18N4MARKETPRO_LANG_ROW_CODE},{I18N4MARKETPRO_LANG_ROW_TITLE},{I18N4MARKETPRO_LANG_ROW_CLASS},{I18N4MARKETPRO_LANG_ROW_SELECTED},{PAGE_I18N4MARKETPRO_TRANSLATE},{PAGE_I18N4MARKETPRO_DELETE}
[END_COT_EXT]
==================== */

/**
 * Assigns i18n4marketpro control tags for a page
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
//global $i18n4marketpro_enabled, $i18n4marketpro_locale, $i18n4marketpro_fallback, $i18n4marketpro_locales, $i18n4marketpro_write, $i18n4marketpro_admin, $item, $db, $db_i18n4marketpro_pages, $db_market;
global $i18n4marketpro_enabled, $i18n4marketpro_locale, $i18n4marketpro_fallback, $i18n4marketpro_locales, $i18n4marketpro_write, $i18n4marketpro_admin, $item, $db, $db_i18n4marketpro_pages, $db_market, $cfg;

if ($i18n4marketpro_enabled) {
	$id = (empty($id)) ? $item['fieldmrkt_id'] : $id;
	
	// --- Загружаем все переводы заголовков для этого товара ---
	$translations = array();
	if ($id > 0) {
		$res = $db->query("SELECT ipage_locale, ipage_title FROM $db_i18n4marketpro_pages WHERE ipage_id = ?", [$id]);
		while ($row = $res->fetch()) {
			$translations[$row['ipage_locale']] = $row['ipage_title'];
		}
	}
	// ---------------------------------------------------------
	
	// --- Получаем оригинальное название товара (язык по умолчанию) ---
	$original_title = '';
	if ($id > 0 && isset($db_market)) {
		$original_title = $db->query("SELECT fieldmrkt_title FROM $db_market WHERE fieldmrkt_id = ?", [$id])->fetchColumn();
	}
	if (empty($original_title)) {
		// запасной вариант: если не удалось получить из БД, используем текущее значение (но это может быть перевод)
		$original_title = $item['fieldmrkt_title'];
	}
	// ---------------------------------------------------------
		// --- Assign original URL without locale for canonical/template use ---
		$urlparams_original = empty($item['fieldmrkt_alias'])
			? array('c' => $item['fieldmrkt_cat'], 'id' => $id)
			: array('c' => $item['fieldmrkt_cat'], 'al' => $item['fieldmrkt_alias']);
		$original_relative_url = cot_url('market', $urlparams_original, '', false, true);
		$original_full_url = rtrim($cfg['mainurl'], '/') . '/' . ltrim($original_relative_url, '/');
		$t->assign(array(
			'MARKET_I18N_ORIGINAL_URL' => $original_relative_url,
			'MARKET_I18N_ORIGINAL_URL_FULL' => $original_full_url,
			'MARKET_I18N_ORIGINAL_TITLE' => htmlspecialchars($original_title, ENT_QUOTES, 'UTF-8'),
		));
		// ---------------------------------------------------------	
	// Render language selection
	$pag_i18n4marketpro_locales = cot_i18n4marketpro_list_page_locales($id);
	if (count($pag_i18n4marketpro_locales) > 0) {
		array_unshift($pag_i18n4marketpro_locales, Cot::$cfg['defaultlang']);
		foreach ($pag_i18n4marketpro_locales as $lc) {
			if ($lc == $i18n4marketpro_locale) {
				$lc_class = 'selected';
				$lc_selected = 'selected="selected"';
			} else {
				$lc_class = '';
				$lc_selected = '';
			}
			// Исправлено: используем $item['fieldmrkt_alias'] вместо неопределенной переменной $al
			$urlparams = empty($item['fieldmrkt_alias'])
				? array('c' => $item['fieldmrkt_cat'], 'id' => $id)
				: array('c' => $item['fieldmrkt_cat'], 'al' => $item['fieldmrkt_alias']);
			if (!Cot::$cfg['plugin']['i18n4marketpro']['omitmain'] || $lc != $i18n4marketpro_fallback) {
				$urlparams['l'] = $lc;
			}
			
			// Добавлено: модифицированный массив параметров без l для основного языка
			global $cfg; // Обеспечиваем доступ к $cfg['mainurl'] и $cfg['defaultlang']
			$urlparams_modified = $urlparams;
			if ($lc == $cfg['defaultlang'] && isset($urlparams_modified['l'])) {
				unset($urlparams_modified['l']);
			}
			
			// Формируем абсолютный URL для модифицированной ссылки
			$relative_url_modified = cot_url('market', $urlparams_modified, '', false, true);
			$full_url_modified = rtrim($cfg['mainurl'], '/') . '/' . ltrim($relative_url_modified, '/');
			
			// --- Получаем локализованный заголовок для ALT / title ---
			$alt_title = $original_title; // по умолчанию оригинал (для основного языка)
			if ($lc != $cfg['defaultlang'] && isset($translations[$lc])) {
				$alt_title = $translations[$lc]; // если есть перевод, используем его
			} elseif ($lc != $cfg['defaultlang'] && !isset($translations[$lc])) {
				// если перевода нет, тоже показываем оригинал (fallback)
				$alt_title = $original_title;
			}
			// -------------------------------------------------------
			
			$t->assign(array(
				'I18N4MARKETPRO_LANG_ROW_URL' => cot_url('market', $urlparams, '', false, true),
				'I18N4MARKETPRO_LANG_ROW_URL_MODIFIED' => $full_url_modified,
				'I18N4MARKETPRO_LANG_ROW_ALTTITLE_MODIFIED' => htmlspecialchars($alt_title, ENT_QUOTES, 'UTF-8'), // новый тег
				'I18N4MARKETPRO_LANG_ROW_CODE' => $lc,
				'I18N4MARKETPRO_LANG_ROW_TITLE' => $i18n4marketpro_locales[$lc],
				'I18N4MARKETPRO_LANG_ROW_CLASS' => $lc_class,
				'I18N4MARKETPRO_LANG_ROW_SELECTED' => $lc_selected
			));
			$t->parse('MAIN.I18N4MARKETPRO_LANG.I18N4MARKETPRO_LANG_ROW');
		}
		$t->parse('MAIN.I18N4MARKETPRO_LANG');
	}

	if ($i18n4marketpro_write) {
		// Translation tags
		if (!empty($pag_i18n4marketpro)) {
			if ($i18n4marketpro_admin || $pag_i18n4marketpro['ipage_translatorid'] == Cot::$usr['id'] || $item['fieldmrkt_ownerid'] == Cot::$usr['id']) {
				// Edit translation
				$url_i18n4marketpro = cot_url('plug', "e=i18n4marketpro&m=market&a=edit&id=$id&l=$i18n4marketpro_locale");
				$t->assign(array(
					'MARKET_I18N4MARKETPRO_ADMIN_EDIT' => cot_rc_link($url_i18n4marketpro, Cot::$L['Edit']),
					'MARKET_I18N4MARKETPRO_ADMIN_EDIT_URL' => $url_i18n4marketpro
				));
			}
		} else {
			if (count($pag_i18n4marketpro_locales) < count($i18n4marketpro_locales)) {
				// Translate button
				$url_i18n4marketpro = cot_url('plug', "e=i18n4marketpro&m=market&a=add&id=$id");
				$t->assign(array(
					'MARKET_I18N4MARKETPRO_TRANSLATE' => cot_rc_link($url_i18n4marketpro, Cot::$L['i18n4marketpro_translate']),
					'MARKET_I18N4MARKETPRO_TRANSLATE_URL' => $url_i18n4marketpro
				));
			}
		}
	}

	if ($i18n4marketpro_admin) {
		// Control tags
		if (!empty($pag_i18n4marketpro)) {
			// Delete translation button and URL
            $i18n4marketproDeleteUrl = cot_url(
                'plug',
                ['e' => 'i18n4marketpro', 'm' => 'market', 'a' => 'delete', 'id' => $id, 'l' => $i18n4marketpro_locale]);
            $i18n4marketproDeleteConfirmUrl = cot_confirm_url($i18n4marketproDeleteUrl, 'i18n4marketpro', 'i18n4marketpro_confirm_delete');
			$t->assign([
				'MARKET_I18N4MARKETPRO_ADMIN_DELETE' => cot_rc_link($i18n4marketproDeleteConfirmUrl, Cot::$L['i18n4marketpro_delete'], 'class="confirmLink"'),
				'MARKET_I18N4MARKETPRO_ADMIN_DELETE_URL' => $i18n4marketproDeleteConfirmUrl,
			]);
		}
	}
	// === Extrafields for i18n pages — вывод в шаблон ===
	if (!empty($pag_i18n4marketpro) && !empty(Cot::$extrafields[Cot::$db->i18n4marketpro_pages])) {
		foreach (Cot::$extrafields[Cot::$db->i18n4marketpro_pages] as $exfld) {
			$tag = mb_strtoupper($exfld['field_name']);
			$exfld_title = cot_extrafield_title($exfld, 'i18n4marketpro_');

			$temp_value = null;
			if (isset($pag_i18n4marketpro['ipage_' . $exfld['field_name']])) {
				$temp_value = $pag_i18n4marketpro['ipage_' . $exfld['field_name']];
			}

			// Индивидуальные теги (ручной вывод)
			$t->assign([
				'I18N_' . $tag . '_TITLE' => $exfld_title,
				'I18N_' . $tag           => cot_build_extrafields_data('i18n4marketpro', $exfld, $temp_value, $item['fieldmrkt_parser']),
				'I18N_' . $tag . '_VALUE' => $temp_value,
			]);

			// Динамический вывод через блок EXTRAFLD
			$t->assign([
				'I18N_EXTRAFIELD_TITLE' => $exfld_title,
				'I18N_EXTRAFIELD_VALUE' => cot_build_extrafields_data('i18n4marketpro', $exfld, $temp_value, $item['fieldmrkt_parser']),
			]);
			$t->parse('MAIN.EXTRAFLD');
		}
	}
}