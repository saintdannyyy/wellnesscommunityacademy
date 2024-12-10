<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #f8f9fa;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 56px;
            z-index: 1;
        }

        .main-content {
            z-index: 0;
            margin-left: 250px;
            width: calc(100% - 250px);
            padding-top: 56px;
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
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="sidebar">
                <?php include 'sidebar.html'; ?>
            </div>

            <!-- Main Content -->
            <div class="main-content">
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
                    <div class="modal fade" id="referralModal" tabindex="-1" aria-labelledby="referralModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
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
    </div>
    <script>
        $(document).ready(function() {
            const table = $('#affiliateTable').DataTable({
                ajax: 'api/fetch_affiliates.php',
                columns: [
                    {
                        data: 'customer_name'
                    },
                    {
                        data: 'referrer_affiliate_id'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                        <button class="btn btn-sm btn-primary activate-btn" data-id="${row.id}">Activate</button>
                                        <button class="btn btn-sm btn-danger deactivate-btn" data-id="${row.id}">Deactivate</button>
                                        <button class="btn btn-sm btn-secondary view-referrals-btn" data-id="${row.id}">View Referrals</button>
                                    `;
                        }
                    }
                ]
            });

            $('#affiliateTable').on('click', '.activate-btn', function() {
                const id = $(this).data('id');
                
                // Send the POST request to update the status
                $.post('api/update_status.php', { id, status: 'active' })
                    .done(function(response) {
                        let data;
                        
                        // Attempt to parse the response
                        try {
                            data = typeof response === 'string' ? JSON.parse(response) : response;
                        } catch (error) {
                            // console.error('Failed to parse response:', error);
                            alert('Unexpected server response. Please try again.');
                            return;
                        }

                        // Handle the success or failure of the operation
                        if (data.success) {
                            alert('Status updated successfully!');
                        } else {
                            console.warn('Server response:', data);
                            alert(data.message || 'Failed to update status. Please try again.');
                        }

                        // Reload the table to reflect changes
                        table.ajax.reload();
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        // Handle request failure
                        // console.error('AJAX Error:', textStatus, errorThrown);
                        alert('An error occurred while updating the status. Please check your network and try again.');
                    });
            });


            $('#affiliateTable').on('click', '.deactivate-btn', function() {
                const id = $(this).data('id');

                // Send the POST request to deactivate the affiliate
                $.post('api/update_status.php', { id, status: 'inactive' })
                    .done(function(response) {
                        let data;

                        // Attempt to parse the response
                        try {
                            data = typeof response === 'string' ? JSON.parse(response) : response;
                        } catch (error) {
                            console.error('Failed to parse response:', error);
                            alert('Unexpected server response. Please try again.');
                            return;
                        }

                        // Handle the success or failure of the operation
                        if (data.success) {
                            alert('Affiliate deactivated successfully!');
                        } else {
                            console.warn('Server response:', data);
                            alert(data.message || 'Failed to deactivate the affiliate. Please try again.');
                        }

                        // Reload the table to reflect changes
                        table.ajax.reload();
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        // Handle request failure
                        console.error('AJAX Error:', textStatus, errorThrown);
                        alert('An error occurred while deactivating the affiliate. Please check your network and try again.');
                    });
            });


            $('#affiliateTable').on('click', '.view-referrals-btn', function() {
                const id = $(this).data('id');

                // Fetch referrals for the selected affiliate
                $.get('api/fetch_referrals.php', { affiliate_id: id })
                    .done(function(data) {
                        let referrals;

                        // Attempt to parse the response
                        try {
                            referrals = typeof data === 'string' ? JSON.parse(data) : data;
                        } catch (error) {
                            console.error('Failed to parse referral data:', error);
                            alert('Unexpected server response. Please try again.');
                            return;
                        }

                        // Render the referral tree and display the modal
                        $('#referralTree').html(renderTree(referrals));
                        $('#referralModal').modal('show');
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error:', textStatus, errorThrown);
                        alert('An error occurred while fetching referrals. Please check your network and try again.');
                    });
            });

            // Recursive function to render the referral tree
            function renderTree(nodes) {
                if (!Array.isArray(nodes) || nodes.length === 0) return '<p>No referrals found.</p>';

                let html = '<ul>';
                nodes.forEach(node => {
                    html += `
                        <li>
                            <strong>${node.customer_name}</strong> (Affiliate ID: ${node.affiliate_id})
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