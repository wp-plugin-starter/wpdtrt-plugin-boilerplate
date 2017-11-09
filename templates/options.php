<?php
/**
 * Template partial for Admin Options page.
 *
 * WP Admin > Settings > DTRT Blocks
 *
 * @uses        WordPress_Admin_Style
 *
 * @package     wpdtrt_blocks
 * @subpackage  wpdtrt_blocks/templates
 * @since     1.0.0
 * @version   1.0.0
 */

  $plugin_options = $this->get_plugin_options();
  $form_submitted = ( $this->options_saved() === true );
  $plugin_version = $this->get_version();
  $plugin_title = $this->get_developer_prefix() . ' ' . $this->get_menu_title();
  $plugin_data_length = $this->get_plugin_data_length();
  $demo_date_last_updated_date = $this->render_last_updated_humanised() ? $this->render_last_updated_humanised() : '';
  $messages = $this->get_messages();

  $options_form_title = $messages['options_form_title']; 
  $options_form_description = $messages['options_form_description']; 
  $options_form_submit = $messages['options_form_submit'];

  $demo_shortcode_params = $this->demo_shortcode_params;
  $demo_display = isset( $this->demo_shortcode_params ) && ( $form_submitted || ( $demo_date_last_updated_date !== '' ) );
  $demo_shortcode = $demo_display ? $this->build_demo_shortcode() : '';
  $demo_data_maxlength = $demo_shortcode_params ? $demo_shortcode_params['number'] : 0;

  if ( $demo_display ) {
    $demo_sample_title = $messages['demo_sample_title'];
    $demo_data_title = $messages['demo_data_title'];
    $demo_shortcode_title = $messages['demo_shortcode_title']; 
    $demo_data_description = $messages['demo_data_description']; 
    $demo_noscript_warning = $messages['demo_noscript_warning']; 
    $demo_data_length = str_replace('#', $plugin_data_length, $messages['demo_data_length']); 
    $demo_data_displayed_length = str_replace('#', $demo_data_maxlength, $messages['demo_data_displayed_length']); 
    $demo_date_last_updated = $messages['demo_date_last_updated']; 
  }
?>

<div class="wrap">

  <div id="icon-options-general" class="icon32"></div>
  <h1>
    <?php echo $plugin_title; ?>
    <span class="wpdtrt-blocks-version"><?php echo $plugin_version; ?></span>
  </h1>
  <noscript>
    <div class="notice notice-warning">
      <p><?php echo $demo_noscript_warning; ?></p>
    </div>
  </noscript>

  <form name="data_form" method="post" action="">

    <?php //hidden field is used by options_saved() ?>
    <input type="hidden" name="wpdtrt_blocks_form_submitted" value="Y" />

    <h2 class="title"><?php echo $options_form_title; ?></h2>
    <p><?php echo $options_form_description; ?></p>

    <fieldset>
      <legend class="screen-reader-text">
        <span><?php echo $demo_sample_title; ?></span>
      </legend>
      <table class="form-table">
        <tbody>
          <?php
            foreach( $plugin_options as $name => $attributes ) {
              echo $this->render_form_element( $name, $attributes );
            }
          ?>
        </tbody>
      </table>
    </fieldset>

    <?php
      submit_button(
        $text = $options_form_submit,
        $type = 'primary',
        $name = 'wpdtrt_blocks_submit', // TODO: can this be generic?
        $wrap = true, // wrap in paragraph
        $other_attributes = null
      );
    ?>

  </form>

  <?php
    if ( $demo_display ):
  ?>

  <h2>
    <span><?php echo $demo_data_title; ?></span>
  </h2>

  <p><?php echo $demo_shortcode_title; ?>:
    <code>
      <?php echo $demo_shortcode; ?>
    </code>
  </p>

  <p><?php echo $demo_data_length; ?>.</p>

  <p><?php echo $demo_data_displayed_length; ?>:</p>

  <div class="wpdtrt-plugin-ajax-response" data-format="ui"></div>

  <h2>
    <span><?php echo $demo_data_title; ?></span>
  </h2>

  <p><?php echo $demo_data_description; ?>.</p>

  <div class="wpdtrt-plugin-ajax-response wpdtrt-blocks-data" data-format="data"></div>

  <p class="wpdtrt-blocks-date">
    <em><?php echo $demo_date_last_updated . ': ' . $demo_date_last_updated_date; ?></em>
  </p>

  <?php
    endif;
  ?>

</div>
<!-- .wrap -->
