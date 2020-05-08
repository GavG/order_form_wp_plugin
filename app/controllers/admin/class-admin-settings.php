<?php
namespace Order_Form\App\Controllers\Admin;

use Order_Form\App\Controllers\Admin\Base_Controller;
use Order_Form;

if ( ! class_exists( __NAMESPACE__ . '\\' . 'Admin_Settings' ) ) {

	/**
	 * Controller class that implements Plugin Admin Settings configurations
	 *
	 * @since      1.0.0
	 * @package    Order_Form
	 * @subpackage Order_Form/controllers/admin
	 */
	class Admin_Settings extends Base_Controller {

		/**
		 * Holds suffix for dynamic add_action called on settings page.
		 *
		 * @var string
		 * @since 1.0.0
		 */
		private static $hook_suffix = 'settings_page_' . Order_Form::PLUGIN_ID;

		/**
		 * Slug of the Settings Page
		 *
		 * @since    1.0.0
		 */
		const SETTINGS_PAGE_SLUG = Order_Form::PLUGIN_ID;

		/**
		 * Capability required to access settings page
		 *
		 * @since 1.0.0
		 */
		const REQUIRED_CAPABILITY = 'manage_options';

		/**
		 * Register callbacks for actions and filters
		 *
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {
			// Create Menu.
			add_action( 'admin_menu', array( $this, 'plugin_menu' ) );

			// Enqueue Styles & Scripts.
			add_action( 'admin_print_scripts-' . static::$hook_suffix, array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_print_styles-' . static::$hook_suffix, array( $this, 'enqueue_styles' ) );

			// Register Fields.
			add_action( 'load-' . static::$hook_suffix, array( $this, 'register_fields' ) );

			// Register Settings.
			add_action( 'admin_init', array( $this->get_model(), 'register_settings' ) );

			// Settings Link on Plugin's Page.
			add_filter(
				'plugin_action_links_' . Order_Form::PLUGIN_ID . '/' . Order_Form::PLUGIN_ID . '.php',
				array( $this, 'add_plugin_action_links' )
			);
		}

		/**
		 * Create menu for Plugin inside Settings menu
		 *
		 * @since    1.0.0
		 */
		public function plugin_menu() {
			// @codingStandardsIgnoreStart.
			static::$hook_suffix = add_options_page(
				__( Order_Form::ORDER_FORM, Order_Form::PLUGIN_ID ),        // Page Title.
				__( Order_Form::ORDER_FORM, Order_Form::PLUGIN_ID ),        // Menu Title.
				static::REQUIRED_CAPABILITY,           // Capability.
				static::SETTINGS_PAGE_SLUG,             // Menu URL.
				array( $this, 'markup_settings_page' ) // Callback.
			);
			// @codingStandardsIgnoreEnd.
		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {

			/**
			 * This function is provided for demonstration purposes only.
			 */

			wp_enqueue_script(
				Order_Form::PLUGIN_ID . '_admin-js',
				Order_Form::get_plugin_url() . 'assets/js/admin/order-form.js',
				array( 'jquery' ),
				Order_Form::PLUGIN_VERSION,
				true
			);
		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {

			/**
			 * This function is provided for demonstration purposes only.
			 */

			wp_enqueue_style(
				Order_Form::PLUGIN_ID . '_admin-css',
				Order_Form::get_plugin_url() . 'assets/css/admin/order-form.css',
				array(),
				Order_Form::PLUGIN_VERSION,
				'all'
			);
		}

		/**
		 * Creates the markup for the Settings page
		 *
		 * @since    1.0.0
		 */
		public function markup_settings_page() {
			if ( current_user_can( static::REQUIRED_CAPABILITY ) ) {
				$this->view->admin_settings_page(
					array(
						'page_title'    => Order_Form::ORDER_FORM,
						'settings_name' => $this->get_model()->get_plugin_settings_option_key(),
					)
				);
			} else {
				wp_die( __( 'Access denied.' ) ); // WPCS: XSS OK.
			}
		}

		/**
		 * Registers settings sections and fields
		 *
		 * @since    1.0.0
		 */
		public function register_fields() {

			// Add Settings Page Section.
			add_settings_section(
				'order_form_section',                    // Section ID.
				__( 'Settings', Order_Form::PLUGIN_ID ), // Section Title.
				array( $this, 'markup_section_headers' ), // Section Callback.
				static::SETTINGS_PAGE_SLUG                 // Page URL.
			);

			// Add Settings Page Field.
			add_settings_field(
				'order_form_field',                                // Field ID.
				__( 'Order Form Field:', Order_Form::PLUGIN_ID ), // Field Title.
				array( $this, 'markup_fields' ),                    // Field Callback.
				static::SETTINGS_PAGE_SLUG,                          // Page.
				'order_form_section',                              // Section ID.
				array(                                              // Field args.
					'id'        => 'order_form_field',
					'label_for' => 'order_form_field',
				)
			);
		}

		/**
		 * Adds the section introduction text to the Settings page
		 *
		 * @param array $section Array containing information Section Id, Section
		 *                       Title & Section Callback.
		 *
		 * @since    1.0.0
		 */
		public function markup_section_headers( $section ) {
			$this->view->section_headers(
				array(
					'section'      => $section,
					'text_example' => __( 'This is a text example for section header', Order_Form::PLUGIN_ID ),
				)
			);
		}

		/**
		 * Delivers the markup for settings fields
		 *
		 * @param array $field_args Field arguments passed in `add_settings_field`
		 *                          function.
		 *
		 * @since    1.0.0
		 */
		public function markup_fields( $field_args ) {
			$field_id = $field_args['id'];
			$settings_value = $this->get_model()->get_setting( $field_id );
			$this->view->markup_fields(
				array(
					'field_id'       => esc_attr( $field_id ),
					'settings_name'  => $this->get_model()->get_plugin_settings_option_key(),
					'settings_value' => ! empty( $settings_value ) ? esc_attr( $settings_value ) : '',
				)
			);
		}

		/**
		 * Adds links to the plugin's action link section on the Plugins page
		 *
		 * @param array $links The links currently mapped to the plugin.
		 * @return array
		 *
		 * @since    1.0.0
		 */
		public function add_plugin_action_links( $links ) {
			$settings_link = '<a href="options-general.php?page=' . static::SETTINGS_PAGE_SLUG . '">' . __( 'Settings', Order_Form::PLUGIN_ID ) . '</a>';
			array_unshift( $links, $settings_link );

			return $links;
		}

	}

}
