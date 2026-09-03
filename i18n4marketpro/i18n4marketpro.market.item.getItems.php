<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=market.item.getItems
[END_COT_EXT]
==================== */

declare(strict_types=1);

use cot\dto\ItemDto;

/**
 * I18n for pages
 *
 * @package I18N4MARKETPRO
 * @copyright (c) Cotonti Team
 * @license BSD
 *
 * @var bool $withFullItemData
 * @var list<int> $pageIds
 * @var list<ItemDto> $result
 */

defined('COT_CODE') or die('Wrong URL');

global $i18n4marketpro_read, $i18n4marketpro_locale, $i18n4marketpro_notmain;

$i18n4marketproEnabled = !empty($i18n4marketpro_read);

if (!$i18n4marketproEnabled || !$i18n4marketpro_notmain) {
    return;
}

$i18n4marketproTable = Cot::$db->quoteTableName(Cot::$db->i18n4marketpro_pages);

$sqlSelect = $withFullItemData
    ? "{$i18n4marketproTable}.*"
    : "{$i18n4marketproTable}.ipage_title, {$i18n4marketproTable}.ipage_desc";

$query = "SELECT $sqlSelect FROM $i18n4marketproTable "
    . "WHERE {$i18n4marketproTable}.ipage_id IN (" . implode(',', $pageIds) . ") "
    . "AND {$i18n4marketproTable}.ipage_locale = '$i18n4marketpro_locale'";

$data = Cot::$db->query($query)->fetchAll();
$i18n4marketproData = [];
foreach ($data as $row) {
    $i18n4marketproData[$row['ipage_id']] = $row;
}
unset($data);

foreach ($result as $row) {
    if (empty($i18n4marketproData[$row->id])) {
        continue;
    }
    if (!empty($i18n4marketproData[$row->id]['ipage_title'])) {
        $row->title = $i18n4marketproData[$row->id]['ipage_title'];
    }
    if (!empty($i18n4marketproData[$row->id]['ipage_desc'])) {
        $row->description = $i18n4marketproData[$row->id]['ipage_desc'];
    }

    if ($withFullItemData) {
        $row->data = array_merge($row->data, $i18n4marketproData[$row->id]);
    }

    $catI18n = cot_i18n4marketpro_get_cat($row->categoryCode, $i18n4marketpro_locale);
    if ($catI18n) {
        $row->categoryTitle = $catI18n['title'];
    }
}
