<?php
/**
 * LoginPress Google Fonts.
 *
 * @since 1.0.1
 */
if ( ! class_exists( 'WP_Customize_Control' ) )
  return NULL;

/**
 * A class to create a dropdown for all google fonts
 */
class LoginPress_Google_Fonts extends WP_Customize_Control {

  private $fonts = false;
  public function __construct( $manager, $id, $args = array(), $options = array() ) {

      $this->fonts = $this->get_fonts();
      parent::__construct( $manager, $id, $args );
  }
  /**
   * Render the content of the category dropdown
   *
   * @return HTML
   */
  public function render_content() {

    if( ! empty( $this->fonts ) ) :

      ?>
      <label>
        <span class="customize-category-select-control"><?php echo esc_html( $this->label ); ?></span>
        <select <?php $this->link(); ?>>
          <option value="">-- Default --</option>
          <?php
          foreach ( $this->fonts as $k => $v ) :

            $font_name = $v->family;//strtolower( str_replace( ' ', '_', $v->family ) );
            printf('<option value="%s" %s>%s</option>', $font_name, selected($this->value(), $k, false), $v->family);

          endforeach;
          ?>
        </select>
      </label>
      <?php
    endif;
  }

  /**
   * Get the google fonts from the API or in the cache
   *
   * @param  integer $amount
   *
   * @return String
   */
  public function get_fonts() {

    $fontFile = LOGINPRESS_PRO_ROOT_PATH . '/fonts/google-web-fonts.txt';
    if( file_exists( $fontFile ) ) {
      $content = json_decode( file_get_contents( $fontFile ) );
    }

      return $content->items;
  }
}
?>
