<?php include('billcollection.php') ?>
<style>
    .table-responsive {
        overflow-x: auto;
    }
</style>
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <!-- Form Wizard Start -->
            <div class="form-wizard">
                <fieldset class="wizard-fieldset show">
                    <div class="row gy-3">
                        <div class="col-sm-3">
                            <label for="zone-filter" class="form-label">Zone</label>
                            <div>
                                <select id="zone-filter" class="form-control">
                                    <option value="">All Zones</option>
                                    <?php foreach ($bzones as $zone): ?>
                                        <option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['zone_name']; ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label for="zone-filter" class="form-label">Sub Zone</label>
                            <div>
                                <select id="sub-zone-filter" class="form-control">
                                    <option value="">All Sub Zones</option>
                                    <?php foreach ($sub_zones as $zone): ?>
                                        <option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['zone_name']; ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label for="billing-person-filter" class="form-label">Billing Person</label>
                            <div class="position-relative">
                                <select id="billing-person-filter" class="form-control">
                                    <option value="">All Billing Persons</option>
                                    <?php foreach ($bbillingperson as $bp): ?>
                                        <option value="<?php echo $bp['UserId']; ?>"><?php echo $bp['FullName']; ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label for="status-filter" class="form-label">Status</label>
                            <div class="position-relative">
                                <select id="status-filter" class="form-control">
                                    <option value="">All</option>
                                    <option value="0">UnPaid</option>
                                    <option value="1">Partial</option>
                                </select>
                            </div>
                        </div>
                        <!--<div class="col-sm-2">-->
                        <!--    <label for="date-from" class="form-label">Bill From Date</label>-->
                        <!--    <div class="position-relative">-->
                        <!--        <input type="date" id="date-from" class="form-control">-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="col-sm-2">-->
                        <!--    <label for="date-to" class="form-label">Bill To Date</label>-->
                        <!--    <div class="position-relative">-->
                        <!--        <input type="date" id="date-to" class="form-control">-->
                        <!--    </div>-->
                        <!--</div>-->
                        <div class="col-sm-2">
                            <label for="disconnect-date-from" class="form-label">Disconnect From Date</label>
                            <div class="position-relative">
                                <input type="date" id="disconnect-date-from" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label for="disconnect-date-to" class="form-label">Disconnect To Date</label>
                            <div class="position-relative">
                                <input type="date" id="disconnect-date-to" class="form-control">
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
    <div class="col-md-12">
        <div class="card h-100 p-0 radius-12">
            <div class="card-body p-24">
                <div class="mt-24">
                    <div class="row mt-24 gy-0">
                        <div class="col-xxl-3 col-sm-6">
                            <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-1">
                                <div class="card-body p-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                <iconify-icon icon="hugeicons:invoice-03" class="icon"></iconify-icon>
                                            </span>
                                            <div>
                                                <span class="mb-2 fw-medium text-secondary-light text-sm">Total Due</span>
                                                <h6 class="fw-semibold" id="totalDue">0</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-sm-6">
                            <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-3">
                                <div class="card-body p-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="mb-0 w-48-px h-48-px bg-warning-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                <iconify-icon icon="hugeicons:invoice-03" class="icon"></iconify-icon>
                                            </span>
                                            <div>
                                                <span class="mb-2 fw-medium text-secondary-light text-sm"><span class="text-danger"><?= date("F") ?></span> Month Due</span>
                                                <h6 class="fw-semibold" id="runningMonthDue">0</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-sm-6">
                            <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-1">
                                <div class="card-body p-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                <iconify-icon icon="hugeicons:invoice-03" class="icon"></iconify-icon>
                                            </span>
                                            <div>
                                                <span class="mb-2 fw-medium text-secondary-light text-sm">Total Previous Due</span>
                                                <h6 class="fw-semibold" id="totalPreviousDue">0</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- total customer due -->
                        <div class="col-xxl-3 col-sm-6">
                            <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-start-5">
                                <div class="card-body p-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="mb-0 w-48-px h-48-px border-danger-light-white bg-danger flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                                            </span>
                                            <div>
                                                <span class="mb-2 fw-medium text-secondary-light text-sm"><span class="text-danger"><?= date("F") ?></span> Due Customers</span>
                                                <h6 class="fw-semibold" id="totalDueCustomers">0</h6>
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
</div>


<div class="col-md-12">
    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="card-title mb-0">Bill Collection List</h5>
        </div>
        <div class="card-body table-responsive">
            <table
                class="table table-striped bordered-table mb-0"
                id="customer-datatable"
                data-page-length="10">
                <thead>
                    <tr>
                        <th scope="col">SL.</th>
                        <?php if ($obj->userWorkPermission('edit')) { ?>
                            <th scope="col-md-2">Action</th>
                        <?php } ?>
                        <th scope="col">ID</th>
                        <th scope="col">IP</th>
                        <th scope="col">Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Mobile No</th>
                        <th scope="col">Package</th>
                        <th scope="col">Monthly Bill</th>
                        <th scope="col">Total Due</th>
                        <th scope="col">Previous Due</th>
                        <!--<th scope="col">Bill Date</th>-->
                        <th scope="col">Disconn. Date</th>
                        <th scope="col">Expire Time</th>
                        <th scope="col">Zone</th>
                        <th scope="col">B.Person</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>

                <tfoot>
                    <tr>
                    <th colspan="8" class="text-end">Page Total:</th>
                    <th id="ft_monthly"></th>  
                    <th id="ft_total_due"></th>
                    <th id="ft_prev_due"></th>  
                    <th colspan="5"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>



<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="paymentModalLabel">Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-3">
                <form id="paymentForm">
                    <input type="hidden" id="ag_id" name="ag_id" class="form-control">
                    <div class="form-group mt-3 mb-1">
                        <label for="amount">Due Amount:</label> <strong><span id="due-amount">0</span> BDT*</strong>
                        <label for="amount">Pay Amount:</label>
                        <input type="text" id="amount" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group mb-1">
                        <label for="discount">Discount Amount:</label>
                        <input type="text" id="discount" name="discount" class="form-control" value="0">
                    </div>
                    <div class="form-group mb-1">
                        <label for="payment_type">Payment Type:</label>
                        <select id="payment_type" name="payment_type" class="form-control">
                            <option value="">Select Payment Type</option>
                            <option value="2">Bkash</option>
                            <option value="3">Nagad</option>
                            <option value="4">Bank</option>
                        </select>
                    </div>
                    <div class="form-group  mb-3">
                        <label for="discription">Description:</label>
                        <textarea id="discription" name="discription" class="form-control"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <div class="form-switch switch-success d-flex align-items-center gap-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="smssend" name="smssend" value="smssend" checked>
                            <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="yes">SMS Notification Send</label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary" id="submit-btn">Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<button class="btn btn-success btn-lg" style="padding: 10px;margin-bottom: 16px;font-size: 21px;" id="graphicalViewButton">Graphical View</button>
<!-- chart start -->
<!-- Loading Bar (hidden by default) -->

<div class="col-md-6 graphicalChart" style="display: none;">
    <div class="card h-100 p-0">
        <div class="card-header border-bottom bg-base py-16 px-24" style="display: flex; justify-content: space-between;">
            <h6 class="card-title text-lg fw-semibold mb-0">Bill Collection Comparison <span id="comparisonYear"><?php echo date("Y") . " vs " . (intval(date("Y")) - 1); ?></span></h6>
            <div class="">
                <select id="twoYearsBillCollectionChart" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                    <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                        <option value="<?= $i; ?>"><?= $i . " - " . ($i - 1); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

        <div class="card-body p-24">
            <div id="bil_collection_view_line_chart"></div>
        </div>
    </div>
</div>

<div class="col-md-6 graphicalChart" style="display: none;">
    <div class="card h-100 p-0">
        <div class="card-header border-bottom bg-base py-16 px-24" style="display: flex; justify-content: space-between;">
            <h6 class="card-title text-lg fw-semibold mb-0">Income And Expenses Comparison - <span id="incomeExpenseYear"><?= date("Y"); ?></span></h6>

            <div class="">
                <select id="yearlyIncomeExpenseComparison" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                    <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                        <option value="<?= $i; ?>"><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="card-body p-24">
            <div id="cutomer_view_column_chart" class=""></div>
        </div>
    </div>
</div>


<?php $obj->start_script(); ?>
<script>
    $(document).ready(function() {
        var table = $('#customer-datatable').DataTable({
            dom: `<"row"<"col-sm-6"l><"col-sm-6 text-end"f>>` + // Show entries and search in one row
                `<"row"<"col-sm-12 text-end"B>>` + // Buttons in a separate row
                `<"row dt-layout-row"<"col-sm-12"tr>>` + // Table content
                `<"row"<"col-sm-5"i><"col-sm-7 text-end"p>>`, // Info left, pagination right
            keys: true,
            stateSave: true,
            lengthChange: true,
            responsive: false,
            pagingType: "full_numbers",
            processing: true, // Show the processing indicator
            serverSide: true, // Enable server-side processing
            ajax: {
                url: "./pages/billcollection/billcollection_ajax.php", // URL to your PHP file
                type: "GET",
                data: function(d) {
                    d.zone = $('#zone-filter').val(); // Get zone filter value
                    d.sub_zone = $('#sub-zone-filter').val(); // Get zone filter value
                    d.bid = $('#billing-person-filter').val(); // Get billing person filter
                    d.status = $('#status-filter').val(); // Get status filter
                    d.datefrom = $('#date-from').val(); // Get date from filter
                    d.dateto = $('#date-to').val(); // Get date to filter
                    d.disconnectdateto = $('#disconnect-date-from').val(); // Get date to filter
                    d.disconnectdatefrom = $('#disconnect-date-to').val(); // Get date to filter
                }
            },
            columns: [{
                    data: 'sl',
                    orderable: false
                },
                {
                    data: 'ag_id',
                    render: function(data, type, row) {
                        return `<a href="pages/pdf/invoice.php?token=` + data + `" target="_blank">
                        <button class="btn btn-secondary waves-effect waves-light btn-sm"><i class="fas fa-print"></i></button></a>
                        <button class='btn btn-success waves-effect waves-light btn-sm' data-name='` + row['ag_name'] + `'  data-id='` + row['ag_id'] + `' data-due='` + row['dueadvance'] + `' onclick="openPaymentModal(this)">Pay</button>
                        `;
                    }
                },
                {
                    data: 'cus_id',
                    orderable: true,
                    render: function(data, type, row) {
                        return '<a class="btn btn-outline-success waves-effect waves-light btn-sm" title="Customer Ledger" href="?page=customer_ledger&token=' + row['ag_id'] + '"> ' + data + ' </a>' +
                            ' <button class="btn btn-secondary waves-effect waves-light btn-sm send-sms mt-1" data-customerid=' + row['ag_id'] + '><i class="fas fa-envelope"></i></button>'
                    }
                },
                {
                    data: 'ip',
                    orderable: true
                },
                {
                    data: 'ag_name',
                    orderable: true,
                    render: function(data, type, row) {
                        return "<span class='text-wrap'>" + data + "</span>";
                    }
                },
                {
                    data: 'ag_office_address',
                    orderable: true,
                    render: function(data, type, row) {
                        return (data != null) ? "<span class='text-wrap'>" + data + "</span>" : "N/A";
                    }
                },
                {
                    data: 'ag_mobile_no',
                    orderable: true,
                    defaultContent: 'N/A'
                },
                {
                    data: 'mb',
                    orderable: true
                },
                {
                    data: 'taka',
                    orderable: true
                }, // Bill Amount
                {
                    data: 'dueadvance',
                    orderable: true
                },
                {
                    data: 'dueadvance',
                    orderable: true,
                    render: function(data, type, row) {
                        let totalDue = parseInt(data);
                        let monthlyBillAmount = parseInt(row['taka']);
                        if (totalDue > monthlyBillAmount) {
                            return '<span class="">' + (totalDue - monthlyBillAmount) + '</span>';
                        } else {
                            return '<span class="">' + 0 + '</span>';
                        }
                    }
                },
                {
                    data: 'mikrotik_disconnect',
                    orderable: true
                },
                {
                    data: 'mikrotik_disconnect',
                    orderable: false,
                    render: function(data, type, row) {
                        if (!data) return '<span class="text-muted">N/A</span>';
                
                        // present date
                        let today = new Date();
                        let todayDay = today.getDate(); // Todays number
                
                        // Get date to days
                        let disconnectDay = parseInt(data);
                
                        // if day invalid 
                        if (isNaN(disconnectDay)) return '<span class="text-muted">Invalid</span>';
    
                        let lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
            
                        if (disconnectDay <= todayDay) {
                            return '<span class="text-danger fw-semibold">Expired</span>';
                        }
                        let diffDays = disconnectDay - todayDay;
                        return `<span class="text-success fw-semibold">${diffDays} days left</span>`;
                    }
                }

                ,
                {
                    data: 'zone_name',
                    orderable: true,
                    render: function(data, type, row) {
                        return (data != null) ? "<span class='text-wrap'>" + data + "</span>" : "N/A";
                    }
                },
                {
                    data: 'FullName',
                    orderable: true,
                    render: function(data, type, row) {
                        return (data != null) ? "<span class='text-wrap'>" + data + "</span>" : "N/A";
                    }
                },
                {
                    data: 'bill_status',
                    orderable: true,
                    render: function(data, type, row) {
                        if (data == 1) {
                            return "<span class='text-success'>Partial Paid</span>";
                        } else {
                            return "<span class='text-danger'>Unpaid</span>";
                        }
                    }
                },
            ],
            buttons: [
                { extend: "copy", className: "btn-light" },

                {
                    extend: "print",
                    className: "btn-light",
                    exportOptions: {
                    columns: [0,2,3,4,6,7,8,9,10,11,12,13]
                    },
                    action: function (e, dt, button, config) {
                    exportTotals = calcPageTotals(dt); // 
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    },
                    customize: function (win) {
                    const summaryHtml = `
                        <div style="margin-top:10px; font-size:12px;">
                        <b>Page Summary:</b>
                        Monthly Bill Total: <b>${exportTotals.monthly.toFixed(2)}</b> |
                        Total Due: <b>${exportTotals.due.toFixed(2)}</b> |
                        Previous Due: <b>${exportTotals.prev.toFixed(2)}</b>
                        </div>
                    `;
                    $(win.document.body).append(summaryHtml);
                    }
                },

                {
                    extend: "pdfHtml5",
                    className: "btn-light",
                    exportOptions: {
                    columns: [0,2,3,4,6,7,8,9,10,11,12,13]
                    },
                    orientation: "landscape",
                    pageSize: "A4",
                    action: function (e, dt, button, config) {
                    exportTotals = calcPageTotals(dt); 
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    },
                    customize: function (doc) {
                    doc.content.push({
                        margin: [0, 10, 0, 0],
                        fontSize: 10,
                        text:
                        `Page Summary: Monthly Bill Total: ${exportTotals.monthly.toFixed(2)} | ` +
                        `Total Due: ${exportTotals.due.toFixed(2)} | ` +
                        `Previous Due: ${exportTotals.prev.toFixed(2)}`
                    });
                    }
                },

                { extend: "excel", className: "btn btn-success btn-sm" }
            ],

            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search customers...",
                lengthMenu: "Show _MENU_ entries",
                emptyTable: "No data available in table"
            },
            drawCallback: function() {
                $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
            },
            lengthMenu: [10, 25, 50, 100, 500],

            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                // numeric convert helper
                const toNumber = (val) => {
                    if (val === null || val === undefined) return 0;
                    return parseFloat(String(val).replace(/[^0-9.-]/g, "")) || 0;
                };

                const colMonthlyBill = 8;  
                const colTotalDue    = 9; 
                const colPrevDue     = 10;

                // current page sum
                const pageMonthly = api.column(colMonthlyBill, { page: 'current' }).data()
                    .reduce((a, b) => toNumber(a) + toNumber(b), 0);

                const pageDue = api.column(colTotalDue, { page: 'current' }).data()
                    .reduce((a, b) => toNumber(a) + toNumber(b), 0);
                let pagePrev = 0;
                api.rows({ page: 'current' }).data().each(function (row) {
                    let totalDue = toNumber(row.dueadvance);
                    let monthly  = toNumber(row.taka);
                    pagePrev += (totalDue > monthly) ? (totalDue - monthly) : 0;
                });

                $('#ft_monthly').html(pageMonthly.toFixed(2));
                $('#ft_total_due').html(pageDue.toFixed(2));
                $('#ft_prev_due').html(pagePrev.toFixed(2));
            },
            
            order: [
                [1, 'asc']
            ], // Order by 'cus_id', adjust as needed
            initComplete: function(settings, json) {
                // This function is called once the table has been fully initialized
                $('#totalDue').text(json?.totalDueAdvanceBill?.dueAdvacne ?? 0);
                $('#runningMonthDue').text(json?.totalDueAdvanceBill?.runningMonthDue ?? 0);
                $('#totalPreviousDue').text(json?.totalDueAdvanceBill?.totalPreviousDue ?? 0);
                $('#totalDueCustomers').text(json?.totalDueAdvanceBill?.totalDueCustomers ?? 0);
            },
            drawCallback: function(settings) {
                var json = settings.json; // Get the JSON response
                $('#totalDue').text(json?.totalDueAdvanceBill?.dueAdvacne ?? 0);
                $('#runningMonthDue').text(json?.totalDueAdvanceBill?.runningMonthDue ?? 0);
                $('#totalPreviousDue').text(json?.totalDueAdvanceBill?.totalPreviousDue ?? 0);
                $('#totalDueCustomers').text(json?.totalDueAdvanceBill?.totalDueCustomers ?? 0);
            }
        });

        // Trigger table reload when filters are changed
        $('#zone-filter, #billing-person-filter, #status-filter, #date-from, #date-to, #disconnect-date-to, #disconnect-date-from, #sub-zone-filter').on('change', function() {
            table.ajax.reload();
        });

        // Handle SMS button click event
        $('#customer-datatable').on('click', '.send-sms', function() {
            $.get("./pages/billcollection/single_due_sms_ajax.php", {
                token: $(this).data('customerid')
            }, function(result) {
                result = JSON.parse(result)
                if (result.response == true) {
                    Swal.fire({
                        icon: "success",
                        title: 'SMS was sent Successfull',
                        timer: 1500,
                        showConfirmButton: false,
                        position: "top-end"
                    });
                } else {
                    // alert(result.response.error_message);
                    Swal.fire({
                        icon: "error",
                        title: result.response.error_message,
                        timer: 1500,
                        showConfirmButton: false,
                        position: "top-end"
                    });
                }
            });
        });
    });
</script>
<script>
    var clickbtn;
    let cachedAmount = 0;

    function openPaymentModal(button) {
        clickbtn = button;
        button.disabled = true;
        // Retrieve data-id and data-due values from the button
        let paymentId = button.getAttribute('data-id');
        let paymentName = button.getAttribute('data-name');
        let dueAmount = button.getAttribute('data-due');

        // Set modal input values
        document.getElementById('ag_id').value = paymentId;
        document.getElementById('due-amount').innerText = dueAmount;
        document.getElementById('amount').value = dueAmount;

        const date = new Date();
        document.getElementById('discription').innerText = `Bill collection for ${date.toLocaleString('default', { month: 'long' })}-${date.getFullYear()} From Customer ${paymentName}`;


        // Reset submit button state
        const submitButton = document.getElementById('submit-btn');
        submitButton.disabled = false;
        submitButton.innerText = 'Submit Payment';

        // Show the modal
        $('#paymentModal').modal('show');

        // Cache the due amount
        cachedAmount = dueAmount;
    }


    // re arrange due amount according to the discount amount
    $(document).ready(function() {
        $("#amount").on('change', function() {
            cachedAmount = $(this).val();
        });

        $("#discount").on('keyup', function() {
            $("#amount").attr("readonly", true);
            $("#amount").val(cachedAmount - $("#discount").val());
        });
    });


    $('#paymentForm').on('submit', function(e) {

        const submitButton = document.getElementById('submit-btn');
        submitButton.disabled = true;
        submitButton.innerText = 'Processing...';
        e.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: './pages/billcollection/billpay_ajax.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                // console.log(response); 

                if (response.success) {
                    document.getElementById('submit-btn').innerText = 'Completed';
                    //   alert(response.message);
                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: response.message || "Pay Operations processed successfully!",
                        showConfirmButton: false,
                        timer: 1500,
                    });
                    $('#customer-datatable').DataTable().ajax.reload();
                    $('#paymentModal').modal('hide');
                }
            },
            error: function() {
                alert('Error processing payment.');
                clickbtn.disabled = false;
            }
        });
    });
</script>
<script>
    // Get the current date
    const today = new Date();

    // Get the first day of the current month (1st)
    const startDate = new Date(today.getFullYear(), today.getMonth(), 2);

    // Get the last day of the current month (30th or 31st based on the month)
    const endDate = new Date(today.getFullYear(), today.getMonth() + 1, 1);
    // Format the dates to 'YYYY-MM-DD'
    const formatDate = (date) => {
        return date.toISOString().split('T')[0];
    };

    // Set the min and max attributes of the date inputs
    // document.getElementById('date-from').setAttribute('min', formatDate(startDate));
    // document.getElementById('date-from').setAttribute('max', formatDate(endDate));
    // document.getElementById('date-to').setAttribute('min', formatDate(startDate));
    // document.getElementById('date-to').setAttribute('max', formatDate(endDate));

    // Set default values to the first day and last day of the current month
    // document.getElementById('date-from').value = formatDate(startDate);
    // document.getElementById('date-to').value = formatDate(endDate);
</script>

<script>
    
    // Get the Static Month Names
    function getMonth(){
        return ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    }

    $(document).ready(function() {
        // Add event listener for the button click
        $('#graphicalViewButton').click(function() {
            $('.graphicalChart').show()
            // Perform AJAX request when the button is clicked

            // two years bill collection chart start
            function twoYearsBillCollectionChartLoad(selectedYear) {
                $.ajax({
                    type: "GET",
                    url: `./pages/billcollection/bill_collection_charts_ajax.php?selectedYear=${selectedYear}`,
                    dataType: "json",
                    success: function(response) {
                        $('#loading-spinner').hide();
                        // Call the function to render the line chart with the data from the server
                        bill_collection_view_line_chart(response.previousData, response.previousYear, response.currentData, response.currentYear, response.maxData);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data:", error);
                    }
                });
            }
            twoYearsBillCollectionChartLoad(new Date().getFullYear());
            $("#twoYearsBillCollectionChart").on("change", function() {
                const selectedYear = $(this).val();
                twoYearsBillCollectionChartLoad(selectedYear);
                $("#comparisonYear").text(selectedYear + " vs " + (selectedYear - 1));
            })
            // two years bill collection chart end



            // yearly income & expense comparison start
            function yearlyIncomeExpenseComparisonLoad(selectedYear) {
                $.ajax({
                    type: "GET",
                    url: `./pages/billcollection/bill_collection_column_chart_ajax.php?selectedYear=${selectedYear}`,
                    dataType: "json",
                    success: function(res) {
                        $('#loading-spinner').hide();
                        // Call the function to render the line chart with the data from the server
                        // cutomer_view_column_chart(res.pakageCountData,res.mbbsCountData);
                        bill_collection_view_column_chart(res.collectionData, res.expenseData, res.collection, res.expense, res.maxData);
                        // console.log(res.pakageCountData);  // To check the response
                        // console.log(res);  // To check the full response
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data:", error);
                    }
                });
            }

            yearlyIncomeExpenseComparisonLoad(new Date().getFullYear());

            $("#yearlyIncomeExpenseComparison").on("change", function() {
                yearlyIncomeExpenseComparisonLoad($(this).val());
                $("#incomeExpenseYear").text($(this).val());
            })
            // yearly income & expense comparison end
        });
    });



    let bill_collection_comparison_chart = null;



    function bill_collection_view_line_chart(previousData, previousYear, currentData, currentYear, maxData) {
        // Define colors
        let colors = ["#6658dd", "#1abc9c"];
        const dataColors = $("#apex-line-test").data("colors");
        if (dataColors) {
            colors = dataColors.split(",");
        }

        // Convert the comma-separated strings to arrays of numbers
        const previousDataArray = previousData.split(',').map(Number);
        const currentDataArray = currentData.split(',').map(Number);


        // Calculate the tick amount for y-axis dynamically
        const tickAmount = Math.ceil(maxData / 1000); // Adjust this based on your maxData

        // Chart options
        const options = {
            chart: {
                height: 380,
                type: "line",
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                }
            },
            colors: colors,
            dataLabels: {
                enabled: true
            },
            stroke: {
                width: [3, 3],
                curve: "smooth"
            },
            series: [{
                name: `Previous - ${previousYear}`,
                data: previousDataArray // Use the actual array of numbers
            }, {
                name: `Current - ${currentYear}`,
                data: currentDataArray // Use the actual array of numbers
            }],
            grid: {
                row: {
                    colors: ["transparent", "transparent"],
                    opacity: 0.2
                },
                borderColor: "#f1f3fa"
            },
            markers: {
                style: "inverted",
                size: 6
            },
            xaxis: {
                categories: getMonth(), // Use static month names
                title: {
                    text: "Month"
                }
            },
            yaxis: {
                title: {
                    text: "Bill Count"
                },
                min: 0, // Start y-axis from 0 for better readability
                max: maxData, // Use maxData to dynamically set the maximum value of the y-axis
                tickAmount: 10 // Calculate dynamic ticks based on maxData
            },
            legend: {
                position: "top",
                horizontalAlign: "right",
                floating: true,
                offsetY: -25,
                offsetX: -25
            },
            responsive: [{
                breakpoint: 600,
                options: {
                    chart: {
                        toolbar: {
                            show: false
                        }
                    },
                    legend: {
                        show: false
                    }
                }
            }]
        };

        // Render the chart


        if (bill_collection_comparison_chart) {
            bill_collection_comparison_chart.updateOptions(options);
        } else {
            if (bill_collection_comparison_chart) {
                bill_collection_comparison_chart.destroy();
            }

            bill_collection_comparison_chart = new ApexCharts(document.querySelector("#bil_collection_view_line_chart"), options);
            bill_collection_comparison_chart.render();
        }
    }
    //end line chart

    // Column Charts Start
    let incomeExpenseChart = null;

    function bill_collection_view_column_chart(collectionData, expenseData, collection, expense, maxData) {
        // Static month names for both years


        // Convert the comma-separated strings to arrays of numbers
        const collectionDataArray = collectionData.split(',').map(Number);
        const expenseDataArray = expenseData.split(',').map(Number);
        var options = {
            series: [{
                name: collection,
                data: collectionDataArray
            }, {
                name: expense,
                data: expenseDataArray
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            colors: ['#008000', '#FF0000'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: getMonth(),
            },
            yaxis: {
                title: {
                    text: "Collection of Bill for Current Year"
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "৳ " + val + " BDT"
                    }
                }
            }
        };

        if (incomeExpenseChart) {
            incomeExpenseChart.updateOptions(options);
        } else {
            if (incomeExpenseChart) {
                incomeExpenseChart.destroy();
            }

            incomeExpenseChart = new ApexCharts(document.querySelector("#cutomer_view_column_chart"), options);
            incomeExpenseChart.render();
        }
    }



    let exportTotals = { monthly: 0, due: 0, prev: 0 };

    function calcPageTotals(dt) {
        const api = dt; 

        const toNumber = (v) => parseFloat(String(v ?? 0).replace(/[^0-9.-]/g, "")) || 0;

        const colMonthlyBill = 8;
        const colTotalDue    = 9; 

        const monthly = api.column(colMonthlyBill, { page: 'current' }).data()
            .reduce((a, b) => toNumber(a) + toNumber(b), 0);

        const due = api.column(colTotalDue, { page: 'current' }).data()
            .reduce((a, b) => toNumber(a) + toNumber(b), 0);

        let prev = 0;
        api.rows({ page: 'current' }).data().each(function (row) {
            const totalDue = toNumber(row.dueadvance);
            const m = toNumber(row.taka);
            prev += (totalDue > m) ? (totalDue - m) : 0;
        });

        return { monthly, due, prev };
    }





</script>



<?php $obj->end_script(); ?>