<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=input
[END_COT_EXT]
==================== */

/**
 * Locale selection
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements
require_once cot_incfile('i18n4marketpro', 'plug');

/**
 * @var ?array<string, string> $i18n4marketpro_locales Locales List ['en' => 'English', 'ru' => 'Русский']
 */
$i18n4marketpro_locales = null;

$i18n4marketpro_fallback = '';

// Load valid locales
if (!empty(Cot::$cache)) {
    $i18n4marketpro_locales = Cot::$cache->db->get('locales', 'i18n4marketpro');
}
if (!$i18n4marketpro_locales) {
	cot_i18n4marketpro_load_locales();
    if (!empty(Cot::$cache)) {
        Cot::$cache->db->store('locales', $i18n4marketpro_locales, 'i18n4marketpro');
    }
}

// Select a locale
$i18n4marketpro_locale = cot_import('l', 'G', 'ALP');
if (empty($i18n4marketpro_locale) && Cot::$cfg['plugin']['i18n4marketpro']['cookie']) {
	// Try restoring from cookie
	$i18n4marketpro_locale = cot_import('i18n4marketpro_locale', 'C', 'ALP');
}

if (empty($i18n4marketpro_locale) || !isset($i18n4marketpro_locales[$i18n4marketpro_locale])) {
	$i18n4marketpro_locale = Cot::$usr['lang'];
}
if (file_exists(Cot::$cfg['lang_dir'] . '/' . $i18n4marketpro_locale)) {
	// Switch interface language for guests
	$i18n4marketpro_fallback = Cot::$usr['lang'];
	if (!Cot::$cfg['forcedefaultlang']) {
        Cot::$usr['lang'] = $i18n4marketpro_locale;
		$lang = $i18n4marketpro_locale;
	}
} else {
	$i18n4marketpro_locale = Cot::$cfg['defaultlang'];
}

// The flag to omit language parameter
$i18n4marketpro_omit = Cot::$cfg['plugin']['i18n4marketpro']['omitmain'] && $i18n4marketpro_locale == $i18n4marketpro_fallback;

if (!$i18n4marketpro_omit) {
	$cot_url_appendix['l'] = $i18n4marketpro_locale;
}

$i18n4marketpro_notmain = ($i18n4marketpro_locale != Cot::$cfg['defaultlang']);
list($i18n4marketpro_read, $i18n4marketpro_write, $i18n4marketpro_admin, $i18n4marketpro_edit) = cot_auth('plug', 'i18n4marketpro', 'RWA1');

// Remember in cookie if needed
$cookie_locale = cot_import('i18n4marketpro_locale', 'COOKIE', 'ALP');
if (Cot::$cfg['plugin']['i18n4marketpro']['cookie'] && $i18n4marketpro_locale !== $cookie_locale) {
	if ($i18n4marketpro_locale === Cot::$cfg['defaultlang'] && $cookie_locale) {
		cot_setcookie('i18n4marketpro_locale', null, -1);
	} elseif ($i18n4marketpro_locale !== Cot::$cfg['defaultlang']) {
		cot_setcookie('i18n4marketpro_locale', $i18n4marketpro_locale);
	}
}

if ($i18n4marketpro_locale) {
    require_once cot_langfile('i18n4marketpro', 'plug', Cot::$cfg['defaultlang'], $i18n4marketpro_locale);
}
