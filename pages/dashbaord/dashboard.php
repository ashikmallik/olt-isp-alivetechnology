<?php
// require(realpath(__DIR__ . '/../../services/Model.php'));
// $obj = new Model();

$currentMonth = date("M"); // Get current month in short format (e.g., Dec)
$currentYear = date("Y"); // Get full year (e.g., 2024)

$currentNumericMonth = date('n'); // Numeric month
$currentNumericYear = date('Y');  // Full numeric year
$datee = date('Y-m-d');

$currentMonthYear = "$currentMonth-$currentYear"; // Combine into "Dec-2024"
// Call the stored procedure FOR BILL GENERATION
$billGenerateInfo = $obj->getSettingValue('billGenerate', 'billGenerate');
if ($billGenerateInfo == 'active') {
    $obj->rawSqlSingle("CALL `billgeneratemonthly`();");
    $obj->rawSqlSingle("DROP PROCEDURE IF EXISTS empty_database");
}
$obj->rawSqlSingle("CALL `clear_activity_log`(30);");

// Query the database
$monthBill = $obj->rawSqlSingle(
    "SELECT * 
FROM monthly_bill_making_check 
WHERE month_year = '$currentMonthYear';"
);
$bilgen = $monthBill['tbillgenerate'] ?? 0;
$due = $monthBill['tdue'] ?? 0;
$totalDue = $bilgen + $due;


// Current Month Collection

$get_all_collection = $obj->get_all_income_with_condition($currentNumericMonth, $currentNumericYear, 'AND (acc_type=2 OR acc_type=3 OR acc_type=4 OR acc_type=5 )');

// print_r($get_all_collection);

$get_connection_charge = $obj->get_all_income_with_condition($currentNumericMonth, $currentNumericYear, 'AND  acc_type=4');

// var_dump($get_all_collection);
// exit();

$totalBillSummary = $obj->rawSqlSingle(
    "SELECT 
            SUM(customer_billing.dueadvance) AS dueAdvacne,
            SUM(IF(customer_billing.dueadvance > vw_agent.taka, vw_agent.taka, customer_billing.dueadvance)) AS runningMonthDue,
            SUM(IF(customer_billing.dueadvance > vw_agent.taka, (customer_billing.dueadvance - vw_agent.taka), 0)) AS totalPreviousDue,
            COUNT(*) AS totalDueCustomers
        FROM vw_agent 
        left join customer_billing on customer_billing.agid = vw_agent.ag_id 
        left join _createuser ON vw_agent.billing_person_id = _createuser.UserId 
        left join tbl_zone on tbl_zone.zone_id = vw_agent.zone WHERE vw_agent.deleted_at is NULL AND customer_billing.dueadvance > 0 AND vw_agent.ag_status = 1;"
);
