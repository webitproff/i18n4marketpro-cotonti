<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.structure.tags
Tags=admin.structure.tpl:{ADMIN_STRUCTURE_I18N4_LINK},{ADMIN_STRUCTURE_I18N_URL}
[END_COT_EXT]
==================== */

/**
 * Locale selection
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 *
 * @var XTemplate $t
 * @var string $n Extension code
 */

defined('COT_CODE') or die('Wrong URL');

if ($n == 'market') {
    $t->assign(array(
        'ADMIN_STRUCTURE_I18N_LINK' => cot_rc_link(
            cot_url('plug', 'e=i18n4marketpro&m=structure'),
            Cot::$L['i18n4marketpro_structure']
        ),
        'ADMIN_STRUCTURE_I18N_URL' => cot_url('plug', 'e=i18n4marketpro&m=structure')
    ));
}
