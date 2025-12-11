<?php include('package.php') ?>

<!-- summary -->
<div class="row col-xxl-12 col-sm-12 mt-5 mb-5">
    <!-- total Packages summary -->
    <div class="col-xxl-4 col-sm-6">
        <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <i class="fa-solid fa-cubes"></i>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <?php echo $obj->rawSqlSingle("SELECT COUNT(*) AS totalPackages FROM tbl_package")['totalPackages']; ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">Total Packages</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Active User's Bill Amount -->
    <div class="col-xxl-4 col-sm-6">
        <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <?php echo "৳" . $obj->rawSqlSingle("SELECT COUNT(`ag_id`) as TotalUsers, SUM(`taka`) as TotalBill FROM `tbl_agent` WHERE deleted_at is NULL  AND `ag_status` = '1';")["TotalBill"]; ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">Active Users Bill Amount</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Total inactive User's Bill Amount -->
    <div class="col-xxl-4 col-sm-6">
        <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-danger-600">
                        <?php echo "৳" . $obj->rawSqlSingle("SELECT COUNT(`ag_id`) as TotalUsers, SUM(`taka`) as TotalBill FROM `tbl_agent` WHERE deleted_at is NULL  AND `ag_status` = '0';")["TotalBill"]; ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">Inactive Users Bill Amount</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card basic-data-table">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="card-title mb-0">Package List</h5>
        <button type="button" class="btn btn-success-600 radius-8 px-20 py-11" data-bs-toggle="modal" data-bs-target="#con-close-modal">Create Package</button>

    </div>
    <div class="card-body table-responsive">
        <table
            class="table table-striped bordered-table mb-0"
            id="dataTable"
            data-page-length="10">
            <thead>
                <tr>
                    <th scope="col">SL.No</th>
                    <th scope="col">Package</th>
                    <th scope="col">Users</th>
                    <th scope="col">Active Billable Users</th>
                    <th scope="col">Inactive Billable Users</th>
                    <th scope="col">Profile</th>
                    <th scope="col">Monthly Bill</th>
                    <?php if ($obj->userWorkPermission('edit')) { ?>
                        <th scope="col">Action</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                foreach ($obj->getAllData("tbl_package") as $value) { ?>
                    <tr>
                        <td><?php echo $i++; ?>
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="text-primary-600"><?php echo $value['package_name'] ?></a>
                        </td>
                        <td>
                            <a href="?page=customer_view&package=<?php echo $value['net_speed'] ?>" style="padding: 0 5px; border-radius: 5px;" class="btn-primary-600">
                                <?php
                                $totalRelatedInfo = $obj->rawSqlSingle("SELECT COUNT(`ag_id`) as TotalUsers, SUM(`taka`) as TotalBill FROM `tbl_agent` WHERE deleted_at is NULL AND `mb` = '$value[net_speed]';");
                                echo $totalRelatedInfo['TotalUsers'] . " => ৳" . ($totalRelatedInfo['TotalBill'] ?? 0);
                                ?>
                            </a>
                        </td>
                        <td>
                            <a href="?page=customer_view&package=<?php echo $value['net_speed'] ?>&ag_status=active" style="padding: 0 5px; border-radius: 5px;" class="btn-primary-600">
                                <?php
                                $totalRelatedInfo2 = $obj->rawSqlSingle("SELECT COUNT(`ag_id`) as TotalUsers, SUM(`taka`) as TotalBill FROM `tbl_agent` WHERE deleted_at is NULL AND `mb` = '$value[net_speed]'  AND `ag_status` = '1';");
                                echo $totalRelatedInfo2['TotalUsers'] . " => ৳" . ($totalRelatedInfo2['TotalBill'] ?? 0);
                                ?>
                            </a>
                        </td>
                        <td>
                            <a href="?page=customer_view&package=<?php echo $value['net_speed'] ?>&ag_status=inactive" style="padding: 0 5px; border-radius: 5px;" class="btn-danger-600">
                                <?php
                                $totalRelatedInfo3 = $obj->rawSqlSingle("SELECT COUNT(`ag_id`) as TotalUsers, SUM(`taka`) as TotalBill FROM `tbl_agent` WHERE deleted_at is NULL AND `mb` = '$value[net_speed]'  AND `ag_status` = '0';");
                                echo $totalRelatedInfo3['TotalUsers'] . " => ৳" . ($totalRelatedInfo3['TotalBill'] ?? 0);
                                ?>
                            </a>
                        </td>
                        <td><?php echo $value['net_speed'] ?></td>
                        <td><?php echo $value['bill_amount'] ?></td>
                        <td>
                            <a
                                href="javascript:void(0)"
                                class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center idupdate" data-packageid="<?php echo $value['package_id'] ?>" data-netspeed="<?php echo $value['net_speed'] ?>" data-billm="<?php echo $value['bill_amount'] ?>" data-packagename="<?php echo $value['package_name'] ?>" data-mkid="<?php echo $value['mikrotik_id'] ?>" data-bs-toggle="modal" data-bs-target="#con-close-modal">
                                <!--<iconify-icon icon="lucide:edit"></iconify-icon>-->
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                             <!-- if this package is not used by any customers, you can delete it. -->
                            <?php
                            $allCustomers = $obj->rawSqlSingle("SELECT COUNT(*) as total FROM tbl_agent WHERE deleted_at IS NULL AND mb = '$value[net_speed]';");
                            if ($allCustomers['total'] == 0) {
                            ?>
                                <a onclick="return confirm('Are you sure you want to delete this package?')"
                                    href="?page=package_view&delete-token=<?php echo $value['package_id'] ?>"
                                    class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                <?php   } ?>
            </tbody>
        </table>
    </div>
</div>
<!-- end row-->

<!-- update modal content -->
<div class="modal fade" id="con-close-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Package</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" class="needs-validation" novalidate>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="package_id" id="package_id">
                                <?php $allMikrotik = $obj->getAllData('mikrotik_user', ['where' => ['status', '=', 1]]);
                                if ($allMikrotik): ?>
                                    <div class="mb-3">
                                        <label for="mikrotikuserid" class="form-label">Mikrotik</label>
                                        <select id="mikrotikuserid" name="mikrotikuserid" class="form-control" required data-error-message="Please select a Mikrotik.">
                                            <option value="">Select</option>
                                            <?php foreach ($allMikrotik as $mikrotiklist) : ?>
                                                <option value="<?= htmlspecialchars($mikrotiklist['id']) ?>"><?= htmlspecialchars($mikrotiklist['mik_ip']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                <div id="speedmk"></div>

                                <div class="mb-3">
                                    <label for="net_speed" class="form-label">Mikrotik Profile Name*</label>
                                    <input type="text" class="form-control" name="net_speed" id="net_speed" placeholder="Mikrotik Profile Name" required data-error-message="Please provide a profile name.">
                                </div>

                                <div class="mb-3">
                                    <label for="package_name" class="form-label">Package Name*</label>
                                    <input type="text" class="form-control" onchange="checkOnlyNumber()" name="package_name" id="package_name" placeholder="Package Name" required data-error-message="Please provide a package name.">
                                </div>

                                <div class="mb-3">
                                    <label for="bill_amount" class="form-label">Monthly Bill*</label>
                                    <input type="number" class="form-control" oninput="checkNegative()" name="bill_amount" id="bill_amount" min="0" placeholder="Monthly Bill Amount" required data-error-message="Please provide the monthly bill amount.">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="submit" class="btn btn-success waves-effect waves-light">Save Package</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Package</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="package_id" id="package_id">

                            <?php $allMikrotik = $obj->getAllData('mikrotik_user', ['where' => ['status', '=', 1]]);
                            if ($allMikrotik): ?>
                                <div class="mb-3">
                                    <label for="package_name" class="form-label">Mikrotik</label>
                                    <select id="mikrotikuserid" name="mikrotikuserid" class="form-control" required>
                                        <option value="">Select</option>
                                        <?php
                                        foreach ($allMikrotik as $mikrotiklist) :
                                            echo '<option value="' . htmlspecialchars($mikrotiklist['id']) . '">' . htmlspecialchars($mikrotiklist['mik_ip']) . '</option>';
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div id="speedmk"></div>

                            <div class="mb-3">
                                <label for="package_name" class="form-label">Mikrotik Profile Name*</label>

                                <input type="text" class="form-control" name="net_speed" id="net_speed" placeholder="Mikrotik Profile Name" required>
                            </div>
                            <div class="mb-3">
                                <label for="package_name" class="form-label">Package Name*</label>
                                <input type="text" class="form-control" name="package_name" id="package_name" placeholder="Package Name" required>
                            </div>
                            <div class="mb-3">
                                <label for="bill_amount" class="form-label">Monthly Bill*</label>
                                <input type="number" class="form-control" name="bill_amount" id="bill_amount" placeholder="Monthly Bill Amount" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-success waves-effect waves-light">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div> -->
<!-- /.modal -->
<?php $obj->start_script(); ?>
<!-- <script>
        let table = new DataTable("#dataTable");
        table.columns.adjust().draw();
    </script> -->


<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "responsive": true,
            "paging": true,
            "searching": true,
            "info": true,
        });
    });



    var elements = document.getElementsByClassName('idupdate');
    for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', function() {
            document.getElementById('package_id').value = this.getAttribute('data-packageid');
            document.getElementById('package_name').value = this.getAttribute('data-packagename');
            document.getElementById('net_speed').value = this.getAttribute('data-netspeed');
            document.getElementById('bill_amount').value = this.getAttribute('data-billm');
            document.getElementById('mikrotikuserid').value = this.getAttribute('data-mkid');
        });
    }

    $('#mikrotikuserid').on('change', function() {
        $('#speedmk').html('');
        $('#net_speed').val();
        $('#net_speed').removeAttr('readonly');
        $.get("./pages/others/mikrotik_ajax.php", {
            mikrotikid: $(this).val()
        }, function(result) {

            $('#speedmk').html(result);
            $('#packagelist').on('change', function() {
                $('#net_speed').attr('readonly', 'readonly');
                $('#net_speed').val($('#packagelist').val());
            });

        });
    });
</script>
<script>
    function checkNegative() {
        var bill_amount = document.getElementById('bill_amount').value;
        if (bill_amount < 0) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Please enter a positive amount.",
            });
            document.getElementById('bill_amount').value = "0";
        }
    }

    function checkOnlyNumber() {
        var package_name = document.getElementById('package_name').value;
        const hasLetters = /[a-zA-Z]/; // Matches any letter
        const hasNumbers = /\d/; // Matches any number
        if (!(package_name.match(hasLetters)) && package_name.match(hasNumbers)) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Input must contain either letters or letters mixed with numbers!",
            });
            document.getElementById('package_name').value = "";
        }
    }
</script>
<?php $obj->end_script(); ?>