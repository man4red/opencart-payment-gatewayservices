<?php
class ControllerExtensionPaymentGatewayservices extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/gatewayservices');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_gatewayservices', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['merchant_id'])) {
			$data['error_merchant_id'] = $this->error['merchant_id'];
		} else {
			$data['error_merchant_id'] = '';
		}

		if (isset($this->error['terminal_id'])) {
			$data['error_terminal_id'] = $this->error['terminal_id'];
		} else {
			$data['error_terminal_id'] = '';
		}

		if (isset($this->error['private_key'])) {
			$data['error_private_key'] = $this->error['private_key'];
		} else {
			$data['error_private_key'] = '';
		}

		if (isset($this->error['api_password'])) {
			$data['error_api_password'] = $this->error['api_password'];
		} else {
			$data['error_api_password'] = '';
		}

		if (isset($this->error['transaction_type'])) {
			$data['error_transaction_type'] = $this->error['transaction_type'];
		} else {
			$data['error_transaction_type'] = '';
		}

		if (isset($this->error['api_password'])) {
			$data['error_api_password'] = $this->error['api_password'];
		} else {
			$data['error_api_password'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/gatewayservices', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/gatewayservices', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_gatewayservices_merchant_id'])) {
			$data['payment_gatewayservices_merchant_id'] = $this->request->post['payment_gatewayservices_merchant_id'];
		} else {
			$data['payment_gatewayservices_merchant_id'] = $this->config->get('payment_gatewayservices_merchant_id');
		}

		if (isset($this->request->post['payment_gatewayservices_terminal_id'])) {
			$data['payment_gatewayservices_terminal_id'] = $this->request->post['payment_gatewayservices_terminal_id'];
		} else {
			$data['payment_gatewayservices_terminal_id'] = $this->config->get('payment_gatewayservices_terminal_id');
		}

		if (isset($this->request->post['payment_gatewayservices_private_key'])) {
			$data['payment_gatewayservices_private_key'] = $this->request->post['payment_gatewayservices_private_key'];
		} else {
			$data['payment_gatewayservices_private_key'] = $this->config->get('payment_gatewayservices_private_key');
		}

		if (isset($this->request->post['payment_gatewayservices_api_password'])) {
			$data['payment_gatewayservices_api_password'] = $this->request->post['payment_gatewayservices_api_password'];
		} else {
			$data['payment_gatewayservices_api_password'] = $this->config->get('payment_gatewayservices_api_password');
		}

		if (isset($this->request->post['payment_gatewayservices_transaction_type'])) {
			$data['payment_gatewayservices_transaction_type'] = $this->request->post['payment_gatewayservices_transaction_type'];
		} else {
			$data['payment_gatewayservices_transaction_type'] = $this->config->get('payment_gatewayservices_transaction_type');
		}

		if (isset($this->request->post['payment_gatewayservices_api_password'])) {
			$data['payment_gatewayservices_api_password'] = $this->request->post['payment_gatewayservices_api_password'];
		} else {
			$data['payment_gatewayservices_api_password'] = $this->config->get('payment_gatewayservices_api_password');
		}

		if (isset($this->request->post['payment_gatewayservices_total'])) {
			$data['payment_gatewayservices_total'] = $this->request->post['payment_gatewayservices_total'];
		} else {
			$data['payment_gatewayservices_total'] = $this->config->get('payment_gatewayservices_total');
		}

		if (isset($this->request->post['payment_gatewayservices_order_status_id'])) {
			$data['payment_gatewayservices_order_status_id'] = $this->request->post['payment_gatewayservices_order_status_id'];
		} else {
			$data['payment_gatewayservices_order_status_id'] = $this->config->get('payment_gatewayservices_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_gatewayservices_geo_zone_id'])) {
			$data['payment_gatewayservices_geo_zone_id'] = $this->request->post['payment_gatewayservices_geo_zone_id'];
		} else {
			$data['payment_gatewayservices_geo_zone_id'] = $this->config->get('payment_gatewayservices_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_gatewayservices_test'])) {
			$data['payment_gatewayservices_test'] = $this->request->post['payment_gatewayservices_test'];
		} else {
			$data['payment_gatewayservices_test'] = $this->config->get('payment_gatewayservices_test');
		}

		if (isset($this->request->post['payment_gatewayservices_status'])) {
			$data['payment_gatewayservices_status'] = $this->request->post['payment_gatewayservices_status'];
		} else {
			$data['payment_gatewayservices_status'] = $this->config->get('payment_gatewayservices_status');
		}

		if (isset($this->request->post['payment_gatewayservices_sort_order'])) {
			$data['payment_gatewayservices_sort_order'] = $this->request->post['payment_gatewayservices_sort_order'];
		} else {
			$data['payment_gatewayservices_sort_order'] = $this->config->get('payment_gatewayservices_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/gatewayservices', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/gatewayservices')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_gatewayservices_merchant_id']) {
			$this->error['merchant_id'] = $this->language->get('error_merchant_id');
		}

		if (!$this->request->post['payment_gatewayservices_terminal_id']) {
			$this->error['terminal_id'] = $this->language->get('error_terminal_id');
		}

		if (!$this->request->post['payment_gatewayservices_private_key']) {
			$this->error['private_key'] = $this->language->get('error_private_key');
		}

		if (!$this->request->post['payment_gatewayservices_api_password']) {
			$this->error['api_password'] = $this->language->get('error_api_password');
		}

		if (!$this->request->post['payment_gatewayservices_transaction_type']) {
			$this->error['transaction_type'] = $this->language->get('error_transaction_type');
		}

		if (!$this->request->post['payment_gatewayservices_api_password']) {
			$this->error['api_password'] = $this->language->get('error_api_password');
		}

		return !$this->error;
	}
	
}