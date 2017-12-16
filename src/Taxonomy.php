<?php
/**
 * Plugin taxonomy class.
 *
 * @package   WPPlugin
 * @version   1.0.0
 */

namespace DoTheRightThing\WPPlugin;

if ( !class_exists( 'Taxonomy' ) ) {

  /**
   * Plugin Taxonomy base class
   *
   * Boilerplate functions, including
   * options support, registration, template loading, custom fields
   *
   * @param       array $atts Optional taxonomy attributes specified by the user
   * @return      Taxonomy
   *
   * @see         http://php.net/manual/en/function.ob-start.php
   * @see         http://php.net/manual/en/function.ob-get-clean.php
   *
   * @since       1.0.0
   * @version     1.0.0
   */
  class Taxonomy {

    /**
     * Hook the plugin in to WordPress
     * This constructor automatically initialises the object's properties
     * when it is instantiated,
     * using new Widget
     *
     * @param     array $options Shortcode options
     *
     * @version   1.1.0
     * @since     1.0.0
     */
    function __construct( $options ) {

      // define variables
      $name = null;
      $plugin = null;
      $selected_instance_options = null;
      $labels = null;

      // extract variables
      extract( $options, EXTR_IF_EXISTS );

      // Store a reference to the partner plugin object
      // which stores global plugin options
      $this->set_plugin( $plugin );

      $taxonomy_instance_options = array();

      $plugin_instance_options = $plugin->get_instance_options();

      foreach( $selected_instance_options as $option_name ) {
        $taxonomy_instance_options[ $option_name ] = $plugin_instance_options[ $option_name ];
      }

      $this->set_instance_options( $taxonomy_instance_options );

      $this->set_labels( $labels );

      $this->set_name( $name );

      $this->register_taxonomy();
    }

    //// START GETTERS AND SETTERS \\\\

    /**
     * Get the value of $name
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      string
     */
    public function get_name() {
      return $this->name;
    }

    /**
     * Set the value of $name
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       string
     */
    protected function set_name( $new_name ) {
      $this->name = $new_name;
    }

    /**
     * Get default options
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_instance_options() {
      return $this->instance_options;
    }

    /**
     * Set instance options
     *
     * @param array $instance_options
     *
     * @since 1.0.0
     *
     */
    protected function set_instance_options( $instance_options ) {
      $this->instance_options = $instance_options;
    }

    /**
     * Get the value of $labels
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      array
     */
    public function get_labels() {
      return $this->labels;
    }

    /**
     * Set the value of $labels
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    protected function set_labels( $labels ) {
      $this->labels = $labels;
    }

    /**
     * Set parent plugin, which contains shortcode/widget options
     * This is a global which is passed to the function which instantiates this object.
     * This is necessary because the object does not exist until the WordPress init action has fired.
     *
     * @param object
     *
     * @since 1.0.0
     *
     * @todo Shortcode/Widget implementation questions (#15)
     */
    protected function set_plugin( $plugin ) {
      $this->plugin = $plugin;
    }

    /**
     * Get parent plugin, which contains shortcode/widget options
     *
     * @return object
     *
     * @since 1.0.0
     */
    public function get_plugin() {
      return $this->plugin;
    }

    //// END GETTERS AND SETTERS \\\\

    //// START RENDERERS \\\\

    /**
     * Form field templating for the widget admin page
     * @param       array $instance The WordPress Taxonomy instance
     * @param       string $name
     * @param       array $attributes
     *
     * @return      string
     *
     * @since       1.0.0
     * @version     1.0.0
     * @todo        Add field validation feedback (#10)
     * @see         https://pippinsplugins.com/adding-custom-meta-fields-to-taxonomies/
     */
    public function render_form_element( $instance, $name, $attributes=array() ) {

      // these options don't have attributes
      if ( $name === 'description' ) {
        return;
      }

      // define variables
      $type = null;
      $label = null;
      $size = null;
      $tip = null;
      $options = null;

      // populate variables
      extract( $attributes, EXTR_IF_EXISTS );

      // name as a string
      $nameStr = $name;

      // widget admin layout
      $label_start = '<div class="form-field">';
      $label_end   = '';
      $field_start = '';
      $field_end   = '</div>';
      $tip_element = 'p';
      $classname   = '';

      /**
       * Set the value to the variable with the same name as the $name string
       * e.g. $name="wpdtrt_attachment_map_toggle_label" => $wpdtrt_attachment_map_toggle_label => ('Open menu', 'wpdtrt-attachment-map')
       * @see http://php.net/manual/en/language.variables.variable.php
       * @see https://developer.wordpress.org/reference/classes/wp_widget/get_field_name/
       */

      $plugin = $this->get_plugin();

      $value = $plugin->helper_normalise_field_value(
        ( isset( $instance[$nameStr] ) ? $instance[$nameStr] : null ),
        $type
      );

      /* Construct name attributes for use in form() fields
       * translating e.g. 'number' to 'wp-widget-foobar[1]-number'
       */
      $name = $this->get_field_name($nameStr);

      if ( $nameStr === 'title' ):
        // Display dynamic widget title in .in-widget-title via appendTitle() in wp-admin/js/widgets.min.js;
        $id = $name . '-' . $nameStr;
      else:
        $id = $name;
      endif;

      $plugin = $this->get_plugin();

      /**
       * Load the HTML template
       * The supplied arguments will be available to this template.
       */

      /**
       * ob_start — Turn on output buffering
       * This stores the HTML template in the buffer
       * so that it can be output into the content
       * rather than at the top of the page.
       */
      ob_start();

      require($plugin->get_path() . 'vendor/dotherightthing/wpdtrt-plugin/views/form-element-' . $type . '.php');

      /**
       * ob_get_clean — Get current buffer contents and delete current output buffer
       */
      return ob_get_clean();
    }

    //// END RENDERERS \\\\

    // START REGISTERERS

    /**
     * Register taxonomy
     * @uses ../../../../wp-includes/taxonomy.php
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
     * @see https://www.smashingmagazine.com/2012/01/create-custom-taxonomies-wordpress/
     * @see https://code.tutsplus.com/articles/the-rewrite-api-post-types-taxonomies--wp-25488
     *
     * Register Custom Taxonomy BEFORE the Custom Post Type
     * for the rewrite rule to work
     * for WordPress to build the URL correctly
     * @see https://cnpagency.com/blog/the-right-way-to-do-wordpress-custom-taxonomy-rewrites/
     * @see https://mondaybynoon.com/revisiting-custom-post-types-taxonomies-permalinks-slugs/
     */
    public function register_taxonomy() {

      $name = $this->get_name();

      if ( !taxonomy_exists( $name ) ) {

        $plugin = $this->get_plugin();
        $text_domain = $plugin->get_slug();
        $tax_labels = $this->get_labels();

        $labels = array(
          /**
           * The same as and overridden by $tax->label
           */
          'name' => _x( $tax_labels['plural'], 'taxonomy general name', $text_domain ),

          /**
           * Default: _x( 'Post Tag', 'taxonomy singular name' )
           */
          'singular_name' => _x( $tax_labels['singular'], 'taxonomy singular name', $text_domain ),

          /**
           * Defaults to value of name label.
           */
          'menu_name' => __( $tax_labels['plural'], $text_domain ),

          /**
           * Default:  All Tags / All Categories
           */
          'all_items' => __( 'All ' . $tax_labels['plural'], $text_domain ),

          /**
           * Default: Add New Tag / Add New Category
           */
          'add_new_item' => __( 'Add New ' . $tax_labels['singular'], $text_domain ),

          /**
           * Default: Edit Tag / Edit Category
           */
          'edit_item' => __( 'Edit ' . $tax_labels['singular'], $text_domain ),

          /**
           * Default: View Tag / View Category
           */
          'view_item' => __( 'View ' . $tax_labels['singular'], $text_domain ),

          /**
           * Default: Update Tag / Update Category
           */
          'update_item' => __( 'Update ' . $tax_labels['singular'], $text_domain ),

          /**
           * Default: New Tag Name / New Category Name
           */
          'new_item_name' => __( 'New ' . $tax_labels['singular'] . ' Name', $text_domain ),

          /**
           * This string is not used on non-hierarchical taxonomies such as post tags.
           * Default: null / Parent Category
           */
          'parent_item' => __( 'Parent ' . $tax_labels['singular'], $text_domain ),

          /**
           * The same as parent_item, but with colon : in the end
           * Default: null / Parent Category:
           */
          'parent_item_colon' => __( 'Parent ' . $tax_labels['singular'] . ':', $text_domain ),

          /**
           * Default: Search Tags / Search Categories
           */
          'search_items' => __( 'Search ' . $tax_labels['plural'], $text_domain ),

          /**
           * This string is not used on hierarchical taxonomies.
           * Default: null / Popular Tags
           */
          'popular_items' => __( 'Popular ' . $tax_labels['plural'], $text_domain ),

          /**
           * Used in the taxonomy meta box.
           * This string is not used on hierarchical taxonomies.
           * Default: null / Separate tags with commas
           */
          'separate_items_with_commas' => __( 'Separate ' . $tax_labels['plural'] . ' with commas', $text_domain ),

          /**
           * Used in the meta box when JavaScript is disabled.
           * This string is not used on hierarchical taxonomies.
           * Default: null / Add or remove tags
           */
          'add_or_remove_items' => __( 'Add or remove '. $tax_labels['plural'], $text_domain ),

          /**
           * Used in the taxonomy meta box.
           * This string is not used on hierarchical taxonomies.
           * Default: null / Choose from the most used tags
           */
          'choose_from_most_used' => __( 'Choose from the most used ' . $tax_labels['plural'], $text_domain ),

          /**
           * (3.6+) - the text displayed via clicking 'Choose from the most used tags' in the taxonomy meta box when no tags are available
           * and
           * (4.2+) - the text used in the terms list table when there are no items for a taxonomy.
           * Default: No tags found / No categories found
           */
          'not_found' => __( 'No ' . $tax_labels['plural'] . ' found', $text_domain ),
        );

        $args = array(

              /**
               * Labels - defined above
               */
              'labels' => $labels,

              /**
               * Whether a taxonomy is intended for use publicly
               * either via the admin interface or by front-end users.
               * Default: true
               */
              //'public'            => true,

              /**
               * Whether the taxonomy is publicly queryable.
               * Default: $public.
               */
              //'publicly_queryable'      => true,

              /**
               * Whether to generate a default UI for managing this taxonomy.
               * 3.5+ setting this to false for attachment taxonomies will hide the UI.
               * Default: $public.
               */
              //'show_ui'           => true,

              /**
               * Where to show the taxonomy in the admin menu.
               * show_ui must be true.
               * Default: $show_ui.
               */
              //'show_in_menu'        => true,

              /**
               * Make this taxonomy available for selection in navigation menus.
               * Default: $public.
               */
              //'show_in_nav_menus'       => true,

              /**
               * Make this taxonomy available for selection in navigation menus.
               * Default: $public.
               */
              //'show_in_rest'        => true,

              /**
               * To change the base url of REST API route.
               */
              //'rest_base'           => $tax_labels['slug'],

              /**
               * REST API Controller class name.
               */
              //'rest_controller_class'     => WP_REST_Terms_Controller,

              /**
               * Whether to allow the Tag Cloud widget to use this taxonomy.
               * Default: $show_ui.
               */
              //'show_tagcloud'         => true,

              /**
               * 4.2+ Whether to show the taxonomy in the quick/bulk edit panel.
               * Default: $show_ui.
               */
              //'show_in_quick_edit'      => true,

              /**
               * 3.8+  Provide a callback function name for the meta box display.
               * No meta box is shown if set to false.
               * Default: null
               */
              //'meta_box_cb'         => null,

              /**
               * 3.5+  Whether to allow automatic creation of taxonomy columns on associated post-types table.
               * Default: false
               */
              //'show_admin_column'       => false,

              /**
               * Default: ''
               */
              'description' => $tax_labels['description'],

              /**
               * Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.
               * Default: false
               */
              'hierarchical' => true,

          /**
           * A function name that will be called when the count of an associated $object_type, such as post, is updated.
           * Works much like a hook.
           * Default: None - but see Note
           * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
           */
          //'update_count_callback'     => '_update_post_term_count',

              /**
               * false = disable the query_var
               * string = use custom query_var instead of default which is $taxonomy
               * Default: $taxonomy
               */
          //'query_var'           => true, // $tax_labels['slug'],

          /**
           * Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks".
           * Pass an $args array to override default URL settings for permalinks as outlined below:
           * Default: true
           */
          'rewrite' => array(

          /**
           * Used as pretty permalink text (i.e. /tag/)
           * Default: $taxonomy
           *
           * Note: if this slug matches the CPT slug, a 404 will result (POST not found)
           * setting 'tourdiaries' fails (like CPT)
           * setting 'tourdiaries/tours' fails (like CPT)
           * setting 'tours' works with:
           * /tours/east-asia/ AND /tourdiaries/east-asia/
           * @todo /tours/east-asia/russia/ BUT NOT /tourdiaries/east-asia/russia/
           * @todo #133
           */
            'slug' => $tax_labels['slug'],

            /**
             * Allows permalinks to be prepended with front base
             * Default: true
             * @src https://mondaybynoon.com/revisiting-custom-post-types-taxonomies-permalinks-slugs/
             */
            'with_front' => false,

            /**
             * 3.1+ Allow hierarchical urls
             * Default: false
             */
            'hierarchical' => true,

            /**
             * Assign an endpoint (EP) mask for this taxonomy.
             * If you do not specify the EP_MASK, pretty permalinks will not work.
             * If pretty permalinks are not enabled then endpoints are not going to work.
             * This is because endpoints rely on WordPress’s internal rewrite system
             * which is disabled for the default links.
             *
             * Endpoints make it easier to get the variable out of a URL when pretty permalinks are enabled.
             *
             * Using endpoints allows you to easily create rewrite rules to catch the normal WordPress URLs,
             * but with a little extra at the end.
             * For example, you could use an endpoint to match all post URLs followed by “gallery”
             * and display all of the images used in a post, e.g. http://example.com/my-fantastic-post/gallery/.
             *
             * Note: resave permalinks or $wp_rewrite->flush_rules() once, after the taxonomy has been created.
             *
             * Default: EP_NONE
             * @see https://make.wordpress.org/plugins/2012/06/07/rewrite-endpoints-api/
             */
            //'ep_mask'           => EP_NONE,
          ),

              /**
               * An array of the capabilities for this taxonomy.
               * manage_terms / manage_categories
               * edit_terms / manage_categories
               * delete_terms / manage_categories
               * assign_terms / edit_posts
               * Default: None
               */
              //'capabilities'        => None,

              /**
               * Whether this taxonomy should remember the order in which terms are added to objects.
               * Default: None
               */
              //'sort'            => None,

              /**
               * Whether this taxonomy is a native or "built-in" taxonomy.
               * Do not edit.
               * Default: false
               */
              //'_builtin'          => false,
        );

        register_taxonomy(
          /**
           * Taxonomy Name should only contain lowercase letters and the underscore character,
           * and not be more than 32 characters long (database structure restriction).
           * Default: None
           */
          $name,

          /**
           * Object-types can be built-in Post Type or any Custom Post Type that may be registered.
           * Default: None
           */
          $tax_labels['posttype'],

          /**
           * Optional array of Arguments.
           * Default: None
           */
          $args
        );

        // if a custom post type
        if ( $tax_labels['posttype'] !== 'post') {
          /**
           * Better be safe than sorry when registering custom taxonomies for custom post types.
           * Use register_taxonomy_for_object_type() right after the function to interconnect them.
           * Else you could run into minetraps where the post type isn't attached inside filter callback
           * that run during parse_request or pre_get_posts.
           * @see https://codex.wordpress.org/Function_Reference/register_taxonomy#Usage
           *
           * Define the taxonomy first
           * So we can piggyback the base URL
           * @see https://mondaybynoon.com/revisiting-custom-post-types-taxonomies-permalinks-slugs/
           * tours/tourname/tourday/postname
           * matching the WP Admin structure makes administration easier for clients
           */
          register_taxonomy_for_object_type(
            /**
             * The name of the taxonomy.
             * Default: None
             */
            $name,

            /**
             * A name of the object type for the taxonomy object.
             * Default: None
             */
            $tax_labels['posttype']
          );
        }
      }
    }

    /**
     * Replace Taxonomy %placeholders% in Custom Post Type permalinks
     *  as taxonomy terms do not automatically appear in Custom Post Type permalinks.
     *  The placeholder will be replaced by the hierarchical term selection (parent_term/child_term)
     *
     * @param $permalink See WordPress function options
     * @param $post See WordPress function options
     * @param $leavename See WordPress function options
     * @return $permalink
     *
     * @example
     *  // wpdtrt-dbth/library/register_post_type_tourdiaries.php
     *  'rewrite' => array(
     *    'slug' => 'tourdiaries/%tours%/%wpdtrt_tourdates_cf_daynumber%'
     *    'with_front' => false
     *  )
     *
     * @see http://shibashake.com/wordpress-theme/add-custom-taxonomy-tags-to-your-wordpress-permalinks
     * @see http://shibashake.com/wordpress-theme/custom-post-type-permalinks-part-2#conflict
     * @see https://stackoverflow.com/questions/7723457/wordpress-custom-type-permalink-containing-taxonomy-slug
     * @see https://kellenmace.com/edit-slug-button-missing-in-wordpress/
     * @see http://kb.dotherightthing.dan/php/wordpress/missing-permalink-edit-button/
     */
    public function replace_taxonomy_in_cpt_permalinks($permalink, $post, $leavename) {

      // Get post
      $post_id = $post->ID;
      $placeholder_name = $this->get_name();

      if ( strpos( $permalink, '%' . $placeholder_name . '%' ) === false ) {
        return $permalink;
      }

      // if taxonomy
      if ( taxonomy_exists( $placeholder_name ) ) {

        /**
         * Get the taxonomy terms related to the current post object
         * wp_get_object_terms() doesn't cache the results but does implement a sort order
         * get_the_terms() does cache the results but doesn't implement a sort order
         *
         * If a post only belongs to one parent, one child and/or one grandchild, you can order the terms by term_id.
         * It is widely accepted that the parent will have a lower numbered ID than the child and the child will have a * lower numbered ID than the grandchild
         * @see https://wordpress.stackexchange.com/questions/172118/get-the-term-list-by-hierarchy-order
         * This isn't true for me: East Asia is lower than China, but NZ is higher than Rainbow Road
         *
         * Returns Array of WP_Term objects on success
         * Return false if there are no terms or the post does not exist
         * Returns WP_Error on failure.
         */
        $terms = get_the_terms(
          $post_id,
          $placeholder_name
        );

        if ( is_array( $terms ) ) {

          /**
           * Sort terms into hierarchical order
           *
           * Has parent: $term->parent === n
           * No parent: $term->parent === 0
           * strnatcmp = Natural string comparison
           *
           * @see https://developer.wordpress.org/reference/functions/get_the_terms/
           * @see https://wordpress.stackexchange.com/questions/172118/get-the-term-list-by-hierarchy-order
           * @see https://stackoverflow.com/questions/1597736/how-to-sort-an-array-of-associative-arrays-by-value-of-a-given-key-in-php
           * @see https://wpseek.com/function/_get_term_hierarchy/
           * @see https://wordpress.stackexchange.com/questions/137926/sorting-attributes-order-when-using-get-the-terms
           */
          uasort ( $terms , function ( $term_a, $term_b ) {
            return strnatcmp( $term_a->parent, $term_b->parent );
            });

          /**
           * Retrieve the slug value of the first custom taxonomy object linked to the current post.
           * If no terms are retrieved, then replace our term tag with the fallback value.
           * This prevents // in permalink
           */
          $replacements = array();

          if ( !is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
              if ( !empty( $term ) && is_object( $term ) ) {
                $replacements[] = $term->slug;
              }
            }

            $replacements = implode('/', $replacements);
          }

          /**
           * Replace the %taxonomy% tag with our custom taxonomy slug.
           */
          $permalink = str_replace( ( '%' . $placeholder_name . '%' ), $replacements, $permalink);
        }
      }

      return $permalink;
    }

    // END REGISTERERS
  }
}