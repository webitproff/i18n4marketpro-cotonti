<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=market.delete.first
[END_COT_EXT]
==================== */

use cot\extensions\ExtensionsDictionary;

/**
 * Removes page translations on page delete
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 *
 * @var int $id Deleting page id
 *
 * Translation will be put to trashcan here: plugins/trashcan/trashcan.page.delete.php
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('i18n4marketpro', ExtensionsDictionary::TYPE_PLUGIN);
// Удаляем переводы, даже если плагин был отключён (проверка таблицы)

if (Cot::$db->tableExists('cot_i18n4marketpro_pages')) {
    Cot::$db->delete(Cot::$db->quoteTableName('cot_i18n4marketpro_pages'), 'ipage_id = ?', $id);
    cot_log("Deleted translate for market i18n4marketpro#" . $id, 'i18n4marketpro', 'market', 'delete');
}

/* if (Cot::$db->tableExists('cot_i18n4marketpro_pages')) {
    Cot::$db->delete(Cot::$db->i18n4marketpro_pages, 'ipage_id = ?', $id);
    cot_log("Deleted translate for market i18n4marketpro#" . $id, 'i18n4marketpro', 'market', 'delete');
} */
/* Cot::$db->delete(Cot::$db->i18n4marketpro_pages, 'ipage_id = ?', $id);
cot_log("Deleted translate for market #" . $id, 'i18n4marketpro', 'market', 'delete'); */
