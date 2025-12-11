<?php include('zone.php') ?>

<!-- summary -->
<div class="row col-xxl-12 col-sm-12 mt-5 mb-5">
    <!-- total Zone summary -->
    <div class="col-xxl-4 col-sm-6">
        <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <i class="fa-solid fa-location-dot"></i>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <?php echo $obj->rawSqlSingle("SELECT COUNT(*) AS totalZone FROM tbl_zone WHERE level = '2'")['totalZone']; ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">Total Sub-Zone</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Active Users -->
    <div class="col-xxl-4 col-sm-6">
        <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <?php echo $obj->rawSqlSingle("SELECT COUNT(*) as TotalUsers FROM `tbl_agent` WHERE deleted_at is NULL AND sub_zone IS NOT NULL AND sub_zone != '0' AND `ag_status` = '1';")["TotalUsers"]; ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">Sub-Zone Wise Active Users</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Inactive Users -->
    <div class="col-xxl-4 col-sm-6">
        <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-danger-600">
                        <?php echo $obj->rawSqlSingle("SELECT COUNT(*) as TotalUsers FROM `tbl_agent` WHERE deleted_at is NULL AND sub_zone IS NOT NULL AND sub_zone != '0' AND `ag_status` = '0';")["TotalUsers"]; ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">Sub-Zone Wise Inactive Users</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card basic-data-table">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="card-title mb-0">SubZone/SubArea List</h5>
        <button type="button" class="btn btn-success-600 radius-8 px-20 py-11" data-bs-toggle="modal" data-bs-target="#con-close-modal">Create SubZone</button>

    </div>
    <div class="card-body">
        <table
            class="table table-striped bordered-table mb-0"
            id="subZoneTable"
            data-page-length="10">
            <thead>
                <tr>
                    <th scope="col">SL.No</th>
                    <th scope="col">SubZone/SubArea</th>
                    <th scope="col">Active Users</th>
                    <th scope="col">Inactive Users</th>
                    <?php if ($obj->userWorkPermission('edit')) { ?>
                        <th scope="col">Action</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                foreach ($viewsubzone as $value) { ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <a href="javascript:void(0)" class="text-primary-600"><?php echo isset($value['zone_name']) ? $value['zone_name'] : NULL; ?></a>
                        </td>
                        <td>
                            <a href="?page=customer_view&sub_zone=<?php echo $value['zone_id'] ?>&ag_status=active" style="padding: 0 5px; border-radius: 5px;" class="btn-primary-600">
                                <?php
                                $totalRelatedInfo = $obj->rawSqlSingle("SELECT COUNT(*) as TotalUsers FROM `tbl_agent` WHERE deleted_at is NULL AND sub_zone = '$value[zone_id]' AND ag_status = '1'");
                                echo $totalRelatedInfo['TotalUsers'];
                                ?>
                            </a>
                        </td>
                        <td>
                            <a href="?page=customer_view&sub_zone=<?php echo $value['zone_id'] ?>&ag_status=inactive" style="padding: 0 5px; border-radius: 5px;" class="btn-danger-600">
                                <?php
                                $totalRelatedInfo = $obj->rawSqlSingle("SELECT COUNT(*) as TotalUsers FROM `tbl_agent` WHERE deleted_at is NULL AND sub_zone = '$value[zone_id]' AND ag_status = '0'");
                                echo $totalRelatedInfo['TotalUsers'];
                                ?>
                            </a>
                        </td>
                        <td>
                            <a
                                href="javascript:void(0)"
                                class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center zoneidupdate"
                                data-zoneid="<?php echo @$value['zoneid'] ?>"
                                data-subzoneid="<?php echo @$value['zone_id'] ?>"
                                data-subzonename="<?php echo @$value['zone_name'] ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#con-close-modal">
                                <!-- <iconify-icon icon="lucide:edit"></iconify-icon> -->
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <!-- if this package is not used by any customers, you can delete it. -->
                            <?php
                            $allCustomers = $obj->rawSqlSingle("SELECT COUNT(*) as total FROM tbl_agent WHERE deleted_at IS NULL AND sub_zone = '$value[zone_id]';");
                            if ($allCustomers['total'] == 0) {
                            ?>
                                <a onclick="return confirm('Are you sure you want to delete this package?')"
                                    href="?page=zone_view&delete-token=<?php echo $value['zone_id'] ?>"
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

<!--zone  modal content -->
<div class="modal fade" id="con-close-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">SubZone</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" class="needs-validation" novalidate>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="subzone_id" id="subzone_id">
                                <div class="mb-3">
                                    <label for="parent_id" class="form-label">Zone/Area*</label>
                                    <select name="parent_id" id="parent_id" class="form-control" required data-error-message="Please select a Zone.">
                                        <option value="">Select</option>
                                        <?php foreach ($viewzone as $value): ?>
                                            <option value="<?php echo $value['zone_id'] ?>"><?php echo $value['zone_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="subzone_name" class="form-label">Zone/Area*</label>
                                    <input type="text" onchange="checkOnlyNumber()" class="form-control" name="subzone_name" id="subzone_name" placeholder="Sub Zone" required data-error-message="Please provide a sub zone name.">
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
    </div>
</div>
<!-- /.modal -->
<?php $obj->start_script(); ?>
<script>
    $(document).ready(function() {
        $('#subZoneTable').DataTable({
            "responsive": true,
            "paging": true,
            "searching": true,
            "info": true,
        });
    });
    var elements = document.getElementsByClassName('zoneidupdate');
    for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', function() {
            var parentid = this.getAttribute('data-zoneid');
            var subzoneid = this.getAttribute('data-subzoneid');
            var subzonename = this.getAttribute('data-subzonename');
            document.getElementById('parent_id').value = parentid;
            document.getElementById('subzone_id').value = subzoneid;
            document.getElementById('subzone_name').value = subzonename;
        });
    }

    function checkOnlyNumber() {
        var zone_name = document.getElementById('subzone_name').value;
        const hasLetters = /[a-zA-Z]/; // Matches any letter
        const hasNumbers = /\d/; // Matches any number
        if (!(zone_name.match(hasLetters)) && zone_name.match(hasNumbers)) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Input must contain either letters or letters mixed with numbers!",
            });
            document.getElementById('zone_name').value = "";
        }
    }
</script>
<?php $obj->end_script(); ?>