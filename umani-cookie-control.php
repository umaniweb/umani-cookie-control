<?php

/**
 * Plugin Name:        UmaniWeb Cookie Control
 * Plugin URI:         https://github.com/umaniweb/umani-cookie-control
 * Version:            2.0.8
 * Description:        Bannière de consentement RGPD avec Consent Mode v2, granularité par catégorie, et injection de code head/body.
 * Author:             UmaniWeb
 * Author URI:         https://www.umaniweb.com/
 * Text Domain:        umani-cookie-control
 * Domain Path:        /languages
 * Requires PHP:       8.0
 */

namespace UMANI;

defined('ABSPATH') || exit;

define('UMANI_CC_VERSION', '2.0.8');
define('UMANI_CC_FILE', __FILE__);
define('UMANI_CC_DIR', __DIR__);
define('UMANI_CC_URL', plugin_dir_url(__FILE__));
define('UMANI_CC_SLUG', 'umani-cookie-control');

require __DIR__ . '/vendor/autoload.php';

new Plugin();
