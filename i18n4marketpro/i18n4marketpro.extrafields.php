<?php
/* ====================
  [BEGIN_COT_EXT]
  Hooks=admin.extrafields.first
  [END_COT_EXT]
==================== */

// файл новый i18n4marketpro.extrafields.php
// Путь: plugins/i18n4marketpro/i18n4marketpro.extrafields.php
defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('i18n4marketpro', 'plug');

$extra_whitelist[$db_i18n4marketpro_pages] = [
    'name'    => $db_i18n4marketpro_pages,
    'caption' => $L['i18n4marketpro_pages'],
    'type'    => 'plug',
    'code'    => 'i18n4marketpro',
    'tags'    => [
        'i18n4marketpro.page.tpl' => '{I18N_PAGE_FORM_XXXXX}, {I18N_PAGE_FORM_XXXXX_TITLE}',
    ]
];