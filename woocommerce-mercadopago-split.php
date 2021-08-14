<?php

/**
 * Plugin Name: Mercado Pago Split (WooCommerce + WCFM)
 * Plugin URI: https://github.com/yosietserga/mercadopago-wcfm
 * Description: Configure the payment options and accept payments with cards, ticket and money of Mercado Pago account.
 * Version: 1.0.9
 * Author: Yosiet Serga (Necoyoad)
 * Author URI: https://developers.mercadopago.com/
 * Text Domain: woocommerce-mercadopago-split
 * Domain Path: /i18n/languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.7.0
 * @package MercadoPago
 * @category Core
 * @author Yosiet Serga (Necoyoad)
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WC_MERCADOPAGO_SPLT_BASENAME')) {
    define('WC_MERCADOPAGO_SPLT_BASENAME', plugin_basename(__FILE__));
}

if (!function_exists('is_plugin_active')) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if (!class_exists('WC_WooMercadoPagoSplit_Init')) {
    include_once dirname(__FILE__) . '/includes/module/WC_WooMercadoPagoSplit_Init.php';

    register_activation_hook(__FILE__, array('WC_WooMercadoPagoSplit_Init', 'mercadopago_plugin_activation'));
    add_action('plugins_loaded', array('WC_WooMercadoPagoSplit_Init', 'woocommerce_mercadopago_init'));
}







/**
 * prueba r'apida de las credenciales
 * 
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/users/test_user');
 curl_setopt($ch, CURLOPT_HTTPHEADER, array(
 	'Content-Type: application/json',
 	'Authorization: Bearer APP_USR-689232386237439-121601-5bfa83dc93c9f20756f22d1abac1263d-135168024',
 ));
 curl_setopt($ch, CURLOPT_POST, true);
 curl_setopt($ch, CURLOPT_POSTFIELDS, '{"site_id":"MLA"}');
 $output = curl_exec($ch);
 curl_close($ch);
 print_r($output);
 exit;
 */




if ( !is_admin() ) {

	////////////////////////////////////////////////////
	// SHORTCODE PARA VINCULAR CUENTAS
	function wcmps_integrar_cuentas ($return=false) {
		global $_GET;
		$tpl = <<<INTEGRAR_CUENTAS
		<style type="text/css">
			#mp-box {
				max-width: 90%;
				width: 500px;
				margin: auto;
				padding: 30px;
				background: #F0F7F6;
			}
			#mp-box a {
			    background-color: #ffde08;
			    color: #0800b3;
			    font-weight: bold;
			    text-shadow: 2px 2px 0 #000;
			    box-shadow: 4px 4px 0 1px;
			    text-shadow: 0 0 10px #746713;
			    transform: all 0.3s;
			}
			#mp-box a:active {
			    box-shadow: none;
			    margin-top: 4px;
			    margin-left: 4px;
			}
			#mp-box.existing p {
				margin-bottom: 0;
			}
			#desv-box {
				max-width: 90%;
				width: 500px;
				margin: auto;
				padding: 30px;
				line-height: 1.2;
				color: gray;
			}
			#desv-box * {
				line-height: 1.2;
			}
		</style>
		INTEGRAR_CUENTAS;

		$href = wcmps_auth_url();
		if ( isset($_GET['code']) && !empty($_GET['code']) && (int)get_current_user_id() > 0 ) {
			if ( base64_decode( $_GET['state'] ) == get_current_user_id() ) {
				update_user_meta( get_current_user_id(), 'wcmps_authcode', $_GET['code'] );
				if ( wcmps_vendedor() ) {
				} elseif ( strlen( get_user_meta( get_current_user_id(), 'wcmps_authcode', true ) ) == 0 || strlen( $cred->refresh_token ) == 0 ) {
					$tpl .= '<ul class="woocommerce-error" role="alert">
						<li>
							Error al intentar verificar su cuenta con Mercado Pago. Puede ser temporal, pero si el problema persiste, verifique si su cuenta está vinculada.
						</li>
						<li>
							<a href="https://appstore.mercadolibre.com.ar/apps/permissions" target="_blank">Comprobar en MercadoPago</a>
						</li>
						<li>
							<a href="'.$href.'">Renovar vínculo</a>
						</li>
					</ul>';
				}
			} else {
				$tpl .= '<ul class="woocommerce-error" role="alert">
					<li>
						Error de usuario. Comprueba el usuario que ha iniciado sesión e inténtalo de nuevo.
					</li>
				</ul>';
			}
		} else if ( (int)get_current_user_id() == 0 ) {
			echo '<div class="message error">Inicie sesión antes de intentar vincular la cuenta.</div>';
		}

		if ( (int)get_current_user_id() > 0 ) {
			$authcode = get_user_meta( get_current_user_id(), 'wcmps_authcode', true );
			$cred = json_decode( get_user_meta( get_current_user_id(), 'wcmps_credentials', true ) );
			if ( strlen($authcode) == 0 || strlen($cred->refresh_token) == 0 ) {
				$tpl .= '<div id="mp-box">
					<p style="font-size:90%;">Para que pueda recibir los pagos realizados en el marketplace, debe vincular su cuenta de Mercado Pago a nuestra tienda. Haga clic en el botón de abajo para vincular.</p>
					<a href="'. $href .'" class="button">Vincular cuenta de Mercado Pago</a>
				</div>';
			} else {
				$tpl .= '<div id="mp-box" class="existing">
					<p style="font-size:90%;">¡Felicitaciones! su cuenta está vinculada, por seguridad, inicie sesión en <a href="https://appstore.mercadolibre.com.ar/apps/permissions" target="_blank">aplicaciones conectadas</a> en tu cuenta de Mercado Pago para confirmar.</p>
				</div>
				<div id="desv-box">
					<p><small><b>Desvincular cuenta de Mercado Pago</b><br>NOTA: si desvincula su cuenta, no podrá recibir sus pagos para compras futuras.</small></p>
				</div>';
			}
		} else {
			$tpl .= '<p>Inicie sesión para acceder a esta página.</p>';
		}

		if ($return) return $tpl;
		else echo $tpl;
	}
	add_shortcode( 'wcmps_integrar_cuentas', 'wcmps_integrar_cuentas' );


	////////////////////////////////////////////////////
	// URL DE MERCADO PAGO PARA VINCULAR CUENTAS
	function wcmps_auth_url () {
		$meta = get_option('woocommerce_woo-mercado-pago-split-basic_settings');
		$url = 'https://auth.mercadopago.com.ar/authorization?client_id='.$meta['_mp_appid'].'&response_type=code&platform_id=mp&state='.base64_encode(get_current_user_id()).'&redirect_uri=' . urlencode( $meta['_mp_returnurl'] );
		return $url;
	}
	
	////////////////////////////////////////////////////
	// DATOS DEL VENDEDOR DE MERCADO PAGO
	function wcmps_vendedor () {

		$meta = get_option('woocommerce_woo-mercado-pago-split-basic_settings');
		
		if ( $meta['checkout_credential_prod'] == 'no' ) {
			$token = $meta['_mp_access_token_test'];
		} else {
			$token = $meta['_mp_access_token_prod'];
		}
		$dt = 'client_secret='.$token.'&grant_type=authorization_code&code='.get_user_meta( get_current_user_id(), 'wcmps_authcode', true ).'&redirect_uri='.urlencode( $meta['_mp_returnurl'] );
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/oauth/token');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'content-type: application/x-www-form-urlencoded',
			'accept: application/json',
		));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dt);

		$output = curl_exec($ch);
		curl_close($ch);
		$_output = json_decode($output);
		if ( !$_output->error ) {
			update_user_meta( get_current_user_id(), 'wcmps_credentials', $output );
			update_user_meta( get_current_user_id(), 'wcmps_refreshed', date('Ymd') );
			return true;
		} elseif ( $_output->status == 400 ) {
			// el error del mercado libre no está claro, pero la mayoría de las veces es el resultado de que el enlace está activo
			return true;
		} else {
			return false;
		}
	}


	////////////////////////////////////////////////////
	// RENOVAR DATOS DEL VENDEDOR DE MERCADO PAGO
	function wcmps_renovar_cron () {
		global $wpdb;
		$meta = get_option('woocommerce_woo-mercado-pago-split-basic_settings');
		if ( $meta['checkout_credential_prod'] == 'no' ) {
			$token = $meta['_mp_access_token_test'];
		} else {
			$token = $meta['_mp_access_token_prod'];
		}
		$hoje = date( 'Ymd' );
		$limite = date( 'Ymd', strtotime( date('Y-m-d H:i:s')." - 1 month" ) );
		$res = $wpdb->get_results( 'select user_id from '.$wpdb->prefix.'usermeta where meta_key = "wcmps_refreshed" and meta_value <= '.$limite, ARRAY_A );
		for ( $i=0; $i<count($res); $i++ ) {
			wcmps_renovar( $res[$i]['user_id'], $meta, $token );
		}
	}
	add_action( 'wcmps_renovar_cron', 'wcmps_renovar_cron' );

	function wcmps_renovar ( $uid, $meta, $token ) {

		$um = get_user_meta( $uid, 'wcmps_credentials', true );
		$um = json_decode($um);
		$rtoken = json_decode( get_user_meta( $uid, 'wcmps_credentials', true ) );
		$dt = 'client_secret='.$token.'&grant_type=refresh_token&refresh_token='.$rtoken->refresh_token;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/oauth/token');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'content-type: application/x-www-form-urlencoded',
			'accept: application/json',
		));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dt);

		$output = curl_exec($ch);
		curl_close($ch);

		$_output = json_decode($output);
		if ( !$_output->error ) {
			update_user_meta( $uid, 'wcmps_credentials', $output );
			update_user_meta( $uid, 'wcmps_refreshed', date('Ymd') );
			return true;
		} else {
			return false;
		}

	}

	function wcmps_start_cron() {
		if( !wp_next_scheduled( 'wcmps_renovar_cron' ) ) {  
		   wp_schedule_event( time(), 'daily', 'wcmps_renovar_cron' );  
		}
	}
	add_action('wp', 'wcmps_start_cron');

	function wcmps_stop_cron() {	
		$timestamp = wp_next_scheduled('wcmps_renovar_cron');
		wp_unschedule_event($timestamp, 'wcmps_renovar_cron');
	} 
	register_deactivation_hook(__FILE__, 'wcmps_stop_cron');


} // if !is_admin

////////////////////////////////////////////////////
// DATOS DEL VENDEDOR DE MERCADO PAGO
function wcmps_get_cart_vendor () {
	if ( WC() && WC()->cart ) {
	    $produtos = WC()->cart->get_cart();
	    foreach ($produtos as $key => $data) {
	        // IMPORTANTE: esto solo funciona para las ventas que tienen solo 1 proveedor. Mercado Pago no permite dividir el pago entre más de 2 partes (marketplace y 1 vendedor), esta es la razón
	        $v = wcfm_get_vendor_id_by_post( $data['product_id'] );
	        if ( (int)$v > 0 ) {
	            $vendedor = $v;
	        }
	    }
	    return $vendedor;
	}
}

