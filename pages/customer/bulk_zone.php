<?php
include('customer.php');
?>
<div class="col-md-12">
    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="card-title mb-0">Customer List</h5>
            <div>
                <button class="btn btn-success-600 btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#zoneModal">Bulk Zone Select</button>
                <button class="btn btn-success-600 btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#packageModal">Bulk Package Select</button>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table
                class="table table-striped bordered-table mb-0"
                id="customer-datatable"
                data-page-length="10">
                <thead>
                    <tr>
                        <th scope="col"> 
                        <!--<input class="form-check-input me-1" type="checkbox" value="" id="check_all">-->
                        <label for="check_all">Check Box</label> 
                        </th>
                        <th scope="col">SL.</th>
                        <th scope="col">ID</th>
                        <th scope="col">IP</th>
                        <th scope="col">Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Mobile No</th>
                        <th scope="col">Package</th>
                        <th scope="col">Zone</th>
                    </tr>
                </thead>
                <tbody id="customer_body">
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="zoneModal" tabindex="-1" aria-labelledby="saleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5">Select zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="post" class="p-3 bg-light rounded shadow-sm">
                            <div class="row g-3 align-items-end">
                                <!-- Zone select -->
                                <div class="col-md-8">
                                    <label for="zone-filter" class="form-label fw-bold text-dark">Select Zone</label>
                                    <select id="zone-modal-filter" name="zone_filter" class="form-select">
                                        <option value="">All Zones</option>
                                        <?php $conditions = "level = 1"; ?>
                                        <?php foreach ($obj->view_all_by_cond('tbl_zone', $conditions) as $zone): ?>
                                            <option value="<?= $zone['zone_id']; ?>"><?= $zone['zone_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Submit button -->
                                <div class="col-md-4 d-flex">
                                    <button type="submit" name="zone_select" id="zone_select" class="btn btn-success w-100 mt-3 mt-md-0">
                                        <i class="bi bi-check2-square me-1"></i>Zone Select
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="packageModal" tabindex="-1" aria-labelledby="saleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5">Select zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="post" class="p-3 bg-light rounded shadow-sm">
                            <div class="row g-3 align-items-end">
                                <!-- Zone select -->
                                <div class="col-md-8">
                                    <label for="zone-filter" class="form-label fw-bold text-dark">Select Zone</label>
                                    <select id="package-modal-filter" name="zone_filter" class="form-select">
                                        <option value="">All Packages</option>
                                        <?php foreach ($obj->rawsql('SELECT package_name from tbl_package' ) as $package): ?>
                                            <option value="<?= $package['package_name']; ?>"><?= $package['package_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Submit button -->
                                <div class="col-md-4 d-flex">
                                    <button type="submit" name="zone_select" id="zone_select" class="btn btn-success w-100 mt-3 mt-md-0">
                                        <i class="bi bi-check2-square me-1"></i>Zone Select
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $obj->start_script(); ?>
<script>
    $(document).on('submit', '#packageModal', function(e){
        e.preventDefault();
         let selectedCustomerIds = [];
        $("#customer_body input[type='checkbox']:checked").each(function(){
            selectedCustomerIds.push($(this).val());
        });
        let selectedPackage = $('#package-modal-filter').val();
        console.log('Selected Package:', selectedPackage);
        console.log('Selected Customers:', selectedCustomerIds);
         $.ajax({
            url: "./pages/customer/bulk_ajax.php",
            type: 'POST',
            data: {
                selectedCustomerIds: selectedCustomerIds,
                selectedPackage: selectedPackage
            },
            success: function(res){
                console.log(res);
                $('#packageModal').modal('hide');
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            },
            error: function(err){
                console.log('Error:', err);
            }
        });
    });
    
    $(document).on('submit', '#zoneModal', function(e) {
        e.preventDefault();
        let selectedZone = $('#zone-modal-filter').val();
        let selectedCustomerIds = [];
        $("#customer_body input[type='checkbox']:checked").each(function() {
            selectedCustomerIds.push($(this).val());
        });
        $.ajax({
            url: "./pages/customer/bulk_ajax.php",
            type: 'POST',
             dataType: 'json',
            data: {
                selectedCustomerIds: selectedCustomerIds,
                selectedZone: selectedZone
            },
            success: function(res) {
                console.log(res.zone);
                $('#zoneModal').modal('hide');
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            },
            error: function(err) {
                console.log('Error:', err);
            }
        });
    });

    $(document).on('change', '#check_all', function() {
        let checked = $(this).prop('checked');
        $("#customer_body input[type='checkbox']").prop('checked', checked);
    });

    function loadCustomers() {
        $.ajax({
            url: "./pages/customer/bulk_ajax.php",
            type: "GET",
            dataType: "json",
            success: function(res) {
                let rows = "";
                res.data.forEach((item, index) => {
                    rows += `
                    <tr>
                       <td>
                            <label class="px-3 m-1 bg-info bg-opacity-10 border border-info border-start-0 rounded-2" for="firstCheckbox${index}"> <input class="form-check-input me-1" type="checkbox" value="${item.ag_id}" id="firstCheckbox${index}"> Select</label>
                        </td>
                        <td>${index + 1}</td>   
                        <td>${item.cus_id}</td>
                        <td>${item.ip ?? 'N/A'}</td>
                        <td>${item.ag_name ?? 'N/A'}</td>
                        <td>${item.ag_office_address ?? 'N/A'}</td>
                        <td>${item.ag_mobile_no ?? 'N/A'}</td>
                        <td>${item.mb ?? 'N/A'}</td>
                        <td>${item.zone_name ?? 'N/A'}</td>
                    </tr>
                `;
                });
                $("#customer_body").html(rows);
            }
        });
    }
    $(document).ready(function() {
        loadCustomers();
    });
</script>
<?php $obj->end_script(); ?>