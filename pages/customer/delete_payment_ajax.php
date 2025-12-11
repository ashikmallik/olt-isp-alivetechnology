<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();


if ($_POST['acc_type'] == '3') {
    $obj->rawSql("SELECT function_bill_update(" . $_POST['customer_id'] . ", 'paybilldeleted', " . $_POST['acc_amount'] . ", " . $_POST['acc_id'] . ", '', 0, 0,'') AS function_bill_update");
} else {
    $obj->rawSqlSingle("DELETE FROM tbl_account WHERE acc_id = " . $_POST['acc_id']);
}


// create activity log for delete payment info
$obj->createActivityLog(
    $_SESSION['userid'],
    '4',
    '3',
    'tbl_account',
    $_POST['acc_id'],
    $_POST['acc_type'],
    $_POST['acc_amount'] ?? 0,
    'Deleted payment of ' . $_POST['acc_amount'] . ' Taka for customer ID: ' . $_POST['cus_id'],
    null,
    null,
    false,
    false,
    null,
    $_POST['customer_id']
);

// Return JSON response for DataTable
echo json_encode([
    "success" => true
]);
exit();
