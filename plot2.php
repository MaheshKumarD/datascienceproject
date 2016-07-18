<!DOCTYPE html>
<html>
<head>
	<title>::  Consumer Complaint Plots ::</title>
	<meta charset="utf-8" />
    <meta name="Description" content="" />
    <meta name="keywords" content="" />

	<link rel="stylesheet" href="css/style.css" media="screen" />
	<script src="js/canvasjs.min.js"></script>
</head>

<body style="margin:0px;background:url('..') 50% 30% no-repeat;background-size:100%; background-color:#000;">
	<?php
		$connection = OCILogon("mdama2", "oracle", "//oracle.uis.edu:1521/oracle.uis.edu:dedicated/oracle" );
		//echo $connection;
		if(!$connection){
			$con = "Couldn't make a connection";
			exit;
		}
		else { $con = "You have connected to the UIS Oracle Database!!" ;}

		$data1Value = array();
		$data2Value = array();
		$compValue = array();

		$sqlquery = "SELECT PRODUCT, YEAR_RECEIVED, COUNT(COMPLAINT_ID)FROM CUSTOMERCOMPLAINT GROUP BY PRODUCT, YEAR_RECEIVED ORDER BY PRODUCT, YEAR_RECEIVED";
		$query = "No. of Players in Each League";

		$sql_id = ociparse($connection, $sqlquery);
		if (!sql_id){
			$e = oci_error($connection);
			print htmlentities($e['message']);
			exit;
		}
		ociexecute($sql_id,OCI_DEFAULT);
	
		//$nrows = oci_fetch_all($sql_id, $res);
		$nrows = oci_fetch_all($sql_id, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW);

		$fldNum = oci_num_fields($sql_id);
		$companyName = "-";
		foreach ($res as $col) {
			$i = 1;
			foreach ($col as $item) {			
				if($i == 1) { 
					if($companyName != htmlentities($item, ENT_QUOTES)) { 
						array_push($compValue, htmlentities($item, ENT_QUOTES));
						if($data1 != "") { array_push($data1Value, $data1); }
						if($data2!="") { array_push($data2Value, $data2); }
						$data1 = "";
						$data2 = "";
						}
						$companyName = htmlentities($item, ENT_QUOTES);
				 }
				else if ($i == 2)
				{
					if ($data1 == ""){ $data1 .= htmlentities($item, ENT_QUOTES); } else { $data1 .= "##". htmlentities($item, ENT_QUOTES); }
				}
				else
				{
					if ($data2 == ""){ $data2 .= htmlentities($item, ENT_QUOTES); } else { $data2 .= "##". htmlentities($item, ENT_QUOTES); }
				}
				$i =$i + 1;
			}
		}

		array_push($data1Value, $data1);
		array_push($data2Value, $data2);

		oci_free_statement($sql_id);

		OCILogoff($connection);

		for ($l=0 ; $l < count($compValue) ;$l++)
			{
				$d1res = explode ( "##" , $data1Value[$l] );
				$d2res = explode ( "##" , $data2Value[$l] );				
				
				$result .= 	'{
					type: "line",
					lineThickness: 3,
					axisYType: "secondary",
					 xValueType: "dateTime",
					showInLegend: true,
					name: "'. $compValue[$l] .'",
					dataPoints: [';
					for ($m=0; $m < count($d1res) ; $m++)
					{			
						if ($m == count(d1res)-2) { $result .= 	'{ x: new Date(' . $d1res[$m] .',00), y: ' . $d2res[$m] . '}'; }
						else { $result .= 	'{ x: new Date(' . $d1res[$m] .',00), y: ' . $d2res[$m] . '},'; }
					}
					$result .= 	']';
					if ($l == count($compValue)-1) { $result .= 	'}'; } else { $result .= 	'},'; }
			}
	?>

	<script type="text/javascript">
		window.onload = function () {

			var chart = new CanvasJS.Chart("chartContainer4", {
				zoomEnabled: false,
				animationEnabled: true,
				title: {
					text: "No. of complaints per year per product"
				},
				axisY2: {
					valueFormatString: "0",

					maximum: 50000,
					interval: 5000,
					interlacedColor: "#F5F5F5",
					gridColor: "#D7D7D7",
					tickColor: "#D7D7D7"
				},
				theme: "theme4",
				toolTip: {
					shared: true
				},
				legend: {
					verticalAlign: "bottom",
					horizontalAlign: "center",
					fontSize: 15,
					fontFamily: "Lucida Sans Unicode"
				},
				data: [ 
				
			<?php echo $result; ?>
				
				],
				legend: {
					cursor: "pointer",
					itemclick: function (e) {
						if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
							e.dataSeries.visible = false;
						}
						else {
							e.dataSeries.visible = true;
						}
						chart.render();
					}
				}
			});

			chart.render();
		}
	</script>
<form>
	<div class="header">
		<img id="idFanatic4" src="Images/download1.png" width="200">	
		<div> The Data Incubator Challenge </div> 
	</div>
	<br />

	<div class="content">
		<div class="welcome">
			<div class="welcome-txt">
				<table>
					<tr>
						<td style="padding-right:40px"> <a href="index.html" >Home</a> </td>
                        <td style="padding-right:40px"> <a href="plot1.php" class="apieChart">Plot 1</a> </td>
                        <td style="padding-right:40px"> <a href="javascript:show('barChart');" class="abarChart" style="color:#78bd00;">Plot 2</a> </td>
					</tr>
				</table>	
			</div>
            <div id="1Report" class="inside-txt" style="padding-top:10px;">
				<div id="chartContainer4" style="width: 70%; height: 400px;margin-left: auto ; margin-right: auto ;"></div>
   			 	<div style='margin-top:20px;'> </div>
                	<table> <tr height='60px'><td></td></tr> <tr><td></td></tr>	</table>
				</div>
		</div>
	</div>
		 
	<div class="footer">		
		<div> About Us <br /> This project helps the consumers to make smarter choices. The dataset has been retrieved from Consumer Complaint Database where complaints are received by the Consumer Financial Protection Bureau (CFPB) on financial products and services.</div>
	</div>
	
	</form>
</body>
</html>

