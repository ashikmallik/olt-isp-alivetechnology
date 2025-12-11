<?php include('account_head.php') ?>
<div class="card basic-data-table">

    <!-- summary of expenses -->
    <div class="row mt-24 gy-4">
        <!-- total expenses summary -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-1">
                <div class="card-body p-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                        <div class="d-flex align-items-center gap-2">
                            <span class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                <iconify-icon icon="hugeicons:invoice-03" class="icon"></iconify-icon>
                            </span>
                            <div>
                                <span class="mb-2 fw-medium text-secondary-light text-sm">Total Expenses as of <span class="text-primary"><?php echo date('F') . " " . date("Y"); ?></span> </span>
                                <h6 class="fw-semibold">
                                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                                    <?php
                                    echo $obj->rawSqlSingle("SELECT SUM(acc_amount) as TotalExpenses FROM `tbl_account` WHERE acc_type = '1'")["TotalExpenses"] ?? 0;
                                    ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- total sub-head income info -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-2">
                <div class="card-body p-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                        <div class="d-flex align-items-center gap-2">
                            <span class="mb-0 w-48-px h-48-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6">
                                <iconify-icon icon="solar:wallet-bold" class="icon"></iconify-icon>
                            </span>
                            <div>
                                <span class="mb-2 fw-medium text-secondary-light text-sm">Total Sub Head Wise Expenses</span>
                                <h6 class="fw-semibold" id="totalpaid">
                                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                                    <?php
                                    $TotalSubExpense = $obj->rawSqlSingle(
                                        "SELECT SUM(acc.acc_amount) AS TotalSubExpense 
                                        FROM `tbl_account` AS acc
                                        LEFT JOIN  tbl_accounts_head head ON acc.acc_head = head.parent_id
                                        WHERE acc.acc_type = '1' AND head.level = '2'"
                                    )["TotalSubExpense"] ?? 0;
                                    echo $TotalSubExpense;
                                    ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- total head info -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-2">
                <div class="card-body p-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                        <div class="d-flex align-items-center gap-2">
                            <span class="mb-0 w-48-px h-48-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6">
                                <iconify-icon icon="solar:wallet-bold" class="icon"></iconify-icon>
                            </span>
                            <div>
                                <span class="mb-2 fw-medium text-secondary-light text-sm">Total Sub Head</span>
                                <h6 class="fw-semibold" id="totalpaid">
                                    <?php
                                    $totalHeads = $obj->rawSqlSingle("SELECT COUNT(*) as TotalHeads FROM `tbl_accounts_head` WHERE acc_type = '1' AND level ='2'")["TotalHeads"] ?? 0;
                                    echo $totalHeads;
                                    ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="card-title mb-0">Account sub-Head List</h5>
        <button type="button" class="btn btn-success-600 radius-8 px-20 py-11" data-bs-toggle="modal" data-bs-target="#con-close-modal">Create sub-head</button>

    </div>
    <div class="card-body table-responsive">
        <table
            class="table table-striped bordered-table mb-0"
            id="sub-head-table" /
            data-page-length="10">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Sub Account Name</th>
                    <th scope="col">Account Description</th>
                    <th scope="col">Total Expenses</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                foreach ($viewAccountSubHead as $value) : ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo isset($value['acc_name']) ? $value['acc_name'] : NULL;  ?></td>
                        <td><?php echo isset($value['acc_desc']) ? $value['acc_desc'] : NULL;  ?></td>
                        <td>
                            <span class="text-danger-600" style="font-weight: bold;">
                                <?php
                                $totalRelatedInfo = $obj->rawSqlSingle(
                                    "SELECT SUM(acc_amount) as TotalExpenses 
                                    FROM `tbl_account`
                                    WHERE acc_type = '1' AND acc_head = '$value[parent_id]'"
                                );
                                // var_dump($totalRelatedInfo['TotalExpenses']);
                                echo '৳ ' . intval($totalRelatedInfo['TotalExpenses']) ?? 0;
                                ?>
                            </span>
                        </td>
                        <td><button type="button" class="btn btn-warning waves-effect waves-light account_sub_head_update"
                                data-acc_id="<?php echo @$value['acc_id'] ?>"
                                data-parent_id="<?php echo @$value['parent_id'] ?>"
                                data-acc_name="<?php echo @$value['acc_name'] ?>"
                                data-acc_desc="<?php echo @$value['acc_desc'] ?>"
                                data-acc_active="<?php echo @$value['acc_status'] ?>"

                                data-bs-toggle="modal"
                                data-bs-target="#con-close-modal"><span class="fas fa-edit"></span></button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> <!-- end card body-->
</div> <!-- end card -->
</div><!-- end col-->
</div>
</div>

<!--zone  modal content -->
<div class="modal fade" id="con-close-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5">Account Sub-head</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="">
                <div class="modal-body">

                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="sub_account_id" id="sub_account_id">
                                <div class="mb-3">

                                    <label for="parent_id" class="form-label">Account Head</label>
                                    <select name="parent_id" id="parent_id" class="form-control" required>
                                        <option value="">Select</option>
                                        <?php foreach ($viewAccountHead as $value): ?>
                                            <option value="<?php echo $value['acc_id'] ?>"><?php echo $value['acc_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name_id" class="form-label">Account Name*</label>
                                    <input type="text" id="name_id" name="subname" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description_id" class="form-label">Account Details</label>
                                    <input type="text" id="description_id" name="details" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="active_id" name="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
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
</div><!-- /.modal -->
<?php $obj->start_script(); ?>

<script>
    $(document).ready(function() {
        $('#sub-head-table').DataTable({
            "responsive": true,
            "paging": true,
            "searching": true,
            "info": true,
            dom: `<"row"<"col-sm-6"l><"col-sm-6 text-end"f>>` +
                `<"row"<"col-sm-12 text-end"B>>` +
                `<"row dt-layout-row"<"col-sm-12"tr>>` +
                `<"row"<"col-sm-5"i><"col-sm-7 text-end"p>>`,
            buttons: [{
                    extend: 'copy',
                    className: 'btn btn-primary btn-sm'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm'
                },
                {
                    extend: 'print',
                    className: 'btn btn-info btn-sm'
                }
            ],
            responsive: true,
            processing: true,
            serverSide: false, // Set this to true if using server-side processing
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true
        });
    });
    var elements = document.getElementsByClassName('account_sub_head_update');
    for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', function() {
            var dataId = this.getAttribute('data-acc_id');
            var parent_id = this.getAttribute('data-parent_id');
            var accname = this.getAttribute('data-acc_name');
            var accdes = this.getAttribute('data-acc_desc');
            var accactive = this.getAttribute('data-acc_active');
            document.getElementById('sub_account_id').value = dataId;
            document.getElementById('parent_id').value = parent_id;
            document.getElementById('name_id').value = accname;
            document.getElementById('description_id').value = accdes;
            document.getElementById('active_id').value = accactive;

        });
    }
</script>
<?php $obj->end_script(); ?>