<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
    <style>
        /* Sidebar style */
        .sidebar {
            min-width: 200px;
            max-width: 250px;
            background-color: #f8f9fa;
            padding: 20px;
            position: fixed;
            height: 100vh;
            z-index: 1;
        }

        /* Main content style */
        .main-content {
            margin-left: 250px; /* Width of the sidebar */
            padding: 20px;
            overflow-y: auto;
            height: 100vh;
            max-height: 100vh; /* Makes the content area scrollable */
        }

        /* Make the tabs sticky */
        .nav-tabs {
            position: sticky;
            top: 0;
            z-index: 0;
            background-color: #fff; /* Make sure tabs have a background */
        }

        /* Optional: To prevent overlap of tabs with content */
        .tab-content {
            margin-top: 20px;
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
                <h3 class="mb-3">Help</h3>
