$(function () {
    pages.constant.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	constant: {
        init: function () {
        },
        ACTION_VIEW: 'view', 
        ACTION_ADD: 'add', 
        ACTION_EDIT: 'edit', 
        ACTION_DELETE: 'delete',
        //-------------COMMON ERROR CODE----------------//
        CODE_SUCCESS: '1',    
        CODE_NO_ERROR: '0', 
        CODE_HAS_ERROR: '-1',
        CODE_REDIRECT: -300,
        CODE_SESSION_EXPIRED: -999,
        //-------------COMMON STATUS----------------//
	STATUS_DELETE : 0,
        STATUS_ACTIVE : 1,
        STATUS_IN_ACTIVE: -1,
        STATUS_LOCKED: -2,
        //
        GOOGLE_RECAPTCHA_SITE_KEY: 'key google recapcha',
        GOOGLE_RECAPTCHA_SECRET_KEY: 'key google secret key'
    }
});