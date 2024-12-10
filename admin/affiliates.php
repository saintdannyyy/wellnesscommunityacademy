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
                    <div class="modal fade" id="referralModal" tabindex="-1" aria-labelledby="referralModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="referralModalLabel">Referral Tree</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul id="referralTree"></ul>
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
                $.post('api/update_status.php', {
                    id,
                    status: 'active'
                }, function(response) {
                    alert(response.message);
                    table.ajax.reload();
                });
            });

            $('#affiliateTable').on('click', '.deactivate-btn', function() {
                const id = $(this).data('id');
                $.post('api/update_status.php', {
                    id,
                    status: 'inactive'
                }, function(response) {
                    alert(response.message);
                    table.ajax.reload();
                });
            });

            $('#affiliateTable').on('click', '.view-referrals-btn', function() {
                const id = $(this).data('id');
                $.get('api/fetch_referrals.php', {
                    affiliate_id: id
                }, function(data) {
                    const referrals = JSON.parse(data);
                    $('#referralTree').html(renderTree(referrals));
                    $('#referralModal').modal('show');
                });
            });

            function renderTree(nodes) {
                let html = '<ul>';
                nodes.forEach(node => {
                    html += `<li>${node.customer_name} (Affiliate ID: ${node.affiliate_id})</li>`;
                    if (node.referrals) {
                        html += renderTree(node.referrals);
                    }
                });
                html += '</ul>';
                return html;
            }
        });
    </script>
</body>

</html>