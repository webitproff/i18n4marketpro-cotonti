<?php
/**
 * Structure translation tool
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 *
 * @var ?array $i18n4marketpro_structure
 * @var bool $i18n4marketpro_admin if user can administrate i18n4marketpro
 * @var ?array<string, string> $i18n4marketpro_locales Locales List ['en' => 'English', 'ru' => 'Русский']
 */

defined('COT_CODE') or die('Wrong URL.');

cot_block($i18n4marketpro_admin);

$maxperpage = (
    Cot::$cfg['maxrowsperpage']
    && is_numeric(Cot::$cfg['maxrowsperpage'])
    && Cot::$cfg['maxrowsperpage'] > 0
) ? Cot::$cfg['maxrowsperpage'] : 15;
list($pg, $d, $durl) = cot_import_pagenav('d', $maxperpage);

Cot::$out['subtitle'] = Cot::$L['i18n4marketpro_structure'];

/* === Hook === */
foreach (cot_getextplugins('i18n4marketpro.structure.first') as $pl) {
	include $pl;
}
/* =============*/

// Refresh i18n4marketpro struct data
cot_i18n4marketpro_load_structure();
Cot::$cache && Cot::$cache->db->store('structure', $i18n4marketpro_structure, 'i18n4marketpro');

if (empty(Cot::$cfg['plugin']['i18n4marketpro']['cats'])) {
    $url = cot_url('admin', ['m' => 'config', 'n' => 'edit', 'o' => 'plug', 'p' => 'i18n4marketpro']);
    cot_message(sprintf(Cot::$L['i18n4marketpro_no_categories'], $url), 'warning');
}

if (empty($i18n4marketpro_locale) || $i18n4marketpro_locale == Cot::$cfg['defaultlang']) {
	// Locale selection
	$t = new XTemplate(cot_tplfile('i18n4marketpro.locales', 'plug'));

	foreach ($i18n4marketpro_locales as $lc => $title) {
		if ($lc != Cot::$cfg['defaultlang']) {
			$t->assign([
				'I18N4MARKETPRO_LOCALE_ROW_URL' => cot_url('plug', "e=i18n4marketpro&m=structure&l=$lc", false, true),
				'I18N4MARKETPRO_LOCALE_ROW_TITLE' => $title
			]);
			$t->parse('MAIN.I18N4MARKETPRO_LOCALE_ROW');
		}
	}
} else {
	// Structure translation for selected locale
	if ($a == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
		// Update stucture translations
		$codes = cot_import('code', 'P', 'ARR');
		$titles = cot_import('title', 'P', 'ARR');
		$descs = cot_import('desc', 'P', 'ARR');

		$cnt = count($codes);

		$inserted_cnt = 0;
		$removed_cnt = 0;
		$updated_cnt = 0;
		for ($i = 0; $i < $cnt; $i++) {
			$code = cot_import($codes[$i], 'D', 'TXT');
			if (isset($titles[$i])) {
				// Updating a translation
				$title = cot_import($titles[$i], 'D', 'TXT');
				$desc = cot_import($descs[$i], 'D', 'TXT');
				if (!isset($i18n4marketpro_structure[$code][$i18n4marketpro_locale]['title'])
					|| $title != $i18n4marketpro_structure[$code][$i18n4marketpro_locale]['title']
					|| $desc != $i18n4marketpro_structure[$code][$i18n4marketpro_locale]['title'])
				{
					// Something has been changed
					if (empty($title)) {
						// Remove
						$removed_cnt += $db->delete($db_i18n4marketpro_structure,
							"istructure_code = ".$db->quote($code)." AND istructure_locale = '$i18n4marketpro_locale'");
					} elseif (empty($i18n4marketpro_structure[$code][$i18n4marketpro_locale]['title'])) {
						// Insert
						$inserted_cnt += $db->insert($db_i18n4marketpro_structure, [
							'istructure_code' => $code,
							'istructure_locale' => $i18n4marketpro_locale,
							'istructure_title' => $title,
							'istructure_desc' => $desc
						]);
					} else {
						// Update
						$updated_cnt += $db->update($db_i18n4marketpro_structure, [
							'istructure_title' => $title,
							'istructure_desc' => $desc
						], "istructure_code = ".$db->quote($code)." AND istructure_locale = '$i18n4marketpro_locale'");
					}
				}
			}
		}
		// Done

		/* === Hook === */
		foreach (cot_getextplugins('i18n4marketpro.structure.update.done') as $pl) {
			include $pl;
		}
		/* =============*/

		if ($inserted_cnt > 0) {
			cot_message(cot_rc('i18n4marketpro_items_added', ['cnt' => $inserted_cnt]));
			cot_log('Add translate for ' . $inserted_cnt . ' categories', 'i18n4marketpro', 'structure', 'add');
		}
		if ($updated_cnt > 0) {
			cot_message(cot_rc('i18n4marketpro_items_updated', ['cnt' => $updated_cnt]));
			cot_log('Edited translate for ' . $updated_cnt . ' categories', 'i18n4marketpro', 'structure', 'edit');
		}
		if ($removed_cnt > 0) {
			cot_message(cot_rc('i18n4marketpro_items_removed', ['cnt' => $removed_cnt]));
        	cot_log('Deleted translate for ' . $removed_cnt . ' categories', 'i18n4marketpro', 'structure', 'delete');
		}

		cot_redirect(
            cot_url('plug', "e=i18n4marketpro&m=structure&l=$i18n4marketpro_locale&d=$durl", '', true)
        );
	}

	$t = new XTemplate(cot_tplfile('i18n4marketpro.structure', 'plug'));

	// Render table
	$ii = 0;
	$k = -1;
	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('i18n4marketpro.structure.loop');
	/* ===== */
	foreach (Cot::$structure['market'] as $code => $row) {
		if (cot_i18n4marketpro_enabled($code)) {
			$k++;
			if ($k < $d || $ii == $maxperpage) {
				continue;
			}

			$cat_i18n4marketpro = isset($i18n4marketpro_structure[$code][$i18n4marketpro_locale]) ?
                $i18n4marketpro_structure[$code][$i18n4marketpro_locale] : ['title' => '', 'desc' => ''];

			$t->assign([
				'I18N4MARKETPRO_CATEGORY_ROW_TITLE' => htmlspecialchars($row['title']),
				'I18N4MARKETPRO_CATEGORY_ROW_DESC' => htmlspecialchars($row['desc']),
				'I18N4MARKETPRO_CATEGORY_ROW_CODE_NAME' => "code[$ii]",
				'I18N4MARKETPRO_CATEGORY_ROW_CODE_VALUE' => $code,
				'I18N4MARKETPRO_CATEGORY_ROW_ITITLE_NAME' => "title[$ii]",
				'I18N4MARKETPRO_CATEGORY_ROW_ITITLE_VALUE' => htmlspecialchars($cat_i18n4marketpro['title']),
				'I18N4MARKETPRO_CATEGORY_ROW_IDESC_NAME' => "desc[$ii]",
				'I18N4MARKETPRO_CATEGORY_ROW_IDESC_VALUE' => htmlspecialchars($cat_i18n4marketpro['desc']),
				'I18N4MARKETPRO_CATEGORY_ROW_ODDEVEN' => cot_build_oddeven($ii)
			]);

			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl) {
				include $pl;
			}
			/* ===== */

			$t->parse('MAIN.I18N4MARKETPRO_CATEGORY_ROW');
			$ii++;
		}
	}
	$totalitems = $k + 1;

	$pagenav = cot_pagenav('plug', 'e=i18n4marketpro&m=structure&l='.$i18n4marketpro_locale, $d, $totalitems,
	$maxperpage, 'd', '', Cot::$cfg['jquery'] && Cot::$cfg['turnajax']);

	$t->assign([
		'I18N4MARKETPRO_ACTION' => cot_url('plug', 'e=i18n4marketpro&m=structure&l='.$i18n4marketpro_locale.'&a=update&d='.$durl),
		'I18N4MARKETPRO_ORIGINAL_LANG' => isset($i18n4marketpro_locales[Cot::$cfg['defaultlang']]) ?
            $i18n4marketpro_locales[Cot::$cfg['defaultlang']] : Cot::$cfg['defaultlang'],
		'I18N4MARKETPRO_TARGET_LANG' => $i18n4marketpro_locales[$i18n4marketpro_locale],
		'I18N4MARKETPRO_PAGINATION_PREV' => $pagenav['prev'],
		'I18N4MARKETPRO_PAGNAV' => $pagenav['main'],
		'I18N4MARKETPRO_PAGINATION_NEXT' => $pagenav['next']
	]);

	cot_display_messages($t);

	/* === Hook === */
	foreach (cot_getextplugins('i18n4marketpro.structure.tags') as $pl) {
		include $pl;
	}
	/* =============*/
}
