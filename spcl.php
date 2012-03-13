<?php
/*
Plugin Name: Save Post. Check Links.
Description: Bei der Speicherung eines Artikels prüft das Plugin die im Text vorhandenen Verlinkungen auf ihre Erreichbarkeit bzw. Gültigkeit.
Author: Sergej M&uuml;ller
Author URI: http://wpseo.de
Plugin URI: https://plus.google.com/110569673423509816572/posts/hDtKSyEozeR
Version: 0.5.1
*/


/* Sicherheitsabfrage */
if ( !class_exists('WP') ) {
	die();
}


/**
* SPCL
*/

final class SPCL {
	

	/**
	* Initiator der Klasse
	*
	* @since   0.1
	* @change  0.3
	*/

  	public static function init()
  	{
		/* Filter */
		if ( (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) or (defined('DOING_CRON') && DOING_CRON) or (defined('DOING_AJAX') && DOING_AJAX) or (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) ) {
			return;
		}
		
		/* Actions */
		add_action(
			'save_post',
			array(
				__CLASS__,
				'check_post_links'
			)	
		);
		add_action(
			'admin_head-post.php',
			array(
				__CLASS__,
				'handle_admin_notices'
			)
		);
	}
	
	
	/**
	* Proxy für die Adminnotiz
	*
	* @since   0.2
	* @change  0.2
	*
	*/
	
	public static function handle_admin_notices() {
		/* Leer? */
		if ( empty($_GET['message']) ) {
			return;
		}
		
		/* Ausführen */
		add_action(
			'admin_notices',
			array(
				__CLASS__,
				'show_admin_notices'
			)
		);
	}
	

  	/**
	* Prüfung der Links
	*
	* @since   0.1
	* @change  0.5.1
	*
	* @param   intval  $id  ID des Beitrags
	*/

	public static function check_post_links($id)
	{
		/* Keine ID? */
		if ( empty($id) ) {
			return;
		}
		
		/* Post */
		$post = get_post($id);
		
		/* Leer? */
		if ( empty($post) or empty($post->post_content) ) {
			return;
		}
		
		/* Links suchen */
		preg_match_all(
			'/<a [^>]*href=["\'](http(?:.+?))["\']/i',
			$post->post_content,
			$out
		);
		
		/* Leer? */
		if ( empty($out[1]) ) {
			return;
		}
		
		/* Init */
		$found = array();
		
		/* Loopen */
		foreach ( $out[1] as $url ) {
			/* Fragment */
			if ( $hash = parse_url($url, PHP_URL_FRAGMENT) ) {
				$url = str_replace('#' .$hash, '', $url);
			}
			
			/* Säubern */
			$url = esc_url_raw($url);
			
			/* Pingen */
			$response = wp_remote_head($url);
			
			/* Fehler? */
			if ( is_wp_error($response) ) {
				$found[] = array(
					'url'	=> $url,
					'error' => esc_html($response->get_error_message())
				);
			
			/* Status Code */
			} else {
				/* Code */
				$code = (int)wp_remote_retrieve_response_code($response);
				
				/* Abfragen */
				if ( $code >= 400 && $code != 405 ) { 
					$found[] = array(
						'url'	=> $url,
						'error' => sprintf(
							'Status Code <a href="http://de.wikipedia.org/wiki/HTTP-Statuscode" target="_blank">%s</a>',
							$code
						)
					);
				}
			}
		}
		
		/* Leer? */
		if ( empty($found) ) {
			return;
		}
		
		/* Speichern */
		set_transient(
			self::get_transient_hash(),
			$found,
			60*30
		);
	}


  	/**
	* Anzeige des Resultats
	*
	* @since   0.1
	* @change  0.3
	*
	*/

	public static function show_admin_notices()
	{
		/* Init */
		$hash = self::get_transient_hash();
		
		/* Auslesen */
		if ( (!$items = get_transient($hash)) or !is_array($items) ) {
			return;
		}

		/* Löschen */
		delete_transient($hash);
		
		/* Ausgabe starten */
		echo '<div id="message" class="updated"><p><strong>Nicht erreichbare Links</strong></p><ul>';
		
		/* Loop */
		foreach ( $items as $item ) {
			echo sprintf(
				'<li><a href="%1$s" target="_blank">%1$s</a> (%2$s)</li>',
				$item['url'],
				$item['error']
				
			);
		}
		
		/* Ausgabe beenden */
		echo '</ul></div>';
	}
	
	
	/**
	* Generierung eines Hash-Wertes
	*
	* @since   0.1
	* @change  0.3
	*
	* @return  string  Hash-Wert
	*/
	
	private static function get_transient_hash() {
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