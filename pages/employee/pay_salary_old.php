<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
// $userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$obj->rawSqlSingle("CALL generate_monthly_salary('$date')");

$allEmployeeTransaction = $obj->view_selected_field_by_cond_left_join("tbl_employee_transaction", 'tbl_employee',
    'employee_id', 'id', 'SUM(tbl_employee_transaction.salary_amount) as total_salary_amount, 
    SUM(tbl_employee_transaction.conveyance) as total_conveyance, 
    SUM(tbl_employee_transaction.received_amount) as total_received_amount, 
    SUM(tbl_employee_transaction.punishment) as total_punishment', '*', 'tbl_employee_transaction.id != 0 GROUP BY tbl_employee_transaction.`employee_id`');


$employeeSalaryReceiveType = 1;
$employeeSalaryDueType = 0;

// $company_give_payment_to_employee = $obj->getAccTypeId('company_give_payment_to_employee');


if (isset($_POST['submit_employee_payment'])) {

     $employee_data = $obj->getSingleData("tbl_employee", "id = " . $_POST['employee_id'] . "");
     
    if( !empty($_POST['employee_payment'])){

        $form_tbl_accounts = array(
            'acc_description' => "Company give payment to Employee",
            'acc_amount' => $_POST['employee_payment'],
            'acc_type' => 1,
            // 'agent_id' => $_POST['employee_id'],
            'acc_head' => 2222,
            'entry_by' => $userId,
            'entry_date' => date('Y-m-d'),
            'update_by' => $userId
        );
        $tbl_accounts_add = $obj->Insert_data("tbl_account", $form_tbl_accounts, " ");
        //  var_dump($form_tbl_accounts);
    //   exit;
    }

    $form_tbl_employee_transaction = array(
        'employee_id' => $_POST['employee_id'],
        'salary_amount' => $_POST['employee_conveyance'],
        'conveyance' => $_POST['employee_conveyance'],
        'received_amount' => $_POST['employee_payment'],
        'received_due' => $employeeSalaryReceiveType,
        'punishment' => $_POST['employee_punishment'],
        'accounts_id' => !empty($_POST['employee_payment']) ? $tbl_accounts_add : 0,
        'created_at' => date('Y-m-d'),
    );
    
    if ($obj->Insert_data("tbl_employee_transaction", $form_tbl_employee_transaction)) {
        $obj->notificationStore('Employee Payment Stored Successfully ', 'success');
        echo '<script>window.location.href=window.location.href;</script>';
    } else {
        $obj->notificationStore('Employee Payment Stored Failed ');
        echo '<script>window.location.href=window.location.href;</script>';
    }

}

?>

<div class="row">
    <div class="col-md-12 padding_5_px">
        <!--<?php $obj->notificationShowRedirect(); ?>-->
    </div>
</div>
<div class="row">
    <div class="col-md-12 bg-teal-800">
        <h4 class="col-md-9">View Employee's Transaction</h4>
        <button type="submit" class="btn btn-primary btn-sm pull-right" onclick="printDiv('print_transaction')">Print Transaction
        </button>
    </div>
</div>
<hr>
<div class="row" style="font-size:12px;">
    <div class="col-md-12">
        <table class="table table-responsive table-bordered table-hover " id="datatable">
            <thead>
            <tr class="bg-teal-800">
                <th class="col-md-1">Employee ID</th>
                <th class="col-md-2">Employee Name</th>
                <th class="col-md-1">Mobile No</th>
                <th class="col-md-2">Address</th>
                <th class="col-md-1"> Total Salary Amount</th>
                <th class="col-md-1"> Total Conveyance Amount</th>
                <th class="col-md-1"> Total Received Amount</th>
                <th class="col-md-1">Total Punishment Amount</th>
                <th class="col-md-1">Employee Due From Company</th>
                <th class="col-md-1">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 0;
            $total_salary = 0;
            $total_conveyance_amount = 0 ;
            $total_received_amount = 0;
            $total_punishment_amount = 0;
            $total_employee_due_from_company = 0;
            foreach ($allEmployeeTransaction as $employeePayment) {
                $total_salary = $employeePayment['total_salary_amount'] + $total_salary;
                $total_conveyance_amount = $employeePayment['total_conveyance'] + $total_conveyance_amount;
                $total_received_amount = $employeePayment['total_received_amount'] + $total_received_amount;
                $total_punishment_amount = $employeePayment['total_punishment'] + $total_punishment_amount;
                ?>
                <tr>
                    <td class="text-center"> <?php echo $employeePayment['employee_id'] ?> </td>
                    <td class=""><a class="btn btn-success" href="?page=employee_ladger&employeeid=<?php echo $employeePayment['id'] ?>"><?php echo $employeePayment['employee_name'] ?></a>
                    </td>
                    <td class="text-center"> <?php echo $employeePayment['employee_mobile_no']  ?></td>
                    <td class=""> <?php echo $employeePayment['employee_address'] ?> </td>
                    <td class="text-right"> <?php echo number_format($employeePayment['total_salary_amount']) ?> </td>
                    <td class="text-right"> <?php echo number_format($employeePayment['total_conveyance']) ?> </td>
                    <td class="text-right"> <?php echo number_format($employeePayment['total_received_amount']) ?> </td>
                    <td class="text-right"> <?php echo number_format($employeePayment['total_punishment']) ?> </td>
                    <?php
                    $totalEmployeeDue = ($employeePayment['total_salary_amount'] + $employeePayment['total_conveyance'])
                        - ($employeePayment['total_received_amount'] + $employeePayment['total_punishment']);
                    $total_employee_due_from_company = $totalEmployeeDue + $total_employee_due_from_company;
                    ?>

                    <td class="text-right"> <?php echo ($totalEmployeeDue < 0) ? '(Advance) ' : '';
                        echo number_format(abs($totalEmployeeDue)) ?> </td>

                    <td class="text-center action-btn">
                        <button type="button" class="btn btn-success waves-effect waves-light edit" data-bs-toggle="modal" data-bs-target="#con-close-modal" data-id="<?php echo $employeePayment['id'] ?>" data-employee="<?php echo $employeePayment['employee_name'] ?>"><i class="mdi mdi-plus me-1"></i> Pay Salary</button>
            </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <th class="text-center">Total</th>
                <th class="text-center "></th>
                <th class="text-center "></th>
                <th class="text-center "></th>
                <th class="text-right "><?php echo number_format($total_salary);  ?></th>
                <th class="text-right "><?php echo number_format($total_conveyance_amount);  ?></th>
                <th class="text-right "><?php echo number_format($total_received_amount);  ?></th>
                <th class="text-right "><?php echo number_format($total_punishment_amount);  ?></th>
                <th class="text-right "><?php echo number_format($total_employee_due_from_company);  ?></th>
                <th class="text-center "></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="row" style="font-size:12px; display: none" id="print_transaction">
    <div class="col-md-12">
        <h3 class="text-center">Employee Transaction</h3>
        <table class="table table-responsive table-bordered table-hover " id="datatable">
            <thead>
            <tr class="bg-teal-800">
                <th class="col-md-1">Employee ID</th>
                <th class="col-md-2">Employee Name</th>
                <th class="col-md-1">Mobile No</th>
                <th class="col-md-2">Address</th>
                <th class="col-md-1"> Total Salary Amount</th>
                <th class="col-md-1"> Total Conveyance Amount</th>
                <th class="col-md-1"> Total Received Amount</th>
                <th class="col-md-1">Total Punishment Amount</th>
                <th class="col-md-1">Employee Due From Company</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 0;
            $total_salary = 0;
            $total_conveyance_amount = 0 ;
            $total_received_amount = 0;
            $total_punishment_amount = 0;
            $total_employee_due_from_company = 0;
            foreach ($allEmployeeTransaction as $employeePayment) {
                $total_salary = $employeePayment['total_salary_amount'] + $total_salary;
                $total_conveyance_amount = $employeePayment['total_conveyance'] + $total_conveyance_amount;
                $total_received_amount = $employeePayment['total_received_amount'] + $total_received_amount;
                $total_punishment_amount = $employeePayment['total_punishment'] + $total_punishment_amount;
                ?>
                <tr>
                    <td class="text-center"> <?php echo $employeePayment['employee_id'] ?> </td>
                    <td class="text-center"><?php echo $employeePayment['employee_name'] ?></td>
                    <td class="text-center"> <?php echo $employeePayment['employee_mobile_no'] ?> </td>
                    <td class="text-center"> <?php echo $employeePayment['employee_address'] ?> </td>
                    <td class="text-center"> <?php echo number_format($employeePayment['total_salary_amount']) ?> </td>
                    <td class="text-center"> <?php echo number_format($employeePayment['total_conveyance']) ?> </td>
                    <td class="text-center"> <?php echo number_format($employeePayment['total_received_amount']) ?> </td>
                    <td class="text-center"> <?php echo number_format($employeePayment['total_punishment']) ?> </td>
                    <?php
                    $totalEmployeeDue = ($employeePayment['total_salary_amount'] + $employeePayment['total_conveyance'])
                        - ($employeePayment['total_received_amount'] - $employeePayment['total_punishment']);
                    $total_employee_due_from_company = $totalEmployeeDue + $total_employee_due_from_company;
                    ?>
                    <td class="text-center"> <?php echo ($totalEmployeeDue < 0) ? '(Advance) ' : '';
                        echo number_format(abs($totalEmployeeDue)) ?> </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <th class="text-center">Total</th>
                <th class="text-center "></th>
                <th class="text-center "></th>
                <th class="text-center "></th>
                <th class="text-center "><?php echo number_format($total_salary);  ?></th>
                <th class="text-center "><?php echo number_format($total_conveyance_amount);  ?></th>
                <th class="text-center "><?php echo number_format($total_received_amount);  ?></th>
                <th class="text-center "><?php echo number_format($total_punishment_amount);  ?></th>
                <th class="text-center "><?php echo number_format($total_employee_due_from_company);  ?></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<hr>

<div class="modal fade" id="con-close-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Pay Employee Salary</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="employee" action="" method="post">
                <div class="modal-body">

                    <!-- Payment Amount -->
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Payment Amount</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                
                                <input required type="number" name="employee_payment" class="form-control" value="0" onkeypress="return numbersOnly(event)">
                                <span class="input-group-text">Taka</span>
                            </div>
                        </div>
                    </div>
                
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Conveyance Amount</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="number" name="employee_conveyance" class="form-control" value="0" onkeypress="return numbersOnly(event)">
                                <span class="input-group-text">Taka</span>
                            </div>
                        </div>
                    </div>
                    <!-- Punishment Amount -->
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Punishment Amount</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="number" name="employee_punishment" class="form-control" value="0" onkeypress="return numbersOnly(event)">
                                <span class="input-group-text">Taka</span>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Description</label>
                        <div class="col-sm-8">
                            <textarea name="description" rows="4" class="form-control"></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="employee_id" id="employee_id">

                </div>
                <div class="modal-footer">
                    <button type="submit" name="submit_employee_payment" class="btn btn-primary">Employee Payment</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->

<?php $obj->start_script(); ?>
<script>
    function numbersOnly(e) // Numeric Validation
    {
        var unicode = e.charCode ? e.charCode : e.keyCode
        if (unicode != 8) {
            if ((unicode < 2534 || unicode > 2543) && (unicode < 48 || unicode > 57)) {
                return false;
            }
        }
    }

    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

    $(document).ready(function () {

        $('table#datatable').on('click', 'td.action-btn button.edit', function () {

            var employeeId = $(this).data('id');
            var employeeName = $(this).data('employee');

            $('div#employee_payment h4.modal-title span#person_name').html(employeeName);
            $('div#con-close-modal form#employee div.modal-body input[name="employee_id"]').val(employeeId);
            
            console.log(employeeId);

        });

        $('#employee').on('hidden.bs.modal', function (e) {

            $('form#employee').trigger("reset");
        })

    });


</script>
<?php $obj->end_script(); ?>