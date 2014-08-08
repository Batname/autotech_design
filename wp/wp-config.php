<?php
/**
 * Основные параметры WordPress.
 *
 * Этот файл содержит следующие параметры: настройки MySQL, префикс таблиц,
 * секретные ключи, язык WordPress и ABSPATH. Дополнительную информацию можно найти
 * на странице {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Кодекса. Настройки MySQL можно узнать у хостинг-провайдера.
 *
 * Этот файл используется сценарием создания wp-config.php в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать этот файл
 * с именем "wp-config.php" и заполнить значения.
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'autotech_new');

/** Имя пользователя MySQL */
define('DB_USER', 'u_autotecht');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'nLoEVc8Q');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется снова авторизоваться.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '8DnYWzVYF!h{ND4i<p/)@oB@VIEMY&#^,t?b[Gma0|P<Q6R(wCOgDRznC<8/A^C!');
define('SECURE_AUTH_KEY',  '[7>zYSdB9i4|_zJ|#atoX2XmioON$n>wcq95$yqzz]< Hwsz4meVcAf,u>SeI.v7');
define('LOGGED_IN_KEY',    'lTYOo++?qZ`-WEV5+ge0Ncn?pJJr)QDXI=E_;|H30PW{AXcN6vb /Afec-{cjjZ]');
define('NONCE_KEY',        '[!zhxqKouD-<4+umELqt_,K2@0Go*aOp>6asRq}&-U^lM1>++@-n6Q^!hvQYZ]-#');
define('AUTH_SALT',        'UWvKA.2Xyqtv~An_H]sx&x2;Bf>KV-!X%UWy-3pRW<tSsm>l)mc@}%vC*s7tW+S;');
define('SECURE_AUTH_SALT', 'Kju QO;V!b1,AA3+q:q!RU4fG[A5#?l$9xIyF3UDMRl|B7ke=rE(%43nzZM.l^>I');
define('LOGGED_IN_SALT',   'sj5QPd1mc*;m.a|#2S?kcM+gihuiA`CL~a,[hhJqarKf-RF$g*1u),WGY]]^8lc5');
define('NONCE_SALT',       'V?1!ek*<0he(g3F.5v Z)}-b:sO9!viq{RLtHNSN2VZU,yPH:lPc|YaX$f=ZqMM3');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько блогов в одну базу данных, если вы будете использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Язык локализации WordPress, по умолчанию английский.
 *
 * Измените этот параметр, чтобы настроить локализацию. Соответствующий MO-файл
 * для выбранного языка должен быть установлен в wp-content/languages. Например,
 * чтобы включить поддержку русского языка, скопируйте ru_RU.mo в wp-content/languages
 * и присвойте WPLANG значение 'ru_RU'.
 */
define('WPLANG', 'ru_RU');

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Настоятельно рекомендуется, чтобы разработчики плагинов и тем использовали WP_DEBUG
 * в своём рабочем окружении.
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
