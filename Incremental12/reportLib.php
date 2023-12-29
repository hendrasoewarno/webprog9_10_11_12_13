<?php
class ReportGen {
	protected $con;
	protected $sql;
	protected $recCount;
	
	function __construct($con, $sql) {
		$this->con = $con;
		$temp = explode(";",$sql);
		$this->sql = $temp[0];
	}
	
	function getRecCount() {
		return $this->recCount;
	}
	
	function onFirstPage() {
	}
	
	function onHeader($row) {
	}
	
	function reConvert($row) {
		return $row;
	}
	
	function getGroupNo($lastRow, $row, $totalGroup) {
		return 0;
	}
	
	function onGroupHeader($row, $groupNo) {
	}
	
	function onDetail($row) {
	}
	
	function onGroupFooter($lastRow, $row, $groupNo) {
	}	
	
	function onFooter($row) {
	}
	
	function onNoRecord() {
	}
	
	function onLastPage() {
	}

	function query($values, $totalGroup) {
	//return array values
		try {
			if (!($stmt = $this->con->prepare($this->sql))) {
				throw new Exception("0:  (" . $con->errno . ") " . $con->error);
			} else {
				$paramValues = $values;
				foreach ($paramValues as $key=>$value)
					$stmt->bindValue(':'.$key,$value);				

				$stmt->execute();

				if ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
					$this->onFirstPage();					
					$lastRow=null;
					$lastGroupNo=0;
					$this->recCount=0;
					do {
						$newRow=$this->reConvert($row);
						
						if ($this->recCount==0)
							$this->onHeader($row);

						$currentGroupNo=$this->getGroupNo($lastRow, $newRow, $totalGroup);
												
						if ($currentGroupNo!=$lastGroupNo) {
							
							if ($this->recCount>0) {
								for($i=$currentGroupNo;$i>0;$i--)
									$this->onGroupFooter($lastRow, $newRow, $i);
							}
							for($i=1;$i<=$currentGroupNo;$i++)
								$this->onGroupHeader($newRow, $i);
						}
						
						$this->recCount++;					
						$this->onDetail($newRow);
						$lastRow=$newRow;
						
					} while ($row = $stmt->fetch(PDO::FETCH_BOTH));
					
					for($i=$totalGroup;$i>0;$i--)
						$this->onGroupFooter($lastRow, null, $i);
					
					$this->onFooter($lastRow);
					
					$this->onLastPage();
				} else {
					$this->onNoRecord();
				}
			}
		} catch (PDOException $e) {
			throw new Exception("0: " . $e->getMessage());
		}
	}
}
?>