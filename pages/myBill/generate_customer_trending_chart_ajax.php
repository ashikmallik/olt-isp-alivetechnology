<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

$year = $_GET['year'];

$generatedCustomerTrend = $obj->rawSql(
  "SELECT months.month_year AS month,
  IFNULL(m.tactivec, 0) AS totalActive,
  IFNULL(m.tinactivec, 0) AS totalInActive,
  IFNULL(m.tdiscontinuec, 0) AS TotalFree, -- tdiscontinuec means free
  IFNULL(m.tfreec, 0) AS TotalDiscontinue -- tfreec means Discontinue
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
GROUP BY months.month_year, months.month_order
ORDER BY months.month_order;"
);

$totalActive = array_map(function ($item) {
  return $item['totalActive'];
}, $generatedCustomerTrend);

$totalFree = array_map(function ($item) {
  return $item['TotalFree'];
}, $generatedCustomerTrend);

$totalInactive = array_map(function ($item) {
  return $item['totalInActive'];
}, $generatedCustomerTrend);

$totalDiscontinue = array_map(function ($item) {
  return $item['TotalDiscontinue'];
}, $generatedCustomerTrend);

echo json_encode([
  "status" => true,
  "totalActive" => $totalActive,
  "totalFree" => $totalFree,
  "totalInactive" => $totalInactive,
  "totalDiscontinue"  => $totalDiscontinue
]);
exit();
