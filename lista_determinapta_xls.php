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
   $tipo_producto = $_POST['tipo_producto'];
   $tipo_servicio = $_POST['tipo_servicio']; 

   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='estado';

   if ($ord=='estado') $orden='ORDER BY cluster';

 // construir la condición de búsqueda
   $condicion = "WHERE 1 ";


   if (!empty($estado))
   $condicion.= " AND  cluster='$estado'"; 
   if (!empty($tipo_producto))
   $condicion.= " AND  tipo_producto='$tipo_producto'";
   if (!empty($tipo_servicio))
   $condicion.= " AND  idServicio='$tipo_servicio'";

//ajuntar la libreria excel
		require_once('phpExcel/Classes/PHPExcel.php');
	
$objPHPExcel = new PHPExcel(); //nueva instancia

$objPHPExcel->getProperties()->setTitle("Reporte Determina Planta"); //titulo


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
$objPHPExcel->getActiveSheet()->SetCellValue("A1", "Reporte Determina Planta");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:L$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:L$fila"); //establecer estilo


$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setAutoSize(true);

$xrow=2;
$objPHPExcel->getActiveSheet()->getStyle("A$xrow:L$xrow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle("A$xrow:L$xrow")->getFill()->getStartColor()->setRGB('F2F2F2');  

$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow","Cluster");
$objPHPExcel->getActiveSheet()->SetCellValue("B$xrow","Tipo Producto");
$objPHPExcel->getActiveSheet()->SetCellValue("C$xrow","Tipo Servicio");
$objPHPExcel->getActiveSheet()->SetCellValue("D$xrow","Descripción Servicio");
$objPHPExcel->getActiveSheet()->SetCellValue("E$xrow","Cedis 1");
$objPHPExcel->getActiveSheet()->SetCellValue("F$xrow","Cedis 2");
$objPHPExcel->getActiveSheet()->SetCellValue("G$xrow","Cedis 3");
$objPHPExcel->getActiveSheet()->SetCellValue("H$xrow","Cedis 4");
$objPHPExcel->getActiveSheet()->SetCellValue("I$xrow","Cedis 5");
$objPHPExcel->getActiveSheet()->SetCellValue("J$xrow","Cedis 6");
$objPHPExcel->getActiveSheet()->SetCellValue("K$xrow","Cedis 7");
$objPHPExcel->getActiveSheet()->SetCellValue("L$xrow","Cedis 8");
$xrow++;   

$query = "SELECT * from determina_planta $condicion ORDER BY cluster";

$resultado= mysql_query($query,$conexion);
$odd = false;
$gran_total = 0;
while ($row = mysql_fetch_array($resultado)){
            $idDeterminacion = $row['idDeterminacion'];
            $cluster = $row['cluster'];
            $tipo_producto = $row['tipo_producto'];
            $idservicio = $row['idServicio'];
            $cedis1 = $row['cedis1']; 
            $cedis2 = $row['cedis2']; 
            $cedis3 = $row['cedis3']; 
            $cedis4 = $row['cedis4']; 
            $cedis5 = $row['cedis5']; 
            $cedis6 = $row['cedis6']; 
            $cedis7 = $row['cedis7']; 
            $cedis8 = $row['cedis8'];
          
            $resultadosrv = mysql_query("SELECT * FROM servicios WHERE idServicio = $idservicio",$conexion);
            $rowsrv= mysql_fetch_assoc($resultadosrv);
            $tiposerv = $rowsrv['tipo_servicio'];
            $descripcion = $rowsrv['descripcion'];
            

$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow",$cluster);
$objPHPExcel->getActiveSheet()->SetCellValue("B$xrow",$tipo_producto);
$objPHPExcel->getActiveSheet()->SetCellValue("C$xrow",$tiposerv);
$objPHPExcel->getActiveSheet()->SetCellValue("D$xrow",$descripcion);
$objPHPExcel->getActiveSheet()->SetCellValue("E$xrow",$cedis1);
$objPHPExcel->getActiveSheet()->SetCellValue("F$xrow",$cedis2);
$objPHPExcel->getActiveSheet()->SetCellValue("G$xrow",$cedis3);
$objPHPExcel->getActiveSheet()->SetCellValue("H$xrow",$cedis4);
$objPHPExcel->getActiveSheet()->SetCellValue("I$xrow",$cedis5);
$objPHPExcel->getActiveSheet()->SetCellValue("J$xrow",$cedis6);
$objPHPExcel->getActiveSheet()->SetCellValue("K$xrow",$cedis7);
$objPHPExcel->getActiveSheet()->SetCellValue("L$xrow",$cedis8);


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
header('Content-Disposition: attachment; filename="lista_determinapta.xlsx"');

//forzar a descarga por el navegador
$objWriter->save('php://output');

?>