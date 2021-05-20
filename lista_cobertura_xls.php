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
   $municipio = $_POST['municipio'];
   $tipo_producto = $_POST['tipo_producto'];
   $tipo_servicio = $_POST['tipo_servicio'];
   $cobertura_=$_POST['cobertura_'];  

   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='estado'; 
   if ($ord=='estado') $orden='ORDER BY cve_estado';

// construir la condición de búsqueda
  $condicion = "WHERE 1 ";

  if (!empty($estado))  
   $condicion.= " AND  cve_estado='$estado'";
   if (!empty($municipio))                     
   $condicion.= "  AND  cve_mnpio='$municipio'";
   if (!empty($tipo_producto))
   $condicion.= "  AND  tipo_producto='$tipo_producto'";
   if (!empty($tipo_servicio))  
   $condicion.= "  AND  idServicio='$tipo_servicio'";
   if (isset($_POST['cobertura_'])) 
   {$cobertura_=$_POST['cobertura_'];
     if ($cobertura_!='') $condicion.= "AND  cobertura='$cobertura_'";  
    }
  else
    { $cobertura_='SI'; $condicion.= "AND  cobertura='$cobertura_'"; }

//ajuntar la libreria excel
		require_once('phpExcel/Classes/PHPExcel.php');
	
$objPHPExcel = new PHPExcel(); //nueva instancia

$objPHPExcel->getProperties()->setTitle("Reporte Cobertura"); //titulo


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
$objPHPExcel->getActiveSheet()->SetCellValue("A1", "Reporte de Cobertura");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:J$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:J$fila"); //establecer estilo


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

$xrow=2;
$objPHPExcel->getActiveSheet()->getStyle("A$xrow:J$xrow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle("A$xrow:J$xrow")->getFill()->getStartColor()->setRGB('F2F2F2');  

$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow","Estado");
$objPHPExcel->getActiveSheet()->SetCellValue("B$xrow","Municipio");
$objPHPExcel->getActiveSheet()->SetCellValue("C$xrow","Tipo de producto");
$objPHPExcel->getActiveSheet()->SetCellValue("D$xrow","Tipo de servicio");
$objPHPExcel->getActiveSheet()->SetCellValue("E$xrow","Cobertura");
$objPHPExcel->getActiveSheet()->SetCellValue("F$xrow","Sucursal (id)");
$objPHPExcel->getActiveSheet()->SetCellValue("G$xrow","Subcategoria 1");
$objPHPExcel->getActiveSheet()->SetCellValue("H$xrow","Subcategoria 2");
$objPHPExcel->getActiveSheet()->SetCellValue("I$xrow","Subcategoria 3");
$objPHPExcel->getActiveSheet()->SetCellValue("J$xrow","Subcategoria 4");
$xrow++;   

$query = "SELECT * from cobertura $condicion $orden";

$resultado= mysql_query($query,$conexion);
$odd = false;
$gran_total = 0;
while ($row = mysql_fetch_array($resultado)){
            $idCobertura = $row['idCobertura'];
              $cve_mpio = $row['cve_mnpio'];
              $cve_edo = $row['cve_estado'];
              $idserv = $row['idServicio']; 
              $tipo_producto=$row['tipo_producto'];
          
            $resultadoMpo = mysql_query("SELECT estado,mnpio FROM cp_sepomex WHERE cve_mnpio = $cve_mpio and cve_estado=$cve_edo",$conexion);
            $rowmpo = mysql_fetch_assoc($resultadoMpo);
            $nombrempio = $rowmpo['mnpio'];
            $nombreedo = $rowmpo['estado'];
            $resultadosrv = mysql_query("SELECT * FROM servicios WHERE idServicio = $idserv",$conexion);
            $rowsrv= mysql_fetch_assoc($resultadosrv);
            $tiposerv = $rowsrv['tipo_servicio'];
            $descripcion = $rowsrv['descripcion'];
            $resultadoclstr = mysql_query("SELECT * FROM  estados where cve_estado= $cve_edo",$conexion);
            $rowclstr= mysql_fetch_assoc($resultadoclstr);
            $cluster = $rowclstr['cluster'];   
            $resultadosucrs = mysql_query("SELECT * FROM  cobertura_sucursal where idCobertura= $idCobertura",$conexion); 

             while ($rowsucrs = mysql_fetch_array($resultadosucrs)){ 
            $idcobertura_sucursal = $rowsucrs['idcobertura_sucursal']; 
            $cobertura_suc = $rowsucrs['idsuc']; 
             
           }
           $resultadosucursales = mysql_query("SELECT * FROM  cobertura_sucursal where idCobertura= $idCobertura",$conexion); 
          $trae_sucursal = array(); 
          $cobertura_sucursales = "";
          while ($rowsucursales = mysql_fetch_assoc($resultadosucursales)){ 
            $idcobertura_sucursal = $rowsucursales['idcobertura_sucursal']; 
            $cobertura_suc= $rowsucursales['idsuc']; 
            if($cobertura_suc == 0) {
              $cobertura_sucursales= 'N/A';
            } else{
              $cobertura_sucursales.="(".$cobertura_suc.")";
            }
          }

          $suc_id = $tiposerv."(".$descripcion.")";
          $queryw= "SELECT subtipo_producto  FROM precioservicios where cluster= $cluster AND tipo_producto = '$tipo_producto' AND idServicio=$idserv";
          $resultadow= mysql_query($queryw,$conexion);
          while ($roww = mysql_fetch_array($resultadow)){ 
            $subtipo_prd=$roww['subtipo_producto'];    
            $prefsubtipo=substr($roww['subtipo_producto'], -0 , 1);
          }

          $prefsubtipo1= $prefsubtipo.'1';   
          $resulta = mysql_query("SELECT subtipo_producto  FROM precioservicios where cluster= $cluster AND tipo_producto = '$tipo_producto' AND subtipo_producto='$prefsubtipo1' AND idServicio=$idserv",$conexion);
          $rowcr= mysql_fetch_assoc($resulta);
          $ss1 = $rowcr['subtipo_producto'];  
          if(empty($ss1)) $preciosub1="NO";
          if(!empty($ss1)) $preciosub1="SI";

          $prefsubtipo1= $prefsubtipo.'2';   
          $resulta = mysql_query("SELECT subtipo_producto  FROM precioservicios where cluster= $cluster AND tipo_producto = '$tipo_producto' AND subtipo_producto='$prefsubtipo1' AND idServicio=$idserv",$conexion);
          $rowcr= mysql_fetch_assoc($resulta);
          $ss2 = $rowcr['subtipo_producto'];  
          if(empty($ss2)) $preciosub2="NO";
          if(!empty($ss2)) $preciosub2="SI";

          $prefsubtipo1= $prefsubtipo.'3';   
          $resulta = mysql_query("SELECT subtipo_producto  FROM precioservicios where cluster= $cluster AND tipo_producto = '$tipo_producto' AND subtipo_producto='$prefsubtipo1' AND idServicio=$idserv",$conexion);
          $rowcr= mysql_fetch_assoc($resulta);
          $ss3 = $rowcr['subtipo_producto']; 
          if(empty($ss3)) $preciosub3="NO";
          if(!empty($ss3)) $preciosub3="SI";

          $prefsubtipo1= $prefsubtipo.'4';   
          $resulta = mysql_query("SELECT subtipo_producto  FROM precioservicios where cluster= $cluster AND tipo_producto = '$tipo_producto' AND subtipo_producto='$prefsubtipo1' AND idServicio=$idserv",$conexion);
          $rowcr= mysql_fetch_assoc($resulta);
          $ss4 = $rowcr['subtipo_producto'];  
          if(empty($ss4)) $preciosub4="NO";
          if(!empty($ss4)) $preciosub4="SI";

$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow",utf8_encode($nombreedo));
$objPHPExcel->getActiveSheet()->SetCellValue("B$xrow",utf8_encode($nombrempio));
$objPHPExcel->getActiveSheet()->SetCellValue("C$xrow",substr($row['tipo_producto'], 1));
$objPHPExcel->getActiveSheet()->SetCellValue("D$xrow",$suc_id);
$objPHPExcel->getActiveSheet()->SetCellValue("E$xrow",$row['cobertura']);
$objPHPExcel->getActiveSheet()->SetCellValue("F$xrow",$cobertura_sucursales);
$objPHPExcel->getActiveSheet()->SetCellValue("G$xrow",$preciosub1);
$objPHPExcel->getActiveSheet()->SetCellValue("H$xrow",$preciosub2);
$objPHPExcel->getActiveSheet()->SetCellValue("I$xrow",$preciosub3);
$objPHPExcel->getActiveSheet()->SetCellValue("J$xrow",$preciosub4);


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
header('Content-Disposition: attachment; filename="listado_cobertura.xlsx"');

//forzar a descarga por el navegador
$objWriter->save('php://output');

?>