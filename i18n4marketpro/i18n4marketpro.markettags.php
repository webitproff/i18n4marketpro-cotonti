<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=markettags.main
[END_COT_EXT]
==================== */

/**
 * Overrides page tags in cot_generate_markettags() function
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 * @see cot_generate_pagetags()
 *
 * @var array<string, mixed> $page_data
 */


defined('COT_CODE') or die('Wrong URL');

global $i18n4marketpro_locale, $i18n4marketpro_read, $i18n4marketpro_write, $i18n4marketpro_admin;
if (empty($item_data) || !is_array($item_data) || empty($item_data['fieldmrkt_id'])) {
    return;
}
// ИСПРАВЛЕНО: определяем, включён ли i18n именно для категории товара
$cat_i18n_enabled = cot_i18n4marketpro_enabled($item_data['fieldmrkt_cat']);

// ИСПРАВЛЕНО: больше не используем старый $i18n4marketpro_enabled как общий флаг
// Теперь он нужен только для логики категории и URL

$i18n_array = [];

// ДОБАВЛЕНО: всегда пытаемся загрузить перевод товара на текущую локаль
// Это работает даже в общем списке товаров (без категории)
$translated_page = null;
$has_translation = false;
if ($i18n4marketpro_read && $i18n4marketpro_locale) {
    $translated_page = cot_i18n4marketpro_get_page($item_data['fieldmrkt_id'], $i18n4marketpro_locale);
    $has_translation = $translated_page !== false;
}

// ДОБАВЛЕНО: базовые параметры URL товара
$urlparams = empty($item_data['fieldmrkt_alias'])
    ? ['c' => $item_data['fieldmrkt_cat'], 'id' => $item_data['fieldmrkt_id']]
    : ['c' => $item_data['fieldmrkt_cat'], 'al' => $item_data['fieldmrkt_alias']];

$append_param = '';

// ИСПРАВЛЕНО: добавляем &l=locale в URL товара ТОЛЬКО если i18n включён в категории
if ($cat_i18n_enabled && (!Cot::$cfg['plugin']['i18n4marketpro']['omitmain'] || $i18n4marketpro_locale != Cot::$cfg['defaultlang'])) {
    $urlparams['l'] = $i18n4marketpro_locale;
    $append_param = '&l=' . $i18n4marketpro_locale;
}

$page_url = cot_url('market', $urlparams);

// ИСПРАВЛЕНО: блок с переводом товара теперь вне условия $i18n4marketpro_enabled && $i18n4marketpro_notmain
// Работает всегда, если есть перевод
if ($has_translation) {
    $title = htmlspecialchars($translated_page['ipage_title']);
    $desc  = !empty($translated_page['ipage_desc']) ? htmlspecialchars($translated_page['ipage_desc']) : '';
    $text  = cot_parse($translated_page['ipage_text'], Cot::$cfg['market']['marketmarkup'], $item_data['fieldmrkt_parser'] ?? '');
    $text_cut = ($textLength > 0) ? cot_string_truncate($text, $textLength) : cot_cut_more_market($text);
    $cutted = mb_strlen($text) > mb_strlen($text_cut);

    // ДОБАВЛЕНО: полный путь с "Главная → Market" (переведённые названия)
    $fullpath = [
        [cot_url('index'), Cot::$L['Home']],
        [cot_url('market'), Cot::$L['market_Market']]
    ];

    $page_link = [$page_url, $translated_page['ipage_title']];

    // ИСПРАВЛЕНО: если категория поддерживает i18n — используем переведённый путь категории
    if ($cat_i18n_enabled) {
        $pagepath = cot_i18n4marketpro_build_catpath('market', $item_data['fieldmrkt_cat'], $i18n4marketpro_locale);
        $fullpath = array_merge($fullpath, $pagepath);
    }

    $fullpath[] = $page_link;
    $breadcrumbs_item = cot_breadcrumbs($fullpath, $pagepath_home, true);

    $i18n_array += [
        'URL'                     => $page_url,
        'TITLE'                   => $title,
        'DESCRIPTION'             => $desc,
        'TEXT'                    => $text,
        'TEXT_CUT'                => $text_cut,
        'TEXT_IS_CUT'             => $cutted,
        'DESCRIPTION_OR_TEXT'     => $desc ?: $text,
        'DESCRIPTION_OR_TEXT_CUT' => $desc ?: $text_cut,
        'MORE'                    => $cutted ? cot_rc_link($page_url, Cot::$L['ReadMore']) : '',
        'UPDATED_STAMP'           => $translated_page['ipage_date'],
        'BREADCRUMBS_ITEM'        => $breadcrumbs_item,  // полный путь: Главная → Market → ... → Товар
    ];

    // ДОБАВЛЕНО: если i18n включён в категории — добавляем перевод категории и связанные теги
    if ($cat_i18n_enabled) {
        $cat_trans = cot_i18n4marketpro_get_cat($item_data['fieldmrkt_cat'], $i18n4marketpro_locale);
        $cat_title = $cat_trans ? $cat_trans['title'] : Cot::$structure['market'][$item_data['fieldmrkt_cat']]['title'];
        $cat_desc  = $cat_trans ? $cat_trans['desc']  : Cot::$structure['market'][$item_data['fieldmrkt_cat']]['desc'];

        $pagepath = cot_i18n4marketpro_build_catpath('market', $item_data['fieldmrkt_cat'], $i18n4marketpro_locale);

        $i18n_array += [
            'CAT_TITLE'        => htmlspecialchars($cat_title),
            'CAT_DESCRIPTION'  => htmlspecialchars($cat_desc),
            'CAT_PATH_SHORT'   => cot_rc_link(cot_url('market', 'c=' . $item_data['fieldmrkt_cat'] . $append_param), htmlspecialchars($cat_title)),
            'BREADCRUMBS'      => cot_breadcrumbs(array_merge($pagepath, [$page_link]), $pagepath_home),
            'BREADCRUMBS_FULL' => cot_breadcrumbs(array_merge($pagepath, [$page_link]), $pagepath_home),
        ];

        // ИСПРАВЛЕНО: админские ссылки (validate/unvalidate/edit) — только если i18n в категории
        if ($admin_rights) {
            $edit_url = cot_url('market', "m=edit&id={$item_data['fieldmrkt_id']}$append_param");
            $validate_url = cot_url('admin', "m=market&a=validate&id={$item_data['fieldmrkt_id']}&x={$sys['xk']}$append_param");
            $unvalidate_url = cot_url('admin', "m=market&a=unvalidate&id={$item_data['fieldmrkt_id']}&x={$sys['xk']}$append_param");

            $i18n_array['ADMIN_EDIT_TRANSLATION'] = cot_rc_link($edit_url, Cot::$L['Edit']);
            $i18n_array['ADMIN_EDIT_TRANSLATION_URL'] = $edit_url;
            $i18n_array['ADMIN_UNVALIDATE_TRANSLATION'] = $item_data['fieldmrkt_state'] == 1
                ? cot_rc_link($validate_url, Cot::$L['Validate'])
                : cot_rc_link($unvalidate_url, Cot::$L['Putinvalidationqueue']);
            $i18n_array['ADMIN_UNVALIDATE_TRANSLATION_URL'] = $item_data['fieldmrkt_state'] == 1 ? $validate_url : $unvalidate_url;
        } elseif (Cot::$usr['id'] == $item_data['fieldmrkt_ownerid']) {
            $edit_url = cot_url('market', "m=edit&id={$item_data['fieldmrkt_id']}$append_param");
            $i18n_array['ADMIN_EDIT_TRANSLATION'] = cot_rc_link($edit_url, Cot::$L['Edit']);
            $i18n_array['ADMIN_EDIT_TRANSLATION_URL'] = $edit_url;
        }
    }
    // === EXTRAFIELDS: Формируем теги для каждого дополнительного поля перевода ===
    // ===== ЭКСТРАПОЛЯ ПЕРЕВОДА (исправлено) =====
    // Берём данные из $translated_page, а не из $page_data
    if (!empty(Cot::$extrafields[Cot::$db->i18n4marketpro_pages])) {
        foreach (Cot::$extrafields[Cot::$db->i18n4marketpro_pages] as $exfld) {
            $tag = 'I18N_PAGE_' . strtoupper($exfld['field_name']);
            // Значение из перевода
            $value = $translated_page['ipage_' . $exfld['field_name']] ?? null;
            // Используем парсер товара
            $i18n_array[$tag] = cot_build_extrafields_data('i18n4marketpro', $exfld, $value, $item_data['fieldmrkt_parser'] ?? '');
            $i18n_array[$tag . '_TITLE'] = cot_extrafield_title($exfld, 'i18n4marketpro_');
            $i18n_array[$tag . '_VALUE'] = $value;
        }
    }
    // === END EXTRAFIELDS ===
} else {
    // ===== Сброс тегов экстраполей, если перевода нет =====
    if (!empty(Cot::$extrafields[Cot::$db->i18n4marketpro_pages])) {
        foreach (Cot::$extrafields[Cot::$db->i18n4marketpro_pages] as $exfld) {
            $tag = 'I18N_PAGE_' . strtoupper($exfld['field_name']);
            $i18n_array[$tag] = '';
            $i18n_array[$tag . '_TITLE'] = '';
            $i18n_array[$tag . '_VALUE'] = '';
        }
    }
}


// ДОБАВЛЕНО: редактирование перевода товара (для переводчиков/админов) — работает независимо от категории
if ($i18n4marketpro_write && $has_translation) {
    $can_edit = $i18n4marketpro_admin || ($translated_page['ipage_translatorid'] == Cot::$usr['id']);
    if ($can_edit) {
        $i18n_array['ADMIN_USER_WRITE_EDIT_TRANSLATION'] = cot_rc_link(
            cot_url('plug', "e=i18n4marketpro&m=market&a=edit&id={$item_data['fieldmrkt_id']}&l={$i18n4marketpro_locale}"),
            Cot::$L['Edit']
        );
    }
}

// Применяем все изменения к тегам
$temp_array = array_merge($temp_array, $i18n_array);
 
/* 
	
	
	
defined('COT_CODE') or die('Wrong URL');

global $i18n4marketpro_enabled, $i18n4marketpro_notmain, $i18n4marketpro_locale, $i18n4marketpro_write, $i18n4marketpro_admin, $i18n4marketpro_read;

$i18n4marketpro_enabled = $i18n4marketpro_read && cot_i18n4marketpro_enabled($item_data['fieldmrkt_cat']);

if ($i18n4marketpro_enabled && $i18n4marketpro_notmain) {
	$i18n4marketpro_array = [];
	$append_param = '';
	$urlparams = empty($item_data['fieldmrkt_alias'])
        ? ['c' => $item_data['fieldmrkt_cat'], 'id' => $item_data['fieldmrkt_id']]
        : ['c' => $item_data['fieldmrkt_cat'], 'al' => $item_data['fieldmrkt_alias']];

	if (!Cot::$cfg['plugin']['i18n4marketpro']['omitmain'] || $i18n4marketpro_locale != Cot::$cfg['defaultlang']) {
		$urlparams['l'] = $i18n4marketpro_locale;
		$append_param = '&l=' . $i18n4marketpro_locale;
	}
	$cat_i18n4marketpro = cot_i18n4marketpro_get_cat($item_data['fieldmrkt_cat'], $i18n4marketpro_locale);
	if ($cat_i18n4marketpro) {
		$cat_url = cot_url('market', 'c=' . $item_data['fieldmrkt_cat'].$append_param);
		$validate_url = cot_url('admin', "m=market&a=validate&id={$item_data['fieldmrkt_id']}&x={$sys['xk']}$append_param");
		$unvalidate_url = cot_url('admin', "m=market&a=unvalidate&id={$item_data['fieldmrkt_id']}&x={$sys['xk']}$append_param");
		$edit_url = cot_url('market', "m=edit&id={$item_data['fieldmrkt_id']}$append_param");
		$pagepath = cot_i18n4marketpro_build_catpath('market', $item_data['fieldmrkt_cat'], $i18n4marketpro_locale);
		$catpath = cot_breadcrumbs($pagepath, $pagepath_home);
		$page_link = array(array(cot_url('market', $urlparams), $item_data['fieldmrkt_title']));
		$i18n4marketpro_array = array_merge(
            $i18n4marketpro_array,
            [
                'BREADCRUMBS' => cot_breadcrumbs(array_merge($pagepath, $page_link), $pagepath_home),
                'BREADCRUMBS_FULL' => cot_breadcrumbs(array_merge($pagepath, $page_link), $pagepath_home),
                'CAT_TITLE' => htmlspecialchars($cat_i18n4marketpro['title']),
                'CAT_PATH' => $catpath,
                'CAT_PATH_SHORT' => cot_rc_link(
                    cot_url('market', 'c=' . $item_data['fieldmrkt_cat'] . $append_param),
                    htmlspecialchars($cat_i18n4marketpro['title'])
                ),
                'CAT_DESCRIPTION' =>  htmlspecialchars($cat_i18n4marketpro['desc']),
            ]
        );


		if ($admin_rights) {
			$i18n4marketpro_array['ADMIN_EDIT_TRANSLATION'] = cot_rc_link($edit_url, Cot::$L['Edit']);
			$i18n4marketpro_array['ADMIN_EDIT_TRANSLATION_URL'] = $edit_url;
			$i18n4marketpro_array['ADMIN_UNVALIDATE_TRANSLATION'] = $item_data['fieldmrkt_state'] == 1 ?
				cot_rc_link($validate_url, Cot::$L['Validate']) :
				cot_rc_link($unvalidate_url, Cot::$L['Putinvalidationqueue']);
			$i18n4marketpro_array['ADMIN_UNVALIDATE_TRANSLATION_URL'] = $item_data['fieldmrkt_state'] == 1 ?
				$validate_url : $unvalidate_url;
		} elseif (Cot::$usr['id'] == $item_data['fieldmrkt_ownerid']) {
			$i18n4marketpro_array['ADMIN_EDIT_TRANSLATION'] = cot_rc_link($edit_url, Cot::$L['Edit']);
			$i18n4marketpro_array['ADMIN_EDIT_TRANSLATION_URL'] = $edit_url;
		}
	} else {
		$cat_i18n4marketpro = &$structure['market'][$item_data['fieldmrkt_cat']];
	}
 */
/* 	if (!empty($item_data['ipage_title'])) {
		$text = cot_parse($item_data['ipage_text'], Cot::$cfg['market']['marketmarkup'], $item_data['fieldmrkt_parser']);
		$text_cut = ((int) $textLength > 0) ? cot_string_truncate($text, $textLength) : cot_cut_more_market($text);
		$cutted = mb_strlen($text) > mb_strlen($text_cut);

        $pageDescription = !empty($item_data['ipage_desc'])
            ? htmlspecialchars($item_data['ipage_desc'])
            : '';

		$page_link = array(array(cot_url('market', $urlparams), $item_data['ipage_title']));
		$i18n4marketpro_array = array_merge(
            $i18n4marketpro_array,
            [
                'URL' => cot_url('market', $urlparams),
                'TITLE' => htmlspecialchars($item_data['ipage_title']),
                'BREADCRUMBS' => cot_breadcrumbs(array_merge($pagepath, $page_link), $pagepath_home),
                'BREADCRUMBS_FULL_ITEM' => cot_breadcrumbs(array_merge($pagepath, $page_link), $pagepath_home),
                'DESCRIPTION' => $pageDescription,
                'TEXT' => $text,
                'TEXT_CUT' => $text_cut,
                'TEXT_IS_CUT' => $cutted,
                'DESCRIPTION_OR_TEXT' => $pageDescription !== '' ? $pageDescription : $text,
                'DESCRIPTION_OR_TEXT_CUT' => $pageDescription !== '' ? $pageDescription : $text_cut,
                'MORE' => $cutted ? cot_rc_link($item_data['fieldmrkt_pageurl'], Cot::$L['ReadMore']) : '',
                'UPDATED_STAMP' => $item_data['ipage_date'],
            ]
        );
	} */
/* 	if (!empty($item_data['ipage_title'])) {
		$text = cot_parse($item_data['ipage_text'], Cot::$cfg['market']['marketmarkup'], $item_data['fieldmrkt_parser']);
		$text_cut = ((int) $textLength > 0) ? cot_string_truncate($text, $textLength) : cot_cut_more_market($text);
		$cutted = mb_strlen($text) > mb_strlen($text_cut);
		$pageDescription = !empty($item_data['ipage_desc'])
			? htmlspecialchars($item_data['ipage_desc'])
			: '';

		// ДОПОЛНЕНИЕ: создаём полный путь с добавлением "Главная" и "Market" из $L[] (переведённые)
		$fullpath_item = [
			[cot_url('index'), Cot::$L['Home']],
			[cot_url('market'), Cot::$L['market_Market']]
		];

		if (is_array($pagepath) && !empty($pagepath)) {
			$fullpath_item = array_merge($fullpath_item, $pagepath);
		}

		$page_link = [cot_url('market', $urlparams), $item_data['ipage_title']];
		$fullpath_item = array_merge($fullpath_item, [$page_link]);

		$catpath_full_item = cot_breadcrumbs($fullpath_item, $pagepath_home, true);

		$i18n4marketpro_array = array_merge(
			$i18n4marketpro_array,
			[
				'URL' => cot_url('market', $urlparams),
				'TITLE' => htmlspecialchars($item_data['ipage_title']),
				'BREADCRUMBS' => cot_breadcrumbs(array_merge($pagepath, [$page_link]), $pagepath_home),
				'BREADCRUMBS_ITEM' => $catpath_full_item,  // теперь полный путь: Главная → Market → Категория → Товар
				'DESCRIPTION' => $pageDescription,
				'TEXT' => $text,
				'TEXT_CUT' => $text_cut,
				'TEXT_IS_CUT' => $cutted,
				'DESCRIPTION_OR_TEXT' => $pageDescription !== '' ? $pageDescription : $text,
				'DESCRIPTION_OR_TEXT_CUT' => $pageDescription !== '' ? $pageDescription : $text_cut,
				'MORE' => $cutted ? cot_rc_link($item_data['fieldmrkt_pageurl'], Cot::$L['ReadMore']) : '',
				'UPDATED_STAMP' => $item_data['ipage_date'],
			]
		);
	}
	if ($i18n4marketpro_write) {
		if (
            !empty($item_data['ipage_id'])
            && ($i18n4marketpro_admin || (isset($pag_i18n4marketpro) && $pag_i18n4marketpro['ipage_translatorid'] == Cot::$usr['id']))
        ) {
			// Edit translation
			$i18n4marketpro_array['ADMIN_USER_WRITE_EDIT_TRANSLATION'] = cot_rc_link(cot_url(
                'plug',
                "e=i18n4marketpro&m=market&a=edit&id=".$item_data['fieldmrkt_id']."&l=$i18n4marketpro_locale"), Cot::$L['Edit']
            );
		}
	}

	$temp_array = array_merge($temp_array, $i18n4marketpro_array);
}
 */
