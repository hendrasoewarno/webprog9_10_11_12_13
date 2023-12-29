<?php
include("library.php");

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
  <link href="DataTables/datatables.min.css" rel="stylesheet"> 
  <script src="DataTables/datatables.min.js"></script>
</head>
<body>

<!-- Modal -->
<div class="modal" id="myForm" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myFormTitle">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
	  <input type="hidden" class="form-control" id="OccupiedId" name="input-element" value="0">
      <div class="modal-body">
        <div class="mb-3">
           <label for="RoomId" class="form-label">RoomId</label>
		   <select class="form-select" id="RoomId" name="input-element">
           </select>           
        </div>
        <div class="mb-3">
           <label for="GuestId" class="form-label">GuestId</label>
           <select class="form-select" id="GuestId" name="input-element">
           </select>
        </div>		
        <div class="mb-3">
           <label for="VoucherId" class="form-label">VoucherId</label>
           <input type="text" class="form-control" id="VoucherId" placeholder="Isikan VoucherId" name="input-element">
        </div>
		<div class="mb-3">
		   <label for="DariTanggal" class="form-label">Dari Tanggal</label>
		   <input type="date" class="form-control" id="DariTanggal" placeholder="Isikan Dari Tanggal" name="input-element">
           </select>
        </div>
		<div class="mb-3">
		   <label for="DariTanggal" class="form-label">Sampai Tanggal</label>
		   <input type="date" class="form-control" id="SampaiTanggal" placeholder="Isikan Sampai Tanggal" name="input-element">
           </select>
        </div>
		<div class="mb-3">
		   <label for="Rate" class="form-label">Rate</label>
		   <input type="number" class="form-control" id="Rate" placeholder="Isikan Rate" name="input-element">
           </select>
        </div>						
		<div class="mb-3">
		   <label for="GroupId" class="form-label">GroupId</label>
		   <input type="text" class="form-control" id="GroupId" placeholder="Isikan Group Id" name="input-element">
           </select>
        </div>
      </div>		
      <div class="modal-footer">
		<p id="feedback"><p>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="save" type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
<p>
<button id="add" type="button" class="btn btn-primary btn-sm">Add</button>
</p>
<table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>RoomId</th><th>GuestId</th><th>Nama</th><th>VoucherId</th><th>DariTanggal</th><th>SampaiTanggal</th><th>CheckInTime</th><th>CheckOutTime</th><th>Rate</th><th>GroupId</th><th>OccupiedId</th><th>Action</th>
            </tr>
        </thead>
</table>
</div>
<script>
$(document).ready(function () {
	$.get("SelectOptions.php?flag=RoomId",
	    	function(data,status) {	
				for (i=0; i<data.length; i++) {
					$('#RoomId').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
				}	
		}
	);
	$.get("SelectOptions.php?flag=GuestId",
	    	function(data,status) {	
				for (i=0; i<data.length; i++) {
					$('#GuestId').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
				}	
		}
	);
});
</script>
<script>
var flag="none";

var table = $('#example').DataTable( {
    serverSide: true,
    ajax: {
        url: '?flag=show',
		type: 'POST'
    },
	columns: [
        { data: 'RoomId' }, { data: 'GuestId' },  { data: 'Nama' },  { data: 'VoucherId' }, { data: 'DariTanggal' },  { data: 'SampaiTanggal' }, { data: 'CheckInTime' }, { data: 'CheckOutTime' }, { data: 'Rate' }, { data: 'GroupId' }, { data: 'OccupiedId' },
		{ "orderable": false, "data": null,"defaultContent":
				"<button type=\"button\" class=\"btn btn-warning btn-sm\" id=\"edit\">Edit</button>&nbsp;<button type=\"button\" class=\"btn btn-danger btn-sm\" id=\"delete\">Delete</button>&nbsp;<button type=\"button\" class=\"btn btn-dark btn-sm\" id=\"checkin\">Check-In</button>&nbsp;<button type=\"button\" class=\"btn btn-dark btn-sm\" id=\"checkout\">Check-Out</button>"}
    ]
} );

$('#add').click(function() {
	flag="add";
	$('#myFormTitle').text("Add Data");
	$('#RoomId').val("");
	$('#GuestId').val("");
	$('#VoucherId').val("");
	$('#DariTanggal').val(new Date().toISOString().slice(0, 10));
	$('#SampaiTanggal').val("");
	$('#Rate').val("0");
	$('#GroupId').val("");
	$('#save').text("Save change");
	$('#feedback').text("");
	$('#myForm').modal('show');
});

function postToServer(obj, callBack) {
	$.post("?flag=" + flag,
		JSON.stringify(obj), 
	    	function(data,status) {
				if (data["status"]==1) {
					callBack();
				}
				else {
					$("#feedback").text(data["message"]);
				}
		}
	);
}

//klik pada button save
$('#save').click(function() {
	var formControl = document.getElementsByName("input-element");
	var data = {};
	for (var i=0;i<formControl.length;i++) {
		data[formControl[i].id] = formControl[i].value;
	}
	
	postToServer(data, function() {
		$('#myForm').modal('hide');	
		table.ajax.reload();
	});
});

function readFromServer(obj, callBack) {
	$.post("?flag=read",
		JSON.stringify(obj), 
	    	function(data,status) {
				if (data["status"]==1) {
					callBack(data["data"]);
				}
				else {
					$("#feedback").text(data["message"]);
				}
		}
	);
}

//klik pada button edit
table.on('click', '#edit', function (e) {
	//ambil data dari baris yang diklik
    var row = table.row(e.target.closest('tr')).data();
    var OccupiedId = row['OccupiedId'];
	readFromServer({"OccupiedId":OccupiedId}, function(data) {
		flag="edit";
		$('#myFormTitle').text("Edit Data");
		$('#OccupiedId').val(data["OccupiedId"]);
		$('#RoomId').val(data["RoomId"]);
		$('#GuestId').val(data["GuestId"]);
		$('#VoucherId').val(data["VoucherId"]);
		$('#DariTanggal').val(data["DariTanggal"]);
		$('#SampaiTanggal').val(data["SampaiTanggal"]);
		$('#Rate').val(data["Rate"]);
		$('#GroupId').val(data["GroupId"]);
		$('#save').text("Save change");
		$('#feedback').text("");
		$('#myForm').modal('show');
	});
});

//klik pada button delete
table.on('click', '#delete', function (e) {
	//ambil data dari baris yang diklik
var row = table.row(e.target.closest('tr')).data();
    var OccupiedId = row['OccupiedId'];
	readFromServer({"OccupiedId":OccupiedId}, function(data) {
		flag="delete";
		$('#myFormTitle').text("Edit Data");
		$('#OccupiedId').val(data["OccupiedId"]);
		$('#RoomId').val(data["RoomId"]);
		$('#GuestId').val(data["GuestId"]);
		$('#VoucherId').val(data["VoucherId"]);
		$('#DariTanggal').val(data["DariTanggal"]);
		$('#SampaiTanggal').val(data["SampaiTanggal"]);
		$('#Rate').val(data["Rate"]);
		$('#GroupId').val(data["GroupId"]);
		$('#save').text("Delete record");
		$('#feedback').text("");
		$('#myForm').modal('show');
	});	
});

//klik pada button checkin
table.on('click', '#checkin', function (e) {
	//ambil data dari baris yang diklik
    var row = table.row(e.target.closest('tr')).data();
    var OccupiedId = row['OccupiedId'];
	var obj = {"OccupiedId":OccupiedId};
	$.post("?flag=checkin", JSON.stringify(obj), 
	    function(data,status) {
			if (data["status"]==1) {
				table.ajax.reload();
			}
		}
	);
});

//klik pada button checkin
table.on('click', '#checkout', function (e) {
	//ambil data dari baris yang diklik
    var row = table.row(e.target.closest('tr')).data();
    var OccupiedId = row['OccupiedId'];
	var obj = {"OccupiedId":OccupiedId};
	$.post("?flag=checkout", JSON.stringify(obj), 
	    function(data,status) {
			if (data["status"]==1) {
				table.ajax.reload();
			}
		}
	);
});
</script>
</body>
</html>
<?php
}
if (isset($_REQUEST["flag"])) {
	if ($_REQUEST["flag"]=="show") {
		$con = openConnection();

		//untuk hitung total baris
		$sqlCount = "SELECT count(*) FROM occupied a WHERE a.CheckInTime is Not Null and a.CheckOutTime is Null;";

		//untuk mengembalikan data
		$length = intval($_REQUEST["length"]);
		$start = intval($_REQUEST["start"]);
		//Data yang belum checkout atau checkouttime - checkintime < 1 jam
		$sqlData = "SELECT a.RoomId, a.GuestId, b.Nama, a.VoucherId, a.DariTanggal, a.SampaiTanggal, a.CheckInTime, a.CheckOutTime, a.Rate, a.GroupId, a.OccupiedId FROM occupied a Inner Join Guest b On a.GuestId=b.GuestId WHERE (RoomId LIKE :search or Nama Like :search) and (a.CheckOutTime is Null or HOUR(TIMEDIFF(now(), a.CheckOutTime)) < 1) LIMIT $length OFFSET $start";
		$data = array();
		$data["draw"]=intval($_REQUEST["draw"]);
		$data["recordsTotal"]=querySingleValue($con, $sqlCount, array());
		$param = array("search"=>$_REQUEST["search"]["value"]."%");
		$data["data"]=queryArrayRowsValues($con, $sqlData, $param);
		$data["recordsFiltered"]=sizeof($data["data"]);
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($data);
	}
	else if($_REQUEST["flag"]=="add") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);		
			$sql = "INSERT into occupied(RoomId, GuestId, VoucherId, DariTanggal, SampaiTanggal, Rate, GroupId, OccupiedId) VALUES (:RoomId, :GuestId, :VoucherId, :DariTanggal, :SampaiTanggal, :Rate, Case When :GroupId='' Then :GuestId else :GroupId end, :OccupiedId);";		
			createRow($con, $sql, $data);
			$response["status"]=1;
			$response["message"]="Ok";
			$response["data"]=$data;
		}
		catch(Exception $e) {
			$response["status"]=0;
			$response["message"]=$e->getMessage();
			$response["data"]=null;						
		}
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	}
	else if($_REQUEST["flag"]=="read") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$param = json_decode($body, true);		
			$sql = "SELECT a.RoomId, a.GuestId, b.Nama, a.VoucherId, a.DariTanggal, a.SampaiTanggal, a.Rate, a.GroupId, a.OccupiedId FROM occupied a Inner Join Guest b On a.GuestId=b.GuestId WHERE OccupiedId=:OccupiedId;";		
			$data = queryArrayValue($con, $sql, $param);
			$response["status"]=1;
			$response["message"]="Ok";
			$response["data"]=$data;
		}
		catch(Exception $e) {
			$response["status"]=0;
			$response["message"]=$e->getMessage();
			$response["data"]=null;						
		}
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	}
	else if($_REQUEST["flag"]=="edit") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);		
			$sql = "UPDATE occupied SET RoomId=:RoomId, GuestId=:GuestId, VoucherId=:VoucherId, DariTanggal=:DariTanggal, SampaiTanggal=:SampaiTanggal, Rate=:Rate, GroupId=:GroupId WHERE OccupiedId=:OccupiedId and CheckOutTime is Null;";		
			updateRow($con, $sql, $data);
			$response["status"]=1;
			$response["message"]="Ok";
			$response["data"]=$data;
		}
		catch(Exception $e) {
			$response["status"]=0;
			$response["message"]=$e->getMessage();
			$response["data"]=null;						
		}
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	}
	else if($_REQUEST["flag"]=="delete") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);		
			$sql = "DELETE FROM occupied WHERE OccupiedId=:OccupiedId and CheckInTime is Null;";
			deleteRow($con, $sql, array("OccupiedId"=>$data['OccupiedId']));
			$response["status"]=1;
			$response["message"]="Ok";
			$response["data"]=$data;
		}
		catch(Exception $e) {
			$response["status"]=0;
			$response["message"]=$e->getMessage();
			$response["data"]=null;						
		}
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	}	
	else if($_REQUEST["flag"]=="checkin") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);		
			$sql = "UPDATE Occupied SET CheckInTime=now() WHERE OccupiedId=:OccupiedId and CheckInTime is Null;";
			updateRow($con, $sql, array("OccupiedId"=>$data['OccupiedId']));
			$response["status"]=1;
			$response["message"]="Ok";
			$response["data"]=$data;
		}
		catch(Exception $e) {
			$response["status"]=0;
			$response["message"]=$e->getMessage();
			$response["data"]=null;						
		}
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	}
	else if($_REQUEST["flag"]=="checkout") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);		
			$sql = "UPDATE Occupied SET CheckOutTime=now() WHERE CheckInTime is not Null and OccupiedId=:OccupiedId;";		
			updateRow($con, $sql, array("OccupiedId"=>$data['OccupiedId']));
			$response["status"]=1;
			$response["message"]="Ok";
			$response["data"]=$data;
		}
		catch(Exception $e) {
			$response["status"]=0;
			$response["message"]=$e->getMessage();
			$response["data"]=null;						
		}
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($response);
	}		
}
else {
	showUI();
}
?>
