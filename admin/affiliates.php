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
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 CSS -->
    <!-- <link rel="stylesheet" href="../assets/sweetalert2/package/dist/sweetalert2.min.css"> -->
    <style>
        .main {
            border-top: 5px solid #007bff;
        }

        .modal-backdrop {
            z-index: 1040 !important;
            /* Ensure backdrop doesn't block modal */
        }

        .modal {
            z-index: 1050 !important;
            /* Ensure modal is above the backdrop */
        }

        body.modal-open {
            overflow: hidden;
            /* Prevent background scrolling when modal is open */
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
                <table id="affiliateTable" class="table table-bordered table-hover table-responsive w-100 d-block d-md-table">
                    <thead>
                        <tr>
                            <th>No.</th>
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
                <!-- Links Modal -->
                <div class="modal fade" id="linksModal" tabindex="-1" aria-labelledby="linksModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="linksModalLabel">Affiliate Links</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="cusRefLink" class="form-label">Customer Referral Link</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="cusRefLink" readonly>
                                        <button class="btn btn-outline-secondary" type="button" id="copyCusRefLink">Copy</button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="affiliateRefLink" class="form-label">Affiliate Referral Link</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="affiliateRefLink" readonly>
                                        <button class="btn btn-outline-secondary" type="button" id="copyAffiliateRefLink">Copy</button>
                                    </div>
                                </div>
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
    <!-- SweetAlert2 JS -->
    <!-- <script src="../assets/sweetalert2/package/dist/sweetalert2.all.min.js"></script> -->
    <!-- SweetAlert2 CSS -->
    <!-- <link rel="stylesheet" href="../assets/sweetalert2/package/dist/sweetalert2.min.css"> -->


    <script>
        $(document).ready(function() {
            let cus_ref, affiliate_ref;
            // Initialize DataTable
            const table = $('#affiliateTable').DataTable({
                ajax: 'api/fetch_affiliates.php',
                columns: [
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'customer_name'
                    },
                    {
                        data: 'referrer_name'
                    },
                    {
                        data: function(row) {
                            return row.status == 1 ? "Active" : "Inactive";
                        }
                    },
                    {
                        data: 'created_at',
                        render: function(data, type, row) {
                            const date = new Date(data);
                            const options = {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            };
                            return date.toLocaleDateString('en-GB', options);
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                    ${row.status == 1 ? 
                        `<button class="btn btn-sm btn-danger deactivate-btn" data-id="${row.id}" title="Deactivate">
                        <i class="bi bi-x-circle"></i>
                        </button>` : 
                        `<button class="btn btn-sm btn-primary activate-btn" data-id="${row.id}" title="Activate">
                        <i class="bi bi-check-circle"></i>
                        </button>`
                    }
                                <button class="btn btn-sm btn-secondary view-referrals-btn" data-id="${row.id}" title="View Referrals">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning view-links-btn" data-id="${row.id}" data-customer-name="${row.customer_name}" data-cus-ref="${row.cus_ref}" data-affiliate-ref="${row.affiliate_ref}" title="View Links">
                                    <i class="bi bi-link-45deg"></i>
                                </button>
                            `;
                        }
                    }
                ]
            });

            // View Links Button Click Handler
            $('#affiliateTable').on('click', '.view-links-btn', function() {
                const cusRef = $(this).data('cus-ref');
                const affiliateRef = $(this).data('affiliate-ref');
                const customerName = $(this).data('customer-name');
                // console.log("Customer Referral Link:", cusRef);
                // console.log("Affiliate Referral Link:", affiliateRef);
                $('#cusRefLink').val(cusRef);
                $('#affiliateRefLink').val(affiliateRef);
                $('#linksModal').modal('show');
            });

            // Copy to Clipboard Function
            function copyToClipboard(elementId) {
                const copyText = document.getElementById(elementId);
                copyText.select();
                copyText.setSelectionRange(0, 99999); // For mobile devices
                document.execCommand("copy");
            }

            // Copy Customer Referral Link
            $('#copyCusRefLink').click(function() {
                copyToClipboard('cusRefLink');
                alert(`${customerName}'s Customer Referral Link copied to clipboard`);
            });

            // Copy Affiliate Referral Link
            $('#copyAffiliateRefLink').click(function() {
                copyToClipboard('affiliateRefLink');
                alert(`${customerName}'s Affiliate Referral Link copied to clipboard!`);
            });
            // Activate Button Click Handler
            $('#affiliateTable').on('click', '.activate-btn', function() {
                const id = $(this).data('id');
                updateStatus(id, 1);
            });

            // Deactivate Button Click Handler
            $('#affiliateTable').on('click', '.deactivate-btn', function() {
                const id = $(this).data('id');
                updateStatus(id, 0);
            });

            // View Referrals Button Click Handler
            $('#affiliateTable').on('click', '.view-referrals-btn', function() {
                const id = $(this).data('id');

                // Fetch referrals for the selected affiliate
                $.get('api/fetch_referrals.php', {
                        affiliate_id: id
                    })
                    .done(function(data) {
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
                    .fail(function() {
                        alert('Failed to fetch referrals. Please try again.');
                    });
            });

            // Update Status Function
            function updateStatus(id, status) {
                $.post('api/update_status.php', {
                        id,
                        status
                    })
                    .done(function(response) {
                        let data;

                        // Parse the response
                        try {
                            data = typeof response === 'string' ? JSON.parse(response) : response;
                        } catch (error) {
                            console.error('Failed to parse response:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Unexpected server response. Please try again.'
                            });
                            return;
                        }

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Status updated successfully!'
                            });
                            table.ajax.reload(); // Reload DataTable
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to update status. Please try again.'
                            });
                        }
                    })
                    .fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating the status. Please try again.'
                        });
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