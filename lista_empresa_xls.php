<?php
	date_default_timezone_set("America/Mexico_City");

    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('lib.php');
	$modulo=6;
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
   $texto = $_POST['texto'];
   $ftipo = $_POST['ftipo'];
   $estatus = $_POST['estatus'];
   if (!$texto) $texto = $_GET['texto'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='nombre';

   if ($ord=='nombre') $orden='ORDER BY nombre';
	if ($estatus == '') $condicion = " WHERE 1=1 ";
	if ($estatus == '1') $condicion = " WHERE estatus=1 ";
	if ($estatus == '0') $condicion = " WHERE estatus=0 ";

	// construir la condición de búsqueda

	if ($ftipo) $condicion .= " AND cliente_tipo_id = '$ftipo' ";

	//$condicion .= ($_SESSION['usr_service']==1) ? ' AND empresa_publica = 1 ' : '';

	if ($texto) $condicion.= " AND nombre LIKE '%$texto%' ";


	

							
		
	//  $condicion .= " AND pedido.estatus = 1 ";
//ajuntar la libreria excel
		require_once('phpExcel/Classes/PHPExcel.php');
	
$objPHPExcel = new PHPExcel(); //nueva instancia

$objPHPExcel->getProperties()->setTitle("Reporte Empresas Formas de Pago"); //titulo


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
$objPHPExcel->getActiveSheet()->SetCellValue("A1", "Reporte de Empresas - Formas de Pago");
$objPHPExcel->getActiveSheet()->mergeCells("A$fila:T$fila"); //unir celdas
$objPHPExcel->getActiveSheet()->setSharedStyle($titulo, "A$fila:T$fila"); //establecer estilo


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
$objPHPExcel->getActiveSheet()->getColumnDimension("N")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("O")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("P")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("R")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("S")->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension("T")->setAutoSize(true);


$xrow=2;
$objPHPExcel->getActiveSheet()->getStyle("A$xrow:T$xrow")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle("A$xrow:T$xrow")->getFill()->getStartColor()->setRGB('F2F2F2');  

$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow","Empresa");
$objPHPExcel->getActiveSheet()->SetCellValue("B$xrow","Tipo de Cliente");
$objPHPExcel->getActiveSheet()->SetCellValue("C$xrow","Clave");
$objPHPExcel->getActiveSheet()->SetCellValue("D$xrow","Tarjetas [Usadas/Sin usar]");
$objPHPExcel->getActiveSheet()->SetCellValue("E$xrow","Clientes");
$objPHPExcel->getActiveSheet()->SetCellValue("F$xrow","Lista de Precios WEB");
$objPHPExcel->getActiveSheet()->SetCellValue("G$xrow","Lista de Precios POS");
$objPHPExcel->getActiveSheet()->SetCellValue("H$xrow","CLABE");
$objPHPExcel->getActiveSheet()->SetCellValue("I$xrow","Cheque");
$objPHPExcel->getActiveSheet()->SetCellValue("J$xrow","CEP");
$objPHPExcel->getActiveSheet()->SetCellValue("K$xrow","ODC");
$objPHPExcel->getActiveSheet()->SetCellValue("L$xrow","Puntos");
$objPHPExcel->getActiveSheet()->SetCellValue("M$xrow","3 MSI");
$objPHPExcel->getActiveSheet()->SetCellValue("N$xrow","6 MSI");
$objPHPExcel->getActiveSheet()->SetCellValue("O$xrow","9 MSI");
$objPHPExcel->getActiveSheet()->SetCellValue("P$xrow","10 MSI");
$objPHPExcel->getActiveSheet()->SetCellValue("Q$xrow","12 MSI");
$objPHPExcel->getActiveSheet()->SetCellValue("R$xrow","18 MSI");
$objPHPExcel->getActiveSheet()->SetCellValue("S$xrow","24 MSI");
$objPHPExcel->getActiveSheet()->SetCellValue("T$xrow","Estatus");
$xrow++;   

 $query = "SELECT * FROM empresa $condicion $orden";

$resultado= mysql_query($query,$conexion);
$odd = false;
$gran_total = 0;
while ($row = mysql_fetch_array($resultado)){ 
				$empresa= $row['clave'];
			    $resCLI= mysql_query("SELECT 1 FROM cliente WHERE empresa=$empresa",$conexion);
			    $clientes = mysql_num_rows($resCLI);
			    
				$resTAR= mysql_query("SELECT 1 FROM tarjeta WHERE empresa=$empresa",$conexion);
			    $tarjetas = mysql_num_rows($resTAR);

                $resTAR= mysql_query("SELECT COUNT(*) AS tarjetas FROM tarjeta WHERE empresa='$empresa'",$conexion);
                $rowTAR= mysql_fetch_array($resTAR);

                $resTAR2= mysql_query("SELECT COUNT(*) AS tarjetas_usadas FROM tarjeta WHERE empresa='$empresa' AND cliente>0",$conexion);
                $rowTAR2= mysql_fetch_array($resTAR2);

                $resTAR3= mysql_query("SELECT COUNT(*) AS tarjetas_sinusar FROM tarjeta WHERE empresa='$empresa' AND cliente=0",$conexion);
                $rowTAR3= mysql_fetch_array($resTAR3);
                
                $tipo_cliente= $row['cliente_tipo_id'];
				        $resTC= mysql_query("SELECT nombre FROM cliente_tipo WHERE id='$tipo_cliente'",$conexion);
                $rowTC= mysql_fetch_array($resTC);

$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow",str_replace(',',' ',$row['nombre']));
$objPHPExcel->getActiveSheet()->SetCellValue("B$xrow", $rowTC['nombre']);
$objPHPExcel->getActiveSheet()->SetCellValue("C$xrow",$row['clave']);
$objPHPExcel->getActiveSheet()->SetCellValue("D$xrow",$rowTAR['tarjetas'].' ['.$rowTAR2['tarjetas_usadas'].'/'.$rowTAR3['tarjetas_sinusar'].']');
$objPHPExcel->getActiveSheet()->SetCellValue("E$xrow",$clientes);
$objPHPExcel->getActiveSheet()->SetCellValue("F$xrow",$row['lista_precios']);
$objPHPExcel->getActiveSheet()->SetCellValue("G$xrow",$row['lista_precios_pos']);

$objPHPExcel->getActiveSheet()->SetCellValue("H$xrow",($row['pago_deposito']==1)?"SI":"NO");
$objPHPExcel->getActiveSheet()->SetCellValue("I$xrow",($row['pago_cheque']==1)?"SI":"NO");
$objPHPExcel->getActiveSheet()->SetCellValue("J$xrow",($row['pago_cep']==1)?"SI":"NO");
$objPHPExcel->getActiveSheet()->SetCellValue("K$xrow",($row['pago_odc']==1)?"SI":"NO");
$objPHPExcel->getActiveSheet()->SetCellValue("L$xrow",($row['puntos']==1)?"SI":"NO");
$objPHPExcel->getActiveSheet()->SetCellValue("M$xrow",($row['msi03']==1)?"SI":"NO");
$objPHPExcel->getActiveSheet()->SetCellValue("N$xrow",($row['msi06']==1)?"SI":"NO");
$objPHPExcel->getActiveSheet()->SetCellValue("O$xrow",($row['msi09']==1)?"SI":"NO");
$objPHPExcel->getActiveSheet()->SetCellValue("P$xrow",($row['msi10']==1)?"SI":"NO");
$objPHPExcel->getActiveSheet()->SetCellValue("Q$xrow",($row['msi12']==1)?"SI":"NO");
$objPHPExcel->getActiveSheet()->SetCellValue("R$xrow",($row['msi18']==1)?"SI":"NO");
$objPHPExcel->getActiveSheet()->SetCellValue("S$xrow",($row['msi24']==1)?"SI":"NO");



$objVal = $objPHPExcel->getActiveSheet();
$objValidation = $objVal->getDataValidation();
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
$objVal->setDataValidation("H$xrow:S$xrow", $objValidation);


$estatus = '';
switch ($row['estatus']) {
			
				case '0' : $estatus = 'Inactiva'; break;
				case '1' : $estatus =  'Activa'; break;
				case '2' : $estatus =  'Pendiente Aprobacion'; break;
			   }
$objPHPExcel->getActiveSheet()->SetCellValue("T$xrow",$estatus);
$objPHPExcel->getActiveSheet()->getStyle("H$xrow:S$xrow")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED); 
$xrow++;
}
//$objPHPExcel->getActiveSheet()->SetCellValue("A$xrow",$query);
$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
// Guardar como excel 2007
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); //Escribir archivo

// Establecer formado de Excel 2007
header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

// nombre del archivo
header('Content-Disposition: attachment; filename="empresas_formas_pago.xlsx"');

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