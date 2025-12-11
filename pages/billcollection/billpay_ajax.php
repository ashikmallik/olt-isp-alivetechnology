<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();
$userId = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;
header('Content-Type: application/json');

$day = date('d');
$year = date('Y');
$month = date('F');

$response = ['success' => false, 'message' => 'Invalid Request'];

// Get agent
$agent = $obj->getSingleData('tbl_agent', ['where' => ['ag_id', '=', $_POST['ag_id']]]);
$cus_id = $agent['cus_id'] ?? null;

// Get payment type
// $paymentType = $_POST['payment_type'] ?? 1;
$paymentType = !empty($_POST['payment_type']) ? $_POST['payment_type'] : 1;


try {


    if ((isset($_POST['amount']) && $_POST['amount'] > 0) ||
        (isset($_POST['discount']) && $_POST['discount'] > 0) ||
        (isset($_POST['ag_id']) && $_POST['ag_id'])
    ) {

        $postAmount = $_POST['amount'] ?? 0;
        $discription = $_POST['discription'] ?? "Bill collection";

        // Bill Payment
        if ($postAmount > 0) {
            $stmt = $obj->rawSql("
                    SELECT function_bill_update(
                        " . $_POST['ag_id'] . ", 
                        'billpay', 
                        " . $postAmount . ", 
                        0, 
                        '', 
                        $userId,
                        $paymentType,
                        '" . $discription . "'
                    ) AS function_bill_update
                ");
            $response['billPayment'] = 'Bill payment updated successfully';
        }

        // Discount
        if (isset($_POST['discount']) && $_POST['discount'] > 0) {
            $discount = $_POST['discount'];
            $stmt = $obj->rawSql("SELECT function_bill_update(" . $_POST['ag_id'] . ", 'discount', $discount, 0, '', $userId,$paymentType,'$discription') AS function_bill_update");
            $response['discount'] = 'Discount applied successfully';
            $discountInfo = " and a discount of $discount Taka has been applied";
        }

        // Activity Log including payment type
        $obj->createActivityLog(
            $userId,
            '2',
            '3',
            'tbl_agent',
            $_POST['ag_id'],
            3,
            $postAmount,
            "A payment of $postAmount Taka " . (isset($discountInfo) ? $discountInfo : '') . " for customer $cus_id.",
            null,
            null,
            false,
            false,
            null,
            $_POST['ag_id']
        );

        // Customer Activation
        if (isset($_POST['ag_id']) && $_POST['ag_id']) {
            $customerSingle = $obj->getSingleData('tbl_agent', ['where' => ['ag_id', '=', $_POST['ag_id']]]);
            if ($customerSingle) {
                $obj->enableSingleSecret($customerSingle['mikrotik_id'], $customerSingle['ip']);
                $response['customerActivation'] = 'Customer activated successfully';
            } else {
                $response['customerActivation'] = 'Customer not found';
            }
        }

        if (isset($_POST['smssend']) && $_POST["smssend"] == 'smssend') {

            $mobile = "88" . strval($agent['ag_mobile_no']);
            $customerName = $agent['ag_name'] ?? NULL;

            $totalDueAmount = $obj->rawSqlSingle("SELECT dueadvance FROM customer_billing WHERE agid = '$_POST[ag_id]'");

            $var_replacement = [
                '{PAID_DAY}' => $day,
                '{PAID_MONTH}' => $month,
                '{PAID_YEAR}' => $year,
                '{CUSTOMER_NAME}' => $customerName,
                '{PAID_AMOUNT}' => $postAmount,
                '{PACKAGE_NAME}' =>  $obj->rawSqlSingle("SELECT package_name FROM tbl_package WHERE net_speed = '$agent[mb]'")['package_name'],
                '{MONTHLY_BILL}' => $agent['taka'],
                '{CUSTOMER_ID}' => $agent['cus_id'],
                '{IP_ADDRESS}' => $agent['ip'],
                '{DUE_AMOUNT}' => $totalDueAmount['dueadvance'],
            ];

            $paidSmsInfo = $obj->details_by_cond("sms", "status='3'");
            $sms_b = $paidSmsInfo['smsbody'] ?? '';
            $sms_b = strtr($sms_b, $var_replacement);

            // var_dump($sms_b);

            $obj->sendsms($mobile, $sms_b);
        }

        $obj->notificationStore('Pay Operations processed successfully.', 'success');
        $response['success'] = true;
        $response['message'] = 'Operations processed successfully';

        // SMS send
    }
} catch (Exception $e) {
    $obj->notificationStore('Pay Operations processed Failed.', 'error');
    $response['success'] = false;
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
exit();
