<?php
$token = isset($_GET['token']) ? $_GET['token'] : NULL;
// $bonus = $obj->rawSql("SELECT * FROM customer_billing LEFT JOIN tbl_agent ON customer_billing.agid = tbl_agent.ag_id WHERE agid = '$token'");
$bonus = $obj->rawSql("SELECT * FROM bonus LEFT JOIN tbl_agent ON bonus.ag_id = tbl_agent.ag_id WHERE bonus.ag_id = '$token'");
?>
<style>
    .table-responsive {
        overflow-x: auto;
    }
</style>

<!-- Include DataTables and Buttons extension CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<div class="col-md-12">
    <div class="card basic-data-table">
        <div class="card-body table-responsive">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                <h5 class="card-title mb-0">Same Discount List</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table bordered-table mb-0 datatable"
                    id="dataTable"
                    data-page-length="10">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Agent Name</th>
                            <th>Agent Phone</th>
                            <th>Agent Address</th>
                            <th>Agent Email</th>
                            <th>Bonus Amount</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        $totalDiscount = 0;
                        foreach ($bonus as $value) {
                            $i++;
                            $totalDiscount += $value['amount'];
                        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo !empty($value['ag_name']) ? $value['ag_name'] : 'N/A'; ?></td>
                                <td><?php echo !empty($value['ag_mobile_no']) ? $value['ag_mobile_no'] : 'N/A'; ?></td>
                                <td><?php echo !empty($value['ag_office_address']) ? $value['ag_office_address'] : 'N/A'; ?></td>
                                <td><?php echo !empty($value['ag_email']) ? $value['ag_email'] : 'N/A'; ?></td>
                                <td><?php echo !empty($value['amount']) ? $value['amount'] : 0; ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5">Total Discount</th>
                            <th colspan="4"><?php echo $totalDiscount; ?> tk</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>


<?php $obj->start_script(); ?>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            dom: '<"d-flex justify-content-between align-items-center"<"btn-group"B><"search-box"f>>rtip', // Custom layout
            buttons: [{
                    extend: 'copy',
                    className: 'btn btn-secondary',
                    text: 'Copy'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-secondary',
                    text: 'CSV'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-secondary',
                    text: 'Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-secondary',
                    text: 'PDF'
                },
                {
                    extend: 'print',
                    className: 'btn btn-secondary',
                    text: 'Print'
                }
            ]
        });
    });
</script>
<?php $obj->end_script(); ?>