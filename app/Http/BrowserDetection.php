<?php

/**
* PHP Browser Detection Class.
*
* The MIT License (MIT)
*
* Copyright (c) 2020 Artem Murugov <murugov@gmail.com>
*
* Permission is hereby granted, free of charge, to any person obtaining a copy of
* this software and associated documentation files (the "Software"), to deal in
* the Software without restriction, including without limitation the rights to
* use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
* the Software, and to permit persons to whom the Software is furnished to do so,
* subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
* FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
* COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
* IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
* CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*
* @version 1.1
* @last-modified June 10, 2020
* @link https://github.com/foroco/php-browser-detection
*/

namespace App\Http;

class BrowserDetection
{
    public $useragent;

    private $get_mode;

    private $touch_support_mode;

    private $real_os_name;

    private $result_ios;

    private $result_mobile;

    private $result_os_type;

    private $result_os_family;

    private $result_os_name;

    private $result_os_version;

    private $result_os_title;

    private $result_device_type;

    private $result_browser_name;

    private $result_browser_version;

    private $result_browser_title;

    private $result_browser_chromium_version;

    private $result_browser_gecko_version;

    private $result_browser_webkit_version;

    private $result_browser_chrome_original;

    private $result_browser_firefox_original;

    private $result_browser_safari_original;

    private $result_browser_android_webview;

    private $result_browser_ios_webview;

    private $result_browser_desktop_mode;

    /**
     * Common User-Agent matching method.
     *
     * @param string $data The string to data search for
     * @param bool $case_s Determines if we do a case-sensitive search (false) or a case insensitive (true)
     * @return bool Returns true if $data found in $useragent property, false otherwise.
     */
    private function matching_ua($data, $case_s)
    {
        if (empty($data)) {
            return false;
        }

        if (substr($data, 0, 1) === '/' && substr($data, -1) === '/') {
            if ($case_s == false) {
                $data = $data.'i';
            }
            if (preg_match($data, $this->useragent, $matches)) {
                if (! isset($matches[0])) {
                    $matches[0] = 0;
                }
                if (! isset($matches[1])) {
                    $matches[1] = 0;
                }
                if (! isset($matches[2])) {
                    $matches[2] = 0;
                }

                return $matches;
            } else {
                return false;
            }
        } else {
            $data_a = explode('|', $data);
            foreach ($data_a as $v) {
                if ($case_s == true) {
                    if (strpos($this->useragent, $v) !== false) {
                        return true;
                    }
                } else {
                    if (stripos($this->useragent, $v) !== false) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Method to call common User-Agent matching method if matching data is case sensitive.
     *
     * @param string $data The string to data search for
     * @return bool Returns true if case sensitive $data found in $useragent property, false otherwise.
     */
    private function match_ua($data)
    {
        return $this->matching_ua($data, true);
    }

    /**
     * Method to call common User-Agent matching method if matching data is case insensitive.
     *
     * @param string $data The string to data search for
     * @return bool Returns true if case insensitive $data found in $useragent property, false otherwise.
     */
    private function matchi_ua($data)
    {
        return $this->matching_ua($data, false);
    }

    /**
     * Detect iOS method
     * The method will try to recognize iOS signs from $useragent property and will placed true in result_ios property if iOS found, false otherwise.
     *
     * @return null
     */
    private function match_ios()
    {
        if ($this->match_ua('iPhone|iphone|iPad|iPod|CFNetwork') && ! $this->match_ua('x86_64|i386')) {
            $this->result_ios = true;
        }

        return null;
    }

    /**
     * Detect mobile OS method
     * The method will try to recognize mobile OS signs from $useragent property and will placed true in result_mobile property if mobile OS found, false otherwise.
     *
     * @return null
     */
    private function match_mobile()
    {
        if (! $this->match_ua('WOW64|Win64')) {
            $this->match_ios();
            if ($this->result_ios || $this->matchi_ua('mobile|tablet') || $this->match_ua('Android|BlackBerry|BB10;|MIDP|PlayBook|Windows Phone|Windows Mobile|Windows CE|IEMobile|Opera Mini|OPiOS|Opera Mobi|Kindle|Silk/|Bada|Tizen|Lumia|Symbian|SymbOS|(Series|PalmOS|PalmSource|Dolfin|Crosswalk|Obigo|MQQBrowser|CriOS') || $this->matchi_ua('nokia|playstation|watch')) {
                $this->result_mobile = 1;
            }
        }

        return null;
    }

    /**
     * Reset all common defined properties.
     *
     * @return null
     */
    private function resetProperties()
    {
        $this->real_os_name = '';
        $this->result_ios = false;
        $this->result_mobile = 0;
        $this->result_os_type = 'unknown';
        $this->result_os_family = 'unknown';
        $this->result_os_name = 'unknown';
        $this->result_os_version = 0;
        $this->result_os_title = 'unknown';
        $this->result_device_type = 'unknown';
        $this->result_browser_name = 'unknown';
        $this->result_browser_version = 0;
        $this->result_browser_title = 'unknown';
        $this->result_browser_chromium_version = 0;
        $this->result_browser_gecko_version = 0;
        $this->result_browser_webkit_version = 0;
        $this->result_browser_chrome_original = 0;
        $this->result_browser_firefox_original = 0;
        $this->result_browser_safari_original = 0;
        $this->result_browser_android_webview = 0;
        $this->result_browser_ios_webview = 0;
        $this->result_browser_desktop_mode = 0;

        return null;
    }

    /**
     * Common User-Agent parsing method
     * The method will try to recognize environment data from $useragent property and will placed it in defined properties.
     *
     * @return null
     */
    private function getResult()
    {
        // Detect mobile device

        $this->match_mobile();

        // Detect mobile browser Desktop Mode

        if ($this->touch_support_mode == true) {
            // Android

            if ($this->match_ua('X11; Linux x86_64|X11; Linux aarch64|X11; U; U; Linux x86_64')) {
                $this->real_os_name = 'Android';
                $this->result_mobile = 1;
                $this->result_browser_desktop_mode = 1;
            }

            // iOS

            if ($this->match_ua('Intel Mac OS X')) {
                $this->real_os_name = 'iOS';
                $this->result_mobile = 1;
                $this->result_browser_desktop_mode = 1;
            }
        }

        /*
        --------------
         OS DETECTION
        --------------

        --------------
         Mixed OS(es)
        --------------
        */

        $os_need_continue = true;

        // Windows OS

        if ($this->get_mode !== 'browser' && $this->match_ua('Windows|Win32') && ! $this->match_ua('Windows Phone|Windows Mobile|Windows CE')) {
            $this->result_os_name = 'Windows';
            $matches = $this->match_ua('/Windows ([ .a-zA-Z0-9]+)[;\\)]/');
            $version = $matches[1];
            if ($version === 'NT 10.0') {
                $this->result_os_version = '10';
            }
            if ($version === 'NT 6.4') {
                $this->result_os_version = '10';
            }
            if ($version === 'NT 6.3') {
                $this->result_os_version = '8.1';
            }
            if ($version === 'NT 6.2') {
                $this->result_os_version = '8';
            }
            if ($version === 'NT 6.1') {
                $this->result_os_version = '7';
            }
            if ($version === 'NT 6.0') {
                $this->result_os_version = 'Vista';
            }
            if ($version === 'NT 6') {
                $this->result_os_version = 'NT';
            }
            if ($version === 'NT 5.2') {
                $this->result_os_version = 'XP';
            }
            if ($version === 'NT 5.1') {
                $this->result_os_version = 'XP';
            }
            if ($version === 'NT 5.01') {
                $this->result_os_version = '2000';
            }
            if ($version === 'NT 5.0') {
                $this->result_os_version = '2000';
            }
            if ($version === 'NT 5') {
                $this->result_os_version = '2000';
            }
            if ($version === 'NT 4.0') {
                $this->result_os_version = 'NT 4.0';
            }
            if ($version === 'NT4.0') {
                $this->result_os_version = 'NT 4.0';
            }
            if ($version === 'NT') {
                $this->result_os_version = 'NT';
            }
            if ($version === '98') {
                $this->result_os_version = '98';
            }
            if ($version === '95') {
                $this->result_os_version = '95';
            }
            if ($version === 'ME') {
                $this->result_os_version = 'ME';
            }
            if (empty($this->result_os_version)) {
                if ($this->match_ua('/Win16/')) {
                    $this->result_os_version = '3.1';
                }
                if ($this->match_ua('/(Windows 95)|(Win95)|(Windows_95)/')) {
                    $this->result_os_version = '95';
                }
                if ($this->match_ua('/(Windows 98)|(Win98)/')) {
                    $this->result_os_version = '98';
                }
                if ($this->match_ua('/(Windows 2000)/')) {
                    $this->result_os_version = '2000';
                }
                if ($this->match_ua('/(Win NT 5\.0)/')) {
                    $this->result_os_version = '2000';
                }
                if ($this->match_ua('/(Windows XP)/')) {
                    $this->result_os_version = 'XP';
                }
                if ($this->match_ua('/WinNT4\.0/')) {
                    $this->result_os_version = 'NT 4.0';
                }
            }
            if (! empty($this->result_os_version)) {
                $this->result_os_title = 'Windows '.$this->result_os_version;
            } else {
                $this->result_os_title = 'Windows (unknown version)';
            }
            if (intval($this->result_os_version) < 7 || intval($this->result_os_version) > 10) {
                $this->result_os_type = 'desktop';
            } else {
                $this->result_os_type = 'mixed';
            }
            $this->result_os_family = 'windows';
            $os_need_continue = false;
        }

        /*
        -------------
         Desktop OS
        -------------
        */

        if ($this->get_mode !== 'browser' && $this->result_mobile == 0) {
            $this->result_os_type = 'desktop';

            // Mac OS (X) / macOS

            if ($os_need_continue && $this->match_ua('Mac OS X|Mac_PowerPC|Macintosh|Mac_68K')) {
                $this->result_os_version = 0;
                $this->result_os_name = 'MacOS';
                if ($this->match_ua('Mac OS X')) {
                    $matches = $this->match_ua('/Mac OS X (\d+)[_.](\d+)/');
                    $version = $matches[1];
                    $version_minor = $matches[2];

                    if (! empty($version_minor) && $version == 10) {
                        switch ($version_minor) {
                            case 0: $this->result_os_version = 'Cheetah';
                                break;
                            case 1: $this->result_os_version = 'Puma';
                                break;
                            case 2: $this->result_os_version = 'Jaguar';
                                break;
                            case 3: $this->result_os_version = 'Panther';
                                break;
                            case 4: $this->result_os_version = 'Tiger';
                                break;
                            case 5: $this->result_os_version = 'Leopard';
                                break;
                            case 6: $this->result_os_version = 'Snow Leopard';
                                break;
                            case 7: $this->result_os_version = 'Lion';
                                break;
                            case 8: $this->result_os_version = 'Mountain Lion';
                                break;
                            case 9: $this->result_os_version = 'Mavericks';
                                break;
                            case 10: $this->result_os_version = 'Yosemite';
                                break;
                            case 11: $this->result_os_version = 'El Capitan';
                                break;
                            case 12: $this->result_os_version = 'Sierra';
                                break;
                            case 13: $this->result_os_version = 'High Sierra';
                                break;
                            case 14: $this->result_os_version = 'Mojave';
                                break;
                            case 15: $this->result_os_version = 'Catalina';
                                break;
                            default: $this->result_os_version = 'New';
                                break;
                        }
                    }

                    if (! empty($this->result_os_version)) {
                        $this->result_os_title = 'MacOS '.$this->result_os_version;
                    } else {
                        $this->result_os_title = 'MacOS (unknown version)';
                    }
                } else {
                    $this->result_os_title = 'MacOS (classic old)';
                }
                $this->result_os_family = 'macintosh';
                $os_need_continue = false;
            }

            // Darwin (mac)

            if ($os_need_continue && $this->match_ua('Darwin/')) {
                $this->result_os_version = 0;
                $this->result_os_name = 'Darwin';
                $matches = $this->match_ua('/Darwin\/([0-9]+\.[0-9]+)/');
                if (! empty($matches[1])) {
                    $this->result_os_version = (float) $matches[1];
                }
                $this->result_os_family = 'macintosh';
                $os_need_continue = false;
            }

            // Linux family OS(es)

            if ($os_need_continue) {
                $os_list[] = ['Ubuntu' => '/Ubuntu(?: )?(?:\/)?([0-9]+\.[0-9]+)/'];
                $os_list[] = ['Kubuntu' => '/Kubuntu(?: )?(?:\/)?([0-9]+\.[0-9]+)/'];
                $os_list[] = ['Linux Mint' => '/Linux Mint\/([.0-9]+)/'];
                $os_list[] = ['CentOS' => '/CentOS\/([0-9]+\.[0-9]+)/'];
                $os_list[] = ['OpenSUSE' => '/SUSE\/([0-9]+\.[0-9]+)/'];
                $os_list[] = ['Red Hat' => '/Red\sHat\/([0-9]+\.[0-9]+)/'];

                foreach ($os_list as $os_list_va) {
                    $k = key($os_list_va);
                    $v = $os_list_va[$k];

                    $matches = $this->match_ua($v);
                    if ($matches) {
                        $this->result_os_family = 'linux';
                        $this->result_os_name = $k;
                        $this->result_os_version = (float) $matches[1];
                        $os_need_continue = false;
                        break;
                    }
                }
                unset($os_list);
            }

            // Other Desktop OS(es)

            if ($os_need_continue) {
                $other_os = [];

                $other_os[] = ['Linux Mint' => 'Linux Mint'];
                $other_os[] = ['Kubuntu' => 'Kubuntu'];
                $other_os[] = ['Ubuntu' => 'Ubuntu'];
                $other_os[] = ['Ubuntu' => 'ubuntu'];
                $other_os[] = ['Debian' => 'Debian'];
                $other_os[] = ['CentOS' => 'CentOS'];
                $other_os[] = ['Fedora' => 'Fedora'];
                $other_os[] = ['Arch Linux' => 'ArchLinux'];
                $other_os[] = ['OpenSUSE' => 'SUSE'];
                $other_os[] = ['Red Hat' => 'Red Hat'];
                $other_os[] = ['Elementary' => ' elementary'];
                $other_os[] = ['OpenBSD' => 'OpenBSD'];
                $other_os[] = ['NetBSD' => 'NetBSD'];
                $other_os[] = ['FreeBSD' => 'FreeBSD'];
                $other_os[] = ['SunOS' => 'SunOS'];
                $other_os[] = ['RISC OS' => 'RISC OS'];
                $other_os[] = ['Linux' => 'Linux'];
                $other_os[] = ['Linux' => 'X11;'];
                $other_os[] = ['Linux' => 'Mozilla/5.0 (x86_64)'];
                $other_os[] = ['Linux' => 'Mozilla/5.0 (i686)'];
                $other_os[] = ['AmigaOS' => 'AmigaOS'];
                $other_os[] = ['Haiku' => 'Haiku'];
                $other_os[] = ['BeOS' => 'BeOS'];

                foreach ($other_os as $other_os_va) {
                    $k = key($other_os_va);
                    $v = $other_os_va[$k];

                    if ($this->match_ua($v)) {
                        $this->result_os_family = 'linux';
                        if ($k === 'AmigaOS') {
                            $this->result_os_family = 'amiga';
                        }
                        if ($k === 'Haiku' || $k === 'BeOS') {
                            $this->result_os_family = 'beos';
                        }
                        if ($k === 'OpenBSD' || $k === 'NetBSD' || $k === 'FreeBSD' || $k === 'SunOS' || $k === 'RISC OS') {
                            $this->result_os_family = 'unix';
                        }
                        $this->result_os_version = 0;
                        $this->result_os_name = $k;
                        $this->result_os_title = $k;
                        $os_need_continue = false;
                        break;
                    }
                }
                unset($other_os);
            }

            // End of desktop OS detection
        }

        /*
        -------------
         Mobile OS
        -------------
        */

        if ($this->get_mode !== 'browser' && $this->result_mobile == 1) {
            if ($this->result_os_type === 'unknown') {
                $this->result_os_type = 'mobile';
            }

            // Android OS

            if ($os_need_continue && $this->match_ua('Android') && ! $this->match_ua('Windows Phone|Windows Mobile|Tizen')) {
                $this->result_os_version = 0;
                $this->result_os_name = 'Android';
                $matches = $this->match_ua('/Android(?: |\-)([0-9]+\.[0-9]+)/');
                $this->result_os_version = (float) $matches[1];
                if (empty($this->result_os_version)) {
                    $matches = $this->match_ua('/Android(?: |\-)(\d+)/');
                    $this->result_os_version = (float) $matches[1];
                }
                $this->result_os_family = 'android';
                $os_need_continue = false;
            }

            // Android OS Desktop Mode

            if ($os_need_continue && $this->result_browser_desktop_mode == 1 && $this->real_os_name === 'Android') {
                $this->result_os_version = 0;
                $this->result_os_name = 'Android';
                $this->result_os_family = 'android';
                $os_need_continue = false;
            }

            // iOS OS

            if ($os_need_continue && $this->result_ios != false && ! $this->match_ua('Windows Phone|Windows Mobile')) {
                $this->result_os_version = 0;
                $this->result_os_name = 'iOS';
                $matches = $this->match_ua('/\sOS\s(\d+)[_.](\d+)/');
                $version = $matches[1];
                $version_minor = $matches[2];
                if (! empty($version)) {
                    $this->result_os_version = floatval($version.'.'.$version_minor);
                }
                $this->result_os_family = 'macintosh';
                $os_need_continue = false;
            }

            // iOS OS Desktop Mode

            if ($os_need_continue && $this->result_browser_desktop_mode == 1 && $this->real_os_name === 'iOS') {
                $this->result_os_version = 0;
                $this->result_os_name = 'iOS';
                $this->result_os_family = 'macintosh';
                $os_need_continue = false;
            }

            // Windows Phone OS, Tizen OS, Bada OS, Kindle OS, FIre OS, Java Platform

            if ($os_need_continue) {
                $os_list[] = ['Windows Phone' => '/Phone(?: OS)? ([.0-9]+)/'];
                $os_list[] = ['Windows Mobile' => '/Windows Mobile ([.0-9]+)/'];
                $os_list[] = ['Windows Mobile' => 'Windows Mobile'];
                $os_list[] = ['Windows CE' => 'Windows CE'];
                $os_list[] = ['Firefox OS' => '/Mozilla\/5\.0\s\((?:Mobile|Tablet);(?:.*;)?\srv:[.0-9]+\)\sGecko\/[.0-9]+\sFirefox\/[.0-9]+$/'];
                $os_list[] = ['Tizen' => '/Tizen[\/\s]([.0-9]+)/'];
                $os_list[] = ['Bada' => '/Bada[;\/]([.0-9]+)/'];
                $os_list[] = ['Kindle' => '/Kindle\/([0-9]+\.[0-9]+)/'];
                $os_list[] = ['Fire OS' => 'KFJW|Silk/'];
                $os_list[] = ['Java Platform' => '/MIDP\-([0-9]+\.[0-9]+)/'];
                $os_list[] = ['Java Platform' => '/MIDP/'];

                foreach ($os_list as $os_list_va) {
                    $k = key($os_list_va);
                    $v = $os_list_va[$k];

                    $matches = $this->match_ua($v);
                    if ($matches) {
                        $this->result_os_family = 'linux';
                        if (strpos($k, 'Windows') !== false) {
                            $this->result_os_family = 'windows';
                        }
                        $this->result_os_name = $k;
                        $this->result_os_version = (float) $matches[1];
                        $os_need_continue = false;

                        // J2ME/MIDP

                        if (strpos($k, 'Java Platform') !== false) {
                            $this->result_os_family = 'unknown';
                            $os_need_continue = true;
                        }
                        break;
                    }
                }
                unset($os_list);
            }

            // Other Mobile OS(es)

            if ($os_need_continue) {
                $other_os = [];

                $other_os[] = ['Sailfish OS' => 'Sailfish'];
                $other_os[] = ['BlackBerry' => 'BlackBerry'];
                $other_os[] = ['BlackBerry' => 'BB10;'];
                $other_os[] = ['BlackBerry' => 'RIM Tablet'];
                $other_os[] = ['Symbian' => 'Symbian'];
                $other_os[] = ['Symbian' => '(Series'];
                $other_os[] = ['Symbian' => 'SymbOS'];
                $other_os[] = ['WatchOS' => 'Watch'];
                $other_os[] = ['KaiOS' => 'KAIOS'];
                $other_os[] = ['RemixOS' => 'Remix Mini'];
                $other_os[] = ['RemixOS' => 'RemixOS'];
                $other_os[] = ['LiveArea' => 'PlayStation Vita'];
                $other_os[] = ['PalmOS' => 'PalmOS'];
                $other_os[] = ['PalmOS' => 'PalmSource'];
                $other_os[] = ['Maemo' => 'Maemo'];

                foreach ($other_os as $other_os_va) {
                    $k = key($other_os_va);
                    $v = $other_os_va[$k];

                    if ($this->match_ua($v)) {
                        if ($k === 'BlackBerry') {
                            $this->result_os_family = 'blackberry';
                        }
                        if ($k === 'Symbian') {
                            $this->result_os_family = 'symbian';
                        }
                        if ($k === 'WatchOS') {
                            $this->result_os_family = 'macintosh';
                        }
                        if ($k === 'Sailfish OS' || $k === 'KaiOS' || $k === 'Maemo') {
                            $this->result_os_family = 'linux';
                        }
                        if ($k === 'RemixOS') {
                            $this->result_os_family = 'android';
                        }
                        if ($k === 'LiveArea') {
                            $this->result_os_family = 'livearea';
                        }
                        if ($k === 'PalmOS') {
                            $this->result_os_family = 'palm';
                        }
                        $this->result_os_version = 0;
                        $this->result_os_name = $k;
                        $this->result_os_title = $k;
                        $os_need_continue = false;
                        break;
                    }
                }
                unset($other_os);
            }

            // End of mobile OS detection
        }

        // Other mixed-type linux-family OS(es)

        if ($this->get_mode !== 'browser' && ($this->result_os_family === 'linux' || $this->result_os_family === 'unknown')) {
            $other_os = [];

            $other_os[] = ['Chrome OS' => 'CrOS'];
            $other_os[] = ['WebOS' => 'hpwOS'];
            $other_os[] = ['WebOS' => 'Web0S'];
            $other_os[] = ['WebOS' => 'WebOS'];
            $other_os[] = ['WebOS' => 'webOS'];

            foreach ($other_os as $other_os_va) {
                $k = key($other_os_va);
                $v = $other_os_va[$k];

                if ($this->match_ua($v)) {
                    $this->result_os_family = 'linux';
                    $this->result_os_type = 'mixed';
                    $this->result_os_version = 0;
                    $this->result_os_name = $k;
                    $this->result_os_title = $k;
                    break;
                }
            }
            unset($other_os);
        }

        // Set OS title and type

        if ($this->get_mode !== 'browser' && $this->result_os_name !== 'MacOS') {
            if (! empty($this->result_os_version)) {
                $this->result_os_title = $this->result_os_name.' '.$this->result_os_version;
            } else {
                if ($this->result_os_name !== 'Windows') {
                    $this->result_os_title = $this->result_os_name;
                } else {
                    $this->result_os_title = $this->result_os_name.' (unknown version)';
                }
            }
            if (strpos($this->result_os_name, 'unknown') !== false) {
                $this->result_os_type = 'unknown';
            }
            if ($this->result_os_version == null) {
                $this->result_os_version = 0;
            }
        }

        // If mode 'os' only

        if ($this->get_mode === 'os') {
            return null;
        }
        if ($this->result_os_name === 'Darwin') {
            return null;
        }

        /*
        -------------------
         BROWSER DETECTION
        -------------------

        ---------------------
         Mixed-type Browsers
        ---------------------
        */

        $browser_need_continue = true;
        if ($this->get_mode === 'device') {
            $browser_need_continue = false;
        }

        // Chrome / Chromium

        if ($browser_need_continue && $this->match_ua('Chrome/|Chromium/|CriOS/|CrMo/')) {
            $this->result_browser_version = 0;
            $matches = $this->match_ua('/(Chrome|Chromium|CriOS|CrMo)\/([0-9]+)\./');
            $this->result_browser_name = 'Chrome';
            if ($matches[1] === 'Chromium') {
                $this->result_browser_name = 'Chromium';
            }
            if (! empty($matches[2])) {
                $this->result_browser_version = (int) $matches[2];
            }
            $this->result_browser_chromium_version = $this->result_browser_version;
            if ($this->match_ua('/Gecko\)\s(Chrome|CriOS|CrMo)\/(\d+\.\d+\.\d+\.\d+)\s(?:Mobile)?(?:\/[.0-9A-Za-z]+\s|\s)?Safari\/[.0-9]+$/')) {
                $this->result_browser_chrome_original = 1;
            }
        }

        // Firefox

        if ($browser_need_continue && $this->match_ua('Firefox/') && ! $this->match_ua('Focus/|FxiOS')) {
            $this->result_browser_version = 0;
            $matches = $this->match_ua('/Firefox\/([0-9]+)\./');
            $this->result_browser_name = 'Firefox';
            if (! empty($matches[1])) {
                $this->result_browser_version = (int) $matches[1];
            }
            if ($this->match_ua('/\)\sGecko\/[.0-9]+\sFirefox\/[.0-9]+$/')) {
                $this->result_browser_firefox_original = 1;
            }
        }

        // Cross-device browsers (Yandex, Edge, Firefox, Opera, Puffin, Vivaldi, QQBrowser, Whale, Brave etc)

        if ($browser_need_continue && $this->result_browser_chrome_original == 0 && $this->result_browser_firefox_original == 0) {
            $browser_list[] = ['Yandex Browser', 'YaBrowser', '/YaBrowser\/([0-9]+\.[0-9]+)/', '1', 'YaApp_'];
            $browser_list[] = ['Edge', 'Edg', '/Edg(|e|A|iOS)\/([0-9]+)\./', '2', ''];
            $browser_list[] = ['Opera', 'Opera|OPR/', '/OPR\/(\d+)/', '1', 'Opera Mini|OPiOS|OPT/|OPRGX/|AlohaBrowser'];
            $browser_list[] = ['Opera', 'Opera|OPR/', '/Opera.*Version\/([0-9]+\.[0-9]+)/', '1', 'Opera Mini|OPiOS|OPT/'];
            $browser_list[] = ['Opera', 'Opera|OPR/', '/Opera\s([0-9]+\.[0-9]+)/', '1', 'Opera Mini|OPiOS|OPT/'];
            $browser_list[] = ['Opera', 'Opera|OPR/', '/Opera\/([0-9]+\.[0-9]+)/', '1', 'Opera Mini|OPiOS|OPT/'];
            $browser_list[] = ['UC Browser', 'UBrowser|UCBrowser|UCMini', '/(UBrowser|UCBrowser|UCMini)\/([0-9]+\.[0-9]+)/', '2', 'UCTurbo'];
            $browser_list[] = ['UC Browser Turbo', 'UCTurbo/', '/UCTurbo\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Puffin', 'Puffin/', '/Puffin\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Vivaldi', 'Vivaldi/', '/Vivaldi\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['QQBrowser', 'QQBrowser/', '/QQBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Coc Coc', 'coc_coc_browser', '/coc_coc_browser\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Whale', 'Whale/', '/Whale\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Brave', 'Brave', '/Brave(?: Chrome)?\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Maxthon', 'Maxthon/', '/Maxthon\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Maxthon', 'MxBrowser/', '/MxBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['2345 Explorer', '2345Explorer|2345Browser', '/(2345Explorer|2345Browser)(?: |\/)?([0-9]+\.[0-9]+)/', '2', ''];
            $browser_list[] = ['IceCat', 'IceCat/', '/IceCat\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Lunascape', 'Lunascape', '/Lunascape(?: |\/)?([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Seznam Browser', 'Seznam.cz/|SznProhlizec/', '/(Seznam\.cz|SznProhlizec)\/([0-9]+\.[0-9]+)/', '2', ''];
            $browser_list[] = ['Sleipnir', 'Sleipnir/', '/Sleipnir\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Sputnik', 'SputnikBrowser/', '/SputnikBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Oculus Browser', 'OculusBrowser/', '/OculusBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['PaleMoon', 'PaleMoon', '/PaleMoon\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['SalamWeb', 'SalamWeb|Salam Browser', '/SalamWeb\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Swing', 'Swing/|Swing(And)/', '/Swing(?:\(And\))?\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Safe Exam Browser', 'SEB/', '/SEB\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Colibri', 'Colibri/', '/Colibri\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Opera GX', 'OPRGX', '/OPRGX\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Xvast', 'Xvast/', '/Xvast\/([0-9]+\.[0-9]+)/', '1', ''];

            foreach ($browser_list as $browser_list_va) {
                $match = $browser_list_va[1];
                $match_exclude = $browser_list_va[4];

                if ($this->match_ua($match) && ! $this->match_ua($match_exclude)) {
                    $this->result_browser_version = 0;
                    $this->result_browser_name = $browser_list_va[0];
                    $matches = $this->match_ua($browser_list_va[2]);
                    $matches_n = intval($browser_list_va[3]);
                    $this->result_browser_version = (float) $matches[$matches_n];
                    if (! empty($this->result_browser_version)) {
                        $browser_need_continue = false;
                        break;
                    }
                }
            }

            unset($browser_list);
        }

        // Gecko engine detection

        if ($this->get_mode !== 'device' && $this->match_ua('Gecko/') && $this->match_ua('/\srv:[.0-9a-z]+\)\sGecko\/[.0-9]+\s/')) {
            $this->result_browser_gecko_version = 0;
            $match = $this->match_ua('/\srv:([0-9]+\.[0-9]+)(?:[.0-9a-z]+)?\)\sGecko\/[.0-9]+\s/');
            if ($match[1] >= 5) {
                $match[1] = intval($match[1]);
            } else {
                $match[1] = (float) $match[1];
            }
            if (! empty($match[1])) {
                $this->result_browser_gecko_version = $match[1];
            }
        }

        // WebKit engine detection

        if ($this->get_mode !== 'device' && $this->result_browser_chromium_version == 0 && $this->match_ua('AppleWebKit/')) {
            $this->result_browser_webkit_version = 0;
            $match = $this->match_ua('/AppleWebKit\/([0-9]+\.[0-9]+)/');
            if (! empty($match[1])) {
                $this->result_browser_webkit_version = (float) $match[1];
            }
        }

        /*
        ------------------
         Desktop Browsers
        ------------------
        */

        if ($browser_need_continue && $this->result_browser_chrome_original == 0 && $this->result_browser_firefox_original == 0 && $this->result_mobile == 0) {
            // Safari

            $browser_list[] = ['Safari', '/AppleWebKit\/[.0-9]+.*Gecko\)\sSafari\/[.0-9A-Za-z]+$/', '/Safari\/(\d+)/', '1', 'Version/'];
            $browser_list[] = ['Safari', '/Version\/([0-9]+\.[0-9]+).*Safari/', '/Version\/([0-9]+\.[0-9]+).*Safari/', '1', 'Epiphany'];

            // IE

            $browser_list[] = ['Internet Explorer', 'MSIE', '/MSIE(?:\s|)([0-9]+)/', '1', 'Opera|IEMobile|Trident/'];
            $browser_list[] = ['Internet Explorer', 'Trident/', '/Trident\/([0-9]+)/', '1', 'Opera|IEMobile'];

            // Chromium based browsers

            $browser_list[] = ['Atom', 'Atom/', '/Atom\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Avast Secure Browser', 'Avast/', '/Avast\/([0-9]+)/', '1', ''];
            $browser_list[] = ['AVG Secure Browser', 'AVG/', '/AVG\/([0-9]+)/', '1', ''];
            $browser_list[] = ['CCleaner Browser', 'CCleaner/', '/CCleaner\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Comodo Dragon', 'Dragon/', '/Dragon\/([0-9]+)/', '1', 'IceDragon'];
            $browser_list[] = ['Iridium', 'Iridium/', '/Iridium\/.*Safari.*Chrome\/([0-9]+)/', '1', ''];
            $browser_list[] = ['MxNitro', 'MxNitro|mxnitro', '/(MxNitro|mxnitro)\/([0-9]+\.[0-9]+)/', '2', ''];
            $browser_list[] = ['Iron', ' Iron ', '/Chrome\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Iron', 'Iron/', '/Iron\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Hola Browser', 'Hola/', '/Hola\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Sogou Explorer', '/Chrome.*Safari.*SE\s.*MetaSr/', '/Chrome\/([0-9]+)/', '1', ''];
            $browser_list[] = ['360 browser', 'QIHU 360', '/Chrome\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Slimjet', 'Slimjet/', '/Slimjet\/([0-9]+)/', '1', ''];
            $browser_list[] = ['FreeU', 'FreeU|Freeu', '/(FreeU|Freeu)\/([0-9]+)/', '2', ''];
            $browser_list[] = ['Kinza', 'Kinza/', '/Kinza\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Station', 'Station/', '/Station\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Superbird', 'Superbird|SuperBird', '/(Superbird|SuperBird)\/([0-9]+)/', '2', ''];
            $browser_list[] = ['Polypane', 'Polypane/', '/Polypane\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['OhHai Browser', 'OhHaiBrowser/', '/OhHaiBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Sizzy', 'Sizzy', '/Sizzy\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['AOL Desktop', 'ADG/', '/ADG\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Elements Browser', 'Elements Browser', '/Elements\sBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['115Browser', '115Browser', '/115Browser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Falkon', 'Falkon/', '/Falkon\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['AOL Shield Pro', 'AOLShield', '/AOLShield\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Google Earth', 'Google Earth', '/Google Earth(?:|\sPro)\/([0-9]+\.[0-9]+)/', '1', ''];

            // Webkit, Gecko and other engine based browsers

            $browser_list[] = ['Cyberfox', 'Cyberfox/', '/Cyberfox\/([0-9]+)/', '1', ''];
            $browser_list[] = ['SeaMonkey', 'SeaMonkey/', '/SeaMonkey\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['K-Meleon', 'K-Meleon', '/K\-Meleon\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['IceWeasel', 'Iceweasel/', '/Iceweasel\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['IceApe', 'Iceape/', '/Iceape\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Comodo Ice Dragon', 'IceDragon/', '/IceDragon\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['QtWeb', 'QtWeb Internet Browser/', '/QtWeb\sInternet\sBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['QtWebEngine', 'QtWebEngine', '/QtWebEngine\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Qt', 'Qt/', '/Qt\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['WebKitGTK', 'WebKitGTK+/', '/WebKitGTK\+\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Konqueror', 'Konqueror', '/Konqueror\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Konqueror', 'konqueror', '/konqueror\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Epiphany', 'Epiphany', '/Epiphany\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['OmniWeb', 'OmniWeb/', '/OmniWeb\//', '1', ''];
            $browser_list[] = ['iCab', 'iCab', '/iCab(\s|\/)([0-9]+\.[0-9]+)/', '2', ''];
            $browser_list[] = ['Camino', 'Camino/', '/Camino\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Midori', 'Midori|midori', '/Midori\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Otter', 'Otter/', '/Otter\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Kazehakase', 'Kazehakase/', '/Kazehakase\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['QupZilla', 'QupZilla/', '/QupZilla\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Waterfox', 'Waterfox/', '/Waterfox\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Firefox', 'BonEcho/', '/BonEcho\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Basilisk', 'Basilisk/', '/Basilisk\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Dooble', 'Dooble/', '/Dooble\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Fluid', 'Fluid/', '/Fluid\/([0-9]+\.[0-9]+)/', '1', ''];

            foreach ($browser_list as $browser_list_va) {
                $match = $browser_list_va[1];
                $match_exclude = $browser_list_va[4];

                if ($this->match_ua($match) && ! $this->match_ua($match_exclude)) {
                    $this->result_browser_version = 0;
                    $this->result_browser_name = $browser_list_va[0];
                    $matches = $this->match_ua($browser_list_va[2]);
                    $matches_n = intval($browser_list_va[3]);
                    $this->result_browser_version = (float) $matches[$matches_n];

                    // Safari old version conversion

                    if ($match === '/AppleWebKit\/[.0-9]+.*Gecko\)\sSafari\/[.0-9A-Za-z]+$/') {
                        $ev = intval($this->result_browser_version);
                        if (! empty($ev)) {
                            $this->result_browser_version = 1;
                        }
                        if ($ev > 400) {
                            $this->result_browser_version = 2;
                        }
                        if ($ev > 500) {
                            $this->result_browser_version = 3;
                        }
                        if ($ev > 527) {
                            $this->result_browser_version = 4;
                        }
                        if ($ev > 532) {
                            $this->result_browser_version = 5;
                        }
                        if ($ev > 535) {
                            $this->result_browser_version = 6;
                        }
                        $this->result_browser_safari_original = 1;
                    }

                    // IE Trident engine version conversion

                    if ($match === 'Trident/') {
                        if ($this->result_browser_version == 4) {
                            $this->result_browser_version = 8;
                        }
                        if ($this->result_browser_version == 5) {
                            $this->result_browser_version = 9;
                        }
                        if ($this->result_browser_version == 6) {
                            $this->result_browser_version = 10;
                        }
                        if ($this->result_browser_version == 7) {
                            $this->result_browser_version = 11;
                        }
                    }

                    if (! empty($this->result_browser_version)) {
                        $browser_need_continue = false;
                        break;
                    }
                }
            }

            unset($browser_list);

            // End of desktop browsers detection
        }

        /*
        -----------------
         Mobile Browsers
        -----------------
        */

        if ($browser_need_continue && $this->result_browser_chrome_original == 0 && $this->result_browser_firefox_original == 0 && $this->result_mobile == 1) {
            // Mobile browsers with detectable versions

            $browser_list[] = ['Safari Mobile', '/(iPhone|iphone|iPad|iPod).*AppleWebKit\/.*Version\/[.0-9]+\sMobile\/\w+\sSafari\/[.0-9A-Za-z]+$/', '/Version\/([0-9]+\.[0-9]+).+?Mobile\/\w+\s(Safari)/', '1', ''];
            $browser_list[] = ['Safari Mobile', '/(Intel\sMac\sOS\sX).*AppleWebKit\/.*Version\/[.0-9]+\s(?:|Mobile\/\w+\s)Safari\/[.0-9A-Za-z]+$/', '/Version\/([0-9]+\.[0-9]+).+?(?:|Mobile\/\w+\s)(Safari)/', '1', ''];
            $browser_list[] = ['Android Browser', '/Android.*Version\/[.0-9]+\s(?:Mobile\s)?Safari\/[.0-9]+$/', '/Android.*Version\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Samsung Browser', 'SamsungBrowser', '/SamsungBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Firefox Focus', 'Focus/', '/Focus\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Firefox iOS', 'FxiOS', '/FxiOS\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Opera Mini', 'Opera Mini|OPiOS', '/(Opera Mini|OPiOS)\/([0-9]+\.[0-9]+)/', '2', ''];
            $browser_list[] = ['Opera Touch', 'OPT/', '/OPT\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Huawei Browser', 'HuaweiBrowser/', '/HuaweiBrowser\/([0-9]+)/', '1', ''];
            $browser_list[] = ['DuckDuckGo', 'DuckDuckGo/', '/DuckDuckGo\/([0-9]+)/', '1', ''];
            $browser_list[] = ['MIUI Browser', 'MiuiBrowser/', '/MiuiBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Mint Browser', 'Mint Browser/', '/Mint\sBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Google App', '/(iPhone|iphone|iPad|iPod).*\)\sGSA\/([0-9]+)/', '/(iPhone|iphone|iPad|iPod).*\)\sGSA\/([0-9]+)/', '2', ''];
            $browser_list[] = ['Google App', '/\sGSA\//', '/\sGSA\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Facebook App', 'FBAV/|FBSV/', '/FBAV\/([0-9]+)\./', '1', ''];
            $browser_list[] = ['Instagram App', 'Instagram', '/Instagram\s([0-9]+)\./', '1', ''];
            $browser_list[] = ['Yandex App', 'YaApp_', '/YaApp_(Android|iOS)\/([0-9]+\.[0-9]+)/', '2', ''];
            $browser_list[] = ['Yandex App', 'YandexSearch/', '/YandexSearch\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Dolfin', 'Dolfin/', '/Dolfin\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Flipboard App', 'Flipboard/', '/Flipboard\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['FreeU', 'FreeU|Freeu', '/(FreeU|Freeu)\/([0-9]+\.[0-9]+)/', '2', ''];
            $browser_list[] = ['Iron', 'Iron Safari|MobileIron', '/Chrome\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Sogou Mobile Browser', 'SogouMobileBrowser/', '/SogouMobileBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Meizu Browser', 'MZBrowser/', '/MZBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['360 Mobile Browser', '360 Aphone', '/360\sAphone\sBrowser\s\(([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Aloha Browser', 'AlohaBrowser/', '/AlohaBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Ecosia Browser', 'Ecosia', '/Chrome\/([0-9]+)/', '1', ''];
            $browser_list[] = ['HeyTap Browser', 'HeyTapBrowser/', '/HeyTapBrowser\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Oppo Browser', 'OppoBrowser/', '/OppoBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Silk', 'Silk/', '/Silk\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Surf Browser', 'SurfBrowser/', '/SurfBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Phoenix Browser', 'PHX/', '/PHX\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['CM Mobile', 'ACHEETAHI', '/Chrome\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Bing Search App', 'BingWeb/', '/BingWeb\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Firefox Klar', 'Klar/', '/Klar\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Super Fast Browser', 'SFBrowser|tssomas', '/(SFBrowser|tssomas)\/([0-9]+\.[0-9]+)/', '2', ''];
            $browser_list[] = ['Tenta Browser', 'Tenta/', '/Tenta\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Kiwi Browser', 'Kiwi Chrome/', '/Kiwi\sChrome\/([0-9]+)/', '1', ''];
            $browser_list[] = ['JioBrowser', 'JioBrowser/', '/JioBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Qwant Mobile', 'QwantBrowser', '/QwantBrowser\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Qwant Mobile', 'QwantMobile', '/QwantMobile\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Baidu Mobile Browser', 'bdbrowser', '/bdbrowser(|_i18n)\/([0-9]+\.[0-9]+)/', '2', ''];
            $browser_list[] = ['Baidu Search App', 'baiduboxapp/', '/baiduboxapp\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Baidu Browser for Tablet', 'BaiduHD/', '/BaiduHD\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['NineSky', 'Ninesky-android-mobile/', '/Ninesky\-android\-mobile\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Realme Browser', 'RealmeBrowser/', '/RealmeBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Vivo Browser', 'VivoBrowser/', '/VivoBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Web Explorer', 'Web Explorer/', '/Web\sExplorer\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['NTENT Browser', 'NTENTBrowser/', '/NTENTBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Nox Browser', 'NoxBrowser/', '/NoxBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Quark Browser', 'Quark/', '/Quark\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Yaani Browser', 'YaaniBrowser', '/YaaniBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Tizen Browser', '/Tizen(?: )?(?:\/)?[.0-9]+.*AppleWebKit\/.*(Version|SLP Browser|Tizen Browser|TizenBrowser)\/([0-9]+\.[0-9]+)/', '/Tizen(?: )?(?:\/)?[.0-9]+.*AppleWebKit\/.*(Version|SLP Browser|Tizen Browser|TizenBrowser)\/([0-9]+\.[0-9]+)/', '2', ''];
            $browser_list[] = ['Internet Explorer Mobile', 'IEMobile', '/IEMobile(?: )?(?:\/)?([0-9]+)/', '1', ''];
            $browser_list[] = ['Internet Explorer Mobile', 'Trident/', '/Trident\/[.0-9].*Touch;*.rv\:([0-9]+)/', '1', ''];
            $browser_list[] = ['ZetaKey', 'Zetakey/', '/Zetakey\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Nokia Browser', 'NokiaBrowser/', '/NokiaBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['NetFront', 'NetFront/', '/NetFront\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['BlackBerry Browser', '/(BB10;|BlackBerry|PlayBook).*AppleWebKit.*Version\/([0-9]+\.[0-9]+)/', '/(BB10;|BlackBerry|PlayBook).*AppleWebKit.*Version\/([0-9]+\.[0-9]+)/', '2', ''];
            $browser_list[] = ['ONE Browser', 'OneBrowser/', '/OneBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['Web Explorer', 'webexplorer/', '/webexplorer\/([0-9]+)/', '1', ''];
            $browser_list[] = ['Start Internet Browser', 'Start/', '/Start\/([0-9]+)/', '1', ''];
            $browser_list[] = ['EUI Browser', 'EUI Browser', '/EUI\sBrowser\/([0-9]+\.[0-9]+)/', '1', ''];
            $browser_list[] = ['iCab Mobile', 'iCabMobile', '/iCabMobile(\s|\/)([0-9]+\.[0-9]+)/', '2', ''];

            foreach ($browser_list as $browser_list_va) {
                $match = $browser_list_va[1];
                $match_exclude = $browser_list_va[4];

                if ($this->match_ua($match) && ! $this->match_ua($match_exclude)) {
                    $this->result_browser_version = 0;
                    $this->result_browser_name = $browser_list_va[0];
                    $matches = $this->match_ua($browser_list_va[2]);
                    $matches_n = intval($browser_list_va[3]);
                    $this->result_browser_version = (float) $matches[$matches_n];

                    if (! empty($this->result_browser_version)) {
                        $browser_need_continue = false;
                        break;
                    }
                }
            }

            unset($browser_list);

            // Other mobile browsers without version specified

            if ($browser_need_continue) {
                $other_bsr = [];

                $other_bsr[] = ['Maple' => 'Maple'];
                $other_bsr[] = ['Espial' => 'Espial'];

                foreach ($other_bsr as $other_bsr_va) {
                    $k = key($other_bsr_va);
                    $v = $other_bsr_va[$k];

                    if ($this->match_ua($v)) {
                        $this->result_browser_version = 0;
                        $this->result_browser_name = $k;
                        $this->result_browser_title = $k;
                        break;
                    }
                }
                unset($other_bsr);
            }

            /*
            -------------------
             WebView detection
            -------------------
            */

            // Android WebView

            if ($this->match_ua('Android') && $this->match_ua('; wv|;FB|FB_IAB|OKApp')) {
                $this->result_browser_android_webview = 1;
            }

            // Safari WebView

            if ($this->result_ios) {
                if (! $this->match_ua('Safari|iCabMobile') && $this->match_ua('/Darwin\/|\shw\/|FBAV\/|FBSV\/|mailapp\/|OKApp/')) {
                    $this->result_browser_ios_webview = 1;
                    if (! $this->match_ua('FBAV/|FBSV/')) {
                        $this->result_browser_name = 'Mobile Safari WebView';
                        $this->result_browser_version = 0;
                    }
                }
            }

            // End of mobile browsers detection
        }

        /*
        -------------------------
         Detect Safari original
        -------------------------
        */

        if ($this->result_browser_name === 'Safari' || $this->result_browser_name === 'Safari Mobile') {
            if ($this->match_ua('/AppleWebKit\/[.0-9]+.*Gecko\)\sVersion\/[.0-9].*Safari\/[.0-9A-Za-z]+$/')) {
                $this->result_browser_safari_original = 1;
            }
        }

        // Check and correct browser version anomaly

        if (intval($this->result_browser_version) > 100 && ! $this->match_ua('FBAV/|FBSV/|GSA/|Instagram')) {
            $this->result_browser_version = 0;
        }

        // Set Browser title

        if (! empty($this->result_browser_version)) {
            $this->result_browser_title = $this->result_browser_name.' '.$this->result_browser_version;
        } else {
            if ($this->result_browser_name === 'Mobile Safari WebView') {
                $this->result_browser_title = $this->result_browser_name;
            } else {
                $this->result_browser_title = $this->result_browser_name.' (unknown version)';
            }
        }

        if (strpos($this->result_browser_name, 'unknown') !== false) {
            $this->result_browser_title = 'unknown';
        }
        if ($this->result_browser_version == null) {
            $this->result_browser_version = 0;
        }

        if ($this->get_mode === 'browser') {
            return null;
        }

        /*
        ---------------------
         Detect device types
        ---------------------
        */

        // Desktop

        if ($this->result_os_family !== 'unknown' && $this->result_os_type !== 'mixed') {
            $this->result_device_type = 'desktop';
        }

        // Mobile

        if ($this->result_mobile == 1) {
            $this->result_device_type = 'mobile';
        }

        // Other

        if (! $this->result_browser_chrome_original) {
            // TV
            if ($this->match_ua('TV|HDMI|CrKey| Escape |Kylo/|SmartLabs|SC/IHD|Viera|BRAVIA|NetCast|Roku/DVP| Roku |Maple|DuneHD|CE-HTML|EIS iPanel|Sunniwell; Resolution|Freebox|Netbox|Netgem|AFTT|AFTM')) {
                $this->result_device_type = 'tv';
            }

            // Console
            if ($this->matchi_ua('playstation') || $this->match_ua('Xbox|GAMEPAD|Nintendo|; SHIELD|OUYA')) {
                $this->result_device_type = 'console';
            }

            // MediaPlayer
            if ($this->match_ua('iPod|iTunes|AlexaMediaPlayer|AppleCoreMedia')) {
                $this->result_device_type = 'mediaplayer';
            }

            // Car
            if ($this->match_ua('CarBrowser')) {
                $this->result_device_type = 'car';
            }

            // Watch
            if ($this->matchi_ua('watch') && ! $this->match_ua('AirWatch')) {
                $this->result_device_type = 'watch';
            }
        }

        $this->touch_support_mode = false;

        return null;
    }

    /**
     * The method returns all possible environment data from User-Agent.
     *
     * @param string $ua The User-Agent string
     * @param string $result_format If specified, method returns result as JSON
     * @return mixed Returns User-Agent parsing result as array or JSON string (optional)
     */
    public function getAll($ua, $result_format = '')
    {
        $this->useragent = trim($ua);
        $this->get_mode = 'all';
        $this->resetProperties();
        $this->getResult();
        $result = ['os_type' => $this->result_os_type,
            'os_family' => $this->result_os_family,
            'os_name' => $this->result_os_name,
            'os_version' => $this->result_os_version,
            'os_title' => $this->result_os_title,
            'device_type' => $this->result_device_type,
            'browser_name' => $this->result_browser_name,
            'browser_version' => $this->result_browser_version,
            'browser_title' => $this->result_browser_title,
            'browser_chrome_original' => $this->result_browser_chrome_original,
            'browser_firefox_original' => $this->result_browser_firefox_original,
            'browser_safari_original' => $this->result_browser_safari_original,
            'browser_chromium_version' => $this->result_browser_chromium_version,
            'browser_gecko_version' => $this->result_browser_gecko_version,
            'browser_webkit_version' => $this->result_browser_webkit_version,
            'browser_android_webview' => $this->result_browser_android_webview,
            'browser_ios_webview' => $this->result_browser_ios_webview,
            'browser_desktop_mode' => $this->result_browser_desktop_mode, ];

        if (strtolower($result_format) === 'json') {
            $result = json_encode($result);
        }

        return $result;
    }

    /**
     * The method returns OS data from User-Agent.
     *
     * @param string $ua The User-Agent string
     * @param string $result_format If specified, method returns result as JSON
     * @return mixed Returns User-Agent parsing result as array or JSON string (optional)
     */
    public function getOS($ua, $result_format = '')
    {
        $this->useragent = trim($ua);
        $this->get_mode = 'os';
        $this->resetProperties();
        $this->getResult();
        $result = ['os_type' => $this->result_os_type,
            'os_family' => $this->result_os_family,
            'os_name' => $this->result_os_name,
            'os_version' => $this->result_os_version,
            'os_title' => $this->result_os_title, ];

        if (strtolower($result_format) === 'json') {
            $result = json_encode($result);
        }

        return $result;
    }

    /**
     * The method returns Browser data from User-Agent.
     *
     * @param string $ua The User-Agent string
     * @param string $result_format If specified, method returns result as JSON
     * @return mixed Returns User-Agent parsing result as array or JSON string (optional)
     */
    public function getBrowser($ua, $result_format = '')
    {
        $this->useragent = trim($ua);
        $this->get_mode = 'browser';
        $this->resetProperties();
        $this->getResult();
        $result = ['browser_name' => $this->result_browser_name,
            'browser_version' => $this->result_browser_version,
            'browser_title' => $this->result_browser_title,
            'browser_chrome_original' => $this->result_browser_chrome_original,
            'browser_firefox_original' => $this->result_browser_firefox_original,
            'browser_safari_original' => $this->result_browser_safari_original,
            'browser_chromium_version' => $this->result_browser_chromium_version,
            'browser_gecko_version' => $this->result_browser_gecko_version,
            'browser_webkit_version' => $this->result_browser_webkit_version,
            'browser_android_webview' => $this->result_browser_android_webview,
            'browser_ios_webview' => $this->result_browser_ios_webview,
            'browser_desktop_mode' => $this->result_browser_desktop_mode, ];

        if (strtolower($result_format) === 'json') {
            $result = json_encode($result);
        }

        return $result;
    }

    /**
     * The method returns Device Type data from User-Agent.
     *
     * @param string $ua The User-Agent string
     * @param string $result_format If specified, method returns result as JSON
     * @return mixed Returns User-Agent parsing result as array or JSON string (optional)
     */
    public function getDevice($ua, $result_format = '')
    {
        $this->useragent = trim($ua);
        $this->get_mode = 'device';
        $this->resetProperties();
        $this->getResult();
        $result = ['device_type' => $this->result_device_type];

        if (strtolower($result_format) === 'json') {
            $result = json_encode($result);
        }

        return $result;
    }

    /**
     * This method is needed to detect mobile browsers in Desktop Mode (Android and iOS)
     * The method should call if browser supports Touch events (client-side JS detection is necessary).
     *
     * @return null
     */
    public function setTouchSupport()
    {
        $this->touch_support_mode = true;

        return null;
    }
}
