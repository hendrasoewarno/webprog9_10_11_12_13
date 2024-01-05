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
	  <input type="hidden" class="form-control" id="ARId" name="input-element" value="0">
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
		   <label for="Jenis" class="form-label">Jenis</label>
		   <select class="form-select" id="Jenis" name="input-element">
				<option value="DK">Debet/Kredit</option>
           </select>
        </div>		
		<div class="mb-3">
		   <label for="Keterangan" class="form-label">Keterangan</label>
		   <textarea class="form-control" id="Keterangan" rows="3" name="input-element"></textarea>
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

<div class="modal" id="myFormProcessAR" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myFormProcessARTitle">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
           <label for="GroupId" class="form-label">GroupId</label>
           <input type="text" class="form-control" id="ProcessARGroupId" placeholder="Isikan GroupId" name="input-element">
        </div>
      </div>		
      <div class="modal-footer">
		<p id="feedbackProcessAR"><p>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="saveProcessAR" type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>


<div class="container-fluid">
<p>
<button id="processAR" type="button" class="btn btn-danger btn-sm">Process AR</button>
<button id="add" type="button" class="btn btn-primary btn-sm">Add</button>
</p>
<table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>GroupId</th><th>Tanggal</th><th>Jenis</th><th>Keterangan</th><th>Amount</th><th>PaymentId</th><th>ARId</th><th>Action</th>
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
        { data: 'GroupId' }, { data: 'Tanggal' }, { data: 'Jenis' }, { data: 'Keterangan' }, { data: 'Amount' }, { data: 'PaymentId' }, { data: 'ARId' },
		{ "orderable": false, "data": null,"defaultContent":
				"<button type=\"button\" class=\"btn btn-warning btn-sm\" id=\"edit\">Edit</button>&nbsp;<button type=\"button\" class=\"btn btn-danger btn-sm\" id=\"delete\">Delete</button>"}
    ]
} );

$('#add').click(function() {
	flag="add";
	$('#myFormTitle').text("Add Data");
	$('#GroupId').val("");
	$('#Tanggal').val(new Date().toISOString().slice(0, 10));
	$('#Jenis').val("")
	$('#Keterangan').val("")
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

function postToServerProcessAR(obj, callBack) {
	$.post("?flag=" + flag,
		JSON.stringify(obj), 
	    	function(data,status) {
				if (data["status"]==1) {
					callBack();
				}
				else {
					$("#feedbackProcessAR").text(data["message"]);
				}
		}
	);
}

$('#processAR').click(function() {
	flag="processAR";
	$('#myFormProcessARTitle').text("Process AR");
	$('#GroupId').val("");
	$('#save').text("Save change");
	$('#feedback').text("");
	$('#myFormProcessAR').modal('show');
});

$('#saveProcessAR').click(function() {
	var data = {"GroupId":$('#ProcessARGroupId').val()};
	postToServerProcessAR(data, function() {
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
    var ARId = row['ARId'];
	if (row['Jenis']=="DK") {
		readFromServer({"ARId":ARId}, function(data) {
			flag="edit";
			$('#myFormTitle').text("Edit Data");
			$('#ARId').val(data["ARId"]);
			$('#GroupId').val(data["GroupId"]);
			$('#Tanggal').val(data["Tanggal"]);
			$('#Jenis').val(data["Jenis"]);
			$('#Keterangan').val(data["Keterangan"]);
			$('#Amount').val(data["Amount"]);
			$('#save').text("Save change");
			$('#feedback').text("");
			$('#myForm').modal('show');
		});
	}
});

//klik pada button delete
table.on('click', '#delete', function (e) {
	//ambil data dari baris yang diklik
var row = table.row(e.target.closest('tr')).data();
    var ARId = row['ARId'];
	if (row['Jenis']=="DK") {
		readFromServer({"ARId":ARId}, function(data) {
			flag="delete";
			$('#myFormTitle').text("Delete Data");
			$('#ARId').val(data["ARId"]);
			$('#GroupId').val(data["GroupId"]);
			$('#Tanggal').val(data["Tanggal"]);
			$('#Jenis').val(data["Jenis"]);
			$('#Keterangan').val(data["Keterangan"]);
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
		$sqlData = "SELECT GroupId, Tanggal, Jenis, Keterangan, Amount, PaymentId, ARId FROM ar WHERE GroupId LIKE :search LIMIT $length OFFSET $start";

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
			$sql = "SELECT GroupId, Tanggal, Jenis, Keterangan, Amount, PaymentId, ARId FROM ar WHERE ARId=:ARId;";		
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
			unset($data["ProcessARGroupId"]);
			$ada = querySingleValue($con, "SELECT count(*) FROM ar WHERE GroupId=:GroupId;", array("GroupId"=>$data["GroupId"]));
			if ($ada > 0) {
				$sql = "INSERT into ar(GroupId, Tanggal, Jenis, Keterangan, Amount, ARId) VALUES (:GroupId, :Tanggal, :Jenis, :Keterangan, :Amount, :ARId);";		
				createRow($con, $sql, $data);
				$response["status"]=1;
				$response["message"]="Ok";
				$response["data"]=$data;
			}
			else {
				throw new Exception("GroupId " . $data["GroupId"] . " tidak ditemukan atau belum Check-Out");
			}
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
			unset($data["ProcessARGroupId"]);
			$sql = "UPDATE ar SET GroupId=:GroupId, Tanggal=:Tanggal, Jenis=:Jenis, Keterangan=:Keterangan, Amount=:Amount WHERE ARId=:ARId and Jenis='DK';";		
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
			$sql = "DELETE FROM ar WHERE ARId=:ARId and Jenis='DK';";		
			deleteRow($con, $sql, array("ARId"=>$data['ARId']));
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
	else if($_REQUEST["flag"]=="processAR") {
		$response=array();
		try {
			$con = openConnection();
			$body = file_get_contents('php://input');
			$data = json_decode($body, true);
			$ada = querySingleValue($con, "SELECT count(*) FROM AR WHERE GroupId=:GroupId And Jenis='AR';", $data);
			if ($ada > 0) {
				throw new Exception("GroupId " . $data["GroupId"] . " Telah pernah diproses AR");
			}
			else {
			
				$Amount = querySingleValue($con, "SELECT Sum(Amount) as Amount FROM dk WHERE GroupId=:GroupId;", $data);
				if ($Amount > 0) {
					$con->BeginTransaction();
					$sql = "INSERT into ar(GroupId, Tanggal, Jenis, Keterangan, Amount, ARId) VALUES (:GroupId, CurDate(), 'AR', 'Total D/K', :Amount, 0);";
					createRow($con, $sql, array("GroupId"=>$data["GroupId"], "Amount"=>$Amount));
					$ARId=$con->lastInsertId();
					updateRow($con, "UPDATE dk SET ARId=:ARId WHERE GroupId=:GroupId", array("ARId"=>$ARId, "GroupId"=>$data["GroupId"]));
					
					#Process Payment
					$arrData = queryArrayRowsValues($con, "SELECT a.VoucherId, b.Amount, b.PaymentId FROM occupied a INNER JOIN payment b WHERE a.GroupId=:GroupId GROUP BY a.voucherId, b.amount, b.paymentId;", array("GroupId"=>$data["GroupId"]));
					if (sizeof($arrData)>0) {
						for ($i=0;$i<sizeof($arrData);$i++) {
							$row=$arrData[$i];
							#Tambahkan payment ke AR
							$sqlPayment = "INSERT into ar(GroupId, Tanggal, Jenis, Keterangan, Amount, PaymentId, ARId) VALUES (:GroupId, CurDate(), 'Payment', CONCAT('VoucherId ', :VoucherId ), :Amount, :PaymentId, 0);";
							createRow($con, $sqlPayment, array("GroupId"=>$data["GroupId"], "VoucherId"=>$row["VoucherId"], "Amount"=>$row["Amount"], "PaymentId"=>$row["PaymentId"]));		
							#Update GroupId pada Tabel Payment
							$sqlPaymentSettle = "UPDATE payment SET GroupId=:GroupId WHERE PaymentId=:PaymentId AND (GroupId='' OR GroupId=:GroupId);";
							updateRow($con, $sqlPaymentSettle, array("GroupId"=>$data["GroupId"], "PaymentId"=>$row["PaymentId"]));
						}
					}
					$con->Commit();
					$response["status"]=1;
					$response["message"]="Ok";
					$response["data"]=$data;
				}
				else {
					throw new Exception("GroupId " . $data["GroupId"] . " Tidak ada AR atau Belum Checkout");
				}
			}
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
