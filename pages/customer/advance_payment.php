<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();



// Validation (optional but recommended)
if (!empty($_POST['advance_amount']) && !empty($_POST['entry_date'])) {
    $obj->insertData("tbl_account", [
        'cus_id' => $_POST['cus_id'],
        'agent_id' => $_POST['ag_id'],
        'acc_amount' => $_POST['advance_amount'],
        'acc_type' => '3',
        'acc_description' => strval($_POST['description']) > 0 ? $_POST['description'] : 'Advance Payment',
        'entry_by' => $_POST['entry_by'],
        'entry_date' => $_POST['entry_date'],
        'update_by' => $_POST['entry_by']
    ]);

    $obj->rawSqlSingle(
        "UPDATE customer_billing 
            SET totalpaid =  totalpaid + $_POST[advance_amount],
            dueadvance = dueadvance - $_POST[advance_amount] 
            WHERE agid = $_POST[ag_id]"
    );

    // sms start
    $agent = $obj->getSingleData('tbl_agent', ['where' => ['ag_id', '=', $_POST['ag_id']]]);
    $paidSmsInfo = $obj->details_by_cond("sms", "status='7'");

    if (isset($paidSmsInfo['smshead']) && $paidSmsInfo['smshead'] == 'active') {
        $totalDueAmount = $obj->rawSqlSingle("SELECT dueadvance FROM customer_billing WHERE agid = '$_POST[ag_id]'");
        // SMS SEND
        $var_replacement = [
            '{CUSTOMER_NAME}' => $agent['ag_name'],
            '{ADVANCE_AMOUNT}' => $_POST['advance_amount'],
            // '{PACKAGE_NAME}' => $agent['mb'],
            '{PACKAGE_NAME}' =>  $obj->rawSqlSingle("SELECT package_name FROM tbl_package WHERE net_speed = '$agent[mb]'")['package_name'],
            '{MONTHLY_BILL}' => $agent['taka'],
            '{CUSTOMER_ID}' => $agent['cus_id'],
            '{IP_ADDRESS}' => $agent['ip'],
            '{DUE_AMOUNT}' => $totalDueAmount['dueadvance'],
        ];

        $sms_b = isset($paidSmsInfo['smsbody']) ? $paidSmsInfo['smsbody'] : null;
        $sms_b = strtr($sms_b, $var_replacement);
        $message = "$sms_b";

        $mobile = "88" . strval($agent['ag_mobile_no']);

        $sms = $obj->sendsms($mobile, $message);
        // sms end
    }

    echo json_encode([
        "success" => true,
        "message" => "Advance Payment Successfully"
    ]);
    exit();
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid Request"
    ]);
    exit();
}
