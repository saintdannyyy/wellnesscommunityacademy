<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        header {
            background-color: darkgreen;
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 3px;
            margin-bottom: 20px;
        }
        header h2 {
            color: #fff;
            margin: 0;
            font-size: 1.8rem;
            flex-grow: 1;
        }
        .logout {
            font-size: 1.1rem;
            margin-right: 0;
            color: white;
            text-decoration: none;
            background-color: rgb(0, 174, 255);
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .logout:hover {
            background-color: #007bff;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <header>
        <h2>City of Sellinstar Admin</h2>
        <!--Localhost-->
        <!--<a href="\cossghana\admin\logout.html" class="logout">Logout</a>-->
        <!--Hosting-->
        <a href="\admin\logout.html" class="logout">Logout</a>
    </header>
</body>
</html>
