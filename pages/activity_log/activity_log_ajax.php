<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

$where = [];
$wherecond = '';
$i = 1;

if (isset($_GET['activity_type']) && !empty($_GET['activity_type'])) {
    $activity_type = $_GET['activity_type'];
    $wherecond .= " AND activity_logs.action_related = $activity_type";
}

if (isset($_GET['bid']) && !empty($_GET['bid'])) {
    $bid = $_GET['bid'];
    $wherecond .= " AND activity_logs.created_by = $bid";
}


if (isset($_GET['datefrom']) && isset($_GET['dateto']) && !empty($_GET['datefrom']) && !empty($_GET['dateto'])) {
    $dateFrom = date('Y-m-d', strtotime($_GET['datefrom']));
    $dateTo = date('Y-m-d', strtotime($_GET['dateto']));

    $wherecond .= " AND DATE(activity_logs.created_at) BETWEEN '$dateFrom' AND '$dateTo'";
}


$order_column = $_GET['order'][1]['column'] ?? '';
$order_dir = $_GET['order'][1]['dir'] ?? '';

$columns = [
    'description',
    'date',
    'user_name'
];


// // Construct the ORDER BY clause dynamically
if (isset($order_column) && isset($columns[$order_column])) {
    if ($order_column == "0") {
        // If the column is FullName, order by created_by (or the corresponding column)
        $order_by = "activity_logs.created_at " . $order_dir;
    }
} else {
    // Default order by id descending
    $order_by = "activity_logs.created_at DESC";
}

$search = $_GET['search']['value'] ?? '';
if (!empty($search)) {
    $wherecond .= " AND (
        activity_logs.description LIKE '%$search%' OR 
        activity_logs.created_at LIKE '%$search%' OR 
        _createuser.FullName LIKE '%$search%'
    )";
}


// var_dump($where);

// Pagination (start and length)
$start = $_GET['start'] ?? 0;
$length = $_GET['length'] ?? 10;

// // Fetch filtered data with pagination
$allData = $obj->rawSql(
    "SELECT activity_logs.description as description, activity_logs.created_at AS date,activity_logs.ip_address AS ip, activity_logs.device_info AS device_info, activity_logs.action_type AS action_type, _createuser.FullName AS user_name 
    FROM activity_logs 
    LEFT JOIN _createuser ON activity_logs.created_by = _createuser.UserId
    WHERE activity_logs.deleted_at IS NULL $wherecond  ORDER BY $order_by LIMIT $start,$length"
);

$totalData = $obj->rawSqlSingle(
    "SELECT COUNT(*) AS totalData
    FROM activity_logs 
    LEFT JOIN _createuser ON activity_logs.created_by = _createuser.UserId
    WHERE activity_logs.deleted_at IS NULL $wherecond"
)["totalData"];

$totalFiltered = $obj->rawSqlSingle(
    "SELECT COUNT(*) AS totalFiltered
    FROM activity_logs 
    LEFT JOIN _createuser ON activity_logs.created_by = _createuser.UserId
    WHERE activity_logs.deleted_at IS NULL $wherecond"
)["totalFiltered"];

// Return JSON response for DataTable
echo json_encode([
    "draw" => intval($_GET['draw']), // Draw counter from DataTable
    "recordsTotal" => $totalData,    // Total records in database (without filters)
    "recordsFiltered" => $totalFiltered, // Total records after filtering
    "data" => $allData,
    'date' => $_GET['datefrom']
]);
exit();
