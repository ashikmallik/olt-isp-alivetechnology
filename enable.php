<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

require(__DIR__.'/services/Model.php');

    
$obj = new Model();


if (isset($_GET["ag_id"]) && $_GET["ag_id"]) {
            $customerSingle = $obj->getSingleData('tbl_agent', ['where' => ['ag_id', '=', $_GET['ag_id']]]);
            // var_dump($customerSingle);
            if ($customerSingle) {
                // for sms
                $mobile = "88" . strval($customerSingle['ag_mobile_no']);
        $customerName = isset($customerSingle['ag_name']) ? $customerSingle['ag_name'] : NULL;
        $cusbill = isset($_POST['amount']) ? $_POST['amount'] : NULL;
         $iid = $_GET['ag_id'];
        $totalDueAmount = $obj->rawSqlSingle("SELECT dueadvance FROM customer_billing WHERE agid = $iid");
        $cusbill = isset($_GET['amount']) ? $_GET['amount'] : NULL;
        // SMS SEND
        $var_replacement = [
            '{CUSTOMER_NAME}' => $customerName,
            '{PAID_AMOUNT}' => $cusbill,
            '{PACKAGE_NAME}' => $customerSingle['mb'],
            '{MONTHLY_BILL}' => $customerSingle['taka'],
            '{CUSTOMER_ID}' => $customerSingle['cus_id'],
            '{IP_ADDRESS}' => $customerSingle['ip'],
            '{DUE_AMOUNT}' => $totalDueAmount['dueadvance'],
        ];
        $paidSmsInfo = $obj->details_by_cond("sms", "status='3'");
        $sms_b = isset($paidSmsInfo['smsbody']) ? $paidSmsInfo['smsbody'] : NULL;
        $sms_b = strtr($sms_b, $var_replacement);
        $message = "$sms_b";
        $sms = $obj->sendsms($mobile, $message);
        
                // for enable
                $obj->enableSingleSecret($customerSingle['mikrotik_id'], $customerSingle['ip']);
            }
        }
?>