<?php
$monthArray = array(
    "1" => 'January',
    "2" => 'February',
    "3" => 'March',
    "4" => 'April',
    "5" => 'May',
    "6" => 'June',
    "7" => 'July',
    "8" => 'August',
    "9" => 'September',
    "10" => 'October',
    "11" => 'November',
    "12" => 'December',
);
?>

<!-- Begin page -->
<div class="container-fluid py-4">
    <!-- Filters -->
    <div class="card mb-5 p-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="select-month" class="form-label fw-semibold">Select Month</label>
                    <select id="select-month" name="dateMonth" class="form-select shadow-sm rounded">
                        <option value="">Select Month</option>
                        <?php
                        $currentMonth = date('n');
                        foreach ($monthArray as $monthKey => $monthVal) {
                            echo "<option value='$monthKey' " . ($monthKey == $currentMonth ? 'selected' : '') . ">$monthVal</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="select-year" class="form-label fw-semibold">Select Year</label>
                    <select id="select-year" name="dateYear" class="form-select shadow-sm rounded">
                        <option value="">Select Year</option>
                        <?php
                        $currentYear = date('Y');
                        for ($year = 2010; $year <= $currentYear; $year++) {
                            echo "<option value='$year' " . ($year == $currentYear ? 'selected' : '') . ">$year</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4">
        <!-- Customer Summary -->
        <div class="col-lg-6">
            <div class="card shadow border-0 h-100">
                <div style="background-color: #16a34a;" class="card-header text-white d-flex justify-content-between align-items-center rounded-top">
                    <h6 class="mb-0 text-white">Customer Bill Generate Summary</h6>
                    <span class="badge bg-light text-dark GenerateMonth"></span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td>Total Customer Bill Generate
                                    <a id="runningPaidInfo" href="?page=all_paid">
                                        <span style="margin-left: 20px; font-size: 16px; background-color: #16a34a;" class="badge text-white">Total Paid:
                                            <span id="runningPaid">0</span>
                                    </a>

                                    <span id="previousPaidInfo" style="margin-left: 20px; font-size: 16px;" class="badge bg-light text-success d-none">Total Paid:
                                        <span id="prevPaid">0</span>
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-primary">
                                    <a href="?page=customer_view&ag_status=active&billGenerate=1" style="padding: 0 5px; border-radius: 5px;" class="btn-primary-600 customerLink  d-none">
                                        <span id="totalGenerateCustomerLink">
                                            0
                                        </span>
                                    </a>

                                    <span class="defalutCustomerInfo" id="totalGenerateCustomerDefault">
                                        0
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>Total Inactive Customer</td>
                                <td class="text-end fw-bold text-warning" id="">
                                    <a href="?page=customer_view&ag_status=inactive" style="padding: 0 5px; border-radius: 5px;" class="btn-warning-600 customerLink d-none">
                                        <span id="totalInactiveCustomerLink">
                                            0
                                        </span>
                                    </a>

                                    <span class="defalutCustomerInfo" id="totalInactiveCustomerDefault">
                                        0
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>Total Free Customer</td>
                                <td class="text-end fw-bold text-info" id="">
                                    <a href="?page=customer_view&ag_status=free" style="padding: 0 5px; border-radius: 5px;" class="btn-info-600 customerLink d-none">
                                        <span id="totalFreeCustomerLink">
                                            0
                                        </span>
                                    </a>

                                    <span class="defalutCustomerInfo" id="totalFreeCustomerDefalut">
                                        0
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>Total Discontinue Customer</td>
                                <td class="text-end fw-bold text-danger" id="">
                                    <a href="?page=customer_view&ag_status=discontinue" style="padding: 0 5px; border-radius: 5px;" class="btn-danger-600 customerLink d-none">
                                        <span id="totalDisContinueLink">
                                            0
                                        </span>
                                    </a>

                                    <span class="defalutCustomerInfo" id="totalDisContinueDefault">
                                        0
                                    </span>
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Total Customer</strong></td>
                                <td class="text-end fs-5 fw-bold text-success" id="totalCusotomer">0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Amount Summary -->
        <div class="col-lg-6">
            <div class="card shadow border-0 h-100">
                <div style="background-color: #16a34a;" class="card-header text-white d-flex justify-content-between align-items-center rounded-top">
                    <h6 class="mb-0 text-white">Bill Generate Summary</h6>
                    <span class="badge bg-light text-dark GenerateMonth"></span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td>Total Generated Amount</td>
                                <td class="text-end fw-bold" id="totalGenerateAmount">0</td>
                            </tr>
                            <tr>
                                <td>Total Previous Due Amount</td>
                                <td class="text-end fw-bold text-warning" id="totalPreviousDueAmount">0</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Total Bill Collection</strong></td>
                                <td class="text-end fs-5 fw-bold text-primary" id="totalCollectableAmount">0</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="text-end fw-bold text-warning" id=""></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="text-end fw-bold text-warning" id=""></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="text-end fw-bold text-warning" id=""></td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Total Collected Monthly Bill</strong></td>
                                <td class="text-end fs-5 fw-bold text-success" id="totalCollectedBill">0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 50px;">
    <!-- Statistics Start -->
    <div class="col-xxl-4">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24">
                <h6 style="padding: 10px;">Customer Statistics</h6>
                <div id="zoneWiseCustomerViewPieChart" class="apex-charts mt-3"></div>
            </div>
        </div>
    </div>

    <div class="col-xxl-4">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24">
                <h6 class="mb-2 fw-bold text-lg text-neutral-600">
                    Paid VS Unpaid Rate – <span class="text-success" id="paidMonthYear"></span>
                </h6>

                <div class="mt-24">
                    <div id="dashboad_view_redial_chart" width="500" height="500"></div>
                </div>

            </div>
        </div>
    </div>


    <div class="col-xxl-4">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                    <div style="margin-bottom: 10px;">
                        <h6 class="mb-2 fw-bold text-lg text-neutral-600">Bill Generate vs Collected</h6>
                    </div>
                    <div class=" d-flex flex-wrap">
                        <div class="me-40">
                            <span class="text-secondary-light text-sm mb-1">Bill</span>
                            <div class="">
                                <h6 id="billCollectableAmount" class="fw-semibold d-inline-block mb-0 text-neutral-600">$0</h6>
                            </div>
                        </div>
                        <div>
                            <span class="text-secondary-light text-sm mb-1">Collected</span>
                            <div>
                                <h6 id="billCollectedAmount" class="fw-semibold d-inline-block mb-0 text-neutral-600">$0</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="income_view_column"></div>
            </div>
        </div>
    </div>
</div>


<div class="row g-3" style="margin-top: 50px;">
    <div class="col-12 col-xxl-6">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                    <div style="margin-bottom: 10px;">
                        <h6 class="mb-2 fw-bold text-lg text-neutral-600">Bill Generate vs Collected - <span class="yearShowing">2025</span></h6>
                    </div>
                </div>

                <div id="bill_generate_and_collected_column_chart"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xxl-6">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                    <div style="margin-bottom: 10px;">
                        <h6 class="mb-2 fw-bold text-lg text-neutral-600">Customer Trending - <span class="yearShowing">2025</span></h6>
                    </div>
                </div>

                <div id="customer_trending_chart"></div>
            </div>
        </div>
    </div>
</div>



<?php $obj->start_script(); ?>

<script>
    $(document).ready(function() {
        function getSummaryInfo(month, year) {
            $.ajax({
                type: "GET",
                url: "./pages/myBill/bill_generate_summary_ajax.php",
                dataType: "json",
                data: {
                    month: month,
                    year: year
                },
                success: function(res) {
                    console.log('err-sadi', res);
                    if (res.status == true) {
                        $('#totalGenerateCustomerLink').text(res?.data?.tactivec);
                        $('#totalGenerateCustomerDefault').text(res?.data?.tactivec);

                        $('#totalInactiveCustomerLink').text(res?.data?.tinactivec);
                        $('#totalInactiveCustomerDefault').text(res?.data?.tinactivec);

                        $('#totalFreeCustomerLink').text(res?.data?.tdiscontinuec); // tdiscontinuec means free due to missmatch with database
                        $('#totalFreeCustomerDefalut').text(res?.data?.tdiscontinuec); // tdiscontinuec means free due to missmatch with database

                        $('#totalDisContinueLink').text(res?.data?.tfreec); // tfreec means discontinue due to missmatch with database
                        $('#totalDisContinueDefault').text(res?.data?.tfreec); // tfreec means discontinue due to missmatch with database

                        $('#totalCusotomer').text(parseInt(res?.data?.tactivec) + parseInt(res?.data?.tinactivec) + parseInt(res?.data?.tdiscontinuec) + parseInt(res?.data?.tfreec));

                        $("#totalGenerateAmount").text(res?.data?.tbillgenerate);
                        $("#totalPreviousDueAmount").text(res?.totalPreviousDueAmount);
                        $("#totalCollectableAmount").text(parseInt(res?.data?.tbillgenerate) + parseInt(res?.totalPreviousDueAmount));
                        $("#totalCollectedBill").text(res?.totalCollectedBill);

                        $("#runningPaid").text(res?.totalPaidInfo);
                        $("#prevPaid").text(res?.totalPaidInfo);

                        paidVsUnpaidChartLoad(parseInt(res?.data?.tactivec), parseInt(res?.totalPaidInfo));
                        zoneWiseCustomerViewPieChart(parseInt(res?.data?.tactivec), parseInt(res?.data?.tinactivec), parseInt(res?.data?.tdiscontinuec), parseInt(res?.data?.tfreec));
                        GenerateVSCollectionChartLoad((parseInt(res?.data?.tbillgenerate) + parseInt(res?.totalPreviousDueAmount)), parseInt(res?.totalCollectedBill));
                    } else {
                        $('#totalGenerateCustomerLink').text(0);
                        $('#totalGenerateCustomerDefault').text(0);

                        $('#totalInactiveCustomerLink').text(0);
                        $('#totalInactiveCustomerDefault').text(0);

                        $('#totalFreeCustomerLink').text(0); // tdiscontinuec means free due to missmatch with database
                        $('#totalFreeCustomerDefalut').text(0); // tdiscontinuec means free due to missmatch with database

                        $('#totalDisContinueLink').text(0); // tfreec means discontinue due to missmatch with database
                        $('#totalDisContinueDefault').text(0); // tfreec means discontinue due to missmatch with database

                        $('#totalCusotomer').text(0);

                        $("#totalGenerateAmount").text(0);
                        $("#totalPreviousDueAmount").text(0);
                        $("#totalCollectableAmount").text(0);
                        $("#totalCollectedBill").text(0);

                        $("#runningPaid").text(0);
                        $("#prevPaid").text(0);

                        paidVsUnpaidChartLoad();
                        zoneWiseCustomerViewPieChart();
                        GenerateVSCollectionChartLoad();
                    }

                    $(".GenerateMonth").text(res?.monthYear);
                    $("#paidMonthYear").text(res?.monthYear);
                    $(".yearShowing").text($("#select-year").val());

                    const date = new Date();
                    const month = date.toLocaleString('en-US', {
                        month: 'short'
                    });
                    const year = date.getFullYear();
                    const formatted = `${month}-${year}`;
                    if (res?.data?.month_year != formatted) {
                        $(".customerLink").addClass("d-none");
                        $(".defalutCustomerInfo").removeClass("d-none");
                        $("#runningPaidInfo").addClass("d-none");
                        $("#previousPaidInfo").removeClass("d-none");
                    } else {
                        $(".customerLink").removeClass("d-none");
                        $(".defalutCustomerInfo").addClass("d-none");
                        $("#runningPaidInfo").removeClass("d-none");
                        $("#previousPaidInfo").addClass("d-none");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }
        getSummaryInfo($("#select-month").val(), $("#select-year").val());


        $("#select-month").change(function() {
            getSummaryInfo($("#select-month").val(), $("#select-year").val());
        });
        $("#select-year").change(function() {
            getSummaryInfo($("#select-month").val(), $("#select-year").val());
            billGeneateAndCollectedColumnChart($(this).val());
            generatedCustomerTrendingChart($(this).val());
        });




        // paid vs unpaid statistics
        let paidVSUnpaidChartInstance = null;

        function paidVsUnpaidChartLoad(totalGenerate = 0, totalPaid = 0) {
            var options = {
                series: [totalGenerate, totalPaid],
                chart: {
                    type: 'donut',
                    width: 450,
                    height: 450
                },
                colors: ['#FFA500', '#45B369'],
                labels: [`Remaining - ${totalGenerate}`, `Collected - ${totalPaid}`],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200,
                            height: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };


            if (paidVSUnpaidChartInstance) {
                paidVSUnpaidChartInstance.updateOptions(options);
            } else {
                if (paidVSUnpaidChartInstance) {
                    paidVSUnpaidChartInstance.destroy();
                }

                paidVSUnpaidChartInstance = new ApexCharts(document.querySelector("#dashboad_view_redial_chart"), options);
                paidVSUnpaidChartInstance.render();
            }
        }



        // customer bill generate statistics
        let customerStatisticsInstance = null;

        function zoneWiseCustomerViewPieChart(active = 0, inactive = 0, free = 0, discontinue = 0) {
            var options = {
                series: [active, inactive, free, discontinue],
                chart: {
                    width: 450,
                    type: 'pie',
                },
                labels: [`Active - ${active}`, `Inactive - ${inactive}`, `Free - ${free}`, `Discontinue - ${discontinue}`],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };


            if (customerStatisticsInstance) {
                customerStatisticsInstance.updateOptions(options);
            } else {
                if (customerStatisticsInstance) {
                    customerStatisticsInstance.destroy();
                }

                customerStatisticsInstance = new ApexCharts(document.querySelector("#zoneWiseCustomerViewPieChart"), options);
                customerStatisticsInstance.render();
            }
        }



        let generateVsCollectedInstance = null;

        function GenerateVSCollectionChartLoad(generate = 0, collected = 0) {
            $("#billCollectableAmount").text(`৳${generate}`);
            $("#billCollectedAmount").text(`৳${collected}`);
            var options = {
                series: [generate, collected],
                chart: {
                    width: 450,
                    type: 'pie',
                },
                labels: [`Bill - ${generate}`, `Collected - ${collected}`],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };


            if (generateVsCollectedInstance) {
                generateVsCollectedInstance.updateOptions(options);
            } else {
                if (generateVsCollectedInstance) {
                    generateVsCollectedInstance.destroy();
                }

                generateVsCollectedInstance = new ApexCharts(document.querySelector("#income_view_column"), options);
                generateVsCollectedInstance.render();
            }
        }



        function billGeneateAndCollectedColumnChart(year) {
            $.ajax({
                type: "GET",
                url: `./pages/myBill/bill_generate_and_collected_chart_ajax.php?year=${year}`,
                dataType: "json",
                success: function(response) {
                    if (response?.status == true) {
                        billGeneateAndCollectedColumnChartLoad(response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        billGeneateAndCollectedColumnChart($("#select-year").val());



        let billGenAndColColumnChartInstance = null;

        function billGeneateAndCollectedColumnChartLoad(response) {
            var options = {
                series: [{
                    name: 'Total Generated',
                    data: response.totalGenerated
                }, {
                    name: 'Previous Month Due',
                    data: response.previousMonthDue
                }, {
                    name: 'Collected Bill',
                    data: response.totalCollectedAmount
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '25%',
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
                    categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                },
                yaxis: {
                    title: {
                        text: '$ (thousands)'
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#9195F6', "#FF4F0F", "#16a34a"],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "$ " + val + " thousands"
                        }
                    }
                }
            };



            if (billGenAndColColumnChartInstance) {
                billGenAndColColumnChartInstance.updateOptions(options);
            } else {
                if (billGenAndColColumnChartInstance) {
                    billGenAndColColumnChartInstance.destroy();
                }

                billGenAndColColumnChartInstance = new ApexCharts(document.querySelector("#bill_generate_and_collected_column_chart"), options);
                billGenAndColColumnChartInstance.render();
            }
        }



        // customer trending chart
        function generatedCustomerTrendingChart(year) {
            $.ajax({
                type: "GET",
                url: `./pages/myBill/generate_customer_trending_chart_ajax.php?year=${year}`,
                dataType: "json",
                success: function(response) {
                    if (response.status == false) return;
                    generatedCustomerTrendingChartLoad(response);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        generatedCustomerTrendingChart($("#select-year").val());
        // customer trending chart
        let generatedCustomerTrendingChartInstance = null;

        function generatedCustomerTrendingChartLoad(response) {

            var options = {
                series: [{
                    name: `Total Active`,
                    data: response.totalActive
                }, {
                    name: `Total Free`,
                    data: response.totalFree
                }, {
                    name: `Total InActive`,
                    data: response.totalInactive
                }, {
                    name: `Total Dicontinue`,
                    data: response.totalDiscontinue
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '25%',
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
                    categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                },
                yaxis: {
                    title: {
                        text: '$ (thousands)'
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#16a34a', '#FFB823', "#FF4F0F", "#901E3E"],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "$ " + val + " thousands"
                        }
                    }
                }
            };



            if (generatedCustomerTrendingChartInstance) {
                generatedCustomerTrendingChartInstance.updateOptions(options);
            } else {
                if (generatedCustomerTrendingChartInstance) {
                    generatedCustomerTrendingChartInstance.destroy();
                }

                generatedCustomerTrendingChartInstance = new ApexCharts(document.querySelector("#customer_trending_chart"), options);
                generatedCustomerTrendingChartInstance.render();
            }
        }

    });
</script>
<?php $obj->end_script(); ?>