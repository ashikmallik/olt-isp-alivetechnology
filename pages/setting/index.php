<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;


$customers = $obj->view_all_by_cond("tbl_agent", "ag_status=1");
$templates = $obj->view_all("tbl_complain_templates");
$apiKey = $obj->getSettingValue('sms', 'pass');
$smsPass = $obj->getSettingValue('sms', 'password');
$sender = $obj->getSettingValue('sms', 'sender');

$smsUser = $obj->getSettingValue('sms', 'user');
$billGenerateInfo = $obj->getSettingValue('billGenerate', 'billGenerate');

$logo = $obj->getSettingValue('logo', 'logo');
$companyName = $obj->getSettingValue('excel', 'name');
$address = $obj->getSettingValue('address', 'address');
$mobile = $obj->getSettingValue('mobile', 'mobile');
$email = $obj->getSettingValue('email', 'email');
$employees = $obj->view_all_by_cond('tbl_employee', 'employee_status=1');

// var_dump($byId);
// exit();
if (isset($_POST['submit'])) {

    // Input theke value newa
    $smsApiKey = $_POST['sms_api_key'];
    $smsSender = $_POST['sms_sender_number'];
    $smsUsername = $_POST['sms_user_name'];
    $smspassword = $_POST['sms_pass_name'];

    // image upload
    if (isset($_FILES['logo'])) {
        $targetDir = "assets/images/bsd/";
        $fileName = $_FILES["logo"]["name"];
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFilePath)) {
            // Insert image file name into database
            $obj->updateSettingValue('logo', $targetFilePath, 'logo');
        }
    }
    if (isset($_POST['companyName'])) {
        $companyName = $_POST['companyName'];
        $obj->updateSettingValue('excel', $companyName, 'name');
    }
    
    $addressInfo = isset($_POST['address']) ? $_POST['address'] : "";
    if (isset($address)) {
        $obj->updateSettingValue('address', $addressInfo, 'address');
    } else {
        $obj->insertData('tbl_setting', ['value' => $addressInfo, 'field' => 'address', 'other_parameter' => 'address', 'description' => 'Company Address']);
    }
    
    $mobileInfo = isset($_POST['mobile']) ? $_POST['mobile'] : "";
    if (isset($mobile)) {
        $obj->updateSettingValue('mobile', $mobileInfo, 'mobile');
    } else {
        $obj->insertData('tbl_setting', ['value' => $mobileInfo, 'field' => 'mobile', 'other_parameter' => 'mobile', 'description' => 'Mobile Number']);
    }
    
    $emailInfo = isset($_POST['email']) ? $_POST['email'] : "";
    if (isset($email)) {
        $obj->updateSettingValue('email', $emailInfo, 'email');
    } else {
        $obj->insertData('tbl_setting', ['value' => $emailInfo, 'field' => 'email', 'other_parameter' => 'email', 'description' => 'Email Address']);
    }

    $billGenerateStatus = isset($_POST['billGenerateStatus']) && $_POST['billGenerateStatus'] == 'active' ? 'active' : 'inactive';
    if ($billGenerateInfo) {
        $obj->updateSettingValue('billGenerate', $billGenerateStatus, 'billGenerate');
    } else {
        $obj->insertData('tbl_setting', ['value' => $billGenerateStatus, 'field' => 'billGenerate', 'other_parameter' => 'billGenerate', 'description' => 'Bill Generate Status if Active then Bill Generate will be enable']);
    }

    // Empty check
    if ($smsApiKey != '' || $smsSender != '' || $smsUsername != '') {

        // Update each setting
        $obj->updateSettingValue('sms', $smsApiKey, 'pass');
        $obj->updateSettingValue('sms', $smsSender, 'sender');
        $obj->updateSettingValue('sms', $smsUsername, 'user');
        $obj->updateSettingValue('sms', $smspassword, 'password');

        // Notification and redirect
       // $obj->notificationStore('Data Updated Successfully.', 'success');
       // echo '<script> window.location="?page=setting"; </script>';
    } else {
        $obj->notificationStore('Data Added Failed.', 'success');
        echo '<script> window.location="?page=setting"; </script>';
    }
}

$procedureExists = false;
try {
    $checkSql = "SELECT ROUTINE_NAME 
                 FROM information_schema.ROUTINES 
                 WHERE ROUTINE_SCHEMA = DATABASE() 
                 AND ROUTINE_TYPE='PROCEDURE' 
                 AND ROUTINE_NAME='empty_database'";
    $res = $obj->rawSql($checkSql); // $obj->rawSql() returns array of rows
    if (!empty($res)) {
        $procedureExists = true;
    }
} catch (Exception $e) {
    $procedureExists = false;
}

// ==== Empty Database Procedure Call ====
if (isset($_POST['empty_database_btn'])) {
    try {
        $sql = "CALL empty_database()";
        $succ = $obj->rawSqlSingle($sql);
        if ($succ) {
            $obj->notificationStore('Database emptied successfully!', 'success');
            echo "<script>alert('✅ Database emptied successfully!'); window.location='?page=setting';</script>";
        } else {
            echo "<script>alert('❌ Failed to execute procedure.');</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('⚠️ Error: " . $e->getMessage() . "');</script>";
    }
}

?>
<style>
    .selection {
        width: 100%;
    }
</style>
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <h6 class="text-md text-neutral-500">Settiongs</h6>

            <!-- Form Wizard Start -->
            <div class="form-wizard">
                <form enctype="multipart/form-data" method="post">

                    <fieldset class="wizard-fieldset show">
                        <div class="row gy-3">

                            <div class="col-sm-6">
                                <label class="form-label">Sms Api Key</label>
                                <div class="position-relative">
                                    <textarea name="sms_api_key" class="form-control wizard-required" placeholder="Enter Sms Api Key"><?php echo $apiKey ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Sms Sender Number</label>
                                <div class="position-relative">
                                    <textarea name="sms_sender_number" class="form-control wizard-required" placeholder="Enter Sms Sender Number"><?php echo $sender ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Sms User Name</label>
                                <div class="position-relative">
                                    <textarea name="sms_user_name" class="form-control wizard-required" placeholder="Sms User Name"><?php echo $smsUser ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Sms Password</label>
                                <div class="position-relative">
                                    <input type="password" name="sms_pass_name" value="<?php echo $smsPass ?>" class="form-control wizard-required" placeholder="Sms User Name">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Company Name</label>
                                <div class="position-relative">
                                    <textarea name="companyName" class="form-control wizard-required" placeholder="Company Name"><?php echo $companyName ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Address</label>
                                <div class="position-relative">
                                    <textarea name="address" class="form-control wizard-required" placeholder="Address"><?php echo $address ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Mobile Number</label>
                                <div class="position-relative">
                                   <textarea name="mobile" class="form-control wizard-required" placeholder="Mobile Number"><?php echo $mobile ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Email Address</label>
                                <div class="position-relative">
                                   <input name="email" class="form-control wizard-required" value="<?php echo $email ?>" placeholder="Email Address">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Logo</label>
                                <div class="row">
                                    <div class="position-relative col-md-10">
                                        <input name="logo" class="form-control wizard-required" type="file" placeholder="Logo">
                                    </div>
                                    <div class="col-md-2">
                                        <img src="<?php echo $logo ?>" style="height: 50px;" class="img-fluid" alt="logo">
                                    </div>
                                </div>

                            </div>

                            <div class="form-check mb-2" style="margin-top: 50px; margin-left: 10px;">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    value="active"
                                    id="billGenerateStatus"
                                    name="billGenerateStatus"
                                    <?php echo isset($billGenerateInfo) && $billGenerateInfo == 'active' ? 'checked' : ''; ?>>
                                <label class="form-check-label" style="margin-left: 5px;" for="billGenerateStatus">
                                    Bill Generate Status
                                    <?php if (isset($billGenerateInfo) && $billGenerateInfo == 'active') { ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php } else { ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php } ?>
                                </label>

                            </div>

                            <div class="form-group text-end">
                                <button type="submit" name="submit" class="form-wizard-next-btn btn btn-success-600 px-32">Submit</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <!-- Form Wizard End -->

        </div>
    </div>



    <?php
    $smsInfo = $obj->details_by_cond("sms", "status='6'");
    $mikrotikDisconCronJob = $obj->getSettingValue('mikrotikDisconCronJob', 'mikrotikDisconCronJob');
    $cronJobSmsSend = $obj->getSettingValue('cronSmsSend', 'cronSmsSend');
    $MikDisconBillStatusRes = $obj->getSettingValue('MikDisconBillStatus', 'MikDisconBillStatus');
    if (isset($_POST['sms_submit'])) {
        extract($_POST);
        $form_data_sms = array(
            'smsbody' => $sms_body,
            'smshead' => isset($activeStatus) && $activeStatus == 'active' ? 'active' : 'inactive',
        );


        if (isset($smsInfo['status']) && $smsInfo['status'] == 6) {
            $smsRow = $obj->Update_data("sms", $form_data_sms, " status='6' ");
        } else {
            $form_data_sms['status'] = 6;
            $smsRow = $obj->insertData("sms", $form_data_sms);
        }

        $mikrotikDisconnectStatus = isset($_POST['mikrotikDisconnect']) && $_POST['mikrotikDisconnect'] == 'active' ? 'active' : 'inactive';
        if ($mikrotikDisconCronJob) {
            $obj->updateSettingValue('mikrotikDisconCronJob', $mikrotikDisconnectStatus, 'mikrotikDisconCronJob');
        } else {
            $obj->insertData('tbl_setting', ['value' => $mikrotikDisconnectStatus, 'field' => 'mikrotikDisconCronJob', 'other_parameter' => 'mikrotikDisconCronJob', 'description' => 'If Active then mikrotik Secret Disconnect will perform by cron job']);
        }

        $cronSmsDay = $_POST['SelectSmsSendingDays'];
        if ($cronJobSmsSend) {
            $obj->updateSettingValue('cronSmsSend', $cronSmsDay, 'cronSmsSend');
        } else {
            $obj->insertData('tbl_setting', ['value' => $cronSmsDay, 'field' => 'cronSmsSend', 'other_parameter' => 'cronSmsSend', 'description' => 'The sms will send before selected days']);
        }


        $BillStatusVal = $_POST['MikDisconBillStatus'];
        if ($MikDisconBillStatusRes) {
            $obj->updateSettingValue('MikDisconBillStatus', $BillStatusVal, 'MikDisconBillStatus');
        } else {
            $obj->insertData('tbl_setting', ['value' => $BillStatusVal, 'field' => 'MikDisconBillStatus', 'other_parameter' => 'MikDisconBillStatus', 'description' => 'bill_status column of tbl_agent']);
        }


        if ($smsRow) {
            echo '<script> window.location="?page=setting"; </script>';
        } else {
            echo $notification = 'Insert Failed';
        }
    }
    ?>


    <!-- cron job sms settings -->
    <div class="d-flex justify-content-center flex-wrap gap-4 mt-5">
        <!-- Add Due SMS Section -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <!-- Form Wizard Start -->
                    <div class="form-wizard">
                        <fieldset class="wizard-fieldset show">
                            <div class="text-center">
                                <div class="col-md-12 bg-teal-800">
                                    <h6 class="text-lg text-neutral-500">CRON JOB Configuration</h6>
                                </div>
                                <hr>
                                <b><?php echo isset($notification) ? $notification : NULL; ?></b>
                            </div>

                            <form style="padding: 20px;" role="form" enctype="multipart/form-data" method="post" class="d-flex flex-column align-items-center w-100">
                                <div class="mb-3">
                                    <strong style="font-size: 14px" class="badge bg-success" title="Customer Name">{CUSTOMER_NAME}</strong>
                                    <strong style="font-size: 14px" class="badge bg-primary" title="Total Due Amount">{DUE_AMOUNT}</strong>
                                    <strong style="font-size: 14px" class="badge bg-secondary">{PACKAGE_NAME}</strong>
                                    <strong style="font-size: 14px" class="badge bg-warning" title="Regular Monthly Bill">{MONTHLY_BILL}</strong>
                                    <strong style="font-size: 14px" class="badge bg-success">{CUSTOMER_ID}</strong>
                                    <strong style="font-size: 14px" class="badge bg-warning">{IP_ADDRESS}</strong>
                                </div>

                                <div class="form-group w-100">
                                    <label style="font-size:14px">DUE SMS Details</label>
                                    <textarea class="form-control" onkeyup="countCharB(this)" name="sms_body" id="ResponsiveDetelis"
                                        rows="4" style="width: 100%;"><?php echo isset($smsInfo['smsbody']) ? $smsInfo['smsbody'] : NULL; ?></textarea>

                                    <!-- <input type="checkbox" name="status" value="active" id="status" class="form-control"> -->
                                    <!-- checked -->
                                    <div><span id="charNumB"></span></div>
                                </div>

                                <div class="row w-100">
                                    <div class="col-md-6" style="margin-top: 25px;">
                                        <div class="form-check mt-2 mb-10">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value="active"
                                                id="mikrotikDisconnect"
                                                name="mikrotikDisconnect"
                                                <?php echo isset($mikrotikDisconCronJob) && $mikrotikDisconCronJob == 'active' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" style="margin-left: 5px;" for="mikrotikDisconnect">
                                                Mikrotik User Disconnect
                                                <?php if (isset($mikrotikDisconCronJob) && $mikrotikDisconCronJob == 'active') { ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="">
                                            <label for="">Mikrotik Disconnect for Bill Status</label>
                                            <select style="width: 300px;" id="MikDisconBillStatus" name="MikDisconBillStatus" class="form-control">
                                                <option <?php echo isset($MikDisconBillStatusRes) && $MikDisconBillStatusRes == 'inc_par_paid' ? 'selected' : '' ?> value="inc_par_paid">Include Partial Paid</option>
                                                <option <?php echo isset($MikDisconBillStatusRes) && $MikDisconBillStatusRes == 'exc_par_paid' ? 'selected' : '' ?> value="exc_par_paid">Exclude Partial Paid</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row w-100 mt-5">
                                    <div class="col-md-6">
                                        <div class="form-check" style="margin-top: 25px;">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value="active"
                                                id="flexCheckDefault"
                                                name="activeStatus"
                                                <?php echo isset($smsInfo['smshead']) && $smsInfo['smshead'] == 'active' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" style="margin-left: 5px;" for="flexCheckDefault">
                                                SMS Status
                                                <?php if (isset($smsInfo['smshead']) && $smsInfo['smshead'] == 'active') { ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 pull-right">
                                        <div class="" style="width: 300px;">
                                            <label for="">Send Sms</label>
                                            <select id="SelectSmsSendingDays" name="SelectSmsSendingDays" class="form-control">
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '1' ? 'selected' : '' ?> value="1">Before 1 day</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '2' ? 'selected' : '' ?> value="2">Before 2 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '3' ? 'selected' : '' ?> value="3">Before 3 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '4' ? 'selected' : '' ?> value="4">Before 4 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '5' ? 'selected' : '' ?> value="5">Before 5 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '6' ? 'selected' : '' ?> value="6">Before 6 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '7' ? 'selected' : '' ?> value="7">Before 7 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '8' ? 'selected' : '' ?> value="8">Before 8 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '9' ? 'selected' : '' ?> value="9">Before 9 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '10' ? 'selected' : '' ?> value="10">Before 10 days</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <button type="submit" class="btn btn-success text-center" name="sms_submit" style="margin-top: 50px;">SAVE SMS</button>
                            </form>
                            <?php if($procedureExists): ?>
                                <div class="col-md-12 mt-5">
                                    <div class="card border-danger shadow-sm">
                                        <div class="card-body text-center">
                                            <h5 class="text-danger mb-3">⚠️ Empty Database</h5>
                                            <p class="text-muted">এই বাটনে ক্লিক করলে সম্পূর্ণ ডাটাবেস খালি হয়ে যাবে। নিশ্চিত হয়ে কাজ করুন।</p>
                                            <form method="post">
                                                <button type="submit" name="empty_database_btn" class="btn btn-danger btn-lg px-5"
                                                    onclick="return confirm('আপনি কি নিশ্চিত যে ডাটাবেস খালি করতে চান? এই কাজটি ফিরিয়ে আনা যাবে না!')">
                                                    <i class="fa fa-trash"></i> Empty Database
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $obj->start_script(); ?>
<script>
    $(document).ready(function() {



        // Initialize select2 for multiple select
        $('#support_employees').select2({
            placeholder: "Choose employees...",
        });


    });

    /*$('input[name="complain_date"]').datepicker({
        autoclose: true,
        toggleActive: true,
        format: 'dd-mm-yyyy'
    // });*/
    $('input[name="complain_time"]').timepicker();
</script>

<!-- Init js-->
<script src="assets/js/pages/form-advanced.init.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Add Select2 JS (at the end of your body tag) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?php $obj->end_script(); ?>
