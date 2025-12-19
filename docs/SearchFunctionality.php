<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameDen - Search Games</title>
    <style> 
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #ffffffff;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            padding: 10px;
        }
        
        
        h3 {
            color: #2c3e50;
            margin-top: 20px;
        }
        
        form {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin: 10px 0;
        }
        
        select, input[type="text"] {
            padding: 8px;
            margin: 5px;
            border: 1px solid #bdc3c7;
            border-radius: 3px;
            width: 200px;
        }
        
        input[type="submit"] {
            background-color: #327099ff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 3px;
            cursor: pointer;
        }
        
        input[type="submit"]:hover {
            background-color: #234c68ff;
        }
        
        hr {
            border: 0;
            height: 1px;
            background: #ddd;
            margin: 20px 0;
        }
        p {
            
            font-size: 13px;
            color: #2c3e50;
            margin-top: 20px;
        }
         
        table{
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border-radius:20px
        }
        
        th {
            background-color: #327099ff;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #acaeb0ff;
        }
        
        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f5f5f5;
        }
        
        
    </style>
</head>
<body>

    <h1> GameDen - Search Games</h1>

    <br>
    <hr>
     <!-- Search Type Menu  -->
    <h3>Select Search Type:</h3>
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <select name="search_type">
        <option value="">-- Choose Search Type --</option>
        <option value="name">Search by Game Name</option>
        <option value="category">Search by Category</option>
        <option value="rating">Search by Minimum Rating</option>
        <option value="all">Show All Games</option>
    </select>
    <input type="submit" value="Go">
    </form>

    <hr>

    <?php
    // Check which search type was selected - ONLY show forms after clicking "Go"
    if(isset($_GET["search_type"]) && $_GET["search_type"] != "") {
        $search_type = $_GET["search_type"];
        
        // Show appropriate form based on search type
        if($search_type == 'name') {
            ?>
            <h3>Search Games by Name</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>?search_type=name">
            Select Game Name:
            <select name="search_value">
                <option value="">Select a game</option>
                <option value="Rock Paper Scissors">Rock Paper Scissors</option>
                <option value="Sudoku Challenge">Sudoku Challenge</option>
                <option value="Tic Tac Toe">Tic Tac Toe</option>
                <option value="Brain Quiz">Brain Quiz</option>
            </select>
            <input type="hidden" name="search_type" value="name">
            <input type="submit" name="submit" value="Search">
            </form>
            <hr>
            <?php
        }
        elseif($search_type == 'category') {
            ?>
            <h3>Search Games by Category</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>?search_type=category">
            Select Category:
            <select name="search_value">
                <option value="">Select a category</option>
                <option value="Strategy">Strategy</option>
                <option value="Puzzle">Puzzle</option>
                <option value="Knowledge">Knowledge</option>
            </select>
            <input type="hidden" name="search_type" value="category">
            <input type="submit" name="submit" value="Search">
            </form>
            <hr>
            <?php
        }
        elseif($search_type == 'rating') {
            ?>
            <h3>Search Games by Minimum Rating</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>?search_type=rating">
            Minimum Rating (e.g., 4.0):
            <input type="text" name="search_value">
            <input type="hidden" name="search_type" value="rating">
            <input type="submit" name="submit" value="Search">
            </form>
            <hr>
            <?php
        }
        elseif($search_type == 'all') {
            ?>
            <h3>Show All Games</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>?search_type=all">
            <input type="hidden" name="search_type" value="all">
            <input type="submit" name="submit" value="Show All Games">
            </form>
            <hr>
            <?php
        }


        
    } 
    
    // Process form submission and show results
    if(isset($_POST["submit"])) {
        
        echo "<h3>Query Result:</h3>";
        

        
        $servername = "localhost"; 
        $username = "root";
        $password = ""; 
        $dbname = "gamedendb";
        
        //Connect to DB
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        
        //Check if there is problem in DB connection
        if (!$conn) {
            die("Connection failed: ". mysqli_connect_error());
        }
        
        $search_type = $_POST["search_type"];
        $search_value = "";
        if(isset($_POST["search_value"])) {
            $search_value = $_POST["search_value"];
        }

        //Create SQL statement based on search type
        
        
        if($search_type == 'name') {
            // Search by game name
            $sql = "SELECT * FROM games WHERE game_name='$search_value'";
        }
        elseif($search_type == 'category') {
            // Search by category
           $sql = "SELECT * FROM games WHERE category='$search_value'";

        }
        elseif($search_type == 'rating') {
            // Search by minimum rating
            $min_rating = floatval($search_value);
            $sql = "SELECT * FROM games WHERE rating >= $min_rating ORDER BY rating DESC";
        }
        elseif($search_type == 'all') {
            // Show all games
            $sql = "SELECT * FROM games";
        }
        
        //Execute SQL statement
        $result = mysqli_query($conn, $sql);
        
        
        //Check if result is not empty
        if(mysqli_num_rows($result) > 0) {
            // Show summary
            $total_rows = mysqli_num_rows($result);
            echo "<p><strong>Found $total_rows game(s)</strong></p>";
            //Create table to display result
            echo "<table border='1'>";
            echo "<tr>";
            echo "<th>Game Name</td>";
            echo "<th>Category</td>";
            echo "<th>Difficulty</td>";
            echo "<th>Players</td>";
            echo "<th>Rating</td>";
            echo "</tr>";
            
            //Display each row in <tr><td></td>….</tr>
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>".$row["game_name"]."</td>";
                echo "<td>".$row["category"]."</td>";
                echo "<td>".$row["difficulty"]."</td>";
                echo "<td>".$row["players"]."</td>";
                echo "<td>".$row["rating"]."</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            
            
        } 
        else { 
            echo "<p>No games were found</p>";
        }
        
        mysqli_close($conn); //Close DB connection
    }
    ?>
</body>
</html>
