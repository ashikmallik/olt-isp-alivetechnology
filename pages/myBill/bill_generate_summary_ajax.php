<?php
session_start();
header('Content-Type: application/json'); // Ensure the response is JSON
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

// $monthYear = date('M-Y');
$month = $_GET['month'];
$year = $_GET['year'];

// $dateObj = DateTime::createFromFormat('!m Y', $month . ' ' . $year);
// $monthYear = $dateObj->format('M-Y');

$dateObj = DateTime::createFromFormat('!m Y', $month . ' ' . $year);

if (!$dateObj) {
  echo json_encode(["status" => false, "error" => "Invalid month or year", "input_month" => $month, "input_year" => $year]);
  exit;
}

$monthYear = $dateObj->format('M-Y');


$generatedBillInfo = $obj->rawSqlSingle("SELECT * FROM monthly_bill_making_check WHERE month_year = '$monthYear'");

$totalPreviousDueAmount = $obj->rawSqlSingle("SELECT SUM(previous_due_amount) AS totalPreDueAmount FROM tbl_previous_due WHERE MONTH(updated_at) = '$month' AND YEAR(updated_at) = '$year'")['totalPreDueAmount']  ?? 0;

$totalCollectedBillAmount = $obj->rawSqlSingle(
  "SELECT SUM(acc_amount) AS totalCollectedBill
    FROM tbl_account 
    WHERE acc_type = 3 AND MONTH(entry_date) = '$month' AND YEAR(entry_date) = '$year'"
)['totalCollectedBill'] ?? 0;




if ($generatedBillInfo == false) {
  echo json_encode(["status" => false, "data" => $generatedBillInfo, 'monthYear' => $monthYear]);
  exit;
}


$totalPaidInfo = 0;
if ($month != date('n')) {
  // previous month paid
  $prevMonth = $month == 12 ? 1 : $month + 1;
  $prevYear = $month == 12 ? $year + 1 : $year;
  $prevDueCustomer = $obj->rawSqlSingle("SELECT COUNT(*) AS totalDueCus FROM tbl_previous_due WHERE MONTH(updated_at) = '$prevMonth' AND YEAR(updated_at) = '$prevYear'")['totalDueCus'] ?? 0;
  $totalPaidInfo = intval($generatedBillInfo["tactivec"]) - intval($prevDueCustomer);
} else {
  $totalPaidInfo = $obj->rawSqlSingle(
    "SELECT COUNT(*) AS totalFullPaidCustomers 
        FROM customer_billing AS billing
        JOIN tbl_agent  agent ON agent.ag_id = billing.agid
        WHERE agent.deleted_at IS NULL AND agent.ag_status = 1 AND billing.dueadvance <= 0 AND billing.totalpaid > 0"
  )["totalFullPaidCustomers"];
}

echo json_encode(["status" => true, "data" => $generatedBillInfo, 'totalPreviousDueAmount' => intval($totalPreviousDueAmount), 'totalCollectedBill' => intval($totalCollectedBillAmount), 'monthYear' => $monthYear, 'totalPaidInfo' => intval($totalPaidInfo)]);
exit();
