<?php

session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

// $currentMonth = date("M"); // Get current month in short format (e.g., Dec)
$currentMonth = $_GET["month"]; // Get current month in short format (e.g., Dec)
// $currentYear = date("Y"); // Get full year (e.g., 2024)
$currentYear = $_GET["year"]; // Get full year (e.g., 2024)

$currentMonthYear = "$currentMonth-$currentYear"; // Combine into "Dec-2024"

// Query the database
$monthlyRevenue = $obj->rawSqlSingle(
    "SELECT SUM(`acc_amount`) AS totalRevenue 
    FROM `tbl_account` 
    WHERE `acc_type` != 1 AND MONTH(`entry_date`) = '$currentMonth' AND YEAR(`entry_date`) = '$currentYear';"
);

$totalMonthlyRevenue = $monthlyRevenue['totalRevenue'] ?? 0;

$getExpense = $obj->rawSqlSingle(
    "SELECT SUM(`acc_amount`) AS totalExpense 
    FROM `tbl_account` 
    WHERE `acc_type` = 1 AND MONTH(`entry_date`) = '$currentMonth' AND YEAR(`entry_date`) = '$currentYear';"
);

$totalExpenseAmount = $getExpense['totalExpense'] ?? 0;

echo json_encode([
    "totalRevenue" => $totalMonthlyRevenue ?? 0,
    "totalExpense" => $totalExpenseAmount ?? 0
]);
