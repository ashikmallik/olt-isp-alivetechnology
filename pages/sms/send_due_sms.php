<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;
$notification = "";
//taking month and years

$day = date('M-Y');
$whereCond = '';

$obj->Update_data("sms", ['smshead' => isset($_GET['excPartialPaid']) && $_GET['excPartialPaid'] == 'active' ? 'active' : 'inactive'], " status='1' ");

if (isset($_GET['excPartialPaid']) && $_GET['excPartialPaid'] == 'active') {
    $whereCond = 'AND tbl_agent.bill_status != 1';
}


if (isset($_GET['zone']) && !empty($_GET['zone'])) {
    $zone = $_GET['zone'];

    if ($zone == 'x') {

        // $smsClient = $obj->view_all_by_cond("tbl_agent", "ag_status='1' AND pay_status='1' AND due_status='0' ");
        $smsClient = $obj->rawSql(
            "SELECT * 
            FROM tbl_agent 
            LEFT JOIN customer_billing ON tbl_agent.ag_id = customer_billing.agid 
            WHERE tbl_agent.ag_status='1' AND customer_billing.dueadvance > 0 AND tbl_agent.deleted_at is NULL $whereCond"
        );
    } else {


        // $smsClient = $obj->view_all_by_cond("tbl_agent", "ag_status='1' and pay_status='1' AND due_status='0'  AND zone = $zone");

        $smsClient = $obj->rawSql(
            "SELECT * 
            FROM tbl_agent 
            LEFT JOIN customer_billing ON tbl_agent.ag_id = customer_billing.agid 
            WHERE tbl_agent.ag_status='1' AND customer_billing.dueadvance > 0 AND tbl_agent.deleted_at is NULL AND zone = $zone $whereCond;"
        );
    }
} else {


    die();
}


$i = '0';
$total_due_amount = 0;
if (isset($_GET['submitbtn']) && $_GET['randcheck'] == $_SESSION['rand']) {
    $_SESSION['rand'] = 2;
    $smsArray = [];
    $smsI = 0;
    foreach ($smsClient as $value) {
        $i++;
        $mobile = isset($value['ag_mobile_no']) ? $value['ag_mobile_no'] : NULL;
        $ip = isset($value['ip']) ? $value['ip'] : NULL;
        $cid = isset($value['cus_id']) ? $value['cus_id'] : NULL;
        $agid = isset($value['ag_id']) ? $value['ag_id'] : NULL;
        $ag_name = isset($value['ag_name']) ? $value['ag_name'] : NULL;

        // $all_d = $obj->get_customer_dues(isset($value['ag_id']) ? $value['ag_id'] : NULL);
        $all_duee = $obj->details_by_cond("customer_billing", "agid='$agid'");
        $all_d = $all_duee['dueadvance'];
        $value1 = $obj->details_by_cond("sms", "status='1'");

        $sms_h = isset($value1['smshead']) ? $value1['smshead'] : NULL;
        $sms_b = isset($value1['smsbody']) ? $value1['smsbody'] : NULL;

        $var_replacement = [
            '{CUSTOMER_NAME}' => $ag_name,
            '{DUE_AMOUNT}' => $all_d,
            '{CUSTOMER_ID}' => $cid,
            '{IP_ADDRESS}' => $ip,
        ];

        $sms_b = strtr($sms_b, $var_replacement);
        $mass = "$sms_b";
        // $mass = "$sms_h $ag_name আপনার customer id: $cid ip: $ip, Due: $all_d $sms_b ";
        $mobilenum = "88" . $mobile;
        //     $obj->sendsms($mass, $mobilenum);

        //   $agentId = isset($value['ag_id']) ? $value['ag_id'] : NULL;
        //   $obj->Update_data("tbl_agent", ['sms_sent' => 1], "where ag_id='$agentId'");


        $smsArray[$smsI++] = ["to" => $mobilenum, "message" => $mass];
    }


    $obj->sms_send($smsArray);
}


?>

<script>
    window.location = "?page=due_sms";
</script>