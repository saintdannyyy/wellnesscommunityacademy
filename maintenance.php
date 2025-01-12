<!DOCTYPE html>
<html>

<head>
    <title>Maintenance</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            background-color: #f8f9fa;
        }

        .d-flex {
            flex: 1;
            display: flex;
            overflow: hidden;
        }

        .sidebar {
            flex-shrink: 0;
            width: 250px;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .coming-soon-container {
            text-align: center;
            max-width: 600px;
            margin: auto;
        }

        .coming-soon-container h1 {
            font-size: 3rem;
            color: #343a40;
        }

        .coming-soon-container p {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        .coming-soon-container .btn {
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="main-content" style="border-top: 5px solid #007bff;">
        <div class="coming-soon-container">
            <h1>Coming Soon</h1>
            <p>This page is currently under maintenance. We're working hard to bring you an amazing experience!</p>
            <button class="btn btn-primary" id="notify-btn">Notify Me</button>
            <div class="mt-3">
                <i class="bi bi-clock-history" style="font-size: 3rem; color: #6c757d;"></i>
            </div>
            <script>
                document.getElementById('notify-btn').addEventListener('click', function() {
                    Swal.fire({
                        title: 'Notify Me',
                        text: 'Enter your email address, and we’ll notify you once we’re back online!',
                        input: 'email',
                        inputPlaceholder: 'Enter your email',
                        showCancelButton: true,
                        confirmButtonText: 'Submit',
                        cancelButtonText: 'Cancel',
                        preConfirm: (email) => {
                            if (!email) {
                                Swal.showValidationMessage('Please enter a valid email!');
                            }
                            return email;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thank You!',
                                text: 'We’ll notify you as soon as the site is live.'
                            });
                        }
                    });
                });
            </script>
        </div>
    </div>
</body>

</html>