<!-- chart-2 end -->
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
                            <label for="billing-person-filter" class="form-label">Billing Person</label>
                            <div class="position-relative">
                                <select id="billing-person-filter" class="form-control">
                                    <option value="">All Billing Persons</option>
                                    <?php foreach ($obj->getAllData('vw_user_info') as $bp): ?>
                                        <option value="<?php echo $bp['UserId']; ?>"><?php echo $bp['FullName']; ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <label for="date-from" class="form-label">From Date</label>
                            <div class="position-relative">
                                <input value="<?= date("Y-m-01") ?>" type="date" id="date-from" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label for="date-to" class="form-label">To Date</label>
                            <div class="position-relative">
                                <input value="<?= date("Y-m-d") ?>" type="date" id="date-to" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label for="activity_type" class="form-label">Types</label>
                            <div class="position-relative">
                                <select id="activity_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="1"> Login </option>
                                    <option value="2"> Customer </option>
                                    <option value="3"> Payment </option>
                                    <option value="4"> Employee </option>
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
    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="card-title mb-0">Customer List</h5>
            <!--<button type="button" class="btn btn-success-600 radius-8 px-20 py-11" data-bs-toggle="modal" data-bs-target="#import">-->
            <!--    Import Customer <i class="fa-solid fa-arrow-up"></i> </button>-->
        </div>
        <div class="card-body table-responsive">
            <table
                class="table bordered-table mb-0"
                id="customer-datatable"
                data-page-length="10">
                <thead>
                    <tr>
                        <!-- <th scope="col">SL.</th> -->
                        <th scope="col">Description</th>
                        <th scope="col">Date</th>
                        <th scope="col">User</th>
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
            processing: true,
            serverSide: true,
            ajax: {
                url: "./pages/activity_log/activity_log_ajax.php", // URL to your PHP file
                type: "GET",
                data: function(d) {
                    d.bid = $('#billing-person-filter').val();
                    d.datefrom = $('#date-from').val();
                    d.dateto = $('#date-to').val();
                    d.activity_type = $('#activity_type').val();
                }
            },
            columns: [{
                    data: 'description',
                    orderable: true,
                    render: function(data, type, row) {
                        const device_info = row.device_info != null ? ` | ${row.device_info}]` : "]";
                        if (row.action_type == '1') {
                            return "<span class=''>" + data + " [IP: " + row.ip + device_info + "</span>";
                        } else {
                            return "<span class=''>" + data + "</span>";
                        }
                    }
                },
                {
                    data: 'date',
                    orderable: true
                }, // Bill Amount
                {
                    data: 'user_name',
                    defaultContent: 'N/A',
                    orderable: true
                },
            ],
            buttons: [{
                    extend: "copy"
                },
                {
                    extend: "print"
                },
                {
                    extend: "pdf"
                },

                {
                    extend: "excel"
                },
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search customers...",
                lengthMenu: "Show _MENU_ entries",
                emptyTable: "No data available in table"
            },
            lengthMenu: [10, 25, 50, 100, 500],
            order: [
                [1, 'asc']
            ], // Order by 'cus_id', adjust as needed
            initComplete: function(settings, json) {
                // console.log(json);

            },
            drawCallback: function(settings) {
                var json = settings.json; // Get the JSON response
                // console.log(json);
                // Update total bill and total connection fee after each table draw
                // $('#totalbill').text(json.totalbill);
            }
        });

        // Trigger table reload when filters are changed
        $('#zone-filter, #billing-person-filter, #status-filter, #date-from, #date-to, #activity_type').on('change', function() {
            table.ajax.reload();
        });


        // Handle SMS button click event
        $('#customer-datatable').on('click', '.restore', function() {
            $.get("./pages/customer/customer_delete_ajax.php", {
                token: $(this).data('customerid')
            }, function(result) {
                result = JSON.parse(result)
                if (result.response == true) {
                    Swal.fire({
                        icon: "success",
                        title: 'Restore Successfull',
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
<?php $obj->end_script(); ?>