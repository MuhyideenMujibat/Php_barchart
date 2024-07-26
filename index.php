<?php

$link = mysqli_connect("localhost","root","");
mysqli_select_db($link,"chart_db");
$test=array();
$count=0;

$result=mysqli_query($link,"select * from chart_tb");
while($row=mysqli_fetch_array($result))
{
	$test[$count]["label"]=$row["Days"];
	$test[$count]["y"]=$row["Hours"];
	$count=$count+1;
}
?>
<!DOCTYPE HTML>
<html>
<head>
<script>
window.onload = function() {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	title:{
		text:"Working Hours"
	},
	axisY: {
		title: "Time",
		includeZero: true,
		// prefix: "$",
		suffix:  "h"
	},
	axisX: {
		title: "Days",
	},
	data: [{
		type: "bar",
		yValueFormatString: "#0.##h",
		indexLabel: "{y}",
		indexLabelPlacement: "inside",
		indexLabelFontWeight: "bolder",
		indexLabelFontColor: "white",
		dataPoints: <?php echo json_encode($test, JSON_NUMERIC_CHECK); ?>
	}]

});
chart.render();
 
}
</script>
<?php

// SQL query to calculate total hours and minutes
$sql = "
    SELECT 
        SUM(FLOOR(hours)) AS total_hours,
        SUM((hours - FLOOR(hours)) * 100) AS total_minutes
    FROM 
        chart_tb
";

// Execute the query
$results = $link->query($sql);

// Check if the query was successful
if ($results) {
    // Fetch the result
    $row = $results->fetch_assoc();
    $total_hours = (int) $row['total_hours'];
    $total_minutes = (int) $row['total_minutes'];

    // Convert total minutes into hours and minutes
    $additional_hours = intdiv($total_minutes, 60);
    $total_minutes = $total_minutes % 60;
    $total_hours += $additional_hours;

    echo "<h2>Total Worked Hours: $total_hours hours $total_minutes minutes</h2>";
} else {
    // Display any errors if the query fails
    echo "Error: " . htmlspecialchars($link->error);
}
?>



</head>
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</body>
</html> 
