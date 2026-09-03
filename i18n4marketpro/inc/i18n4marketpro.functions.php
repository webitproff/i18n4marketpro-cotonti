<?php
/**
 * Content Internationalization API
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('i18n4marketpro', 'plug');
require_once cot_incfile('i18n4marketpro', 'plug', 'resources');

Cot::$db->registerTable('i18n4marketpro_locales'); // пока непонятно
Cot::$db->registerTable('i18n4marketpro_pages');
Cot::$db->registerTable('i18n4marketpro_structure');

/**
 * Builds internationalized category path
 *
 * @param string $area Area code
 * @param string $cat Category code
 * @param string $locale Locale code
 * @return list<array{0: string, 1:string}>
 * [
 *   0 => [
 *      0 => 'url',
 *      1 => 'Title'
 *   ],
 *   1 => [
 *      0 => 'url',
 *      1 => 'Title'
 *   ],
 *   ...
 * ]
 *
 */
function cot_i18n4marketpro_build_catpath($area, $cat, $locale)
{
	global $i18n4marketpro_structure;

	$result = [];
	$pathCodes = explode('.', Cot::$structure[$area][$cat]['path']);
	foreach ($pathCodes as $code) {
		if ($code !== 'system') {
			if (empty($i18n4marketpro_structure[$code][$locale]['title'])) {
				$title = Cot::$structure[$area][$code]['title'];
				$url = cot_url($area, ['c' => $code]);
			} else {
				$title = $i18n4marketpro_structure[$code][$locale]['title'];
				$url = cot_url($area, ['c' =>  $code, 'l' => $locale]);
			}
			$result[] = [$url, $title];
		}
	}

	return $result;
}

/**
 * Checks if internationalization is enabled in a selected category
 *
 * @param string $cat Category code
 * @return bool TRUE if enabled, FALSE if not
 */
function cot_i18n4marketpro_enabled($cat)
{
	static $i18n4marketpro_cats = false;

	if (!$i18n4marketpro_cats) {
		// Get configured cats
		$i18n4marketpro_cats = explode(',', Cot::$cfg['plugin']['i18n4marketpro']['cats']);
		$i18n4marketpro_cats = array_map('trim', $i18n4marketpro_cats);
	}

	return in_array(cot_structure_parents('market', $cat, 'first'), $i18n4marketpro_cats);

}

/**
 * Fetches translation row for a specific category
 *
 * @param string $cat Category code
 * @param string $locale Locale code
 * @return mixed Category translation row or FALSE if not found
 */
function cot_i18n4marketpro_get_cat($cat, $locale)
{
	global $i18n4marketpro_structure;

	return isset($i18n4marketpro_structure[$cat][$locale]) ? $i18n4marketpro_structure[$cat][$locale] : false;
}

/**
 * Fetches translation row for a specific page
 *
 * @param int $page_id Page ID
 * @param string $locale Locale code
 * @return mixed Page translation row (array) on success or FALSE on error
 * @global CotDB $db
 */
function cot_i18n4marketpro_get_page($page_id, $locale)
{
	global $db, $db_i18n4marketpro_pages;

	$res = $db->query("SELECT * FROM $db_i18n4marketpro_pages WHERE ipage_id = ? AND ipage_locale = ?",
		array((int) $page_id, $locale));
	return $res->rowCount() == 1 ? $res->fetch() : false;
}

/**
 * Returns a list of all locales available for a category
 *
 * @param string $cat Category code
 * @return array List of locale codes
 */
function cot_i18n4marketpro_list_cat_locales($cat)
{
	global $i18n4marketpro_structure;

	return (isset($i18n4marketpro_structure[$cat]) && is_array($i18n4marketpro_structure[$cat]))
		? array_keys($i18n4marketpro_structure[$cat])
		: array();
}

/**
 * Returns a list of all locales available for a specific page
 *
 * @param int $page_id Page ID
 * @return array List of locale codes
 * @global CotDB $db
 */
function cot_i18n4marketpro_list_page_locales($page_id)
{
	global $db, $db_i18n4marketpro_pages;

	$res = $db->query("SELECT DISTINCT ipage_locale FROM $db_i18n4marketpro_pages
		WHERE ipage_id = ?", array((int) $page_id));
	return $res->fetchAll(PDO::FETCH_COLUMN, 0);
}

/**
 * Loads registered locales
 *
 * @global ?array<string, string> $i18n4marketpro_locales Available locale data
 */
function cot_i18n4marketpro_load_locales()
{
	global $i18n4marketpro_locales, $cot_languages;

	$lines = preg_split('#\r?\n#', Cot::$cfg['plugin']['i18n4marketpro']['locales']);
	foreach ($lines as $line) {
		$lc = explode('|', $line);
		$lc = array_map('trim', $lc);
		if (!empty($lc[0]) && !empty($lc[1])) {
			$i18n4marketpro_locales[$lc[0]] = $lc[1];
		}

        // Todo use locale_get_display_language() if $cot_languages[Cot::$cfg['defaultlang']] is not set
        if (!array_key_exists(Cot::$cfg['defaultlang'], $i18n4marketpro_locales)) {
            $i18n4marketpro_locales[Cot::$cfg['defaultlang']] = isset($cot_languages[Cot::$cfg['defaultlang']]) ?
                $cot_languages[Cot::$cfg['defaultlang']] : Cot::$cfg['defaultlang'];
        }
	}
}

/**
 * Loads structure internationalization data
 *
 * @global array $i18n4marketpro_structure Structure localizations
 * @global CotDB $db
 */
function cot_i18n4marketpro_load_structure()
{
	global $db, $db_i18n4marketpro_structure, $i18n4marketpro_structure;

	$res = $db->query("SELECT * FROM $db_i18n4marketpro_structure");
	while ($row = $res->fetch()) {
		$i18n4marketpro_structure[$row['istructure_code']][$row['istructure_locale']] = array(
			'title' => $row['istructure_title'],
			'desc' => $row['istructure_desc']
		);
	}
	$res->closeCursor();
}

/**
 * Adds i18n4marketpro support to tags
 * @return bool
 */
function cot_i18n4marketpro_installTagsIntegration()
{
    if (!cot_extension_installed('tags')) {
        return false;
    }

    require_once cot_incfile('tags', 'plug');

    // Add tag_locale column
    if (
        !Cot::$db->fieldExists(Cot::$db->tag_references, 'tag_locale')
    ) {
        Cot::$db->query('ALTER TABLE ' . Cot::$db->tag_references .
            " ADD COLUMN tag_locale VARCHAR(8) NOT NULL DEFAULT ''");
        Cot::$db->query('ALTER TABLE ' . Cot::$db->tag_references . ' DROP PRIMARY KEY');
        Cot::$db->query('ALTER TABLE ' . Cot::$db->tag_references .
            ' ADD PRIMARY KEY (tag, tag_area, tag_item, tag_locale)');
    }

    return true;
}


