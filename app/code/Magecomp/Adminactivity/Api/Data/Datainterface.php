<?php
namespace Magecomp\Adminactivity\Api\Data;

interface Datainterface
{
    /**
     * @var string
     */
    const ACTIVITY_ENABLE = 'adminactivity/general/enable';

    /**
     * @var string
     */
    const LOGIN_ACTIVITY_ENABLE = 'adminactivity/general/login_activity';

    /**
     * @var string
     */
    const PAGE_VISIT_ENABLE = 'adminactivity/general/page_visit';

    /**
     * @var string
     */
    const CLEAR_LOG_DAYS = 'adminactivity/general/clearlog';

    const CONFIG_ALLOWED_MODULE = 'adminactivity/general/allowed_module';
    /**
     * @var string
     */
    const CONFIG_ADMIN_EMAIL='adminactivity/general/admin_email';

    const CONFIG_SENDER_EMAIL='trans_email/ident_general/email';

    const CONFIG_SENDER_NAME='trans_email/ident_general/name';

    const CONFIG_EMAIL_TEMPLATE='adminactivity/general/email_template';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    const PLATFORM_WINDOWS = 'Windows';
    const PLATFORM_WINDOWS_CE = 'Windows CE';
    const PLATFORM_APPLE = 'Apple';
    const PLATFORM_LINUX = 'Linux';
    const PLATFORM_OS2 = 'OS/2';
    const PLATFORM_BEOS = 'BeOS';
    const PLATFORM_IPHONE = 'iPhone';
    const PLATFORM_IPOD = 'iPod';
    const PLATFORM_IPAD = 'iPad';
    const PLATFORM_BLACKBERRY = 'BlackBerry';
    const PLATFORM_NOKIA = 'Nokia';
    const PLATFORM_FREEBSD = 'FreeBSD';
    const PLATFORM_OPENBSD = 'OpenBSD';
    const PLATFORM_NETBSD = 'NetBSD';
    const PLATFORM_SUNOS = 'SunOS';
    const PLATFORM_OPENSOLARIS = 'OpenSolaris';
    const PLATFORM_ANDROID = 'Android';
    const PLATFORM_PLAYSTATION = "Sony PlayStation";
    const PLATFORM_ROKU = "Roku";
    const PLATFORM_APPLE_TV = "Apple TV";
    const PLATFORM_TERMINAL = "Terminal";
    const PLATFORM_FIRE_OS = "Fire OS";
    const PLATFORM_SMART_TV = "SMART-TV";
    const PLATFORM_CHROME_OS = "Chrome OS";
    const PLATFORM_JAVA_ANDROID = "Java/Android";
    const PLATFORM_POSTMAN = "Postman";
    const PLATFORM_I_FRAME = "Iframely";

}
