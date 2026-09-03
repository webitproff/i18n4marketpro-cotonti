<?php
/**
 * Ukrainian Language File for Content Internationalization Plugin
 *
 * @package i18n4marketpro
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

// Plugin configuration

$L['cfg_cats'] = 'Кореневі категорії для застосування i18n4marketpro';
$L['cfg_cats_hint'] = 'Коди вже створених батьківських категорій у <a href="' . cot_url('admin', 'm=structure&n=market') . '"><strong>структурі модуля "Market PRO"</strong></a> через кому, наприклад: <br><code>computers,notebooks,tablets,mobile-phones</code>';

$L['cfg_locales'] = 'Список локалей сайту';
$L['cfg_locales_hint'] = '
Кожна локаль з нового рядка, формат: <br>locale_code|Назва локалі<br>
	приклад:
	<br><strong>
	en|English<br>
	ru|Російська<br>
	</strong>
';
$L['cfg_omitmain'] = 'Опускати параметр мови в URL, якщо він вказує на основну мову';
$L['cfg_omitmain_hint'] = '"Ні" за замовчуванням';
$L['cfg_rewrite'] = 'Увімкнути ЧПУ для параметра мови у посиланнях';
$L['cfg_rewrite_hint'] = '"Так" за замовчуванням.<br> Потребує ручного оновлення файлу .htaccess у корені сайту<br>
повинні бути рядки виду:
<br><code>
# Language selector<br>
RewriteRule ^(ru|en)/(.*) $2?l=$1 [QSA,NC,NE,DPI]
</code>';
$L['cfg_cookie'] = 'Запам’ятовувати обрану мову у cookie';
$L['cfg_cookie_hint'] = '"Ні" за замовчуванням';

$L['info_desc'] = 'Підтримка багатомовного контенту у ядрі та розширеннях';

// Plugin strings

$L['i18n4marketpro_adding'] = 'Додавання нового перекладу';
$L['i18n4marketpro_confirm_delete'] = 'Ви справді хочете видалити переклад?';
$L['i18n4marketpro_delete'] = 'Видалити переклад';
$L['i18n4marketpro_editing'] = 'Редагування перекладу';
$L['i18n4marketpro_incorrect_locale'] = 'Неправильна локаль';
$L['i18n4marketpro_items_added'] = '{$cnt} елементів додано';
$L['i18n4marketpro_items_removed'] = '{$cnt} елементів видалено';
$L['i18n4marketpro_items_updated'] = '{$cnt} елементів оновлено';
$L['i18n4marketpro_locale_selection'] = 'Вибір локалі';
$L['i18n4marketpro_localized'] = 'Локалізовано';
$L['i18n4marketpro_no_categories'] = 'Не обрано категорії для перекладу. Встановити їх можна в <a href="%s">налаштуваннях інтернаціоналізації<a>';
$L['i18n4marketpro_original'] = 'Оригінал';
$L['i18n4marketpro_structure'] = 'Інтернаціоналізація структури';
$L['i18n4marketpro_translate'] = 'Перекласти';
$L['i18n4marketpro_translation'] = 'Переклад';
$L['i18n4marketpro_translations_items'] = 'Опис у перекладі на';
$L['i18n4marketpro_pages'] = 'Переклад товарів';