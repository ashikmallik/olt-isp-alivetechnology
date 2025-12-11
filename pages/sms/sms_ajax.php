<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect POST data
    $sms_status = $_POST['sms_status'];
    $sms_id = $_POST['sms_id'];

    // Initialize the WHERE clause with a default condition
    $where = "WHERE ag_id > 0";

    // Check if 'mb' is set and not '0'
    if (isset($_POST['mb']) && $_POST['mb'] != '0' && $_POST['mb'] != '') {
        // if (isset($_POST['mb'])) {
        $where .= " AND mb = '" . $_POST['mb'] . "'";
    }

    // Check if 'zone' is set and not '0'
    if (isset($_POST['zone']) && $_POST['zone'] != '0' && $_POST['zone'] != '' && $_POST['zone'] != 'All') {
        $where .= " AND zone = '" . $_POST['zone'] . "'";
    }

    // Checking sub-zone
    if (isset($_POST['sub_zone']) && $_POST['sub_zone'] != '0') {
        $where .= " AND sub_zone = '" . $_POST['sub_zone'] . "'";
    }

    // Check if 'destination' is set and not '0'
    if (isset($_POST['destination']) && $_POST['destination'] != '0') {
        $where .= " AND destination = '" . $_POST['destination'] . "'";
    }

    // Check if 'mikrotik_disconnect' is set and not '0'
    if (isset($_POST['mikrotik_disconnect']) && $_POST['mikrotik_disconnect'] != '0') {
        $where .= " AND mikrotik_disconnect = '" . $_POST['mikrotik_disconnect'] . "'";
    }

    // Check if 'billing_person_id' is set and not '0'
    if (isset($_POST['billing_person_id']) && $_POST['billing_person_id'] != '0') {
        $where .= " AND billing_person_id = '" . $_POST['billing_person_id'] . "'";
    }

    // Check if 'ag_status' is set and not '1023'
    if (isset($_POST['ag_status']) && $_POST['ag_status'] != '1023') {
        $where .= " AND ag_status = '" . $_POST['ag_status'] . "'";
    }

    // Construct the SQL query
    $sql = "SELECT * FROM tbl_agent $where";
    $smsClient = $obj->rawSql($sql);
    // var_dump($smsClient);

    $smsI = 0;
    $smsArray = [];
    foreach ($smsClient as $value) {
        $mobile = isset($value['ag_mobile_no']) ? $value['ag_mobile_no'] : NULL;
        // $mass = $_POST['sms_body'];
        $mobilenum = "88" . $mobile;
        $totalDueAmount = $obj->rawSqlSingle("SELECT dueadvance FROM customer_billing WHERE agid = '$value[ag_id]'")['dueadvance'];
        $var_replacement = [
            '{CUSTOMER_NAME}' => $value['ag_name'],
            // '{PACKAGE_NAME}' => $value['mb'],
            '{PACKAGE_NAME}' =>  $obj->rawSqlSingle("SELECT package_name FROM tbl_package WHERE net_speed = '$value[mb]'")['package_name'],
            '{MONTHLY_BILL}' => $value['taka'],
            '{CUSTOMER_ID}' => $value['cus_id'],
            '{IP_ADDRESS}' => $value['ip'],
            '{DUE_AMOUNT}' => $totalDueAmount,
        ];
        $sms_b = isset($_POST['sms_body']) ? $_POST['sms_body'] : NULL;
        $sms_b = strtr($sms_b, $var_replacement);
        $mass = "$sms_b";

        $smsArray[$smsI++] = ["to" => $mobilenum, "message" => $mass];
    }

    $sms = $obj->sms_send($smsArray);
    echo json_encode($sms);
    // 
    // echo json_encode(["success" => true, 'sms_body' => $smsArray, 'method' => $where, 'query' => $smsClient]);
    exit();
}
