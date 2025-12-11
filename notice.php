<?php
header("Access-Control-Allow-Origin: *"); // সব ডোমেইন থেকে অ্যাকসেস অনুমোদন
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$day = date('d');

$dd = date('d');
$m = date('m');
$y = date('Y');

require(__DIR__ . '/services/Model.php');


$obj = new Model();
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET' && $_GET['notice_info']) {
    $data = $obj->rawSqlSingle("SELECT * FROM countdown_notice WHERE id = '1'");
    echo json_encode($data);
}

$input = json_decode(file_get_contents("php://input"), true);
if ($method == 'POST' && isset($input['UPDATE_NOTICE'])) {
    $data = $obj->rawSqlSingle(
        "UPDATE countdown_notice 
        SET 
        bill_date = '$input[bill_date]',
        notice_display_before = '$input[notice_display_before]',
        notice_displayable = '$input[notice_displayable]',
        forcefully_stop = '$input[forcefully_stop]',
        permanently_stop = '$input[permanently_stop]',
        complete_shutdown = '$input[complete_shutdown]',
        notice = '$input[notice]' 
        WHERE id = '1'
        "
    );
    echo true;
}


if ($method == 'POST' && isset($input['HIDE_NOTICE'])) {
    $data = $obj->rawSqlSingle(
        "UPDATE countdown_notice 
        SET 
        notice_displayable = '2',
        complete_shutdown = '2'
        WHERE id = '1'
        "
    );
    echo true;
}


if ($method == 'GET' && isset($_GET['customer_info'])) {
    $data = $obj->rawSql(
        "SELECT 
            CASE ag_status
                WHEN 0 THEN 'Inactive'
                WHEN 1 THEN 'Active'
                WHEN 2 THEN 'Free'
                WHEN 3 THEN 'Discontinue'
            END AS status_name,
            COUNT(*) AS total_agents
        FROM tbl_agent
        WHERE deleted_at IS NULL
        GROUP BY ag_status;
        ");
    echo json_encode($data);
}



if($method == 'POST' && isset($input['insert_remarks'])){
    $remarks = $obj->insertData('admin_remarks', ['description' => $input['description'], 'entry_by' => $input['entry_by']]);
    
    echo $remarks;
}

if ($method == 'GET' && isset($_GET['remarks_info'])) {
    $data = $obj->rawSql("SELECT * FROM admin_remarks");
    echo json_encode($data);
}


if ($method == 'POST' && isset($_GET['delete_remarks_id'])) {
    $delete_remarks_id = $_GET['delete_remarks_id'];
    $obj->rawSql("DELETE FROM admin_remarks WHERE id=$delete_remarks_id");
    echo "1";
}


if($method == 'GET' && isset($_GET['sms_info'])){
    $apiKey = $obj->getSettingValue('sms', 'pass');
    echo strlen($apiKey) > 15 ? "true" : "false";
}


if($method == 'GET' && isset($_GET['activity_log'])){
    $activityLogs = $obj->rawSql('SELECT * FROM activity_logs ORDER BY id DESC LIMIT 10');
    echo json_encode($activityLogs);
}
