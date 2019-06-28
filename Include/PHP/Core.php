<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 * 
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       #
 * @since      1.0.0
 * @package    spartan-nash_social-center
 * @subpackage spartan-nash_social-center/Include
 * @author     # <#>
*/

class SpartanNash_SocialCenter {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      SpartanNash_SocialCenter_Loader    $loader    Maintains and registers all hooks for the plugin.
	*/
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	*/
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	*/
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	*/
	public function __construct() {
		if ( defined( 'SpartanNash_SocialCenter_Version' ) ) {
			$this->version = SpartanNash_SocialCenter_Version;
		} else {
			$this->version = 'Unknown';
		}
		$this->plugin_name = 'SpartanNash.SocialCenter';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_To_Fb_Post_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_To_Fb_Post_i18n. Defines internationalization functionality.
	 * - Wp_To_Fb_Post_Admin. Defines all hooks for the admin area.
	 * - Wp_To_Fb_Post_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	*/
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		*/
		require_once plugin_dir_path( __FILE__  ) . 'Loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		*/
		require_once plugin_dir_path( __FILE__ ) . 'Internationalization.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		*/
		require_once plugin_dir_path( __FILE__ ) . 'Admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		*/
		require_once plugin_dir_path( __FILE__ ) . 'Public.php';

		// The following file is what actually runs the posting to the Facebook Graph API.
		require plugin_dir_path( __FILE__ ) . 'Facebook.Share.php';

		$this->loader = new SpartanNash_SocialCenter_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_To_Fb_Post_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	*/
	private function set_locale() {
		/*$plugin_i18n = new SpartanNash_SocialCenter_Internationalization();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );*/
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	*/
	private function define_admin_hooks() {

		$plugin_admin = new SpartanNash_SocialCenter_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	*/
	private function define_public_hooks() {
		
		$plugin_public = new SpartanNash_SocialCenter_Public( $this->get_plugin_name(), $this->get_version() );
		$Class_FacebookShare = new SpartanNash_SocialCenter_FacebookShare();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// The following filter transforms the post content into the customization form.
		// This happens when viewing the post with the proper 'Get' variables set.
		$this->loader->add_filter('single_template',$plugin_public, 'Filter_PostCompose');

		// The following filter transforms the post content when viewing the post before customizing.
		// It adds the customization button to the post content, and also shows the account page when requested.
		$this->loader->add_filter( 'the_content',$plugin_public,'Filter_PostSelect');

		// The following line likely does not work, as there is no "the_excerpt_more" hook listed in the WP Codex.
		// It appears to be an attempt to put the customization button on a page where post excerpt are shown, but I cant find that page.
		// Just confirmed with Josh there is no blog-style view of the posts, so disabling this for now.
		//$this->loader->add_filter( 'the_excerpt_more',$plugin_public, 'add_btn_compose_post_excerpt_more' );

		// This is where the authorization and page picking logic lives.
		$this->loader->add_action( 'init',$plugin_public, 'Action_FacebookAuthorize' );

		// This is where the logic that actually shares to facebook lives.
		$this->loader->add_action( 'init' , $Class_FacebookShare , 'Action_FacebookShare' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	*/
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	*/
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    SpartanNash_SocialCenter_Loader    Orchestrates the hooks of the plugin.
	*/
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	*/
	public function get_version() {
		return $this->version;
	}
}