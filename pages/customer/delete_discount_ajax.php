<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();



// Validation (optional but recommended)
if (!empty($_POST['customerId']) && !empty($_POST['amount']) && !empty($_POST['bonus_id'])) {
    $ag_id = $_POST['customerId'];
    $bonus_id = intval($_POST['bonus_id']);
    $amount = floatval($_POST['amount']);
    $ag_id = intval($_POST['customerId']);
    $obj->rawSqlSingle("DELETE FROM bonus WHERE id = '$bonus_id'");
    $customer  = $obj->rawSqlSingle("SELECT tbl_agent.cus_id FROM tbl_agent WHERE ag_id = '$ag_id'");
    $obj->rawSqlSingle(
        "UPDATE customer_billing 
            SET totaldiscount =  totaldiscount - $amount,
            dueadvance = dueadvance + $amount 
            WHERE agid = $ag_id"
    );
    $obj->createActivityLog(
        $_SESSION['userid'],
        '4',
        '3',
        'bonus',
        $bonus_id,
        null,
        $amount,
        'Deleted Discount of ' . $amount . ' Taka for customer ID: ' . $customer['cus_id'],
        null,
        null,
        false,
        false,
        null,
        $ag_id
    );


    $_SESSION['discount_tab'] = true;

    echo json_encode([
        "success" => true,
        'data' => $customer,
        'bonus_id' => $bonus_id,
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
