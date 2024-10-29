<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class BBPress_Improvements_for_Yoast {

	/**
	 * The single instance of BBPress_Improvements_for_Yoast.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'bbpress_improvements_for_yoast';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		// check for plugin using plugin name
		if( !is_plugin_active( 'wordpress-seo/wp-seo.php' ) || !is_plugin_active( 'bbpress/bbpress.php' ) ) {
			// bbpress or wordpress-seo not installed, we do nothing.
			return;
		} 
		
		// Handle localisation
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
		add_filter( 'wpseo_metadesc', array( $this, 'bbp_process_wpseo_meta_description' ),99 );
		add_filter( 'wpseo_title', array( $this, 'bbp_process_wpseo_meta_title'), 99 );
		add_filter( 'wpseo_canonical', array( $this, 'bbp_process_wpseo_canonical'), 99 );
	
	} // End __construct ()
	/**
	 * process the yoast meta title in bbpress. we know in bbpress, When installed yoast seo, canonical of single forum, single topic, user profile, topic tag lost
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  correct meta title for user profile
	 */
	public function bbp_process_wpseo_canonical( $canonical ) {
		global $wp_query,$wp_rewrite;
		# View single forum
		if( function_exists('bbp_is_single_forum') && bbp_is_single_forum() && get_query_var( 'paged' ) > 1){
			if ( ! $wp_rewrite->using_permalinks() ) {
				$canonical = add_query_arg( 'paged', get_query_var( 'paged' ), $canonical );
			}
			else {
				$canonical = user_trailingslashit( trailingslashit( $canonical ) . trailingslashit( $wp_rewrite->pagination_base ) . get_query_var( 'paged' ) );
			}
		}
		# View single topic
		if( function_exists('bbp_is_single_topic') && bbp_is_single_topic() && get_query_var( 'paged' ) > 1 ){
			if ( ! $wp_rewrite->using_permalinks() ) {
				$canonical = add_query_arg( 'paged', get_query_var( 'paged' ), $canonical );
			}
			else {
				$canonical = user_trailingslashit( trailingslashit( $canonical ) . trailingslashit( $wp_rewrite->pagination_base ) . get_query_var( 'paged' ) );
			}
		}
		# View user profile
		if( function_exists('bbp_is_single_user') && bbp_is_single_user() ){
			$canonical = bbp_get_user_profile_url();
		}
		# topic tag
		if( function_exists('bbp_is_topic_tag') && bbp_is_topic_tag() ){
			$canonical = bbp_get_topic_tag_link();
		}
		
		return $canonical;
	}
	
	/**
	 * process the yoast meta title in bbpress. we know in bbpress, When installed yoast seo, meta title of single user, topic tag lost
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  correct meta title for user profile
	 */
	public function bbp_process_wpseo_meta_title( $title ) {
		# View user profile
		if( function_exists('bbp_is_single_user') && bbp_is_single_user() ){
			return sprintf( esc_attr__( "%s's Profile - %s", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ), get_bloginfo( 'name' ));
		}
		# View topic tag
		if( function_exists('bbp_is_topic_tag') && bbp_is_topic_tag() ){
			return sprintf( __( 'Topic Tag: %s - %s', 'bbpress' ), bbp_get_topic_tag_name(),get_bloginfo( 'name' ) );
		}
		return $title;
	}
	
	/**
	 * process the yoast meta description in bbpress. we know in bbpress, When installed yoast seo, meta description of topic archive, user profile, topic tag, single topic lost
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  correct description for topic, profile page etc.
	 */
	public function bbp_process_wpseo_meta_description( $description ) {
		global $post;
		
		# topic archive
		if( function_exists('bbp_is_topic_archive') && bbp_is_topic_archive() ){
			return __('Forum topics on ', 'bbp-improvements-for-yoast'). get_bloginfo( 'name' );
		}
		# user profile
		if( function_exists('bbp_is_single_user') && bbp_is_single_user() ){
			return sprintf( esc_attr__( "%s's Profile on %s", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ), get_bloginfo( 'name' ));
		}
		# topic tag
		if( function_exists('bbp_is_topic_tag') && bbp_is_topic_tag() ){
			return sprintf( __( 'Topic Tag: %s on %s', 'bbpress' ), bbp_get_topic_tag_name(),get_bloginfo( 'name' ) );
		}
		# single topic
		if( function_exists('bbp_is_single_topic') && bbp_is_single_topic() ){
			$topic_content = trim( strip_tags( bbp_get_topic_content() ) );
			if ( !empty( $topic_content ) ) {
				return $this->get_excerpt($topic_content ) .' - ' . get_bloginfo( 'name' );
			}	
		}
		# single forum
		if( function_exists('bbp_is_single_forum') && bbp_is_single_forum() ){
			return $this->get_excerpt( bbp_get_forum_content() ).' - ' . get_bloginfo( 'name' );
		}
		
		return $description;
	}
	/** 
	* get excerpt from content
	*
	* @access  public
    * @since   1.0.0
    * @return  excerpt with specific length
	*/
	public function get_excerpt( $content = '' , $len = 200 ) {
		$excerpt = '';
		if( $content == '' ) return '';
			$excerpt = $content;

		$len++;
		if ( mb_strlen( $excerpt ) > $len ) {
			$subex = mb_substr( $excerpt, 0, $len - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if ( $excut < 0 ) {
				$excerpt = mb_substr( $subex, 0, $excut );
			} else {
				$excerpt = $subex;
			}
		}
		
		return trim($excerpt);
	}
	
	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'bbp-improvements-for-yoast', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Main BBPress_Improvements_for_Yoast Instance
	 *
	 * Ensures only one instance of BBPress_Improvements_for_Yoast is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see BBPress_Improvements_for_Yoast()
	 * @return Main BBPress_Improvements_for_Yoast instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()
}