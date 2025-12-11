<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$employeeSalaryReceiveType = 1;
$employeeSalaryDueType = 0;

if (isset($_POST['employee_salary'])) {

    $form_tbl_employee_transaction = array(
        'employee_id' => $_POST['employee_id'],
        'salary_amount' => $_POST['salary_amount'],
        'conveyance' => $_POST['conveyance_amount'],
        'received_amount' => 0,
        'received_due' => $employeeSalaryDueType,
        'punishment' => 0,
        'accounts_id' => 0,
        'created_at' => date('Y-m-d'),
    );

    if ($obj->insertData("tbl_employee_transaction", $form_tbl_employee_transaction)) {
        // $obj->notificationStore('eployee Salary Assign Successfully', 'success');
       echo '<script>window.location = "?page=add_employee_salary";</script>';
    } else {
        $obj->notificationStore('Failed', 'danger');
            echo '<script>window.location = "?page=add_employee_salary";</script>';
    }
}
?>

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script>
    function numbersOnly(e) {
        var unicode = e.charCode ? e.charCode : e.keyCode
        if (unicode != 8) {
            if ((unicode < 2534 || unicode > 2543) && (unicode < 46 || unicode > 57)) {
                return false;
            } else if (unicode == 47) {
                return false;
            }
        }
    }
</script>

<div class="row">
    <div class="col-md-12 bg-grey-800 text-center">
        <h4>Welcome to Assign Salary To Employee</h4>
    </div>
</div>
<div class="row">
    <div class="col-md-12 padding_5_px">
        <!--<?php $obj->notificationShowRedirect(); ?>-->
    </div>
</div>
<hr>


<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-success text-white text-center rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Employee Salary</h5>
                </div>

                <div class="card-body p-4">
                    <form id="purchase_form" class="needs-validation" method="post" enctype="multipart/form-data" novalidate>

                        <!-- Employee Selection -->
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee</label>
                            <select class="form-select" name="employee_id" id="employee_id" required>
                                <option value="">-- Select an Employee --</option>
                                <?php
                                foreach ($obj->view_all("tbl_employee") as $employee) { ?>
                                    <option value="<?= $employee['id'] ?? '' ?>">
                                        <?= $employee['employee_name'] ?? '' ?> - <?= $employee['employee_id'] ?? '' ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <div class="invalid-feedback">Please select an employee.</div>
                        </div>

                        <!-- Salary Amount -->
                        <div class="mb-3">
                            <label for="salary_amount" class="form-label">Salary Amount (৳)</label>
                            <input type="text" name="salary_amount" id="salary_amount" class="form-control" required onkeypress="return numbersOnly(event)">
                            <div class="invalid-feedback">Salary amount is required.</div>
                        </div>

                        <!-- Conveyance Amount -->
                        <div class="mb-3">
                            <label for="conveyance_amount" class="form-label">Conveyance Amount (৳)</label>
                            <input type="text" name="conveyance_amount" id="conveyance_amount" class="form-control" onkeypress="return numbersOnly(event)">
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success" name="employee_salary">
                                <i class="bi bi-check-circle me-1"></i>Submit
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>



<hr>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script type="text/javascript">
    $(document).ready(function () {
        $('select[name="employee_id"]').selectpicker();

        $('select[name="payment_method"').on('change', function () { // banking section will show when click bank
            if (this.value == 'bank') {
                $('#bank_info select[name="account_no"]').removeAttr('disabled');
                $('#bank_info input[name="diposited_by"]').removeAttr('disabled');
                $('#bank_info').show();
            } else {
                $('#bank_info select[name="account_no"]').attr('disabled', 'disabled');
                $('#bank_info input[name="diposited_by"]').attr('disabled', 'disabled');
                $('#bank_info').hide();
            }
        });
    });
</script>
