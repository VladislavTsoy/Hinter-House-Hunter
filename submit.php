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
                                <li><a href="index.html">Home</a></li>
                            </ul>
                    </div>
                </nav>
				<br>
				

<?php


/* Connect to MySQL and select the database. */
$conn = mysqli_connect("hinter.cbg0dfhuoegi.us-east-1.rds.amazonaws.com", "Hinter", "db336project", "Hinter");
    
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
    

 // Preference selections from submission   
$counties = $_POST['counties'];
$demographics = $_POST['demo-option'];
$population = $_POST['population-option'];
$family_oriented = $_POST['family-option'];
$education = $_POST['education-option'];
$male_female_ratio = $_POST['male-female-option'];
$safety = $_POST['safety-option'];
$monthly_cost = $_POST['monthly-cost-option'];
$houseval_option = $_POST['houseval-option'];
$average_income = $_POST['income-option'];

// Preference selection rankings from queries

    
$database = mysqli_select_db($conn, DB_DATABASE);
?>


<?php
/* ----- TOWN FROM COUNTY PICK QUERY -----*/
$TownCityList = new SplDoublyLinkedList();
$FinalZipList = new SplDoublyLinkedList();
$FinalRank = new SplDoublyLinkedList(); 

$sql = "SELECT Zip_Code, City FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "'";

$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) { 
	$FinalZipList->push($row["Zip_Code"]);
	$TownCityList->push($row["City"]);
	$FinalRank->push(0); 
}

?>

<?php
  /* ----- AGE DEMOGRAPHICS QUERY -----*/  

$count = 1;
$DemoList = new SplDoublyLinkedList();
$DemoRank = new SplDoublyLinkedList();
if($demographics != -1){
$sql = "SELECT Zip_Code, ". $demographics. " FROM Hinter.NJ_Residents_Characteristics WHERE Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') GROUP by Zip_Code ORDER BY ". $demographics. " DESC";
    
$result =  mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) {
	$DemoList->push($row["Zip_Code"]);
	$DemoRank->push($count);
	$count++;
}
}   
?>

<?php
  /* ----- POPULATION DENSITY QUERY -----*/  
$count = 1;
$PopList = new SplDoublyLinkedList();
$PopRank = new SplDoublyLinkedList();

if($population > 0){
$sql = "SELECT b.Zip_Code, a.City, a.Population FROM Hinter.NJ_Crime_Statistics a, Hinter.City_Zip_County_Key b WHERE a.Population <= ". $population. " AND a.Date = 2016 AND a.City IN (SELECT City FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') AND b.City = a.City GROUP BY a.City ORDER BY a.Population DESC";

$result =  mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) {
	$PopList->push($row["Zip_Code"]);
	$PopRank->push($count);
	$count++;
}
}
?>
    

<?php
  /* ----- FAMILY ORIENTED QUERY -----*/  

$count = 1;
$FamList = new SplDoublyLinkedList();
$FamRank = new SplDoublyLinkedList();

if($family_oriented != '-1'){
$sql = "SELECT distinct Zip_Code, ". $family_oriented. " FROM Hinter.NJ_Residents_Characteristics WHERE Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY ". $family_oriented. " DESC";

$result =  mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)) {
	$FamList->push($row["Zip_Code"]);
	$FamRank->push($count);
	$count++;
}
}
?>

<?php
   /* ----- MALE/FEMALE RATIO QUERY -----*/     

$count = 1;
$RatioList = new SplDoublyLinkedList();
$RatioRank = new SplDoublyLinkedList();

if($male_female_ratio != '-1'){
if($male_female_ratio == 'Female_Perc') {
        $sql="SELECT Zip_Code FROM Hinter.NJ_Residents_Characteristics WHERE Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY Female_Perc DESC";

} elseif ($male_female_ratio == 'Male_Perc') {
        $sql="SELECT Zip_Code FROM Hinter.NJ_Residents_Characteristics WHERE Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY Male_Perc DESC";
} else { 
        $sql="SELECT Zip_Code FROM Hinter.NJ_Residents_Characteristics WHERE Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY ABS(Male_Perc - Female_Perc) ASC";
}

$result =  mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)) {
	$RatioList->push($row["Zip_Code"]);
	$RatioRank->push($count);
	$count++;
}
}
?>

<?php
   /* ----- MONTHLY COST QUERY -----*/    
	
$count = 1;
$MonthlyCostList = new SplDoublyLinkedList();
$MonthlyCostRank = new SplDoublyLinkedList();

if($monthyl_cost != '-1'){
if($monthly_cost == '1') {
        $sql="SELECT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Monthly_Cost <= 1000 AND Zip_Code NOT IN (SELECT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Monthly_Cost = 0)AND Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY Median_Monthly_Cost ASC";
    
} elseif ($monthly_cost == '2') {
        $sql="SELECT s1.Zip_Code FROM NJ_Housing_Expenses s1, NJ_Housing_Expenses s2 WHERE s1.Median_Monthly_Cost <= 2000 AND s2.Median_Monthly_Cost > 1000 AND s1.Zip_Code=s2.Zip_Code AND s1.Zip_Code NOT IN ( SELECT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Monthly_Cost = 0 ) AND s1.Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY s1.Median_Monthly_Cost ASC";
    
} elseif ($monthly_cost == '3') {
        $sql="SELECT s1.Zip_Code FROM NJ_Housing_Expenses s1, NJ_Housing_Expenses s2 WHERE s1.Median_Monthly_Cost <= 3000 AND s2.Median_Monthly_Cost > 2000 AND s1.Zip_Code=s2.Zip_Code AND s1.Zip_Code NOT IN (SELECT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Monthly_Cost = 0 ) AND s1.Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY s1.Median_Monthly_Cost ASC";
    
} elseif ($monthly_cost == '4') {
        $sql="SELECT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Monthly_Cost > 3000 and Zip_Code NOT IN ( SELECT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Monthly_Cost = 0 ) AND Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY Median_Monthly_Cost ASC";
} 

$result =  mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)) {
	$MonthlyCostList->push($row["Zip_Code"]);
	$MonthlyCostRank->push($count);
	$count++;
}
}
?>


<?php
   /* ----- MEDIAN HOUSE VALUE QUERY -----*/       

$count = 1;
$HouseValueList = new SplDoublyLinkedList();
$HouseValueRank = new SplDoublyLinkedList();

if($houseval_option != '-1'){
if($houseval_option == '1') {
		$sql="SELECT DISTINCT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Value < 100000 AND Zip_Code NOT IN (SELECT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Value = 0) AND Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY Median_Value DESC";
    
} elseif ($houseval_option == '2') {
        $sql="SELECT DISTINCT s1.Zip_Code FROM NJ_Housing_Expenses s1, NJ_Housing_Expenses s2 WHERE s1.Median_Value >= 100000 AND s2.Median_Value < 250000 AND s1.Zip_Code=s2.Zip_Code AND s1.Zip_Code NOT IN (SELECT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Value = 0) AND s1.Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY s1.Median_Value DESC";
    
} elseif ($houseval_option == '3') {
        $sql="SELECT DISTINCT s1.Zip_Code FROM NJ_Housing_Expenses s1, NJ_Housing_Expenses s2 WHERE s1.Median_Value >= 250000 AND s2.Median_Value < 500000 AND s1.Zip_Code=s2.Zip_Code AND s1.Zip_Code NOT IN (SELECT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Value = 0) AND s1.Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY s1.Median_Value DESC";
    
} elseif ($houseval_option == '4') {
        $sql="SELECT DISTINCT s1.Zip_Code FROM NJ_Housing_Expenses s1, NJ_Housing_Expenses s2 WHERE s1.Median_Value >= 500000 AND s2.Median_Value < 750000 AND s1.Zip_Code=s2.Zip_Code AND s1.Zip_Code NOT IN (SELECT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Value = 0) AND s1.Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY s1.Median_Value DESC";
    
} elseif ($houseval_option == '5') {
        $sql="SELECT DISTINCT s1.Zip_Code FROM NJ_Housing_Expenses s1, NJ_Housing_Expenses s2 WHERE s1.Median_Value >= 750000 AND s2.Median_Value < 1000000 AND s1.Zip_Code=s2.Zip_Code AND s1.Zip_Code NOT IN (SELECT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Value = 0) AND s1.Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY s1.Median_Value DESC";
    
} elseif ($houseval_option == '6') {
        $sql="SELECT DISTINCT Zip_Code FROM NJ_Housing_Expenses s WHERE Median_Value >= 1000000  AND Zip_Code NOT IN (SELECT Zip_Code FROM NJ_Housing_Expenses WHERE Median_Value = 0) AND Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "') ORDER BY Median_Value DESC ";
} 

$result =  mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)) {
	$HouseValueList->push($row["Zip_Code"]);
	$HouseValueRank->push($count);
	$count++;
}
}
?>

<?php
   /* ----- MEAN INCOME QUERY -----*/       

$count = 1;
$MeanIncomeList = new SplDoublyLinkedList();
$MeanIncomeRank = new SplDoublyLinkedList();   

if($average_income != '-1') {
if($average_income == '1') {
        $sql="SELECT DISTINCT Zip_Code FROM NJ_Residents_Mean_Income WHERE Mean_Income < 50000 AND Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "')";
    
} elseif ($average_income == '2') {
        $sql="SELECT DISTINCT s1.Zip_Code FROM NJ_Residents_Mean_Income s1,NJ_Residents_Mean_Income s2 WHERE s1.Mean_Income >= 50000 AND s2.Mean_Income < 100000 AND s1.Zip_Code=s2.Zip_Code AND s1.Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "')";
    
} elseif ($average_income == '3') {
        $sql="SELECT DISTINCT s1.Zip_Code FROM NJ_Residents_Mean_Income s1,NJ_Residents_Mean_Income s2 WHERE s1.Mean_Income >= 100000 AND s2.Mean_Income < 150000 AND s1.Zip_Code=s2.Zip_Code AND s1.Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "')";
    
} elseif ($average_income == '4') {
        $sql="SELECT DISTINCT s1.Zip_Code FROM NJ_Residents_Mean_Income s1,NJ_Residents_Mean_Income s2 WHERE s1.Mean_Income >= 150000 AND s2.Mean_Income < 200000 AND s1.Zip_Code=s2.Zip_Code AND s1.Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "')";
    
} elseif ($average_income == '5') {
        $sql="SELECT DISTINCT s1.Zip_Code FROM NJ_Residents_Mean_Income s1,NJ_Residents_Mean_Income s2 WHERE s1.Mean_Income >= 200000 AND s2.Mean_Income < 250000 AND s1.Zip_Code=s2.Zip_Code AND s1.Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "')";
    
} elseif ($average_income == '6') {
        $sql="SELECT DISTINCT Zip_Code FROM NJ_Residents_Mean_Income WHERE Mean_Income > 250000 AND Zip_Code IN (SELECT Zip_Code FROM Hinter.City_Zip_County_Key WHERE County = '". $counties. "')";
} 

$result =  mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)) {
	$MeanIncomeList->push($row["Zip_Code"]);
	$MeanIncomeRank->push($count);
	$count++;
}
}
?>
       
    
<?php
	// Count the rankings for the Demographics preference.
	if($demographics != '-1' && $DemoList->count() > 0){
		
		$DemoRank->rewind();
		for($DemoList->rewind(); $DemoList->valid(); $DemoList->next()){
			$count = 0;
			$go = 1;
			$FinalRank->rewind();
			
			for($FinalZipList->rewind(); $FinalZipList->valid() && $go == 1; $FinalZipList->next()){
				
				if($DemoList->current() == $FinalZipList->current()){
					$newValue = $FinalRank->current() + $DemoRank->current();
					
					$FinalRank->add($count, $newValue);
					$go = 0;
				}
				
				$count++;
				$FinalRank->next();
			}
			
			$DemoRank->next();
		}
	} elseif($demographics == '-1') {
	} else {
		echo '<script>alert("Your preference for demographics returned no results. Results will not be affected by demographics.")</script>';
	}
	
	// Count the rankings for the Population Density preference.
	if($population != '-1' && $PopList->count() > 0){
		
		$PopRank->rewind();
		for($PopList->rewind(); $PopList->valid(); $PopList->next()){
			$count = 0;
			$go = 1;
			$FinalRank->rewind();
			
			for($FinalZipList->rewind(); $FinalZipList->valid() && $go == 1; $FinalZipList->next()){
				
				if($PopList->current() == $FinalZipList->current()){
					$newValue = $FinalRank->current() + $PopRank->current();
					
					$FinalRank->add($count, $newValue);
					$go = 0;
				}
				
				$count++;
				$FinalRank->next();
			}
			
			$PopRank->next();
		}
	} elseif($population == '-1') {
	} else {
		echo '<script>alert("Your preference for population density returned no results. Results will not be affected by population.")</script>';
	}
	
	// Count the rankings for the family oriented preference.
	if($family_oriented != '-1' && $FamList->count() > 0){
		
		$FamRank->rewind();
		for($FamList->rewind(); $FamList->valid(); $FamList->next()){
			$count = 0;
			$go = 1;
			$FinalRank->rewind();
			
			for($FinalZipList->rewind(); $FinalZipList->valid() && $go == 1; $FinalZipList->next()){
				
				if($FamList->current() == $FinalZipList->current()){
					$newValue = $FinalRank->current() + $FamRank->current();
					
					$FinalRank->add($count, $newValue);
					$go = 0;
				}
				
				$count++;
				$FinalRank->next();
			}
			
			$FamRank->next();
		}
	} elseif($family_oriented == '-1') {
	} else {
		echo '<script>alert("Your preference for family orientation returned no results. Results will not be affected by this option.")</script>';
	}
	
	// Count the rankings for the gender ratio preference.
	if($male_female_ratio != '-1' && $RatioList->count() > 0){
		
		$RatioRank->rewind();
		for($RatioList->rewind(); $RatioList->valid(); $RatioList->next()){
			$count = 0;
			$go = 1;
			$FinalRank->rewind();
			
			for($FinalZipList->rewind(); $FinalZipList->valid() && $go == 1; $FinalZipList->next()){
				
				if($RatioList->current() == $FinalZipList->current()){
					$newValue = $FinalRank->current() + $RatioRank->current();

					$FinalRank->add($count, $newValue);
					$go = 0;
				}
				
				$count++;
				$FinalRank->next();
			}
			
			$RatioRank->next();
		}
	} elseif($male_female_ratio == '-1') {
	} else {
		echo '<script>alert("Your preference for gender ratio returned no results. Results will not be affected by this option.")</script>';
	}
	
	// Count the rankings for the monthly cost preference.
	if($monthly_cost != '-1' && $MonthlyCostList->count() > 0){
		
		$MonthlyCostRank->rewind();
		for($MonthlyCostList->rewind(); $MonthlyCostList->valid(); $MonthlyCostList->next()){
			$count = 0;
			$go = 1;
			$FinalRank->rewind();
			
			for($FinalZipList->rewind(); $FinalZipList->valid() && $go == 1; $FinalZipList->next()){
				
				if($MonthlyCostList->current() == $FinalZipList->current()){
					$newValue = $FinalRank->current() + $MonthlyCostRank->current();
					
					$FinalRank->add($count, $newValue);
					$go = 0;
				}
				
				$count++;
				$FinalRank->next();
			}
			
			$MonthlyCostRank->next();
		}
	} elseif($monthly_cost == '-1') {
	} else {
		echo '<script>alert("Your preference for monthly cost returned no results. Results will not be affected by this option.")</script>';
	}
	
	// Zips included in this list will cut the list of places to live down to just within the Zip Codes returned
	// Count the rankings for the house value preference.
	if($houseval_option != '-1' && $HouseValueList->count() > 0){
		
		$HouseValueRank->rewind();
		for($HouseValueList->rewind(); $HouseValueList->valid(); $HouseValueList->next()){
			$count = 0;
			$go = 1;
			$FinalRank->rewind();
			
			for($FinalZipList->rewind(); $FinalZipList->valid() && $go == 1; $FinalZipList->next()){
				
				if($HouseValueList->current() == $FinalZipList->current()){
					$newValue = $FinalRank->current() + $HouseValueRank->current();
					
					$FinalRank->add($count, $newValue);
					$go = 0;
				}
				
				$count++;
				$FinalRank->next();
			}
			
			$HouseValueRank->next();
		}
	} elseif($houseval_option == '-1') {
	} else {
		echo '<script>alert("Your preference for house value returned no results. Results will not be affected by this option.")</script>';
	}
	
	//Zips included here are generally the best cities to live in.
	// Count the rankings for the mean income preference.
	if($average_income != '-1' && $MeanIncomeList->count() > 0){
		
		$MeanIncomeRank->rewind();
		for($MeanIncomeList->rewind(); $MeanIncomeList->valid(); $MeanIncomeList->next()){
			$count = 0;
			$go = 1;
			$FinalRank->rewind();
			
			for($FinalZipList->rewind(); $FinalZipList->valid() && $go == 1; $FinalZipList->next()){
				
				if($MeanIncomeList->current() == $FinalZipList->current()){
					$newValue = $FinalRank->current() + $MeanIncomeRank->current();
					$FinalRank->add($count, $newValue);
					$go = 0;
				}
				
				$count++;
				$FinalRank->next();
			}
			
			$MeanIncomeRank->next();
		}
	} elseif($average_income == '-1') {
	} else {
		echo '<script>alert("Your preference for house value returned no results. Results will not be affected by this option.")</script>';
	}
	
	$FinalRank->rewind();
	$FinalZipList->rewind();
	$finalZip = $FinalZipList->current();
	$finalValue = $FinalRank->current();
	for($FinalRank->rewind(); $FinalRank->valid(); $FinalRank->next()){
		
		if($finalValue > $FinalRank->current() && $FinalRank->current() != 0 && $FinalZipList->current() != ""){
			$finalZip = $FinalZipList->current();
			$finalValue = $FinalRank->current();
		}
		
		$FinalZipList->next();
	}
	
	$finalCity;
                
                
                
	$sql="SELECT City FROM Hinter.City_Zip_County_Key WHERE Zip_Code = ". $finalZip;

	$result =  mysqli_query($conn, $sql);
	while($row = mysqli_fetch_assoc($result)) {
		$finalCity = $row["City"];
	}
	
	
?>



<?php
  mysqli_free_result($result);
  mysqli_close($conn);

?>
</header>
 
        <section class="submit-query">
            <div class="row submit-body">
                <form method="post" action="results.php"class="query-form" id="home_pref">
                        <div class="preferences-box col span-1-of-5">
                            <div class="query-title">
                                <h6>Home Preferences</h6><br>
                            </div>
                            <label>Your City</label>
                            <select name="city">
								<option value="<?php echo $finalCity?>" selected><?php echo $finalCity?></option>
                            </select>
                            <label>Your County</label>
                            <select name="county">
								<option value="<?php echo $counties?>" selected><?php echo $counties?></option>
                            </select>
                            <br>

                            <label for="price">House Price</label>
                            <select name="price">
                                <option value="-1" >No Preference</option>
                                <option value="100000" > less than $100K</option>
                                <option value="250000" >$100K - $250K</option>
                                <option value="500000" selected>$250K - $500K</option>
                                <option value="750000" >$500K - $750K</option>
                                <option value="1000000" >$750K - $1M</option>
                                <option value="60000000" >$1M plus</option>
                            </select>
                            <br>

                            <label for="bed">Bedrooms</label>
                            <select class="bed" name="bed" id="bed">
                                <option value="100" >No Preference</option>
                                <option value="1" >1</option>
                                <option value="2" >2</option>
                                <option value="3" selected>3</option>
                                <option value="4" >4</option>
                                <option value="5" >5</option>
                                <option value="6" >6 </option>
                                <option value="100" >7 plus </option>
                            </select>
                            <br>
                            <br>

                            <label for="bath">Bathrooms</label>
                            <select class="bath" name="bath" id="bath">
                                <option value="100" >No Preference</option>
                                <option value="1" >1</option>
                                <option value="2" >2</option>
                                <option value="3" selected>3</option>
                                <option value="4" >4</option>
                                <option value="5" >5</option>
                                <option value="6" >6 </option>
                                <option value="100" >7 plus </option>
                            </select>
                            <br>
                            <br>

                            <input id="Search" type="submit" value="Search!">
                        </div>
                    </form>
                
                <div class="preferences-box col span-4-of-5">
                    <h1 align="center">Based on your preferences your ideal City is <?php echo $finalCity ?></h1>
					<br>
					<br>
					<div id="map-canvas"></div>
                </div>
            </div>
        </section>
		
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFclVV6QDJlGXKM4CZ1FCRJwbAE9jrXjg"></script>
		
		<script>
			function init() {
				
				// Creates the map options
				var mapOptions = {
					zoom: 8,
					center: new google.maps.LatLng(40.0583, -74.4057)
				}
				
				// Create the initial map
				var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
				
				var geocoder = new google.maps.Geocoder();
				
				var lat, lon;
				var address = "<?php echo $finalCity ?>" + ", NJ";
				
				geocoder.geocode( { 'address': address}, function(results, status) {
				  if (status == google.maps.GeocoderStatus.OK)
				  {
					
					var myLatLng = {lat: results[0].geometry.location.lat(), lng:  results[0].geometry.location.lng()};
					
					var marker = new google.maps.Marker({
						position: myLatLng,
						map: map,
						title: "<?php echo $finalCity ?>",
						animation: google.maps.Animation.DROP
					});
					map.setCenter(myLatLng);
					map.setZoom(11);
					
				  }
				});
			}
			
			init();
		</script>

</body>
</html>

