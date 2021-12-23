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

		$subject = apply_filters( 'how_many_posts_email_subject', esc_html__( 'The number of posts published this week!', 'email-notifications-for-wp-ulike' ) );
		$send_to = apply_filters( 'how_many_posts_email_receipent', get_option( 'admin_email' ) );

		$message = apply_filters( 'how_many_posts_email_message', $this->get_counts() . ' posts were published this past week.' );

		$sent = wp_mail( $send_to, $subject, $message );

		return $sent;
	}

	/**
	 * Get the post count for the past week.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function get_counts() {

		$args = array(
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'date_query'     => array(
				array( 'after' => '1 week ago' ),
			),
		);

		$result = new \WP_Query( $args );

		return (int) $result->found_posts;
	}
}
