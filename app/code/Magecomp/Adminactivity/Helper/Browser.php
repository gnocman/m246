<?php

namespace Magecomp\Adminactivity\Helper;

/**
 * Class Browser
 * @package Magecomp\Adminactivity\Helper
 */
class Browser extends \Magento\Framework\App\Helper\AbstractHelper implements \Magecomp\Adminactivity\Api\Data\Datainterface
{
    private $_agent='';
    public function __construct()
    {
        $this->setUserAgent();
        $this->checkPlatform();
    }

    public function setUserAgent($userAgent=''){
        if($userAgent!=''){
            $this->_agent=$userAgent;
        }
        else{
            $this->_agent=$_SERVER['HTTP_USER_AGENT'];
        }
    }

    /**
     * Set the version of the browser
     * @param string $version The version of the Browser
     * @return void
     */
    public function getVersion()
    {
        $result = explode("/", stristr($this->_agent, $this->getBrowserName()));
        if (isset($result[1])) {
            $version = explode(' ', $result[1]);
            return preg_replace('/[^0-9,.,a-z,A-Z-]/', '', $version[0]);
        }
        return '';
    }

    /**
     * Set the name of the platform
     * @param string $platform The name of the Platform
     * @return void
     */

    public function getBrowserName()
    {
        // Make case insensitive.
        //$t = strtolower($this->_agent);
        $t = " " . strtolower($this->_agent);

        // Humans / Regular Users
        if (strpos($t, 'opera')!==false || strpos($t, 'opr/')!==false) return 'Opera';
        elseif (strpos($t, 'edge')!==false) return 'Edge';
        elseif (strpos($t, 'chrome')!==false) return 'Chrome';
        elseif (strpos($t, 'safari')!==false) return 'Safari';
        elseif (strpos($t, 'firefox')!==false) return 'Firefox';
        elseif (strpos($t, 'msie')!==false || strpos($t, 'trident/7')!==false) return 'Internet Explorer';

        // Search Engines
        elseif (strpos($t, 'google')!==false) return '[Bot] Googlebot';
        elseif (strpos($t, 'bing') !==false) return '[Bot] Bingbot';
        elseif (strpos($t, 'slurp')!==false) return '[Bot] Yahoo! Slurp';
        elseif (strpos($t, 'duckduckgo')!==false) return '[Bot] DuckDuckBot' ;
        elseif (strpos($t, 'baidu')!==false) return '[Bot] Baidu';
        elseif (strpos($t, 'yandex')!==false) return '[Bot] Yandex';
        elseif (strpos($t, 'sogou')!==false) return '[Bot] Sogou';
        elseif (strpos($t, 'exabot')!==false) return '[Bot] Exabot';
        elseif (strpos($t, 'msn')!==false) return '[Bot] MSN';

        // Common Tools and Bots
        elseif (strpos($t, 'mj12bot')!==false) return '[Bot] Majestic';
        elseif (strpos($t, 'ahrefs')!==false) return '[Bot] Ahrefs';
        elseif (strpos($t, 'semrush')!==false) return '[Bot] SEMRush';
        elseif (strpos($t, 'rogerbot')!==false || strpos($t, 'dotbot')!==false) return '[Bot] Moz or OpenSiteExplorer';
        elseif (strpos($t, 'frog')!==false|| strpos($t, 'screaming')!==false) return '[Bot] Screaming Frog';

        // Miscellaneous
        elseif (strpos($t, 'facebook')!==false) return '[Bot] Facebook';
        elseif (strpos($t, 'pinterest')!==false) return '[Bot] Pinterest';

        // Check for strings commonly used in bot user agents
        elseif (strpos($t, 'crawler')!==false || strpos($t, 'api')!==false||
            strpos($t, 'spider')!==false || strpos($t, 'http')!==false ||
            strpos($t, 'bot')!==false || strpos($t, 'archive')!==false ||
            strpos($t, 'info')!==false || strpos($t, 'data')!==false) return '[Bot] Other';

        return 'Other (Unknown)';
    }
    protected function checkPlatform()
    {
        if (stripos($this->_agent, 'windows') !== false) {
           return self::PLATFORM_WINDOWS;
        } else if (stripos($this->_agent, 'iPad') !== false) {
            return self::PLATFORM_IPAD;
        } else if (stripos($this->_agent, 'iPod') !== false) {
           return self::PLATFORM_IPOD;
        } else if (stripos($this->_agent, 'iPhone') !== false) {
            return self::PLATFORM_IPHONE;
        } elseif (stripos($this->_agent, 'mac') !== false) {
            return self::PLATFORM_APPLE;
        } elseif (stripos($this->_agent, 'android') !== false) {
            return self::PLATFORM_ANDROID;
        } elseif (stripos($this->_agent, 'Silk') !== false) {
            return self::PLATFORM_FIRE_OS;
        } elseif (stripos($this->_agent, 'linux') !== false && stripos($this->_agent, 'SMART-TV') !== false ) {
            return self::PLATFORM_LINUX .'/'.self::PLATFORM_SMART_TV;
        } elseif (stripos($this->_agent, 'linux') !== false) {
           return self::PLATFORM_LINUX;
        } else if (stripos($this->_agent, 'Nokia') !== false) {
            return self::PLATFORM_NOKIA;
        } else if (stripos($this->_agent, 'BlackBerry') !== false) {
            return self::PLATFORM_BLACKBERRY;
        } elseif (stripos($this->_agent, 'FreeBSD') !== false) {
            return self::PLATFORM_FREEBSD;
        } elseif (stripos($this->_agent, 'OpenBSD') !== false) {
            return self::PLATFORM_OPENBSD;
        } elseif (stripos($this->_agent, 'NetBSD') !== false) {
            return self::PLATFORM_NETBSD;
        } elseif (stripos($this->_agent, 'OpenSolaris') !== false) {
            return self::PLATFORM_OPENSOLARIS;
        } elseif (stripos($this->_agent, 'SunOS') !== false) {
            return self::PLATFORM_SUNOS;
        } elseif (stripos($this->_agent, 'OS\/2') !== false) {
            return self::PLATFORM_OS2;
        } elseif (stripos($this->_agent, 'BeOS') !== false) {
            return self::PLATFORM_BEOS;
        } elseif (stripos($this->_agent, 'win') !== false) {
            return self::PLATFORM_WINDOWS;
        } elseif (stripos($this->_agent, 'Playstation') !== false) {
            return self::PLATFORM_PLAYSTATION;
        } elseif (stripos($this->_agent, 'Roku') !== false) {
            return self::PLATFORM_ROKU;
        } elseif (stripos($this->_agent, 'iOS') !== false) {
            return self::PLATFORM_IPHONE . '/' . self::PLATFORM_IPAD;
        } elseif (stripos($this->_agent, 'tvOS') !== false) {
            return self::PLATFORM_APPLE_TV;
        } elseif (stripos($this->_agent, 'curl') !== false) {
            return self::PLATFORM_TERMINAL;
        } elseif (stripos($this->_agent, 'CrOS') !== false) {
            return self::PLATFORM_CHROME_OS;
        } elseif (stripos($this->_agent, 'okhttp') !== false) {
            return self::PLATFORM_JAVA_ANDROID;
        } elseif (stripos($this->_agent, 'PostmanRuntime') !== false) {
            return self::PLATFORM_POSTMAN;
        } elseif (stripos($this->_agent, 'Iframely') !== false) {
            return self::PLATFORM_I_FRAME;
        }

    }

    public function getDevice(){
        if (stripos($this->_agent, 'Android') !== false || stripos($this->_agent, 'ipad') !== false) {
            if (stripos($this->_agent, 'Mobile') !== false) {
                return 'Mobile';
            } else {
               return 'Tablet';
            }
        }
        return 'Desktop';
    }
    public function __toString()
    {
        return "<div><div><strong>Browser Name:</strong> {$this->getBrowserName()}</div>" .
            "<div><strong>Browser Version:</strong> {$this->getVersion()}</div>" .
            "<div><strong>Platform:</strong> {$this->checkPlatform()}</div></div>"
//            "<div><strong>Device:</strong> {$this->getDevice()}</div></div>"
            ;
    }
}
