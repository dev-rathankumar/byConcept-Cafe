<?php
defined("ABSPATH") or die("");

/**
 * The base class for all screen.php files.  This class is used to control items that are common
 * among all screens, namely the Help tab and Screen Options drop down items.  When creating a 
 * screen object please extent this class.
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes/ui
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 * @since 3.3.0
 *
 */
class DUP_PRO_UI_Screen
{
    /**
     * Used as a placeholder for the current screen object
     */
    public $screen;

    /**
     *  Init this object when created
     */
    public function __construct()
    {

    }

    /**
     * Get the help support tab view content shown in the help system
     *
     * @param string $guide		The target URL to navigate to on the online user guide
     * @param string $faq		The target URL to navigate to on the online user tech FAQ
     *
     * @return null
     */
    public function getSupportTab($guide, $faq)
    {
        $content = DUP_PRO_U::__("<b>Need Help?</b>  Please check out these resources:"
			."<ul>"
				."<li><a href='https://snapcreek.com/duplicator/docs/guide{$guide}' target='_sc-faq'>Full Online User Guide</a></li>"
				."<li><a href='https://snapcreek.com/duplicator/docs/faqs-tech{$faq}' target='_sc-faq'>Frequently Asked Questions</a></li>"
				."<li><a href='https://snapcreek.com/duplicator/docs/quick-start/' target='_sc-faq'>Quick Start Guide</a></li>"
			."</ul>");

        $this->screen->add_help_tab(array(
            'id' => 'dpro_help_tab_callback',
            'title' => DUP_PRO_U::esc_html__('Support'),
            'content' => "<p>{$content}</p>"
            )
        );
    }

    /**
     * Get the help support side bar found in the right most part of the help system
     *
     * @return null
     */
    public function getHelpSidbar()
    {
        $txt_title = DUP_PRO_U::__("Resources");
        $txt_home  = DUP_PRO_U::__("Knowledge Base");
        $txt_guide = DUP_PRO_U::__("Full User Guide");
        $txt_faq   = DUP_PRO_U::__("Technical FAQs");
		$txt_sets  = DUP_PRO_U::__("Package Settings");
        $this->screen->set_help_sidebar(
            "<div class='dpro-screen-hlp-info'><b>".esc_html($txt_title).":</b> <br/>"
            ."<i class='fa fa-home'></i> <a href='https://snapcreek.com/duplicator/docs/' target='_sc-home'>".esc_html($txt_home)."</a> <br/>"
            ."<i class='fa fa-book'></i> <a href='https://snapcreek.com/duplicator/docs/guide/' target='_sc-guide'>".esc_html($txt_guide)."</a> <br/>"
            ."<i class='far fa-file-code'></i> <a href='https://snapcreek.com/duplicator/docs/faqs-tech/' target='_sc-faq'>".esc_html($txt_faq)."</a> <br/>"
			."<i class='fas fa-cog'></i> <a href='admin.php?page=duplicator-pro-settings&tab=package'>".esc_html($txt_sets)."</a></div>"
        );
    }
}