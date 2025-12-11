<?php
$allmikrotik = $obj->getAllData('mikrotik_user');
$allPackages = $obj->getAllData('tbl_package');
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation (optional but recommended)
    if (!empty($_POST['mikrotik']) && !empty($_POST['name']) && !empty($_POST['password']) && !empty($_POST['profile'])) {

        $response = $obj->createNewSecret($_POST['mikrotik'], $_POST['name'], $_POST['password'], $_POST['profile'], $_POST['comment']);

        var_dump($response);

        if (!empty($response)) {
            if (isset($response['!trap'])) {
                $errorMsg = $response['!trap'][0]['message'];
            } else {
                $successMsg = "Static user created successfully!";
            }
        } else {
            $errorMsg = "Failed to Create User";
        }
    }



    // delete user start
    if (isset($_POST['delete_static_user']) && !empty($_POST['delete_id'])) {

        $response = $obj->removeSecret(1, $_POST['delete_id']);
        var_dump($response);
        if (!empty($response)) {
            if ($response) {
                $successMsg = "User Delted Successfully.";
            }
        } else {
            $errorMsg = $response['!trap'][0]['message'];
        }
    }
    // delete user end
}
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


<div class="card basic-data-table">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="card-title mb-0">All Secret List</h5>
        <button id="createSecret" class="btn btn-success">Add Secret User</button>
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
                    <th scope="col">Password</th>
                    <th scope="col">Profile</th>
                    <th scope="col">Last Log out</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody id="secret_body">
            </tbody>
        </table>
    </div>
</div>


<!--add secret modal start-->
<div class="modal fade" id="createSecretModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Add Static User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createSecretForm" method="POST">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Mikrotik</label>
                        <select class="form-control wizard-required" name="mikrotik" id="mikrotik">
                            <option value="">Select</option>
                            <?php foreach ($allmikrotik as $key => $mikrotiks):  ?>
                                <option <?php echo $key == 0 ? "selected" : ""; ?> value="<?= $mikrotiks['id'] ?>"><?= $mikrotiks['mik_ip'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input placeholder="" type="text" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="profile" class="form-label">Profile</label>
                        <select class="form-control wizard-required" name="profile" id="profile">
                            <option value="">Select</option>
                            <?php foreach ($allPackages as $key => $package):  ?>
                                <option value="<?= $package['net_speed'] ?>"><?= $package['net_speed'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <input placeholder="Comment" type="text" class="form-control" id="comment" name="comment" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success waves-effect waves-light" id="createSecretSaveBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<!--add secret modal end-->

<?php $obj->start_script(); ?>
<script src="assets/libs/jquery-tabledit/jquery.tabledit.min.js"></script>

// open form create static user modal
<script>
    $("#createSecret").on("click", function() {
        $('#createSecretModal').modal('show');
    })

    $("#createSecretSaveBtn").on("click", function() {
        $("#createSecretForm").submit();
    })
</script>

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

    function mkAllSecret(id) {
        // set selected mikrotik id globally
        currentLoadedMikrotikId = id;
        $.ajax({
            type: "GET",
            url: "./pages/mikrotik/connect_ajax.php",
            data: {
                'mkidsecretall': id,
                'secretStatus': $('#status-filter').val()
            },
            dataType: "JSON",
            success: function(response) {
                console.log('sss', response);
                if (response.connection) {
                    $("#activeSecretUsers").text(response?.totalEnableSecret);
                    $("#inactiveSecretUser").text(response?.totalDisableSecret);

                    if ($.fn.DataTable.isDataTable('#secrettable')) {
                        $('#secrettable').DataTable().destroy();
                    }


                    $('#secret_body').html('');
                    $('#secret_body').html(`${response.status}`);


                    $('#secrettable').DataTable({
                        pageLength: 100,
                        lengthMenu: [ [10, 25, 100, 500, 1000], [10, 25, 100, 500, 1000] ],
                        responsive: true
                    });
                } else {
                    $('#secret_body').html('');
                }
            },
            error: function(e) {
                alert("Something went wrong while loading secrets.");
            }
        });
    }

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