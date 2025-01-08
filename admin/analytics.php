<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div>
            <?php include 'sidebar.html'; ?>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1" style="border-top: 5px solid #007bff;">
            <div class="p-3">
                <h3 class="mb-3">Analytics Dashboard</h3>
            </div>
            <div class="container mt-5">
                <div class="row">
                    <!-- Number of Affiliates -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Number of Affiliates</h4>
                            </div>
                            <div class="card-body">
                                <h2 id="totalAffiliates" style="text-align: center;"></h2>
                            </div>
                        </div>
                    </div>
                    <!-- Total Sales -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Total Sales (GHC)</h4>
                            </div>
                            <div class="card-body">
                                <h2 id="totalSales" style="text-align: center;"></h2>
                            </div>
                        </div>
                    </div>
                    <!-- Top Affiliates -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Top Affiliates</h4>
                            </div>
                            <div class="card-body">
                                <ul id="topAffiliates" class="list-group">
                                    <!-- Top affiliates will be appended here -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <!-- Sales by Month -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Sales by Month</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="salesByMonthChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Affiliate Growth -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Affiliate Growth</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="affiliateGrowthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Fetch analytics data
            $.ajax({
                url: 'api/fetch_analytics.php',
                type: 'GET',
                success: function(response) {
                    var data = JSON.parse(response);

                    // Number of Affiliates
                    $('#totalAffiliates').text(data.totalAffiliates);

                    // Total Sales
                    $('#totalSales').text(data.totalSales.toFixed(2));

                    // Top Affiliates
                    var topAffiliates = data.topAffiliates;
                    topAffiliates.forEach(function(affiliate) {
                        $('#topAffiliates').append('<li class="list-group-item">' + affiliate.name + ' - $' + parseFloat(affiliate.sales).toFixed(2) + '</li>');
                    });


                    // Sales by Month Chart
                    var salesByMonthCtx = document.getElementById('salesByMonthChart').getContext('2d');
                    var salesByMonthChart = new Chart(salesByMonthCtx, {
                        type: 'line',
                        data: {
                            labels: data.salesByMonth.labels,
                            datasets: [{
                                label: 'Sales',
                                data: data.salesByMonth.data,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1,
                                fill: false
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    // Affiliate Growth Chart
                    var affiliateGrowthCtx = document.getElementById('affiliateGrowthChart').getContext('2d');
                    var affiliateGrowthChart = new Chart(affiliateGrowthCtx, {
                        type: 'line',
                        data: {
                            labels: data.affiliateGrowth.labels,
                            datasets: [{
                                label: 'Growth',
                                data: data.affiliateGrowth.data,
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                fill: false
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>