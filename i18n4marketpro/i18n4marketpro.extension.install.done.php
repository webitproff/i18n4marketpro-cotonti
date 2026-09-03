<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=extension.install.done
[END_COT_EXT]
==================== */

/**
 * Adds i18n4marketpro support to tags when installing the tags plugin after i18n4marketpro
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 *
 * @var string $extensionCode Extension code
 */

defined('COT_CODE') or die('Wrong URL');

if ($extensionCode === 'tags') {
    global $L, $R; // for included file

    require_once cot_incfile('i18n4marketpro', 'plug');

    cot_i18n4marketpro_installTagsIntegration();
}
