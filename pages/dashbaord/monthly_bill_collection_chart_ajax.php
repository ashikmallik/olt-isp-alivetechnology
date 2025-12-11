<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();


$currentYear = $_GET["selectedYear"];

$CurrentYear = $obj->rawSql("SELECT SUM(acc_amount) as amount , MONTH(entry_date) as month FROM tbl_account WHERE  YEAR(entry_date)='$currentYear'  AND acc_type='3' GROUP BY MONTH(entry_date)");


$currentYearData = array_fill(0, 12, 0);

foreach ($CurrentYear as $data) {
    $currentYearData[$data['month'] - 1] = $data["amount"];
}

$currentData = implode(',', $currentYearData);

$allData = $currentYearData;
$maxValue = max($allData);
$maxData = $maxValue + ($maxValue * 0.5);

echo json_encode([
    "currentData" => $currentData,
    "currentYear" => $currentYear,
    "maxData" => $maxData,

]);
exit();
