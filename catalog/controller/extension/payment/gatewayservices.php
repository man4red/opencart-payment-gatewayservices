<?php
class ControllerExtensionPaymentGatewayservices extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$config = array (
			'merchant_id'      => $this->config->get('payment_gatewayservices_merchant_id'),
			'terminal_id'      => $this->config->get('payment_gatewayservices_terminal_id'),
			'private_key'      => $this->config->get('payment_gatewayservices_private_key'),
			'api_password'     => $this->config->get('payment_gatewayservices_api_password'),
			'transaction_type' => $this->config->get('payment_gatewayservices_transaction_type'),
			'return_url'       => $this->url->link('checkout/success'),
			'gateway_url'      => $this->config->get('payment_gatewayservices_test') == "sandbox" ? "https://test.gateway-services.com/acquiring.php" : "https://gateway-services.com/acquiring.php",
		);
		$out_trade_no = trim($order_info['order_id']);
		$subject = trim($this->config->get('config_name'));
		
		$transactionDetails = array();
		$transactionDetails['pmt_amt'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$transactionDetails['pmt_currency_code'] = $order_info['currency_code'];
		$transactionDetails['order_id'] = $order_info['order_id'];
		$transactionDetails['pmt_sender_email'] = $order_info['email'];
		$transactionDetails['pmt_contact_firstname'] = $order_info['payment_firstname'];
		$transactionDetails['pmt_contact_lastname'] = $order_info['payment_lastname'];
		$transactionDetails['pmt_contact_phone'] = $order_info['telephone'];
		$transactionDetails['pmt_country'] = $order_info['payment_iso_code_2'];
		$transactionDetails['pmt_address_1'] = $order_info['payment_address_1'];
		$transactionDetails['pmt_city'] = $order_info['payment_city'];
		$transactionDetails['pmt_state'] = $order_info['payment_zone'];
		$transactionDetails['pmt_postcode'] = $order_info['payment_postcode'];
		$transactionDetails['pmt_ipaddress'] = $this->request->server['REMOTE_ADDR'];
		$transactionDetails['order_id'] = $out_trade_no;
		

		$this->load->model('extension/payment/gatewayservices');

		$response = $this->model_extension_payment_gatewayservices->pagePay($transactionDetails, $config);
		$data['action'] = $config['gateway_url'];
		$data['form_params'] = $response;

		return $this->load->view('extension/payment/gatewayservices', $data);
	}

	public function callback() {
		$this->log->write('gatewayservices redirect notify:');
		$post_arr = $_POST;
		$get_arr = $_GET;
		$merged_arr = array_merge($_GET, $_POST);
		$config = array (
			'merchant_id'      => $this->config->get('payment_gatewayservices_merchant_id'),
			'terminal_id'      => $this->config->get('payment_gatewayservices_terminal_id'),
			'private_key'      => $this->config->get('payment_gatewayservices_private_key'),
			'transaction_type' => $this->config->get('payment_gatewayservices_transaction_type'),
			'return_url'       => $this->url->link('checkout/success'),
			'gateway_url'      => $this->config->get('payment_gatewayservices_test') == "sandbox" ? "https://test.gateway-services.com/acquiring.php" : "https://gateway-services.com/acquiring.php",
			'api_password' => $this->config->get('payment_gatewayservices_api_password'),
		);
		$this->load->model('extension/payment/gatewayservices');
		$this->log->write('POST' . var_export($_POST,true));

		$result = $this->model_extension_payment_gatewayservices->check($merged_arr, $config);

		if($result) {//check successed
			$this->log->write('Gatewayservices check successed');

			if ((string)$result->PaymentStatus == 'APPROVED' && isset($_GET['order_id'])) {
				$this->load->model('checkout/order');
				$order_id = $_GET['order_id'];
				$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_gatewayservices_order_status_id'));
				$this->response->redirect($this->url->link('checkout/success', '', true));
			}
			$this->response->redirect($this->url->link('checkout/failure', '', true));
		} else {
			$error = (string)$result->Description . ' (' . (string)$result->Code . ')';
			$this->log->write('Gatewayservices check failed: ' . $error);
			//check failed
			$this->response->redirect($this->url->link('checkout/failure', '', true));
		}
	}
}