<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

$year = $_GET['year'];

$billGenerateAndCollection = $obj->rawSql("SELECT 
  months.month_year AS month,
  IFNULL(SUM(m.tbillgenerate), 0) AS total_generated,
  IFNULL(SUM(p.totalPrevDue), 0) AS totalPrevDue,
  IFNULL(SUM(a.totalCollected), 0) AS totalCollectedAmount
FROM (
  SELECT 'Jan-$year' AS month_year, 1 AS month_order UNION ALL
  SELECT 'Feb-$year', 2 UNION ALL
  SELECT 'Mar-$year', 3 UNION ALL
  SELECT 'Apr-$year', 4 UNION ALL
  SELECT 'May-$year', 5 UNION ALL
  SELECT 'Jun-$year', 6 UNION ALL
  SELECT 'Jul-$year', 7 UNION ALL
  SELECT 'Aug-$year', 8 UNION ALL
  SELECT 'Sep-$year', 9 UNION ALL
  SELECT 'Oct-$year', 10 UNION ALL
  SELECT 'Nov-$year', 11 UNION ALL
  SELECT 'Dec-$year', 12
) AS months
LEFT JOIN monthly_bill_making_check m 
  ON m.month_year = months.month_year
LEFT JOIN (
  SELECT 
    DATE_FORMAT(updated_at, '%b-%Y') AS month_year,
    SUM(previous_due_amount) AS totalPrevDue
  FROM tbl_previous_due
  WHERE YEAR(updated_at) = '$year'
  GROUP BY DATE_FORMAT(updated_at, '%b-%Y')
) p ON p.month_year = months.month_year
LEFT JOIN (
  SELECT 
    DATE_FORMAT(entry_date, '%b-%Y') AS month_year,
    SUM(acc_amount) AS totalCollected
  FROM tbl_account
  WHERE YEAR(entry_date) = '$year'
  GROUP BY DATE_FORMAT(entry_date, '%b-%Y')
) a ON a.month_year = months.month_year
GROUP BY months.month_year, months.month_order
ORDER BY months.month_order;");



$totalGenerated = array_map(function ($item) {
  return intval($item['total_generated']);
}, $billGenerateAndCollection);

$previousMonthDue = array_map(function ($item) {
  return intval($item['totalPrevDue']);
}, $billGenerateAndCollection);

$totalCollectedAmount = array_map(function ($item) {
  return intval($item['totalCollectedAmount']);
}, $billGenerateAndCollection);

echo json_encode([
  "status" => true,
  "totalGenerated" => $totalGenerated,
  "previousMonthDue" => $previousMonthDue,
  "totalCollectedAmount" => $totalCollectedAmount
]);
exit();
