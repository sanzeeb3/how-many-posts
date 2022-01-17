<?php

namespace HowManyPosts;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin Class.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize.
	 */
	public function init() {

		add_action( 'admin_init', array( $this, 'schedule' ) );
		add_action( 'how_many_posts_weekly_email', array( $this, 'send' ) );
	}

	/**
	 * Schedule weekly email.
	 *
	 * @since 1.0.0
	 *
	 * @return integer|void The actions'd ID or void.
	 */
	public function schedule() {

		if ( false === as_next_scheduled_action( 'how_many_posts_weekly_email' ) ) {
			as_schedule_recurring_action( strtotime( '+ 7 days' ), WEEK_IN_SECONDS, 'how_many_posts_weekly_email', array(), 'how_many_posts' );
		}
	}

	/**
	 * Initiate email sending.
	 *
	 * @since 1.0.0
	 *
	 * @return bool.
	 */
	public function send() {

		$subject = apply_filters( 'how_many_posts_email_subject', 'How Many Posts? Weekly Summary!' );
		$send_to = apply_filters( 'how_many_posts_email_receipent', get_option( 'admin_email' ) );

		$message = apply_filters( 'how_many_posts_email_message', $this->get_message() );

		if ( defined( 'WC_VERSION' ) ) {

			// Get woocommerce mailer from instance.
			$mailer = \WC()->mailer();

			// Header Title.
			$heading = \get_bloginfo( 'name' );

			// Wrap message using woocommerce html email template.
			$wrapped_message = $mailer->wrap_message( $heading, $message );

			$wc_email = new \WC_Email();

			// Style the wrapped message with woocommerce inline styles.
			$message = $wc_email->style_inline( $wrapped_message );

			$sent = $mailer->send( $send_to, $subject, $message );

		} elseif ( function_exists( 'wpforms' ) ) {

			$emails = new \WPForms_WP_Emails();
			$sent   = $emails->send( $send_to, $subject, $message );

		} else {

			$sent = wp_mail( $send_to, $subject, $message );
		}//end if

		return $sent;
	}

	/**
	 * Get the message for the email.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function get_message() {

		$args = array(
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'date_query'     => array(
				array(
					'after' => '1 week ago',
				),
			),
		);

		$result = new \WP_Query( $args );

		$html = 'Oh, hi there.';
		$html .= '<br/><br/>';
		$html .= (int) $result->found_posts . ' posts were published this past week.';

		if ( $result->have_posts() ) {
			while( $result->have_posts() ) {
				$result->the_post();

				$html .= '<li><a href='. esc_url( get_the_permalink() ) .'> ' . wp_kses_post( get_the_title() ) . ' </a></li>';
			}
		}

		return $html;
	}
}
