<?php include "../inc/dbinfo.inc"; ?>

<html>
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
                                <li><a href="http://ec2-54-209-143-82.compute-1.amazonaws.com/">Home</a></li> 	
                            </ul>
                    </div>
                </nav>
				<br>
				<h1 align="center">Results based on selected preferences:</h1>

<?php


/* Connect to MySQL and select the database. */
$conn = mysqli_connect("hinter.cbg0dfhuoegi.us-east-1.rds.amazonaws.com", "Hinter", "db336project", "Hinter");
    
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
    

 // Preference selections from submission
$city = $_POST['city'];
$price = $_POST['price'];
$bed = $_POST['bed'];
$bath = $_POST['bath'];
$county = $_POST['county'];


// Preference selection rankings from queries

    
$database = mysqli_select_db($conn, DB_DATABASE);
?>

<table border='1'; align=center; font size='8';>
<tr>
<th>Address</th>
<th>Price</th>
<th>Beds</th>
<th>Baths</th>
<th>URL</th>
</tr>
<?php
$sql = "SELECT Address, Address_Full, Price, Bedroom, Bath, House_Link FROM Hinter.House_For_Sale WHERE Bedroom >= ". $bed. " AND Bath >= ". $bath. " AND Price <= ". $price." AND Zip IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $county. "')";

$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_array($result)){
	echo "<tr>";
	echo "<td>" . $row['Address_Full']. "</td>";
	echo "<td>" . $row['Price']. "</td>";
	echo "<td>" . $row['Bedroom']. "</td>";
	echo "<td>" . $row['Bath']. "</td>";
	echo "<td>" . $row['House_Link']. "</td>";
	echo "</tr>";
}


?>
</table>
<?php
  


  mysqli_free_result($result);
  mysqli_close($conn);
  

?>

</header>
</body>
</html>

