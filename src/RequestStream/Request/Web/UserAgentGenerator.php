<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Request\Web;

/**
 * User agent generator
 */
class UserAgentGenerator
{
    /**
     * Generate random user agent
     *
     * @param string $browser
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function generateUserAgent($browser = null)
    {
        //Possible processors on Linux
        $linuxProc = array( 'i686', 'x86_64' );

        //Mac processors (i also added U;)
        $macProc   = array( 'Intel', 'PPC', 'U; Intel', 'U; PPC' );

        //Add as many languages as you like.
        $lang = array(
            'ru',
            'ru-RU',
            'en',
            'en-EN'
        );

        $allowedBrowser = array('Firefox', 'Opera', 'Chrome', 'IE', 'Safari');

        if ($browser !== null){
            $notBrowser = true;
            foreach ($allowedBrowser as $ab) {
                if (strtolower($ab) == strtolower($browser)) {
                    $browser = $ab;
                    $notBrowser = false;
                    break;
                }
            }

            if ($notBrowser === true) {
                throw new \InvalidArgumentException(sprintf(
                    'Undefined browser "%s". Allowed browsers: "%s"',
                    $browser, implode('", "', $allowedBrowser)
                ));
            }
        } else {
            $browser = $allowedBrowser[array_rand($allowedBrowser)];
        }

        switch ($browser){
            case 'Firefox':
                // Generate Mozilla Firefox agent
                $version = array(
                  date('Ymd', rand(strtotime('2011-1-1'), time())) . ' Firefox/' . rand(5, 7) . '.0',
                  date('Ymd', rand(strtotime('2011-1-1'), time())) . ' Firefox/' . rand(5, 7) . '.0.1',
                  date('Ymd', rand(strtotime('2010-1-1'), time())) . ' Firefox/3.6.' . rand(1, 20),
                  date('Ymd', rand(strtotime('2010-1-1'), time())) . ' Firefox/3.8'
                );

                $platforms = array(
                  '(Windows NT ' . rand(5, 6) . '.' . rand(0, 1) . '; ' . $lang[array_rand($lang, 1)] . '; rv:1.9.' . rand(0, 2) . '.20) Gecko/' . $version[array_rand($version, 1)],
                  '(X11; Linux ' . $linuxProc[array_rand($linuxProc, 1)] . '; rv:' . rand(5, 7) . '.0) Gecko/' . $version[array_rand($version, 1)],
                  '(Macintosh; ' . $macProc[array_rand($macProc, 1)] . ' Mac OS X 10_' . rand(5, 7) . '_' . rand(0, 9) . ' rv:' . rand(2, 6) . '.0) Gecko/' . $version[array_rand($version, 1)]
                );

                $ua = "Mozilla/5.0 " . $platforms[array_rand($platforms, 1)];
                break;

            case 'Safari':
                // Generate Safari agent
                $saf = rand(531, 535) . '.' . rand(1, 50) . '.' . rand(1, 7);
                $version = (rand(0, 1) == 0)
                        ? rand(4, 5) . '.' . rand(0, 1)
                        : $ver = rand(4, 5) . '.0.' . rand(1, 5);

                $platforms = array(
                    '(Windows; U; Windows NT ' . rand(5, 6) . '.' . rand(0, 1) . ") AppleWebKit/{$saf} (KHTML, like Gecko) Version/{$version} Safari/{$version}",
                    '(Macintosh; U; ' . $macProc[array_rand($macProc, 1)] . ' Mac OS X 10_' . rand(5, 7) . '_' . rand(0, 9) . ' rv:' . rand(2, 6) . '.0; ' . $lang[array_rand($lang, 1)] . ") AppleWebKit/{$saf} (KHTML, like Gecko) Version/{$version} Safari/{$saf}",
                    '(iPod; U; CPU iPhone OS ' . rand(3, 4) . '_' . rand(0, 3) . ' like Mac OS X; ' . $lang[array_rand($lang, 1)] . ") AppleWebKit/{$saf} (KHTML, like Gecko) Version/" . rand(3, 4) . ".0.5 Mobile/8B" . rand(111, 119) . " Safari/6{$saf}",
                );

                $ua = "Mozilla/5.0 " . $platforms[array_rand($platforms, 1)];
                break;

            case 'IE':
                // Generate Internet Explorer agent
                $platforms = array(
                    '(compatible; MSIE ' . rand(5, 9) . '.0; Windows NT ' . rand(5, 6) . '.' . rand(0, 1) . '; Trident/' . rand(3, 5) . '.' . rand(0, 1) . ')'
                );

                $ua = "Mozilla/" . rand(4, 5) . ".0 " . $platforms[array_rand($platforms, 1)];
                break;

            case 'Opera':
                // Generate Opera agent
                $platforms = array(
                    '(X11; Linux ' . $linuxProc[array_rand($linuxProc, 1)] . '; U; ' . $lang[array_rand($lang, 1)] . ') Presto/2.9.' . rand(160, 190) . ' Version/' . rand(10, 12) . '.00',
                    '(Windows NT ' . rand(5, 6) . '.' . rand(0, 1) . '; U; ' . $lang[array_rand($lang, 1)] . ') Presto/2.9.' . rand(160, 190) . ' Version/' . rand(10, 12) . '.00'
                );
                $ua = "Opera/9." . rand(10, 99) . ' ' . $platforms[array_rand($platforms, 1)];
                break;

            case 'Chrome':
                // Generate Chrome agent
                $saf = rand(531, 536) . rand(0, 2);

                $platforms = array(
                    '(X11; Linux ' . $linuxProc[array_rand($linuxProc, 1)] . ") AppleWebKit/{$saf} (KHTML, like Gecko) Chrome/" . rand(13, 15) . '.0.' . rand(800, 899) . ".0 Safari/{$saf}",
                    '(Windows NT ' . rand(5, 6) . '.' . rand(0, 1) . ") AppleWebKit/{$saf} (KHTML, like Gecko) Chrome/" . rand(13, 15) . '.0.' . rand(800, 899) . ".0 Safari/{$saf}",
                    '(Macintosh; U; ' . $macProc[array_rand($macProc, 1)] . ' Mac OS X 10_' . rand(5, 7) . '_' . rand(0, 9) . ") AppleWebKit/{$saf} (KHTML, like Gecko) Chrome/" . rand(13, 15) . '.0.' . rand(800, 899) . ".0 Safari/{$saf}"
                );

                $ua = 'Mozilla/5.0' . $platforms[array_rand($platforms, 1)];
                break;

            default:
                throw new \RuntimeException(sprintf('Undefined browser "%s".', $browser));
        }

        return $ua;
    }
}