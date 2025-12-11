<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;

$value = $obj->details_by_cond("sms", "status='5'");


if (isset($_POST['submit'])) {
    extract($_POST);
    // var_dump($_POST);
    // exit;
    $form_data_sms = array(
        'smsbody' => $smsbody,
        'smshead' => 'general_sms'
    );


    if (isset($value['status']) && $value['status'] == 5) {
        $smsRow = $obj->Update_data("sms", $form_data_sms, " status='5' ");
    } else {

        $form_data_sms['status'] = 5;

        $smsRow = $obj->insertData("sms", $form_data_sms);
    }

    if ($smsRow) {
        echo '<script> window.location="?page=sms"; </script>';
    } else {
        echo $notification = 'Insert Failed';
    }
}

// echo $obj->sendsms('$smsbody', '01687758595');
?>

<div class="d-flex justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <!-- Form Wizard Start -->
                <div class="form-wizard">
                    <fieldset class="wizard-fieldset show">
                        <div class="col-md-12 bg-teal-800">
                            <h6 class="text-md text-neutral-500 text-center">Custom SMS</h6>
                        </div>
                        <hr>
                        <div class="mb-3 mt-5">
                            <strong style="font-size: 14px" class="badge bg-primary" title="Customer Name">{CUSTOMER_NAME}</strong>
                            <!-- <strong style="font-size: 14px" class="badge bg-success" title="Total Paid Amount">{PAID_AMOUNT}</strong> -->
                            <strong style="font-size: 14px" class="badge bg-secondary">{PACKAGE_NAME}</strong>
                            <strong style="font-size: 14px" class="badge bg-info" title="Regular Monthly Bill">{MONTHLY_BILL}</strong>
                            <strong style="font-size: 14px" class="badge bg-success">{CUSTOMER_ID}</strong>
                            <strong style="font-size: 14px" class="badge bg-warning" title="Mikrotik Secret">{IP_ADDRESS}</strong>
                            <strong style="font-size: 14px" class="badge bg-danger" title="Mikrotik Secret">{DUE_AMOUNT}</strong>
                        </div>
                        <form role="form" enctype="multipart/form-data" method="post" class="d-flex flex-column w-100">
                            <div class="form-group w-100">
                                <label for="smsbody" class="form-label">Body</label>
                                <textarea name="smsbody" id="smsbody" class="form-control w-100" rows="6"><?php echo isset($value['smsbody']) ? $value['smsbody'] : NULL; ?></textarea>
                            </div>
                            <div class="form-group pt-3">
                                <button type="submit" name="submit" class="btn btn-success waves-effect waves-light btn-lg">Save</button>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="col-md-12">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-12 bg-teal-800">
                        <h6 class="text-md text-neutral-500">Custom SMS destination</h6>
                    </div>
                    <hr>
                    <div class="col-md-2 pb-3">
                        <label for="mb" class="form-label">Package*</label>
                        <select name="mb" id="mb" class="form-control" required>
                            <option value="">Select</option>
                            <?php foreach ($obj->getAllData("tbl_package", ($mikrotikget > 0) ? ['where' => [['type', '=', 1], ['mikrotik_id', '=', $mikrotikget]]] : ['where' => ['type', '=', 1]]) as $value): ?>
                                <option data-bill="<?php echo $value['bill_amount'] ?>"
                                    value="<?php echo $value['net_speed'] ?>">
                                    <?php echo $value['package_name'] . " - " . $value["net_speed"] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2 pb-3">
                        <div class="mb-3">
                            <label for="zone" class="form-label">Zone*</label>
                            <select name="zone" id="zone" class="form-control" required>
                                <option value="">Select</option>
                                <option value="All">All Zone</option>
                                <?php foreach ($obj->getAllData("tbl_zone", ['where' => ['level', '=', '1']]) as $value): ?>
                                    <option value="<?php echo $value['zone_id'] ?>"><?php echo $value['zone_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 pb-3">
                        <label for="sub_zone" class="form-label">SubZone</label>
                        <select name="sub_zone" id="sub_zone" class="form-control">
                            <option value="0">Select</option>
                            <?php foreach ($subzones as $subzone): ?>
                                <?php echo '<option value="' . $subzone['zone_id'] . '">' . $subzone['zone_name'] . '</option>'; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- <div class="col-md-2 pb-3">
                            <label for="destination" class="form-label">Destination</label>
                            <select name="destination" id="destination" class="form-control">
                                <option value="0">Select</option>
                                <?php foreach ($destinations as $destination): ?>
                                    <?php echo '<option value="' . $destination['zone_id'] . '">' . $destination['zone_name'] . '</option>'; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2 pb-3">
                            <label for="destination" class="form-label">Destination</label>
                            <select name="destination" id="destination" class="form-control">
                                <option value="0">Select</option>
                                <?php foreach ($destinations as $destination): ?>
                                    <?php echo '<option value="' . $destination['zone_id'] . '">' . $destination['zone_name'] . '</option>'; ?>
                                <?php endforeach; ?>
                            </select>
                        </div> -->

                    <div class="col-md-2 pb-3">
                        <label for="mikrotik_disconnect" class="form-label">Disconnect Date</label>
                        <?php
                        $disconnectDays = $obj->rawSql("SELECT DISTINCT mikrotik_disconnect FROM tbl_agent ORDER BY mikrotik_disconnect ASC");
                        ?>
                        <select name="mikrotik_disconnect" id="mikrotik_disconnect" class="form-control">
                            <option value="0">Select</option>
                            <?php foreach ($disconnectDays as $disconnectDay): ?>
                                <?php echo '<option value="' . $disconnectDay['mikrotik_disconnect'] . '">' . $disconnectDay['mikrotik_disconnect'] . '</option>'; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2 pb-3">
                        <label for="billing_person_id" class="form-label">Billing Person</label>
                        <?php
                        $billingPersons = $obj->rawSql("SELECT DISTINCT tbl_agent.entry_by, user.UserName 
                                                    FROM tbl_agent 
                                                    LEFT JOIN _createuser AS user ON user.UserId = tbl_agent.entry_by");
                        ?>
                        <select name="billing_person_id" id="billing_person_id" class="form-control">
                            <option value="0">Select</option>
                            <?php foreach ($billingPersons as $billingperson): ?>
                                <?php echo '<option value="' . $billingperson['entry_by'] . '">' . $billingperson['UserName'] . '</option>'; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>



                    <div class="col-md-2 pb-3">
                        <label for="ag_status" class="form-label">Status</label>
                        <select name="ag_status" id="ag_status" class="form-control">
                            <option value="1023">ALL</option>
                            <option value="1">Active</option>
                            <option value="0">InActive</option>
                            <option value="2">Free</option>
                            <option value="3">Discontinue</option>
                        </select>
                    </div>


                    <div class="col-md-1 pt-3 text-center">
                        <button type="button" class="btn btn-success waves-effect waves-light btn-lg"
                            data-sms_status="<?php echo @$getCustomSMS['status'] ?>"
                            data-sms_id="<?php echo @$getCustomSMS['status'] ?>" id="sms_custom_sent">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


</div>
<?php $obj->start_script(); ?>

<script>
    $(document).ready(function() {
        $("#mb").on('change', function() {
            // Capture the selected value
            var selectedPackage = $("#mb option:selected").val();

            getZone(selectedPackage);

            // subzone clear
            // $("#sub_zone").html('');
            // $("#sub_zone").append('<option value="0">select</option>');

            // // mikrotik disconnect
            // $("#mikrotik_disconnect").html('');
            // $("#mikrotik_disconnect").append('<option value="0">select</option>');

            // // billing person
            // $("#billing_person_id").html('');
            // $("#billing_person_id").append('<option value="0">select</option>');

        });

        function getZone(selectedPackage) {
            $.ajax({
                url: './pages/sms/zone_ajax.php',
                type: 'POST',
                data: {
                    package: selectedPackage
                },
                success: function(response) {
                    $("#zone").html('');
                    $("#zone").append(
                        '<option value="0">select</option>'
                    );
                    $("#zone").append(
                        '<option value="All">All Zone</option>'
                    );
                    response.forEach(function(item) {
                        $("#zone").append(
                            '<option value="' + item.zone_id + '">' + item.zone_name + '</option>'
                        );
                    });
                },
                error: function() {
                    // Handle error
                    alert('An error occurred. Please try again.');
                },
                complete: function() {
                    // Optional actions after completion
                }
            });
        }



        $("#zone").on('change', function() {
            // Capture the selected value
            var selectedZone = $("#zone option:selected").val();

            // mikrotik disconnect
            // $("#mikrotik_disconnect").html('');
            // $("#mikrotik_disconnect").append('<option value="0">select</option>');

            // // billing person
            // $("#billing_person_id").html('');
            // $("#billing_person_id").append('<option value="0">select</option>');

            $.ajax({
                url: './pages/sms/sub_zone_ajax.php',
                type: 'POST',
                data: {
                    zone: selectedZone
                },
                success: function(response) {
                    $("#sub_zone").html('');
                    $("#sub_zone").append(
                        '<option value="0">select</option>'
                    );
                    response.forEach(function(item) {
                        $("#sub_zone").append(
                            '<option value="' + item.zone_id + '">' + item.zone_name + '</option>'
                        );
                    });
                },
                error: function() {
                    // Handle error
                    alert('An error occurred. Please try again.');
                },
                complete: function() {
                    // Optional actions after completion
                }
            });
        });

        $("#sub_zone").on('change', function() {
            // Capture the selected value
            var selectedSubZone = $("#sub_zone option:selected").val();

            // billing person
            $("#billing_person_id").html('');
            $("#billing_person_id").append('<option value="0">select</option>');


            $.ajax({
                url: './pages/sms/discounnect_days_ajax.php',
                type: 'POST',
                data: {
                    subZone: selectedSubZone
                },
                success: function(response) {
                    $("#mikrotik_disconnect").html('');
                    $("#mikrotik_disconnect").append(
                        '<option value="0">select</option>'
                    );
                    response.forEach(function(item) {
                        $("#mikrotik_disconnect").append(
                            '<option value="' + item.mikrotik_disconnect + '">' + item.mikrotik_disconnect + '</option>'
                        );
                    });
                },
                error: function() {
                    // Handle error
                    alert('An error occurred. Please try again.');
                },
                complete: function() {
                    // Optional actions after completion
                }
            });
        });

        $("#mikrotik_disconnect").on('change', function() {
            // Capture the selected value
            var selectedDisconnectDays = $("#mikrotik_disconnect option:selected").val();

            $.ajax({
                url: './pages/sms/billing_person_ajax.php',
                type: 'POST',
                data: {
                    disconnectDay: selectedDisconnectDays
                },
                success: function(response) {
                    $("#billing_person_id").html('');
                    $("#billing_person_id").append(
                        '<option value="0">select</option>'
                    );
                    response.forEach(function(item) {
                        $("#billing_person_id").append(
                            '<option value="' + item.entry_by + '">' + item.UserName + '</option>'
                        );
                    });
                },
                error: function() {
                    // Handle error
                    alert('An error occurred. Please try again.');
                },
                complete: function() {
                    // Optional actions after completion
                }
            });
        });
    });
</script>


<script>
    $(document).ready(function() {
        // Listen for the click event on the Send button
        $('#sms_custom_sent').on('click', function() {
            if (confirm('Are you sure you want to send SMS to all customers?')) {
                console.log('check mik', $('#mikrotik_disconnect').val());

                var $button = $(this); // Cache the button for easier reference
                var buttonText = $button.text(); // Store current button text

                // Disable the button and change text to "Loading"
                // $button.prop('disabled', true).text('Loading...').removeClass('btn-success').addClass('btn-info');

                // Collect the selected values
                var selectedData = {
                    mb: $('#mb').val(),
                    zone: $('#zone').val(),
                    sub_zone: $('#sub_zone').val(),
                    destination: $('#destination').val(),
                    mikrotik_disconnect: $('#mikrotik_disconnect').val(),
                    billing_person_id: $('#billing_person_id').val(),
                    ag_status: $('#ag_status').val(),
                    sms_status: $button.data('sms_status'),
                    sms_id: $button.data('sms_id'),
                    sms_body: $("#smsbody").val()
                };


                // Perform the AJAX request
                $.ajax({
                    url: './pages/sms/sms_ajax.php', // Replace with your server-side endpoint
                    type: 'POST',
                    data: selectedData,
                    success: function(response) {
                        console.log(response);

                        try {
                            // Try to find JSON part from response (if exists)
                            var jsonStartIndex = response.indexOf("{");
                            if (jsonStartIndex > -1) {
                                var jsonString = response.substring(jsonStartIndex);
                                var jsonResponse = JSON.parse(jsonString);

                                // JSON response-based handling
                                if (jsonResponse.error_message) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops!',
                                        text: jsonResponse.error_message,
                                    });
                                } else if (jsonResponse.success_message) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: jsonResponse.success_message,
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'Notice',
                                        text: 'No message returned from server.',
                                    });
                                }

                            } else {
                                // JSON not found in response — fallback to string logic
                                if (response.includes('202 Etrue')) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: 'SMS sent successfully!',
                                    });
                                } else if (response.includes('1005 EUndefined')) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed!',
                                        text: 'SMS sending failed: Undefined variable.',
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Unknown Response',
                                        text: response,
                                    });
                                }
                            }
                        } catch (e) {
                            console.error("Parse Error:", e);
                            console.error("Raw response:", response);

                            Swal.fire({
                                icon: 'error',
                                title: 'Unexpected Error',
                                text: 'Could not process server response.',
                            });
                        }
                    },
                    error: function() {
                        // If AJAX fails, handle the error
                        $button.removeClass('btn-info').addClass('btn-danger').text('Not Sent');
                        alert('An error occurred. Please try again.');
                    },
                    complete: function() {
                        // Re-enable the button after the request is completed (optional)
                        // You can either keep the button disabled until success or re-enable it here
                        // $button.prop('disabled', false); 
                    }
                });
            }
        });
    });
</script>
<?php $obj->end_script(); ?>