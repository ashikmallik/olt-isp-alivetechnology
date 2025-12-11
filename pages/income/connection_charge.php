<?php

if (isset($_POST['search'])) {

    $dateform = date('Y-m-d', strtotime($_POST['dateform']));
    $dateto = date('Y-m-d', strtotime($_POST['dateto']));
    //$expenseDetails = $obj->getAllData("vw_account", "entry_date BETWEEN '" . date('Y-m-d', strtotime($dateform)) . "' and '" . date('Y-m-d', strtotime($dateto)) . "' AND acc_type='1' ORDER BY entry_date DESC");
    $connection_charge = $obj->rawSql("SELECT * FROM tbl_account WHERE entry_date BETWEEN '$dateform' AND '$dateto' AND acc_type = '4' ORDER BY entry_date DESC");
} else {
    $firsDayOfMonth = new DateTime('first day of this month');
    $dateform = $firsDayOfMonth->format('Y-m-d');
    $dateto = date('Y-m-d');
    $connection_charge = $obj->rawSql("SELECT * FROM tbl_account WHERE MONTH(entry_date) = MONTH(CURRENT_DATE) AND YEAR(entry_date) = YEAR(CURRENT_DATE) AND acc_type = '4' ORDER BY entry_date DESC;");

    //$obj->getAllData("vw_account", "MONTH(entry_date)='" . date('m') . "' and YEAR(entry_date)='" . date('Y') . "'  AND acc_type='1' ORDER BY entry_date DESC");
}
$previewDate = date('d M Y', strtotime($dateform)) . ' to ' . date('d M Y', strtotime($dateto));



?>
<!-- chart start php -->
<?php
$PreviousYear = $obj->rawSql('select  count(acc_id) from  tbl_account  where YEAR(entry_date)= 2023 and acc_type = 4 group by MONTH(entry_date)');
$CurrentYear = $obj->rawSql('select count(acc_id) from  tbl_account  where YEAR(entry_date)=2024 and acc_type = 4 group by MONTH(entry_date)');

$previousData = implode(',', array_map(function ($item) {
    return $item["count(acc_id)"];
}, $PreviousYear));
$currentData = implode(',', array_map(function ($item) {
    return $item["count(acc_id)"];
}, $CurrentYear));
$allData = array_merge(explode(',', $previousData), explode(',', $currentData));

$maxValue = max(array_map('intval', $allData));
// $minData = min(array_map('intval', $allData));
$maxData = $maxValue + ($maxValue * 0.50);
?>
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <!-- Form Wizard Start -->

            <form action="" method="POST">
                <div class="form-wizard">
                    <fieldset class="wizard-fieldset show">
                        <div class="row gy-3">
                            <!-- <div class="col-sm-3">
                                <label for="date-from" class="form-label">From Date</label>
                                <div class="position-relative">
                                    <input type="date" id="date-from" name="dateform" class="form-control wizard-required">
                                </div>
                            </div> -->
                            <div class="col-md-3">
                                <label for="date-from">From Date</label>
                                <input type="date" class="form-control"
                                    value="<?php echo date('Y-m-d', strtotime($dateform)); ?>"
                                    placeholder="Date"
                                    name="dateform"
                                    id="new_flight_date" required>

                            </div>
                            <div class="col-md-3">
                                <label for="date-to">To Date</label>
                                <input type="date" class="form-control"
                                    value="<?php echo date('Y-m-d', strtotime($dateto)); ?>"
                                    placeholder="Date"
                                    name="dateto"
                                    id="new_flight_date" required>
                            </div>
                            <div class="col-sm-2">
                                <label for="date-to" class="form-label "></label>
                                <div class="position-relative mt-1">
                                    <input type="submit" name="search" class="btn btn-secondary" value="Search">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </form>
            <!-- Form Wizard End -->


            <!-- summary start -->
            <div class="row mt-24 gy-4">
                <div class="col-xxl-3 col-sm-6">
                    <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-1">
                        <div class="card-body p-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                <div class="d-flex align-items-center gap-2">
                                    <span class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                        <iconify-icon icon="hugeicons:invoice-03" class="icon"></iconify-icon>
                                    </span>
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-sm">Total Connection Charge as of <span class="text-primary"><?php echo date('F') . " " . date("Y"); ?></span> </span>
                                        <h6 class="fw-semibold">
                                            <?php
                                           $totalRangeConnectionCharge = $obj->rawSqlSingle("SELECT SUM(acc_amount) AS totalConnectionCharge FROM `tbl_account` WHERE `acc_type` = '4' and entry_date BETWEEN '$dateform' AND '$dateto'")["totalConnectionCharge"] ?? 0;
                                            echo $totalRangeConnectionCharge;
                                            ?>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- May Month Income -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-2">
                        <div class="card-body p-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                <div class="d-flex align-items-center gap-2">
                                    <span class="mb-0 w-48-px h-48-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6">
                                        <iconify-icon icon="solar:wallet-bold" class="icon"></iconify-icon>
                                    </span>
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-sm">
                                            Connection Charge <br>
                                            <span class="text-primary"><?php echo date('d M Y', strtotime($dateform)) ?></span>
                                            to
                                            <span class="text-primary"><?php echo date('d M Y', strtotime($dateto)) ?></span>
                                        </span>
                                        <h6 class="fw-semibold" id="totalpaid">
                                            <?php
                                            echo $obj->rawSqlSingle("SELECT SUM(acc_amount) AS connectionCharge FROM `tbl_account` WHERE `acc_type` = '4' AND entry_date BETWEEN '$dateform' AND '$dateto'")["connectionCharge"] ?? 0;
                                            ?>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- summary end -->
        </div>
    </div>
</div>







<div class="col-md-12">
    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="card-title mb-0">Connection Charge List</h5>
        </div>
        <div class="card-body table-responsive">
            <table
                class="table table-striped bordered-table mb-0"
                id="dataTable"
                data-page-length="10">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Date</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Address</th>
                        <th scope="col">Agent Email</th>
                        <th scope="col">Connection Charge</th>
                        <th scope="col">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = '0';
                    $totalExpense = 0;
                    foreach ($connection_charge as $value) {
                        $i++;
                        $totalExpense += intval($value['acc_amount']);
                        $viewAgent = $obj->getSingleData("tbl_agent", ['where' => ['ag_id', '=', $value['agent_id']]]);
                    ?>

                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo date("d-m-Y", strtotime(isset($value['entry_date']) ? $value['entry_date'] : "2016-02-1")); ?></td>
                            <td><?php echo isset($viewAgent['ag_name']) ? $viewAgent['ag_name'] : NULL; ?></td>
                            <td><?php echo isset($viewAgent['ag_mobile_no']) ? $viewAgent['ag_mobile_no'] : NULL; ?></td>
                            <td><?php echo isset($viewAgent['ag_office_address']) ? $viewAgent['ag_office_address'] : NULL; ?></td>
                            <td><?php echo isset($viewAgent['ag_email']) ? $viewAgent['ag_email'] : NULL; ?></td>
                            <td><?php echo isset($value['acc_amount']) ? $value['acc_amount'] : NULL; ?></td>

                            <!-- <td><?php echo $result ?></td> -->

                            <td><?php echo isset($value['acc_description']) ? $value['acc_description'] : NULL; ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?php echo $totalRangeConnectionCharge; ?>TK</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<button class="btn btn-success btn-lg" style="padding: 10px;margin-bottom: 16px;font-size: 21px;" id="graphicalViewButton">Graphical View</button>


<div id="loading-spinner" class="spinner-border" role="status" style="display:none; position: fixed; top: 50%; left: 50%; z-index: 9999;">
    <span class="sr-only">Loading...</span>
</div>

<div class="col-md-12 graphicalChart mb-5" style="display: none;">
    <div class=" card h-100 p-0">
        <div class="card-header border-bottom bg-base py-16 px-24" style="display: flex; justify-content: space-between;">
            <h6 class="card-title text-lg fw-semibold mb-0">
                Monthly Connection Charge
                <span id="monthlyConnectionText"><?= date("Y"); ?></span>
            </h6>

            <div class="">
                <select id="monthlyConnectionSelect" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                    <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                        <option value="<?= $i; ?>"><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="card-body p-24">
            <div id="monthly_connection_charge_chart"></div>
        </div>
    </div>
</div>

<div class="row gy-3">
    <div class="col-xxl-6 graphicalChart" style="display: none;">
        <div class="card h-100 p-0">
            <div class="card-header border-bottom bg-base py-16 px-24" style="display: flex; justify-content: space-between;">
                <h6 class="card-title text-lg fw-semibold mb-0">
                    Connection Charge Comparison
                    <span id="conncectionChargeYearText"><?= date("Y") . " vs " . (date("Y") - 1); ?></span>
                </h6>

                <div class="">
                    <select id="twoYearsConnectionSelect" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                        <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                            <option value="<?= $i; ?>"><?= $i . " - " . ($i - 1); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="card-body p-24">
                <div id="connection_view_line_chart"></div>
            </div>
        </div>
    </div>

    <div class="col-xxl-6 graphicalChart" style="display: none;">
        <div class="shadow-7 p-20 radius-12 bg-base h-100">
            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between" style="display: flex; justify-content: space-between;">
                <h6 class="card-title mb-2 fw-bold text-lg">Income Comparison - <span id="incomeComparisonYear"><?= date("Y"); ?></span></h6>

                <div class="">
                    <select id="incomeComparisonSelectYear" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                        <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                            <option value="<?= $i; ?>"><?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="position-relative">
                <div id="income_view_bar_chart" class="text-style"></div>
            </div>
        </div>
    </div>
</div>




<?php $obj->start_script(); ?>



<!-- <script>
    $(document).ready(function() {

        $('input[name="dateform"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });

        $('input[name="dateto"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });
    });
</script> -->

<!-- <script>
    $(document).ready(function() {

        $('input[name="dateform"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });

        $('input[name="dateto"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });
    });
</script> -->

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "responsive": true,
            "paging": true,
            "searching": true,
            "info": true,
        });






        $('#graphicalViewButton').click(function() {
            $('loading-spinner').show();
            $('.graphicalChart').show();


            // monthly connection charge chart start
            function monthlyConnectionChargeChartLoad(selectedYear) {
                $.ajax({
                    type: "GET",
                    url: `./pages/income/connection_monthly_chart_ajax.php?selectedYear=${selectedYear}`,
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

            $("#monthlyConnectionSelect").on("change", function() {
                monthlyConnectionChargeChartLoad($(this).val());
                $("#monthlyConnectionText").text(`${$(this).val()}`);
            })

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

                    monthlyConnectionChargeInstance = new ApexCharts(document.querySelector("#monthly_connection_charge_chart"), options);
                    monthlyConnectionChargeInstance.render();
                }
            }
            // monthly connection charge chart end



            // two years connection charge comparison start
            function twoYearsConnectionChargeComparisonLoad(selectedYear) {
                $.ajax({
                    type: "GET",
                    url: `./pages/income/connection_view_line_chart_ajax.php?selectedYear=${selectedYear}`,
                    dataType: "json",
                    success: function(response) {
                        $('#loading-spinner').hide();
                        // Call the function to render the line chart with the data from the server
                        connection_view_line_chart(response.previousData, response.previousYear, response.currentData, response.currentYear, response.maxData);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data:", error);
                    }
                });
            }

            twoYearsConnectionChargeComparisonLoad(new Date().getFullYear());

            $("#twoYearsConnectionSelect").on("change", function() {
                twoYearsConnectionChargeComparisonLoad($(this).val());
                $("#conncectionChargeYearText").text(`${$(this).val()} vs ${($(this).val() - 1)}`);
            })
            // two years connection charge comparison end



            // income comparison start
            function incomeComparisonChartLoad(selectedYear) {
                $.ajax({
                    type: "GET",
                    url: `./pages/income/income_view_bar_chart_ajax.php?selectedYear=${selectedYear}`,
                    dataType: "json",
                    success: function(res) {
                        $('#loading-spinner').hide();
                        // Call the function to render the line chart with the data from the server
                        income_view_bar_chart(res.totalBill, res.totalCharge, res.totalOther, res.totalIncome); // To check the full response
                        // alert(res.totalBill); // To check the full response
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data:", error);
                    }
                });
            }
            incomeComparisonChartLoad(new Date().getFullYear());

            $("#incomeComparisonSelectYear").on("change", function() {
                incomeComparisonChartLoad($(this).val());
                $("#incomeComparisonYear").text($(this).val());
            })
            // income comparison end
        })


        let twoYearsConnectionInstance = null;

        function connection_view_line_chart(previousData, previousYear, currentData, currentYear, maxData) {
            // Define colors for the series
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

            const tickAmount = Math.ceil(maxData / 1000);

            var options = {
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
                    data: previousDataArray,
                }, {
                    name: `Current - ${ currentYear}`,
                    data: currentDataArray
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
                    categories: months, // Use fixed month names
                    title: {
                        text: "Month"
                    }
                },
                yaxis: {
                    title: {
                        text: "Connection Charge Count"
                    },
                    min: 0,
                    max: maxData,
                    tickAmount: 5, // Dynamic based on your data's max value
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


            if (twoYearsConnectionInstance) {
                twoYearsConnectionInstance.updateOptions(options);
            } else {
                if (twoYearsConnectionInstance) {
                    twoYearsConnectionInstance.destroy();
                }

                twoYearsConnectionInstance = new ApexCharts(document.querySelector("#connection_view_line_chart"), options);
                twoYearsConnectionInstance.render();
            }
        }



        let income_view_bar_chart_instance = null;

        function income_view_bar_chart(totalBill, totalCharge, totalOther, totalIncome) {
            const totalArr = [totalBill, totalCharge, totalOther, totalIncome];
            var options = {
                series: [{
                    data: totalArr
                }],
                chart: {
                    type: 'bar',
                    height: 270,
                    toolbar: {
                        show: false
                    },
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                        distributed: true, // Enables individual bar styling
                        barHeight: '22px'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    show: true,
                    borderColor: '#ddd',
                    strokeDashArray: 0,
                    position: 'back',
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: false
                        }
                    },
                },
                xaxis: {
                    categories: ['Total Bill Collection', 'Connection Charge', 'Total Other Income', 'Total Opening Income'],
                    labels: {
                        formatter: function(value) {
                            return (value / 1000).toFixed(0) + 'k';
                        }
                    }
                },
                legend: {
                    show: false
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "horizontal",
                        shadeIntensity: 0.5,
                        gradientToColors: ['#C98BFF', '#FFDC90', '#94FF9B', '#FFAC89', '#A3E2FE'],
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100]
                    }
                },
                colors: [
                    '#8501F8',
                    '#FF9F29',
                    '#00D40E',
                    '#F84B01',
                    '#2FBCFC'
                ]
            };

            if (income_view_bar_chart_instance) {
                income_view_bar_chart_instance.updateOptions(options);
            } else {
                if (income_view_bar_chart_instance) {
                    income_view_bar_chart_instance.destroy();
                }

                income_view_bar_chart_instance = new ApexCharts(document.querySelector("#income_view_bar_chart"), options);
                income_view_bar_chart_instance.render();
            }
        }
    })
</script>
<?php $obj->end_script(); ?>