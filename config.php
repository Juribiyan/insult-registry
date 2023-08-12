<?php
// error_reporting(E_ERROR | E_PARSE);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWD', '');
define('DB_DATABASE', 'insult-registry');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

mb_internal_encoding('UTF-8');

define('ENTRIES_PER_PAGE', 5);

define('ENABLE_CAPTCHA', true);
define('ENABLE_CAPTCHA_COMMENTS', false);
define('CAPTCHA_LIFE', 35);
define('DEFAULT_NAME', "Аноним");
define('PIC_URL_PATTERN', '(?:https?:\/\/)?((?:files\.catbox\.moe|i\.imgur\.com)\/[a-zA-Z0-9]+\.(gif|jpe?g|png|webm|mp4))');