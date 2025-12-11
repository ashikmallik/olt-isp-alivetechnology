<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($_POST['selectedCustomerIds']) && is_array($_POST['selectedCustomerIds'])) {
    $selectedCustomerIds = $_POST['selectedCustomerIds'];
    // select Zone
    if(isset($_POST['selectedZone'])){
    $selectedZone = $_POST['selectedZone'];
    foreach ($selectedCustomerIds as $key => $value) {
        $obj->rawSql("UPDATE tbl_agent SET zone = '$selectedZone' WHERE ag_id = '$value'");
    }
    header('Content-Type: application/json');
    echo json_encode([
        'status'=>'success',
        'zone'=>$selectedZone,
        'ids'=>$selectedCustomerIds
    ]);
    exit;   
    }
    // package select
    if(isset($_POST['selectedPackage'])){
        $selectedPackage = $_POST['selectedPackage'];
        foreach ($selectedCustomerIds as $key => $value) {
            $obj->rawSql("UPDATE tbl_agent SET mb = '$selectedPackage' WHERE ag_id = '$value'");
        }
        header('Content-Type: application/json');
        echo json_encode([
            'status'=>'success',
            'package'=>$selectedPackage,
            'ids'=>$selectedCustomerIds
        ]);
        exit;
    }
    
}

$data = $obj->rawSql('SELECT ag_id ,cus_id, ip, ag_name, ag_office_address, ag_mobile_no, mb, zone_name 
                      FROM vw_agent 
                      LEFT JOIN tbl_zone ON vw_agent.zone = tbl_zone.zone_id 
                      WHERE vw_agent.deleted_at IS NULL');

if (!$data) {
    $data = [];
}
echo json_encode([
    "data" => $data
], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);

exit;