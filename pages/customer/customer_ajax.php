<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

$where = [];
$wherecond = '';
$allAgentData = [];
$total_bill = $totalconnectionFee = $totalcustomer = 0;
$i = 1;

// Apply filters from AJAX request
if (isset($_GET['zone']) && !empty($_GET['zone'])) {
    $zone = $_GET['zone'];
    $wherecond .= " AND vw_agent.zone = $zone";
}
if (isset($_GET['sub_zone']) && !empty($_GET['sub_zone'])) {
    $szone = $_GET['sub_zone'];
    $wherecond .= " AND vw_agent.sub_zone = $szone";
}
if (isset($_GET['bid']) && !empty($_GET['bid'])) {
    $bid = $_GET['bid'];
    $wherecond .= " AND vw_agent.billing_person_id = $bid";
}
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $status = $_GET['status'];
    $wherecond .= " AND vw_agent.ag_status = $status";
}

if (isset($_GET['package_filter']) && !empty($_GET['package_filter'])) {
    $package_filter = $_GET['package_filter'];
    $wherecond .= " AND vw_agent.mb = '$package_filter'";
}

if (isset($_GET['select_package_filter']) && !empty($_GET['select_package_filter'])) {
    $select_package_filter = $_GET['select_package_filter'];
    $wherecond .= " AND vw_agent.mb = '$select_package_filter'";
}

if ($_GET['ag_status_filter'] == "active" || $_GET['ag_status_filter'] == "inactive" || $_GET['ag_status_filter'] == "free" || $_GET['ag_status_filter'] == "discontinue") {
    if ($_GET['ag_status_filter'] == 'active') {
        $statusVal = '1';
    } elseif ($_GET['ag_status_filter'] == 'inactive') {
        $statusVal = '0';
    } elseif ($_GET['ag_status_filter'] == 'free') {
        $statusVal = '2';
    } elseif ($_GET['ag_status_filter'] == 'discontinue') {
        $statusVal = '3';
    }

    $wherecond .= " AND vw_agent.ag_status = '$statusVal'";
}




if (isset($_GET['zone_filter']) && !empty($_GET['zone_filter'])) {
    $zone_filter = $_GET['zone_filter'];
    $wherecond .= " AND vw_agent.zone = '$zone_filter'";
}

if (isset($_GET['sub_zone_filter']) && !empty($_GET['sub_zone_filter'])) {
    $sub_zone_filter = $_GET['sub_zone_filter'];
    $wherecond .= " AND vw_agent.sub_zone = '$sub_zone_filter'";
}


if (isset($_GET['datefrom']) && isset($_GET['dateto']) && !empty($_GET['datefrom']) && !empty($_GET['dateto'])) {
    $dateFrom = date('Y-m-d', strtotime($_GET['datefrom']));
    $dateto = date('Y-m-d', strtotime($_GET['dateto']));
    $wherecond .= " AND vw_agent.entry_date  BETWEEN '$dateFrom' AND '$dateto'";
}




if (isset($_GET['isBillGenerate']) && $_GET['isBillGenerate'] == 1) {
    $generateMonth = date('n');
    $generateYear = date('Y');
    $wherecond .= " AND MONTH(customer_billing.generate_at) = $generateMonth AND YEAR(customer_billing.generate_at) = $generateYear";
}



$order_column = $_GET['order'][0]['column'] ?? '';  // Column index
$order_dir = $_GET['order'][0]['dir'] ?? '';  // Order direction (asc or desc)

$columns = [
    'ag_id',
    'ag_id',
    'cus_id',
    'ip',
    'ag_name',
    'ag_office_address',
    'ag_mobile_no',
    'mb',
    'taka',
    'connection_date',
    'zone_name',
    'FullName',
    'ag_status',
];
// Construct the ORDER BY clause dynamically
if (isset($order_column) && isset($columns[$order_column])) {
    if ($order_column == "11") {
        // If the column is FullName, order by created_by (or the corresponding column)
        $order_by = "vw_agent.billing_person_id " . $order_dir;
    } elseif ($order_column == "10") {
        // If the column is FullName, order by created_by (or the corresponding column)
        $order_by = "vw_agent.zone " . $order_dir;
    } else {
        // Else use the valid column name from the array
        $order_by = "vw_agent." . $columns[$order_column] . ' ' . $order_dir;
    }
} else {
    // Default order by id descending
    $order_by = "vw_agent.ag_id DESC";
}


// Apply search filter (if search is provided)
// Apply search filter (if search is provided)
// Apply search filter (if search is provided)
$search = $_GET['search']['value'] ?? '';
if (!empty($search)) {
    $wherecond .= " AND (
        vw_agent.cus_id LIKE '%$search%' OR 
        vw_agent.ip LIKE '%$search%' OR 
        vw_agent.mb LIKE '%$search%' OR 
        vw_agent.ag_mobile_no LIKE '%$search%' OR 
        vw_agent.taka LIKE '%$search%' OR 
        vw_agent.ag_name LIKE '%$search%' OR 
        vw_agent.ag_office_address LIKE '%$search%' OR
        tbl_zone.zone_name LIKE '%$search%' OR
        _createuser.FullName LIKE '%$search%' 
    )";
}


// var_dump($where);

// Pagination (start and length)
$start = $_GET['start'] ?? 0;
$length = $_GET['length'] ?? 10;



// Fetch filtered data with pagination
$allData = $obj->rawSql("SELECT *,tbl_zone.zone_name,_createuser.FullName FROM vw_agent 
LEFT JOIN tbl_zone ON vw_agent.zone = tbl_zone.zone_id 
LEFT JOIN _createuser ON vw_agent.billing_person_id = _createuser.UserId 
LEFT JOIN customer_billing ON vw_agent.ag_id = customer_billing.agid 
WHERE vw_agent.deleted_at is NULL $wherecond ORDER BY $order_by LIMIT $start,$length");

$totalData = count($obj->rawSql("SELECT * FROM vw_agent WHERE vw_agent.deleted_at is NULL")); // Total without filters
// $totalFiltered = count($obj->rawSql("SELECT * FROM vw_agent WHERE vw_agent.deleted_at is NULL")); // Total with applied filters

$totalFiltered = $obj->rawSqlSingle(
    "SELECT COUNT(*) as total 
    FROM vw_agent 
    LEFT JOIN tbl_zone ON vw_agent.zone = tbl_zone.zone_id 
    LEFT JOIN _createuser ON vw_agent.billing_person_id = _createuser.UserId 
    LEFT JOIN customer_billing ON vw_agent.ag_id = customer_billing.agid 
    WHERE vw_agent.deleted_at IS NULL $wherecond
"
)["total"];


$totalCustomerInfo = $obj->rawSqlSingle(
    "SELECT COUNT(*) as total, SUM(taka) as total_bill_amount
    FROM vw_agent 
    LEFT JOIN tbl_zone ON vw_agent.zone = tbl_zone.zone_id 
    LEFT JOIN _createuser ON vw_agent.billing_person_id = _createuser.UserId 
    LEFT JOIN customer_billing ON vw_agent.ag_id = customer_billing.agid 
    WHERE vw_agent.deleted_at IS NULL $wherecond
"
);
$totalCustomers = $totalCustomerInfo["total"];
$totalCustomerBill = $totalCustomerInfo["total_bill_amount"];



$totalActiveCustomersInfo = $obj->rawSqlSingle(
    "SELECT COUNT(*) as totalActiveCustomers, SUM(taka) as total_bill_amount
    FROM vw_agent 
    LEFT JOIN tbl_zone ON vw_agent.zone = tbl_zone.zone_id 
    LEFT JOIN _createuser ON vw_agent.billing_person_id = _createuser.UserId
    LEFT JOIN customer_billing ON vw_agent.ag_id = customer_billing.agid  
    WHERE vw_agent.deleted_at IS NULL AND ag_status = '1' $wherecond
"
);

$totalActiveCustomers = $totalActiveCustomersInfo["totalActiveCustomers"];
$totalActiveCustomerBill = $totalActiveCustomersInfo["total_bill_amount"];


$InActiveCustomersInfo = $obj->rawSqlSingle(
    "SELECT COUNT(*) as totalCustomers, SUM(taka) as total_bill_amount
    FROM vw_agent 
    LEFT JOIN tbl_zone ON vw_agent.zone = tbl_zone.zone_id 
    LEFT JOIN _createuser ON vw_agent.billing_person_id = _createuser.UserId 
    LEFT JOIN customer_billing ON vw_agent.ag_id = customer_billing.agid 
    WHERE vw_agent.deleted_at IS NULL 
    AND ag_status = '0' 
    $wherecond
"
);

$totalInActiveCustomers = $InActiveCustomersInfo["totalCustomers"];
$totalInActiveCustomerBill = $InActiveCustomersInfo["total_bill_amount"];


$freeCustomersInfo = $obj->rawSqlSingle(
    "SELECT COUNT(*) as totalCustomer, SUM(taka) as total_bill_amount
    FROM vw_agent 
    LEFT JOIN tbl_zone ON vw_agent.zone = tbl_zone.zone_id 
    LEFT JOIN _createuser ON vw_agent.billing_person_id = _createuser.UserId 
    LEFT JOIN customer_billing ON vw_agent.ag_id = customer_billing.agid 
    WHERE vw_agent.deleted_at IS NULL 
    AND ag_status = '2' 
    $wherecond
"
);

$totalFreeCustomers = $freeCustomersInfo["totalCustomer"];
$totalFreeCustomerBill = $freeCustomersInfo["total_bill_amount"];



foreach ($allData as $customer) {
    $total_bill += $customer['taka'];
    // $totalconnectionFee += $customer['connect_charge'];
    $totalcustomer += 1;

    // $bp = $obj->getSingleData('_createuser', [['UserId', '=', @$customer['billing_person_id']]]);
    // $customer['billingperson'] = @$bp['FullName'];
    $customer['sl'] = $i++;
    $allAgentData[] = $customer;
}

// Return JSON response for DataTable
echo json_encode([
    "draw" => intval($_GET['draw']), // Draw counter from DataTable
    "recordsTotal" => $totalData,    // Total records in database (without filters)
    "recordsFiltered" => $totalFiltered, // Total records after filtering
    "data" => $allAgentData,          // The actual data
    "totalbill" => $total_bill,          // The actual data
    "totalconnectionFee" => $totalconnectionFee,
    "totalCustomers" => $totalCustomers,
    "totalCustomerBill" => $totalCustomerBill,
    "totalActiveCustomers" => $totalActiveCustomers,
    "totalActiveCustomerBill" => $totalActiveCustomerBill,
    "totalInActiveCustomers" => $totalInActiveCustomers,
    "totalInActiveCustomerBill" => $totalInActiveCustomerBill,
    "totalFreeCustomers" => $totalFreeCustomers,
    "totalFreeCustomerBill" => $totalFreeCustomerBill,
], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
exit();
