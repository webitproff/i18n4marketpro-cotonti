<?php
/**
 * Polish Language File for Content Internationalization Plugin
 *
 * @package i18n4marketpro
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

// Plugin configuration

$L['cfg_cats'] = 'Kategoria główna do zastosowania i18n4marketpro';
$L['cfg_cats_hint'] = 'Kody już utworzonych kategorii nadrzędnych w <a href="' . cot_url('admin', 'm=structure&n=market') . '"><strong>strukturze modułu "Market PRO"</strong></a>, oddzielone przecinkami, na przykład: <br><code>computers,notebooks,tablets,mobile-phones</code>';

$L['cfg_locales'] = 'Lista lokalizacji strony';
$L['cfg_locales_hint'] = '
Każda lokalizacja w nowej linii, format: <br>locale_code|Tytuł lokalizacji<br>
	Przykład:
	<br><strong>
	en|English<br>
	ru|Русский<br>
	</strong>
';
$L['cfg_omitmain'] = 'Pomijać parametr języka w URL, jeśli wskazuje on na główny język';
$L['cfg_omitmain_hint'] = '"Nie" domyślnie';
$L['cfg_rewrite'] = 'Włączyć przyjazne linki dla parametru języka w adresach URL';
$L['cfg_rewrite_hint'] = '"Tak" domyślnie.<br> Wymaga ręcznej aktualizacji pliku .htaccess w katalogu głównym strony<br>
Muszą być w nim wpisy w formacie:
<br><code>
# Wybór języka<br>
RewriteRule ^(ru|en)/(.*) $2?l=$1 [QSA,NC,NE,DPI]
</code>';
$L['cfg_cookie'] = 'Zapamiętać wybrany język w cookie';
$L['cfg_cookie_hint'] = '"Nie" domyślnie';

$L['info_desc'] = 'Wsparcie dla wielojęzycznego kontentu w rdzeniu i rozszerzeniach';

// Plugin strings

$L['i18n4marketpro_adding'] = 'Dodawanie nowego tłumaczenia';
$L['i18n4marketpro_confirm_delete'] = 'Czy na pewno chcesz usunąć to tłumaczenie?';
$L['i18n4marketpro_delete'] = 'Usuń tłumaczenie';
$L['i18n4marketpro_editing'] = 'Edycja tłumaczenia';
$L['i18n4marketpro_incorrect_locale'] = 'Niepoprawna lokalizacja';
$L['i18n4marketpro_items_added'] = '{$cnt} elementów dodanych';
$L['i18n4marketpro_items_removed'] = '{$cnt} elementów usuniętych';
$L['i18n4marketpro_items_updated'] = '{$cnt} elementów zaktualizowanych';
$L['i18n4marketpro_locale_selection'] = 'Wybór lokalizacji';
$L['i18n4marketpro_localized'] = 'Zlokalizowane';
$L['i18n4marketpro_no_categories'] = 'Nie wybrano kategorii do tłumaczenia. Można je ustawić w <a href="%s">ustawieniach internacjonalizacji<a>';
$L['i18n4marketpro_original'] = 'Oryginał';
$L['i18n4marketpro_structure'] = 'Internacjonalizacja struktury';
$L['i18n4marketpro_translate'] = 'Przetłumacz';
$L['i18n4marketpro_translation'] = 'Tłumaczenie';
$L['i18n4marketpro_translations_items'] = 'Opis w tłumaczeniu na';

$L['i18n4marketpro_pages'] = 'Translations Product Pages';