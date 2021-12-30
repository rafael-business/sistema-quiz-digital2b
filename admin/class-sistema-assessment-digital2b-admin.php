<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    sistema_assessment_digital2b
 * @subpackage sistema_assessment_digital2b/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    sistema_assessment_digital2b
 * @subpackage sistema_assessment_digital2b/admin
 * @author     Your Name <email@example.com>
 */
class sistema_assessment_digital2b_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $sistema_assessment_digital2b    The ID of this plugin.
	 */
	private $sistema_assessment_digital2b;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $sistema_assessment_digital2b       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $sistema_assessment_digital2b, $version ) {

		$this->sistema_assessment_digital2b = $sistema_assessment_digital2b;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in sistema_assessment_digital2b_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The sistema_assessment_digital2b_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->sistema_assessment_digital2b, plugin_dir_url( __FILE__ ) . 'css/sistema-assessment-digital2b-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in sistema_assessment_digital2b_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The sistema_assessment_digital2b_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		 
		wp_enqueue_script( 'admin-sweet-alert', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'sistema-assessment-digital2b-admin', plugin_dir_url( __FILE__ ) . 'js/sistema-assessment-digital2b-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script('sistema-assessment-digital2b-admin', 'digital2b_scripts', array(
			'ajax' => admin_url('admin-ajax.php'),
			'postID' => get_the_ID(),
			)
		);

	}

	public function remove_modulo_taxonomy_assessments() {

		$custom_taxonomy_slug = 'modulo_pergunta';
		$custom_post_type = 'perguntas';
		
		remove_meta_box( 'tagsdiv-'.$custom_taxonomy_slug, $custom_post_type, 'side' );
	}

}
