<?php
	date_default_timezone_set("America/Mexico_City");

    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('lib.php');
	$modulo=9;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	ini_set("memory_limit", "256M");
	include('../conexion.php');
	
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $estado = $_POST['estado'];
   $idestado = $_POST['idEstado'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='estado';

   if ($ord=='nombre') $orden='ORDER BY nombresucursal';
   if ($ord=='estado') $orden='ORDER BY cve_estado, nombresucursal';

// construir la condición de búsqueda
    $condicion = "WHERE 1 ";

    if (!empty($estado))
    $condicion.= " AND estados.cve_estado='$estado'";

//ajuntar la libreria excel
		require_once('phpExcel/Classes/PHPExcel.php');
	
$objPHPExcel = new PHPExcel(); //nueva instancia

$objPHPExcel->getProperties()->setTitle("Reporte Sucursales"); //titulo


//inicio estilos
$titulo = new PHPExcel_Style(); //nuevo estilo
$titulo->applyFromArray(
  array('alignment' => array( //alineacion
      'wrap' => false,
      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'font' => array( //fuente
      'bold' => true,
      'size' => 20
    )
));

$subtitulo = new PHPExcel_Style(); //nuevo estilo

$subtitulo->applyFromArray(
  array('fill' => array( //relleno de color
      'type' => PHPExcel_Style_Fill::FILL_SOLID,
      'color' => array('argb' => 'FFCCFFCC')
    ),
    'borders' => array( //bordes
      'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
    )
));

$bordes = new PHPExcel_Style(); //nuevo estilo

$bordes->applyFromArray(
  array('borders' => array(
      'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
    )
));
//fin estilos

$objPHPExcel->createSheet(0); //crear hoja
$objPHPExcel->setActiveSheetIndex(0); //seleccionar hora
$objPHPExcel->getActiveSheet()->setTitle("Listado"); //establecer titulo de hoja

//orientacion hoja
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

//tipo papel
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);

//establecer impresion a pagina completa
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToPage(true);
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);
//fin: establecer impresion a pagina completa

//establecer margenes
$margin = 0.5 / 2.54; // 0.5 centimetros
$marginBottom = 1.2 / 2.54; //1.2 centimetros
$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin);
$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom($marginBottom);
$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft($margin);
$objPHPExcel->getActiveSheet()->getPageMargins()->setRight($margin);
//fin: establecer margenes



//establecer titulos de impresion en cada hoja
$fila=1;
$objPHPExcel->getActiveSheet()->SetCellValue("A1", "Reporte de Sucursales");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:E$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:E$fila"); //establecer estilo


$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);


$xrow=2;
$objPHPExcel->getActiveSheet()->getStyle("A$xrow:E$xrow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle("A$xrow:E$xrow")->getFill()->getStartColor()->setRGB('F2F2F2');  

$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow","Id Sucursal");
$objPHPExcel->getActiveSheet()->SetCellValue("B$xrow","Sucursal");
$objPHPExcel->getActiveSheet()->SetCellValue("C$xrow","Dirección");
$objPHPExcel->getActiveSheet()->SetCellValue("D$xrow","Planta");
$objPHPExcel->getActiveSheet()->SetCellValue("E$xrow","SL");
$xrow++;   

$query = "SELECT sucursales.*, estados.estado AS nombre_estado FROM sucursales INNER JOIN estados ON sucursales.cve_estado = estados.cve_estado
            $condicion $orden ";

$resultado= mysql_query($query,$conexion);
$odd = false;
$gran_total = 0;
while ($row = mysql_fetch_array($resultado)){
            $idsucursal = $row['idsuc'];
            $cve_mpio = $row['cve_mnpio'];
            $cve_edo = $row['cve_estado'];
            $calle= utf8_encode($row['calle']);
            $numext= $row['numext'];
            $numint= $row['numint'];
            $cp= $row['cp'];
            $colonia= utf8_encode($row['colonia']);
            $nombre_estado = utf8_encode($row['nombre_estado']);
            $query2 = "SELECT mnpio FROM municipios WHERE cve_mnpio = $cve_mpio and cve_estado=$cve_edo";
            $resultadoMpo = mysql_query($query2,$conexion);
            $rowmpo = mysql_fetch_assoc($resultadoMpo);
            $nombrempio = utf8_encode($rowmpo['mnpio']);
            if($row['numext'] <> ""){$numerex= "  #".$row['numext']; }else{$numerex="  ";}
            if($row['numint']  <> ""){$numerin= "  #".$row['numint']. "(int)";}else{$numerin= "  ";}

            $direccionsuc=$calle. "   ".$numext.  "   " .$numint. "  CP. ". $cp.  "  Col.". $colonia ." , ". $nombrempio . ", ". $nombre_estado;

$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow",$row['idsuc']);
$objPHPExcel->getActiveSheet()->SetCellValue("B$xrow", $row['nombresucursal']);
$objPHPExcel->getActiveSheet()->SetCellValue("C$xrow",$direccionsuc);
$objPHPExcel->getActiveSheet()->SetCellValue("D$xrow",$row['planta']);
$objPHPExcel->getActiveSheet()->SetCellValue("E$xrow",$row['SL']);


$objVal = $objPHPExcel->getActiveSheet();

$xrow++;
}
//$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow",$query);
$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
// Guardar como excel 2007
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); //Escribir archivo

// Establecer formado de Excel 2007
header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

// nombre del archivo
header('Content-Disposition: attachment; filename="listado_sucursales.xlsx"');

//forzar a descarga por el navegador
$objWriter->save('php://output');

?>