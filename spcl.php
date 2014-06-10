<?php
/*
Plugin Name: Save Post. Check Links.
Description: Bei der Speicherung der Artikel prüft das Plugin die im Text vorhandenen Verlinkungen auf ihre Erreichbarkeit bzw. Gültigkeit.
Author: Sergej M&uuml;ller
Author URI: http://wpcoder.de
Plugin URI: https://plus.google.com/110569673423509816572/posts/hDtKSyEozeR
License: GPLv2 or later
Version: 0.6.0
*/

/*
Copyright (C)  2011-2014 Sergej Müller

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


/* Quit */
defined('ABSPATH') OR exit;


/**
* SPCL
*/

final class SPCL {


	/**
	* Initiator der Klasse
	*
	* @since   0.1
	* @change  0.6.0
	*/

  	public static function init()
  	{
		/* Come out */
		if ( (! is_admin()) OR (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) OR (defined('DOING_CRON') && DOING_CRON) OR (defined('DOING_AJAX') && DOING_AJAX) OR (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) ) {
			return;
		}

		/* Actions */
		add_action(
			'save_post',
			array(
				__CLASS__,
				'validate_links_for_post'
			)
		);
		add_action(
			'admin_notices',
			array(
				__CLASS__,
				'display_validation_errors'
			),
			99
		);
	}


  	/**
	* Validate post links
	*
	* @since   0.1.0
	* @change  0.5.1
	*
	* @param   intval  $id  Post ID
	*/

	public static function validate_links_for_post($id)
	{
		/* No PostID? */
		if ( empty($id) ) {
			return;
		}

		/* Get post data */
		$post = get_post($id);

		/* Post incomplete? */
		if ( empty($post) OR empty($post->post_content) ) {
			return;
		}

		/* Extract urls */
		if ( ! $urls = wp_extract_urls($post->post_content) ) {
			return;
		}

		/* Init */
		$found = array();

		/* Loop the urls */
		foreach ( $urls as $url ) {
			/* Fragment */
			if ( $hash = parse_url($url, PHP_URL_FRAGMENT) ) {
				$url = str_replace('#' .$hash, '', $url);
			}

			/* Ping */
			$response = wp_safe_remote_head($url);

			/* Error? */
			if ( is_wp_error($response) ) {
				$found[] = array(
					'url'	=> $url,
					'error' => $response->get_error_message()
				);

			/* Respronse code */
			} else {
				/* Status code */
				$code = (int)wp_remote_retrieve_response_code($response);

				/* Handle error codes */
				if ( $code >= 400 && $code != 405 ) {
					$found[] = array(
						'url'	=> $url,
						'error' => sprintf(
							'Status Code %d',
							$code
						)
					);
				}
			}
		}

		/* No items? */
		if ( empty($found) ) {
			return;
		}

		/* Cache the result */
		set_transient(
			self::_transient_hash(),
			$found,
			60*30
		);
	}


  	/**
	* Output of validation errors
	*
	* @since   0.1.0
	* @change  0.6.0
	*
	*/

	public static function display_validation_errors()
	{
		/* Check for accessibility */
		if ( empty($GLOBALS['pagenow']) OR empty($_GET['message']) OR $GLOBALS['pagenow'] !== 'post.php' ) {
			return;
		}

		/* Cache hash */
		$hash = self::_transient_hash();

		/* Get errors from cache */
		if ( (! $items = get_transient($hash)) OR (! is_array($items)) ) {
			return;
		}

		/* Kill current cache */
		delete_transient($hash);

		/* Output start */
		echo '<div class="error"><ul>';

		/* Loop the cache items */
		foreach ( $items as $item ) {
			echo sprintf(
				'<li><a href="%1$s" target="_blank">%1$s</a> (%2$s)</li>',
				esc_url($item['url']),
				esc_html($item['error'])

			);
		}

		/* Output end */
		echo '</ul></div>';
	}


	/**
	* Create transient hash based on post and user IDs
	*
	* @since   0.1.0
	* @change  0.6.0
	*
	* @return  string  Transient hash
	*/

	private static function _transient_hash() {
		return md5(
			sprintf(
				'SPCL_%s_%s',
				get_the_ID(),
				wp_get_current_user()->ID
			)
		);
	}
}


/* Fire */
add_action(
	'admin_init',
	array(
		'SPCL',
		'init'
	),
	99
);