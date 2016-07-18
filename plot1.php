<!DOCTYPE html>
<html>
<head>
	<title>:: Consumer Complaint Plots ::</title>
	<meta charset="utf-8" />
    <meta name="Description" content="" />
    <meta name="keywords" content="" />

	<link rel="stylesheet" href="css/style.css" media="screen" />
	<script src="js/canvasjs.min.js"></script>
</head>

<body style="margin:0px;background:url('..') 50% 30% no-repeat;background-size:100%; background-color:#000;">
	<?php
		$connection = OCILogon("mdama2", "oracle", "//oracle.uis.edu:1521/oracle.uis.edu:dedicated/oracle" );

		if(!$connection){
			$con = "Couldn't make a connection";
			exit;
		}
		else { $con = "You have connected to the UIS Oracle Database!!" ;}
		
		$data1Value = array();
		$data2Value = array();
		$compValue = array();

		$sqlquery = "SELECT PRODUCT, COMPANY_RESPONSE_TO_CONSUMER, COUNT(COMPLAINT_ID)FROM CUSTOMERCOMPLAINT GROUP BY PRODUCT, COMPANY_RESPONSE_TO_CONSUMER ORDER BY PRODUCT, COMPANY_RESPONSE_TO_CONSUMER";
//		$sqlquery = "SELECT COMPANY_RESPONSE_TO_CONSUMER, PRODUCT, COUNT(COMPLAINT_ID)FROM CUSTOMERCOMPLAINT GROUP BY COMPANY_RESPONSE_TO_CONSUMER, PRODUCT ORDER BY COMPANY_RESPONSE_TO_CONSUMER, PRODUCT";

		$sql_id = ociparse($connection, $sqlquery);
		if (!sql_id){
			$e = oci_error($connection);
			print htmlentities($e['message']);
			exit;
		}
		ociexecute($sql_id,OCI_DEFAULT);
	
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
					type: "stackedBar100",
					showInLegend: true,
					name: "'. $compValue[$l] .'",
					indexLabel: "{}",
					dataPoints: [';
					for ($m=0; $m < count($d1res) ; $m++)
					{
						if ($m == count(d1res)-2) { $result .= 	'{ y: '.$d2res[$m].', label: "'.$d1res[$m].'" }'; }
						else { $result .= 	'{ y: '.$d2res[$m].', label: "'.$d1res[$m].'" },'; }
					}
					$result .= 	']';
					if ($l == count($compValue)-1) { $result .= 	'}'; } else { $result .= 	'},'; }
			}

	?>
	<script type="text/javascript">
		window.onload = function () {
			var chart = new CanvasJS.Chart("chartContainer",
			{
				theme: 'theme4',
				title: {
					text: "Product vs Company Response to consumer complaints"
				},
				animationEnabled: true,
				axisY: {
					title: "percent"
				},
				legend: {
					horizontalAlign: 'center',
					verticalAlign: 'bottom'
				},
				toolTip: {
					shared: true
				},
				data: [
					<? echo $result; ?>
				]
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
                        <td style="padding-right:40px"> <a href="javascript:show('pieChart');" class="apieChart" style="color:#78bd00;">Plot 1</a> </td>                        
                        <td style="padding-right:40px"> <a href="plot2.php" class="abarChart">Plot 2</a> </td>
					</tr>
				</table>	
			</div>
            <div id="1Report" class="inside-txt" style="padding-top:10px;">
				<div id="chartContainer" style="height: 400px; width: 100%;">
				</div>
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

