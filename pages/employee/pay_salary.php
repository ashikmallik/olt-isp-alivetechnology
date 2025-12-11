<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$obj->rawSqlSingle("CALL generate_monthly_salary('$date')");

if (isset($_POST['submit_employee_payment'])) {
    // echo $_POST['employee_id'];
    // exit;


    if (!empty($_POST['employee_payment'])) {

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
        'received_due' => 1,
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

<div class="col-md-12">
    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="card-title mb-0">Account Statement</h5>
        </div>


        <div class="card-body table-responsive">
            <div class="row gy-3" style="margin-bottom: 16px;">
                <div class="col-sm-2">
                    <label for="date-from" class="form-label">From Date</label>
                    <div class="position-relative">
                        <input type="date" id="date-from" value="<?php echo date('Y-m-01') ?>" class="form-control">
                    </div>
                </div>
                <div class="col-sm-2">
                    <label for="date-to" class="form-label">To Date</label>
                    <div class="position-relative">
                        <input type="date" id="date-to" value="<?php echo date('Y-m-d') ?>" class="form-control">
                    </div>
                </div>
            </div>
            <table id="employeeTable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Mobile No</th>
                        <th>Address</th>
                        <th>Total Salary</th>
                        <th>Total Conveyance</th>
                        <th>Total Received</th>
                        <th>Total Punishment</th>
                        <th>Employee Due</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

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
    $(document).ready(function() {
        let table = $('#employeeTable').DataTable({
            ajax: {
                url: "./pages/employee/employee_transaction_ajax.php",
                type: "GET",
                data: function(d) {
                    d.date = $('#date-from').val() + '/' + $('#date-to').val();
                    console.log("Sending Date: ", d.date);
                }
            },
            columns: [{
                    data: 'employee_id'
                },
                {
                    data: 'employee_name'
                },
                {
                    data: 'employee_mobile_no'
                },
                {
                    data: 'employee_address'
                },
                {
                    data: 'total_salary_amount'
                },
                {
                    data: 'total_conveyance'
                },
                {
                    data: 'total_received_amount'
                },
                {
                    data: 'total_punishment'
                },
                {
                    data: 'employee_due'
                },
                {
                    data: 'action'
                }
            ]
        });

        $('table#employeeTable').on('click', 'button.edit', function() {
            let id = $(this).data('id');
            console.log(id);
            let name = $(this).data('employee');
            $('#employee_id').val(id);
            $('#exampleModalLabel').text("Pay Salary to " + name);
        });

        $('#date-from, #date-to').on('change', function() {
            table.ajax.reload();
        });
    });
</script>



<?php $obj->end_script(); ?>