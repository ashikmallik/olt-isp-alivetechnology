<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();



$activeCustomer = $obj->rawSqlSingle("SELECT COUNT(ag_id) AS activeCustomer FROM tbl_agent WHERE deleted_at IS NULL AND ag_status='1'")["activeCustomer"];
$inactiveCustomer = $obj->rawSqlSingle("SELECT COUNT(ag_id) AS inactiveCustomer FROM tbl_agent WHERE deleted_at IS NULL AND ag_status='0'")["inactiveCustomer"];
$freeCustomer = $obj->rawSqlSingle("SELECT COUNT(ag_id) AS freeCustomer FROM tbl_agent WHERE deleted_at IS NULL AND ag_status='2'")["freeCustomer"];
$deltedCustomer = $obj->rawSqlSingle("SELECT COUNT(ag_id) AS deltedCustomer FROM tbl_agent WHERE deleted_at IS NOT NULL")["deltedCustomer"];


$customerType = [
    "Active",
    "Inactive",
    "Free",
    "Deleted",
];


$customerCountData = [
    $activeCustomer,
    $inactiveCustomer,
    $freeCustomer,
    $deltedCustomer,
];



echo json_encode([
    "customerType" => $customerType,
    "customerCountData" => $customerCountData,
]);
exit();
