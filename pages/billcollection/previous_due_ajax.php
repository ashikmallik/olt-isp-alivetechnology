<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

$where = [];
$wherecond = '';
$allAgentData = [];
$total  = $totalcustomer = 0;
$i = 1;

// Apply filters from AJAX request
if (isset($_GET['zone']) && !empty($_GET['zone'])) {
    $zone = $_GET['zone'];
    $wherecond .= " AND zone = $zone";
}
if (isset($_GET['bid']) && !empty($_GET['bid'])) {
    $bid = $_GET['bid'];
    $wherecond .= " AND billing_person_id = $bid";
}

if (isset($_GET['monthyear']) && !empty($_GET['monthyear'])) {
    $dateFrom = $_GET['monthyear'];
    $firstday = date('Y-m-d', strtotime($_GET['monthyear'] . "-01"));
    $lastday = date('Y-m-d', strtotime($_GET['monthyear'] . "-" . date('t', strtotime($_GET['monthyear'] . '-01'))));
    $wherecond .= " AND (date BETWEEN '$firstday' AND '$lastday')";
}

// Apply search filter (if search is provided)
$search = $_GET['search']['value'] ?? '';
if (!empty($search)) {
    $wherecond .= " AND (ag_name LIKE '%$search%' OR  cus_id LIKE '%$search%' OR  ip LIKE '%$search%' OR  mb LIKE '%$search%' OR  ag_mobile_no LIKE '%$search%' )";
}
// var_dump($where);

// Pagination (start and length)
$start = $_GET['start'] ?? 0;
$length = $_GET['length'] ?? 10;

// Fetch filtered data with pagination
$allData = $obj->rawSql("SELECT vw_agent.*, customer_billing.dueadvance,tbl_zone.zone_name , customer_billing.monthlybill, customer_billing.generate_at   FROM vw_agent left join customer_billing on customer_billing.agid = vw_agent.ag_id left join tbl_zone on tbl_zone.zone_id = vw_agent.zone WHERE vw_agent.deleted_at is NULL AND ag_status = 1 AND customer_billing.dueadvance > customer_billing.monthlybill $wherecond ORDER BY vw_agent.ag_id DESC LIMIT $start, $length");

$totalData = $obj->rawSqlSingle("SELECT COUNT(*) AS total FROM vw_agent left join customer_billing on customer_billing.agid = vw_agent.ag_id WHERE vw_agent.deleted_at is NULL AND ag_status = 1 AND customer_billing.dueadvance > customer_billing.monthlybill $wherecond")["total"];

$totalFiltered = $obj->rawSqlSingle("SELECT COUNT(*) AS total FROM vw_agent left join customer_billing on customer_billing.agid = vw_agent.ag_id WHERE vw_agent.deleted_at is NULL AND ag_status = 1 AND customer_billing.dueadvance > customer_billing.monthlybill $wherecond")["total"];


$totalPreviousDueSummary = $obj->rawSqlSingle(
    "SELECT 
        SUM(customer_billing.dueadvance) AS dueAdvacne,
        SUM(IF(customer_billing.dueadvance > vw_agent.taka, vw_agent.taka, customer_billing.dueadvance)) AS runningMonthDue,
        SUM(IF(customer_billing.dueadvance > vw_agent.taka, (customer_billing.dueadvance - vw_agent.taka), 0)) AS totalPreviousDue,
        COUNT(*) AS totalDueCustomers  
    FROM vw_agent left join customer_billing on customer_billing.agid = vw_agent.ag_id 
    left join tbl_zone on tbl_zone.zone_id = vw_agent.zone 
    WHERE vw_agent.deleted_at is NULL AND ag_status = 1 AND customer_billing.dueadvance > customer_billing.monthlybill $wherecond;"
);

foreach ($allData as $customer) {
    // $total += $customer['dueadvance'];
    $totalcustomer += 1;

    $bp = $obj->getSingleData('_createuser', ['where' => ['UserId', '=', @$customer['billing_person_id']]]);
    $customer['billingperson'] = @$bp['FullName'];
    $customer['sl'] = $i++;
    $allAgentData[] = $customer;
}

// Return JSON response for DataTable
echo json_encode([
    "draw" => intval($_GET['draw']),
    "recordsTotal" => $totalData,
    "recordsFiltered" => $totalFiltered,
    "data" => $allAgentData,
    // "totalbill" => $total,
    'totalPreviousDueSummary' => $totalPreviousDueSummary
]);
exit();
