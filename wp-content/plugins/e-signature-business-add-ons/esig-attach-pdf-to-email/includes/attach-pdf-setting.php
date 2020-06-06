<?php

class esigAttachmentSetting {

    const PDF_ATTACHMENT_TEXT = 'esig_pdf_attachment_';

    public static function get($documentId) {

        $pdfAttachment = WP_E_Sig()->setting->get_generic(self::PDF_ATTACHMENT_TEXT . $documentId);
        if ($pdfAttachment) {
            return $pdfAttachment;
        }
        return WP_E_Sig()->meta->get($documentId, self::PDF_ATTACHMENT_TEXT);
    }

    public static function save($documentId, $value) {
        WP_E_Sig()->meta->add($documentId, self::PDF_ATTACHMENT_TEXT, $value);
    }

    public static function is_enabled($documentId) {
        if (self::get($documentId)) {
            return true;
        }
        return false;
    }

    public static function is_pdf_inactive() {
        if (!class_exists('ESIG_PDF_Admin')) {
            return true;
        }
        return false;
    }

    public static function isPublicUrl($documentId) {
        $documentType = WP_E_Sig()->document->getDocumenttype($documentId);
        if ($documentType == 'stand_alone') {
            return true;
        }
        return false;
    }

}
