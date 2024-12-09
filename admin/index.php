<?php
// Include database connection
require('db/connection.php');

// Define queries for different purchase categories
$sql_sold_books = "SELECT email, book_id, title AS book_title, amount, created_at, reference FROM Transactions JOIN Books b ON book_id = b.id ORDER BY created_at DESC";
$sql_sold_programs = "SELECT email, program, amount, reference, date FROM sold_programs ORDER BY date DESC";
$sql_sold_courses = "SELECT email, course, amount, reference, date FROM sold_courses ORDER BY date DESC";
$sql_meetings = "SELECT name, email, number, message, duration, visit_date FROM booked_appointments ORDER BY visit_date DESC";
$sql_donation = "SELECT email, amount, reference, date FROM `one-time_donation` ORDER BY date DESC";

// Execute the queries and check for errors
$sold_books_result = $mysqli->query($sql_sold_books);
$sold_programs_result = $mysqli->query($sql_sold_programs);
$sold_courses_result = $mysqli->query($sql_sold_courses);
$meetings_result = $mysqli->query($sql_meetings);
$donation_result = $mysqli->query($sql_donation);

if (!$sold_books_result || !$sold_programs_result || !$sold_courses_result || !$meetings_result || !$donation_result) {
    die("Error in query: " . $mysqli->error);
}
?>

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
                <h3 class="mb-3">Dashboard</h3>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#books">Sold eBooks</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#programs">Sold Programs</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#courses">Sold Courses</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#meetings">Meetings</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#donations">Donations</a></li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3">
                    <!-- Sold Books -->
                    <div id="books" class="tab-pane fade show active">
                        <h4>Sold eBooks</h4>
                        <div class="table-responsive">
                            <table id="booksTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Customer Email</th>
                                        <th>Amount Paid (USD)</th>
                                        <th>Reference</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($sold_books_result->num_rows > 0) {
                                        while ($transaction = $sold_books_result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>{$transaction['book_title']}</td>
                                                    <td>{$transaction['email']}</td>
                                                    <td>{$transaction['amount']}</td>
                                                    <td>{$transaction['reference']}</td>
                                                    <td>" . (new DateTime($transaction['created_at']))->format('jS F, Y \a\t h:i a') . "</td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No transactions found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sold Programs -->
                    <div id="programs" class="tab-pane fade">
                        <h4>Sold Programs</h4>
                        <div class="table-responsive">
                            <table id="programsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>User Email</th>
                                        <th>Program Title</th>
                                        <th>Amount Paid (GHS)</th>
                                        <th>Reference</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($sold_programs_result->num_rows > 0) {
                                        while ($transaction = $sold_programs_result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>{$transaction['email']}</td>
                                                    <td>{$transaction['program']}</td>
                                                    <td>{$transaction['amount']}</td>
                                                    <td>{$transaction['reference']}</td>
                                                    <td>" . (new DateTime($transaction['date']))->format('jS F, Y \a\t h:i a') . "</td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No transactions found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sold Courses -->
                    <div id="courses" class="tab-pane fade">
                        <h4>Sold Courses</h4>
                        <div class="table-responsive">
                            <table id="coursesTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>User Email</th>
                                        <th>Course Title</th>
                                        <th>Amount Paid (GHS)</th>
                                        <th>Reference</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($sold_courses_result->num_rows > 0) {
                                        while ($transaction = $sold_courses_result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>{$transaction['email']}</td>
                                                    <td>{$transaction['course']}</td>
                                                    <td>{$transaction['amount']}</td>
                                                    <td>{$transaction['reference']}</td>
                                                    <td>" . (new DateTime($transaction['date']))->format('jS F, Y \a\t h:i a') . "</td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No transactions found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Meetings -->
                    <div id="meetings" class="tab-pane fade">
                        <h4>Virtual Meetings</h4>
                        <div class="table-responsive">
                            <table id="meetingsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Message</th>
                                        <th>Duration (Mins)</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($meetings_result->num_rows > 0) {
                                        while ($meeting = $meetings_result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>{$meeting['name']}</td>
                                                    <td>{$meeting['email']}</td>
                                                    <td>{$meeting['number']}</td>
                                                    <td>{$meeting['message']}</td>
                                                    <td>{$meeting['duration']}</td>
                                                    <td>" . (new DateTime($meeting['visit_date']))->format('jS F, Y') . "</td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No meetings found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Donations -->
                    <div id="donations" class="tab-pane fade">
                        <h4>One-Time Donations</h4>
                        <div class="table-responsive">
                            <table id="donationsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Donor Email</th>
                                        <th>Amount Donated</th>
                                        <th>Reference</th>
                                        <th>Donation Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($donation_result->num_rows > 0) {
                                        while ($transaction = $donation_result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>{$transaction['email']}</td>
                                                    <td>{$transaction['amount']}</td>
                                                    <td>{$transaction['reference']}</td>
                                                    <td>" . (new DateTime($transaction['date']))->format('jS F, Y \a\t h:i a') . "</td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>No donations found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Initialize DataTables -->
<script>
    $(document).ready(function() {
        $('#booksTable').DataTable({
            "order": [[4, 'desc']], // Sort by transaction date in descending order
            "pageLength": 10 // Display 10 rows per page
        });
        $('#programsTable').DataTable({
            "order": [[4, 'desc']], // Sort by transaction date in descending order
            "pageLength": 10 // Display 10 rows per page
        });
        $('#coursesTable').DataTable({
            "order": [[4, 'desc']], // Sort by transaction date in descending order
            "pageLength": 10 // Display 10 rows per page
        });
        $('#meetingsTable').DataTable({
            "order": [[5, 'desc']], // Sort by meeting date in descending order
            "pageLength": 10 // Display 10 rows per page
        });
        $('#donationsTable').DataTable({
            "order": [[3, 'desc']], // Sort by donation date in descending order
            "pageLength": 10 // Display 10 rows per page
        });
    });
</script>
</body>
</html>
