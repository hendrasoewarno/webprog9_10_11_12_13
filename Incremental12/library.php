<?php
define("TIMEOUT", 900); //900 detik=15 menit
define("SECURITY", 1); //900 detik=15 menit

function openConnection() {
	try {
		$con = new PDO('mysql:host=localhost;port=3306;dbname=hotel', 'root', '');
		#$con = new PDO('sqlsrv:Server=YouAddress;Database=YourDatabase', 'Username', 'Password');
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		throw new Exception("0: " . $e->getMessage());
	}
	return $con;
}

//Select
function querySingleValue($con, $sSql, $values) {
//return single Value
	try {
		if (!($stmt = $con->prepare($sSql))) {
			throw new Exception("0:  (" . $con->errno . ") " . $con->error);
		} else {
			$paramValues = $values;
			if (strpos($sSql, "?")) {
				for ($i=0; $i<sizeof($values);$i++) {   
					$paramCount = $i+1;
					$paramValues[$i] = descapeCSV($values[$i]);
					$stmt->bindParam($paramCount, $paramValues[$i]);
				}			
			} else if (strpos($sSql, ":1")) {
				for ($i=0; $i<sizeof($values);$i++) {   
					$paramCount = $i+1;
					$paramValues[$i] = descapeCSV($values[$i]);
					$stmt->bindParam(":" . $paramCount, $paramValues[$i]);
				}				
			} else {
				foreach ($paramValues as $key=>$value)
					$stmt->bindValue(':'.$key,$value);				
			}

			$stmt->execute();
			if ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
				return $row[0];
			} else {
				return null;
			}
		}
	} catch (PDOException $e) {
		throw new Exception("0: " . $e->getMessage());
	}
}

function queryArrayValue($con, $sSql, $values) {
//return array values
	try {
		if (!($stmt = $con->prepare($sSql))) {
			throw new Exception("0:  (" . $con->errno . ") " . $con->error);
		} else {
			$paramValues = $values;
			if (strpos($sSql, "?")) {
				for ($i=0; $i<sizeof($values);$i++) {   
					$paramCount = $i+1;
					$paramValues[$i] = descapeCSV($values[$i]);
					$stmt->bindParam($paramCount, $paramValues[$i]);
				}			
			} else if (strpos($sSql, ":1")) {
				for ($i=0; $i<sizeof($values);$i++) {   
					$paramCount = $i+1;
					$paramValues[$i] = descapeCSV($values[$i]);
					$stmt->bindParam(":" . $paramCount, $paramValues[$i]);
				}				
			} else {
				foreach ($paramValues as $key=>$value)
					$stmt->bindValue(':'.$key,$value);				
			}
		
			$stmt->execute();
			if ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
				return $row;
			} else {
				return null;
			}
		}
	} catch (PDOException $e) {
		throw new Exception("0: " . $e->getMessage());
	}
}

function queryArrayRowsValue($con, $sSql, $values) {
//return array values (1 dimensi)
	$arr = array();	
	try {
		if (!($stmt = $con->prepare($sSql))) {
			throw new Exception("0:  (" . $con->errno . ") " . $con->error);
		} else {
			$paramValues = $values;
			if (strpos($sSql, "?")) {
				for ($i=0; $i<sizeof($values);$i++) {   
					$paramCount = $i+1;
					$paramValues[$i] = descapeCSV($values[$i]);
					$stmt->bindParam($paramCount, $paramValues[$i]);
				}			
			} else if (strpos($sSql, ":1")) {
				for ($i=0; $i<sizeof($values);$i++) {   
					$paramCount = $i+1;
					$paramValues[$i] = descapeCSV($values[$i]);
					$stmt->bindParam(":" . $paramCount, $paramValues[$i]);
				}				
			} else {
				foreach ($paramValues as $key=>$value)
					$stmt->bindValue(':'.$key,$value);				
			}
		
			$stmt->execute();
			if ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
				do {
					$arr[] = $row[0];
			
				} while ($row = $stmt->fetch(PDO::FETCH_BOTH));
			}
		return $arr;
		}
	} catch (PDOException $e) {
		throw new Exception("0: " . $e->getMessage());
	}
}

function queryArrayRowsValues($con, $sSql, $values) {
//return array values (2 dimensi)
	$arr = array();		
	try {
		if (!($stmt = $con->prepare($sSql))) {
			throw new Exception("0:  (" . $con->errno . ") " . $con->error);
		} else {
			$paramValues = $values;
			if (strpos($sSql, "?")) {
				for ($i=0; $i<sizeof($values);$i++) {   
					$paramCount = $i+1;
					$paramValues[$i] = descapeCSV($values[$i]);
					$stmt->bindParam($paramCount, $paramValues[$i]);
				}			
			} else if (strpos($sSql, ":1")) {
				for ($i=0; $i<sizeof($values);$i++) {   
					$paramCount = $i+1;
					$paramValues[$i] = descapeCSV($values[$i]);
					$stmt->bindParam(":" . $paramCount, $paramValues[$i]);
				}				
			} else {
			
				foreach ($paramValues as $key=>$value)
					$stmt->bindValue(':'.$key,$value);				
			}
			
			$stmt->execute();
			if ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
				do {

					$arr[] = $row;
				
				} while ($row = $stmt->fetch(PDO::FETCH_BOTH));		
			}
						
			return $arr;
		}
	} catch (PDOException $e) {
		throw new Exception("0: " . $e->getMessage());
	}
}

//CRUD
function createRow($con, $sSql, $values) {
//return row Affected
	try {
		if (!($stmt = $con->prepare($sSql))) {
			throw new Exception("0:  (" . $con->errno . ") " . $con->error);
		} else {
			$paramValues = $values;
			if (strpos($sSql, "?")) {
				for ($i=0; $i<sizeof($values);$i++) {   
					$paramCount = $i+1;
					$paramValues[$i] = descapeCSV($values[$i]);
					$stmt->bindParam($paramCount, $paramValues[$i]);
				}			
			} else if (strpos($sSql, ":1")) {
				for ($i=0; $i<sizeof($values);$i++) {   
					$paramCount = $i+1;
					$paramValues[$i] = descapeCSV($values[$i]);
					$stmt->bindParam(":" . $paramCount, $paramValues[$i]);
				}				
			} else {
				foreach ($paramValues as $key=>$value)
					$stmt->bindValue(':'.$key,$value);				
			}
			$stmt->execute();
			return $stmt->rowCount();
		}
	} catch (PDOException $e) {
		throw new Exception("0: " . $e->getMessage());
	}
}

function updateRow($con, $sSql, $values) {
//return row Affected
	try {
		if (!($stmt = $con->prepare($sSql))) {
			throw new Exception("0:  (" . $con->errno . ") " . $con->error);
		} else {
			$paramValues = $values;
			if (strpos($sSql, "?")) {
				for ($i=0; $i<sizeof($values);$i++) {   
					$paramCount = $i+1;
					$paramValues[$i] = descapeCSV($values[$i]);
					$stmt->bindParam($paramCount, $paramValues[$i]);
				}			
			} else if (strpos($sSql, ":1")) {
				for ($i=0; $i<sizeof($values);$i++) {   
					$paramCount = $i+1;
					$paramValues[$i] = descapeCSV($values[$i]);
					$stmt->bindParam(":" . $paramCount, $paramValues[$i]);
				}				
			} else {
				foreach ($paramValues as $key=>$value)
					$stmt->bindValue(':'.$key,$value);				
			}
			$stmt->execute();
			return $stmt->rowCount();
		}
	} catch (PDOException $e) {
		throw new Exception("0: " . $e->getMessage());
	}
}

function deleteRow($con, $sSql, $values) {
//return row Affected
	try {
		if (!($stmt = $con->prepare($sSql))) {
		throw new Exception("0:  (" . $con->errno . ") " . $con->error);
	} else {
		$paramValues = $values;
		if (strpos($sSql, "?")) {
			for ($i=0; $i<sizeof($values);$i++) {   
				$paramCount = $i+1;
				$paramValues[$i] = descapeCSV($values[$i]);
				$stmt->bindParam($paramCount, $paramValues[$i]);
			}			
		} else if (strpos($sSql, ":1")) {
			for ($i=0; $i<sizeof($values);$i++) {   
				$paramCount = $i+1;
				$paramValues[$i] = descapeCSV($values[$i]);
				$stmt->bindParam(":" . $paramCount, $paramValues[$i]);
			}				
		} else {
			foreach ($paramValues as $key=>$value)
				$stmt->bindValue(':'.$key,$value);				
		}	
		$stmt->execute();
		return $stmt->rowCount();
		}
	} catch (PDOException $e) {
		throw new Exception("0: " . $e->getMessage());
	}
}

function formatUang($number, $mataUang=true) {
	return ($mataUang?"Rp. ":"") . number_format($number,0,",",".");
}

function isLogin() {
	return isset($_SESSION['userid']) and isset($_SESSION['password']);
}

function isValidUser() {
	#return (strtolower($_SESSION['userid']=="hendra") && $_SESSION['password']=="soewarno");
$con=openConnection();	
$sqlS = "Select count(*) as ada from zoperator where userid=:userid and password=:password";
$ada = querySingleValue($con, $sqlS, array("userid"=>$_SESSION["userid"], "password"=>$_SESSION["password"]));
return ($ada>0);
}

function loginPage($title, $message) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="AdminLTE-3.2.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="AdminLTE-3.2.0/dist/css/adminlte.min.css">
  <script>
	//break out iframe
	if(this != top){
		top.document.location.href = this.document.location.href;
	}
  </script>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <?php echo $title; ?></b>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg"><?php echo $message; ?> </p>

      <form action="index.php" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="UserId" name="userid">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
</body>
</html>
<?php
}

/*
//--------------------------Entry Point-------------------------------
//setting timezone
date_default_timezone_set('Asia/Jakarta');

if (SECURITY>0) {
	// Start the session
	session_start();

	#inisialisasi nilai failed
	if (!isset($_SESSION["failed"]))
		$_SESSION["failed"]=0;
	#jika sudah gagal 3x
	else if ($_SESSION["failed"]>=3)
		#belum 15 menit berikutnya
		if (time() < $_SESSION["lasttime"]+15*60) {
			echo "Silakan Tunggu 15 menit berikutnya!";
			die();
		}
		else
			$_SESSION["failed"]=0;

	$con = openConnection();

	if (isset($_REQUEST['logout'])) {
		session_destroy();
		header('Location: index.php');
		die();
	}
	else if (isset($_REQUEST['userid']) && isset($_REQUEST['password'])) {
		$_SESSION['userid']=$_REQUEST['userid'];
		$_SESSION['password']=$_REQUEST['password'];
	}

	try {
		if (!isLogin()) {		
			throw new Exception("Please Login");		
		}
		else if(!isValidUser()){
			#tambahkan counter kegagalan
			$_SESSION["failed"]+=1;
			#catat waktu terakhir gagal
			$_SESSION["lasttime"]=time();
			throw new Exception("Please Login");
		}
		else {
			$_SESSION["failed"]=0;
		}
	}
	catch (Exception $e) {
		loginPage("Login", $e->getMessage());
		die();
	}
}
?>
*/