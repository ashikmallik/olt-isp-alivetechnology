<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;

$value = $obj->details_by_cond("sms", "status='1'");

if (isset($_POST['clearsms'])) {
    $obj->Update_data("tbl_agent", ['sms_sent' => 0], "where ag_id > 0");
}

if (isset($_POST['submit'])) {

    extract($_POST);

    $form_data_sms = array(
        'smsbody' => $sms_body,
        'smshead' => isset($activeStatus) && $activeStatus == 'active' ? 'active' : 'inactive',
    );


    if (isset($value['status']) && $value['status'] == 1) {
        $smsRow = $obj->Update_data("sms", $form_data_sms, " status='1' ");
    } else {

        $form_data_sms['status'] = 1;

        $smsRow = $obj->insertData("sms", $form_data_sms);
    }

    if ($smsRow) {

        echo '<script> window.location="?page=due_sms"; </script>';
    } else {

        echo $notification = 'Insert Failed';
    }
}

// echo $obj->sendsms('$smsbody', '01687758595');
?>

<!--===================end Function===================-->

<div class="d-flex justify-content-center flex-wrap gap-4">
    <!-- Add Due SMS Section -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <!-- Form Wizard Start -->
                <div class="form-wizard">
                    <fieldset class="wizard-fieldset show">
                        <div class="text-center">
                            <div class="col-md-12 bg-teal-800">
                                <h6 class="text-md text-neutral-500">Add Due SMS</h6>
                            </div>
                            <hr>
                            <b><?php echo isset($notification) ? $notification : NULL; ?></b>
                        </div>

                        <form id="zoneSmsBodyForm" style="padding: 20px;" role="form" enctype="multipart/form-data" method="post" class="d-flex flex-column align-items-center w-100">
                            <!-- <div class="form-group w-100">
                                <label style="font-size:14px">DUE SMS Header</label>
                                <textarea class="form-control" onkeyup="countCharH(this)" name="sms_head" id="ResponsiveDetelis"
                                    rows="2" style="width: 100%;"><?php echo isset($value['smshead']) ? $value['smshead'] : NULL; ?></textarea>
                                <div><span id="charNumH"></span></div>
                            </div> -->

                            <div class="mb-3">
                                <strong style="font-size: 16px" class="badge bg-primary">{CUSTOMER_NAME}</strong>
                                <strong style="font-size: 16px" class="badge bg-secondary">{DUE_AMOUNT}</strong>
                                <strong style="font-size: 16px" class="badge bg-success">{CUSTOMER_ID}</strong>
                                <strong style="font-size: 16px" class="badge bg-warning">{IP_ADDRESS}</strong>
                            </div>

                            <div class="form-group w-100">
                                <label style="font-size:14px">DUE SMS Details</label>
                                <textarea class="form-control" onkeyup="countCharB(this)" name="sms_body" id="ResponsiveDetelis"
                                    rows="4" style="width: 100%;"><?php echo isset($value['smsbody']) ? $value['smsbody'] : NULL; ?></textarea>
                                <div><span id="charNumB"></span></div>
                            </div>

                            <div style="display: flex; justify-content: center; align-items: center;" class="form-check mt-2 mb-5">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    value="active"
                                    id="excludePartiallyPaid"
                                    name="activeStatus"
                                    <?php echo isset($value['smshead']) && $value['smshead'] == 'active' ? 'checked' : ''; ?>>
                                <label class="form-check-label" style="margin-left: 0px;" for="excludePartiallyPaid">
                                    Exclude Partially Paid Clients
                                </label>
                            </div>

                            <button type="submit" class="btn btn-success mt-2 text-center" name="submit">SAVE SMS</button>
                        </form>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>

    <!-- Send SMS To All Due Clients Section -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <!-- Form Wizard Start -->
                <div class="form-wizard">
                    <fieldset class="wizard-fieldset show">
                        <div class="text-center">
                            <div class="col-md-12 bg-teal-800">
                                <h6 class="text-md text-neutral-500">Send SMS To All Due Clients</h6>
                            </div>
                            <hr>
                        </div>

                        <form style="padding: 20px;" role="form" action="index.php" method="get" class="d-flex flex-column align-items-center w-100">
                            <input type="hidden" name="page" value="send_due_sms" />

                            <div class="form-group w-100">
                                <label style="font-size:14px">Select Zone</label>
                                <select class="form-control" name="zone" required>
                                    <option style="text-align: center;" value="x">All Zones Due Clients</option>
                                    <?php foreach ($obj->view_all('tbl_zone') as $singleZone) { ?>
                                        <option value="<?php echo $singleZone['zone_id'] ?>">
                                            <?php echo $singleZone['zone_name'] ?> -
                                            (<?php
                                                $whereCond = '';
                                                if ($value['smshead'] == 'active') {
                                                    $whereCond = 'AND vw_agent.bill_status != 1';
                                                }

                                                echo  $obj->rawSqlSingle("SELECT COUNT(*) AS total 
                                                FROM vw_agent 
                                                LEFT JOIN customer_billing ON vw_agent.ag_id = customer_billing.agid
                                                WHERE vw_agent.zone = " . $singleZone['zone_id'] . " AND vw_agent.ag_status='1' AND customer_billing.dueadvance > 0 AND vw_agent.deleted_at is NULL $whereCond")["total"];
                                                ?>)
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <br>


                            <div class="form-group">
                                <?php
                                $rand = 1;
                                $_SESSION['rand'] = 1;
                                ?>
                                <input type="hidden" value="<?php echo $rand; ?>" name="randcheck" />
                                <input type="hidden" value="<?php echo isset($value['smshead']) && $value['smshead'] == 'active' ? 'active' : 'inactive'; ?>" name="excPartialPaid" id="excPartialPaid" />
                                <input id="submitbtn" type="submit" name="submitbtn" value="SEND SMS TO DUE CLIENTS" class="btn btn-success mt-2" />
                            </div>
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



    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("excludePartiallyPaid").addEventListener("change", function() {
            if (this.checked) {
                document.getElementById("excPartialPaid").value = "active";
            } else {
                document.getElementById("excPartialPaid").value = "inactive";
            }
        });
    });
</script>