<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.tags
Tags=header.tpl:{I18N4MARKETPRO_LANG_ROW_URL},{I18N4MARKETPRO_LANG_ROW_CODE},{I18N4MARKETPRO_LANG_ROW_TITLE},{I18N4MARKETPRO_LANG_ROW_CLASS},{I18N4MARKETPRO_LANG_ROW_SELECTED}
[END_COT_EXT]
==================== */

/**
 * File: /plugins/i18n4marketpro/i18n4marketpro.header.tags.php
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$i18n4marketpro_ietf_map = [
    'ua' => 'uk-UA',   // Украинский язык для Украины (основной)
    'ru' => 'ru-UA',   // Русский язык для Украины
    'en' => 'en-UA',   // Английский язык для Украины (для экспатов и туристов)
    'pl' => 'pl-UA'    // Польский язык для Украины (если актуально)
];

if (count($i18n4marketpro_locales) > 0)
{
	foreach ($i18n4marketpro_locales as $lc => $lc_title)
	{
		if ($lc == $i18n4marketpro_locale)
		{
			$lc_class = 'selected';
			$lc_selected = 'selected="selected"';
		}
		else
		{
			$lc_class = '';
			$lc_selected = '';
		}
		$i18n4marketpro_urlparams = $_GET;
		if ($cfg['plugin']['i18n4marketpro']['omitmain'] && $lc == $i18n4marketpro_fallback && (!$cfg['plugin']['i18n4marketpro']['cookie'] || !cot_import('i18n4marketpro_locale', 'COOKIE', 'ALP')))
		{
			unset($i18n4marketpro_urlparams['l']);
		}
		else
		{
			$i18n4marketpro_urlparams['l'] = $lc;
		}
		if (defined('COT_PLUG'))
		{
			$i18n4marketpro_ext = 'plug';
		}
		else
		{
			$i18n4marketpro_ext = $env['ext'];
			unset($i18n4marketpro_urlparams['e']);
		}
		if (isset($i18n4marketpro_urlparams['rwr']))
		{
			unset($i18n4marketpro_urlparams['rwr']);
		}
		$t->assign(array(
			'I18N4MARKETPRO_LANG_ROW_URL' => cot_url($i18n4marketpro_ext, $i18n4marketpro_urlparams, '', false, true),
			'I18N4MARKETPRO_LANG_ROW_CODE' => $lc,
			'I18N4MARKETPRO_LANG_ROW_FLAG' => $lc == 'en' ? 'gb' : $lc,
			'I18N4MARKETPRO_LANG_ROW_TITLE' => htmlspecialchars($lc_title),
			'I18N4MARKETPRO_LANG_ROW_CLASS' => $lc_class,
			'I18N4MARKETPRO_LANG_ROW_SELECTED' => $lc_selected
		));
		$t->parse('HEADER.I18N4MARKETPRO_LANG.I18N4MARKETPRO_LANG_ROW');
	}
	$t->parse('HEADER.I18N4MARKETPRO_LANG');
}



// ========== 2. УСТАНОВКА ПОЛНОГО IETF-ТЕГА ДЛЯ АТРИБУТА lang ==========
// Установка полного lang="xx-XX" в тег <html>
// Здесь мы определяем, какое значение будет подставлено в шаблон header.tpl
// в атрибут lang (например, <html lang="uk-UA">). Без этой логики там
// оказался бы короткий внутренний код типа "ua", что не соответствует стандартам
// HTML5 и SEO-рекомендациям (требуется формат "язык-РЕГИОН").

// Получаем короткий код текущего активного языка. Переменная $i18n_locale
// устанавливается плагином i18n и содержит код типа 'ua', 'ru', 'en'.
// Если она по какой-то причине пуста, берём язык по умолчанию из конфига.
// Определяем текущий язык (активный или язык по умолчанию).
$current_short_locale = $i18n4marketpro_locale ?: Cot::$cfg['defaultlang'];

// Преобразуем короткий код в полный IETF-тег (например, 'ua' -> 'uk-UA').
// Проверяем, есть ли соответствие в нашей карте $i18n_ietf_map.
// Если есть — используем значение из карты. Если нет — оставляем исходный
// короткий код в качестве запасного варианта (на случай появления новых языков).
$html_lang = isset($i18n4marketpro_ietf_map[$current_short_locale]) 
    ? $i18n4marketpro_ietf_map[$current_short_locale] // условие истинно: берём полный тег
    : $current_short_locale;                // условие ложно: оставляем как есть
	
// Передаём полученное значение в шаблонизатор Cotonti. В header.tpl должна
// присутствовать переменная {HTML_LANG}, которая будет заменена этим значением.
// пример <html lang="{HTML_LANG}">
// полный шаблон https://github.com/webitproff/index36-cotonti-theme/blob/main/themes/index36/header.tpl
$t->assign('HTML_LANG', $html_lang);


// ========== 3. ГЕНЕРАЦИЯ ТЕГОВ hreflang ДЛЯ ПЕРЕВЕДЁННЫХ СТРАНИЦ ==========
// Теги <link rel="alternate" hreflang="..."> сообщают поисковым системам
// (Google, Яндекс) о существовании альтернативных языковых версий текущей
// страницы. Это предотвращает проблемы с дублированием контента и помогает
// показывать пользователю версию на его родном языке.

// Проверяем, что мы находимся на странице модуля "page" (статьи/товары),
// что передан идентификатор страницы $id и он больше нуля.
if ($env['ext'] == 'market' && isset($id) && $id > 0) {
    // Получаем массив коротких кодов языков (грубо говоря список языков), на которые переведена данная страница.
    // Функция cot_i18n_list_page_locales() возвращает к примеру ['ua', 'en', 'pl'].
    $translated_locales = cot_i18n4marketpro_list_page_locales($id);
	
    // Если есть хотя бы одна альтернативная версия (кроме основной) — продолжаем.
    if (!empty($translated_locales)) {
        // Запрашиваем из базы данных категорию и алиас текущей страницы.
        // Эта информация необходима для корректного построения URL (ЧПУ).
        $page_data = Cot::$db->query("SELECT fieldmrkt_cat, fieldmrkt_alias FROM " . Cot::$db->market . " WHERE fieldmrkt_id = ?", [$id])->fetch();
        // Если данные о странице успешно получены — формируем теги.
        if ($page_data) {
            // Определяем параметры для построения URL страницы.
            // Если у страницы есть алиас (человекопонятный URL), используем 'al' => алиас.
            // Если алиаса нет, используем 'id' => идентификатор страницы.
            $url_params = empty($page_data['fieldmrkt_alias']) 
                ? ['c' => $page_data['fieldmrkt_cat'], 'id' => $id]
                : ['c' => $page_data['fieldmrkt_cat'], 'al' => $page_data['fieldmrkt_alias']];
				
            // Строка, в которую будем складывать все сгенерированные теги <link>.
            $alternate_tags = '';
			
            // Получаем основной URL сайта (например, "https://cotonti.local") и
            // удаляем возможный слеш в конце, чтобы избежать двойных слешей при склейке.
            $main_url = rtrim(Cot::$cfg['mainurl'], '/');
            
            // Теги для всех переведённых языков, кроме основного
            // Цикл по всем языкам, на которые есть перевод данной страницы.
            foreach ($translated_locales as $lc) {
                // Пропускаем основной язык сайта ($cfg['defaultlang']). Для него
                // hreflang не генерируется, потому что его версия обычно является
                // канонической и может не иметь языкового префикса в URL (omitmain).
                if ($lc == Cot::$cfg['defaultlang']) continue;
				
                // Копируем базовые параметры URL и добавляем параметр 'l' с кодом языка.
                $params = $url_params;
                $params['l'] = $lc;
				
                // Формируем полный URL альтернативной версии с помощью функции cot_url().
                $url = $main_url . '/' . cot_url('market', $params, '', false, false);
				
                // Преобразуем короткий код языка в полный IETF-тег (например, 'ua' -> 'uk-UA').
                // Если соответствия нет — используем оригинальный короткий код.
                $hreflang = isset($i18n4marketpro_ietf_map[$lc]) ? $i18n4marketpro_ietf_map[$lc] : $lc;
				
                // Добавляем тег <link> к общей строке. Экранируем URL для безопасности.
                $alternate_tags .= '<link rel="alternate" hreflang="' . $hreflang . '" href="' . htmlspecialchars($url) . '">' . "\n";
            }
            
            // Формируем тег x-default — версию, которая будет показана пользователям,
            // чей язык/регион не совпадает ни с одним из явно указанных в hreflang.
            // Обычно это либо URL основного языка без префикса (omitmain=1),
            // либо URL основного языка с префиксом (omitmain=0).
            $default_params = $url_params;
			
            // Проверяем настройку плагина i18n: нужно ли убирать параметр 'l'
            // для основного языка (omitmain = 1).
            if ((int)Cot::$cfg['plugin']['i18n4marketpro']['omitmain'] === 1) {
                // omitmain включён: основной язык не имеет префикса в URL.
                // Удаляем параметр 'l' из массива параметров.
                unset($default_params['l']);
                // Генерируем URL без языкового префикса.
                $default_url = $main_url . '/' . cot_url('market', $default_params, '', false, true);
            } else {
                // omitmain выключен: основной язык имеет префикс в URL.
                // Добавляем параметр 'l' со значением языка по умолчанию.
                $default_params['l'] = Cot::$cfg['defaultlang'];
                // Генерируем URL с языковым префиксом.
                $default_url = $main_url . '/' . cot_url('market', $default_params, '', false, false);
            }
            // Добавляем тег x-default к общей строке.
            $alternate_tags .= '<link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($default_url) . '">' . "\n";

            // Передаём сгенерированные теги в шаблонизатор под именем {ALTERNATE_TAGS}.
            $t->assign('ALTERNATE_TAGS', $alternate_tags);
			
            // Парсим соответствующий блок в шаблоне header.tpl, если он там определён.
            $t->parse('HEADER.ALTERNATE_TAGS');
        }
    }
}

// ========== 4. ПЕРЕДАЧА ЭКСТРАПОЛЕЙ ПЕРЕВОДА В HEADER ==========
if ($env['ext'] == 'market' && isset($id) && $id > 0) {
    // Загружаем перевод для текущего языка, если ещё не загружен
    if (empty($pag_i18n4marketpro) && !empty($i18n4marketpro_locale)) {
        $pag_i18n4marketpro = cot_i18n4marketpro_get_page($id, $i18n4marketpro_locale);
    }
    
    if (!empty($pag_i18n4marketpro) && !empty(Cot::$extrafields[Cot::$db->i18n4marketpro_pages])) {
        // Загружаем данные оригинальной страницы для парсера
        $page_data = Cot::$db->query("SELECT fieldmrkt_parser FROM " . Cot::$db->market . " WHERE fieldmrkt_id = ?", array($id))->fetch();
        $parser = $page_data['fieldmrkt_parser'] ?? Cot::$cfg['market']['marketparser'];
        
        foreach (Cot::$extrafields[Cot::$db->i18n4marketpro_pages] as $exfld) {
            $field_name = $exfld['field_name'];
            $tag = 'I18N_HEADER_' . strtoupper($field_name);
            $value = $pag_i18n4marketpro['ipage_' . $field_name] ?? '';
            
            // Присваиваем теги для header.tpl
            $t->assign([
             // без экранирования
             // $tag                     => cot_build_extrafields_data('i18n', $exfld, $value, $parser), 
             // экранируем кавычки и другие специальные символы
				$tag                     => htmlspecialchars(cot_build_extrafields_data('i18n4marketpro', $exfld, $value, $parser), ENT_QUOTES, 'UTF-8'), 
                $tag . '_TITLE'          => cot_extrafield_title($exfld, 'i18n4marketpro_'),
                $tag . '_VALUE'          => $value,
            ]);
        }
    } else {
        // Сброс тегов, если перевод отсутствует
        if (!empty(Cot::$extrafields[Cot::$db->i18n4marketpro_pages])) {
            foreach (Cot::$extrafields[Cot::$db->i18n4marketpro_pages] as $exfld) {
                $tag = 'I18N_HEADER_' . strtoupper($exfld['field_name']);
                $t->assign([
                    $tag             => '',
                    $tag . '_TITLE'  => '',
                    $tag . '_VALUE'  => '',
                ]);
            }
        }
    }
}