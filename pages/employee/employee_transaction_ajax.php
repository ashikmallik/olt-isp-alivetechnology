<?php
session_start();
header('Content-Type: application/json'); // Ensure the response is JSON
require(realpath(__DIR__ . '/../../services/Model.php'));

$obj = new Model();
$wherecond = 'tbl_employee_transaction.id != 0';

//====== Date Filter ======
$dateft = isset($_GET['date']) ? $_GET['date'] : '';
if ($dateft != '') {
    $datearay = explode('/', $dateft);
    $dateFrom = date('Y-m-d', strtotime($datearay[0]));
    $dateto = date('Y-m-d', strtotime($datearay[1]));
    $wherecond .= " AND (tbl_employee_transaction.created_at BETWEEN '$dateFrom' AND '$dateto')";
}

//====== Final Condition with GROUP BY ======
$wherecond .= " GROUP BY tbl_employee_transaction.employee_id";

//====== Fetch Data ======
$allEmployeeTransaction = $obj->view_selected_field_by_cond_left_join(
    "tbl_employee_transaction",
    "tbl_employee",
    "employee_id",
    "id",
    "SUM(tbl_employee_transaction.salary_amount) as total_salary_amount,
     SUM(tbl_employee_transaction.conveyance) as total_conveyance,
     SUM(tbl_employee_transaction.received_amount) as total_received_amount,
     SUM(tbl_employee_transaction.punishment) as total_punishment,
     tbl_employee.id as employee_id,
     tbl_employee.employee_name,
     tbl_employee.employee_mobile_no,
     tbl_employee.employee_address",
    "*",
    $wherecond
);

//====== Process Data for JSON ======
$data = [];
foreach ($allEmployeeTransaction as $row) {
    $due = ($row['total_salary_amount'] + $row['total_conveyance']) - ($row['total_received_amount'] + $row['total_punishment']);
    $data[] = [
        'employee_id' => $row['employee_id'],
        'employee_name' => $row['employee_name'],
        'employee_mobile_no' => $row['employee_mobile_no'],
        'employee_address' => $row['employee_address'],
        'total_salary_amount' => number_format($row['total_salary_amount']),
        'total_conveyance' => number_format($row['total_conveyance']),
        'total_received_amount' => number_format($row['total_received_amount']),
        'total_punishment' => number_format($row['total_punishment']),
        'employee_due' => ($due < 0 ? '(Advance) ' : '') . number_format(abs($due)),
        'action' => '<button type="button" class="btn btn-success waves-effect waves-light edit"
                     data-bs-toggle="modal" data-bs-target="#con-close-modal"
                     data-id="' . $row['id'] . '" 
                     data-employee="' . $row['employee_name'] . '">Pay Salary</button>'
    ];
}

//====== Return JSON ======
echo json_encode(['data' => $data]);
