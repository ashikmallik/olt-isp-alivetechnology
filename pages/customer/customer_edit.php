<?php 
include('customer.php');

$allmikrotik = $obj->getAllData('mikrotik_user');
$allData_p = $obj->profilelist($mikrotikget);
            
?>
<?php if ($mikrotikConnect){ ?>
<div class="card h-100 p-0">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <h6 class="text-lg fw-semibold mb-0">Mikrotik Connection</h6>
    </div>
    <div class="card-body p-24">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <?php foreach ($allmikrotik as $mikrotiks): ?>
                <div class="col-md-2 mb-2">
                    <button class="btn btn-primary" type="button"
                        data-bs-toggle="modal" 
                        data-bs-target="#transferModal"
                        data-mikrotik-ip="<?php echo $mikrotiks['mik_ip']; ?>"
                        data-mikrotik-id="<?php echo $mikrotiks['id']; ?>">
                        <?php echo $mikrotiks['mik_ip']; ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php } ?>
<form action="" method="POST" id="addUser" enctype="multipart/form-data">

    <input type="hidden" name="ag_id" value="<?php echo ($customer) ? $customer['ag_id'] : '' ?>">
    <div class="row gy-4">
        <?php if ($activeMikrotik): ?>
        
            <?php if ($mikrotikget == 0): ?>
                <div class="col-md-12 my-20">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-4 text-center text-danger text-xl">Please Select Mikrotik</h6>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($mikrotikConnect): ?>
                <div class="col-md-12 my-20">
                    <div class="card">
                        <div class="card-body">
                            <fieldset class="wizard-fieldset">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h6 class="text-md text-neutral-500">Connected Mikrotik</h6>
                                    </div>
                                    <div class="col-md-3">
                                        <h5 class="text-md text-success-600">Successfully Conected</h5>
                                    </div>
                                </div>
                                <div class="row gy-3">
                                    <div class="col-sm-6">
                                        <label class="form-label">PPPOE User*</label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control wizard-required"
                                                id="ip" name="ip" placeholder="Enter IP" required value="<?php echo ($customer) ? $customer['ip'] : '' ?>">
                                            <div class="wizard-form-error"></div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label">Mikrotik Secret Password*</label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control wizard-required" id="queue_password"
                                                name="queue_password" placeholder="Enter Password" value="<?php echo ($customer) ? $customer['queue_password'] : '' ?>" required>
                                            <div class="wizard-form-error"></div>
                                        </div>
                                    </div>

                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($checkconenctM): ?>
        





            <div class="col-md-6 my-20">
                <div class="card">
                    <div class="card-body">
                        <fieldset class="wizard-fieldset show">
                            <h6 class="text-md text-neutral-500">Customer Personal Information</h6>
                            <div class="row gy-3">
                                <?php if (!$mikrotikConnect): ?>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="ip" class="form-label">PPPOE User*</label>
                                            <input type="text" id="ip" name="ip" value="<?php echo ($customer) ? $customer['ip'] : '' ?>" class="form-control" required>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="col-sm-6">
                                    <label class="form-label">Full Name*</label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control wizard-required" id="ag_name" name="ag_name"
                                            placeholder="Enter Name" value="<?php echo ($customer) ? $customer['ag_name'] : '' ?>" required>
                                        <div class="wizard-form-error"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Mobile*</label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control wizard-required" onkeypress="return numbersOnly(event)"
                                            id="ag_mobile_no" name="ag_mobile_no" value="<?php echo ($customer) ? $customer['ag_mobile_no'] : '' ?>"
                                            placeholder="Enter Mobile Number" required>
                                        <div class="wizard-form-error"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Others Mobile</label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control wizard-required" onkeypress="return numbersOnly(event)"
                                            id="regular_mobile" name="regular_mobile" value="<?php echo ($customer) ? $customer['regular_mobile'] : '' ?>"
                                            placeholder="Enter Other Mobile Number">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Email</label>
                                    <div class="position-relative">
                                        <input type="email" class="form-control wizard-required" id="ag_email" name="ag_email"
                                            placeholder="Enter Email">

                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Gender</label>
                                    <div class="position-relative">
                                        <select class="form-control wizard-required" id="gender" name="gender">
                                            <option <?php echo ($customer) ? ($customer['gender'] == 'Male' ? 'selected' : '') : '' ?> value="Male">Male</option>
                                            <option <?php echo ($customer) ? ($customer['gender'] == 'Female' ? 'selected' : '') : '' ?> value="Female">Female</option>
                                            <option <?php echo ($customer) ? ($customer['gender'] == 'Other' ? 'selected' : '') : '' ?> value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Onu Mac Address</label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control wizard-required" id="onumac" name="onumac" value="<?php echo ($customer) ? $customer['onumac'] : '' ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">NID/Passport No</label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control wizard-required" id="national_id" name="national_id" value="<?php echo ($customer) ? $customer['national_id'] : '' ?>">
                                        <div class="wizard-form-error"></div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">NID/Passport Photo</label>
                                    <div class="position-relative">
                                        <input type="file" class="form-control wizard-required" id="nationalidphoto" name="nationalidphoto">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="position-relative">
                                        <img src="<?php echo ($customer) ? $customer['nationalidphoto'] : '' ?>" height="100px" width="100px" id="img" alt="img">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" id="address" class="form-control wizard-required"><?php echo ($customer) ? $customer['ag_office_address'] : '' ?></textarea>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Fiber Code/Id</label>
                                    <input type="text" class="form-control wizard-required" id="fibercode" name="fibercode" value="<?php echo ($customer) ? $customer['fibercode'] : '' ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Agent Type*</label>
                                    <select class="form-control wizard-required" id="agent_type" name="agent_type" required>
                                        <option <?php echo ($customer) ? ($customer['agent_type'] == 'Optical Fiber' ? 'selected' : '') : '' ?> value="Optical Fiber">Optical Fiber</option>
                                        <option <?php echo ($customer) ? ($customer['agent_type'] == 'Cat 5' ? 'selected' : '') : '' ?> value="Cat 5">Cat 5</option>
                                    </select>
                                    <div class="wizard-form-error"></div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Connection Type*</label>
                                    <select class="form-control wizard-required" id="connectiontype" name="connectiontype" required>
                                        <option <?php echo ($customer) ? ($customer['connectiontype'] == 'Home' ? 'selected' : '') : '' ?> value="Home">Home</option>
                                        <option <?php echo ($customer) ? ($customer['connectiontype'] == 'Corporate' ? 'selected' : '') : '' ?> value="Corporate">Corporate</option>
                                    </select>
                                    <div class="wizard-form-error"></div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Connection Date*</label>
                                    <input type="date" class="form-control wizard-required" id="connection_date" name="connection_date" value="<?php echo ($customer) ? date('Y-m-d', strtotime($customer['connection_date'])) : date('Y-m-d'); ?>" required>
                                    <div class="wizard-form-error"></div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>

            <div class="col-md-6 my-20">
                <div class="card">
                    <div class="card-body">
                        <div class="form-wizard">
                            <fieldset class="wizard-fieldset show">
                                <h6 class="text-md text-neutral-500">Customer Billing Information</h6>
                                <div class="row gy-3">
                                    <div class="col-sm-6">
                                        <label class="form-label">MB*</label>
                                        <select name="mb" id="mb" class="form-control" required>
                                            <option value="">Select</option>
                                            <?php foreach ($obj->getAllData("tbl_package", ($mikrotikget > 0) ? ['where' => [['type', '=', 1], ['mikrotik_id', '=', $mikrotikget]]] : ['where' => ['type', '=', 1]]) as $value): ?>
                                                <option <?php echo ($customer) ? ($customer['mb'] == $value['net_speed'] ? 'selected' : '') : '' ?> data-bill="<?php echo $value['bill_amount'] ?>" value="<?php echo $value['net_speed'] ?>"><?php echo $value['package_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="wizard-form-error"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Monthly Bill Amount*</label>
                                        <input type="text" class="form-control wizard-required" onkeypress="return numbersOnly(event)" id="taka" name="taka" value="<?php echo ($customer) ? $customer['taka'] : '' ?>" required>
                                        <div class="wizard-form-error"></div>
                                        <input type="hidden" value="<?php echo ($customer) ? $customer['taka'] : '' ?>" name="taka_cache">
                                        <input type="hidden" value="<?php echo ($customer) ? $customer['cus_id'] : '' ?>" name="customer_id">
                                    </div>
                                    <?php
                                    $previous_due = $obj->rawSqlSingle("SELECT previousdue FROM `customer_billing` WHERE `agid` = '" . $customer['ag_id'] . "'")["previousdue"] ?? 0;
                                    if ($previous_due == 0) {
                                    ?>

                                        <div class="col-sm-6">

                                            <label class="form-label">Current <span class="text-danger"> Previous Due </span> Amount</label>
                                            <input type="text" class="form-control wizard-required" onkeypress="return numbersOnly(event)" id="previous_due" name="previous_due" value="">
                                            <input type="hidden" name="previous_due_cache" value="<?php echo ($previous_due) ? $previous_due : 0 ?>">
                                            <div class="wizard-form-error"></div>
                                        </div>
                                    <?php } ?>
                                    <div class="col-sm-6">
                                        <?php
                                        $generateAtExist = $obj->rawSqlSingle("SELECT generate_at FROM `customer_billing` WHERE `agid` = '" . $customer['ag_id'] . "'");
                                        $generateAt = !empty($generateAtExist) ? $generateAtExist["generate_at"] : '';
                                        
                                        if (!empty($generateAt) && $generateAt === '2024-01-01') {
                                        ?>
                                            <label class="form-label">Effected From Current Month Bill</label>
                                            <input type="checkbox" class="form-check-input" id="effected" name="effected">
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Running Month Paid Amount*</label>
                                        <input type="text" class="form-control wizard-required" id="runningpaid" name="runningpaid" value="0" onkeypress="return numbersOnly(event)" required>
                                        <div class="wizard-form-error"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Connection Fee Paid Amount*</label>
                                        <input type="text" class="form-control wizard-required" id="connect_charge" name="connect_charge" value="0" class="form-control" onkeypress="return numbersOnly(event)" required>
                                        <div class="wizard-form-error"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Disconenct Day*</label>
                                        <input type="text" min="1" max="32" id="mikrotik_disconnect" name="mikrotik_disconnect" class="form-control" onkeypress="return numbersOnly(event)" value="<?php echo ($customer) ? $customer['mikrotik_disconnect'] : '' ?>" required>
                                        <div class="wizard-form-error"></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label">Zone*</label>
                                        <select name="zone" id="zone" class="form-control wizard-required" required>
                                            <option value="">Select</option>
                                            <?php foreach ($obj->getAllData("tbl_zone", ['where' => ['level', '=', '1']]) as $value): ?>
                                                <option <?php echo ($customer) ? ($customer['zone'] == $value['zone_id'] ? 'selected' : '') : '' ?> value="<?php echo $value['zone_id'] ?>"><?php echo $value['zone_name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="wizard-form-error"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="zonesub">
                                            <div class="mb-3">
                                                <label class="form-label">SubZone/SubArea*</label>
                                                <select name="sub_id" class="form-control">
                                                    <?php
                                                    if (isset($customer['sub_zone'])) {
                                                        foreach ($obj->getAllData("tbl_zone", ['where' => ['zone_id', '=', $customer['sub_zone']]]) as $value): ?>
                                                            <option value="<?php echo $value['zone_id'] ?>"><?php echo $value['zone_name'] ?></option>
                                                        <?php endforeach;
                                                    } else {
                                                        ?>
                                                        <option value="">N/A</option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="destination">
                                            <div class="mb-3">
                                                <label class="form-label">Destination/Area*</label>
                                                <select name="destination" class="form-control">

                                                    <?php
                                                    if (isset($customer['destination'])) {
                                                        foreach ($obj->getAllData("tbl_zone", ['where' => ['zone_id', '=', $customer['destination']]]) as $value): ?>
                                                            <option value="<?php echo $value['zone_id'] ?>"><?php echo $value['zone_name'] ?></option>
                                                        <?php endforeach;
                                                    } else {
                                                        ?>
                                                        <option value="">N/A</option>
                                                    <?php
                                                    }
                                                    ?>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Billing Person*</label>
                                        <select name="billing_person_id" id="billing_person_id" class="form-control wizard-required" required>
                                            <option value="">Select</option>
                                            <?php foreach ($obj->getAllData("vw_user_info") as $value): ?>
                                                <option <?php echo ($customer) ? ($customer['billing_person_id'] == $value['UserId'] ? 'selected' : '') : '' ?> value="<?php echo $value['UserId'] ?>"><?php echo $value['FullName'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="wizard-form-error"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Status*</label>
                                        <select class="form-select" id="ag_status" name="ag_status" required>
                                            <option <?php echo ($customer) ? ($customer['ag_status'] == '1' ? 'selected' : '') : '' ?> value="1">Active</option>
                                            <option <?php echo ($customer) ? ($customer['ag_status'] == '0' ? 'selected' : '') : '' ?> value="0">InActive</option>
                                            <option <?php echo ($customer) ? ($customer['ag_status'] == '2' ? 'selected' : '') : '' ?> value="2">Free</option>
                                            <option <?php echo ($customer) ? ($customer['ag_status'] == '3' ? 'selected' : '') : '' ?> value="3">Discontinue</option>
                                        </select>
                                        <div class="wizard-form-error"></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label">Remarks</label>
                                        <textarea id="remark" name="remark" class="form-control"><?php echo ($customer) ? $customer['remark'] : '' ?></textarea>
                                    </div>
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-success-600 radius-8 px-20 py-11" name="update">Success</button>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
</form>




<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?php 
           
            
            if ($mikrotikConnect) { ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="transferModalLabel">User Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h3>
                        MikroTik:
                        <span class="fw-bold text-success"><?php echo $editMikrotik['mik_ip'] ?> Online</span> 
                        TO 
                        <span id="toMk" class="fw-bold text-success"></span>
                    </h3>
                    
                    <form method="POST" id="transferModalForm">
                        <!-- Hidden Inputs -->
                        <input type="hidden" name="newmkid" id="newMkId" class="form-control">
                        <input type="hidden" value="<?php echo $usermikrotik; ?>" name="oldmkid" class="form-control">
                        <input type="hidden" value="<?php echo $token ?>" name="customerId" class="form-control">

                        <div class="row mt-3">
                            <!-- Secret Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer IP/Secret Name</label> 
                                <small class="text-muted"><?php echo $customer['ip']; ?></small>
                                <input type="text" value="<?php echo $customer['ip']; ?>" name="usecret" id="TransferclientIp" 
                                    data-usersecret="<?php echo $customer['ip']; ?>" 
                                    class="form-control" placeholder="User IP/Secret Name" 
                                    required onkeypress="return noSpace(event)">
                                <input type="hidden" value="<?php echo $customer['ip']; ?>" name="usecretold">
                                <div id="checkavailablityClientIdsoft" class="text-center"></div>
                                <div id="checkavailablityClientIdmk" class="text-center"></div>
                            </div>

                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer Password</label> 
                                <small class="text-muted"><?php echo $customer['queue_password']; ?></small>
                                <input type="text" name="upassword" value="<?php echo $customer['queue_password']; ?>" class="form-control" placeholder="Password" onkeypress="return noSpace(event)">
                                <input type="hidden" value="<?php echo $customer['queue_password']; ?>" name="upasswordold">
                            </div>

                            <!-- Profile -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer Profile</label> 
                                <small class="text-muted"><?php echo $customer['mb']; ?></small>
                                <div id="uprofile"></div>
                                <input type="hidden" value="<?php echo $customer['mb']; ?>" name="uprofileold">
                            </div>
                            <div class="mb-3">
                                <label for="packagelist" class="form-label">Mikrotik Profile*</label>
                                <select id="packagelist" name="uprofile" class="form-control" required>
                                    <option value="">Select</option>
                                    <?php foreach ($allData_p as $data) { ?>
                                        <option value="<?php echo $data['name']; ?>" 
                                            <?php echo ($data['name'] == $customer['mb']) ? 'selected' : ''; ?>>
                                            <?php echo $data['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>                                                          
                            </div>

                            <!-- Comment -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Customer Comments</label> 
                                
                                <textarea name="ucomment" class="form-control" rows="3" onkeypress="return noSpace(event)"></textarea>
                                <!--<input type="hidden" value="" name="ucommentold">-->
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-12 text-center">
                                <button onclick="return confirmTransfer()" type="submit" class="btn btn-primary" name="transfareUser">Transfer</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php $obj->start_script(); ?>
<script>

let sectetShow = true;
    // set selected mikrotik id globally
    let currentLoadedMikrotikId = null; // sadi

    function mkConnectCheck(id, mkip) {
        $.ajax({
            type: "GET",
            url: "./pages/mikrotik/connect_ajax.php",
            data: {
                'mkid': id
            },
            dataType: "JSON",
            success: function(response) {
                if (response.connection) {
                    $('#m' + id).html(`<button class="btn btn-success" onclick="mkAllSecret(${id})"   type="button">${mkip} ${response.status}</button>`);
                    if (sectetShow) {
                        mkAllSecret(`${id}`);
                        // set selected mikrotik id globally
                        currentLoadedMikrotikId = id;
                    }
                    sectetShow = false;
                } else {
                    $('#m' + id).html(`<button class="btn btn-danger" disabled type="button">${mkip} ${response.status}</button>`);
                }

            },
            error: function(response) {
                $('#m' + id).html(`<button class="btn btn-danger" type="button"> ${mkip} ${response.status}</button>`);
            }
        });
    }
    
    

    $(document).ready(function() {
        $('#mikrotikuserid').on('change', function() {
            var mid = $(this).val();
            if (mid > 0) {
                window.location.href = "?page=customer_create&mikrotik=" + mid;
            } else {
                window.location.href = "?page=customer_create";
            }
        });

        $('#zone').on('change', function() {
            $('#zonesub').html('');
            $('#destination').html('');
            $.get("./pages/others/zone_ajax.php", {
                zone_id: $(this).val()
            }, function(result) {
                $('#zonesub').html(result);

                $('#sub_id').on('change', function() {
                    $('#destination').html('');
                    $.get("./pages/others/zone_ajax.php", {
                        subzone_id: $(this).val()
                    }, function(result) {
                        $('#destination').html(result);
                    });
                });

            });
        });

        $('#mb').on('change', function() {
            $('#taka').val($('#mb option:selected').data('bill'));
        });



        $("#nationalidphoto").change(function() {
            var input = this; // Assign 'this' to input
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#img').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        });

    });
    function confirmTransfer() {

        return confirm('Customer will be Transfer,\n Are You sure? ');

        }
        
        
        
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var transferModal = document.getElementById('transferModal');

    transferModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var mikrotikIp = button.getAttribute('data-mikrotik-ip');
        var mikrotikId = button.getAttribute('data-mikrotik-id');

        // Set values in modal
        document.getElementById('toMk').textContent = mikrotikIp;
        document.getElementById('newMkId').value = mikrotikId;
    });
});
</script>

<?php $i = 1;
foreach ($allmikrotik as $mikrotiks) { ?>
    <script>
        mkConnectCheck(`<?php echo $mikrotiks['id']; ?>`, `<?php echo $mikrotiks['mik_ip']; ?>`);
    </script>
<?php } ?>



<?php $obj->end_script(); ?>