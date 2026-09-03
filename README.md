# Справочное руководство по плагину i18n4marketpro для CMF Cotonti

## Оглавление

- [Введение](#introduction)
- [Установка и зависимости](#installation)
- [Структура файлов и папок](#structure)
- [Таблицы базы данных](#database)
- [Конфигурация плагина](#configuration)
- [Локализация и языковые файлы](#localization)
- [API и основные функции](#api-functions)
- [Перевод структуры категорий](#category-translation)
- [Перевод товаров (market)](#product-translation)
- [Интеграция с дополнительными полями](#extrafields)
- [Шаблоны плагина](#templates)
- [Хуки и интеграция с Cotonti](#hooks)
- [SEO-аспекты: hreflang и языковые переключатели](#seo)
- [Примеры использования в шаблонах](#usage-examples)
- [Приложение: список хуков и тегов](#appendix)

---

<a name="introduction"></a>
## 1. Введение

Плагин **i18n4marketpro** предназначен для организации многоязычного контента в системе управления сайтом **CMF Cotonti** версии 1.0 и выше. Он расширяет возможности базового плагина **i18n**, добавляя поддержку интернационализации для модуля **market** (торговая площадка / каталог товаров) и его структуры категорий.

Плагин позволяет:
- создавать переводы названий и описаний категорий модуля market;
- создавать переводы товаров (название, описание, полный текст);
- управлять переводами через отдельные интерфейсы;
- автоматически подменять заголовки, описания и тексты товаров на выбранном языке;
- предоставлять переключатели языков в шаблонах;
- генерировать SEO-теги `hreflang` для альтернативных языковых версий страниц;
- интегрироваться с системой дополнительных полей (extrafields) для перевода мета-тегов и других данных.

Плагин разработан как модификация оригинального плагина **i18n** с фокусом на модуль market (Market PRO). Автор модификации — **webitproff**, оригинальный автор — Trustmaster, Cotonti Team.

Плагин работает в связке с базовым плагином **i18n**, который отвечает за определение текущего языка, загрузку локалей, управление правами и другие общие задачи. Сам i18n4marketpro добавляет специфическую функциональность для модуля market.

---

<a name="installation"></a>
## 2. Установка и зависимости

### Требования
- CMF Cotonti версии 1.0.0 .
- Модуль **market** (Market PRO) должен быть установлен и активирован.
- Плагин **i18n** должен быть установлен и активирован (указан в `Requires_plugins=i18n`).
- (Опционально) Плагин **tags** — для интеграции с тегами (функция `cot_i18n4marketpro_installTagsIntegration()`).
- (Опционально) Плагин **trashcan** — для перемещения удалённых переводов в корзину.

### Установка
1. Скопируйте папку `i18n4marketpro` в каталог `plugins/` вашего сайта.
2. Зайдите в админ-панель Cotonti → «Расширения».
3. Найдите плагин «Content Internationalization» (i18n4marketpro) и нажмите «Установить».
4. При установке будут выполнены SQL-запросы из файла `setup/i18n4marketpro.install.sql` для создания таблиц.
5. После установки перейдите в настройки плагина и укажите корневые категории, которые будут переводиться, список локалей и другие параметры.

### Обновление
При обновлении с более ранних версий плагин может потребовать ручного обновления базы данных (например, добавление колонки `tag_locale` в таблицу тегов через функцию `cot_i18n4marketpro_installTagsIntegration()`). Следите за сообщениями в админ-панели.

---

<a name="structure"></a>
## 3. Структура файлов и папок

Плагин имеет следующую файловую структуру (согласно предоставленному списку):

```
i18n4marketpro/
├── inc/ 
│   ├── i18n4marketpro.functions.php      – основные функции API
│   ├── i18n4marketpro.market.php          – обработка перевода товаров (страница инструмента)
│   ├── i18n4marketpro.resources.php       – (не показан, вероятно, содержит пути к ресурсам)
│   └── i18n4marketpro.structure.php       – обработка перевода структуры категорий
├── lang/
│   ├── i18n4marketpro.en.lang.php         – английский языковой файл
│   ├── i18n4marketpro.ru.lang.php         – русский языковой файл
│   └── i18n4marketpro.ua.lang.php         – украинский языковой файл
├── setup/
│   ├── i18n4marketpro.install.php         – скрипт установки
│   ├── i18n4marketpro.install.sql         – SQL-запросы для создания таблиц
│   ├── i18n4marketpro.uninstall.php       – скрипт удаления
│   └── i18n4marketpro.uninstall.sql       – SQL-запросы для удаления таблиц
├── tpl/
│   ├── i18n4marketpro.locales.tpl         – шаблон выбора локали для перевода структуры
│   ├── i18n4marketpro.market.tpl          – шаблон формы перевода товара
│   └── i18n4marketpro.structure.tpl       – шаблон формы перевода категорий
├── i18n4marketpro.admin.structure.delete.php – (вероятно, обработка удаления перевода структуры)
├── i18n4marketpro.admin.structure.php       – (вероятно, старая версия страницы структуры)
├── i18n4marketpro.extension.install.done.php – (хук после установки расширения)
├── i18n4marketpro.extrafields.php           – регистрация дополнительных полей для таблицы переводов
├── i18n4marketpro.global.php                – глобальная инициализация (не предоставлен код)
├── i18n4marketpro.header.tags.php           – хуки для header.tpl (языковой переключатель, hreflang, экстраполя)
├── i18n4marketpro.input.php                 – (вероятно, перехват ввода данных)
├── i18n4marketpro.market.delete.php         – обработка удаления перевода товара (может быть устаревшим)
├── i18n4marketpro.market.enum.query.php     – (вероятно, модификация запросов списка товаров)
├── i18n4marketpro.market.item.getItems.php  – (вероятно, подмена товаров в списках)
├── i18n4marketpro.market.list.main.php      – (обработка списка товаров)
├── i18n4marketpro.market.list.query.php     – (модификация запроса списка)
├── i18n4marketpro.market.list.rowcat.php    – (обработка строк категорий в списке)
├── i18n4marketpro.market.list.tags.php      – (теги списка)
├── i18n4marketpro.market.main.php           – хук market.main (подмена данных товара)
├── i18n4marketpro.market.tags.php           – хук market.tags (добавление тегов для шаблона товара)
├── i18n4marketpro.markettags.php            – хук markettags.main (модификация генерируемых тегов товара)
├── i18n4marketpro.php                       – основной файл плагина (standalone-хук)
├── i18n4marketpro.png                       – иконка плагина
├── i18n4marketpro.selectBox.structure.php   – хук selectBox.structure (перевод списка категорий)
├── i18n4marketpro.setup.php                 – метаданные и конфигурация плагина
├── i18n4marketpro.structure.update.done.php – хук после обновления структуры
└── i18n4marketpro.trashcan.php              – интеграция с корзиной
```



Основные исполняемые файлы, содержащие логику, – это файлы в корне плагина, которые подключаются через хуки Cotonti. Каждый такой файл начинается с блока хука, например:

```php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */
```

Это указывает Cotonti, в какой момент выполнять данный скрипт.

---

<a name="database"></a>
## 4. Таблицы базы данных

Плагин создаёт две таблицы, префикс которых по умолчанию `cot_` (может быть изменён в настройках Cotonti).

### 4.1. `cot_i18n4marketpro_structure`

Хранит переводы названий и описаний категорий модуля market.

```sql
CREATE TABLE IF NOT EXISTS `cot_i18n4marketpro_structure` (
    `istructure_code` VARCHAR(255) NOT NULL,
    `istructure_locale` VARCHAR(8) NOT NULL DEFAULT 'en',
    `istructure_title` VARCHAR(128) NOT NULL,
    `istructure_desc` VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`istructure_code`, `istructure_locale`),
    KEY `istructure_code` (`istructure_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Поля:**
- `istructure_code` – код категории в структуре market (например, `computers`);
- `istructure_locale` – код локали (например, `ru`, `en`);
- `istructure_title` – переведённое название категории;
- `istructure_desc` – переведённое описание категории.

**Первичный ключ** – составной (`istructure_code`, `istructure_locale`), что гарантирует уникальность перевода для каждой категории и языка.

### 4.2. `cot_i18n4marketpro_pages`

Хранит переводы товаров (страниц модуля market).

```sql
CREATE TABLE IF NOT EXISTS `cot_i18n4marketpro_pages` (
    `ipage_id` INT UNSIGNED NOT NULL,
    `ipage_locale` VARCHAR(8) NOT NULL DEFAULT 'en',
    `ipage_translatorid` INT UNSIGNED NOT NULL,
    `ipage_translatorname` VARCHAR(100) NOT NULL,
    `ipage_date` INT UNSIGNED NOT NULL DEFAULT 0,
    `ipage_title` VARCHAR(128) NOT NULL DEFAULT '',
    `ipage_desc` VARCHAR(255) NOT NULL DEFAULT '',
    `ipage_text` MEDIUMTEXT NULL DEFAULT NULL,
    PRIMARY KEY (`ipage_id`, `ipage_locale`),
    KEY `ipage_id` (`ipage_id`),
    KEY `ipage_translatorid` (`ipage_translatorid`),
    CONSTRAINT fk_translation_market FOREIGN KEY (ipage_id) REFERENCES `cot_market` (`fieldmrkt_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Поля:**
- `ipage_id` – идентификатор товара (внешний ключ на `cot_market.fieldmrkt_id`);
- `ipage_locale` – код локали;
- `ipage_translatorid` – ID пользователя, создавшего перевод;
- `ipage_translatorname` – имя пользователя-переводчика (для удобства);
- `ipage_date` – Unix timestamp даты перевода/обновления;
- `ipage_title` – переведённый заголовок товара;
- `ipage_desc` – переведённое описание (краткое);
- `ipage_text` – полный переведённый текст товара (HTML).

**Первичный ключ** – составной (`ipage_id`, `ipage_locale`). Внешний ключ `fk_translation_market` с `ON DELETE RESTRICT` запрещает удаление товара, если для него существуют переводы, чтобы не потерять данные.

### 4.3. Интеграция с таблицей тегов (при наличии плагина tags)

Функция `cot_i18n4marketpro_installTagsIntegration()` в `inc/i18n4marketpro.functions.php` добавляет колонку `tag_locale` в таблицу `cot_tag_references`, если плагин tags установлен:

```php
if (!Cot::$db->fieldExists(Cot::$db->tag_references, 'tag_locale')) {
    Cot::$db->query('ALTER TABLE ' . Cot::$db->tag_references .
        " ADD COLUMN tag_locale VARCHAR(8) NOT NULL DEFAULT ''");
    Cot::$db->query('ALTER TABLE ' . Cot::$db->tag_references . ' DROP PRIMARY KEY');
    Cot::$db->query('ALTER TABLE ' . Cot::$db->tag_references .
        ' ADD PRIMARY KEY (tag, tag_area, tag_item, tag_locale)');
}
```

Это позволяет привязывать теги к конкретному переводу товара (разные теги для разных языков). Первичный ключ таблицы ссылок тегов изменяется: добавляется поле `tag_locale`.

---

<a name="configuration"></a>
## 5. Конфигурация плагина

Настройки плагина определяются в файле `i18n4marketpro.setup.php` в секции `[BEGIN_COT_EXT_CONFIG]`.

Доступные настройки:

| Ключ | Тип | По умолчанию | Описание |
|------|-----|--------------|----------|
| `cats` | text | (пусто) | Коды корневых категорий модуля market, для которых включён перевод. Перечисляются через запятую. |
| `locales` | text | `en|English` | Список локалей сайта. Каждая локаль с новой строки в формате `код|Название` |
| `omitmain` | radio | 1 | Опускать параметр языка в URL, если указывает на основной язык (1 = да, 0 = нет). |
| `rewrite` | radio | 0 | Включить ЧПУ для параметра языка в ссылках (1 = да, 0 = нет). |
| `cookie` | radio | 0 | Запоминать выбранный язык в cookie (1 = да, 0 = нет). |

Значения настроек доступны в коде как `Cot::$cfg['plugin']['i18n4marketpro']['cats']`, `['locales']`, `['omitmain']`, `['rewrite']`, `['cookie']`.

**Примечание по настройке `rewrite`**: если включена, требуется ручное обновление `.htaccess` – добавление правил перезаписи URL, пример приведён в языковом файле:

```apache
# Language selector
RewriteRule ^(ru|en)/(.*) $2?l=$1 [QSA,NC,NE,DPI]
```

**Настройка `cats`** – важнейшая: она определяет, какие категории (все их подкатегории) будут поддерживать перевод. Например, если указать `computers,notebooks`, то все товары внутри этих категорий и их подкатегорий смогут иметь переводы, а также их категории будут переведены.

---

<a name="localization"></a>
## 6. Локализация и языковые файлы

Плагин содержит три языковых файла в папке `lang/`:
- `i18n4marketpro.en.lang.php`
- `i18n4marketpro.ru.lang.php`
- `i18n4marketpro.ua.lang.php`

Эти файлы содержат массив `$L` с переводами строк интерфейса плагина. Пример строк из русского файла:

- `$L['cfg_cats']` – заголовок настройки категорий;
- `$L['i18n4marketpro_adding']` – «Добавление нового перевода»;
- `$L['i18n4marketpro_structure']` – «Интернационализация структуры»;
- и другие.

Языковые файлы подключаются функцией `cot_langfile('i18n4marketpro', 'plug')` в файле `inc/i18n4marketpro.functions.php` и, вероятно, в `i18n4marketpro.global.php`.

Список локалей, доступных для перевода, задаётся в настройке `locales`. Они загружаются в глобальный массив `$i18n4marketpro_locales` функцией `cot_i18n4marketpro_load_locales()`. Массив имеет вид:

```php
$i18n4marketpro_locales = [
    'en' => 'English',
    'ru' => 'Русский',
    'ua' => 'Українська'
];
```

Если в настройках отсутствует локаль, соответствующая `Cot::$cfg['defaultlang']`, она автоматически добавляется с названием из `$cot_languages` (если есть) или кодом языка.

Текущая выбранная локаль хранится в переменной `$i18n4marketpro_locale` (вероятно, устанавливается в `i18n4marketpro.global.php` на основе параметра URL, cookie и т.д.). Также используется переменная `$i18n4marketpro_fallback` – язык по умолчанию (обычно `defaultlang`).

---

<a name="api-functions"></a>
## 7. API и основные функции

Основные функции плагина находятся в файле `inc/i18n4marketpro.functions.php`. Этот файл подключается в `i18n4marketpro.global.php` (или в standalone-файле) и предоставляет следующие публичные функции:

### 7.1. `cot_i18n4marketpro_build_catpath($area, $cat, $locale)`

**Назначение**: строит массив «хлебных крошек» для категории с учётом переводов.

**Параметры:**
- `$area` – код области (обычно `'market'`);
- `$cat` – код категории;
- `$locale` – локаль, для которой строить путь.

**Возвращает**: список массивов `[url, title]` для каждой категории в пути (от родительской до текущей). Если перевод названия категории отсутствует, используется оригинальное название из `Cot::$structure[$area][$code]['title']` и URL без параметра `l`. Если перевод есть, используется переведённое название и URL с параметром `l=$locale`.

**Используется в**:
- `market.tags.php` для формирования пути категории;
- `selectBox.structure.php` для вывода переведённых названий в выпадающих списках.

### 7.2. `cot_i18n4marketpro_enabled($cat)`

Проверяет, включён ли перевод для указанной категории.

**Логика**: извлекает настройку `cats`, разбивает на массив корневых категорий. Затем определяет корневого родителя категории `$cat` с помощью `cot_structure_parents('market', $cat, 'first')` и проверяет, входит ли этот корневой родитель в список разрешённых.

**Возвращает**: `true` или `false`.

**Кэширование**: статическая переменная `$i18n4marketpro_cats` для хранения списка разрешённых категорий в течение одного запроса.

### 7.3. `cot_i18n4marketpro_get_cat($cat, $locale)`

Возвращает массив перевода категории (поля `title` и `desc`) или `false`, если перевод не найден.

**Источник данных**: глобальный массив `$i18n4marketpro_structure`, который загружается функцией `cot_i18n4marketpro_load_structure()`. Массив имеет структуру:

```php
$i18n4marketpro_structure[$cat_code][$locale] = [
    'title' => ...,
    'desc'  => ...
];
```

### 7.4. `cot_i18n4marketpro_get_page($page_id, $locale)`

Запрашивает из базы данных перевод товара по ID и локали. Использует таблицу `$db_i18n4marketpro_pages`.

**Параметры**: `$page_id` (int), `$locale` (string).

**Возвращает**: ассоциативный массив строки перевода или `false`.

**Пример использования**:

```php
$translation = cot_i18n4marketpro_get_page($id, 'ru');
if ($translation) {
    echo $translation['ipage_title'];
}
```

### 7.5. `cot_i18n4marketpro_list_cat_locales($cat)`

Возвращает список локалей, для которых существует перевод указанной категории. Берёт ключи массива `$i18n4marketpro_structure[$cat]`.

### 7.6. `cot_i18n4marketpro_list_page_locales($page_id)`

Выполняет SQL-запрос `SELECT DISTINCT ipage_locale` к таблице переводов товаров для получения списка локалей, на которые переведён товар.

### 7.7. `cot_i18n4marketpro_load_locales()`

Загружает список доступных локалей из настройки `locales` в глобальную переменную `$i18n4marketpro_locales`. Каждая строка настройки разбивается по символу `|` на код и название. Если язык по умолчанию отсутствует в списке, он добавляется автоматически.

### 7.8. `cot_i18n4marketpro_load_structure()`

Загружает все переводы категорий из таблицы `$db_i18n4marketpro_structure` в глобальный массив `$i18n4marketpro_structure`. Массив индексируется по коду категории, затем по локали.

### 7.9. `cot_i18n4marketpro_installTagsIntegration()`

Проверяет, установлен ли плагин `tags`. Если да, добавляет колонку `tag_locale` в таблицу `cot_tag_references` и изменяет первичный ключ, чтобы учесть локаль. Возвращает `true` при успехе или `false`, если плагин tags не установлен.

Этот метод, вероятно, вызывается при установке или обновлении плагина.

---

<a name="category-translation"></a>
## 8. Перевод структуры категорий

Управление переводами категорий осуществляется через standalone-страницу плагина, которая обрабатывается файлом `i18n4marketpro.php` при `$m == 'structure'`. Этот файл подключает `inc/i18n4marketpro.structure.php`.

### 8.1. Доступ к инструменту

Для доступа требуется право администрирования плагина (`$i18n4marketpro_admin`). Проверка выполняется функцией `cot_block($i18n4marketpro_admin)`.

URL для открытия: `index.php?e=i18n4marketpro&m=structure`

### 8.2. Логика работы

1. Определяется пагинация: количество категорий на страницу берётся из `Cot::$cfg['maxrowsperpage']` (по умолчанию 15).
2. Загружаются переводы категорий (`cot_i18n4marketpro_load_structure()`) и кэшируются.
3. Если в настройках не указаны корневые категории (`cats`), выводится предупреждение со ссылкой на настройки.
4. Если не выбрана целевая локаль (`$i18n4marketpro_locale` пуста или равна языку по умолчанию), отображается страница выбора локали (шаблон `i18n4marketpro.locales.tpl`).
5. Если локаль выбрана, выводится форма перевода всех категорий, которые попадают под разрешённые корневые категории.

### 8.3. Обработка отправки формы (обновление переводов)

При POST-запросе с параметром `a=update` выполняется обновление переводов.

Из POST извлекаются массивы:
- `code` – коды категорий (скрытые поля);
- `title` – переведённые названия;
- `desc` – переведённые описания.

Для каждой категории:
- Если поле `title` пустое – перевод удаляется (DELETE из таблицы).
- Если перевод ещё не существует (по коду и локали) – создаётся новая запись (INSERT).
- Если перевод существует и изменился – обновляется (UPDATE).

Подсчитываются количества добавленных, обновлённых и удалённых записей. После выполнения выводятся соответствующие сообщения, логируются действия и происходит редирект на ту же страницу с текущей локалью.

### 8.4. Шаблон формы перевода категорий

Файл `tpl/i18n4marketpro.structure.tpl` содержит разметку формы. Для каждой категории выводится блок `I18N4MARKETPRO_CATEGORY_ROW`, где слева – оригинальные название и описание, справа – поля для ввода перевода.

Основные теги шаблона:
- `{I18N4MARKETPRO_ACTION}` – URL отправки формы;
- `{I18N4MARKETPRO_ORIGINAL_LANG}` – название языка оригинала;
- `{I18N4MARKETPRO_TARGET_LANG}` – название целевого языка;
- Внутри блока:
  - `{I18N4MARKETPRO_CATEGORY_ROW_TITLE}` – оригинальное название;
  - `{I18N4MARKETPRO_CATEGORY_ROW_DESC}` – оригинальное описание;
  - `{I18N4MARKETPRO_CATEGORY_ROW_CODE_NAME}` и `{I18N4MARKETPRO_CATEGORY_ROW_CODE_VALUE}` – имя и значение скрытого поля кода;
  - `{I18N4MARKETPRO_CATEGORY_ROW_ITITLE_NAME}` и `{I18N4MARKETPRO_CATEGORY_ROW_ITITLE_VALUE}` – поле ввода перевода названия;
  - `{I18N4MARKETPRO_CATEGORY_ROW_IDESC_NAME}` и `{I18N4MARKETPRO_CATEGORY_ROW_IDESC_VALUE}` – textarea для перевода описания.

### 8.5. Хук `selectBox.structure`

Файл `i18n4marketpro.selectBox.structure.php` перехватывает генерацию выпадающих списков категорий (функция `cot_selectbox_structure()`) и подменяет названия категорий на переведённые, если:
- пользователь имеет право на чтение переводов (`$i18n4marketpro_read`);
- текущий язык не является основным (`$i18n4marketpro_notmain`);
- переданный список категорий не пуст.

Для каждой категории строится путь с помощью `cot_i18n4marketpro_build_catpath()` и объединяется названиями с учётом разделителя из `Cot::$cfg['separator']`.

---

<a name="product-translation"></a>
## 9. Перевод товаров (market)

### 9.1. Обзор

Переводы товаров управляются через отдельную страницу инструмента, обрабатываемую в `i18n4marketpro.php` при `$m == 'market'`. Этот файл подключает `inc/i18n4marketpro.market.php`. Также переводы влияют на отображение товаров через хуки `market.main`, `market.tags`, `markettags.main` и другие.

### 9.2. Страница добавления/редактирования перевода товара

Доступ к странице:
- Добавление: `index.php?e=i18n4marketpro&m=market&a=add&id=ID_товара`
- Редактирование: `index.php?e=i18n4marketpro&m=market&a=edit&id=ID_товара&l=локаль`
- Удаление: `index.php?e=i18n4marketpro&m=market&a=delete&id=ID_товара&l=локаль`

Файл `inc/i18n4marketpro.market.php` обрабатывает эти действия.

#### Инициализация
- Получает `$id` (int) и `$l` (локаль) из GET.
- Проверяет существование товара в таблице `$db->market`.
- Загружает данные товара.
- В зависимости от действия (`add` или `edit`) загружает или создаёт массив `$pag_i18n4marketpro` для текущего перевода.

#### Добавление (`$a == 'add'`)
- Если товар существует и перевода ещё нет, выводится форма добавления.
- При POST-запросе:
  - Проверяется выбранная локаль `locale` (должна быть в списке `$i18n4marketpro_locales`).
  - Проверяется отсутствие дубликата перевода.
  - Собираются данные: `ipage_id`, `ipage_locale`, `ipage_translatorid`, `ipage_translatorname`, `ipage_date`, `ipage_title`, `ipage_desc`, `ipage_text`.
  - Дополнительно импортируются значения дополнительных полей (extrafields).
  - Проверяется длина заголовка (не менее 2 символов).
  - Если ошибок нет, запись вставляется в БД, вызывается хук `i18n4marketpro.market.add.done`, логируется, и происходит редирект на страницу товара с параметром `l`.
- Если есть ошибки, форма отображается снова с введёнными данными.

#### Редактирование (`$a == 'edit'`)
- Доступно, если перевод существует и пользователь имеет право (админ плагина или владелец перевода).
- При POST:
  - Может быть изменена локаль (выпадающий список).
  - Проверяется, что новая локаль не занята другим переводом.
  - Данные обновляются: `ipage_title`, `ipage_desc`, `ipage_text`, `ipage_locale`, `ipage_date`.
  - Обновление производится либо через `Cot::$db->update()` (дважды в коде: сначала частичное обновление, потом полное с дополнительными полями – возможна избыточность).
  - Вызывается хук `i18n4marketpro.market.edit.update`.
  - Редирект на страницу товара с новой локалью.
- При ошибках – повторный вывод формы с сохранением введённых значений.

#### Удаление (`$a == 'delete'`)
- Доступно администратору плагина или переводчику.
- Если активен плагин trashcan и включена опция `trash_page`, перевод помещается в корзину (функция `TrashcanService::put()`).
- Затем запись удаляется из таблицы.
- Вызывается хук `i18n4marketpro.market.delete.done`, логирование, редирект.

#### Шаблон `i18n4marketpro.market.tpl`
Используется для отображения формы перевода. Основные блоки:
- Левая колонка – оригинальные данные товара (название, описание, мета-теги, текст).
- Правая колонка – поля для ввода перевода (заголовок, описание, текст, а также дополнительные поля и теги).

Теги шаблона включают:
- `{I18N4MARKETPRO_ACTION}`, `{I18N4MARKETPRO_TITLE}`
- `{I18N4MARKETPRO_ORIGINAL_LANG}`, `{I18N4MARKETPRO_LOCALIZED_LANG}` (селектор языка)
- `{I18N4MARKETPRO_PAGE_TITLE}`, `{I18N4MARKETPRO_PAGE_DESC}`, `{I18N4MARKETPRO_PAGE_TEXT}` и др.
- Для дополнительных полей: `{I18N_PAGE_FORM_EXTRAFLD}` и `{I18N_PAGE_FORM_EXTRAFLD_TITLE}`, а также индивидуальные `{I18N_PAGE_FORM_XXXXX}`.

### 9.3. Подмена данных товара при отображении

#### Хук `market.main` (файл `i18n4marketpro.market.main.php`)

Выполняется при загрузке страницы товара в модуле market. Проверяет, включён ли перевод для категории (`$i18n4marketpro_enabled`) и не является ли текущий язык основным (`$i18n4marketpro_notmain`). Если да, загружает перевод товара и перевод категории. Если перевод товара существует, он сливается с данными товара через `array_merge($item, $pag_i18n4marketpro)`. Это приводит к тому, что поля `fieldmrkt_title`, `fieldmrkt_desc`, `fieldmrkt_text` и другие (если имена совпадают) переопределяются переведёнными значениями. Также переопределяются мета-теги страницы (`Cot::$out['subtitle']`, `Cot::$out['desc']`).

**Примечание**: в коде используется переменная `$id`, которая, вероятно, устанавливается в модуле market.

#### Хук `market.tags` (файл `i18n4marketpro.market.tags.php`)

Этот хук добавляет в шаблон товара различные теги, связанные с переводом. Он работает только если `$i18n4marketpro_enabled` истинно.

Основные действия:
1. Загружает все переводы товара (список локалей и заголовков).
2. Определяет оригинальное название товара.
3. Формирует языковой переключатель: для каждой доступной локали (включая основной язык) создаётся блок `I18N4MARKETPRO_LANG_ROW` с URL, кодом, названием, CSS-классом (selected) и т.д. Блок парсится в `MAIN.I18N4MARKETPRO_LANG`.
4. Добавляет кнопки управления переводом:
   - Если перевод существует и пользователь имеет право редактировать – теги `MARKET_I18N4MARKETPRO_ADMIN_EDIT` и `..._URL`.
   - Если перевода нет и пользователь может писать – теги `MARKET_I18N4MARKETPRO_TRANSLATE` и `..._URL`.
   - Если пользователь администратор и перевод есть – теги `MARKET_I18N4MARKETPRO_ADMIN_DELETE` и `..._URL` (с подтверждением).
5. Выводит дополнительные поля перевода в блоках `EXTRAFLD`.

#### Хук `markettags.main` (файл `i18n4marketpro.markettags.php`)

Этот хук изменяет теги, генерируемые функцией `cot_generate_markettags()` для товаров. Он используется как в списке товаров, так и на странице товара (в зависимости от контекста).

Логика:
- Определяет, включён ли i18n для категории товара (`$cat_i18n_enabled`).
- Всегда пытается загрузить перевод товара для текущей локали (если есть право чтения).
- Формирует URL товара с учётом параметра `l`, если категория поддерживает перевод и не действует `omitmain`.
- Если перевод найден, переопределяются многие теги: `URL`, `TITLE`, `DESCRIPTION`, `TEXT`, `TEXT_CUT`, `MORE`, `BREADCRUMBS` и т.д. Также добавляются теги `CAT_TITLE`, `CAT_DESCRIPTION`, `CAT_PATH_SHORT`, `BREADCRUMBS_FULL`.
- Для дополнительных полей перевода формируются теги `I18N_PAGE_XXXXX`, `I18N_PAGE_XXXXX_TITLE`, `I18N_PAGE_XXXXX_VALUE`.
- Если перевода нет, эти теги сбрасываются в пустые строки.
- Добавляются кнопки редактирования перевода для тех, кто имеет право.

Этот хук позволяет полностью заменить вывод товара на переведённую версию во всех шаблонах, где используются теги `PAGE_...` (или `PRD_...`), так как он модифицирует исходный массив `$temp_array`.

### 9.4. Другие файлы, влияющие на товары

В списке файлов присутствуют `i18n4marketpro.market.list.*`, `i18n4marketpro.market.enum.query.php`, `i18n4marketpro.market.item.getItems.php`. Они, вероятно, отвечают за интеграцию переводов в списки товаров (например, в категориях, при поиске и т.д.). Код этих файлов не предоставлен, но по названиям можно судить:
- `market.list.query.php` – модификация SQL-запроса для выборки товаров с учётом языка;
- `market.list.main.php` – обработка основного списка;
- `market.list.rowcat.php` – возможно, подмена названий категорий в строках списка;
- `market.list.tags.php` – добавление тегов для элементов списка;
- `market.enum.query.php` – модификация запроса перечисления товаров;
- `market.item.getItems.php` – подмена данных получаемых товаров.

Так как кода нет, в данном руководстве мы не можем описать их поведение детально. Рекомендуется изучить эти файлы непосредственно.

---

<a name="extrafields"></a>
## 10. Интеграция с дополнительными полями

Плагин позволяет переводить не только стандартные поля товара, но и дополнительные поля (extrafields), созданные для таблицы переводов.

### 10.1. Регистрация таблицы для extrafields

Файл `i18n4marketpro.extrafields.php` подключается к хуку `admin.extrafields.first` и добавляет в белый список таблицу переводов товаров:

```php
$extra_whitelist[$db_i18n4marketpro_pages] = [
    'name'    => $db_i18n4marketpro_pages,
    'caption' => $L['i18n4marketpro_pages'],
    'type'    => 'plug',
    'code'    => 'i18n4marketpro',
    'tags'    => [
        'i18n4marketpro.page.tpl' => '{I18N_PAGE_FORM_XXXXX}, {I18N_PAGE_FORM_XXXXX_TITLE}',
    ]
];
```

Это позволяет администратору создавать дополнительные поля для таблицы `cot_i18n4marketpro_pages` через стандартный интерфейс Cotonti. Созданные поля будут автоматически подхватываться плагином.

### 10.2. Работа с дополнительными полями в форме перевода

В файле `inc/i18n4marketpro.market.php` загружается конфигурация дополнительных полей:

```php
$extrafields = Cot::$extrafields[$db_i18n4marketpro_pages] ?? [];
```

При добавлении или редактировании перевода значения доп. полей импортируются из POST с помощью `cot_import_extrafields()` и сохраняются в массиве `$pag_i18n4marketpro` под именами `ipage_<имя_поля>`.

В шаблоне `i18n4marketpro.market.tpl` для каждого доп. поля генерируется элемент формы через `cot_build_extrafields()` и передаётся как тег `I18N_PAGE_FORM_XXXXX` и `I18N_PAGE_FORM_XXXXX_TITLE`. Также есть общий блок `EXTRAFLD`, который выводит все поля в цикле.

### 10.3. Вывод дополнительных полей перевода на странице товара

Хук `market.tags` (файл `i18n4marketpro.market.tags.php`) добавляет теги `I18N_XXXXX`, `I18N_XXXXX_TITLE`, `I18N_XXXXX_VALUE` для использования в шаблоне товара.

Хук `markettags.main` (файл `i18n4marketpro.markettags.php`) также формирует теги `I18N_PAGE_XXXXX` и сбрасывает их, если перевод отсутствует.

Хук `header.tags` (файл `i18n4marketpro.header.tags.php`) передаёт в шаблон `header.tpl` теги `I18N_HEADER_XXXXX`, `I18N_HEADER_XXXXX_TITLE`, `I18N_HEADER_XXXXX_VALUE` для вывода мета-тегов и других данных перевода в `<head>`.

---

<a name="templates"></a>
## 11. Шаблоны плагина

Плагин использует три шаблона, расположенных в папке `tpl/`.

### 11.1. `i18n4marketpro.locales.tpl`

Простой шаблон для выбора языка при переводе структуры. Содержит блок `I18N4MARKETPRO_LOCALE_ROW`, внутри которого доступны теги:
- `{I18N4MARKETPRO_LOCALE_ROW_URL}` – ссылка на страницу перевода структуры с выбранной локалью;
- `{I18N4MARKETPRO_LOCALE_ROW_TITLE}` – название локали.

### 11.2. `i18n4marketpro.structure.tpl`

Шаблон формы перевода категорий. Основные блоки и теги описаны в разделе 8.4.

### 11.3. `i18n4marketpro.market.tpl`

Шаблон формы добавления/редактирования перевода товара. Подробно описан в разделе 9.2.

Шаблон использует Bootstrap-классы (например, `border`, `py-3`, `card`), что указывает на то, что он рассчитан на тему с Bootstrap. В нижней части шаблона есть отладочный блок для администратора (группа 5), который выводит имя шаблона.

Также в шаблоне присутствует JavaScript для ограничения количества символов в полях с классом `js-chars-limit-block` (используется для мета-тегов).

---

<a name="hooks"></a>
## 12. Хуки и интеграция с Cotonti

Плагин регистрирует несколько хуков через заголовки файлов. Ниже перечислены все хуки, встречающиеся в предоставленных файлах:

### 12.1. Основной файл `i18n4marketpro.php`
- **Хук**: `standalone` – обрабатывает запросы к инструменту перевода. В зависимости от параметра `m` подключает `structure.php` или `market.php`.

### 12.2. `i18n4marketpro.header.tags.php`
- **Хук**: `header.tags` – добавляет теги в шаблон `header.tpl`:
  - языковой переключатель (блок `I18N4MARKETPRO_LANG`);
  - `HTML_LANG` – полный IETF-тег языка для атрибута `lang` тега `<html>`;
  - `ALTERNATE_TAGS` – теги `<link rel="alternate" hreflang="...">`;
  - экстраполя перевода для вывода в `<head>` (теги `I18N_HEADER_...`).

### 12.3. `i18n4marketpro.market.main.php`
- **Хук**: `market.main` (порядок 5) – выполняется при загрузке страницы товара, подменяет данные товара переведёнными.

### 12.4. `i18n4marketpro.market.tags.php`
- **Хук**: `market.tags` – добавляет теги в шаблон товара (языковой переключатель, кнопки управления, экстраполя).

### 12.5. `i18n4marketpro.markettags.php`
- **Хук**: `markettags.main` – модифицирует теги, генерируемые функцией `cot_generate_markettags()`. Это влияет на вывод товара в любом контексте (страница, список, поиск).

### 12.6. `i18n4marketpro.selectBox.structure.php`
- **Хук**: `selectBox.structure` – переопределяет названия категорий в выпадающих списках.

### 12.7. `i18n4marketpro.extrafields.php`
- **Хук**: `admin.extrafields.first` – регистрирует таблицу переводов в белом списке доп. полей.

### 12.8. `i18n4marketpro.extension.install.done.php`
- Вероятно, хук после установки расширения (не описан).

### 12.9. `i18n4marketpro.structure.update.done.php`
- Хук, вызываемый после обновления переводов структуры (в файле `structure.php` через `cot_getextplugins('i18n4marketpro.structure.update.done')`).

### 12.10. `i18n4marketpro.trashcan.php`
- Интеграция с плагином trashcan (не описан).

### Внутренние хуки (вызываются через `cot_getextplugins`)
В коде присутствуют вызовы дополнительных хуков, которые позволяют другим плагинам расширять функциональность:
- `i18n4marketpro.market.first`
- `i18n4marketpro.market.add.done`
- `i18n4marketpro.market.edit.update`
- `i18n4marketpro.market.edit.tags`
- `i18n4marketpro.market.translate.tags`
- `i18n4marketpro.market.delete.done`
- `i18n4marketpro.structure.first`
- `i18n4marketpro.structure.loop`
- `i18n4marketpro.structure.update.done`
- `i18n4marketpro.structure.tags`
- `i18n4marketpro.standalone`

Эти хуки позволяют разработчикам вмешиваться в процесс перевода на разных этапах.

---

<a name="seo"></a>
## 13. SEO-аспекты: hreflang и языковые переключатели

Плагин обеспечивает улучшение SEO для многоязычных страниц товаров.

### 13.1. Языковой переключатель в шапке

В файле `i18n4marketpro.header.tags.php` формируется блок `HEADER.I18N4MARKETPRO_LANG`, который обычно размещается в шапке сайта. Он содержит ссылки на текущую страницу на разных языках. Для каждой локали создаётся строка с тегами:
- `{I18N4MARKETPRO_LANG_ROW_URL}` – URL;
- `{I18N4MARKETPRO_LANG_ROW_CODE}` – код языка;
- `{I18N4MARKETPRO_LANG_ROW_FLAG}` – код для отображения флага (например, `gb` для английского);
- `{I18N4MARKETPRO_LANG_ROW_TITLE}` – название языка;
- `{I18N4MARKETPRO_LANG_ROW_CLASS}` – класс `selected` для текущего языка;
- `{I18N4MARKETPRO_LANG_ROW_SELECTED}` – атрибут `selected` для option, если используется select.

Ссылки строятся с учётом настройки `omitmain`: если включено и язык основной, параметр `l` не добавляется. Если выключено, параметр добавляется всегда.

### 13.2. Атрибут `lang` у тега `<html>`

Переменная `{HTML_LANG}` содержит полный IETF-тег языка (например, `uk-UA`, `ru-UA`, `en-UA`). Карта соответствий задана в массиве `$i18n4marketpro_ietf_map`:

```php
$i18n4marketpro_ietf_map = [
    'ua' => 'uk-UA',
    'ru' => 'ru-UA',
    'en' => 'en-UA',
    'pl' => 'pl-UA'
];
```

Если код не найден в карте, используется короткий код.

### 13.3. Генерация `hreflang`

Когда пользователь просматривает товар (`$env['ext'] == 'market'` и `$id > 0`), плагин получает список всех локалей, на которые переведён товар. Для каждой локали (кроме основной) формируется тег:

```html
<link rel="alternate" hreflang="<полный_код>" href="<полный_URL>">
```

URL строится на основе категории и алиаса товара с параметром `l`. Также добавляется тег `x-default`, который указывает на версию для основного языка (без параметра `l`, если `omitmain=1`, или с параметром, если `omitmain=0`).

Все теги передаются в шаблон как `{ALTERNATE_TAGS}` внутри блока `HEADER.ALTERNATE_TAGS`.

---

<a name="usage-examples"></a>
## 14. Примеры использования в шаблонах

### 14.1. Языковой переключатель в шапке

В шаблоне `header.tpl` нужно разместить блок:

```html
<!-- BEGIN: I18N4MARKETPRO_LANG -->
<ul class="lang-switcher">
    <!-- BEGIN: I18N4MARKETPRO_LANG_ROW -->
    <li class="{I18N4MARKETPRO_LANG_ROW_CLASS}">
        <a href="{I18N4MARKETPRO_LANG_ROW_URL}">
            <img src="images/flags/{I18N4MARKETPRO_LANG_ROW_FLAG}.png" alt="{I18N4MARKETPRO_LANG_ROW_TITLE}" />
            {I18N4MARKETPRO_LANG_ROW_TITLE}
        </a>
    </li>
    <!-- END: I18N4MARKETPRO_LANG_ROW -->
</ul>
<!-- END: I18N4MARKETPRO_LANG -->
```

### 14.2. Вывод hreflang в `<head>`

В `header.tpl` внутри `<head>`:

```html
{ALTERNATE_TAGS}
```

### 14.3. Кнопка перевода на странице товара

В шаблоне товара (`market.tpl`) можно разместить:

```html
<!-- IF {MARKET_I18N4MARKETPRO_TRANSLATE} -->
<a href="{MARKET_I18N4MARKETPRO_TRANSLATE_URL}" class="btn">{MARKET_I18N4MARKETPRO_TRANSLATE}</a>
<!-- ENDIF -->

<!-- IF {MARKET_I18N4MARKETPRO_ADMIN_EDIT} -->
<a href="{MARKET_I18N4MARKETPRO_ADMIN_EDIT_URL}">{MARKET_I18N4MARKETPRO_ADMIN_EDIT}</a>
<!-- ENDIF -->

<!-- IF {MARKET_I18N4MARKETPRO_ADMIN_DELETE} -->
<a href="{MARKET_I18N4MARKETPRO_ADMIN_DELETE_URL}" class="confirmLink">{MARKET_I18N4MARKETPRO_ADMIN_DELETE}</a>
<!-- ENDIF -->
```

### 14.4. Вывод дополнительных полей перевода

В шаблоне товара можно использовать теги, сформированные хуком `market.tags`:

```html
<div class="extrafield">
    <strong>{I18N_MXTRA_META_TITLE_TITLE}:</strong>
    {I18N_MXTRA_META_TITLE}
</div>
```

Или через блок `EXTRAFLD`:

```html
<!-- BEGIN: EXTRAFLD -->
<div>
    <span>{I18N_EXTRAFIELD_TITLE}:</span>
    {I18N_EXTRAFIELD_VALUE}
</div>
<!-- END: EXTRAFLD -->
```

### 14.5. Вывод переведённых категорий в хлебных крошках

Благодаря хуку `markettags.main`, тег `{PRD_BREADCRUMBS}` или `{PAGE_BREADCRUMBS}` (в зависимости от используемых тегов) автоматически будет содержать переведённые названия категорий, если они существуют.

---

<a name="appendix"></a>
## 15. Приложение: список хуков и тегов

### 15.1. Все хуки Cotonti, используемые плагином

| Файл | Хук | Описание |
|------|-----|----------|
| `i18n4marketpro.php` | `standalone` | Обработка инструмента перевода |
| `i18n4marketpro.header.tags.php` | `header.tags` | Добавление тегов в header.tpl |
| `i18n4marketpro.market.main.php` | `market.main` | Подмена данных товара |
| `i18n4marketpro.market.tags.php` | `market.tags` | Добавление тегов в шаблон товара |
| `i18n4marketpro.markettags.php` | `markettags.main` | Модификация тегов `cot_generate_markettags()` |
| `i18n4marketpro.selectBox.structure.php` | `selectBox.structure` | Перевод категорий в select |
| `i18n4marketpro.extrafields.php` | `admin.extrafields.first` | Регистрация таблицы для extrafields |

### 15.2. Глобальные переменные, используемые плагином

Имена начинаются с `$i18n4marketpro_`:

- `$i18n4marketpro_locales` – массив доступных локалей.
- `$i18n4marketpro_locale` – текущая выбранная локаль.
- `$i18n4marketpro_fallback` – язык по умолчанию (обычно основной).
- `$i18n4marketpro_read` – право на чтение переводов (булево).
- `$i18n4marketpro_write` – право на создание/редактирование переводов (булево).
- `$i18n4marketpro_admin` – право администрирования плагина (булево).
- `$i18n4marketpro_notmain` – true, если текущий язык не является основным.
- `$i18n4marketpro_enabled` – true, если перевод включён для текущего контекста (устанавливается в `market.main` и других местах).
- `$i18n4marketpro_structure` – глобальный массив переводов структуры.
- `$db_i18n4marketpro_pages`, `$db_i18n4marketpro_structure` – имена таблиц.

Эти переменные, вероятно, определяются в `i18n4marketpro.global.php` и/или в базовом плагине i18n.

### 15.3. Основные теги, добавляемые в шаблоны

#### В header.tpl (через `header.tags`)
- `{HTML_LANG}` – полный IETF-тег языка.
- `{ALTERNATE_TAGS}` – сгенерированные `hreflang` теги.
- Блок `I18N4MARKETPRO_LANG` с внутренними `I18N4MARKETPRO_LANG_ROW_*`.

#### В шаблон товара (через `market.tags`)
- Языковой переключатель: `{I18N4MARKETPRO_LANG_ROW_URL}`, `{I18N4MARKETPRO_LANG_ROW_CODE}`, `{I18N4MARKETPRO_LANG_ROW_TITLE}`, `{I18N4MARKETPRO_LANG_ROW_CLASS}`, `{I18N4MARKETPRO_LANG_ROW_SELECTED}`.
- Кнопки управления: `{MARKET_I18N4MARKETPRO_TRANSLATE}`, `{MARKET_I18N4MARKETPRO_TRANSLATE_URL}`, `{MARKET_I18N4MARKETPRO_ADMIN_EDIT}`, `{MARKET_I18N4MARKETPRO_ADMIN_EDIT_URL}`, `{MARKET_I18N4MARKETPRO_ADMIN_DELETE}`, `{MARKET_I18N4MARKETPRO_ADMIN_DELETE_URL}`.
- Доп. поля: `{I18N_XXXXX}`, `{I18N_XXXXX_TITLE}`, `{I18N_XXXXX_VALUE}`, а также блок `EXTRAFLD` с `{I18N_EXTRAFIELD_TITLE}`, `{I18N_EXTRAFIELD_VALUE}`.

#### В теги товара через `markettags.main`
- Переопределяются стандартные теги товара: `{PRD_URL}`, `{PRD_TITLE}`, `{PRD_DESCRIPTION}`, `{PRD_TEXT}`, `{PRD_TEXT_CUT}`, `{PRD_MORE}`, `{PRD_BREADCRUMBS}` и т.п.
- Добавляются теги: `{PRD_CAT_TITLE}`, `{PRD_CAT_DESCRIPTION}`, `{PRD_CAT_PATH_SHORT}`, `{PRD_BREADCRUMBS_FULL}`.
- Для доп. полей: `{I18N_PAGE_XXXXX}`, `{I18N_PAGE_XXXXX_TITLE}`, `{I18N_PAGE_XXXXX_VALUE}`.
- Кнопка редактирования: `{ADMIN_USER_WRITE_EDIT_TRANSLATION}`.

### 15.4. Языковые строки (ключи `$L`)

Основные строки из русского языкового файла:
- `i18n4marketpro_adding` – Добавление нового перевода
- `i18n4marketpro_confirm_delete` – Вы действительно хотите удалить перевод?
- `i18n4marketpro_delete` – Удалить перевод
- `i18n4marketpro_editing` – Редактирование перевода
- `i18n4marketpro_incorrect_locale` – Неверная локаль
- `i18n4marketpro_items_added` – `{$cnt}` элементов добавлено
- `i18n4marketpro_items_removed` – `{$cnt}` элементов удалено
- `i18n4marketpro_items_updated` – `{$cnt}` элементов обновлено
- `i18n4marketpro_locale_selection` – Выбор локали
- `i18n4marketpro_localized` – Локализованное
- `i18n4marketpro_no_categories` – Не выбраны категории для перевода...
- `i18n4marketpro_original` – Оригинал
- `i18n4marketpro_structure` – Интернационализация структуры
- `i18n4marketpro_translate` – Перевести
- `i18n4marketpro_translation` – Перевод
- `i18n4marketpro_translations_items` – Описание в переводе на
- `i18n4marketpro_pages` – Переводы товаров

---

## Заключение

Плагин i18n4marketpro является мощным инструментом для создания многоязычного каталога товаров в Cotonti. Он тесно интегрируется с модулем market, предоставляя удобные интерфейсы для перевода категорий и товаров, а также гибкие хуки и теги для настройки вывода. 
