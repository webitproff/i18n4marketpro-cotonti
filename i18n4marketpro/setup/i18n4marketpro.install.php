<?php
/**
 * Installation handler
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

use cot\extensions\ExtensionsService;

defined('COT_CODE') or die('Wrong URL');

// Tags integration
if (ExtensionsService::getInstance()->isInstalled('tags')) {
    global $L, $R; // for included file

    require_once cot_incfile('i18n4marketpro', 'plug');

    cot_i18n4marketpro_installTagsIntegration();
}
