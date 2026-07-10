<?php
// File: app/utils/HtmlSanitizer.php

class HtmlSanitizer {
    private static $purifier = null;

    /**
     * Initializes HTMLPurifier only once
     */
    private static function init() {
        if (self::$purifier === null) {
            // Check if HTMLPurifier class exists (loaded via Composer)
            if (class_exists('HTMLPurifier_Config')) {
                $config = HTMLPurifier_Config::createDefault();
                // Allow some safe HTML tags usually used in WYSIWYG
                $config->set('HTML.Allowed', 'p,b,i,u,a[href|title],ul,ol,li,br,span[style],strong,em,h1,h2,h3,h4,h5,h6,img[src|alt|width|height|style]');
                // Allow some CSS properties
                $config->set('CSS.AllowedProperties', 'text-align,color,background-color,font-weight,font-style,text-decoration');
                self::$purifier = new HTMLPurifier($config);
            }
        }
    }

    /**
     * Cleans HTML input
     * @param string $html
     * @return string
     */
    public static function clean($html) {
        if (empty($html)) {
            return $html;
        }

        self::init();

        if (self::$purifier !== null) {
            return self::$purifier->purify($html);
        }

        // Fallback if HTMLPurifier is not available for some reason
        return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
    }
}
?>
