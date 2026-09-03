<?php
/**
 * Page translation tool i18n4marketpro.market.php
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\plugins\trashcan\inc\TrashcanService;

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('market', 'module');
require_once cot_incfile('forms');
// === EXTRAFIELDS: Подключаем API дополнительных полей ===
require_once cot_incfile('extrafields');
// === EXTRAFIELDS: Подключаем API дополнительных полей ===
$id = cot_import('id', 'G', 'INT');
$l = cot_import('l', 'G', 'ALP');

if (!$id || $id < 1) {
    cot_die_message(404);
}

/* === Hook === */
foreach (cot_getextplugins('i18n4marketpro.market.first') as $pl) {
    include $pl;
}
/* =============*/

$stmt = Cot::$db->query('SELECT * FROM ' . Cot::$db->market . ' WHERE fieldmrkt_id = ?', $id);

if ($id > 0 && $stmt->rowCount() == 1) {
    $item= $stmt->fetch();
    $stmt->closeCursor();

    // Для добавления перевод не загружаем
    if ($a == 'add') {
        $pag_i18n4marketpro = [];
    } else {
        $stmt = Cot::$db->query('SELECT * FROM ' . $db->i18n4marketpro_pages . " WHERE ipage_id = ? AND ipage_locale = ?",
            [$id, $i18n4marketpro_locale]);
        $pag_i18n4marketpro = $stmt->rowCount() == 1 ? $stmt->fetch() : [];
        $stmt->closeCursor();
    }
    // === EXTRAFIELDS: Загружаем конфигурацию дополнительных полей для таблицы переводов ===
    $extrafields = Cot::$extrafields[$db_i18n4marketpro_pages] ?? [];
    // === EXTRAFIELDS: Загружаем конфигурацию дополнительных полей для таблицы переводов ===
	
    // ------------------------------------------------------------------
    // ДОБАВЛЕНИЕ НОВОГО ПЕРЕВОДА 
    // ------------------------------------------------------------------
    if ($a == 'add' && empty($pag_i18n4marketpro)) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $selected_locale = cot_import('locale', 'P', 'ALP');
            if (!in_array($selected_locale, array_keys($i18n4marketpro_locales))) {
                cot_error('i18n_incorrect_locale', 'locale');
            }
            if (empty(cot_error_found())) {
                $checkStmt = Cot::$db->query(
                    'SELECT 1 FROM ' . $db->i18n4marketpro_pages . ' WHERE ipage_id = ? AND ipage_locale = ?',
                    [$id, $selected_locale]
                );
                if ($checkStmt->rowCount() > 0) {
                    cot_error('i18n_translation_exists', 'locale');
                }
                $checkStmt->closeCursor();
            }
            $pag_i18n4marketpro = [
                'ipage_id' => $id,
                'ipage_locale' => $selected_locale,
                'ipage_translatorid' => Cot::$usr['id'],
                'ipage_translatorname' => Cot::$usr['name'],
                'ipage_date' => Cot::$sys['now'],
                'ipage_title' => cot_import('title', 'P', 'TXT'),
                'ipage_desc' => cot_import('desc', 'P', 'TXT'),
                'ipage_text' => cot_import('translate_text', 'P', 'HTM')
            ];
            // === EXTRAFIELDS: Импортируем значения дополнительных полей из POST ===
            foreach ($extrafields as $exfld) {
                $fieldName = 'ipage_' . $exfld['field_name'];
                $pag_i18n4marketpro[$fieldName] = cot_import_extrafields(
                    'ri18n4marketpro' . $exfld['field_name'],
                    $exfld,
                    'P',
                    '',
                    'i18n4marketpro_'
                );
            }
            // === EXTRAFIELDS: Импортируем значения дополнительных полей из POST ===
            if (mb_strlen($pag_i18n4marketpro['ipage_title']) < 2) {
                cot_error('page_titletooshort', 'title');
            }
            if (!cot_error_found()) {
                Cot::$db->insert($db->i18n4marketpro_pages, $pag_i18n4marketpro);
                foreach (cot_getextplugins('i18n4marketpro.market.add.done') as $pl) {
                    include $pl;
                }
                cot_message('Added');
                cot_log('Add translate for page #' . $id, 'i18n4marketpro', 'market', 'add');
                $page_urlp = empty($item['fieldmrkt_alias']) ? "c={$item['fieldmrkt_cat']}&id=$id&l=" . $selected_locale
                    : 'c=' . $item['fieldmrkt_cat'] . '&al=' . $item['fieldmrkt_alias'] . '&l=' . $selected_locale;
                cot_redirect(cot_url('market', $page_urlp, '', true, false, true));
            }
        }

        Cot::$out['subtitle'] = Cot::$L['i18n4marketpro_adding'];

        $t = new XTemplate(cot_tplfile('i18n4marketpro.market', 'plug'));

        $lc_list = $i18n4marketpro_locales;
        unset($lc_list[Cot::$cfg['defaultlang']]);
        foreach (cot_i18n4marketpro_list_page_locales($id) as $lc) {
            unset($lc_list[$lc]);
        }
        $lc_values = array_keys($lc_list);
        $lc_names = array_values($lc_list);

        $selected_in_selector = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($pag_i18n4marketpro['ipage_locale'])) ? $pag_i18n4marketpro['ipage_locale'] : '';
        $title_val = isset($pag_i18n4marketpro['ipage_title']) ? $pag_i18n4marketpro['ipage_title'] : '';
        $desc_val = isset($pag_i18n4marketpro['ipage_desc']) ? $pag_i18n4marketpro['ipage_desc'] : '';
        $text_val = isset($pag_i18n4marketpro['ipage_text']) ? $pag_i18n4marketpro['ipage_text'] : (isset($item['fieldmrkt_text']) ? $item['fieldmrkt_text'] : '');

        $t->assign([
            'I18N4MARKETPRO_ACTION' => cot_url('plug', "e=i18n4marketpro&m=market&a=add&id=$id"),
            'I18N4MARKETPRO_TITLE' => Cot::$L['i18n4marketpro_adding'],
            'I18N4MARKETPRO_ORIGINAL_LANG' => $i18n4marketpro_locales[Cot::$cfg['defaultlang']],
            'I18N4MARKETPRO_LOCALIZED_LANG' => cot_selectbox($selected_in_selector, 'locale', $lc_values, $lc_names, false),
            'I18N4MARKETPRO_PAGE_TITLE' => htmlspecialchars($item['fieldmrkt_title']),
            'I18N4MARKETPRO_PAGE_DESC' => htmlspecialchars($item['fieldmrkt_desc']),
            'I18N4MARKETPRO_PAGE_METATITLE' => htmlspecialchars($item['fieldmrkt_metatitle']),
            'I18N4MARKETPRO_PAGE_METADESC' => htmlspecialchars($item['fieldmrkt_metadesc']),
            'I18N4MARKETPRO_PAGE_TEXT' => cot_parse($item['fieldmrkt_text'], Cot::$cfg['market']['marketmarkup']),
            'I18N4MARKETPRO_IPAGE_TITLE' => htmlspecialchars($title_val),
            'I18N4MARKETPRO_IPAGE_DESC' => htmlspecialchars($desc_val),
            'I18N4MARKETPRO_IPAGE_TEXT' => cot_textarea('translate_text', $text_val, 32, 80, '', 'input_textarea_editor')
        ]);
        // === EXTRAFIELDS: Генерируем и передаём в шаблон поля для ввода дополнительных данных ===
        if (!empty($extrafields)) {
            foreach ($extrafields as $exfld) {
                $uname = strtoupper($exfld['field_name']);
                $fieldValue = $pag_i18n4marketpro['ipage_' . $exfld['field_name']] ?? null;
                $extrafieldElement = cot_build_extrafields(
                    'ri18n4marketpro' . $exfld['field_name'],
                    $exfld,
                    $fieldValue
                );
                $extrafieldTitle = cot_extrafield_title($exfld, 'i18n4marketpro_');

                // === FIX: Добавлены общие теги EXTRAFLD для шаблона ===
                $t->assign([
                    'I18N_PAGE_FORM_' . $uname            => $extrafieldElement,
                    'I18N_PAGE_FORM_' . $uname . '_TITLE' => $extrafieldTitle,
                    'I18N_PAGE_FORM_EXTRAFLD'             => $extrafieldElement,
                    'I18N_PAGE_FORM_EXTRAFLD_TITLE'       => $extrafieldTitle,
                ]);
                $t->parse('MAIN.EXTRAFLD');
            }
        }
        // === EXTRAFIELDS: Генерируем и передаём в шаблон поля для ввода дополнительных данных ===		
        cot_display_messages($t);

        foreach (cot_getextplugins('i18n4marketpro.market.translate.tags') as $pl) {
            include $pl;
        }
    }
    // ------------------------------------------------------------------
    // РЕДАКТИРОВАНИЕ (ДОБАВЛЕН СЕЛЕКТОР ЛОКАЛИ)
    // ------------------------------------------------------------------
    elseif (
        $a == 'edit' && !empty($pag_i18n4marketpro)
        && ($i18n4marketpro_admin || $i18n_edit || Cot::$usr['id'] == $pag_i18n4marketpro['ipage_translatorid'])
    ) {
	
        // === EXTRAFIELDS: Сохраняем старые данные для корректной обработки файловых полей ===
        $pag_i18n4marketpro_old = $pag_i18n4marketpro;
		
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Получаем новую локаль из POST
            $new_locale = cot_import('locale', 'P', 'ALP');
            // Проверяем допустимость
            if (!in_array($new_locale, array_keys($i18n4marketpro_locales))) {
                cot_error('i18n_incorrect_locale', 'locale');
            }
            // Если локаль изменилась, проверяем, нет ли уже перевода для новой локали
            if ($new_locale != $i18n4marketpro_locale && empty(cot_error_found())) {
                $checkStmt = Cot::$db->query(
                    'SELECT 1 FROM ' . $db->i18n4marketpro_pages . ' WHERE ipage_id = ? AND ipage_locale = ?',
                    [$id, $new_locale]
                );
                if ($checkStmt->rowCount() > 0) {
                    cot_error('i18n_translation_exists', 'locale');
                }
                $checkStmt->closeCursor();
            }

            // Обновляем данные перевода
            $pag_i18n4marketpro['ipage_date'] = Cot::$sys['now'];
            $pag_i18n4marketpro['ipage_title'] = cot_import('title', 'P', 'TXT');
            if (mb_strlen($pag_i18n4marketpro['ipage_title']) < 2) {
                cot_error('page_titletooshort', 'rpagetitle');
            }
            $pag_i18n4marketpro['ipage_desc'] = cot_import('desc', 'P', 'TXT');
            $pag_i18n4marketpro['ipage_text'] = cot_import('translate_text', 'P', 'HTM');
            // Если локаль изменилась, обновляем и её
            $pag_i18n4marketpro['ipage_locale'] = $new_locale;
			
            // === EXTRAFIELDS: Импортируем значения дополнительных полей из POST, передавая старые значения ===
            foreach ($extrafields as $exfld) {
                $fieldName = 'ipage_' . $exfld['field_name'];
                $oldValue = $pag_i18n4marketpro_old[$fieldName] ?? '';
                $pag_i18n4marketpro[$fieldName] = cot_import_extrafields(
                    'ri18n4marketpro' . $exfld['field_name'],
                    $exfld,
                    'P',
                    $oldValue,
                    'i18n4marketpro_'
                );
            }
            // === EXTRAFIELDS: Импортируем значения дополнительных полей из POST, передавая старые значения ===			
            if (cot_error_found()) {
                // При ошибках не делаем редирект, а покажем форму снова с заполненными полями
                // Для этого нужно сохранить $pag_i18n4marketpro с новыми данными и $new_locale
                // Просто продолжим выполнение до отрисовки формы
            } else {
                // Если локаль изменилась, удаляем старую запись и вставляем новую? 
                // Или просто обновляем, меняя ipage_locale. 
                // Проще обновить, но условие WHERE должно быть по старой локали.
                Cot::$db->update($db->i18n4marketpro_pages,
                    [
                        'ipage_locale' => $new_locale,
                        'ipage_date' => $pag_i18n4marketpro['ipage_date'],
                        'ipage_title' => $pag_i18n4marketpro['ipage_title'],
                        'ipage_desc' => $pag_i18n4marketpro['ipage_desc'],
                        'ipage_text' => $pag_i18n4marketpro['ipage_text']
                    ],
                    "ipage_id = ? AND ipage_locale = ?",
                    [$id, $i18n4marketpro_locale]
                );
				
			// === EXTRAFIELDS: 
				unset($pag_i18n4marketpro['ipage_id']);
				// старый ДиБи-update заменили на новый
				Cot::$db->update(Cot::$db->i18n4marketpro_pages, $pag_i18n4marketpro, "ipage_id = ? AND ipage_locale = ?", [$id, $i18n4marketpro_locale]);
			// === EXTRAFIELDS: 
			
                /* === Hook === */
                foreach (cot_getextplugins('i18n4marketpro.market.edit.update') as $pl) {
                    include $pl;
                }
                /* =============*/

                cot_message('Updated');
                cot_log("Edited translate for page #" . $id, 'i18n4marketpro', 'market', 'edit');
                $page_urlp = empty($item['fieldmrkt_alias']) ? 'c=' . $item['fieldmrkt_cat'] . "&id=$id&l=" . $new_locale
                    : 'c=' . $item['fieldmrkt_cat'] . '&al=' . $item['fieldmrkt_alias'] . '&l=' . $new_locale;
                cot_redirect(cot_url('market', $page_urlp, '', true, false, true));
            }
        }

        Cot::$out['subtitle'] = Cot::$L['i18n4marketpro_editing'];

        $t = new XTemplate(cot_tplfile('i18n4marketpro.market', 'plug'));

        // Формируем список локалей для селектора
        // Все доступные локали
        $lc_list = $i18n4marketpro_locales;
        // Исключаем язык оригинала
        unset($lc_list[Cot::$cfg['defaultlang']]);
        // Получаем все существующие переводы для этой страницы
        $existing = cot_i18n4marketpro_list_page_locales($id);
        // Удаляем из списка те локали, которые уже заняты (кроме текущей)
        foreach ($existing as $lc) {
            if ($lc != $i18n4marketpro_locale) {
                unset($lc_list[$lc]);
            }
        }
        $lc_values = array_keys($lc_list);
        $lc_names = array_values($lc_list);

        // Значение для селектора: если форма отправлена с ошибкой, берём из POST, иначе текущая локаль
        $selected_in_selector = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($new_locale)) ? $new_locale : $i18n4marketpro_locale;

        // Для полей формы при ошибке подставляем введённые данные
        $title_val = ($_SERVER['REQUEST_METHOD'] == 'POST') ? cot_import('title', 'P', 'TXT') : $pag_i18n4marketpro['ipage_title'];
        $desc_val = ($_SERVER['REQUEST_METHOD'] == 'POST') ? cot_import('desc', 'P', 'TXT') : $pag_i18n4marketpro['ipage_desc'];
        $text_val = ($_SERVER['REQUEST_METHOD'] == 'POST') ? cot_import('translate_text', 'P', 'HTM') : $pag_i18n4marketpro['ipage_text'];

        $t->assign([
            'I18N4MARKETPRO_ACTION' => cot_url('plug', "e=i18n4marketpro&m=market&a=edit&id=$id&l=$i18n4marketpro_locale"),
            'I18N4MARKETPRO_TITLE' => Cot::$L['i18n4marketpro_editing'],
            'I18N4MARKETPRO_ORIGINAL_LANG' => $i18n4marketpro_locales[Cot::$cfg['defaultlang']],
            // Вместо текста теперь селектор
            'I18N4MARKETPRO_LOCALIZED_LANG' => cot_selectbox($selected_in_selector, 'locale', $lc_values, $lc_names, false),
            'I18N4MARKETPRO_PAGE_TITLE' => htmlspecialchars($item['fieldmrkt_title']),
            'I18N4MARKETPRO_PAGE_DESC' => htmlspecialchars($item['fieldmrkt_desc']),

            'I18N4MARKETPRO_PAGE_METATITLE' => htmlspecialchars($item['fieldmrkt_metatitle']),
            'I18N4MARKETPRO_PAGE_METADESC' => htmlspecialchars($item['fieldmrkt_metadesc']),
			
            'I18N4MARKETPRO_PAGE_TEXT' => cot_parse($item['fieldmrkt_text'], Cot::$cfg['market']['marketmarkup']),
            'I18N4MARKETPRO_IPAGE_TITLE' => htmlspecialchars($title_val),
            'I18N4MARKETPRO_IPAGE_DESC' => htmlspecialchars($desc_val),
            'I18N4MARKETPRO_IPAGE_TEXT' => cot_textarea('translate_text', $text_val, 32, 80, '', 'input_textarea_editor')
        ]);
        // === EXTRAFIELDS: Генерируем и передаём в шаблон поля для ввода дополнительных данных (редактирование) ===
        if (!empty($extrafields)) {
            foreach ($extrafields as $exfld) {
                $uname = strtoupper($exfld['field_name']);
                $fieldValue = $pag_i18n4marketpro['ipage_' . $exfld['field_name']] ?? null;
                $extrafieldElement = cot_build_extrafields(
                    'ri18n4marketpro' . $exfld['field_name'],
                    $exfld,
                    $fieldValue
                );
                $extrafieldTitle = cot_extrafield_title($exfld, 'i18n4marketpro_');

                // === FIX: Добавлены общие теги EXTRAFLD для шаблона ===
                $t->assign([
                    'I18N_PAGE_FORM_' . $uname            => $extrafieldElement,
                    'I18N_PAGE_FORM_' . $uname . '_TITLE' => $extrafieldTitle,
                    'I18N_PAGE_FORM_EXTRAFLD'             => $extrafieldElement,
                    'I18N_PAGE_FORM_EXTRAFLD_TITLE'       => $extrafieldTitle,
                ]);
                $t->parse('MAIN.EXTRAFLD');
            }
        }
        cot_display_messages($t);

        /* === Hook === */
        foreach (cot_getextplugins('i18n4marketpro.market.edit.tags') as $pl) {
            include $pl;
        }
        /* =============*/
    }
    // ------------------------------------------------------------------
    // УДАЛЕНИЕ 
    // ------------------------------------------------------------------
    elseif ($a == 'delete' && ($i18n4marketpro_admin || Cot::$usr['id'] == $pag_i18n4marketpro['ipage_translatorid'])) {
        if (cot_plugin_active('trashcan') && Cot::$cfg['plugin']['trashcan']['trash_page']) {
            require_once cot_incfile('trashcan', 'plug');
            $row = Cot::$db->query('SELECT * FROM ' . $db->i18n4marketpro_pages .
                ' WHERE ipage_id = ? AND ipage_locale = ?', [$id, $i18n4marketpro_locale])->fetch();

            TrashcanService::getInstance()->put(
                'i18n4marketpro_page',
                Cot::$L['i18n4marketpro_translation'] . " #$id ($i18n4marketpro_locale) " . $row['ipage_title'],
                (string) $id,
                $row
            );
        }

        Cot::$db->delete($db->i18n4marketpro_pages, "ipage_id = $id AND ipage_locale = '$i18n4marketpro_locale'");

        $urlParams = [];

        /* === Hook === */
        foreach (cot_getextplugins('i18n4marketpro.market.delete.done') as $pl) {
            include $pl;
        }
        /* =============*/

        cot_message(Cot::$L['Deleted']);
        cot_log("Deleted translate for page #" . $id, 'i18n4marketpro', 'market', 'delete');

        if (empty($urlParams)) {
            $urlParams = ['c' => $item['fieldmrkt_cat']];
            if (!empty($item['fieldmrkt_alias'])) {
                $urlParams['al'] = $item['fieldmrkt_alias'];
            } else {
                $urlParams['id'] = $id;
            }
        }
        cot_redirect(cot_url('market', $urlParams, '', true));
    } else {
        cot_die(true, true);
    }
} else {
    cot_die(true, true);

}
