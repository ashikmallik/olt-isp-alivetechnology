<?php

$userId = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;

// EDIT MODE: check if edit-token is set
$editDevice = null;
if (isset($_GET['edit-token'])) {
    $editId = intval($_GET['edit-token']);
    $editDevice = $obj->view_all_by_cond('tbl_olt_devices', "id = $editId")[0] ?? null;
}

// INSERT DEVICE
if (isset($_POST['submit'])) {
    $fromInsert = [
        'vendor_id'      => $_POST['vendor_id'],
        'device_ip'      => $_POST['device_ip'],
        'snmp_community' => $_POST['snmp_community'],
        'entry_by'       => $userId,
        'entry_date'     => date('Y-m-d'),
    ];

    $obj->insertData('tbl_olt_devices', $fromInsert);

    $obj->notificationStore("New OLT Device Added Successfully", 'success');
    echo '<script>window.location="?page=device_list";</script>';
    exit;
}

// UPDATE DEVICE
if (isset($_POST['update'])) {
    $fromUpdate = [
        'vendor_id'      => $_POST['vendor_id'],
        'device_ip'      => $_POST['device_ip'],
        'snmp_community' => $_POST['snmp_community'],
        'entry_by'       => $userId,
    ];

    $obj->updateData('tbl_olt_devices', $fromUpdate, ['id' => $_POST['device_id']]);

    $obj->notificationStore("OLT Device Updated Successfully", 'success');
    echo '<script>window.location="?page=device_list";</script>';
    exit;
}

// DELETE DEVICE
if (isset($_GET['delete-token'])) {
    $token = intval($_GET['delete-token']);
    $deleted = $obj->singleDeleteData("tbl_olt_devices", "id = $token");

    $obj->notificationStore(
        $deleted ? "OLT Device Deleted Successfully" : "Failed to delete OLT Device.",
        $deleted ? "success" : "danger"
    );

    echo '<script>window.location="?page=device_list";</script>';
    exit;
}

// SHOW NOTIFICATIONS
$obj->notificationShow();

// FETCH DEVICES
$devices = $obj->view_all('tbl_olt_devices');
?>

<style>
.table-responsive { overflow-x: auto; }
</style>

<div class="col-md-12 mb-3">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#deviceModal" id="openAddModal">
        ➕ Add New Device
    </button>
</div>

<div class="col-md-12">
    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="card-title mb-0">OLT Device List</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered" id="dataTable">
                <thead>
                    <tr>
                        <th>SL.</th>
                        <th>Vendor</th>
                        <th>Device IP</th>
                        <th>SNMP Community</th>
                        <?php if ($obj->userWorkPermission('edit')) { ?>
                            <th>Action</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($devices as $index => $device) { ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                            <?php
                                if ($device['vendor_id'] == 1) {
                                    echo 'BDCOM (EPON)';
                                } elseif ($device['vendor_id'] == 2) {
                                    echo 'BDCOM (GPON)';
                                } elseif ($device['vendor_id'] == 3) {
                                    echo 'DBC (EPON)';
                                } elseif ($device['vendor_id'] == 4) {
                                    echo 'V-SOL (EPON)';
                                }elseif ($device['vendor_id'] == 5) {
                                    echo 'Ecom';
                                }
                                elseif ($device['vendor_id'] == 6) {
                                    echo 'TVS Poton (EPON)';
                                }
                                 else {
                                    echo 'Unknown Vendor';
                                }
                            ?>
                        </td>

                            <td><?= $device['device_ip'] ?></td>
                            <td><?= $device['snmp_community'] ?></td>
                              
                                  <td>

                                      <!-- Interface Status -->
                                      <a href="?page=interface_state&id=<?= $device['id'] ?>&vendor=<?= $device['vendor_id'] ?>&ip=<?= urlencode($device['device_ip']) ?>&community=<?= urlencode($device['snmp_community']) ?>"
                                        class="btn btn-sm btn-success">
                                        Interface Status
                                      </a>

                                      <!-- Device Condition -->
                                      <a href="?page=device_condition&id=<?= $device['id'] ?>&vendor=<?= $device['vendor_id'] ?>&ip=<?= urlencode($device['device_ip']) ?>&community=<?= urlencode($device['snmp_community']) ?>"
                                        class="btn btn-sm btn-warning">
                                        Device Condition
                                      </a>


                                      <!-- Device Diagram -->
                                      <a href="?page=olt_diagram&id=<?= $device['id'] ?>&vendor=<?= $device['vendor_id'] ?>&ip=<?= urlencode($device['device_ip']) ?>&community=<?= urlencode($device['snmp_community']) ?>"
                                        class="btn btn-sm btn-info">
                                         Diagram
                                      </a>

                                    <?php if ($obj->userWorkPermission('edit')) { ?>
                                      <!-- Edit Modal Button -->
                                      <button class="btn btn-sm btn-primary editBtn"
                                          data-id="<?= $device['id'] ?>"
                                          data-vendor="<?= $device['vendor_id'] ?>"
                                          data-ip="<?= $device['device_ip'] ?>"
                                          data-community="<?= $device['snmp_community'] ?>">
                                          Edit
                                      </button>
                                    <?php } ?>
                                    <?php if ($obj->userWorkPermission('delete')) { ?>
                                      <!-- Delete Button -->
                                      <a href="?page=device_list&delete-token=<?= $device['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                    <?php } ?>
                                  </td>
                              

                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ADD/EDIT DEVICE MODAL -->
<div class="modal fade" id="deviceModal" tabindex="-1" aria-labelledby="deviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="deviceForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="deviceModalLabel">Add/Edit Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="device_id" id="device_id">
                    <div class="mb-3">
                        <label class="form-label">Vendor:</label>
                        <select class="form-select" name="vendor_id" id="vendor_id" required>
                            <option value="">--Select--</option>
                            <option value="1">BDCOM (EPON)</option>
                            <option value="2">BDCOM (GPON)</option>
                            <option value="3">DBC (EPON)</option>
                            <option value="4">V-SOL (EPON)</option>
                            <option value="5">Ecom</option>
                            <option value="6">TVS Poton (EPON)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Device IP:</label>
                        <input type="text" class="form-control" name="device_ip" id="device_ip" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SNMP Community:</label>
                        <input type="text" class="form-control" name="snmp_community" id="snmp_community" required>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-info" onclick="checkConnection()">
                            🔄 Check Connection
                        </button>
                        <div id="connection-status" class="mt-2 fw-bold"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary" id="modalSubmitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $obj->start_script(); ?>
<script>
const deviceModal = new bootstrap.Modal(document.getElementById('deviceModal'));

document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('device_id').value = btn.dataset.id;
        document.getElementById('vendor_id').value = btn.dataset.vendor;
        document.getElementById('device_ip').value = btn.dataset.ip;
        document.getElementById('snmp_community').value = btn.dataset.community;

        document.getElementById('modalSubmitBtn').name = 'update';
        document.getElementById('deviceModalLabel').innerText = 'Edit Device';
        deviceModal.show();
    });
});

// If Add button clicked, reset form
document.getElementById('openAddModal').addEventListener('click', () => {
    document.getElementById('deviceForm').reset();
    document.getElementById('modalSubmitBtn').name = 'submit';
    document.getElementById('deviceModalLabel').innerText = 'Add New Device';
    document.getElementById('connection-status').innerText = '';
});

// SNMP Connection Check
function checkConnection() {
    const device_ip = document.getElementById('device_ip').value;
    const snmp_community = document.getElementById('snmp_community').value;

    if (!device_ip || !snmp_community) {
        document.getElementById('connection-status').innerHTML = "❌ Please fill all fields.";
        return;
    }

    document.getElementById('connection-status').innerHTML = "⏳ Checking...";

    fetch('check_snmp_connection.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `device_ip=${encodeURIComponent(device_ip)}&snmp_community=${encodeURIComponent(snmp_community)}`
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('connection-status').innerHTML = data;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('connection-status').innerHTML = "❌ Error connecting.";
    });
}
</script>
<?php $obj->end_script(); ?>
