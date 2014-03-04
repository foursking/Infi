<?php
namespace lestore_payment\app\service;

class PaymentServiceImpl implements PaymentService{
    public function getVaildPaymentProducts($orderInfo){
		$all_payment_methods = Payment::getAllPayments();
		foreach ($all_payment_methods as $pk => &$pm)
		{
			$payment_desc = str_replace('"', '\"', $pm['payment_desc']);
			eval("\$payment_desc = \"$payment_desc\";");
			$pm['desc'] = $payment_desc;
		}
		unset($pm);
		
		include_once __DIR__ . '/includes/lib_region.php';
		$region = Region::getRegionById($address['country'] ? $address['country'] : $country_default);
		if (isset($region[0]['region_code'])) {
			$country_code = $region[0]['region_code'];
		} else {
			$country_code = 'US';
		}
		
		//$payment_methods = get_filter_payment_method($country_code, $currency_code, $lang_code);
		$payment_methods = get_filter_payment_method($country_code, $currency_code, '', PROJECT_NAME);
		$get_payment_modules_with_ccc = get_payment_modules_with_ccc($country_code, $currency_code, '', PROJECT_NAME);
		$use_gc = in_array('gc', $get_payment_modules_with_ccc) || (isset($_GET['gc']) && $_GET['gc'] == 1);
		$use_realtimebank = in_array('realtimebank', $get_payment_modules_with_ccc);
		$use_webmoney = in_array('webmoney', $get_payment_modules_with_ccc);
		if ($use_gc || $use_realtimebank || $use_webmoney)
		{

		}
    }

    public function pay($orderInfo, $paymentProduct){
    }
}
