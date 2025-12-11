<?php
 include('employee.php');

// Delete Employee
if (isset($_GET['delete-token'])) {
    $token = $_GET['delete-token'];
    $obj->deleteData("tbl_employee", ['where' => ['id', '=', $token]]);
}
?>

<!-- Add/Edit Modal Trigger -->

<div class="d-flex justify-content-end mb-2">
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#employeeModal"><i class="fas fa-plus"></i> Add Employee</button>
</div>
<!-- Table View -->
<table class="table table-bordered" id="dataTable">
    <thead>
        <tr>
            <th>SL</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>Designation</th>
            <th>Salary Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $employees = $obj->getAllData('tbl_employee');
        $i = 1;
        foreach ($employees as $emp):
        ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><a class="btn btn-success" href="?page=employee_ladger&employeeid=<?php echo $emp['id'] ?>"><?php echo $emp['employee_name'] ?></a></td>
            <td><?= $emp['employee_mobile_no'] ?></td>
            <td><?= $emp['employee_email'] ?></td>
            <td><?= $emp['designation'] ?></td>
            <td><?= $emp['salary_amount'] ?></td>
            
            <td><?= $emp['employee_status'] == 1 ? 'Active' : 'Inactive' ?></td>
            <td>
                <button class="btn btn-warning btn-sm employee_update_data"
                    data-emp_id="<?= $emp['id'] ?>"
                    data-name="<?= $emp['employee_name'] ?>"
                    data-phone="<?= $emp['employee_mobile_no'] ?>"
                    data-email="<?= $emp['employee_email'] ?>"
                    data-nid="<?= $emp['employee_national_id'] ?>"
                    data-designation="<?= $emp['designation'] ?>"
                    data-joining_date="<?= $emp['joining_date'] ?>"
                    data-salary="<?= $emp['salary_amount'] ?>"
                    data-address="<?= $emp['employee_address'] ?>"
                    data-status="<?= $emp['employee_status'] ?>"
                    data-bs-toggle="modal" data-bs-target="#employeeModal">Edit</button>

                <a href="?page=employee_view&delete-token=<?= $emp['id'] ?>" class="btn btn-danger btn-sm delete-confirm">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal Form -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="employeeModalLabel">Employee Info</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row">
            <input type="hidden" id="em_id" name="em_id">

            <div class="col-md-6 mb-3">
                <label>Employee Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Mobile</label>
                <input type="text" name="phone" id="phone" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Email</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>NID</label>
                <input type="text" name="nid" id="nid" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Designation</label>
                <input type="text" name="designation" id="designation" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Joining Date</label>
                <input type="date" name="joining_date" id="joining_date" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Salary</label>
                <input type="number" name="salary_amount" id="salary_amount" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Status</label>
                <select name="activeStatus" id="activeStatus" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="col-md-12 mb-3">
                <label>Address</label>
                <textarea name="address" id="address" class="form-control"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="submit" class="btn btn-success" id="modalSubmitBtn">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JS Script -->
<script>
    document.querySelectorAll('.employee_update_data').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('em_id').value = this.dataset.emp_id;
            document.getElementById('name').value = this.dataset.name;
            document.getElementById('phone').value = this.dataset.phone;
            document.getElementById('email').value = this.dataset.email;
            document.getElementById('nid').value = this.dataset.nid;
            document.getElementById('designation').value = this.dataset.designation;
            document.getElementById('joining_date').value = this.dataset.joining_date;
            document.getElementById('salary_amount').value = this.dataset.salary;
            document.getElementById('address').value = this.dataset.address;
            document.getElementById('activeStatus').value = this.dataset.status;

            document.getElementById('modalSubmitBtn').setAttribute('name', 'update');
            document.getElementById('modalSubmitBtn').innerText = 'Update';
        });
    });

    // Optional reset on modal hide
    document.getElementById('employeeModal').addEventListener('hidden.bs.modal', function () {
        document.querySelector('form').reset();
        document.getElementById('modalSubmitBtn').setAttribute('name', 'submit');
        document.getElementById('modalSubmitBtn').innerText = 'Save';
        document.getElementById('em_id').value = '';
    });

    // Delete confirmation (SweetAlert optional)
    document.querySelectorAll('.delete-confirm').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this employee?')) {
                window.location.href = this.href;
            }
        });
    });
</script>
