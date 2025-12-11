<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;

$value = $obj->details_by_cond("sms", "status='3'");
if (isset($_POST['submit'])) {
    extract($_POST);
    $form_data_sms = array(
        'smsbody' => $sms_body,
        'smshead' => 'paid_sms'
    );


    if (isset($value['status']) && $value['status'] == 3) {
        $smsRow = $obj->Update_data("sms", $form_data_sms, " status='3' ");
    } else {

        $form_data_sms['status'] = 3;

        $smsRow = $obj->insertData("sms", $form_data_sms);
    }

    if ($smsRow) {
        echo '<script> window.location="?page=customize_paid_sms"; </script>';
    } else {
        echo $notification = 'Insert Failed';
    }
}

// echo $obj->sendsms('$smsbody', '01687758595');
?>

<!--===================end Function===================-->

<div class="d-flex justify-content-center flex-wrap gap-4">
    <!-- Add Due SMS Section -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <!-- Form Wizard Start -->
                <div class="form-wizard">
                    <fieldset class="wizard-fieldset show">
                        <div class="text-center">
                            <div class="col-md-12 bg-teal-800">
                                <h6 class="text-md text-neutral-500">Customize Paid SMS</h6>
                            </div>
                            <hr>
                            <b><?php echo isset($notification) ? $notification : NULL; ?></b>
                        </div>

                        <form style="padding: 20px;" role="form" enctype="multipart/form-data" method="post" class="d-flex flex-column align-items-center w-100">
                            <div class="mb-3">
                                <strong style="font-size: 14px" class="badge bg-primary" title="Customer Name">{CUSTOMER_NAME}</strong>
                                <strong style="font-size: 14px" class="badge bg-success" title="Total Paid Amount">{PAID_AMOUNT}</strong>
                                <strong style="font-size: 14px" class="badge bg-secondary">{PACKAGE_NAME}</strong>
                                <strong style="font-size: 14px" class="badge bg-warning" title="Regular Monthly Bill">{MONTHLY_BILL}</strong>
                                <strong style="font-size: 14px" class="badge bg-success">{CUSTOMER_ID}</strong>
                                <strong style="font-size: 14px" class="badge bg-warning" title="Mikrotik Secret">{IP_ADDRESS}</strong>
                                <strong style="font-size: 14px" class="badge bg-danger">{DUE_AMOUNT}</strong>
                                <strong style="font-size: 14px" class="badge bg-info">{PAID_DAY}</strong>
                                <strong style="font-size: 14px" class="badge bg-success">{PAID_MONTH}</strong>
                                <strong style="font-size: 14px" class="badge bg-warning" title="Mikrotik Secret">{PAID_YEAR}</strong>
                                
                            </div>

                            <div class="form-group w-100">
                                <label style="font-size:14px">DUE SMS Details</label>
                                <textarea class="form-control" onkeyup="countCharB(this)" name="sms_body" id="ResponsiveDetelis"
                                    rows="4" style="width: 100%;"><?php echo isset($value['smsbody']) ? $value['smsbody'] : NULL; ?></textarea>
                                <div><span id="charNumB"></span></div>
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