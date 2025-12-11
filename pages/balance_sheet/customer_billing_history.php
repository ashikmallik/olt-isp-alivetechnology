<?php
$monthArray = array(
    "1" => 'January',
    "2" => 'February',
    "3" => 'March',
    "4" => 'April',
    "5" => 'May',
    "6" => 'June',
    "7" => 'July',
    "8" => 'August',
    "9" => 'September',
    "10" => 'October',
    "11" => 'November',
    "12" => 'December',
);
?>
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <!-- Form Wizard Start -->
            <div class="form-wizard">
                <fieldset class="wizard-fieldset show">
                    <div class="row gy-3">
                        <!-- <div class="col-sm-3">
                            <label class="form-label">Select Month</label>
                            <div class="position-relative">
                                <select id="select-month" name="dateMonth" class="form-control">
                                    <option value="">Select Month</option>
                                    <?php
                                    $currentMonth = date('n');
                                    foreach ($monthArray as $monthKey => $monthVal) {
                                    ?>
                                        <option <?= ($monthKey == $currentMonth) ? 'selected' : '' ?> value="<?= $monthKey ?>">
                                            <?= $monthVal ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div> -->
                        <!-- <div class="col-sm-3">
                            <label class="form-label">Select Year</label>
                            <div class="position-relative">
                                <select id="select-year" name="dateYear" class="form-control">
                                    <option value="">Select Year</option>
                                    <?php
                                    $currentYear = date('Y');
                                    for ($year = 2010; $year <= $currentYear; $year++) {
                                    ?>
                                        <option <?= ($year == $currentYear) ? 'selected' : '' ?> value="<?= $year ?>">
                                            <?= $year ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div> -->
                        <div class="col-sm-3">
                            <label class="form-label">Due Type</label>
                            <div class="position-relative">
                                <select id="due-type" class="form-control">
                                    <option value="">All</option>
                                    <option value="dueadvance"><?php echo date('F'); ?> Month Due</option>
                                    <option value="previousdue">Previous Due</option>
                                    <option value="advancepaid">Advance Paid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Zone</label>
                            <div class="position-relative">
                                <select id="zone-filter" name="zone" class="form-control">
                                    <option value="">All Zones</option>
                                    <?php
                                    $zoneInfo = $obj->rawSql(
                                        "SELECT DISTINCT zone.zone_name, zone.zone_id
                                        FROM tbl_agent AS agent
                                        LEFT JOIN tbl_zone AS zone ON agent.zone = zone.zone_id
                                        WHERE zone.zone_name IS NOT NULL
                                        "
                                    );
                                    foreach ($zoneInfo as $zone) {
                                    ?>
                                        <option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['zone_name']; ?> </option>
                                    <?php
                                    }
                                    ?>
                                </select>


                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <!-- Form Wizard End -->
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="mt-24">
                <div class="row  gy-4">
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-1">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                            <iconify-icon icon="hugeicons:invoice-03" class="icon"></iconify-icon>
                                        </span>
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-sm">Total Collected Bill as of <span class="text-primary"><?php echo date('F') . " " . date("Y"); ?></span> </span>
                                            <h6 class="fw-semibold">
                                                <?php
                                                echo "৳ " . $obj->rawSqlSingle("SELECT SUM(totalpaid) as TotalCollectedBill FROM `customer_billing`")["TotalCollectedBill"];
                                                ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Running Month Collected Bill -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-2">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mb-0 w-48-px h-48-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6">
                                            <iconify-icon icon="solar:wallet-bold" class="icon"></iconify-icon>
                                        </span>
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-sm"><span class="text-primary"><?php echo date('F') . " " ?></span> </span> Month Bill Collected:</span>
                                            <h6 class="fw-semibold" id="totalpaid">

                                                <?php
                                                echo "৳ " . $obj->rawSqlSingle("SELECT SUM(acc_amount) AS totalCurrentMonthPaid FROM `tbl_account` WHERE MONTH(`entry_date`) = MONTH(CURRENT_DATE) AND YEAR(`entry_date`) = YEAR(CURRENT_DATE)")["totalCurrentMonthPaid"] ?? 0;
                                                ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Running month due -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-1">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                            <iconify-icon icon="hugeicons:invoice-03" class="icon"></iconify-icon>
                                        </span>
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-sm"><span class="text-danger"><?= date("F") ?></span> Month Due Bill</span>
                                            <h6 class="fw-semibold" id="">
                                                <?= "৳ " . $obj->rawSqlSingle("SELECT SUM(billing.dueadvance) AS TotalRunningMonthDue 
                                                    FROM customer_billing AS billing
                                                    JOIN tbl_agent  agent ON agent.ag_id = billing.agid
                                                    WHERE agent.deleted_at IS NULL AND ag_status = 1 AND billing.dueadvance > 0
                                                    ")["TotalRunningMonthDue"]; ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- previous due bill -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-5">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mb-0 w-48-px h-48-px bg-danger text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                            <iconify-icon icon="fa6-solid:file-invoice-dollar" class="text-white text-2xl"></iconify-icon>
                                        </span>
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-sm">Total Previous Due Bill</span>
                                            <h6 class="fw-semibold" id="">
                                                <h6 class="fw-semibold" id="">
                                                    <?= "৳ " . $obj->rawSqlSingle("SELECT SUM(billing.previousdue) AS TotalPreviousDue
                                                    FROM customer_billing AS billing
                                                    JOIN tbl_agent  agent ON agent.ag_id = billing.agid
                                                    WHERE agent.deleted_at IS NULL AND ag_status = 1 AND billing.previousdue > 0
                                                    ")["TotalPreviousDue"] ?? 0; ?>
                                                </h6>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- total advance -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-3">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mb-0 w-48-px h-48-px bg-yellow text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                            <iconify-icon icon="iconamoon:discount-fill" class="icon"></iconify-icon>
                                        </span>
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-sm">Total Advance</span>
                                            <h6 class="fw-semibold" id="totaladvance">
                                                <?php
                                                echo abs(intval($obj->rawSqlSingle("SELECT SUM(dueadvance) AS totalAdvance FROM `customer_billing` WHERE dueadvance < 0")["totalAdvance"]));
                                                ?>
                                            </h6>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- total discount -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-2">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mb-0 w-48-px h-48-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6">
                                            <iconify-icon icon="solar:wallet-bold" class="icon"></iconify-icon>
                                        </span>
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-sm">Total Discount Provided:</span>
                                            <h6 class="fw-semibold" id="totalDiscount">
                                                <?php
                                                echo $obj->rawSqlSingle("SELECT SUM(totaldiscount) AS TotalDiscount FROM `customer_billing`")["TotalDiscount"] ?? 0;
                                                ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="card-title mb-0">Customer Billing History</h5>
        </div>
        <div class="card-body table-responsive">
            <table
                class="table table-striped bordered-table mb-0"
                id="customer-billing-datatable"
                data-page-length="10">
                <thead>
                    <tr>
                        <th scope="col">SL</th>
                        <!-- <th scope="col">Bill Month</th> -->
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">IP</th>
                        <th scope="col">Zone</th>
                        <!-- <th scope="col">Address</th> -->
                        <!-- <th scope="col">Mobile No</th> -->
                        <th scope="col">Package</th>
                        <th scope="col">Monthly Bill</th>
                        <th scope="col">Total Paid</th>
                        <th scope="col">Total Discount</th>
                        <th scope="col">Previous Due</th>
                        <th scope="col">
                            <span class="text-success font-weight-bold">
                                <?= date("F") ?>
                            </span>
                            Month Due
                        </th>
                        <th scope="col">Advance Paid</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $obj->start_script(); ?>
<script>
    $(document).ready(function() {
        var table = $('#customer-billing-datatable').DataTable({
            dom: `<"row"<"col-sm-6 d-flex align-items-center"B l><"col-sm-6 text-end"f>>` + // Buttons and Show entries dropdown together
                `<"row"<"col-sm-12"tr>>` + // Table content
                `<"row"<"col-sm-5"i><"col-sm-7 text-end"p>>`, // Info left, pagination right,
            keys: true,
            stateSave: true,
            lengthChange: true,
            responsive: true,
            pagingType: "full_numbers",
            processing: true,
            // serverSide: true,
            ajax: {
                url: "./pages/balance_sheet/customer_billing_history_ajax.php",
                type: "GET",
                data: function(d) {
                    d.dateMonth = $("#select-month").val();
                    d.dateYear = $("#select-year").val();
                    // d.billingType = $("#billing-type").val();
                    d.zone = $("#zone-filter").val() ?? null;
                    d.dueType = $("#due-type").val() ?? null;
                }
            },
            columns: [{
                    data: 'sl'
                },
                // {
                //     data: 'bill_month'
                // },
                {
                    data: 'customer_id',
                    orderable: true,
                    render: function(data, type, row) {
                        console.log(row);

                        return '<a class="btn btn-info waves-effect waves-light btn-sm" href="?page=customer_ledger&token=' + row['agid'] + '"> ' + data + ' </a>';
                    }
                },
                {
                    data: 'customer_name'
                },
                {
                    data: 'ip'
                },
                {
                    data: 'zone'
                },
                // {
                //     data: 'address'
                // },
                // {
                //     data: 'mobile_no'
                // },
                {
                    data: 'package'
                },
                {
                    data: 'monthly_bill'
                },
                {
                    data: 'totalpaid'
                },
                {
                    data: 'totaldiscount'
                },
                {
                    data: 'previousdue'
                },
                {
                    data: 'due',
                    render: function(data, type, row) {
                        console.log(type);

                        return data;
                    }
                },
                {
                    data: 'advance_paid',
                    render: function(data, type, row) {
                        return Math.abs(data);
                    }
                },
            ],
            buttons: [{
                extend: "pdfHtml5",
                className: "btn-light",
                text: "Download PDF",
                download: "download",
            }],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search customers...",
                lengthMenu: "Show _MENU_ entries",
                emptyTable: "No data available in table"
            },
            lengthMenu: [10, 25, 50, 100, 500],
            order: [
                [0, 'asc']
            ],
            drawCallback: function(settings) {
                $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                console.log('dateYear', settings.json);

                $("#billig-type-col").text($("#billing-type option:selected").text())
            },
            initComplete: function(settings, json) {}
        });

        // Trigger table reload when filters are changed
        $('#due-type, #select-month, #select-year, #billing-type, #zone-filter').on('change', function() {
            table.ajax.reload();
        });
    });
</script>
<?php $obj->end_script(); ?>