<?php
	date_default_timezone_set("America/Mexico_City");

    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('lib.php');
	$modulo=13;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}

	include('../conexion.php');
	
	$tienda = $_POST['tienda']+0;
	$fecha = $_POST['fecha'];
	$folio = $_POST['folio'];
	$folio_oc = $_POST['folio_oc'];
	$tipo = $_POST['tipo'];
	$buscar = $_POST['buscar']+0;
	$ord = $_POST['ord'];
	$empresa = $_POST['empresa'];
	$vendedor = $_POST['vendedor'];

	
	$condicion = "WHERE pedido.origen = 'pos' and orden_compra.estatus=0 ";

	 if ($tienda > 0) 
	 	$condicion .= " AND pedido.tienda = $tienda ";

	 if ($empresa) 
	 	$condicion .= " AND pedido.empresa = $empresa ";

	 if ($vendedor)
	 	$condicion .= " AND pedido.vendedor = $vendedor ";
								
	 if ($folio_oc) 
	 	$condicion .= " AND pedido.fdp_credito_nomina_folio LIKE '%".trim($folio_oc)."%' ";

	
    if (!empty($fecha)) {
		$fecha_desde = convierte_fecha(substr($fecha,0,10));
		$fecha_hasta = convierte_fecha(substr($fecha,13,10));
		$condicion .= " AND pedido.fecha BETWEEN '$fecha_desde' AND '$fecha_hasta' ";
	}
	
	

	

							
		
	//  $condicion .= " AND pedido.estatus = 1 ";
//ajuntar la libreria excel
		require_once('phpExcel/Classes/PHPExcel.php');
	
$objPHPExcel = new PHPExcel(); //nueva instancia

$objPHPExcel->getProperties()->setTitle("Reporte  Orden de Compra"); //titulo


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
$objPHPExcel->getActiveSheet()->SetCellValue("A1", "Reporte de Orden de Compra");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:M$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:M$fila"); //establecer estilo


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
$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setAutoSize(true);


$xrow=2;
$objPHPExcel->getActiveSheet()->getStyle("A$xrow:M$xrow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle("A$xrow:M$xrow")->getFill()->getStartColor()->setRGB('F2F2F2');  
$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow", $mesD[$mes]);

$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow","Fecha");
$objPHPExcel->getActiveSheet()->SetCellValue("B$xrow","Empresa");
$objPHPExcel->getActiveSheet()->SetCellValue("C$xrow","Pedido POS");
$objPHPExcel->getActiveSheet()->SetCellValue("D$xrow","Cliente");
$objPHPExcel->getActiveSheet()->SetCellValue("E$xrow","No. Empleado");
$objPHPExcel->getActiveSheet()->SetCellValue("F$xrow","Monto a Financiar");
$objPHPExcel->getActiveSheet()->SetCellValue("G$xrow","Plazo");
$objPHPExcel->getActiveSheet()->SetCellValue("H$xrow","Pagos");
$objPHPExcel->getActiveSheet()->SetCellValue("I$xrow","Descuento");
$objPHPExcel->getActiveSheet()->SetCellValue("J$xrow","Articulos");
$objPHPExcel->getActiveSheet()->SetCellValue("K$xrow","Credito Aprobado");
$objPHPExcel->getActiveSheet()->SetCellValue("L$xrow","Monto Disponible");
$objPHPExcel->getActiveSheet()->SetCellValue("M$xrow","Estatus");
$xrow++;   

 $query = "SELECT pedido.*, empresa.nombre AS nombre_empresa, usuario_tienda.nombre AS nombre_vendedor, orden_compra.aprobacion,
orden_compra.estatus as oc_estatus,orden_compra.monto_financiar,orden_compra.monto_aprobado,
(SELECT COUNT(detalle_pedido.pedido) FROM detalle_pedido where detalle_pedido.pedido=pedido.folio) as articulos,
concat_ws(' ',cliente.nombre,cliente.apellido_paterno,cliente.apellido_materno) as cliente_nombre, cliente.numero_empleado
FROM pedido
inner join orden_compra on pedido.fdp_credito_nomina_folio=orden_compra.folio
inner join plazo on plazo.clave = pedido.payment_terms
left join cliente on pedido.cliente=cliente.clave
LEFT JOIN empresa ON pedido.empresa = empresa.clave 
LEFT JOIN usuario_tienda ON pedido.vendedor = usuario_tienda.clave
$condicion";

$resultado= mysql_query($query,$conexion);
$odd = false;
$gran_total = 0;
while ($row = mysql_fetch_array($resultado)){ 

$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow",fechamy2mx($row['fecha']));
$objPHPExcel->getActiveSheet()->SetCellValue("B$xrow",utf8_encode($row['nombre_empresa']));
$objPHPExcel->getActiveSheet()->SetCellValue("C$xrow",$row['folio']);
$objPHPExcel->getActiveSheet()->SetCellValue("D$xrow",utf8_encode($row['cliente_nombre']));
$objPHPExcel->getActiveSheet()->SetCellValue("E$xrow",$row['numero_empleado']);
$objPHPExcel->getActiveSheet()->SetCellValue("F$xrow",$row['monto_financiar']);
$objPHPExcel->getActiveSheet()->SetCellValue("G$xrow",$row['fdp_plazo']);
$objPHPExcel->getActiveSheet()->SetCellValue("H$xrow",$row['fdp_periodo']);
$objPHPExcel->getActiveSheet()->SetCellValue("I$xrow",$row['fdp_periodo_monto']);
$objPHPExcel->getActiveSheet()->SetCellValue("J$xrow",$row['articulos']);
$objValidation = $objPHPExcel->getActiveSheet()->getCell('K'.$xrow)->getDataValidation();
   $objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
   $objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
   $objValidation->setAllowBlank(false);
   $objValidation->setShowInputMessage(true);
   $objValidation->setShowErrorMessage(true);
   $objValidation->setShowDropDown(true);
   $objValidation->setErrorTitle('Error');
   $objValidation->setError('El valor no esta en la lista');
   $objValidation->setPromptTitle('Elige de la lista');
   $objValidation->setPrompt('Elige un valor de la lista');
   $objValidation->setFormula1('"SI,NO"');
$objPHPExcel->getActiveSheet()->SetCellValue("L$xrow",nocero($row['monto_aprobado']));
$estatus = '';
switch ($row['oc_estatus']) {
			
				case '0' : $estatus = 'Pendiente Aprobacion'; break;
				case '1' : $estatus =  'Aprobada'; break;
				case '2' : $estatus =  'No Aprobada'; break;
				case '3' : $estatus =  'Utilizada'; break;
				case '4' : $estatus =  'Cancelada'; break;
				case '9' : $estatus =  'Vencida'; break;
			   }
$objPHPExcel->getActiveSheet()->SetCellValue("M$xrow",$estatus);
$objPHPExcel->getActiveSheet()->getStyle("K$xrow:L$xrow")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED); 
$xrow++;
}
//$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow",$query);
$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
// Guardar como excel 2007
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); //Escribir archivo

// Establecer formado de Excel 2007
header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

// nombre del archivo
header('Content-Disposition: attachment; filename="orden_de_compra.xlsx"');

//forzar a descarga por el navegador
$objWriter->save('php://output');


/*

			 
		  $html .= '

		  <tr class="texto" valign="top" bgcolor="#e5e5e2">
            <td colspan="12" align="left"><strong>Totales</strong></td>
            <td align="right">'.number_format($gran_total,2).'</td>
          </tr>

*/

?>