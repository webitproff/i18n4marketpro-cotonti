<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=market.list.rowcat.loop
[END_COT_EXT]
==================== */
/**
 * Redefines category tags in a list of subcategories (including root list)
 *
 * @package I18N4MARKETPRO
 */

defined('COT_CODE') or die('Wrong URL');

global $i18n4marketpro_locale, $i18n4marketpro_read, $c;

// ИСПРАВЛЕНО: теперь перевод категорий применяется ВЕЗДЕ, где отображается список категорий
// Не только внутри подкатегории ($i18n4marketpro_notmain), но и в корне /market

if ($i18n4marketpro_read && $i18n4marketpro_locale && $i18n4marketpro_locale !== Cot::$cfg['defaultlang']) {
    $cat_trans = cot_i18n4marketpro_get_cat($x, $i18n4marketpro_locale);

    if ($cat_trans) {
        // Параметры URL: добавляем l=locale, если не omitmain или локаль не дефолтная
        $urlparams = ['c' => $x];
        if (!Cot::$cfg['plugin']['i18n4marketpro']['omitmain'] || $i18n4marketpro_locale != Cot::$cfg['defaultlang']) {
            $urlparams['l'] = $i18n4marketpro_locale;
        }

        $t->assign([
            'LIST_CAT_ROW_URL'         => cot_url('market', $urlparams),
            'LIST_CAT_ROW_TITLE'       => htmlspecialchars($cat_trans['title']),
            'LIST_CAT_ROW_DESCRIPTION' => $cat_trans['desc'],
        ]);

        // ДОБАВЛЕНО: обновляем иконку, если нужно (чтобы title/desc в alt были переведены)
        if (!empty($structure['market'][$x]['icon'])) {
            $t->assign([
                'LIST_CAT_ROW_ICON' => cot_rc('img_structure_cat', [
                    'icon'  => $structure['market'][$x]['icon'],
                    'title' => htmlspecialchars($cat_trans['title']),
                    'desc'  => htmlspecialchars($cat_trans['desc']),
                ]),
            ]);
        }
    }
}