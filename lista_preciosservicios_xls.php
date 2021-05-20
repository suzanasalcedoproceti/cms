<?php
	date_default_timezone_set("America/Mexico_City");

    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('lib.php');
	$modulo=9;
	if (!op($modulo))  {
    $modulo=33;
    if (!op($modulo))  {
      $aviso = 'Usuario sin permiso para acceder a este módulo';
      $aviso_link = 'principal.php';
      include('mensaje_sistema.php');
      return;
    }
  }
  if (strpos($_SESSION['ss_opciones'], '33') !== false) {
   $ventas = 1;
 }else{
   $ventas = 0;
 }
	ini_set("memory_limit", "256M");
	include('../conexion.php');
	
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $estado = $_POST['estado'];
   $idestado = $_POST['idEstado'];
   $tipo_productosel= $_POST['tipo_productosel'];  
   $subtipo_productosel = $_POST['subtipo_productow'];
   $tipo_servicio = $_POST['tipo_servicio'];
   $cobertura_ = $_POST['cobertura_']; 
   $bsq=$_GET['bsq']; 
   $sbtipo_productosel=$_POST['subtipo_productow']; 
   $tipo_productoq = $_POST['tipo_producto']; 
   
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='estado'; 
   if ($ord=='estado') $orden='ORDER BY cluster';

   // construir la condición de búsqueda
   $condicion = "  WHERE  1  ";  

   if (!empty($estado))
   $condicion.= " AND  cluster='$estado'"; 
   if (!empty($tipo_productosel))
   $condicion.= " AND  tipo_producto='$tipo_productosel'";
   if (!empty($subtipo_productosel))
   $condicion.= " AND  subtipo_producto='$subtipo_productosel'";
   if (!empty($tipo_servicio))
   $condicion.= " AND  idServicio='$tipo_servicio'"; 

//ajuntar la libreria excel
		require_once('phpExcel/Classes/PHPExcel.php');
	
$objPHPExcel = new PHPExcel(); //nueva instancia

$objPHPExcel->getProperties()->setTitle("Reporte Precios Servicios"); //titulo


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
$objPHPExcel->getActiveSheet()->SetCellValue("A1", "Reporte de Precios Servicios");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:G$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:G$fila"); //establecer estilo


$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);

$xrow=2;
$objPHPExcel->getActiveSheet()->getStyle("A$xrow:G$xrow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle("A$xrow:G$xrow")->getFill()->getStartColor()->setRGB('F2F2F2');  

$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow","Cluster");
$objPHPExcel->getActiveSheet()->SetCellValue("B$xrow","Tipo producto");
$objPHPExcel->getActiveSheet()->SetCellValue("C$xrow","Subtipo producto");
$objPHPExcel->getActiveSheet()->SetCellValue("D$xrow","Tipo servicio");
$objPHPExcel->getActiveSheet()->SetCellValue("E$xrow","Descripción");
$objPHPExcel->getActiveSheet()->SetCellValue("F$xrow","Precio");
$objPHPExcel->getActiveSheet()->SetCellValue("G$xrow","Costo");
$xrow++;   

$query="SELECT * FROM  precioservicios $condicion $orden";

$resultado= mysql_query($query,$conexion);
$odd = false;
$gran_total = 0;
while ($row = mysql_fetch_array($resultado)){
            $idPrecioservicio = $row['idPrecioservicio'];
            $cluster = $row['cluster'];
            $tipo_producto = $row['tipo_producto'];
            $subtipo_producto = $row['subtipo_producto'];
            $idservicio = $row['idServicio'];
            $precio = $row['precio'];
            $costo = $row['costo'];
          
            $resultadoMpo = mysql_query("SELECT estado,mnpio FROM cp_sepomex WHERE cve_mnpio = $cve_mpio and cve_estado=$cve_edo",$conexion);
            $rowmpo = mysql_fetch_assoc($resultadoMpo);
            $nombrempio = $rowmpo['mnpio'];
            $nombreedo = $rowmpo['estado'];

            $resultadosrv = mysql_query("SELECT * FROM servicios WHERE idServicio = $idservicio",$conexion);
            $rowsrv= mysql_fetch_assoc($resultadosrv);
            $tiposerv = $rowsrv['tipo_servicio'];
            $descripcion = $rowsrv['descripcion'];

$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow",$cluster);
$objPHPExcel->getActiveSheet()->SetCellValue("B$xrow",$tipo_producto);
$objPHPExcel->getActiveSheet()->SetCellValue("C$xrow",$subtipo_producto);
$objPHPExcel->getActiveSheet()->SetCellValue("D$xrow",$tiposerv);
$objPHPExcel->getActiveSheet()->SetCellValue("E$xrow",$descripcion);
$objPHPExcel->getActiveSheet()->SetCellValue("F$xrow",$precio);
$objPHPExcel->getActiveSheet()->SetCellValue("G$xrow",$costo);


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
header('Content-Disposition: attachment; filename="listado_preciosservicios.xlsx"');

//forzar a descarga por el navegador
$objWriter->save('php://output');

?>