<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="vendors/css/normalize.css">
        <link rel="stylesheet" type="text/css" href="vendors/css/grid.css">
        <link rel="stylesheet" type="text/css" href="vendors/css/ionicons.min.css">
        <link rel="stylesheet" type="text/css" href="vendors/css/animate.css">
        <link rel="stylesheet" type="text/css" href="resources/css/style.css">
        <link rel="stylesheet" type="text/css" href="resources/css/queries.css">
        <link href="https://fonts.googleapis.com/css?family=Lato:100,300,300i,400" rel="stylesheet">

        <title>Hinter</title>
    </head>
        <body> 
			<script>var RESULT = ["20_34", 50, "Middle", 2, 40, 30, 50, 0, 1, 50, 0];</script>
            <header id="home">
                <nav>
                    <div class="row">
                         <img src="resources/img/nj-largeseal.png" alt="signature" class="signature">
                            <ul class="main-nav">
                                <li><a href="index.html">Home</a></li>
                            </ul>
                    </div>
                </nav>
				
				<h1 align="center">Pending housing data:</h1>
				<br>
				<br>
				
<table border='1'; float=center; font size='8';>
<tr>
<th>RealtorID</th>
<th>Address</th>
<th>Town</th>
<th>Price</th>
<th>Beds</th>
<th>Baths</th>
</tr>
			<?php
			/* Connect to MySQL and select the database. */
			$conn = mysqli_connect("hinter.cbg0dfhuoegi.us-east-1.rds.amazonaws.com", "Hinter", "db336project", "Hinter");
				
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			$database = mysqli_select_db($conn, DB_DATABASE);
			
			$sql = "SELECT * FROM Hinter.pending";

			$result = mysqli_query($conn, $sql);
			
			while($row = mysqli_fetch_array($result)){
				echo "<tr>";
				echo "<td>" . $row['realtorID'] . "</td>";
				echo "<td>" . $row['address'] . "</td>";
				echo "<td>" . $row['town'] . "</td>";
				echo "<td>" . $row['price'] . "</td>";
				echo "<td>" . $row['beds'] . "</td>";
				echo "<td>" . $row['baths'] . "</td>";
				echo "</tr>";
			}
			
			mysqli_free_result($result);
			mysqli_close($conn);
			?>
			</table>
			
			</header>
		</body>
</html>