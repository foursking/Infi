<?php
namespace lestore_payment\app\service;

interface PaymentProduct{
    public function getDesc();
    public function getPayLink();
	public function queryStatus($orderId);
}
