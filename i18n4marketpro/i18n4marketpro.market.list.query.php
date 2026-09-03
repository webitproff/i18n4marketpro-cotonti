<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=market.list.query
Order=5
[END_COT_EXT]
==================== */

/**
 * Modifies page selection query if not in main category
 * to select only translated pages and localize them
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
if (!empty($c) && isset(Cot::$structure['market'][$c])) {

$i18n4marketpro_enabled = $i18n4marketpro_read && cot_i18n4marketpro_enabled($c);

    if ($i18n4marketpro_enabled && $i18n4marketpro_notmain)
    {
        $list_url_path = array('c' => $c, 'ord' => $o, 'p' => $p);
        if ($s != $cfg['market']['cat_' . $c]['marketorder'])
        {
            $list_url_path['s'] = $s;
        }
        if ($w != $cfg['market']['cat_' . $c]['marketway'])
        {
            $list_url_path['w'] = $w;
        }
        if (!$cfg['plugin']['i18n4marketpro']['omitmain'] || $i18n4marketpro_locale != $cfg['defaultlang'])
        {
            $list_url_path['l'] = $i18n4marketpro_locale;
        }
        $list_url = cot_url('market', $list_url_path);

        $join_columns .= ',i18n4marketpro.*';
        $join_condition .= " LEFT JOIN $db_i18n4marketpro_pages AS i18n4marketpro ON i18n4marketpro.ipage_id = p.fieldmrkt_id AND i18n4marketpro.ipage_locale = '$i18n4marketpro_locale' AND i18n4marketpro.ipage_id IS NOT NULL";
    }
}

// === SEARCH IN TRANSLATIONS ===
if (
    !empty($sq)
    && $i18n4marketpro_read
    && $i18n4marketpro_locale != $cfg['defaultlang']
) {
    $sq_like = Cot::$db->quote('%' . $sq . '%');

    // JOIN ТОЛЬКО ДЛЯ ПОИСКА
    $join_condition .= "
        LEFT JOIN $db_i18n4marketpro_pages AS i18n_search
            ON i18n_search.ipage_id = p.fieldmrkt_id
           AND i18n_search.ipage_locale = " . Cot::$db->quote($i18n4marketpro_locale);

    // WHERE: оригинал + перевод
    $where['search'] = "(
        p.fieldmrkt_title LIKE $sq_like
        OR p.fieldmrkt_text LIKE $sq_like
        OR i18n_search.ipage_title LIKE $sq_like
        OR i18n_search.ipage_text LIKE $sq_like
    )";
}
