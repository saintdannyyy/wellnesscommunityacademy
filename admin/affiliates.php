<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .main {
            border-top: 5px solid #007bff;
        }

        .modal-backdrop {
            z-index: 1040 !important; /* Ensure backdrop doesn't block modal */
        }

        .modal {
            z-index: 1050 !important; /* Ensure modal is above the backdrop */
        }

        body.modal-open {
            overflow: hidden; /* Prevent background scrolling when modal is open */
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div>
            <?php include 'sidebar.html'; ?>
        </div>

        <!-- Main Content -->
        <div class="main flex-grow-1 justify-content-center">
            <div class="p-3">
                <h3 class="mb-3">Affiliate Management</h3>
                <table id="affiliateTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Referrer</th>
                            <th>Status</th>
                            <th>Join Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>

                <!-- Referral Tree Modal -->
                <div class="modal fade" id="referralModal" tabindex="-1" aria-labelledby="referralModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="referralModalLabel">Referral Tree</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="referralTree">Loading...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- jQuery -->
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


    <script>
        $(document).ready(function () {
            // Initialize DataTable
            const table = $('#affiliateTable').DataTable({
                ajax: 'api/fetch_affiliates.php',
                columns: [
                    { data: 'customer_name' },
                    { data: 'referrer_name' },
                    {
                        data: function (row) {
                            return row.status == 1 ? "Active" : "Inactive";
                        }
                    },
                    { data: 'created_at' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary activate-btn" data-id="${row.id}">Activate</button>
                                <button class="btn btn-sm btn-danger deactivate-btn" data-id="${row.id}">Deactivate</button>
                                <button class="btn btn-sm btn-secondary view-referrals-btn" data-id="${row.id}">View Referrals</button>
                            `;
                        }
                    }
                ]
            });

            // Activate Button Click Handler
            $('#affiliateTable').on('click', '.activate-btn', function () {
                const id = $(this).data('id');
                updateStatus(id, 1);
            });

            // Deactivate Button Click Handler
            $('#affiliateTable').on('click', '.deactivate-btn', function () {
                const id = $(this).data('id');
                updateStatus(id, 0);
            });

            // View Referrals Button Click Handler
            $('#affiliateTable').on('click', '.view-referrals-btn', function () {
                const id = $(this).data('id');

                // Fetch referrals for the selected affiliate
                $.get('api/fetch_referrals.php', { affiliate_id: id })
                    .done(function (data) {
                        let referrals;

                        // Parse the response
                        try {
                            referrals = typeof data === 'string' ? JSON.parse(data) : data;
                        } catch (error) {
                            console.error('Failed to parse referral data:', error);
                            alert('Unexpected server response. Please try again.');
                            return;
                        }

                        // Render referral tree and display the modal
                        $('#referralTree').html(renderTree(referrals));
                        $('#referralModal').modal('show');
                    })
                    .fail(function () {
                        alert('Failed to fetch referrals. Please try again.');
                    });
            });

            // Update Status Function
            function updateStatus(id, status) {
                $.post('api/update_status.php', { id, status })
                    .done(function (response) {
                        let data;

                        // Parse the response
                        try {
                            data = typeof response === 'string' ? JSON.parse(response) : response;
                        } catch (error) {
                            console.error('Failed to parse response:', error);
                            alert('Unexpected server response. Please try again.');
                            return;
                        }

                        if (data.success) {
                            alert('Status updated successfully!');
                            table.ajax.reload(); // Reload DataTable
                        } else {
                            alert(data.message || 'Failed to update status. Please try again.');
                        }
                    })
                    .fail(function () {
                        alert('An error occurred while updating the status. Please try again.');
                    });
            }

            // Recursive Function to Render Referral Tree
            function renderTree(nodes) {
                if (!Array.isArray(nodes) || nodes.length === 0) return '<p>No referrals found.</p>';

                let html = '<ul>';
                nodes.forEach(node => {
                    html += `
                        <li>
                            <strong>${node.customer_name}</strong> (${node.customer_email})
                            ${node.referrals ? renderTree(node.referrals) : ''}
                        </li>`;
                });
                html += '</ul>';
                return html;
            }
        });
    </script>
</body>

</html>