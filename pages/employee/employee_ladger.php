<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
 $userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$employeeId = isset($_GET['employeeid']) ? $_GET['employeeid'] : NULL;

// ========== Delete Function Start =================
$dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
if (!empty($dltoken)) {

    // $dele = $obj->Delete_data("tbl_employee", "id='$dltoken'");
    
    $dele = $obj->deleteData("tbl_employee", ['where' => ['id', '=', $dltoken]]);

    if (!$dele) {
        $obj->notificationStore("New Employee Add  Successfull", 'success');
        echo ' <script>window.location="?page=employee_view"; </script>';
        exit;
    } else {
        $obj->notificationStore("Delete  Successfull", 'error');
    }
}

if (!empty($employeeId)) {

    $employeeData = $obj->details_by_cond("tbl_employee", "id = $employeeId ");
    $allEmployeeTransactionData = $obj->view_all_by_cond("tbl_employee_transaction", "employee_id = $employeeId");

    if (isset($_GET['deletetransactionId']) && !empty($_GET['deletetransactionId'])) {

        $deleteId = $_GET['deletetransactionId'];
        $deletedLoanData = $obj->details_by_cond('tbl_employee_transaction', "id = $deleteId");

        if ( $obj->deleteData("tbl_employee_transaction", ['where' => ['id', '=', $deleteId]])) {
          

            if ($deletedLoanData['accounts_id'] != 0) { // if employee transactin have punishment or assign salary then tbl_accounts will not touch
                // $obj->deleteData('tbl_account', 'acc_id = ' . $deletedLoanData['accounts_id']);
                $accidd = $deletedLoanData['accounts_id'];
                 $obj->deleteData("tbl_account", ['where' => ['acc_id', '=', $accidd]]);
                 $obj->deleteData("tbl_employee_transaction", ['where' => ['id', '=', $deleteId]]);
            }

            $obj->notificationStore('Transaction deleted Successfully', 'success');
            echo '<script> window.location = "?page=employee_ladger&employeeid=' . $employeeId . '"; </script>';
        } else {

            $obj->notificationStore('Transaction deleted Failed');
            echo '<script> window.location = "?page=employee_ladger&employeeid=' . $employeeId . '"; </script>';
        }
    }
}

$obj->notificationShow();

?>
<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="col-md-12 bg-slate-700" style="margin-top:20px; margin-bottom: 15px;">
    <h4 class="col-md-9"><strong>View Transaction of Employee <?php echo $employeeData['employee_name']; ?></strong></h4>
    <a type="submit" class="btn btn-primary btn-sm pull-right" href="?page=print_single_employee_transaction&employeeid=<?php echo $_GET['employeeid']?>" target="_blank">Print Transaction
    </a>

</div>
<!-- all user show -->
<div id="div_all" class="row" style="font-size: 12px;">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">

                <tr>
                    <th class="text-center col-md-1">Employee ID</th>
                    <th class="text-center col-md-2">Nmae</th>
                    <th class="text-center col-md-2">Designation</th>
                    <th class="text-center col-md-2">Mobile NO</th>
                    <th class="text-center col-md-2">Email NO</th>
                    <th class="text-center col-md-2">Address</th>
                    <th class="text-center col-md-2">Joining Date</th>
                    <th class="text-center col-md-2" id="rem">Action</th>
                </tr>
                <tr>
                    <td class="text-center"><?php echo isset($employeeData['employee_id']) ? $employeeData['employee_id'] : NULL; ?></td>
                    <td class="text-center"><?php echo isset($employeeData['employee_name']) ? $employeeData['employee_name'] : NULL; ?></td>
                    <td class="text-center"><?php echo isset($employeeData['designation']) ? $employeeData['designation'] : NULL; ?></td>
                    <td class="text-center"><?php echo isset($employeeData['employee_mobile_no']) ? $employeeData['employee_mobile_no'] : NULL; ?></td>
                    <td class="text-center"><?php echo isset($employeeData['employee_email']) ? $employeeData['employee_email'] : NULL; ?></td>
                    <td class="text-center"><?php echo isset($employeeData['employee_address']) ? $employeeData['employee_address'] : NULL; ?></td>
                    <td class="text-center"><?php echo isset($employeeData['joining_date']) ? date('d-m-Y', strtotime($employeeData['joining_date'])) : NULL; ?></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a class="btn btn-xs bg-teal btn-primary padding_2_10_px" href="?page=edit_employee&token=<?php echo isset ($employeeData['id']) ? $employeeData['id'] : NULL ?>">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                            <a href="?page=employee_ladger&dltoken=<?php echo isset($employeeData['id']) ? $employeeData['id'] : NULL;?>" onclick="return confirm('Are you sure you want to delete this Employee?');" class="btn btn-xs btn-danger padding_2_10_px">
                                <span class="glyphicon glyphicon-remove"></span>
                            </a>
                        </div>
                    </td>
                </tr>

                <table>

                    <table class="table table-bordered table-hover table-striped" id="datatable">
                        <thead class="bg-teal-800">
                        <tr>
                            <th class="text-center col-md-1">SL</th>
                            <th class="text-center col-md-2">Date</th>
                            <th class="text-center col-md-2">Salary Amount</th>
                            <th class="text-center col-md-2">Conveyance Amount</th>
                            <th class="text-center col-md-2">Received Amount</th>
                            <th class="text-center col-md-2">Punishment Amount</th>
                            <th class="text-center col-md-2">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;
                        $totalSalary = 0;
                        $totalReceived = 0;
                        $totalPunishment = 0;
                        $totalConveyance = 0;
                        foreach ($allEmployeeTransactionData as $transection) {
                            $i++; ?>
                            <tr>
                                <td class="text-center"><?php echo $i; ?></td>
                                <td class="text-center"><?php echo !empty($transection['created_at']) ? date('d-M-y', strtotime($transection['created_at'])) : NULL; ?></td>
                                <td class="text-right"><?php
                                    if ($transection['received_due'] == 0) {
                                        $totalSalary += $transection['salary_amount'];
                                        echo $transection['salary_amount'];
                                    } else {echo 0;}
                                    ?>
                                </td>
                                <td class="text-right"><?php echo $transection['conveyance'];
                                    $totalConveyance = $transection['conveyance'] + $totalConveyance;
                                ?></td>

                                <td class="text-right"><?php
                                    if ($transection['received_due'] == 1) {
                                        $totalReceived += $transection['received_amount'];
                                        echo $transection['received_amount'];
                                    } else {echo 0;}
                                    ?>
                                </td>
                                <td class="text-right"><?php
                                    $totalPunishment += $transection['punishment'];
                                    echo $transection['punishment']; ?>
                                </td>

                                <td class="text-center">
                                    <a type="button"
                                       onclick="return confirm('Are you sure you want to delete this Employee Transection Record?');"
                                       href="?page=employee_ladger&employeeid=<?php echo $transection['employee_id']; ?>&deletetransactionId=<?php echo $transection['id']; ?>"
                                       class="btn btn-danger btn-xs"> <span class="glyphicon glyphicon-trash"></span> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th class="text-center" colspan=2> Total</th>
                            <th class="text-right"><?php echo $totalSalary ?></th>
                            <th class="text-right"><?php echo $totalConveyance ?></th>
                            <th class="text-right"><?php echo $totalReceived ?></th>
                            <th class="text-right"><?php echo $totalPunishment ?></th>
                            <th class="text-center">
                            </td>
                        </tr>
                        </tfoot>
                    </table>
        </div>
    </div>
</div>


<!-- Modal -->
<div id="addPriceModel" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Add Payments for this Sell and Employee <b><span id="employeeNameModal"
                                                                     class="text-grey-800"> </span></b></h4>
            </div>
            <form class="form-horizontal" role="form" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="control-label col-sm-4" for="payment">Add Payments </label>
                            <div class="col-sm-6">
                                <input type="text" onkeypress="return numbersOnly(event)" name="payment"
                                       placeholder="Insert payments" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group" style="margin-top:20px;">
                            <label class="control-label col-sm-4" for="comments">Comments </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="comments" placeholder="Payment Description"
                                          rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="employeeId" value="">
                    <input type="hidden" name="sellId" value="">
                </div>
                <div class="modal-footer text-center">
                    <div class="row">
                        <div class="text-center">
                            <button type="submit" class="btn btn-success" name="addPayment">Add Payment</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {

        $('#datatable').on('click', '[data-target="#addPriceModel"]', function () {
            var employeeId = $(this).data('employee');
            var employeeName = $(this).data('name');
            var sellId = $(this).data('sell_id');

            $('div#addPriceModel input[name="employeeId"]').val(employeeId);
            $('div#addPriceModel input[name="sellId"]').val(sellId);
            $('div#addPriceModel span#employeeNameModal').html(employeeName);

        });
    });

    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        console.log(printContents);
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>