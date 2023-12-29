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
      <div class="modal-body">
        <div class="mb-3">
           <label for="RoomId" class="form-label">RoomId</label>
           <input type="text" class="form-control" id="RoomId" placeholder="Isikan Nomor Kamar" name="input-element">
        </div>
        <div class="mb-3">
           <label for="Lantai" class="form-label">Lantai</label>
           <input type="number" min="1" max="10" class="form-control" id="Lantai" placeholder="Isikan Nomor Lantai" name="input-element">
        </div>
		<div class="mb-3">
		   <label for="JenisKamar" class="form-label">JenisKamar</label>
		   <select class="form-select" id="JenisKamar" name="input-element">
           </select>
        </div>
		<div class="mb-3">
		   <input class="form-check-input" type="checkbox" value="" id="Status" name="input-element">
		   <label for="status" class="form-check-label">
				Aktif
		   </label>
        </div>
		<div class="mb-3">
		   <label for="Keterangan" class="form-label">Keterangan</label>
		   <textarea class="form-control" id="Keterangan" rows="3" name="input-element"></textarea>
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
                <th>RoomId</th><th>Lantai</th><th>JenisKamar</th><th>Status</th><th>ReadyTime</th><th>Keterangan</th><th>Action</th>
            </tr>
        </thead>
</table>
</div>
<script>
$(document).ready(function () {
	$.get("SelectOptions.php?flag=JenisKamar",
	    	function(data,status) {	
				for (i=0; i<data.length; i++) {
					$('#JenisKamar').append('<option value="' + data[i].key + '">' + data[i].value + '</option>');
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
        { data: 'RoomId' }, { data: 'Lantai' },  { data: 'JenisKamar' },  { data: 'Status' }, { data: 'ReadyTime' },  { data: 'Keterangan' },
		{ "orderable": false, "data": null,"defaultContent":
				"<button type=\"button\" class=\"btn btn-warning btn-sm\" id=\"edit\">Edit</button>&nbsp;<button type=\"button\" class=\"btn btn-danger btn-sm\" id=\"delete\">Delete</button>&nbsp;<button type=\"button\" class=\"btn btn-dark btn-sm\" id=\"ready\">Ready</button>"}
    ]
} );

$('#add').click(function() {
	flag="add";
	$('#myFormTitle').text("Add Data");
	$('#RoomId').val("");
	$('#RoomId').prop( "disabled", false );
	$('#Lantai').val("");
	$('#JenisKamar').val("");
	$('#Status').val("0");
	$('#Keterangan').val("");
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
    var RoomId = row[0];
	readFromServer({"RoomId":RoomId}, function(data) {
		flag="edit";
		$('#myFormTitle').text("Edit Data");
		$('#RoomId').val(data["RoomId"]);
		$('#RoomId').prop( "disabled", true );
		$('#Lantai').val(data["Lantai"]);
		$('#JenisKamar').val(data["JenisKamar"]);
		$('#Status').val(data["Status"]);
		$('#Keterangan').val(data["Keterangan"]);		
		$('#save').text("Save change");
		$('#feedback').text("");
		$('#myForm').modal('show');
	});
});

//klik pada button delete
table.on('click', '#delete', function (e) {
	//ambil data dari baris yang diklik
    var row = table.row(e.target.closest('tr')).data();
    var RoomId = row[0];
	readFromServer({"RoomId":RoomId}, function(data) {
		flag="delete";
		$('#myFormTitle').text("Hapus Data");
		$('#RoomId').val(data["RoomId"]);
		$('#RoomId').prop( "disabled", true );
		$('#Lantai').val(data["Lantai"]);
		$('#JenisKamar').val(data["JenisKamar"]);
		$('#Status').val(data["Status"]);
		$('#Keterangan').val(data["Keterangan"]);		
		$('#save').text("Delete record");
		$('#feedback').text("");
		$('#myForm').modal('show');
	});
});

//klik pada button ready
table.on('click', '#ready', function (e) {
	//ambil data dari baris yang diklik
    var row = table.row(e.target.closest('tr')).data();
    var RoomId = row[0];
	var obj = {"RoomId":RoomId};
	$.post("?flag=ready", JSON.stringify(obj), 
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
		$sqlCount = "SELECT count(*) FROM room;";

		//untuk mengembalikan data
		$length = intval($_REQUEST["length"]);
		$start = intval($_REQUEST["start"]);
		$sqlData = "SELECT RoomId, Lantai, JenisKamar, case when Status=1 Then 'Aktif' else 'Non-Aktif' end as Status, ReadyTime, Keterangan FROM room WHERE (RoomId LIKE :search or JenisKamar Like :search) LIMIT $length OFFSET $start";
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
			$sql = "INSERT into room(RoomId, Lantai, JenisKamar, Status, Keterangan) VALUES (:RoomId, :Lantai, :JenisKamar, :Status, :Keterangan);";		
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
			$sql = "SELECT RoomId, Lantai, JenisKamar, Status, Keterangan FROM room WHERE RoomId=:RoomId;";		
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
			$sql = "UPDATE room SET Lantai=:Lantai, JenisKamar=:JenisKamar, Status=:Status, Keterangan=:Keterangan WHERE RoomId=:RoomId;";		
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
			$sql = "DELETE FROM room WHERE RoomId=:RoomId;";		
			deleteRow($con, $sql, array("RoomId"=>$data['RoomId']));
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
	else if($_REQUEST["flag"]=="ready") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);		
			$sql = "UPDATE room SET ReadyTime=now() WHERE RoomId=:RoomId;";		
			updateRow($con, $sql, array("RoomId"=>$data['RoomId']));
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
