<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=trashcan.api
[END_COT_EXT]
==================== */

/**
 * Trash can functions for i18n4marketpro
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('i18n4marketpro', 'plug');

// Register restoration table
$trash_types['i18n4marketpro_page'] = Cot::$db->i18n4marketpro_pages;

// Actually no functions are required so far
