<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!class_exists('ESIG_Save_Pdf')) :

    class ESIG_Save_Pdf {

        private $folderPath;

        public function __construct() {
            
        }

        protected static function create_dir() {

            if (!file_exists(self::dir() . "pdf")) {
                mkdir(self::dir() . "pdf", 0777);
                self::put_index(self::dir() . "pdf");
            }
            return self::dir() . "pdf/";
        }

        public function uploadPath($pdf_name = false) {

            $upload_dir_list = wp_upload_dir();
            $upload_dir = $upload_dir_list['basedir'];

            $upload_path = $upload_dir . "/esig_agreements";
            if (!file_exists($upload_path)) {
                mkdir($upload_dir . "/esig_agreements", 0777);
            }

            if ($pdf_name) {
                $upload_path = $upload_dir . "/esig_agreements/" . $pdf_name;
            } else {
                $upload_path = $upload_dir . "/esig_agreements/";
            }

            return $upload_path;
        }

        public function rootPath() {

            $upload_dir_list = wp_upload_dir();
            $upload_dir = $upload_dir_list['basedir'];

            return $upload_dir;
        }

        private function delete_directory($dirname) {
            if (is_dir($dirname))
                $dir_handle = opendir($dirname);
            if (!$dir_handle)
                return false;
            while ($file = readdir($dir_handle)) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($dirname . "/" . $file))
                        unlink($dirname . "/" . $file);
                    else
                        delete_directory($dirname . '/' . $file);
                }
            }
            closedir($dir_handle);
            rmdir($dirname);
            return true;
        }

        public function downloadPdf() {

            if (!class_exists('PclZip')) {
                // Load class file if it's not loaded yet
                include ABSPATH . 'wp-admin/includes/class-pclzip.php';
            }

            $fileName = 'esig_agreements.zip';

            $root = $this->rootPath();
            $path = $root . "/esig_agreements";
            $tempFile = $root . "/" . $fileName;

            // Add entire folder to the archive
            $archive = new PclZip($tempFile);
            $archive->add($path, PCLZIP_OPT_REMOVE_PATH, $root);
            // Set headers for the zip archive
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header("Content-Length: ".filesize($tempFile));
            ob_clean();
            flush();
            readfile($tempFile);
            unlink($tempFile);
            $this->delete_directory($path);
            exit;
        }

        function newName($path, $filename) {

            $res = "$path/$filename";
            if (!file_exists($res))
                return $res;
            $fnameNoExt = pathinfo($filename, PATHINFO_FILENAME);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            $i = 1;
            while (file_exists("$path/$fnameNoExt ($i).$ext")) {
                $i++;
            }
            return "$path/$fnameNoExt ($i).$ext";
        }

        function savePdf($document_id) {

            $pdf_buffer = ESIG_PDF_Admin::instance()->pdf_document($document_id);
            $pdf_name = ESIG_PDF_Admin::instance()->pdf_file_name($document_id) . ".pdf";

            $upload_path = $this->newName($this->uploadPath(), $pdf_name);    //$this->uploadPath($pdf_name);

            if (!@file_put_contents($upload_path, $pdf_buffer)) {
                
                $uploadfile = @fopen($upload_path, "w");
                @fwrite($uploadfile, $pdf_buffer);
                fclose($uploadfile);
            }
            
        }

    }

    

    

    

    
    
endif;