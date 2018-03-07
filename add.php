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
				<h1 align="center">Thank you!</h1>

<?php


/* Connect to MySQL and select the database. */
$conn = mysqli_connect("hinter.cbg0dfhuoegi.us-east-1.rds.amazonaws.com", "Hinter", "db336project", "Hinter");
    
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
    

 // Preference selections from submission
$realtorID = $_POST['realtorID'];
$address = $_POST['address'];
$town = $_POST['town'];
$zip = $_POST['zip'];
$price = $_POST['price'];
$beds = $_POST['beds'];
$baths = $_POST['baths'];

// Preference selection rankings from queries

$database = mysqli_select_db($conn, DB_DATABASE);
                
?>
                
<?php
$sql = "INSERT INTO `Hinter`.`pending` (`realtorID`, `address`, `town`, `zip`, `price`, `beds`, `baths`) 
VALUES ($realtorID, '$address', '$town', $zip, $price, $beds, $baths)";
                
if(mysqli_query($conn, $sql)){
    echo "                                   Your new house listing has been successfully recorded.";
} 
                
?>




<?php
  


  mysqli_free_result($result);
  mysqli_close($conn);

?>
</header>
</body>
</html>

