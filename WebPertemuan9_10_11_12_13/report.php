<?php
include("library.php");
include("reportLib.php");

function showUI() {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>DataTables Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <link href="bootstrap-5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="bootstrap-5.3.2/dist/js/bootstrap.bundle.min.js"></script>	
  <script src="jquery/jquery-3.7.1.min.js"></script>  
</head>
<body>

<!-- Modal -->
<div class="modal" id="myForm" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Laporan Harga Kamar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
           <label for="DariJenisKamar" class="form-label">Dari Jenis Kamar</label>
           <input type="text" class="form-control" id="DariJenisKamar" placeholder="Isikan Jenis Kamar" value="0">
        </div>
        <div class="mb-3">
           <label for="SampaiJenisKamar" class="form-label">Sampai Jenis Kamar</label>
           <input type="text" class="form-control" id="SampaiJenisKamar" placeholder="Isikan Jenis Kamar" value="Z">
        </div>
      </div>
      <div class="modal-footer">
        <button id="report" type="button" class="btn btn-primary">Report</button>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
<div id="paper"></div>
</div>
<script>
function loadReport(obj, callBack) {
	$.post("?flag=report",
		JSON.stringify(obj), 
	    	function(data,status) {
				$("#paper").html(data);
				$('#myForm').modal('hide');	
		}
	);
}

$('#report').click(function() {
	var formControl = document.getElementsByClassName("form-control");
	var data = {};
	for (var i=0;i<formControl.length;i++) {
		data[formControl[i].id] = formControl[i].value;
	}	
	loadReport(data, function() {
	});
});

$(document).ready(function(){
	$('#myForm').modal('show');	
}); 
</script>
</body>
</html>
<?php
}

class MyReport extends ReportGen {
	function onFirstPage() {
		echo "<h1>Laporan Tarif Kamar</h1>";
	}
	
	function onHeader($row) {
	$header = <<<EOD
<table class="table table-striped table-hover">
<thead>
    <tr>
      <th scope="col">Jenis Kamar</th>
      <th scope="col">Harga</th>
    </tr>
</thead>
<tbody>
EOD;
	echo $header;
	}	
	
function onFooter($row) {
	$footer = <<<EOD
</tbody>
</table>
EOD;
	echo $footer;
	}

	function onDetail($row) {
		echo "<tr><td>$row[0]</td><td align='right'>$row[1]</td></tr>";
	}
	
	function onNoRecord() {
		echo "Tidak ada record yang memenuhi!";
	}
	
	function onLastPage() {
		echo "Printed at " . date("Y-m-d h:i:s");
	}
}

if (isset($_REQUEST["flag"])) {
	if ($_REQUEST["flag"]=="report") {
		$body = file_get_contents('php://input');
		$param = json_decode($body, true);		
		$con = openConnection();
		$sql = "Select JenisKamar, Rate From rate Where JenisKamar>=:DariJenisKamar AND JenisKamar<=:SampaiJenisKamar;";
		$myReport = new MyReport($con, $sql);
		$myReport->query($param);
	}	
}
else {
	showUI();
}
?>
