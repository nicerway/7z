<?php
//引用PHPexcel 类
require __DIR__.'/Excel/PHPExcel.php';
require __DIR__.'/Excel/PHPExcel/IOFactory.php';
require __DIR__.'/Excel/PHPExcel/Reader/Excel5.php';
// 自定义读取excel信息类
class Excel
{
	private $file = '';
	public function __construct($filename)
	{
		if (is_file($filename)) {
			$this->setFile($filename);
		} else {
			E('文件不存在');
		}
	}

	public function setFile($file)
	{
		$this->file = $file;
	}
	public function getFile()
	{
		return $this->file;
	}

	public function readExcel5()
	{
		try {
			$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
			$objPHPExcel = $objReader->load( $this->getFile() ); //$filename可以是上传的文件，或者是指定的文件
			$sheet = $objPHPExcel->getSheet(0);
			$highestRow = $sheet->getHighestRow();
			$highestColumn = $sheet->getHighestColumn();
			$dataArray = $objPHPExcel->getSheet(0)->toArray();
			return $dataArray;
		} catch (Exception $e) {
			return [];
		}
	}
}

	// ECHO $highestRow,$highestColumn;
	/*$fields = array();
	foreach ($dataArray as $i => $value) {
		if ($i==0) continue;
		$fields[$i]['pid']           =     $pid;
		$fields[$i]['ty']            =     $ty;
		$fields[$i]['tty']           =     $tty;
		$fields[$i]['sku']     = $value[1];
		$fields[$i]['buytime'] = $value[2];
		$fields[$i]['name']    = $value[3];
		$fields[$i]['country'] = $value[4];
		$fields[$i]['address'] = $value[5];
		$fields[$i]['dianpu']  = $value[6];

		$fields[$i]['sendtime']=time();
		while(list ($key,$val) =each ($fields[$i])){
			$sqlvalues[$i].=", $key='$val'";
		}
		$sqlvalues[$i]=substr($sqlvalues[$i],1);

		 $sql="INSERT INTO `{$tablepre}news` SET ".$sqlvalues[$i];
		M()->query($sql);
	}*/


 ?>
