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
        <h5 class="modal-title">Laporan Kamar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
           <label for="DariRoomId" class="form-label">Dari Room Id</label>
           <input type="text" class="form-control" id="DariRoomId" placeholder="Isikan Dari Room Id" value="0">
        </div>
        <div class="mb-3">
           <label for="SampaiRoomId" class="form-label">Sampai Room Id</label>
           <input type="text" class="form-control" id="SampaiRoomId" placeholder="Isikan Sampai Room Id" value="Z">
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
	private $jumlahKamar=0;
	
	function onFirstPage() {
		echo "<h1>Laporan Daftar Kamar</h1>";
	}
	
	function onHeader($row) {
	$header = <<<EOD
<table class="table table-striped table-hover">
<thead>
    <tr>
      <th scope="col">RoomId</th>
      <th scope="col">Lantai</th>
	  <th scope="col">Status</th>
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
	
	function getGroupNo($lastRow, $row, $totalGroup) {
		if ($lastRow==null)
			return $totalGroup;
		else if ($lastRow[3]!=$row[3]) {			
			return 1;
		}
		else
			return 0;
	}
	
	function onGroupHeader($row, $groupNo) {
		$this->jumlahKamar=0;
		echo "<tr><td colspan='3'><b>Begin of Jenis Kamar: $row[3]</b></td></tr>";
	}

	function onDetail($row) {
		$this->jumlahKamar++;
		echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td></tr>";
	}
	
	function onGroupFooter($lastRow, $row, $groupNo) {
		echo "<tr><td colspan='3' align='right'>Ada $this->jumlahKamar Kamar $lastRow[3]</td></tr>";
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
		$sql = "Select RoomId, Lantai, Case When Status=0 Then 'Aktif' else 'Non-Aktif' end as Status, JenisKamar From room Where RoomId>=:DariRoomId AND RoomId<=:SampaiRoomId Order By JenisKamar;";
		$myReport = new MyReport($con, $sql);
		$myReport->query($param, 1); //totalGroup=1
	}	
}
else {
	showUI();
}
?>
