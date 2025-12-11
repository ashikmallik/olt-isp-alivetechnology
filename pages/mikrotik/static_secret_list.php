<?php
$allmikrotik = $obj->getAllData('mikrotik_user'); ?>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation (optional but recommended)
    if (!empty($_POST['name']) && !empty($_POST['target']) && !empty($_POST['max-limit']) && !empty($_POST['priority']) && !empty($_POST['queue'])) {
        // Save data (example - adjust to your DB handling)
        // $obj->insertStaticUser($name, $target, $max_limit, $priority, $queue);
        $mk = $obj->getSingleData('mikrotik_user', ['where' => [['id', '=', '1'],['status', '=', '1']]]);
        
        if($mk){
            $statikMikrotik = new Mikrotik($mk["mik_ip"],$mk["mik_port"],$mk["mik_username"],$mk["mik_password"]);
            $response = $statikMikrotik->comm("/queue/simple/add", [
                "name" => $_POST['name'],
                "target" => $_POST['target'],  // Static IP
                "max-limit" => $_POST['max-limit'],           // Upload/Download speed
                "priority" => $_POST['priority'],              // Default priority
                "queue" => $_POST['queue'],     // Queue type
            ]);
        }
        
        
        if(!empty($response)){
            if(isset($response['!trap'])){
                $errorMsg = $response['!trap'][0]['message'];
            }else {
                $successMsg = "Static user created successfully!";
            }
        } else {
            $errorMsg = "Failed to Create User";
        }

    } else {
        $errorMsg = "Please fill out all required fields.";
    }
    
    
    // delete user start
    if (isset($_POST['delete_static_user']) && !empty($_POST['delete_id'])) {
    $queueId = $_POST['delete_id']; // যেমন: *3F22

    $mk = $obj->getSingleData('mikrotik_user', [
        'where' => [
            ['id', '=', '1'],
            ['status', '=', '1']
        ]
    ]);

    if ($mk) {
        $mikrotik = new Mikrotik($mk["mik_ip"], $mk["mik_port"], $mk["mik_username"], $mk["mik_password"]);

            if (!empty($mikrotik) && $mikrotik->connected) {
                $response = $mikrotik->comm('/queue/simple/remove', [
                    ".id" => $queueId
                ]);
    
            if(!empty($response)){
                if(isset($response['!trap'])){
                    $errorMsg = $response['!trap'][0]['message'];
                }
            } else {
                $successMsg = "User Delted Successfully.";
            }
        }
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

<div class="col-xl-12">
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
</div>


<!--modal start-->
<div class="modal fade" id="createStaticUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Add Static User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="CreateStaticUserForm"  method="POST">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="target" class="form-label">Target</label>
                        <input placeholder="192.168.26.161/32" type="text" class="form-control" id="target" name="target" required>
                    </div>
                    <div class="mb-3">
                        <label for="max-limit" class="form-label">Max Limit</label>
                        <input placeholder="5M/5M" type="text" class="form-control" id="max-limit" name="max-limit" required>
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <input placeholder="8/8" type="text" class="form-control" id="priority" name="priority" required>
                    </div>
                    <div class="mb-3">
                        <label for="queue" class="form-label">Queue</label>
                        <input value="default/default" type="text" class="form-control" id="queue" name="queue" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success waves-effect waves-light" id="createStaticUserSaveBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<!--modal end-->



<div class="card h-100 p-0 radius-12">
    <div class="card-body">
        <div class="row mt-5">
            <div class="col-xxl-3 col-sm-6">
                <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-1">
                    <div class="card-body p-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
        
                            <div class="d-flex align-items-center gap-2">
                                <span class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                    <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-sm">Total Active Customers</span>
                                    <h6 id="activeStaticUsers" class="fw-semibold">0</h6>
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
                                    <h6 id="inactiveStaticUser" class="fw-semibold" style="display: flex; justify-content: start; align-items: center;">0</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card basic-data-table">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="card-title mb-0">All Secret List</h5>
        <button id="createStaticUser" class="btn btn-success">Add Static User</button>
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
                    <th scope="col">IP</th>
                    <th scope="col">Queue Name</th>
                    <th scope="col">Tx/Rx (rate)</th>
                    <th scope="col">Total Used (MB)</th>
                    <th scope="col">Status</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody id="secret_body">
            </tbody>
        </table>
    </div>
</div>


<?php $obj->start_script(); ?>
<script src="assets/libs/jquery-tabledit/jquery.tabledit.min.js"></script>

<script>
    // open form create static user modal
    $("#createStaticUser").on("click", function(){
         $('#createStaticUserModal').modal('show');
    })
    
    $("#createStaticUserSaveBtn").on("click", function(){
        $("#CreateStaticUserForm").submit();
    })

    // enable or disable
    $(document).on("click", ".changeStatus", function () {
        let button = $(this);
    
        $.ajax({
            url: "./pages/mikrotik/change_static_user_status.php",
            method: "POST",
            data: {
                mikrotik_id: 1,
                name: button.data("name"),
                status: button.data("status") == 1 ? 0 : 1
            },
            success: function (response) {
                alert("Status changed successfully");
                let newStatus = button.data("status") == 1 ? 0 : 1;
                button.data("status", newStatus);
    
                if (newStatus == 1) {
                    button.removeClass("btn-danger").addClass("btn-success").text("Enable");
                } else {
                    button.removeClass("btn-success").addClass("btn-danger").text("Disable");
                }
            }
        });
    });


    let sectetShow = true;

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
        $.ajax({
            type: "GET",
            url: "./pages/mikrotik/connect_ajax.php",
            data: {
                'mikrotikStatikUser': id
            },
            dataType: "JSON",
            success: function(response) {
                if (response.connection) {
                    // console.log("inactive", response.totalInactive)
                    $("#inactiveStaticUser").text(response.totalInactive)
                    $("#activeStaticUsers").text(response.totalActive)
                    if ($.fn.DataTable.isDataTable('#secrettable')) {
                        $('#secrettable').DataTable().destroy();
                    }
                
                    $('#secret_body').html('');
                    $('#secret_body').html(`${response.status}`);
                    
                    $('#secrettable').DataTable({
                        pageLength: 10,
                        responsive: true
                    });
                } else {
                    $('#secret_body').html('');
                }
            },
            error: function(e) {
                alert(e);
            }
        });
    }
</script>
<?php $i = 1;
foreach ($allmikrotik as $mikrotiks) { ?>
    <script>
        mkConnectCheck(`<?php echo $mikrotiks['id']; ?>`, `<?php echo $mikrotiks['mik_ip']; ?>`);
    </script>
<?php } ?>
<?php $obj->end_script(); ?>