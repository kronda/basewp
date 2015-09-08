<?php
/**
 *
 */

/**
 * handles all database updates for the plugin
 *
 * Class Thrive_Clever_Widgets_Database_Manager
 */
class Thrive_Clever_Widgets_Database_Manager
{

    const THRIVE_CLEVER_WIDGETS_VERSION_OPTION_NAME = 'thrive_clever_widgets_version';
    const TCW_DATABASE_PREFIX = 'cw_';

    /**
     * @var string version as xx.xx.xx
     */
    protected static $DB_VERSION;


    /**
     * Get the current version of database tables
     * If there is no version saved 0.0 is returned
     * @return mixed|string|void
     */
    public static function dbVersion()
    {
        if (empty(self::$DB_VERSION)) {
            self::$DB_VERSION = get_option(self::THRIVE_CLEVER_WIDGETS_VERSION_OPTION_NAME, '0.0');
        }

        return self::$DB_VERSION;
    }

    public static function tableName($table_name)
    {
        global $wpdb;
        return $wpdb->prefix . self::TCW_DATABASE_PREFIX . $table_name;
    }

    /**
     * Compare db version with code version
     * Runs all the scrips of old db version until the current code version
     * @param $version
     */
    public function check($version)
    {
        if (version_compare(self::dbVersion(), $version, '<')) {
            $scripts = self::getScripts(self::dbVersion(), $version);

            if (!empty($scripts)) {
                define('THRIVE_CLEVER_WIDGETS_DB_UPDATE', true);
            }

            foreach ($scripts as $filePath) {
                require_once $filePath;
            }

            update_option(self::THRIVE_CLEVER_WIDGETS_VERSION_OPTION_NAME, $version);
        }
    }

    /**
     * get all DB update scripts from $fromVersion to $toVersion
     *
     * @param $fromVersion
     * @param $toVersion
     * @return array
     */
    protected static function getScripts($fromVersion, $toVersion)
    {
        $scripts = array();
        $dir = new DirectoryIterator(plugin_dir_path(__FILE__) . 'migrations/');
        foreach ($dir as $file) {
            /**
             * @var $file DirectoryIterator
             */
            if ($file->isDot()) {
                continue;
            }
            $scriptVersion = self::getScriptVersion($file->getFilename());
            if (empty($scriptVersion)) {
                continue;
            }
            if (version_compare($scriptVersion, $fromVersion, '>') && version_compare($scriptVersion, $toVersion, '<=')) {
                $scripts[$scriptVersion] = $file->getPathname();
            }
        }

        /**
         * sort the scripts in the correct version order
         */
        uksort($scripts, 'version_compare');

        return $scripts;
    }

    /**
     * Parse the scriptName and return the version
     * @param string $scriptName in the following format {name}-{[\d+].[\d+]}.php
     * @return string
     */
    protected static function getScriptVersion($scriptName)
    {
        if (!preg_match('/(.+?)-(\d+)\.(\d+)(.\d+)?\.php/', $scriptName, $m)) {
            return false;
        }
        return $m[2] . '.' . $m[3] . (!empty($m[4]) ? $m[4] : '');
    }

} 