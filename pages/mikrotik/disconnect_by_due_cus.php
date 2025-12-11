<?php
$allmikrotik = $obj->getAllData('mikrotik_user');
$allPackages = $obj->getAllData('tbl_package');


?>


<?php if (!empty($successMsg)): ?>
    <div class="alert alert-success"><?= $successMsg; ?></div>
<?php endif; ?>

<?php if (!empty($errorMsg)): ?>
    <div class="alert alert-danger"><?= $errorMsg; ?></div>
<?php endif; ?>


<div class="card">
    <div class="card-body">
        <!-- Form Wizard Start -->
        <div class="form-wizard">
            <fieldset class="wizard-fieldset show">
                <div class="row gy-3">
                    <!-- <div class="col-sm-3">
                            <label for="zone-filter" class="form-label">Zone</label>
                            <div>
                                <select id="zone-filter" class="form-control">
                                    <option value="">All Zones</option>
                                    <?php foreach ($bzones as $zone): ?>
                                        <option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['zone_name']; ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div> -->

                    <div class="col-sm-2">
                        <label for="status-filter" class="form-label">Status</label>
                        <div class="position-relative">
                            <select id="status-filter" class="form-control">
                                <option value="">All</option>
                                <option value="0">Enable</option>
                                <option value="1">Disable</option>
                            </select>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <!-- Form Wizard End -->
    </div>
</div>



<div class="card h-100 p-0 radius-12">
    <div class="card-body">
        <div class="row">
            <div class="col-xxl-3 col-sm-6">
                <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-1">
                    <div class="card-body p-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                            <div class="d-flex align-items-center gap-2">
                                <span class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                    <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-sm">Total Active Users</span>
                                    <h6 id="activeSecretUsers" class="fw-semibold">0</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-sm-6">
                <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-start-5">
                    <div class="card-body p-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                            <div class="d-flex align-items-center gap-2">
                                <span class="mb-0 w-48-px h-48-px bg-danger flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                    <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-sm"><?= date("F") ?> Inactive Users</span>
                                    <h6 id="inactiveSecretUser" class="fw-semibold" style="display: flex; justify-content: start; align-items: center;">0</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="card h-100 p-0">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <h6 class="text-lg fw-semibold mb-0">Mikrotik Connection</h6>
    </div>
    <div class="card-body p-24">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <?php foreach ($allmikrotik as $mikrotiks):  ?>
                <div class="col-md-2 mb-2"> <span id="m<?php echo $mikrotiks['id']; ?>"><button class="btn btn-primary" type="button" disabled> <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...</button> </span></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!--<?php foreach ($allmikrotik as $mikrotiks): ?>-->
    <!-- Disconnect Button -->
<!--    <button type="button" class="btn btn-danger col-md-2 m-2 actionBtn" -->
<!--        data-action="disconnect" -->
<!--        data-mik="<?= $mikrotiks['id'] ?>">-->
<!--        Disconnect-->
<!--    </button>-->

    <!-- Reconnect Button -->
<!--    <button type="button" class="btn btn-success col-md-2 m-2 actionBtn" -->
<!--        data-action="reconnect" -->
<!--        data-mik="<?= $mikrotiks['id'] ?>">-->
<!--        Reconnect-->
<!--    </button>-->
<!--<?php endforeach; ?>-->
<!-- Disconnect Button -->
<button type="button" id="disconnectBtn" class="btn btn-danger col-md-2 m-2 actionBtn" 
    data-action="disconnect">
    Disconnect All Due Customers
</button>

<!-- Reconnect Button -->
<button type="button" id="reconnectBtn" class="btn btn-success col-md-2 m-2 actionBtn" 
    data-action="reconnect">
    Reconnect All Due Customers
</button>

<!-- Pre-disconnect Button -->
<!--<button type="button" class="btn btn-warning col-md-2 m-2 actionBtn" data-action="predisconnect" data-mik="<?= $mik_id ?>">Pre-Disconnect</button>-->


<div class="card basic-data-table">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="card-title mb-0">Due Customer List</h5>
    </div>
    <div class="card-body">
        <table
            class="table bordered-table mb-0"
            id="secrettable"
            data-page-length="10">
            <thead>
                <tr>
                    <th scope="col">SL.No</th>
                    <th scope="col">Name</th>
                    <th scope="col">Package</th>
                    
                    <th scope="col">Last Log out</th>
                    
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody id="secret_body">
            </tbody>
        </table>
    </div>
</div>

<!--add secret modal end-->

<?php $obj->start_script(); ?>
<script src="assets/libs/jquery-tabledit/jquery.tabledit.min.js"></script>

<script>
    let sectetShow = true;
    // set selected mikrotik id globally
    let currentLoadedMikrotikId = null; // sadi

    // check connection by ashik
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


    function mkAllSecret(id) {
        
    
    // set selected mikrotik id globally
    currentLoadedMikrotikId = id;

    $.ajax({
        type: "GET",
        url: "./pages/mikrotik/connect_ajax.php",
        data: {
            'mkidduedue': id,            // <-- Fetch due customers first
            'secretStatus': $('#status-filter').val()
        },
        dataType: "JSON",
        success: function(response) {
            console.log('Due & All Secrets:', response);

            if (response.connection) {
                // Update counters
                $("#activeSecretUsers").text(response?.totalEnableSecret ?? 0);
                $("#inactiveSecretUser").text(response?.totalDue ?? response?.totalDisableSecret ?? 0);

                // Destroy existing DataTable if exists
                if ($.fn.DataTable.isDataTable('#secrettable')) {
                    $('#secrettable').DataTable().destroy();
                }

                // Populate table body
                $('#secret_body').html('');
                // First due customers (if any)
                if(response.dueHtml){
                    $('#secret_body').append(response.dueHtml);
                }
                // Then all secrets
                if(response.status){
                    $('#secret_body').append(response.status);
                }

                // Initialize DataTable
                $('#secrettable').DataTable({
                    pageLength: 100,
                    responsive: true
                });
            } else {
                $('#secret_body').html('<tr><td colspan="7">No data found</td></tr>');
            }
        },
        error: function(e) {
            alert("Something went wrong while loading secrets or due customers.");
        }
    });
}

// all disconnect dus cus
    $(document).on('click', '.actionBtn', function() {
    if (!currentLoadedMikrotikId) {
        alert("Please select a Mikrotik first.");
        return;
    }

    var mikId = currentLoadedMikrotikId;
    
    console.log(mikId);
    var action = $(this).data('action');
    var btn = $(this);
    
    if (!confirm(`Are you sure you want to ${action} for Mikrotik ID: ${mikId}?`)) {
    return;
    }

    btn.prop('disabled', true).text('Processing...');

    $.ajax({
        url: './pages/mikrotik/connect_ajax.php',
        type: 'POST',
        data: {
            action: action,
            mikrotikId: mikId
        },
        dataType: 'JSON',
        success: function(response) {
            if (response.success) {
                alert(action.charAt(0).toUpperCase() + action.slice(1) + " successful!");
                // reload the secrets table for this Mikrotik
                mkAllSecret(mikId);
            }
            btn.prop('disabled', false).text(action.charAt(0).toUpperCase() + action.slice(1));
        },
        error: function() {
            alert("Something went wrong!");
            btn.prop('disabled', false).text(action.charAt(0).toUpperCase() + action.slice(1));
        }
    });
});








    $("#status-filter").change(function() {
        mkAllSecret(currentLoadedMikrotikId);
    })
</script>
<?php $i = 1;
foreach ($allmikrotik as $mikrotiks) { ?>
    <script>
        mkConnectCheck(`<?php echo $mikrotiks['id']; ?>`, `<?php echo $mikrotiks['mik_ip']; ?>`);
    </script>
<?php } ?>

<!--single disconect (ashik) -->
<script>
    $(document).ready(function() {
        $('#secrettable').DataTable();

        $('#secrettable').on('click', 'button.secretCangeStatus', function(e) {
            e.preventDefault();
            var secretName = $(this).data('name');

            var status = $(this).html();
            $.ajax({
                type: 'get',
                url: './pages/mikrotik/connect_ajax.php',
                data: {
                    name: secretName,
                    state: status,
                    selectedMktik: currentLoadedMikrotikId
                },
                success: function(result) {
                    console.log('ed', result);
                }
            });
            if (status == 'Enable') {
                $(this).html('Disable');
                $(this).removeClass('btn-success');
                $(this).addClass('btn-danger');
            } else {
                $(this).html('Enable');
                $(this).removeClass('btn-danger');
                $(this).addClass('btn-success');
            }
        });
    });
</script>
<?php $obj->end_script(); ?>