<?php
class ModelExtensionPaymentGatewayservices extends Model {
	private $apiVersion="1.0";
	private $logFileName = "gatewayservices.log";
	private $gateway_url = "https://gateway-services.com/acquiring.php";
	private $api_password;
	private $private_key;
	private $transaction_type;
	private $merchant_id;
	private $terminal_id;
	private $returnUrl;
	private $notifyUrl;
	private $encodedMessage;
	private $signature;
	private $pmt_amt;
	private $pmt_currency_code;
	private $order_id;
	private $transactionId;
	private $pmt_sender_email;
	private $pmt_contact_firstname;
	private $pmt_contact_lastname;
	private $pmt_contact_phone;
	private $pmt_country;
	private $pmt_address_1;
	private $pmt_city;
	private $pmt_state;
	private $pmt_postcode;
	private $pmt_ipaddress;

	private $apiParas = array();

	public function getMethod($address, $total) {
		$this->load->language('extension/payment/gatewayservices');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_gatewayservices_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('payment_gatewayservices_total') > 0 && $this->config->get('payment_gatewayservices_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('payment_gatewayservices_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'gatewayservices',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_gatewayservices_sort_order')
			);
		}

		return $method_data;
	}

	private function setParams($gatewayservices_config) {
		$this->gateway_url = $gatewayservices_config['gateway_url'];
		$this->merchant_id = $gatewayservices_config['merchant_id'];
		$this->terminal_id = $gatewayservices_config['terminal_id'];
		$this->private_key = $gatewayservices_config['private_key'];
		$this->transaction_type = $gatewayservices_config['transaction_type'];
		$this->api_password = $gatewayservices_config['api_password'];
		$this->returnUrl = $gatewayservices_config['return_url'];

		if (empty($this->merchant_id)||trim($this->merchant_id)=="") {
			throw new Exception("merchant_id should not be NULL!");
		}
		if (empty($this->terminal_id)||trim($this->terminal_id)=="") {
			throw new Exception("terminal_id should not be NULL!");
		}
		if (empty($this->private_key)||trim($this->private_key)=="") {
			throw new Exception("private_key should not be NULL!");
		}
		if (empty($this->transaction_type)||trim($this->transaction_type)=="") {
			throw new Exception("transaction_type should not be NULL!");
		}
		if (empty($this->api_password)||trim($this->api_password)=="") {
			throw new Exception("api_password should not be NULL!");
		}
		if (empty($this->gateway_url)||trim($this->gateway_url)=="") {
			throw new Exception("gateway_url should not be NULL!");
		}
	}

	private function setTransactionDetails($transactionDetails) {
		$this->order_id              = $transactionDetails['order_id'];
		$this->pmt_amt               = $transactionDetails['pmt_amt'];
		$this->pmt_currency_code     = $transactionDetails['pmt_currency_code'];
		$this->order_id              = $transactionDetails['order_id'];
		$this->pmt_sender_email      = $transactionDetails['pmt_sender_email'];
		$this->pmt_contact_firstname = $transactionDetails['pmt_contact_firstname'];
		$this->pmt_contact_lastname  = $transactionDetails['pmt_contact_lastname'];
		$this->pmt_contact_phone     = $transactionDetails['pmt_contact_phone'];
		$this->pmt_country           = $transactionDetails['pmt_country'];
		$this->pmt_address_1         = $transactionDetails['pmt_address_1'];
		$this->pmt_city              = $transactionDetails['pmt_city'];
		$this->pmt_state             = $transactionDetails['pmt_state'];
		$this->pmt_postcode          = $transactionDetails['pmt_postcode'];
		$this->pmt_ipaddress 		 = ($this->checkEmpty($transactionDetails['pmt_ipaddress'])) ? $_SERVER['SERVER_ADDR'] : $transactionDetails['pmt_ipaddress'];

		if (empty($this->order_id)||trim($this->order_id)=="") {
			throw new Exception("order_id should not be NULL!");
		}
		if (empty($this->pmt_amt)||trim($this->pmt_amt)=="") {
			throw new Exception("pmt_amt should not be NULL!");
		}
		if (empty($this->pmt_currency_code)||trim($this->pmt_currency_code)=="") {
			throw new Exception("pmt_currency_code should not be NULL!");
		}
		if (empty($this->order_id)||trim($this->order_id)=="") {
			throw new Exception("order_id should not be NULL!");
		}
		if (empty($this->pmt_sender_email)||trim($this->pmt_sender_email)=="") {
			throw new Exception("pmt_sender_email should not be NULL!");
		}
		if (empty($this->pmt_contact_firstname)||trim($this->pmt_contact_firstname)=="") {
			throw new Exception("pmt_contact_firstname should not be NULL!");
		}
		if (empty($this->pmt_contact_lastname)||trim($this->pmt_contact_lastname)=="") {
			throw new Exception("pmt_contact_lastname should not be NULL!");
		}
		if (empty($this->pmt_contact_phone)||trim($this->pmt_contact_phone)=="") {
			throw new Exception("pmt_contact_phone should not be NULL!");
		}
		if (empty($this->pmt_country)||trim($this->pmt_country)=="") {
			throw new Exception("pmt_country should not be NULL!");
		}
		if (empty($this->pmt_address_1)||trim($this->pmt_address_1)=="") {
			throw new Exception("pmt_address_1 should not be NULL!");
		}
		if (empty($this->pmt_city)||trim($this->pmt_city)=="") {
			throw new Exception("pmt_city should not be NULL!");
		}
		if (empty($this->pmt_state)||trim($this->pmt_state)=="") {
			throw new Exception("pmt_state should not be NULL!");
		}
		if (empty($this->pmt_postcode)||trim($this->pmt_postcode)=="") {
			throw new Exception("pmt_postcode should not be NULL!");
		}
		if (empty($this->pmt_ipaddress)||trim($this->pmt_ipaddress)=="") {
			throw new Exception("pmt_ipaddress should not be NULL!");
		}
	}

	function pagePay($transactionDetails, $config) {
		$this->setParams($config);
		$this->setTransactionDetails($transactionDetails);
		$this->transactionId = intval("11" . rand(1, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9));

		$response = $this->pageExecute($this, "post");
		$log = new Log($this->logFileName);
		$log->write("response: ".var_export($response,true));

		return $response;
	}

	function check($arr, $config){
		$this->setParams($config);

		$result = $this->verify($arr);

		return $result;
	}

	public function pageExecute($request, $httpmethod = "POST") {

		$totalParams = $this->sign();

		if ("POST" == strtoupper($httpmethod)) {
			foreach ($totalParams as $key => $value) {
				if (false === $this->checkEmpty($value)) {
					$value = str_replace("\"", "&quot;", $value);
					$totalParams[$key] = $value;
				} else {
					unset($totalParams[$key]);
				}
			}
			return $totalParams;
		}
	}

	protected function checkEmpty($value) {
		if (!isset($value))
			return true;
		if ($value === null)
			return true;
		if (trim($value) === "")
			return true;

		return false;
	}

	function verify($data) {
		$result = false;

		if (!$data || !isset($data['gws_trans']) || !isset($data['signature']) || !isset($data['encodedMessage'])) {
			return false;
		}

		// Get signature key
		$signature_key = trim($this->private_key . $this->api_password . $data['gws_trans']);
		// Get decoded message
		$decodedMessage=base64_decode($data['encodedMessage']);
		// Compute signature
		$computedSignature = base64_encode(hash_hmac("sha256", $decodedMessage, $signature_key, True));
		// Validate signature
		if($computedSignature == $data['signature']) {
			//$this->log->write('QA : Signature verified successfully');
			$result = simplexml_load_string($decodedMessage);
			return $result;
		} else { 
			//$this->log->write('QA : Invalid signature.');
			return false;
		}
		
		return $result;
	}

	protected function sign() {

		$apiPasswordEncrypted = hash('sha256', $this->api_password);
		$productShortDescription = 'Short description';

		//$callbackUrl = $this->url->link('extension/payment/gatewayservices/callback', 'gws_trans=' . $this->transactionId , true);
		$callbackUrl = HTTPS_SERVER;

		$xmlReq = '<?xml version="1.0" encoding="UTF-8" ?>
        <TransactionRequest>
            <Language>ENG</Language>
            <Credentials>
                <MerchantId>' . $this->merchant_id . '</MerchantId>
                <TerminalId>' . $this->terminal_id . '</TerminalId>
                <TerminalPassword>' . $apiPasswordEncrypted . '</TerminalPassword>
            </Credentials>
            <TransactionType>' . ($this->transaction_type != '' ? $this->transaction_type : 'LP101') . '</TransactionType>
            <TransactionId>' . $this->transactionId . '</TransactionId>
			<ReturnUrl page="' . $callbackUrl . '">
				<Param>
					<Key>route</Key>
					<Value>extension/payment/gatewayservices/callback</Value>
				</Param>
				<Param>
					<Key>gws_trans</Key>
					<Value>' . $this->transactionId . '</Value>
				</Param>
				<Param>
					<Key>order_id</Key>
					<Value>' . $this->order_id . '</Value>
				</Param>
			</ReturnUrl>
            <CurrencyCode>' . $this->pmt_currency_code  . '</CurrencyCode>
            <TotalAmount>' . number_format($this->pmt_amt, 2, '', '') . '</TotalAmount>
            <ProductDescription>' . $productShortDescription . '</ProductDescription>
            <CustomerDetails>
				<FirstName>' . $this->pmt_contact_firstname . '</FirstName>
				<LastName>' . $this->pmt_contact_lastname . '</LastName>
                <CustomerIP>' . $this->pmt_ipaddress . '</CustomerIP>
                <Phone>' . $this->pmt_contact_phone . '</Phone>
                <Email>' . $this->pmt_sender_email . '</Email>
                <Street>' . $this->pmt_address_1 . '</Street>
                <City>' . $this->pmt_city . '</City>
                <Region>' . $this->pmt_state . '</Region>
                <Country>' . $this->pmt_country . '</Country>
                <Zip>' . $this->pmt_postcode  . '</Zip>
            </CustomerDetails>
		</TransactionRequest>';
		
		$log = new Log($this->logFileName);
		$log->write($xmlReq);

		$signature_key = trim($this->private_key . $this->api_password . $this->transactionId);
        $signature = base64_encode(hash_hmac("sha256", trim($xmlReq), $signature_key, True));
		$encodedMessage = base64_encode($xmlReq);
		
		$this->encodedMessage = $encodedMessage;
		$this->signature = $signature;

		$totalParams = array(
			'version' => $this->apiVersion,
			'encodedMessage' => $this->encodedMessage,
			'signature' => $this->signature
		);
		return $totalParams;
	}
}
