<?php
include('dashboard.php');
$stock = $obj->raw_sql("
    SELECT stock.*,
    p.product_name,
    s.supplier_name,
     _createuser.FullName
    FROM stock
    LEFT JOIN products p ON stock.product_id = p.product_id
    LEFT JOIN suppliers s ON stock.supplier_id = s.supplier_id
    LEFT JOIN _createuser ON stock.created_by = _createuser.UserId
    where stock.deleted_at is null order by stock.stock_id desc limit 5 offset 0");
$i = 1; // Initialize counter
$minimum_threshold = 0;
foreach ($stock as &$row) {
    $row['sl'] = $i++; // Add the row number
    $row['minimum_threshold'] = $minimum_threshold;
}

// $online   = $obj->countOnlineUsers(1);
// $offline  = $obj->countOfflineUsers(1);
// $disabled = $obj->countDisabledUsers(1);
?>

<!-- <div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto text-white" style="background: linear-gradient(135deg, #28a745, #218838);">
        <a href="?page=mikrotik_online_secret" class="text-white text-decoration-none">
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
             
                <div class="w-64-px h-64-px radius-16 bg-white bg-opacity-25 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-success d-flex justify-content-center align-items-center radius-8 h6 mb-0 text-white">
                        <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                    </span>
                </div>
               
                <div class="text-end">
                    <h6 class="fw-semibold my-1"><?php echo $online ?? 0; ?></h6>
                    <span class="mb-0 fw-medium text-white-50 text-md">Total Online Users</span>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto text-white" style="background: linear-gradient(135deg, #ffc107, #ff9800);">
        <a href="?page=mikrotik_offline" class="text-white text-decoration-none">
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                
                <div class="w-64-px h-64-px radius-16 bg-white bg-opacity-25 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-warning d-flex justify-content-center align-items-center radius-8 h6 mb-0 text-white">
                        <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                    </span>
                </div>
                
                <div class="text-end">
                    <h6 class="fw-semibold my-1"><?php echo $offline ?? 0; ?></h6>
                    <span class="mb-0 fw-medium text-white-50 text-md">Total Offline Users</span>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto text-white" style="background: linear-gradient(135deg, #dc3545, #a71d2a);">
        <a href="" class="text-white text-decoration-none">
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
              
                <div class="w-64-px h-64-px radius-16 bg-white bg-opacity-25 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-danger d-flex justify-content-center align-items-center radius-8 h6 mb-0 text-white">
                        <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                    </span>
                </div>
               
                <div class="text-end">
                    <h6 class="fw-semibold my-1"><?php echo $disabled ?? 0; ?></h6>
                    <span class="mb-0 fw-medium text-white-50 text-md">Total Disabled Users</span>
                </div>
            </div>
        </a>
    </div>
</div> -->
<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
        <a href="?page=customer_view">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <?php echo $obj->Total_Count("tbl_agent", "deleted_at IS NULL") ?? 0; ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">Total Customers</span>
            </div>
        </div>
        </a>
    </div>
</div>

<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
        <a href="?page=customer_view&ag_status=active">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <?php echo $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='1'") ?? 0; ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">Total Active Customer</span>
            </div>
        </div>
        </a>
    </div>
</div>



<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-5">
        <a href='?page=customer_view&ag_status=inactive'>
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-red flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="mdi:account-off" class="text-white text-2xl"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <?php echo $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='0'") ?? 0; ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">
                    Total Inactive Customer
                </span>
            </div>
        </div>
        </a>
    </div>
</div>

<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-5">
        <a href='?page=customer_view&ag_status=discontinue'>
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-red flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0" style="padding: 15px;">
                        <!-- <iconify-icon icon="mdi:account-off" class="text-white text-2xl"></iconify-icon> -->
                        <i class="fa-solid fa-user-xmark" style="font-size: 20px;"></i>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <?php echo $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='3'") ?? 0; ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">
                        Total Discontinue Customer
                    </span>
                </div>
            </div>
        </a>
    </div>
</div>


<!-- free customers -->
<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-2">
        <a href='?page=customer_view&ag_status=free'>
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-purple flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="mdi-account-tag-outline" class="text-white text-2xl"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <?php echo $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='2'") ?? 0; ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">
                    Total Free Customer
                </span>
            </div>
        </div>
        </a>
    </div>
</div>


<!-- running month customer -->
<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-4">
        <a href='?page=running_month_customers'>
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="mdi-account-plus-outline" class="text-white text-2xl"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <?php echo $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='1' AND MONTH(entry_date)='$currentNumericMonth' AND YEAR(entry_date)='$currentNumericYear'"); ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">
                    <?= date('F') . " New Customers"; ?>
                </span>
            </div>
        </div>
        </a>
    </div>
</div>



<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-5">
        <a href='?page=monthly_inactive_customer'>
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-red flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="mdi:account-off" class="text-white text-2xl"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold" id="">
                    <?php echo $obj->rawSqlSingle("SELECT COUNT(*) as TotalUsers FROM `tbl_agent` WHERE deleted_at is NULL  AND `ag_status` = '0' AND MONTH(inactive_date) = MONTH(CURDATE());")["TotalUsers"]; ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">
                    <?= date("F"); ?> Inactive Customer
                </span>
            </div>
        </div>
        </a>
    </div>
</div>


<!-- total bill collectoin -->
<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="hugeicons:invoice-03" class="icon"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    <?php
                    echo $obj->rawSqlSingle("SELECT SUM(totalpaid) as TotalCollectedBill FROM `customer_billing`")["TotalCollectedBill"];
                    ?>
                </h6>
                <span class="mb-2 fw-medium text-secondary-light text-sm">Total Collected Bill as of <span class="text-primary"><?php echo date('F') . " " . date("Y"); ?></span> </span>
            </div>
        </div>
    </div>
</div>



<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-2">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-purple flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    <?php echo $totalBillSummary['dueAdvacne'] ?? 0; ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">
                    <?php echo "Total Due Bill"; ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="mdi:lightning-bolt" class="text-white text-2xl"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    <?php echo $totalBillSummary['totalPreviousDue'] ?? 0; ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">
                    <?php echo  "Total Previous Due"; ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-5">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-red flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="fa6-solid:file-invoice-dollar" class="text-white text-2xl"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    <?php
                    echo $totalBillSummary['runningMonthDue'] ?? 0;
                    ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">
                    <?php echo date('F') . " Due Bill"; ?>
                </span>
            </div>
        </div>
    </div>
</div>


<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-4">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="fa6-solid:file-invoice-dollar" class="text-white text-2xl"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    <?php echo $get_all_collection['amount'] === null ? "0" : $get_all_collection['amount']; ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">
                    <?php echo date('F') . " Total Collection"; ?>
                </span>
            </div>
        </div>
    </div>
</div>



<!-- <div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
<div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
    <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
        <iconify-icon icon="mdi:lightning-bolt" class="text-white text-2xl"></iconify-icon>
    </span>
</div>
<div class="text-end">
    <h6 class="fw-semibold my-1 text-neutral-600">
        <i class="fa-solid fa-bangladeshi-taka-sign"></i>
        <?php echo $get_connection_charge['amount'] === null ? "0" : $get_connection_charge['amount']; ?>
    </h6>
    <span class="mb-0 fw-medium text-secondary-light text-md">
        <?php echo  date('F') . " C.Charge"; ?>
    </span>
</div>
</div>
</div>
</div> -->




<br><br>
<br><br>
<br><br>
<div class="row">
    <!-- Statistics Start -->
    <div class="col-xxl-4">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24">
                <h6 class="mb-2 fw-bold text-lg text-neutral-600">
                    Bill Collection Rate – <span class="text-success"><?= date("F") . " " . date("Y"); ?></span>
                </h6>

                <div class="mt-24">
                    <div id="dashboad_view_redial_chart" width="500" height="500"></div>
                </div>

            </div>
        </div>
    </div>

    <!-- Dashboard Widget End -->
    <div class="col-xxl-8 graphicalChart">
        <div class=" card h-100 p-0">
            <div class="card-header border-bottom bg-base py-16 px-24" style="display: flex; justify-content: space-between;">
                <h6 class="card-title text-lg fw-semibold mb-0">
                    Monthly Bill Collection
                    <span id="monthlyOthersIncomeText"><?= date("Y"); ?></span>
                </h6>

                <div class="">
                    <select id="monthlyOthersIncomeSelect" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                        <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                            <option value="<?= $i; ?>"><?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="card-body p-24">
                <div id="monthly_others_income_chart"></div>
            </div>
        </div>
    </div>
</div>


<!-- bill collection comparison chart start -->
<div class="col-12">
    <div class="graphicalChart">
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
</div>
<!-- bill collection comparison chart end -->



<div class="row" style="margin-top: 50px;">
    <!-- Dashboard Widget End -->
    <div class="col-xxl-8">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                    <div>
                        <h6 class="mb-2 fw-bold text-lg text-neutral-600">Revenue Statistics <span id="revenueYear"><?php echo date('Y'); ?></span></h6>
                        <span class="text-sm fw-medium text-secondary-light">Monthly earning overview</span>
                    </div>
                    <div class=" d-flex flex-wrap">
                        <div class="me-40">
                            <span class="text-secondary-light text-sm mb-1">Income</span>
                            <div>
                                <h6 id="dynamicIncome" class="fw-semibold d-inline-block mb-0 text-neutral-600">$0</h6>
                            </div>
                        </div>
                        <div>
                            <span class="text-secondary-light text-sm mb-1">Expenses</span>
                            <div>
                                <h6 id="dynamicExpenses" class="fw-semibold d-inline-block mb-0 text-neutral-600">$0</h6>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <select id="yearRangeForIncomeExpenseChart" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                            <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                                <option value="<?= $i; ?>"><?= $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                </div>

                <div id="income_view_column"></div>
            </div>
        </div>
    </div>

    <!-- Revenue Statistics End -->

    <!-- Statistics Start -->
    <div class="col-xxl-4">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24 row">
                <h6 class="mb-2 fw-bold text-lg text-neutral-600 col-8">
                    Revenue Statistics Rate – <span id="dateYearRevenueStatistics" class="text-success"><?= date("F") . " " . date("Y") ?></span>
                </h6>
                <div class="col-4">
                    <div class="" style="margin-left: 50px;">
                        <?php $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; ?>
                        <select id="revenueExpenseYear" class=" form-select form-select-sm w-auto bg-base border text-secondary-light">
                            <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                                <option <?php echo $i == date("Y") ? "selected" : "" ?> value="<?= $i; ?>"><?= $i; ?></option>
                            <?php endfor; ?>
                        </select>
                        <select id="revenueExpenseMonth" style="margin-top: 10px;" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                            <?php for ($i = 0; $i < 12; $i++): ?>
                                <option <?php echo ($i + 1) == date("n") ? "selected" : "" ?> value="<?= $i + 1 ?>"><?= $months[$i]; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-24">
                    <div id="revenue_statistics_chart" width="500" height="500">

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<!-- <div class="col-md-8">
    <div class="card h-100 p-0">
        <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Column Charts</h6>
        </div>
        <div class="card-body">
            <div id="dashboard_view_combination_chart"></div>
        </div>
    </div>
</div> -->


<div class="col-xxl-12">
    <div class="card h-100">
        <div class="card-body p-24 mb-8">
            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                <h6 class="mb-2 fw-bold text-lg text-neutral-600 mb-0">Revenue Comparison: <span id="revenueDisplayYear"><?php echo date('Y') . " vs " . (date('Y') - 1); ?></span></h6>

                <div class="">
                    <select id="yearRangeForRevenueChart" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                        <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                            <option value="<?= $i; ?>"><?= $i . " - " . ($i - 1); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <ul class="d-flex flex-wrap align-items-center justify-content-center my-3 gap-24">
                <li class="d-flex flex-column gap-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="w-8-px h-8-px rounded-pill bg-primary-600"></span>
                        <span class="text-secondary-light text-sm fw-semibold">Previous Year Revenue </span>
                    </div>
                    <div class="d-flex align-items-center gap-8">
                        <h6 class="mb-0" id="previous-revenue">0 BDT</h6>

                    </div>
                </li>
                <li class="d-flex flex-column gap-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="w-8-px h-8-px rounded-pill bg-warning-600"></span>
                        <span class="text-secondary-light text-sm fw-semibold">Current Year Revenue </span>
                    </div>
                    <div class="d-flex align-items-center gap-8">
                        <h6 class="mb-0" id="current-revenue">0 BDT</h6>
                    </div>
                </li>
            </ul>
            <div id="revenueChart" class="apexcharts-tooltip-style-1"></div>
        </div>
    </div>
</div>
<!-- Statistics End -->
<div class="col-xxl-12">
    <div class="card h-100">
        <div class="card-body p-24">
            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                <h6 class="mb-2 fw-bold text-lg text-neutral-600 mb-0">Latest Transactions</h6>
            </div>
            <div class="table-responsive scroll-sm">
                <table class="table table-striped bordered-table mb-0" id="transection-table">
                    <thead>
                        <tr>
                            <th scope="col">SL.</th>
                            <th scope="col">Name</th>
                            <th scope="col">Phone </th>
                            <th scope="col">Amount</th>
                            <th scope="col" class="text-center">Type</th>
                            <th scope="col">Date</th>
                            <th scope="col">Created By</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="col-xxl-7">
    <div class="card h-100">
        <div class="card-body p-24">
            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                <h6 class="mb-2 fw-bold text-lg text-neutral-600 mb-0">Latest Complains</h6>
            </div>
            <div class="table-responsive scroll-sm">
                <table class="table table-striped bordered-table mb-0" id="data-table">
                    <thead>
                        <tr>
                            <th scope="col">SL.</th>
                            <th scope="col">Name</th>
                            <th scope="col">Phone </th>
                            <th scope="col">Complain</th>
                            <th scope="col">Date</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="col-xxl-5">
    <div class="card h-100">
        <div class="card-body p-24">
            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                <h6 class="mb-2 fw-bold text-lg text-neutral-600 mb-0">Stock Report</h6>
                <a href="?page=stock" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                    View All
                    <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                </a>
            </div>
            <div class="table-responsive scroll-sm">
                <table class="table table-striped bordered-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Items</th>
                            <th scope="col">Batch No</th>
                            <th scope="col">
                                <div class="max-w-112 mx-auto">
                                    <span>Stock</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stock as $item): ?>
                            <?php
                            $qty = $item['current_stock'];
                            $percentage = ($qty / 50) * 100;
                            ?>
                            <tr>
                                <td><?= $item['product_name'] ?></td>
                                <td><?= $item['batch_id'] ?></td>
                                <td>
                                    <div class="max-w-112 mx-auto">
                                        <div class="w-100">
                                            <div class="progress progress-sm rounded-pill" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                <div
                                                    <?php if ($percentage < 20): ?>
                                                    class="progress-bar bg-danger-main rounded-pill"
                                                    <?php elseif ($percentage < 50 &&  $percentage > 20): ?>
                                                    class="progress-bar bg-warning-main rounded-pill"
                                                    <?php elseif ($percentage == 0): ?>
                                                    class="progress-bar bg-secondary-light rounded-pill"
                                                    <?php else: ?>
                                                    class="progress-bar bg-success-main rounded-pill"
                                                    <?php endif; ?>

                                                    style="width: <?= $percentage ?>%;"></div>
                                            </div>
                                            <?php if ($percentage < 20): ?>
                                                <span class="mt-12 text-secondary-main text-sm fw-medium">Low Stock</span>
                                            <?php elseif ($percentage < 50): ?>
                                                <span class="mt-12 text-secondary-main text-sm fw-medium">Medium Stock</span>
                                            <?php elseif ($percentage == 0): ?>
                                                <span class="mt-12 text-secondary-light text-sm fw-medium">Out of Stock</span>
                                            <?php else: ?>
                                                <span class="mt-12 text-secondary-main text-sm fw-medium">High Stock</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="centermodal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-4" id="myCenterModalLabel">Change Status</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="mb-3 col-md-12">
                        <input type="hidden" id="modalComplainId">
                        <label for="ComplainStatus" class="form-label">Status</label>
                        <select id="ComplainStatus" class="form-control">
                            <option value="">Choose</option>
                            <option value="1">Pending</option>
                            <option value="2">Processing</option>
                            <option value="3">Solved</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" id="changedStatus" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<?php $obj->start_script(); ?>
<script>
    // let chart;
    let incomeExpenseChart = null;

    function IncomeExpenseChartLoad(selectedYear, changeYear = false) {
        fetch(`./pages/dashbaord/up-down_chart_data_ajax.php?selectedYear=${selectedYear}`)
            .then(response => response.json())
            .then(data => {
                // // Update Total Income and Expense
                // document.getElementById('dynamicIncome').textContent = `$${data.totalIncome.toLocaleString()}`;
                // document.getElementById('dynamicExpenses').textContent = `$${data.totalExpense.toLocaleString()}`;
                const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                const incomedata = data.incomeData.split(',').map(Number);
                const expensedata = data.expenseData.split(',').map(Number);
                const sumIncome = incomedata.reduce((accumulator, currentValue) => accumulator + currentValue, 0)
                const sumExpense = expensedata.reduce((accumulator, currentValue) => accumulator + currentValue, 0)

                document.getElementById('dynamicIncome').textContent = `${sumIncome.toLocaleString()} BDT`;
                document.getElementById('dynamicExpenses').textContent = `${sumExpense.toLocaleString()} BDT`;
                let expenseNegative = expensedata.map(value => -Math.abs(value));

                // Generate Up-Down Bar Chart
                var options = {
                    series: [{
                            name: "Income",
                            data: incomedata,
                        },
                        {
                            name: "Expenses",
                            data: expenseNegative,
                        },
                    ],
                    chart: {
                        stacked: true,
                        type: "bar",
                        height: 263,
                        fontFamily: "Poppins, sans-serif",
                        toolbar: {
                            show: false,
                        },
                    },
                    colors: ["#487FFF", "#EF4A00"],
                    plotOptions: {
                        bar: {
                            columnWidth: "8",
                            borderRadius: [2],
                            borderRadiusWhenStacked: "all",
                        },
                    },
                    stroke: {
                        width: [5, 5]
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    legend: {
                        show: true,
                        position: "top",
                    },
                    yaxis: {
                        show: false,
                        title: {
                            text: undefined,
                        },
                        labels: {
                            formatter: function(y) {
                                return y.toFixed(0) + "";
                            },
                        },
                    },
                    xaxis: {
                        show: false,
                        categories: months,
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false,
                        },
                        labels: {
                            show: true,
                            style: {
                                colors: "#d4d7d9",
                                fontSize: "10px",
                                fontWeight: 500,
                            },
                        },
                    },
                    tooltip: {
                        enabled: true,
                        shared: true,
                        intersect: false,
                        theme: "dark",
                        x: {
                            show: false,
                        },
                    },
                };

                if (changeYear && incomeExpenseChart) {
                    incomeExpenseChart.updateOptions(options);
                } else {
                    // Destroy old chart if exists
                    if (incomeExpenseChart) {
                        incomeExpenseChart.destroy();
                    }
                    incomeExpenseChart = new ApexCharts(document.querySelector("#income_view_column"), options);
                    incomeExpenseChart.render();
                }
            })
            .catch(error => console.error("Error fetching chart data:", error));
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Fetch data from the server
        IncomeExpenseChartLoad(new Date().getFullYear());

        $("#yearRangeForIncomeExpenseChart").on("change", function() {
            const selectedYear = $(this).val();
            IncomeExpenseChartLoad(selectedYear, true);

            $("#revenueYear").text(selectedYear);
        })
    });
</script>
<style>
    .apexcharts-track path {
        stroke: #FF8042 !important;
    }
</style>


<!-- bill collecton monthly chart start -->
<script>
    $(document).ready(function() {
        function monthlyConnectionChargeChartLoad(selectedYear) {
            $.ajax({
                type: "GET",
                url: `./pages/dashbaord/monthly_bill_collection_chart_ajax.php?selectedYear=${selectedYear}`,
                dataType: "json",
                success: function(response) {
                    console.log('sadi', response);

                    $('#loading-spinner').hide();
                    // Call the function to render the line chart with the data from the server
                    monthlyConnectionChargeChart(response.currentData, response.currentYear, response.maxData);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        monthlyConnectionChargeChartLoad(new Date().getFullYear());

        $("#monthlyOthersIncomeSelect").on("change", function() {
            monthlyConnectionChargeChartLoad($(this).val());
            $("#monthlyOthersIncomeText").text(`${$(this).val()}`);
        })
    });

    let monthlyConnectionChargeInstance = null

    function monthlyConnectionChargeChart(currentData, currentYear, maxData) {
        const data = currentData.split(',').map(Number);
        console.log('sadi-data', data);

        var options = {
            series: [{
                name: 'Free Cash Flow',
                data
            }],
            colors: ['#9195F6'],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '20%',
                    borderRadius: 5,
                    borderRadiusApplication: 'end'
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
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', "Nov", "Dec"],
            },
            yaxis: {
                title: {
                    text: '$ (thousands)'
                },
                min: 0,
                max: maxData,
                tickAmount: 5,
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "$ " + val + " thousands"
                    }
                }
            }
        };

        if (monthlyConnectionChargeInstance) {
            monthlyConnectionChargeInstance.updateOptions(options);
        } else {
            if (monthlyConnectionChargeInstance) {
                monthlyConnectionChargeInstance.destroy();
            }

            monthlyConnectionChargeInstance = new ApexCharts(document.querySelector("#monthly_others_income_chart"), options);
            monthlyConnectionChargeInstance.render();
        }
    }
    // monthly connection charge chart end
</script>
<!-- bill collecton monthly chart end -->


<script>
    let bill_collection_comparison_chart = null;

    function bill_collection_view_line_chart(previousData, previousYear, currentData, currentYear, maxData) {
        // Define colors
        let colors = ["#6658dd", "#1abc9c"];
        const dataColors = $("#apex-line-test").data("colors");
        if (dataColors) {
            colors = dataColors.split(",");
        }

        // Static month names for both years
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

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
                categories: months, // Use static month names
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


    // two years bill collection chart start
    function twoYearsBillCollectionChartLoad(selectedYear) {
        $.ajax({
            type: "GET",
            url: `./pages/dashbaord/bill_collection_charts_ajax.php?selectedYear=${selectedYear}`,
            dataType: "json",
            success: function(response) {
                $('#loading-spinner').hide();
                // Call the function to render the line chart with the data from the server
                bill_collection_view_line_chart(response?.previousData, response?.previousYear, response?.currentData, response?.currentYear, response?.maxData);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }

    $(document).ready(function() {
        twoYearsBillCollectionChartLoad(new Date().getFullYear());
        $("#twoYearsBillCollectionChart").on("change", function() {
            const selectedYear = $(this).val();
            twoYearsBillCollectionChartLoad(selectedYear);
            $("#comparisonYear").text(selectedYear + " vs " + (selectedYear - 1));
        })
    })
    // two years bill collection chart end
</script>


<script>
    $(document).ready(function() {
        // Make sure the AJAX call fetches data successfully
        $.ajax({
            url: './pages/dashbaord/dashboard_view_redial_chart_ajax.php', // Your PHP script URL
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                // Validate the response and calculate the percentage
                if (response.totalBill > 0) {
                    var totalBill = response.totalBill;
                    var totalBillCollection = response.totalBillCollection;
                    var percentage = Math.round((totalBillCollection / totalBill) * 100);

                    if (percentage > 100) {
                        percentage = 100;
                    }

                    var barTextColor = '#45B369'; // Bar color
                    // Render the chart
                    var options = {
                        series: [percentage], // Dynamically set the percentage
                        chart: {
                            height: 350,
                            type: 'radialBar',
                        },
                        plotOptions: {
                            radialBar: {
                                hollow: {
                                    size: '50%', // Set hollow area
                                },
                            },
                        },
                        colors: [barTextColor], // Radial bar color
                        labels: ['Total Collection'], // Label
                    };

                    var chart = new ApexCharts(document.querySelector("#dashboad_view_redial_chart"), options);
                    chart.render();
                } else {
                    console.error("Invalid response: totalBill is 0 or undefined.");

                    var totalBill = 5000;
                    var totalBillCollection = 2000;
                    var percentage = (totalBillCollection / totalBill) * 100;

                    var barTextColor = 'rgb(9, 142, 54)'; // Bar color
                    // Render the chart
                    var options = {
                        series: [percentage], // Dynamically set the percentage
                        chart: {
                            height: 350,
                            type: 'radialBar',
                        },
                        plotOptions: {
                            radialBar: {
                                hollow: {
                                    size: '50%', // Set hollow area
                                },
                            },
                        },
                        colors: [barTextColor], // Radial bar color
                        labels: ['Total Collection'], // Label
                    };

                    var chart = new ApexCharts(document.querySelector("#dashboad_view_redial_chart"), options);
                    chart.render();
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });


        // monthly revenue and expense statistics start
        let expenseRevenueMonthlyChart = null;

        function monthlyRevenueExpenseChartLoad(year, month) {
            $.ajax({
                url: `./pages/dashbaord/monthly_revenue_expense_redial_chart_ajax.php?year=${year}&month=${month}`, // Your PHP script URL
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    let toalExpense = parseInt(response?.totalExpense);
                    let toalRevenue = parseInt(response?.totalRevenue);
                    var options = {
                        // series: [(toalExpense == 0 && toalRevenue == 0) ? 1 : toalRevenue, toalExpense],
                        series: [(toalExpense == 0 && toalRevenue == 0) ? 1 : toalRevenue, toalExpense],
                        chart: {
                            width: 380,
                            type: 'pie',
                        },
                        labels: ['Income', 'Expense'],
                        colors: ['#28a745', '#dc3545'],
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    };


                    if (expenseRevenueMonthlyChart) {
                        expenseRevenueMonthlyChart.updateOptions(options);
                    } else {
                        if (expenseRevenueMonthlyChart) {
                            expenseRevenueMonthlyChart.destroy();
                        }

                        expenseRevenueMonthlyChart = new ApexCharts(document.querySelector("#revenue_statistics_chart"), options);
                        expenseRevenueMonthlyChart.render();
                    }

                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        monthlyRevenueExpenseChartLoad(new Date().getFullYear(), new Date().getMonth() + 1)

        $("#revenueExpenseYear, #revenueExpenseMonth").on("change", function() {
            monthlyRevenueExpenseChartLoad($("#revenueExpenseYear").val(), $("#revenueExpenseMonth").val());
            let months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            $("#dateYearRevenueStatistics").text(`${months[$("#revenueExpenseMonth").val() - 1]} ${$("#revenueExpenseYear").val()}`)
        })
        // monthly revenue and expense statistics end



        // $.ajax({
        //     url: './pages/dashbaord/dashboard_view_combination_chart_ajax.php', // Your PHP script URL
        //     method: 'GET',
        //     dataType: 'json',
        //     success: function(response) {


        //         const countIds = [];
        //         const totalTakas = [];
        //         // Static month names for both years
        //         const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        //         // Iterate over totalData to separate countId and total_taka
        //         response.totalData.forEach(data => {
        //             if (typeof data === 'object') {
        //                 // If data is an object, push the respective values into the arrays
        //                 countIds.push(data.countId);
        //                 totalTakas.push(data.total_taka);
        //             } else {
        //                 // If the data is 0, push 0 for consistency
        //                 countIds.push(0);
        //                 totalTakas.push(0);
        //             }
        //         });
        //         let maxData = Math.max(...totalTakas.map(value => Number(value)));
        //         let maxIds = Math.max(...countIds.map(value => Number(value)));
        //         maxData = maxData + (maxData * 0.50);
        //         maxIds = maxIds + (maxIds * 0.50);


        //         var options = {
        //             series: [{
        //                     name: "Revenue",
        //                     type: "column", // Bar chart for revenue
        //                     data: totalTakas, // Revenue data
        //                 },
        //                 {
        //                     name: "Sales",
        //                     type: "line", // Line chart for sales
        //                     data: countIds, // Sales data
        //                 },
        //             ],
        //             chart: {
        //                 height: 350,
        //                 type: "line",
        //                 stacked: false,
        //             },
        //             stroke: {
        //                 width: [0, 4], // No stroke for bars, 4px stroke for the line
        //                 curve: "smooth", // Smooth line for sales data
        //             },
        //             plotOptions: {
        //                 bar: {
        //                     columnWidth: "50%", // Width of the bars
        //                 },
        //             },
        //             dataLabels: {
        //                 enabled: true,
        //                 style: {
        //                     fontSize: "12px",
        //                     colors: ["#000000"],
        //                 },
        //                 offsetY: -5, // Position data labels above the bars
        //             },
        //             labels: months,
        //             xaxis: {
        //                 categories: months
        //             },
        //             yaxis: [{
        //                     title: {
        //                         text: "Net Revenue",
        //                     },
        //                     min: 0,
        //                     max: maxData,
        //                 },
        //                 {
        //                     opposite: true,
        //                     title: {
        //                         text: "Number of Sales",
        //                     },
        //                     min: 0,
        //                     max: maxIds,
        //                 },
        //             ],
        //             colors: ["#00E396", "#007BFF"], // Green for revenue (bars) and blue for sales (line)
        //             tooltip: {
        //                 shared: true,
        //                 intersect: false,
        //                 y: {
        //                     formatter: function(value) {
        //                         return value.toFixed(0);
        //                     },
        //                 },
        //             },
        //         };

        //         var chart = new ApexCharts(document.querySelector("#dashboard_view_combination_chart"), options);
        //         chart.render();
        //     }
        // });

        // two years revenue comparison chart start
        let twoYearsRevenueChart = null;

        function TwoYearsRevenueComparisonChartLoad(selectedYear = 2024) {
            $.ajax({
                url: `./pages/dashbaord/dashboard_view_revenue_chart_ajax.php?selectedYear=${selectedYear}`,
                method: 'GET',
                dataType: 'json',
                success: function(res) {

                    let colors = ["#6658dd", "#1abc9c"];
                    const dataColors = $("#apex-line-test").data("colors");
                    if (dataColors) {
                        colors = dataColors.split(",");
                    }


                    // Static month names for both years
                    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                    const previousDataArray = res.previousData.split(',').map(Number);
                    const currentDataArray = res.currentData.split(',').map(Number);

                    const sumPreviousIncome = previousDataArray.reduce((accumulator, currentValue) => accumulator + currentValue, 0)
                    const sumCurrentIncome = currentDataArray.reduce((accumulator, currentValue) => accumulator + currentValue, 0)

                    document.getElementById('previous-revenue').textContent = `${sumPreviousIncome.toLocaleString()} BDT`;
                    document.getElementById('current-revenue').textContent = `${sumCurrentIncome.toLocaleString()} BDT`;
                    const tickAmount = Math.ceil(res.maxData / 1000);
                    const max = res.maxData;
                    var options = {
                        series: [{
                            name: `Previous - ${res.previousYear}`,
                            data: previousDataArray
                        }, {
                            name: `Current - ${res.currentYear}`,
                            data: currentDataArray
                        }],
                        legend: {
                            show: false
                        },
                        chart: {
                            type: 'area',
                            width: '100%',
                            height: 270,
                            toolbar: {
                                show: false
                            },
                            padding: {
                                left: 0,
                                right: 0,
                                top: 0,
                                bottom: 0
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3,
                            colors: ["#487FFF", "#FF9F29"], // Use two colors for the lines
                            lineCap: 'round'
                        },
                        grid: {
                            show: true,
                            borderColor: '#D1D5DB',
                            strokeDashArray: 1,
                            position: 'back',
                            xaxis: {
                                lines: {
                                    show: false
                                }
                            },
                            yaxis: {
                                lines: {
                                    show: true
                                }
                            },
                            row: {
                                colors: undefined,
                                opacity: 0.5
                            },
                            column: {
                                colors: undefined,
                                opacity: 0.5
                            },
                            padding: {
                                top: -20,
                                right: 0,
                                bottom: -10,
                                left: 0
                            },
                        },
                        fill: {
                            type: 'gradient',
                            colors: ["487FFF", "#FF9F29"], // Use two colors for the gradient
                            gradient: {
                                shade: 'light',
                                type: 'vertical',
                                shadeIntensity: 0.5,
                                gradientToColors: [undefined, `#FF9F2900`], // Apply transparency to both colors
                                inverseColors: false,
                                opacityFrom: [0.4, 0.6], // Starting opacity for both colors
                                opacityTo: [0.3, 0.3], // Ending opacity for both colors
                                stops: [0, 100],
                            },
                        },
                        markers: {
                            colors: ["487FFF", "#FF9F29"], // Use two colors for the markers
                            strokeWidth: 3,
                            size: 0,
                            hover: {
                                size: 10
                            }
                        },
                        xaxis: {
                            labels: {
                                show: false
                            },
                            categories: months,
                            tooltip: {
                                enabled: false
                            },
                            labels: {
                                formatter: function(value) {
                                    return value;
                                },
                                style: {
                                    fontSize: "14px"
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: function(value) {
                                    return (value / 1000).toFixed(0) + "k";
                                },
                                style: {
                                    fontSize: "14px"
                                }
                            },
                        },
                        tooltip: {
                            x: {
                                format: 'dd/MM/yy HH:mm'
                            }
                        }
                    };



                    if (twoYearsRevenueChart) {
                        twoYearsRevenueChart.updateOptions(options);
                    } else {
                        // Destroy old chart if exists
                        if (twoYearsRevenueChart) {
                            twoYearsRevenueChart.destroy();
                        }
                        twoYearsRevenueChart = new ApexCharts(document.querySelector("#revenueChart"), options);
                        twoYearsRevenueChart.render();
                    }
                }
            });
        }

        TwoYearsRevenueComparisonChartLoad(new Date().getFullYear());

        $("#yearRangeForRevenueChart").on("change", function() {
            const selectedYear = $(this).val();
            TwoYearsRevenueComparisonChartLoad(selectedYear);
            $("#revenueDisplayYear").text(selectedYear + " vs " + (selectedYear - 1));
        })
        // two years revenue comparison chart end


        var transectionTable = $('#transection-table').DataTable({
            paging: false,
            searching: false,
            ordering: false,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: './pages/dashbaord/get_transection_ajax.php', // Replace with your API endpoint
                dataSrc: 'data', // This points to the part of the response where your data is located
            },
            columns: [{
                    data: 'sl',
                    defaultContent: 'N/A'
                },
                {
                    data: 'customer_name',
                    defaultContent: 'N/A'
                },
                {
                    data: 'phone',
                    defaultContent: 'N/A',

                },
                {
                    data: 'amount',
                    defaultContent: 'N/A'
                },
                {
                    data: 'type',
                    className: 'text-center',

                    render: function(data, type, row) {
                        let statusClass = '';
                        let statusLabel = '';
                        if (data == 1) {
                            statusClass = 'bg-danger';
                            statusLabel = 'Expenses';
                        } else if (data == 2) {
                            statusClass = 'bg-primary';
                            statusLabel = 'Other Income';
                        } else if (data == 3) {
                            statusClass = 'bg-success';
                            statusLabel = 'Bill Collection';
                        } else if (data == 4) {
                            statusClass = 'bg-success';
                            statusLabel = 'Connection Charge';
                        } else if (data == 5) {
                            statusClass = 'bg-secondary';
                            statusLabel = 'Opening Income';
                        }
                        return `<span class="${statusClass} text-white px-24 py-4 rounded-pill fw-medium text-sm">${statusLabel}</span>`;
                    }
                },
                {
                    data: 'entry_date',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-US', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }).replace(',', '') : '';
                    }


                },
                {
                    data: 'entry_by',
                    defaultContent: 'N/A'
                },
                // {
                //     data: null,
                //     render: function(data) {
                //         return `<a href="#centermodal" data-bs-toggle="modal" data-id="${data.id}" class="btn btn-xs btn-light status-modal"><i class="mdi mdi-pencil"></i></a>`;
                //     }
                // }
            ]
        });
        var dataTable = $('#data-table').DataTable({
            paging: false,
            searching: false,
            ordering: false,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: './pages/dashbaord/get_complain_ajax.php', // Replace with your API endpoint
                dataSrc: 'data', // This points to the part of the response where your data is located
            },
            columns: [{
                    data: 'sl',
                    defaultContent: 'N/A'
                },
                {
                    data: 'customer_name',
                    defaultContent: 'N/A'
                },
                {
                    data: 'phone',
                    defaultContent: 'N/A',

                },
                {
                    data: 'complain_date',
                    defaultContent: 'N/A',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-US', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }).replace(',', '') : '';
                    }
                },
                {
                    data: 'complain_name',
                    defaultContent: 'N/A'
                },
                {
                    data: 'status_name',
                    render: function(data, type, row) {
                        let statusClass = '';
                        let statusLabel = '';
                        switch (data) {
                            case '1':
                                statusClass = 'bg-danger';
                                statusLabel = 'Pending';
                                break;
                            case '2':
                                statusClass = 'bg-primary';
                                statusLabel = 'Processing';
                                break;
                            case '3':
                                statusClass = 'bg-success';
                                statusLabel = 'Resolved';
                                break;
                            default:
                                statusClass = 'bg-secondary';
                                statusLabel = 'Unknown';
                                break;
                        }
                        return `<span class="${statusClass} text-white px-24 py-4 rounded-pill fw-medium text-sm">${statusLabel}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        return `<a href="#centermodal" data-bs-toggle="modal" data-id="${data.id}" class="btn btn-xs btn-light status-modal"><i class="mdi mdi-pencil"></i></a>`;
                    }
                }
            ]
        });

        // Handle status update when the button is clicked
        $('#changedStatus').on('click', function() {
            // Collect data
            const complainId = $('#modalComplainId').val();
            const statusId = $('#ComplainStatus').val();

            // Validate status selection
            if (!statusId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please select a status'
                });
                return;
            }

            // Prepare AJAX data
            const formData = {
                id: complainId,
                status: statusId
            };

            // AJAX request to update the status
            $.ajax({
                url: './pages/complain/update_status.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                dataType: 'json',
                success: function(response) {
                    // Hide the modal
                    $('#centermodal').modal('hide');
                    // Reload the DataTable to reflect the status update
                    dataTable.ajax.reload();
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON ? xhr.responseJSON.message : 'An unexpected error occurred');
                }
            });
        });

        // Open the modal when a status is clicked
        $(document).on('click', '.status-modal', function() {
            // Get the ID from the clicked button
            const complainId = $(this).data('id');

            // Set the ID in a hidden input in the modal
            $('#modalComplainId').val(complainId);

            // Reset the status dropdown
            $('#ComplainStatus').val('');
        });
    });
</script>

<?php $obj->end_script(); ?>
