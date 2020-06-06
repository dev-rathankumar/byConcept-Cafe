<?php

class esigBrandingSetting {

    /**
     * Instance of this class.
     * @since    1.0.1
     * @var      object
     */
    protected static $instance = null;

    /**
     * Return an instance of this class.
     * @since     0.1
     * @return    object    A single instance of this class.
     */
    public static function instance() {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function wpUserId($wpUserId) {
        if (!$wpUserId) {
            return WP_E_Sig()->user->esig_get_super_admin_id();
        }
        return $wpUserId;
    }

    public function logoTagLine($wpUserId = false) {
        $tagLine = WP_E_Sig()->setting->get('esig_branding_logo_tagline', $this->wpUserId($wpUserId));
        if ($tagLine) {
            return htmlspecialchars(stripslashes($tagLine));
        }
        return htmlspecialchars(stripslashes(WP_E_Sig()->setting->get_generic('esig_branding_logo_tagline' . $this->wpUserId($wpUserId))));
    }

    public function headerImage($wpUserId = false) {
        $headerImage = WP_E_Sig()->setting->get('esig_branding_header_image', $this->wpUserId($wpUserId));
        if ($headerImage) {
            return $headerImage;
        }
        return WP_E_Sig()->setting->get_generic('esig_branding_header_image' . $this->wpUserId($wpUserId));
    }

    public function textHeadLine($wpUserId = false) {
        $textHeadLine = WP_E_Sig()->setting->get('esig_branding_text_headline', $this->wpUserId($wpUserId));
        if ($textHeadLine) {
            return htmlspecialchars(stripslashes($textHeadLine));
        }
        return htmlspecialchars(stripslashes(WP_E_Sig()->setting->get_generic('esig_branding_text_headline' . $this->wpUserId($wpUserId))));
    }

     public function emailFooterHeadLine($wpUserId = false) {
        $emailFooterHeadLine = WP_E_Sig()->setting->get('esig_branding_footer_text_headline', $this->wpUserId($wpUserId));
        if ($emailFooterHeadLine) {
            return htmlspecialchars(stripslashes($emailFooterHeadLine));
        }
        return htmlspecialchars(stripslashes(WP_E_Sig()->setting->get_generic('esig_branding_footer_text_headline' . $this->wpUserId($wpUserId))));
    }
    public function emailFooterText($wpUserId = false) {
        $emailFooterText = WP_E_Sig()->setting->get('esig_branding_email_footer_text', $this->wpUserId($wpUserId));
        if ($emailFooterText) {
            return stripslashes($emailFooterText);
        }
        return stripslashes(WP_E_Sig()->setting->get_generic('esig_branding_email_footer_text' . $this->wpUserId($wpUserId)));
    }

    public function documentHeadImage($wpUserId = false) {
        $headImage = WP_E_Sig()->setting->get('esig_document_head_img', $this->wpUserId($wpUserId));
        if ($headImage) {
            return $headImage;
        }
        return WP_E_Sig()->setting->get_generic('esig_document_head_img' . $this->wpUserId($wpUserId));
    }

    public function brandhingDisable($wpUserId = false) {
        $brandhingDisable = WP_E_Sig()->setting->get('esig_brandhing_disable', $this->wpUserId($wpUserId));
        if ($brandhingDisable) {
            return $brandhingDisable;
        }
        return WP_E_Sig()->setting->get_generic('esig_brandhing_disable' . $this->wpUserId($wpUserId));
    }

    public function coverPage($wpUserId = false) {
        $coverPage = WP_E_Sig()->setting->get('esig_cover_page', $this->wpUserId($wpUserId));
        if ($coverPage) {
            return $coverPage;
        }
        return WP_E_Sig()->setting->get_generic('esig_cover_page' . $this->wpUserId($wpUserId));
    }

    public function backColor($wpUserId = false) {
        $backColor = WP_E_Sig()->setting->get('esig_branding_back_color', $this->wpUserId($wpUserId));
        if ($backColor) {
            return $backColor;
        }
        return WP_E_Sig()->setting->get_generic('esig_branding_back_color' . $this->wpUserId($wpUserId));
    }

    public function headImgageAlignment($wpUserId = false) {
        $headImageAlignment = WP_E_Sig()->setting->get('esig_document_head_img_alignment', $this->wpUserId($wpUserId));
        if ($headImageAlignment) {
            return $headImageAlignment;
        }
        return WP_E_Sig()->setting->get_generic('esig_document_head_img_alignment' . $this->wpUserId($wpUserId));
    }
    
    public function docTitleAlignment($wpUserId = false) {
        $headImageAlignment = WP_E_Sig()->setting->get('esig_document_title_alignment', $this->wpUserId($wpUserId));
        if ($headImageAlignment) {
            return $headImageAlignment;
        }
        return WP_E_Sig()->setting->get_generic('esig_document_title_alignment' . $this->wpUserId($wpUserId));
    }

    public function invitationSender($wpUserId = false) {
        $invitationSender = WP_E_Sig()->setting->get('esig_email_invitation_sender_checked', $this->wpUserId($wpUserId));
        if ($invitationSender) {
            return $invitationSender;
        }
        return WP_E_Sig()->setting->get_generic('esig_email_invitation_sender_checked' . $this->wpUserId($wpUserId));
    }
    
     public function successPageImage($wpUserId = false) {
        $successPageImage = WP_E_Sig()->setting->get('esig_success_page_image', $this->wpUserId($wpUserId));
        if ($successPageImage) {
            return $successPageImage;
        }
        return WP_E_Sig()->setting->get_generic('esig_success_page_image' . $this->wpUserId($wpUserId));
    }
    
     public function successImageAlignment($wpUserId = false) {
        $successImageAlignment = WP_E_Sig()->setting->get('esig_success_img_alignment', $this->wpUserId($wpUserId));
        if ($successImageAlignment) {
            return $successImageAlignment;
        }
        return WP_E_Sig()->setting->get_generic('esig_success_img_alignment' . $this->wpUserId($wpUserId));
    }
    
    public function successParagraphText($wpUserId = false) {
        $successParagraphText = htmlspecialchars(stripslashes( WP_E_Sig()->setting->get('esig_success_page_paragraph_text', $this->wpUserId($wpUserId))));
        if ($successParagraphText) {
            return $successParagraphText;
        }
        return htmlspecialchars(stripslashes( WP_E_Sig()->setting->get_generic('esig_success_page_paragraph_text' . $this->wpUserId($wpUserId))));
    }
    
     public function successImageDisable($wpUserId = false) {
        $successImageDisable = WP_E_Sig()->setting->get('esig_success_page_image_disable', $this->wpUserId($wpUserId));
        if ($successImageDisable) {
            return $successImageDisable;
        }
        return WP_E_Sig()->setting->get_generic('esig_success_page_image_disable' . $this->wpUserId($wpUserId));
    }
    // saving settings.
    public function save_esig_success_page_image($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_success_page_image' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_success_page_image' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_success_page_image', $value);
    }
    
     public function save_esig_success_img_alignment($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_success_img_alignment' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_success_img_alignment' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_success_img_alignment', $value);
    }
    
     public function save_esig_success_page_paragraph_text($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_success_page_paragraph_text' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_success_page_paragraph_text' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_success_page_paragraph_text', $value);
    }
    
     public function save_esig_success_page_image_disable($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_success_page_image_disable' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_success_page_image_disable' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_success_page_image_disable', $value);
    }
    
    // branding settings save
    public function save_esig_branding_header_image($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_branding_header_image' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_branding_header_image' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_branding_header_image', $value);
    }
    
    public function save_esig_branding_logo_tagline($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_branding_logo_tagline' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_branding_logo_tagline' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_branding_logo_tagline', $value);
    }
    
     public function save_esig_branding_footer_text_headline($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_branding_footer_text_headline' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_branding_footer_text_headline' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_branding_footer_text_headline', $value);
    }
    
    public function save_esig_branding_email_footer_text($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_branding_email_footer_text' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_branding_email_footer_text' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_branding_email_footer_text', $value);
    }
    
    public function save_esig_brandhing_disable($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_brandhing_disable' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_brandhing_disable' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_brandhing_disable', $value);
    }
    
     public function save_esig_document_head_img($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_document_head_img' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_document_head_img' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_document_head_img', $value);
    }
    
     public function save_esig_cover_page($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_cover_page' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_cover_page' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_cover_page', $value);
    }
    
     public function save_esig_branding_back_color($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_branding_back_color' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_branding_back_color' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_branding_back_color', $value);
    }
    
     public function save_esig_document_head_img_alignment($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_document_head_img_alignment' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_document_head_img_alignment' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_document_head_img_alignment', $value);
    }
    
    public function save_esig_document_title_alignment($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_document_title_alignment' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_document_title_alignment' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_document_title_alignment', $value);
    }
    
     public function save_esig_email_invitation_sender_checked($wpUserId,$value){
         $oldData = WP_E_Sig()->setting->get_generic('esig_email_invitation_sender_checked' . $wpUserId);
         if($oldData){
             WP_E_Sig()->setting->delete_generic('esig_email_invitation_sender_checked' . $wpUserId);
         }
         WP_E_Sig()->setting->set('esig_email_invitation_sender_checked', $value);
    }
}
