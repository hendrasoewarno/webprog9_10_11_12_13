<?php
include("library.php");

if (isset($_REQUEST["flag"])) {
	if ($_REQUEST["flag"]=="JenisKamar") {
		$con = openConnection();

		//untuk hitung total baris
		$sqlData = "SELECT JenisKamar as `key`, JenisKamar as `value` FROM rate;";
		$param=array();
		$data=queryArrayRowsValues($con, $sqlData, $param);
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($data);
	}
	else if ($_REQUEST["flag"]=="RoomId") {
		$con = openConnection();

		//untuk hitung total baris
		$sqlData = "SELECT RoomId as `key`, concat(RoomId, '-', JenisKamar, '(', CASE WHEN Status=0 Then 'Aktif' Else 'Non-Aktif' END,')->', ReadyTime) as `value` FROM room Order By RoomId;";
		$param=array();
		$data=queryArrayRowsValues($con, $sqlData, $param);
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($data);
	}
	else if ($_REQUEST["flag"]=="GuestId") {
		$con = openConnection();

		//untuk hitung total baris
		$sqlData = "SELECT GuestId as `key`, concat(GuestId, '-', Nama, '(', JenisId,' ', NomorId, ')->', ContactNo) as `value` FROM Guest Order By GuestId;";
		$param=array();
		$data=queryArrayRowsValues($con, $sqlData, $param);
		
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($data);
	}		
	else {
		echo json_encode(array(array("key"=>"error", "value"=>"Unknowned Flag")));
	}
}
else {
	echo json_encode(array(array("key"=>"error", "value"=>"Invalid Parameter")));
}
?>
