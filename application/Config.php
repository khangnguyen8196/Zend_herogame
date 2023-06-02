<?php
//-------------COMMON FOR ALL WEBSITE----------------//
define('AUTO_LOGOUT_MILISEC', 900000);
define('SITE_FRONT', 'SITE_FRONT');
define('LOGIN_REMEMBER_TIME', 604800);
define('CODE_SUCCESS', 1);
define('CODE_NO_ERROR', 0);
define('CODE_HAS_ERROR', -1);
define('CODE_REDIRECT', -300);
define('CODE_SESSION_EXPIRED', -999);
define('CODE_PERMISSION_DENIED', -998);
// action
define('ACTION_VIEW', 'view');
define('ACTION_ADD', 'add');
define('ACTION_EDIT', 'edit');
define('ACTION_DELETE', 'delete');
// session
define('SESSION_TIMEOUT', 240);
define('DEFAULT_EMAIL', 'shop@herogame.vn');
define('STATUS_DELETE', 0);
define('STATUS_ACTIVE', 1);
define('STATUS_IN_ACTIVE', -1);
define('STATUS_IN_OUT_STOCK',2);
define('STATUS_LOCKED', -2);
define('MAX_LIMIT_LOGIN_TIME', 5);
define('IMG_TYPE', 1);
define('VIDEO_TYPE', 2);

// 
define('ADMIN_ROLE_ID', 1);
//captcha
define( 'GOOGLE_RECAPTCHA_SITE_KEY', '6Lf_UAsUAAAAAO_yAUFQOxu-oCheNW2cOc9c5zM2');
define('CUSTOMER', 4);
// CATEGORY DEFINE
define("CATEGORY_TYPE_PRODUCT", 1);
define("CATEGORY_TYPE_POST", 2);
// PAGINGATOR
define("PAGINNATOR_LIMIT_ROW", 50);
define("PAGINNATOR_MAX_LINK_PER_PAGE", 5);
// STATUS - DON HANG
define("ORDER_COMPLETED", 1);
define("ORDER_ERROR", -1);
define("ORDER_WAITING_CONFIRM", 0);
define("ORDER_DELIVERING", 0);
define("ORDER_DELIVERED", 0);
define("BANNER_MAIN", 1);
define("BANNER_CHILD_LEFT", 2);
define("BANNER_CHILD_MID", 3);
define("BANNER_CHILD_RIGHT", 4);
define("BANNER_CHILD_FOOTER_TOP", 5);
define("BANNER_CHILD_FOOTER_BOT", 6);
define("BANNER_CHILD_HEADER", 7);
define("BANNER_CHILD_LEFT_VERTICAL1", 8);
define("BANNER_CHILD_LEFT_VERTICAL2", 9);
define("BANNER_CHILD_LEFT_VERTICAL3", 10);
define("BANNER_CHILD_LEFT_2", 11);
define("BANNER_CHILD_MID_2", 12);
define("BANNER_CHILD_RIGHT_2", 13);
define("BANNER_CHILD_LEFT_3", 14);
define("BANNER_CHILD_MID_3", 15);
define("BANNER_CHILD_RIGHT_3", 16);
define("BANNER_CHILD_LEFT_4", 17);
define("BANNER_CHILD_MID_4", 18);
define("BANNER_CHILD_RIGHT_4", 19);
define("SCORE_EXCHANGE_RATE", 1000);
define("TYPE_SETTING_VALUE", 1);
define("TYPE_SETTING_SHOW_HIDE", 2);
define("TYPE_SETTING_HTML", 3);
// setting page 
define("LEFT_SITE_PRODUCT_LIMIT", 10);
define("CONTENT_PRODUCT_LIMIT", 200);

define("STATUS_SHOW", 'STATUS_SHOW');
define("STATUS_HIDE", 'STATUS_HIDE');
define("LOGO", 'LOGO');
define("BACKGROUND_HEADER","BACKGROUND_HEADER");
// setting product
define("PAYMENT","PAYMENT");
define("WARRANTY","WARRANTY");
define("BENEFIT","BENEFIT");
define("PRODUCT_CONTACT","PRODUCT_CONTACT");
define("PHOTO_PATH","/upload/images/");
define("MAX_DAY_APPROVE", '5');

define('MAX_POST_OF_PAGE', '10');
define('YEAR_RANGE', 50);