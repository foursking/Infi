<?php
namespace lestore_payment\app\service;

interface PaymentService{
	public function getVaildPaymentProducts($orderInfo);
    public function pay($orderInfo, $paymentProduct);
}
