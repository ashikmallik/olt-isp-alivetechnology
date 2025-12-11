<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();
$sms = 'Something is wrong';
if (isset($_GET['token'])) {
    $customer_id = $_GET['token'];

    // $customer = $obj->getSingleData('tbl_agent', ['ag_id', '=', $customer_id]);
    $customer = $obj->rawSqlSingle("SELECT * FROM tbl_agent WHERE ag_id = $customer_id");
    $mobile = "88" . strval($customer['ag_mobile_no']);
    $customerName = isset($customer['ag_name']) ? $customer['ag_name'] : NULL;
    $cusId = isset($customer['cus_id']) ? $customer['cus_id'] : NULL;
    $cusIp = isset($customer['ip']) ? $customer['ip'] : NULL;
    $cusPackage = isset($customer['mb']) ? $customer['mb'] : NULL;
    $cusbill = isset($customer['taka']) ? $customer['taka'] : NULL;

    $totalDueAmount = $obj->rawSqlSingle("SELECT dueadvance FROM customer_billing WHERE agid = $customer_id")['dueadvance'];

    // $sms = $obj->sendsms($mobile, "Dear $customerName, here are your details: ID - $cusId, Username - $cusIp, Package - $cusPackage, Monthly Bill - $cusbill. Thank you for being with us.");


    $var_replacement = [
        '{CUSTOMER_NAME}' => $customerName,
        '{DUE_AMOUNT}' => $totalDueAmount,
        // '{PACKAGE_NAME}' => $cusPackage,
        '{PACKAGE_NAME}' =>  $obj->rawSqlSingle("SELECT package_name FROM tbl_package WHERE net_speed = '$cusPackage'")['package_name'],
        '{MONTHLY_BILL}' => $cusbill,
        '{CUSTOMER_ID}' => $cusId,
        '{IP_ADDRESS}' => $cusIp,
    ];

    $dusSmsInfo = $obj->details_by_cond("sms", "status='2'");

    // $sms_h = isset($dusSmsInfo['smshead']) ? $dusSmsInfo['smshead'] : NULL;
    $sms_b = isset($dusSmsInfo['smsbody']) ? $dusSmsInfo['smsbody'] : NULL;

    $sms_b = strtr($sms_b, $var_replacement);
    $message = "$sms_b";

    $sms = $obj->sendsms($mobile, $message);
    // $sms = $obj->sendsms($mobile, "প্রিয় গ্রাহক $customerName,\nআপনার আইডি: $cusId, ইউজারনেম: $cusIp, প্যাকেজ: $cusPackage, বর্তমান বকেয়া বিল: $cusbill টাকা।\nThank you for staying with us.");
}

echo json_encode(['response' => $sms]);
