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
           <label for="JenisKamar" class="form-label">Jenis Kamar</label>
           <input type="text" class="form-control" id="JenisKamar" placeholder="Isikan Jenis Kamar">
        </div>
        <div class="mb-3">
           <label for="Rate" class="form-label">Rate</label>
           <input type="text" class="form-control" id="Rate" placeholder="Isikan Rate">
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
                <th>Jenis Kamar</th><th>Rate</th><th>Action</th>
            </tr>
        </thead>
</table>
</div>
<script>
var flag="none";

var table = $('#example').DataTable( {
    serverSide: true,
    ajax: {
        url: '?flag=show',
		type: 'POST'
    },
	columns: [
        { data: 'JenisKamar' }, { data: 'Rate' },
		{ "orderable": false, "data": null,"defaultContent":
				"<button type=\"button\" class=\"btn btn-warning btn-sm\" id=\"edit\">Edit</button>&nbsp;<button type=\"button\" class=\"btn btn-danger btn-sm\" id=\"delete\">Delete</button>"}
    ]
} );

$('#add').click(function() {
	flag="add";
	$('#myFormTitle').text("Add Data");
	$('#JenisKamar').val("");
	$('#JenisKamar').prop( "disabled", false );
	$('#Rate').val("");
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
	var formControl = document.getElementsByClassName("form-control");
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
    var JenisKamar = row[0];
	readFromServer({"JenisKamar":JenisKamar}, function(data) {
		flag="edit";
		$('#myFormTitle').text("Edit Data");
		$('#JenisKamar').val(data["JenisKamar"]);
		$('#JenisKamar').prop( "disabled", true );		
		$('#Rate').val(data["Rate"]);
		$('#save').text("Save change");
		$('#feedback').text("");
		$('#myForm').modal('show');
	});
});

//klik pada button delete
table.on('click', '#delete', function (e) {
	//ambil data dari baris yang diklik
    var row = table.row(e.target.closest('tr')).data();
    var JenisKamar = row[0];
	readFromServer({"JenisKamar":JenisKamar}, function(data) {
		flag="delete";
		$('#myFormTitle').text("Hapus Data");
		$('#JenisKamar').val(data["JenisKamar"]);
		$('#JenisKamar').prop( "disabled", true );		
		$('#Rate').val(data["Rate"]);
		$('#save').text("Delete record");
		$('#feedback').text("");
		$('#myForm').modal('show');
	});
});
</script>
</body>
</html>
<?php
}
function checkForeignKey($con, $values) {
	$sql = "SELECT count(*) FROM room WHERE JenisKamar=:JenisKamar;";
	$ada = querySingleValue($con, $sql, $values);
	return $ada;
}

if (isset($_REQUEST["flag"])) {
	if ($_REQUEST["flag"]=="show") {
		$con = openConnection();

		//untuk hitung total baris
		$sqlCount = "SELECT count(*) FROM rate;";

		//untuk mengembalikan data
		$length = intval($_REQUEST["length"]);
		$start = intval($_REQUEST["start"]);
		$sqlData = "SELECT JenisKamar, Rate FROM rate WHERE JenisKamar LIKE :search LIMIT $length OFFSET $start";

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
			$sql = "INSERT into rate(JenisKamar, Rate) VALUES (:JenisKamar, :Rate);";		
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
			$sql = "SELECT JenisKamar, Rate FROM rate WHERE JenisKamar=:JenisKamar;";		
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
			$sql = "UPDATE rate SET Rate = :Rate WHERE JenisKamar=:JenisKamar;";		
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
			if (checkForeignKey($con, array("JenisKamar"=>$data["JenisKamar"]))) {
				$response["status"]=0; //gagal
				$response["message"]="JenisKamar sudah digunakan!";
				$response["data"]=$data;
			}
			else {
				$sql = "DELETE FROM rate WHERE JenisKamar=:JenisKamar;";		
				deleteRow($con, $sql, array("JenisKamar"=>$data['JenisKamar']));
				$response["status"]=1; //berhasil
				$response["message"]="Ok";
				$response["data"]=$data;
			}
		}
		catch(Exception $e) {
			$response["status"]=0; //gagal
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
