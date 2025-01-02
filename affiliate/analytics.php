<?php
session_start();
require('../conn/conn.php');

// Ensure the affiliate ID is available
if (!isset($_SESSION['affiliate_id'])) {
    die("Unauthorized access!");
}
$affiliate_id = $_SESSION['affiliate_id'];

// Fetch Affiliate Earnings
$stmt = $mysqli->prepare("
    SELECT ae.id AS earning_id, 
           ae.amount, 
           ae.status, 
           ae.created_at, 
           ae.product_name,
           ae.typeof_purchase
    FROM affiliate_earnings ae
    WHERE ae.affiliate_id = ?
");
$stmt->bind_param("i", $affiliate_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Convert mysqli_result to an array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Calculate Total Earnings
$total_earnings = array_sum(array_column($data, 'amount'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        
    <!-- Include jQuery (Must be loaded before daterangepicker.js) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
        }

        #date-filter {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            width: 250px;
        }

        .chart-container {
            border: 1px solid #ced4da;
            border-radius: 8px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .chart-title {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
            color: #495057;
        }

        #pie-chart, #line-chart, #bar-chart {
            height: 400px;
        }

        @media (max-width: 768px) {
            .chart-container {
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div>
            <?php include 'sidebar/sidebar.html'; ?>
        </div>

        <!-- Main Content -->
        <div class="container mt-4">
            <h1 class="text-center mb-4">Analytics Dashboard</h1>

            <!-- Date Filter -->
            <div class="mb-4 d-flex justify-content-end">
                <label for="date-filter" class="me-2">Filter by Date:</label>
                <input type="text" id="date-filter" />
            </div>

            <!-- Charts Section -->
            <div class="row">
                <!-- Pie Chart -->
                <div class="col-md-6">
                    <div class="chart-container">
                        <div class="chart-title">Pie Chart</div>
                        <div id="pie-chart"></div>
                    </div>
                </div>
                <!-- Line Chart -->
                <div class="col-md-6">
                    <div class="chart-container">
                        <div class="chart-title">Line Chart</div>
                        <div id="line-chart"></div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Bar Chart -->
                <div class="col-md-12">
                    <div class="chart-container">
                        <div class="chart-title">Bar Chart</div>
                        <div id="bar-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Include Daterangepicker -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    // Initialize Date Range Picker
    $('#date-filter').daterangepicker({
        startDate: moment().subtract(30, 'days'),
        endDate: moment(),
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
</script>


<!-- Charts -->
<div id="pie-chart" style="height: 400px;"></div>
<div id="line-chart" style="height: 400px;"></div>
<div id="bar-chart" style="height: 400px;"></div>

<script>
    // Initial Data Setup
    let chartData = <?php echo json_encode($data); ?>;

    // Initialize Charts
    function renderCharts(filteredData) {
        // Filtered data logic
        const pieData = {};
        const l1Data = {};
        const l2Data = {};
        const barData = {};
        const currentMonth = moment().format('YYYY-MM');

        filteredData.forEach(row => {
            const product = row.product_name;
            const date = moment(row.created_at).valueOf();
            const month = moment(row.created_at).format('YYYY-MM');
            const earning = row.typeof_purchase === 'L1 Purchase' ? row.amount * 0.15 : row.amount * 0.02;

            // Pie Chart Data
            pieData[product] = (pieData[product] || 0) + earning;

            // Line Chart Data
            if (row.typeof_purchase === 'L1 Purchase') {
                l1Data[date] = (l1Data[date] || 0) + earning;
            } else if (row.typeof_purchase === 'L2 Purchase') {
                l2Data[date] = (l2Data[date] || 0) + earning;
            }

            // Bar Chart Data
            if (month === currentMonth) {
                barData[product] = (barData[product] || 0) + earning;
            }
        });

        // Pie Chart
        Highcharts.chart('pie-chart', {
            chart: { type: 'pie' },
            title: { text: 'Products and Income' },
            tooltip: { pointFormat: '<b>{point.percentage:.1f}%</b><br>Earnings: GHS {point.y:.2f}' },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: { enabled: true, format: '{point.name}: GHS {point.y:.2f}' }
                }
            },
            series: [{
                name: 'Earnings',
                colorByPoint: true,
                data: Object.keys(pieData).map(product => ({
                    name: product,
                    y: pieData[product]
                }))
            }]
        });

        // Line Chart
        Highcharts.chart('line-chart', {
            chart: { type: 'line' },
            title: { text: 'L1 vs L2 Purchases Over Time' },
            xAxis: { type: 'datetime', title: { text: 'Date' } },
            yAxis: { title: { text: 'Earnings (GHS)' } },
            series: [
                {
                    name: 'L1 Purchase',
                    data: Object.keys(l1Data).map(date => [parseInt(date), l1Data[date]])
                },
                {
                    name: 'L2 Purchase',
                    data: Object.keys(l2Data).map(date => [parseInt(date), l2Data[date]])
                }
            ]
        });

        // Bar Chart
        Highcharts.chart('bar-chart', {
            chart: { type: 'column' },
            title: { text: 'Monthly Earnings Per Product' },
            xAxis: { categories: Object.keys(barData) },
            yAxis: { title: { text: 'Earnings (GHS)' } },
            series: [{
                name: 'Earnings',
                data: Object.values(barData)
            }]
        });
    }

    // Initialize Date Range Picker
    $('#date-filter').daterangepicker({
        startDate: moment().subtract(30, 'days'),
        endDate: moment(),
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function (start, end) {
        // Filter data based on selected range
        const filteredData = chartData.filter(row => {
            const rowDate = moment(row.created_at);
            return rowDate.isBetween(start, end, null, '[]');
        });

        // Re-render charts with filtered data
        renderCharts(filteredData);
    });

    // Initial Render
    renderCharts(chartData);
</script>

    </div>

</body>

</html>
