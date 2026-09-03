<?php
/**
 * Russian Language File for Content Internationalization Plugin
 *
 * @package i18n4marketpro
 * @copyright (c) Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

// Plugin configuration

$L['cfg_cats'] = 'Корневые категории для применения i18n4marketpro';
$L['cfg_cats_hint'] = 'Коды уже созданных родительских категорий в <a href="' . cot_url('admin', 'm=structure&n=market') . '"><strong>структуре модуля "Market PRO"</strong></a> через запятую, например: <br><code>computers,notebooks,tablets,mobile-phones</code>';

$L['cfg_locales'] = 'Список локалей сайта';
$L['cfg_locales_hint'] = '
Каждая локаль с новой строки, формат: <br>locale_code|Заголовок локали<br>
	пример:
	<br><strong>
	en|English<br>
	ru|Русский<br>
	</strong>
';
$L['cfg_omitmain'] = 'Опускать параметр языка в URL, если он указывает на основной язык';
$L['cfg_omitmain_hint'] = '"Нет" по-умолчанию';
$L['cfg_rewrite'] = 'Включить ЧПУ для параметра языка в ссылках';
$L['cfg_rewrite_hint'] = '"Да" по-умолчанию.<br> Требует ручного обновления файла .htaccess в корне сайта<br>
должны быть строки вида:
<br><code>
# Language selector<br>
RewriteRule ^(ru|en)/(.*) $2?l=$1 [QSA,NC,NE,DPI]
</code>';
$L['cfg_cookie'] = 'Запоминать выбранный язык в cookie';
$L['cfg_cookie_hint'] = '"Нет" по-умолчанию';

$L['info_desc'] = 'Поддержка многоязычного контента в ядре и расширениях';

// Plugin strings

$L['i18n4marketpro_adding'] = 'Добавление нового перевода';
$L['i18n4marketpro_confirm_delete'] = 'Вы действительно хотите удалить перевод?';
$L['i18n4marketpro_delete'] = 'Удалить перевод';
$L['i18n4marketpro_editing'] = 'Редактирование перевода';
$L['i18n4marketpro_incorrect_locale'] = 'Неверная локаль';
$L['i18n4marketpro_items_added'] = '{$cnt} элементов добавлено';
$L['i18n4marketpro_items_removed'] = '{$cnt} элементов удалено';
$L['i18n4marketpro_items_updated'] = '{$cnt} элементов обновлено';
$L['i18n4marketpro_locale_selection'] = 'Выбор локали';
$L['i18n4marketpro_localized'] = 'Локализованное';
$L['i18n4marketpro_no_categories'] = 'Не выбраны категории для перевода. Установить их можно в <a href="%s">настройках интернационализации<a>';
$L['i18n4marketpro_original'] = 'Оригинал';
$L['i18n4marketpro_structure'] = 'Интернационализация структуры';
$L['i18n4marketpro_translate'] = 'Перевести';
$L['i18n4marketpro_translation'] = 'Перевод';
$L['i18n4marketpro_translations_items'] = 'Описание в переводе на';

$L['i18n4marketpro_pages'] = 'Переводы товаров';
