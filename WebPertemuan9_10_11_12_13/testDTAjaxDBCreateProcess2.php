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
	  <input type="hidden" class="form-control" id="PaymentId" name="input-element" value="0">
      <div class="modal-body">
        <div class="mb-3">
           <label for="GroupId" class="form-label">GroupId</label>
           <input type="text" class="form-control" id="GroupId" placeholder="Isikan GroupId" name="input-element">
        </div>
		<div class="mb-3">
		   <label for="Tanggal" class="form-label">Tanggal</label>
		   <input type="date" class="form-control" id="Tanggal" placeholder="Isikan Tanggal" name="input-element">
        </div>		
		<div class="mb-3">
		   <label for="VoucherId" class="form-label">VoucherId</label>
		   <input type="text" class="form-control" id="VoucherId" placeholder="Isikan VoucherId" name="input-element">
        </div>		
		<div class="mb-3">
		   <label for="Jenis" class="form-label">Jenis</label>
		   <select class="form-select" id="Jenis" name="input-element">
				<option value="Cash">Cash</option>
				<option value="CC">CreditCard</option>
				<option value="Partner">Partner</option>
           </select>
        </div>		
        <div class="mb-3">
           <label for="Amount" class="form-label">Amount</label>
           <input type="text" class="form-control" id="Amount" placeholder="Isikan Amount" name="input-element">
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
                <th>GroupId</th><th>Tanggal</th><th>VoucherId</th><th>Jenis</th><th>Amount</th><th>PaymentId</th><th>Action</th>
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
        { data: 'GroupId' }, { data: 'Tanggal' }, { data: 'VoucherId' }, { data: 'Jenis' }, { data: 'Amount' }, { data: 'PaymentId' },
		{ "orderable": false, "data": null,"defaultContent":
				"<button type=\"button\" class=\"btn btn-danger btn-sm\" id=\"delete\">Delete</button>"}
    ]
} );

$('#add').click(function() {
	flag="add";
	$('#myFormTitle').text("Add Data");
	$('#GroupId').val("");
	$('#Tanggal').val(new Date().toISOString().slice(0, 10));
	$('#VoucherId').val("")
	$('#Jenis').val("")	
	$('#Amount').val("")	
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

//klik pada button delete
table.on('click', '#delete', function (e) {
	//ambil data dari baris yang diklik
var row = table.row(e.target.closest('tr')).data();
    var PaymentId = row['PaymentId'];
	if (row['GroupId']=="" || row["VoucherId"]=="") {
		readFromServer({"PaymentId":PaymentId}, function(data) {
			flag="delete";
			$('#myFormTitle').text("Delete Data");
			$('#PaymentId').val(data["PaymentId"]);
			$('#ARId').val(data["ARId"]);
			$('#GroupId').val(data["GroupId"]);
			$('#Tanggal').val(data["Tanggal"]);
			$('#VoucherId').val(data["VoucherId"]);			
			$('#Jenis').val(data["Jenis"]);
			$('#Amount').val(data["Amount"]);
			$('#save').text("Delete Record");
			$('#feedback').text("");
			$('#myForm').modal('show');
		});	
	}
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
		$sqlCount = "SELECT count(*) FROM ar;";

		//untuk mengembalikan data
		$length = intval($_REQUEST["length"]);
		$start = intval($_REQUEST["start"]);
		$sqlData = "SELECT GroupId, Tanggal, VoucherId, Jenis, Amount, PaymentId FROM payment WHERE GroupId LIKE :search Or VoucherId LIKE :search LIMIT $length OFFSET $start";

		$data = array();
		$data["draw"]=intval($_REQUEST["draw"]);
		$data["recordsTotal"]=querySingleValue($con, $sqlCount, array());
		$param = array("search"=>$_REQUEST["search"]["value"]."%");
		$data["data"]=queryArrayRowsValues($con, $sqlData, $param);
		$data["recordsFiltered"]=sizeof($data["data"]);
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($data);
	}
	else if($_REQUEST["flag"]=="read") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$param = json_decode($body, true);		
			$sql = "SELECT GroupId, Tanggal, VoucherId, Jenis, Amount, PaymentId FROM payment WHERE PaymentId=:PaymentId;";		
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
	else if($_REQUEST["flag"]=="add") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);
			if ($data["GroupId"]=="" && $data["VoucherId"]=="") {
				throw new Exception("Tidak bisa GroupId dan VoucherId Kosong.");			
			}
			else {
				$ada = querySingleValue($con, "SELECT count(*) FROM ar WHERE GroupId=:GroupId;", array("GroupId"=>$data["GroupId"]));
				if ($ada == 0) {			
					throw new Exception("GroupId " . $data["GroupId"] . " tidak ditemukan atau belum Check-Out");			
				}
			}
			$con->BeginTransaction();
			$sql = "INSERT into payment(GroupId, Tanggal, VoucherId, Jenis, Amount, PaymentId) VALUES (:GroupId, :Tanggal, :VoucherId, :Jenis, :Amount, :PaymentId);";			
			if ($data["GroupId"]!="") {
				$PaymentId = $con->lastInsertId();
				$sqlAR = "INSERT into AR(GroupId, Tanggal, Jenis, Keterangan, Amount, PaymentId, ARId) Values (:GroupId, :Tanggal, 'Payment', 'Pembayaran AR', :Amount, :PaymentId, 0);";
				createRow($con, $sqlAR, array("GroupId"=>$data["GroupId"], "Tanggal"=>$data["Tanggal"], "Amount"=>$data["Amount"], "PaymentId"=>$PaymentId));				
			}
			$con->Commit();
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
	else if($_REQUEST["flag"]=="delete") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);	
			$con->BeginTransaction();
			$sql = "DELETE FROM payment WHERE PaymentId=:PaymentId and (GroupId='' Or VoucherId='');";		
			$affected = deleteRow($con, $sql, array("PaymentId"=>$data['PaymentId']));
			if ($affected>1) {
				$sqlAR = "DELETE FROM ar WHERE PaymentId=:PaymentId";
				deleteRow($con, $sqlAR, array("PaymentId"=>$data['PaymentId']));				
			}
			$con->Commit();
			$response["status"]=1; //berhasil
			$response["message"]="Ok";
			$response["data"]=$data;
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
