<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// Define http host
define('WP_HOME', 'http://'.$_SERVER['HTTP_HOST']);
define('WP_SITEURL', 'http://'.$_SERVER['HTTP_HOST']);

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'darwin_lawyer');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'Web#Done3');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ';vO_kwgj`[{t2d`v[YqZcpcV^&Z>#Ja&l4_&b dbJIa:{PSpL)w1qYP+}$)`<|nc');
define('SECURE_AUTH_KEY',  'Kfvx2Z5F=a;tz@^I{TW`B+z)3;f0;m*NFGFkqRfeky-m7~% Rhx%*tt>JFA#[6fw');
define('LOGGED_IN_KEY',    '+-!{~G$|Bn-Md?SdNmatJ&_kqpq5&x(h3cAlnXz|m`{6Rh;{M@h`66X8j~Ko$+Pg');
define('NONCE_KEY',        'k`_Y)khzTIc3|oQz6u@JWqDv:`}Zx}`ZZkz5Cq2HFQ-8tfvC9wX?AVTK,4p/3:l<');
define('AUTH_SALT',        'D i$L{wU[_X8*=|X|azB^_)m{3EFk:E+Zwi$d)-_}maL7Du`Hs/_id4)m3bXC5Dd');
define('SECURE_AUTH_SALT', '_If0i&M#GpJ/Qjv@MKFWGSOeK-pJ<~22L`WN%GcHs-m;E{Yp>F6dX[#W!HJC#)LA');
define('LOGGED_IN_SALT',   'dV95o=~A+1(}|/4[ds`dRo}}:u/[n#GvAu%Z4xD&{hDvMwnn$L}a8-d*+g|F0C.!');
define('NONCE_SALT',       '>5U~r9Uk[PeEn|gnl>hTZd9@s$i;6<IK(Zth.fr>J3o2T)l0s;0Ns;|RwxDuBIh4');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
