<?php

if (!class_exists('Thrive_Icon_Manager_Data')) {

    /**
     *
     * handles the processing and retrieving of all icon-manager related data
     *
     * Class Thrive_Icon_Manager_Data
     */
    class Thrive_Icon_Manager_Data
    {

        /**
         * a list containing all expected files inside a zip archive - relative to the archive root folder
         * @var array
         */
        protected $zip_contents = array(
            'demo.html',
            'style.css',
            'selection.json',
            'fonts/[fontFamily].eot',
            'fonts/[fontFamily].svg',
            'fonts/[fontFamily].ttf',
            'fonts/[fontFamily].woff',
        );

        /**
         *
         * @param string $zip_file full path to IcoMoon zip file
         *
         * @return array processed icons - Icon classes and the url to the css file
         */
        public function processZip($zip_file, $zip_url)
        {
            $clean_filename = $this->validateZip($zip_file);

            //Step 1: make sure the destination folder exists
            $font_dir_path = dirname($zip_file) . '/' . $clean_filename;

            $old_umask = umask(0);
            if (!is_dir($font_dir_path) && !@mkdir($font_dir_path, 0777)) {
                throw new Exception('Could not create Icon folder');
            }

            //Step 2: unzip the archive to destination
            define('FS_METHOD', 'direct');
            WP_Filesystem();
            $result = unzip_file($zip_file, $font_dir_path);
            if (is_wp_error($result)) {
                throw new Exception('Error (unzip): ' . $result->get_error_message());
            }

            //Step 3: process the zip

            //check for selection.json file to be able to check the font family from within it
            $this->checkExtractedFiles($font_dir_path, 'selection.json');

            //read the config
            $config = @json_decode(file_get_contents($font_dir_path . '/selection.json'), true);

            //read the font family from config
            $font_family = $config['preferences']["fontPref"]["metadata"]["fontFamily"];

            //replace the placeholders for expecting files
            $this->prepareRequiredFiles($font_family);

            //check the files expected to be in zip
            $this->checkExtractedFiles($font_dir_path);
            $this->applyChangesOnStyle($font_dir_path . '/style.css', $font_family);

            umask($old_umask);

            if (empty($config) || empty($config['icons'])) {
                throw new Exception('It seems something is wrong inside the selection.json config file');
            }
            $data = array(
                'folder' => $font_dir_path,
                'css' => dirname($zip_url) . '/' . $clean_filename . '/style.css',
                'icons' => array(),
                'fontFamily' => $font_family,
                'css_version' => rand(1, 9) . '.' . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT)
            );

            $prefix = empty($config['preferences']['fontPref']['prefix']) ? 'icon-' : $config['preferences']['fontPref']['prefix'];
            $class_selector = !empty($config['preferences']['fontPref']['classSelector']) ? str_replace('.', '', $config['preferences']['fontPref']['classSelector']) : '';

            foreach ($config['icons'] as $icon_data) {
                if (empty($icon_data['properties']['name'])) {
                    continue;
                }
                $data['icons'] [] = ($class_selector ? $class_selector . ' ' : '') . $prefix . $icon_data['properties']['name'];
            }

            return $data;
        }

        /**
         * Replace the placeholders from required file names with $font_family
         * @param $font_family
         */
        public function prepareRequiredFiles($font_family)
        {
            foreach($this->zip_contents as $key => $file) {
                $this->zip_contents[$key] = str_replace("[fontFamily]", $font_family, $file);
            }
        }

        public function applyChangesOnStyle($css_file, $font_family)
        {
            if (!is_file($css_file)) {
                throw new Exception($css_file . " cannot be found to apply changes on it !");
            }
            $file_content = file_get_contents($css_file);
            $file_content = str_replace("font-family: '{$font_family}';", "font-family: '{$font_family}' !important;", $file_content);
            return file_put_contents($css_file, $file_content);
        }

        /**
         * validate the file
         *
         * @param string $file
         */
        public function validateZip($file)
        {
            if (empty($file) || !is_file($file)) {
                throw new Exception('Invalid or empty file');
            }

            $info = wp_check_filetype($file);

            if (strtolower($info['ext']) !== 'zip') {
                throw new Exception('The selected file is not a zip archive');
            }

            return trim(str_replace($info['ext'], '', basename($file)), '/. ');

        }

        /**
         * check if all the necessary files exist in the specified $dir
         * @param string $dir
         */
        public function checkExtractedFiles($dir, $specific_files = array())
        {
            if (empty($dir)) {
                throw new Exception('Empty folder');
            }

            if (!is_array($specific_files)) {
                $specific_files = array($specific_files);
            }
            $files_to_check = array_intersect($this->zip_contents, $specific_files);

            if (empty($files_to_check)) {
                $files_to_check = $this->zip_contents;
            }

            foreach ($files_to_check as $expected_file) {
                if (!is_file($dir . '/' . $expected_file)) {
                    throw new Exception('Could not find the following file inside the archive: ' . $expected_file);
                }
            }

        }

        /**
         * try to completely remove the $folder
         * @param string $folder full path to the IcoMoon folder
         */
        public function removeIcoMoonFolder($folder, $fontFamily = '')
        {

            $this->prepareRequiredFiles($fontFamily);

            foreach ($this->zip_contents as $file) {
                if (is_file($folder . '/' . $file)) {
                    @unlink($folder . '/' . $file);
                }
            }

            @rmdir($folder . '/fonts');
            @unlink($folder . '/demo-files/demo.css');
            @unlink($folder . '/demo-files/demo.js');
            @rmdir($folder . '/demo-files');
            @unlink($folder . '/Read Me.txt');

            @rmdir($folder);
        }
    }
}
