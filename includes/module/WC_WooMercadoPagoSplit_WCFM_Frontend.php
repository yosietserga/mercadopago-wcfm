<?php

if (!defined('ABSPATH')) {
    exit;
}

// AGREGAR A MERCADO PAGO EN LAS OPCIONES DE PAGO
add_filter(
    'wcfm_marketplace_withdrwal_payment_methods',
    function ($payment_methods) {
        $payment_methods['mercado_pago'] = 'Mercado Pago';
        return $payment_methods;
    }
);

// AGREGAR INPUTS PARA API KEYS
add_filter(
    'wcfm_marketplace_settings_fields_withdrawal_payment_keys',
    function ($payment_keys, $wcfm_withdrawal_options) {
        $gateway_slug = 'mercado_pago';
        $withdrawal_mercado_pago_client_id  = isset($wcfm_withdrawal_options[$gateway_slug.'_client_id']) ? $wcfm_withdrawal_options[$gateway_slug.'_client_id'] : '';
        $withdrawal_mercado_pago_secret_key = isset($wcfm_withdrawal_options[$gateway_slug.'_secret_key']) ? $wcfm_withdrawal_options[$gateway_slug.'_secret_key'] : '';
        $payment_mercado_pago_keys          = [
            'withdrawal_'.$gateway_slug.'_client_id'  => [
                'label'       => __('Mercado Pago Client ID', 'wc-multivendor-marketplace'),
                'name'        => 'wcfm_withdrawal_options['.$gateway_slug.'_client_id]',
                'type'        => 'text',
                'class'       => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug,
                'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug,
                'value'       => $withdrawal_mercado_pago_client_id,
            ],
            'withdrawal_'.$gateway_slug.'_secret_key' => [
                'label'       => __('Mercado Pago Secret Key', 'wc-multivendor-marketplace'),
                'name'        => 'wcfm_withdrawal_options['.$gateway_slug.'_secret_key]',
                'type'        => 'text',
                'class'       => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug,
                'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug,
                'value'       => $withdrawal_mercado_pago_secret_key,
            ],
        ];
        $payment_keys = array_merge($payment_keys, $payment_mercado_pago_keys);
        return $payment_keys;
    },
    50,
    2
);

// AGREGAR INPUTS PARA API TEST KEYS
add_filter(
    'wcfm_marketplace_settings_fields_withdrawal_payment_test_keys',
    function ($payment_test_keys, $wcfm_withdrawal_options) {
        $gateway_slug = 'mercado_pago';
        $withdrawal_mercado_pago_test_client_id  = isset($wcfm_withdrawal_options[$gateway_slug.'_test_client_id']) ? $wcfm_withdrawal_options[$gateway_slug.'_test_client_id'] : '';
        $withdrawal_mercado_pago_test_secret_key = isset($wcfm_withdrawal_options[$gateway_slug.'_test_secret_key']) ? $wcfm_withdrawal_options[$gateway_slug.'_test_secret_key'] : '';
        $payment_mercado_pago_test_keys          = [
            'withdrawal_'.$gateway_slug.'_test_client_id'  => [
                'label'       => __('Mercado Pago Client ID', 'wc-multivendor-marketplace'),
                'name'        => 'wcfm_withdrawal_options['.$gateway_slug.'_test_client_id]',
                'type'        => 'text',
                'class'       => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_test withdrawal_mode_'.$gateway_slug,
                'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_test withdrawal_mode_'.$gateway_slug,
                'value'       => $withdrawal_mercado_pago_test_client_id,
            ],
            'withdrawal_'.$gateway_slug.'_test_secret_key' => [
                'label'       => __('Mercado Pago Secret Key', 'wc-multivendor-marketplace'),
                'name'        => 'wcfm_withdrawal_options['.$gateway_slug.'_test_secret_key]',
                'type'        => 'text',
                'class'       => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_test withdrawal_mode_'.$gateway_slug,
                'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_test withdrawal_mode_'.$gateway_slug,
                'value'       => $withdrawal_mercado_pago_test_secret_key,
            ],
        ];
        $payment_test_keys = array_merge($payment_test_keys, $payment_mercado_pago_test_keys);
        return $payment_test_keys;
    },
    50,
    2
);


// OPCIONES DE CONFIGURAR RECARGOS
add_filter(
    'wcfm_marketplace_settings_fields_withdrawal_charges',
    function ($withdrawal_charges, $wcfm_withdrawal_options, $withdrawal_charge) {
        $gateway_slug                   = 'mercado_pago';
        $withdrawal_charge_mercado_pago = isset($withdrawal_charge[$gateway_slug]) ? $withdrawal_charge[$gateway_slug] : [];
        $payment_withdrawal_charges     = [
            'withdrawal_charge_'.$gateway_slug => [
                'label'             => __('Mercado Pago Charge', 'wc-multivendor-marketplace'),
                'type'              => 'multiinput',
                'name'              => 'wcfm_withdrawal_options[withdrawal_charge]['.$gateway_slug.']',
                'class'             => 'withdraw_charge_block withdraw_charge_'.$gateway_slug,
                'label_class'       => 'wcfm_title wcfm_ele wcfm_fill_ele withdraw_charge_block withdraw_charge_'.$gateway_slug,
                'value'             => $withdrawal_charge_mercado_pago,
                'custom_attributes' => [ 'limit' => 1 ],
                'options'           => [
                    'percent' => [
                        'label'       => __('Percent Charge(%)', 'wc-multivendor-marketplace'),
                        'type'        => 'number',
                        'class'       => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed',
                        'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed',
                        'attributes'  => [
                            'min'  => '0.1',
                            'step' => '0.1',
                        ],
                    ],
                    'fixed'   => [
                        'label'       => __('Fixed Charge', 'wc-multivendor-marketplace'),
                        'type'        => 'number',
                        'class'       => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed',
                        'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed',
                        'attributes'  => [
                            'min'  => '0.1',
                            'step' => '0.1',
                        ],
                    ],
                    'tax'     => [
                        'label'       => __('Charge Tax', 'wc-multivendor-marketplace'),
                        'type'        => 'number',
                        'class'       => 'wcfm-text wcfm_ele',
                        'label_class' => 'wcfm_title wcfm_ele',
                        'attributes'  => [
                            'min'  => '0.1',
                            'step' => '0.1',
                        ],
                        'hints'       => __('Tax for withdrawal charge, calculate in percent.', 'wc-multivendor-marketplace'),
                    ],
                ],
            ],
        ];
        $withdrawal_charges             = array_merge($withdrawal_charges, $payment_withdrawal_charges);
        return $withdrawal_charges;
    },
    50,
    3
);

// CAMPO DE CONFIGURACION DE METODO DE PAGO (EN MENU/PAYMENT)
add_filter(
    'wcfm_marketplace_settings_fields_billing',
    function ($vendor_billing_fileds, $vendor_id) {
        $gateway_slug = 'mercado_pago';
        $vendor_data  = get_user_meta($vendor_id, 'wcfmmp_profile_settings', true);
        if (!$vendor_data) {
            $vendor_data = [];
        }

        $mercado_pago = isset($vendor_data['payment'][$gateway_slug]['email']) ? esc_attr($vendor_data['payment'][$gateway_slug]['email']) : '';
        $vendor_mercado_pago_billing_fileds = [
            $gateway_slug => [
                'label'       => __('Mercado Pago Email', 'wc-frontend-manager'),
                'name'        => 'payment['.$gateway_slug.'][email]',
                'type'        => 'text',
                'class'       => 'wcfm-text wcfm_ele paymode_field paymode_'.$gateway_slug,
                'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_'.$gateway_slug,
                'value'       => $mercado_pago,
            ],
        ];
        $vendor_billing_fileds              = array_merge($vendor_billing_fileds, $vendor_mercado_pago_billing_fileds);
        return $vendor_billing_fileds;
    },
    50,
    2
);


// PROCESO DE PAGO
// class WCFMmp_Gateway_Paypal extends WCFMmp_Abstract_Gateway {
// public $id;
// public $gateway_title;
// public $payment_gateway;
// public $message = array();
// private $client_id;
// private $client_secret;
// private $is_testmode = false;
// private $payout_mode = 'true';
// private $reciver_email;
// private $api_endpoint;
// private $token_endpoint;
// private $access_token;
// private $token_type;
//
// public function __construct() {
// global $WCFM, $WCFMmp;
//
// $this->id              = 'paypal';
// $this->gateway_title   = __('PayPal', 'wc-multivendor-marketplace');
// $this->payment_gateway = $this->id;
// $this->payout_mode     = 'false';
//
// $withdrawal_test_mode = isset( $WCFMmp->wcfmmp_withdrawal_options['test_mode'] ) ? 'yes' : 'no';
//
// $this->api_endpoint = 'https://api.paypal.com/v1/payments/payouts?sync_mode='.$this->payout_mode;
// $this->token_endpoint = 'https://api.paypal.com/v1/oauth2/token';
// $this->client_id = isset( $WCFMmp->wcfmmp_withdrawal_options['paypal_client_id'] ) ? $WCFMmp->wcfmmp_withdrawal_options['paypal_client_id'] : '';
// $this->client_secret = isset( $WCFMmp->wcfmmp_withdrawal_options['paypal_secret_key'] ) ? $WCFMmp->wcfmmp_withdrawal_options['paypal_secret_key'] : '';
//
// if ( $withdrawal_test_mode == 'yes') {
// $this->is_testmode = true;
// $this->api_endpoint = 'https://api.sandbox.paypal.com/v1/payments/payouts?sync_mode='.$this->payout_mode;
// $this->token_endpoint = 'https://api.sandbox.paypal.com/v1/oauth2/token';
// $this->client_id = isset( $WCFMmp->wcfmmp_withdrawal_options['paypal_test_client_id'] ) ? $WCFMmp->wcfmmp_withdrawal_options['paypal_test_client_id'] : '';
// $this->client_secret = isset( $WCFMmp->wcfmmp_withdrawal_options['paypal_test_secret_key'] ) ? $WCFMmp->wcfmmp_withdrawal_options['paypal_test_secret_key'] : '';
// }
// }
//
// public function gateway_logo() { global $WCFMmp; return $WCFMmp->plugin_url . 'assets/images/'.$this->id.'.png'; }
//
// public function process_payment( $withdrawal_id, $vendor_id, $withdraw_amount, $withdraw_charges, $transaction_mode = 'auto' ) {
// global $WCFM, $WCFMmp;
//
// $this->withdrawal_id = $withdrawal_id;
// $this->vendor_id = $vendor_id;
// $this->withdraw_amount = round( $withdraw_amount, 2 );
// $this->currency = get_woocommerce_currency();
// $this->transaction_mode = $transaction_mode;
// $this->reciver_email = $WCFMmp->wcfmmp_vendor->get_vendor_payment_account( $this->vendor_id, 'paypal' );
// if ($this->validate_request()) {
// $this->generate_access_token();
// $paypal_response = $this->process_paypal_payout();
// if ($paypal_response) {
// return array( 'status' => true, 'message' => __('New transaction has been initiated', 'wc-multivendor-marketplace') );
// } else {
// return false;
// }
// } else {
// return $this->message;
// }
// }
//
// public function validate_request() {
// global $WCFMmp;
// if (!$this->client_id || !$this->client_secret) {
// $this->message[] = array( 'status' => false, 'message' => __('PayPal Payout setting is not configured properly please contact site administrator', 'wc-multivendor-marketplace') );
// return false;
// } else if (!$this->reciver_email) {
// $this->message[] = array( 'status' => false, 'message' => __('Please update your PayPal email to receive commission', 'wc-multivendor-marketplace') );
// return false;
// }
// return parent::validate_request();
// }
//
// private function generate_access_token() {
// $curl = curl_init();
// curl_setopt($curl, CURLOPT_HEADER, false);
// curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Accept-Language: en_US'));
// curl_setopt($curl, CURLOPT_VERBOSE, 1);
// curl_setopt($curl, CURLOPT_TIMEOUT, 30);
// curl_setopt($curl, CURLOPT_URL, $this->token_endpoint);
// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($curl, CURLOPT_USERPWD, $this->client_id . ':' . $this->client_secret);
// curl_setopt($curl, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
// curl_setopt($curl, CURLOPT_SSLVERSION, 6);
// $response = curl_exec($curl);
// curl_close($curl);
// $response_array = json_decode($response, true);
// wcfmmp_log( sprintf( '#%s - PayPal payment Access Token: %s %s %s', $this->withdrawal_id, json_encode($response_array) ) );
// $this->access_token = isset($response_array['access_token']) ? $response_array['access_token'] : '';
// $this->token_type = isset($response_array['token_type']) ? $response_array['token_type'] : '';
// }
//
// private function process_paypal_payout() {
// global $WCFM, $WCFMmp;
// $api_authorization = "Authorization: {$this->token_type} {$this->access_token}";
// $note = sprintf( __('Payment recieved from %1$s as commission at %2$s on %3$s', 'wc-multivendor-marketplace'), get_bloginfo('name'), date('H:i:s'), date('d-m-Y'));
// $request_params = '{
// "sender_batch_header": {
// "sender_batch_id":"' . uniqid() . '",
// "email_subject": "You have a payment",
// "recipient_type": "EMAIL"
// },
// "items": [
// {
// "recipient_type": "EMAIL",
// "amount": {
// "value": ' . $this->withdraw_amount . ',
// "currency": "' . $this->currency . '"
// },
// "receiver": "' . $this->reciver_email . '",
// "note": "' . $note . '",
// "sender_item_id": "' . $this->vendor_id . '"
// }
// ]
// }';
// $curl = curl_init();
// curl_setopt($curl, CURLOPT_HEADER, false);
// curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type:application/json', $api_authorization));
// curl_setopt($curl, CURLOPT_VERBOSE, 1);
// curl_setopt($curl, CURLOPT_TIMEOUT, 30);
// curl_setopt($curl, CURLOPT_URL, $this->api_endpoint);
// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($curl, CURLOPT_POSTFIELDS, $request_params);
// curl_setopt($curl, CURLOPT_SSLVERSION, 6);
// $result = curl_exec($curl);
// curl_close($curl);
// $result_array = json_decode($result, true);
// $batch_status = $result_array['batch_header']['batch_status'];
//
// $batch_payout_status = apply_filters('wcfmmp_paypal_payout_batch_status', array('PENDING', 'PROCESSING', 'SUCCESS', 'NEW'));
// if (in_array($batch_status, $batch_payout_status) ) {
// Updating withdrawal meta
// $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'withdraw_amount', $this->withdraw_amount );
// $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'currency', $this->currency );
// $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'reciver_email', $this->reciver_email );
// $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'payout_batch_id', $result_array['batch_header']['payout_batch_id'] );
// $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'batch_status', $batch_status );
// $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'sender_batch_id', $result_array['batch_header']['sender_batch_header']['sender_batch_id'] );
// wcfmmp_log( sprintf( '#%s - PayPal payment processing success: %s', $this->withdrawal_id, json_encode($result_array) ), 'info' );
// return $result_array;
// } else {
// wcfmmp_log( sprintf( '#%s - PayPal payment processing failed: %s', sprintf( '%06u', $this->withdrawal_id ), json_encode($result_array) ), 'error' );
// return false;
// }
// }
// }
