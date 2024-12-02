<?php
// error_reporting(E_ALL);

// Display errors on the screen (for development purposes)
// ini_set('display_errors', 1);

// Log errors to the server's error log (optional)
// ini_set('log_errors', 1);

// Include database connection
require('db/connection.php');

// Define queries for different purchase categories
$sql_sold_books = "SELECT email, book_id, title AS book_title, amount, created_at, reference FROM Transactions JOIN Books b ON book_id = b.id";

$sql_sold_programs = "SELECT email, program, amount, reference, date FROM sold_programs";

$sql_sold_courses = "SELECT email, course, amount, reference, date FROM sold_courses";

$sql_meetings = "SELECT name, email, number, message, duration, visit_date FROM booked_appointments";

$sql_donation = "SELECT email, amount, reference, date FROM `one-time_donation`";

// Execute the queries and check for errors
$sold_books_result = $mysqli->query($sql_sold_books);
$sold_programs_result = $mysqli->query($sql_sold_programs);
$sold_courses_result = $mysqli->query($sql_sold_courses);
$meetings_result = $mysqli->query($sql_meetings);
$donation_result = $mysqli->query($sql_donation);

// var_dump($sold_books_result);
// var_dump($sold_programs_result);
// var_dump($sold_courses_result);
// var_dump($meetings_result);
// var_dump($donation_result);

if (!$sold_books_result || !$sold_programs_result || !$sold_courses_result || !$meetings_result || !$donation_result) {
    die("Error in query: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sold Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            overflow-x: auto;
        }
        .tab-button {
            padding: 10px 20px;
            background-color: #f1f1f1;
            color: black;
            border: 1px solid #ccc;
            cursor: pointer;
            transition: background-color 0.3s ease;
            white-space: nowrap;
        }
        .tab-button.active {
            background-color: #007BFF;
            color: white;
        }
        .content {
            display: none;
        }
        .content.active {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .logout-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: red;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        @media (max-width: 768px) {
            .tabs {
                gap: 5px;
                flex-wrap: nowrap;
                overflow-x: scroll;
            }
            .tab-button {
                flex: 1 0 auto;
                font-size: 14px;
                padding: 8px 10px;
            }
            table {
                display: block;
                overflow-x: auto;
            }
            th, td {
                font-size: 14px;
                padding: 8px;
            }
        }
        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }
            h1 {
                font-size: 18px;
            }
            .tab-button {
                font-size: 12px;
                padding: 5px 8px;
            }
            th, td {
                font-size: 12px;
                padding: 6px;
            }
            .logout-button {
                font-size: 14px;
                padding: 8px 10px;
            }
        }
    </style>

</head>
<body>
    <div class="container">
        <h1>Dashboard</h1>
        <div class="tabs">
            <button class="tab-button active" data-tab="books">Sold eBooks</button>
            <button class="tab-button" data-tab="programs">Sold Programs</button>
            <button class="tab-button" data-tab="courses">Sold Courses</button>
            <button class="tab-button" data-tab="meetings">Meetings</button>
            <button class="tab-button" data-tab="1time_donation">One Time Donations</button>
        </div>

        <!-- Content for each tab -->
        <div id="books" class="content active">
            <h2>Sold eBooks</h2>
            <table>
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
                    if ($sold_books_result->num_rows > 0){
                        while ($transaction = $sold_books_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$transaction['book_title']}</td>
                                    <td>{$transaction['email']}</td>
                                    <td>{$transaction['amount']}</td>
                                    <td>{$transaction['reference']}</td>
                                    <td>";
                                        $date = new DateTime($transaction['created_at']);
                                        echo $date->format('jS F, Y \a\t h:i a');
                                        echo "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No transactions found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div id="programs" class="content">
            <h2>Sold Programs</h2>
            <table>
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
                                    <td>{$transaction['program']}</td>
                                    <td>{$transaction['email']}</td>
                                    <td>{$transaction['amount']}</td>
                                    <td>{$transaction['reference']}</td>
                                    <td>";
                                        $date = new DateTime($transaction['created_at']);
                                        echo $date->format('jS F, Y \a\t h:i a');
                                        echo "
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No transactions found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div id="courses" class="content">
            <h2>Sold Courses</h2>
            <table>
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
                                    <td>{$transaction['course']}</td>
                                    <td>{$transaction['email']}</td>
                                    <td>{$transaction['amount']}</td>
                                    <td>{$transaction['reference']}</td>
                                    <td>";
                                        $date = new DateTime($transaction['created_at']);
                                        echo $date->format('jS F, Y \a\t h:i a');
                                        echo "
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No transactions found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div id="meetings" class="content">
            <h2>Virtual Meetings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Duration(Mins)</th>
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
                                    <td>";
                                        $date = new DateTime($transaction['created_at']);
                                        echo $date->format('jS F, Y \a\t h:i a');
                                        echo "
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No meetings found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div id="1time_donation" class="content">
            <h2>One Time Donations</h2>
            <table>
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
                                    <td>";
                                        $date = new DateTime($transaction['date']);
                                        echo $date->format('jS F, Y \a\t h:i a');
                                        echo "
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No transactions found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <!-- Logout Button -->
        <a href="logout.html" class="logout-button">Logout</a>
    </div>

    <script>
        // JavaScript to handle tab switching
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.content').forEach(content => content.classList.remove('active'));

                button.classList.add('active');
                document.getElementById(button.getAttribute('data-tab')).classList.add('active');
            });
        });
    </script>
</body>
</html>
