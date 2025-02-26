<?php
session_start();

require_once __DIR__ . '/../config/loadENV.php';
if ($_ENV['APP_ENV'] === 'dev') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $adminMail = $_ENV['ADMIN_dev_EMAIL'];
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    $adminMail = $_ENV['ADMIN_EMAIL'];
}
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

// Calculate Total Earnings
$total_earnings = 0;
foreach ($result as $row) {
    $total_earnings += $row['amount'];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery (MUST be loaded first) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <!-- DataTables CSS (optional, for styling) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <script>
        $(document).ready(function() {
                $('#paymentsTable').DataTable({
                    "order": [
                        [4, "desc"]
                    ], // Order by Date column (descending)
                    "pageLength": 10,
                    "lengthMenu": [5, 10, 25, 50],
                    "responsive": true,
                    "language": {
                        "search": "Search:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "paginate": {
                            "previous": "Previous",
                            "next": "Next"
                        }
                    }
                });
            });
    </script>
</head>


<body>
    
    <div class="d-flex">
        <!-- Include Sidebar -->
        <div>
            <?php include 'sidebar/sidebar.html'; ?>
        </div>

        <!-- Main Content -->
        <div class="container mt-4">
            <h3 class="mb-3">Affiliate Purchases</h3>
            <div class="table-responsive">
                <table id="paymentsTable" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Earnings (GHS)</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $counter = 1;
                        foreach ($result as $row) {
                            // Calculate earnings based on typeof_purchase
                            if ($row['typeof_purchase'] === 'L1 Purchase') {
                                $earnings = $row['amount'] * 0.15;
                            } elseif ($row['typeof_purchase'] === 'L2 Purchase') {
                                $earnings = $row['amount'] * 0.02;
                            } else {
                                $earnings = 0; // Default to 0 if type is not L1 or L2
                            }

                            $formattedDate = date('F j, Y g:i A', strtotime($row['created_at']));

                            echo "<tr>
                                <td>{$counter}</td>
                                <td>{$row['product_name']}</td>
                                <td>" . number_format($row['amount'], 2) . "</td>
                                <td>" . ucfirst($row['status']) . "</td>
                                <td>{$formattedDate}</td>
                                <td>";
                            
                            // Conditional rendering based on status
                            if ($row['status'] === 'paid') {
                                echo "<span class='text-success'>Paid Out</span>";
                            } else {
                                echo "<button class='btn btn-success btn-sm payout-btn' data-id='{$row['earning_id']}'>
                                        <i class='bi bi-wallet'></i> Payout
                                    </button>";
                            }

                            echo "</td>
                            </tr>";

                            $counter++;
                        }
                        ?>


                    </tbody>
                </table>
            </div>
            <!-- Total Earnings and Payout Button -->
            <div class="mt-4">
                <h3>Total Earnings: GHS <?php echo number_format($total_earnings, 2); ?></h3>

                <!-- Payout Button -->
                <button class="btn btn-primary mt-3" id="payout-btn">Request Payout</button>
                </div>
            </div>
        </div>

        <script>
                // Payout Logic
                $('#payout-btn').click(function() {
                    const paymentId = $(this).data('id');
                    console.log("Payout commenced!!");
                    let service_provider;
                    let phone_number;
                    let accountHolder;
                    // Check if payment details are available for the affiliate
                    $.ajax({
                        url: 'aff_api/check_payment_details.php',
                        method: 'POST',
                        success: function(response) {
                            const data = JSON.parse(response);
                            // console.log(data);
                            // console.log(response);
                            if (data.status === 'exists') {
                                Swal.fire({
                                    title: 'Confirm Payout',
                                    text: 'Are you sure you want to process this payout?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Yes, process it!',
                                    cancelButtonText: 'No'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: 'aff_api/process_payout.php',
                                            method: 'POST',
                                            success: function(response) {
                                                console.log('Response from api ', response);
                                                const data = JSON.parse(response);
                                                console.log('Data from api ', data);
                                                const transfer_code = data.transfer_code;
                                                console.log('Transfer code', transfer_code);
                                                if (data.status === 'pending') {
                                                    Swal.fire({
                                                        title: 'Enter OTP',
                                                        input: 'text',
                                                        inputLabel: 'An OTP was sent to your mail',
                                                        inputPlaceholder: 'Enter the OTP sent to your mail',
                                                        showCancelButton: true,
                                                        confirmButtonText: 'Submit',
                                                        preConfirm: (otp) => {
                                                            if (!otp) {
                                                                Swal.showValidationMessage('Please enter the OTP');
                                                            } else {
                                                                return otp;
                                                            }
                                                        }
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            const otp = result.value;
                                                            $.ajax({
                                                                url: 'aff_api/finalize_transfer.php',
                                                                method: 'POST',
                                                                data: { otp: otp, transfer_code: transfer_code },
                                                                success: function(response) {
                                                                    const data = JSON.parse(response);
                                                                    message = data.message;
                                                                    if (data.status === 'success') {
                                                                        Swal.fire('Success', 'Payout made successfully!', 'success').then(() => {
                                                                            location.reload();
                                                                        });
                                                                    } else {
                                                                        Swal.fire('Error', data.message, 'error');
                                                                        console.log(data);
                                                                    }
                                                                }
                                                            });
                                                        }
                                                    });
                                                } else {
                                                    Swal.fire('Error', data.message, 'error');
                                                    console.log(data);
                                                }
                                            }
                                        });
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Add Payment Details',
                                    html: '<form id="payment-form">' +
                                        '<div class="mb-3">' +
                                        '<label class="form-label">Mobile Network</label>' +
                                        '<select class="form-control" id="service_provider" required>' +
                                            '<option value="MTN">MTN</option>'+
                                            '<option value="Vodafone">Vodafone</option>'+
                                            '<option value="AirtelTigo">AirtelTigo</option>'+
                                        '</select>' +
                                        '<div class="mb-3">' +
                                        '<label class="form-label">Phone Number</label>'+
                                        '<input type="tel" class="form-control" id="phone-number" required>' +
                                        '</div>' +
                                        '<div class="mb-3">' +
                                        '<label class="form-label">Account Holder</label>' +
                                        '<input type="text" class="form-control" id="account-holder" required>' +
                                        '</div>' +
                                        '</form>',
                                    showCancelButton: true,
                                    confirmButtonText: 'Save',
                                    preConfirm: () => {
                                        service_provider = document.getElementById('service_provider').value;
                                        phone_number = document.getElementById('phone-number').value;
                                        accountHolder = document.getElementById('account-holder').value;
                                        if (!service_provider || !phone_number || !accountHolder) {
                                            Swal.showValidationMessage('Please fill all fields');
                                        } else {
                                            return {
                                                service_provider,
                                                phone_number,
                                                accountHolder
                                            };
                                        }
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        const data = result.value;
                                        console.log('Account details being sent,', data);
                                        fetch('aff_api/save_payment_details.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify(data)
                                        }).then(res => res.json()).then(response => {
                                            if (response.status === 'success') {
                                                Swal.fire('Success', 'Payment details saved!', 'success').then(() => {
                                                    location.reload();
                                                });
                                            } else {
                                                Swal.fire('Error', 'Failed to save payment details.', 'error');
                                                console.log(response);
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    });
                });
            // });
        </script>
</body>

</html>