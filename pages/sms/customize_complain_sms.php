<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;

$complainCustomerSms = $obj->details_by_cond("sms", "status='8'");
$complainEmployeeSms = $obj->details_by_cond("sms", "status='9'");
if (isset($_POST['submit'])) {
    extract($_POST);
    $form_customer_sms = array(
        'smsbody' => $customer_sms_body,
        'smshead' => isset($customerActiveStatus) && $customerActiveStatus == 'active' ? 'active' : 'inactive',
    );


    if (isset($complainCustomerSms['status']) && $complainCustomerSms['status'] == '8') {
        $smsRow = $obj->Update_data("sms", $form_customer_sms, " status='8' ");
    } else {
        $form_customer_sms['status'] = '8';
        $smsRow = $obj->insertData("sms", $form_customer_sms);
    }


    $form_employee_sms = array(
        'smsbody' => $employee_sms_body,
        'smshead' => isset($employeeActiveStatus) && $employeeActiveStatus == 'active' ? 'active' : 'inactive',
    );
    if (isset($complainEmployeeSms['status']) && $complainEmployeeSms['status'] == '9') {
        $smsRow = $obj->Update_data("sms", $form_employee_sms, " status='9' ");
    } else {
        $form_employee_sms['status'] = '9';
        $smsRow = $obj->insertData("sms", $form_employee_sms);
    }

    if ($smsRow) {
        echo '<script> window.location="?page=customize_complain_sms"; </script>';
    } else {
        echo $notification = 'Insert Failed';
    }
}

// echo $obj->sendsms('$smsbody', '01687758595');
?>

<!--===================end Function===================-->

<div class="d-flex justify-content-center flex-wrap gap-4">
    <!-- Add Due SMS Section -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <!-- Form Wizard Start -->
                <div class="form-wizard">
                    <fieldset class="wizard-fieldset show">
                        <div class="text-center">
                            <div class="col-md-12 bg-teal-800">
                                <h6 class="text-md text-neutral-500">Customize Cmplain SMS</h6>
                            </div>
                            <hr>
                            <b><?php echo isset($notification) ? $notification : NULL; ?></b>
                        </div>

                        <form style="padding: 20px;" role="form" enctype="multipart/form-data" method="post" class="d-flex flex-column align-items-center w-100">
                            <div class="mb-3">
                                <strong style="font-size: 14px" class="badge bg-primary" title="Customer Name">{CUSTOMER_NAME}</strong>
                                <strong style="font-size: 14px" class="badge bg-success">{CUSTOMER_ID}</strong>
                                <strong style="font-size: 14px" class="badge bg-warning" title="Mikrotik Secret">{IP_ADDRESS}</strong>
                                <strong style="font-size: 14px" class="badge bg-secondary">{PACKAGE_NAME}</strong>
                            </div>

                            <div class="form-group w-100">
                                <label style="font-size:14px">Customer SMS</label>
                                <textarea class="form-control" onkeyup="countCharB(this)" name="customer_sms_body" id=""
                                    rows="6" style="width: 100%;"><?php echo isset($complainCustomerSms['smsbody']) ? $complainCustomerSms['smsbody'] : NULL; ?></textarea>
                                <div><span id="charNumB"></span></div>
                            </div>

                            <div style="display: flex; justify-content: center; align-items: center;" class="form-check mt-2 mb-5">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    value="active"
                                    id="customerActiveStatus"
                                    name="customerActiveStatus"
                                    <?php echo isset($complainCustomerSms['smshead']) && $complainCustomerSms['smshead'] == 'active' ? 'checked' : ''; ?>>
                                <label class="form-check-label" style="margin-left: 0px;" for="customerActiveStatus">
                                    Customer SMS Status
                                    <?php if (isset($complainCustomerSms['smshead']) && $complainCustomerSms['smshead'] == 'active') { ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php } else { ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php } ?>
                                </label>
                            </div>


                            <div class="form-group w-100">
                                <div class="d-flex flex-column align-items-center w-100">
                                    <div class="mb-3">
                                        <strong style="font-size: 14px" class="badge bg-primary" title="Customer Name">{EMPLOYEE_NAME}</strong>
                                        <strong style="font-size: 14px" class="badge bg-secondary">{COMPLAIN_TYPE}</strong>
                                        <strong style="font-size: 14px" class="badge bg-warning" title="Mikrotik Secret">{CUSTOMER_PHONE}</strong>
                                        <strong style="font-size: 14px" class="badge bg-primary" title="Customer Name">{CUSTOMER_NAME}</strong>
                                        <strong style="font-size: 14px" class="badge bg-warning" title="Mikrotik Secret">{ISSUE_DETAILS}</strong>
                                    </div>
                                </div>
                                <label style="font-size:14px">Employee SMS</label>
                                <textarea class="form-control" onkeyup="countCharB(this)" name="employee_sms_body" id=""
                                    rows="6" style="width: 100%;"><?php echo isset($complainEmployeeSms['smsbody']) ? $complainEmployeeSms['smsbody'] : NULL; ?></textarea>
                                <div><span id="charNumB"></span></div>
                            </div>

                            <div style="display: flex; justify-content: center; align-items: center;" class="form-check mt-2 mb-5">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    value="active"
                                    id="employeeActiveStatus"
                                    name="employeeActiveStatus"
                                    <?php echo isset($complainEmployeeSms['smshead']) && $complainEmployeeSms['smshead'] == 'active' ? 'checked' : ''; ?>>
                                <label class="form-check-label" style="margin-left: 0px;" for="employeeActiveStatus">
                                    Employee SMS Status
                                    <?php if (isset($complainEmployeeSms['smshead']) && $complainEmployeeSms['smshead'] == 'active') { ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php } else { ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php } ?>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-success mt-2 text-center" name="submit">SAVE SMS</button>
                        </form>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function countCharH(val) {
        var len = val.value.length;
        if (len >= 900) {
            val.value = val.value.substring(0, 900);
        } else {
            $('#charNumH').text(900 - len);
        }
    };

    function countCharB(val) {
        var len = val.value.length;
        if (len >= 900) {
            val.value = val.value.substring(0, 900);
        } else {
            $('#charNumB').text(900 - len);
        }
    };
</script>