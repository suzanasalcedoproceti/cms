<?php
// script para generar formato de pedido CEP (Pago Directo Banorte)
//include('ajax_ini.php'); 
include("../conexion.php");

ini_set('display_errors', 1);
//////////////////////////////
// OBTENER DATOS
//////////////////////////////
$folio = mysql_real_escape_string($_POST['folio']);
$token = mysql_real_escape_string($_POST['token']);
if (!$folio) $folio = mysql_real_escape_string($_GET['folio']);
if (!$token) $token = mysql_real_escape_string($_GET['token']);


generar_garantia_pdf($folio, $token, 'pdf');

function generar_garantia_pdf($folio, $token, $output = 'pdf', $filename = ''){
	include("lib.php");
	include("num2letra.php");
	include_once("../conexion.php");
	

	$resultado = mysql_query("SELECT * FROM garantia WHERE folio = $folio AND token = '$token'");
	
	$row = mysql_fetch_array($resultado);
	$modelo = $row['modelo'];
	$pedido = $row['pedido'];
	$estatus = $row['estatus'];
	$numero_serie = $row['numero_serie'];
	$aplica_para = $row['aplica_para'];
	if ($row['origen']=='web') $txt_origen = 'ONLINE';
	if ($row['origen']=='pos') $txt_origen = 'POS';

	if (mysql_num_rows($resultado) <= 0 ) {
		return;
		exit;
	}

	$modelos_previos = array('RPLUSLAVYSEC', 'RPLUSESTUFA', 'RPLUSREFRIS');
	if( in_array($row['modelo'], $modelos_previos)){
		return generar_garantia_pdf_previo($folio, $token);
	}

	$resultadoP = mysql_query("SELECT nombre, terminos_garantia, es_garantia,subcategoria FROM producto WHERE modelo = '$modelo'");
	$rowP = mysql_fetch_array($resultadoP);

	$resultadoPED = mysql_query("SELECT nombre, pers_calle, pers_exterior, pers_interior, pers_colonia, pers_ciudad, pers_estado, pers_cp,
										fact_calle, fact_exterior, fact_interior, fact_colonia, fact_ciudad, fact_estado, fact_cp,
										envio_nombre, envio_calle, envio_exterior, envio_interior,
										pers_telefono, fact_telefono, envio_telefono_casa, fecha, tienda FROM pedido WHERE folio = $pedido");
	$rowPED = mysql_fetch_array($resultadoPED);
	$tienda = $rowPED['tienda'];

	$fecha = fechamy2mx($row['fecha']);
	$fecha_ini = fechamy2mx($row['inicio_garantia']);
	$fecha_fin = fechamy2mx($row['fin_garantia']);

	if ($fecha_ini=='00/00/0000') {
		$fecha_ini = $fecha;				// inicia el día de la compra
		$anos_garantia = $rowP['es_garantia']+0;
		$an_ini = substr($fecha_ini,6,4)+$anos_garantia;
		$fecha_fin = substr($fecha_ini,0,6).$an_ini;
	}
	$folio_pedido_largo = substr($rowPED['fecha'],2,2).substr($rowPED['fecha'],5,2).substr($rowPED['fecha'],8,2).$pedido;


	$calle = trim(strtoupper($rowPED['pers_calle']." ".$rowPED['pers_exterior']." ".$rowPED['pers_interior']));
	if (!$calle) $calle = trim(strtoupper($rowPED['fact_calle']." ".$rowPED['fact_exterior']." ".$rowPED['fact_interior']));
	if (!$calle) $calle = strtoupper($rowPED['envio_calle']." ".$rowPED['envio_exterior']." ".$rowPED['envio_interior']);

	$tel  = strtoupper($rowPED['pers_telefono']);
	if (!$tel) $tel = strtoupper($rowPED['fact_telefono']);
	if (!$tel) $tel = strtoupper($rowPED['envio_telefono_casa']);

	$colonia = trim(strtoupper($rowPED['pers_colonia']));
	if (!$colonia) $colonia = trim(strtoupper($rowPED['fact_colonia']));

	$ciudad  = strtoupper($rowPED['pers_ciudad']);
	if (!$ciudad) $ciudad = strtoupper($rowPED['fact_ciudad']);

	$estado  = strtoupper($rowPED['pers_estado']);
	if (!$estado) $estado = strtoupper($rowPED['fact_estado']);

	$cp  = strtoupper($rowPED['pers_cp']);
	if (!$cp) $cp = strtoupper($rowPED['fact_cp']);


	$querydp = "SELECT marca_nombre, precio_empleado FROM detalle_pedido WHERE pedido = $pedido AND modelo = '$modelo'";
	$resultadoDP = mysql_query($querydp);
	$rowDP = mysql_fetch_array($resultadoDP);
	$subcategoria = '';
	if ($tienda==29||$tienda==30) $subcategoria = $rowDP['marca_nombre'];

	$resultadoPNom = mysql_query("select m.nombre from producto p  inner join marca m on p.marca=m.clave where trim(p.modelo)= '$aplica_para'");
	$rowPNom = mysql_fetch_array($resultadoPNom);
	///////////////////////////////////////////////////////////////////////////
	// armar formato PDF
	///////////////////////////////////////////////////////////////////////////


	require('fpdf.php');

	$pdf = new FPDF();
	$pdf->AddPage('','legal');
	if(!in_array($tienda, array(29,30))) 
	{
	$pdf->Image('D:/inetpub/wwwroot/admin/images/misc/fondo_garantia.jpg',0,0,216);
	}
	else
	{
		$pdf->Image('D:/inetpub/wwwroot/admin/images/misc/fondo_garantia_service.jpg',0,0,216);
	}



	//$estatus = 'CANCELADO';	
	if($estatus == 'CANCELADO'){	
		$pdf->SetFont('Arial','B',25);
		$pdf->Text(110, 80, $estatus);
		$pdf->SetTextColor(128,128,128);
	}

     $pdf->SetFont('Arial','B',15);
	//$pdf->SetXY(20,76);
	//$pdf->Cell(39,10,'TW',0,1,'L');

	$pdf->SetXY(49,63);
	//$pdf->SetTextColor(255,0,0);
	$pdf->SetFont('Arial','B',15);
	$pdf->Cell(40,10,$folio_pedido_largo);

	$pdf->SetXY(58,76);
	//$pdf->SetTextColor(255,0,0);
	$pdf->SetFont('Arial','B',15);
	$pdf->Cell(40,10,$folio);

	if(!in_array($tienda, array(29,30))) {
	$pdf->SetXY(154,76);
	//$pdf->SetTextColor(255,0,0);
	$pdf->SetFont('Arial','B',15);
	$pdf->Cell(40,10,$subcategoria);
	}
	//$pdf->SetTextColor(0,0,0);
	$pdf->SetXY(58,80);
	$pdf->SetFont('Arial','',7);
	//$pdf->Cell(40,10,$row['token']);

	// nombre del cliente
	$pdf->SetFont('Arial','',14);
	session_start();
	$nombre = strtoupper($rowPED['nombre']);
	if(isset($_SESSION['tienda_service'])){
		if(!$_SESSION['tienda_service']){
			$nombre = strtoupper($rowPED['envio_nombre']);
		}
	}
	$pdf->SetXY(20,103);
	$pdf->Cell(40,10,substr($nombre,0,58));

	$pdf->SetXY(20,111);
	$pdf->Cell(40,10,substr($nombre,58,58));


	$pdf->SetXY(20,130);
	$pdf->MultiCell(86,5,substr($calle,0,80),0,2);

	$pdf->SetXY(110,130);
	$pdf->MultiCell(86,5,substr($colonia,0,80),0,2);


	$pdf->SetXY(20,143);
	$pdf->Cell(86,10,$tel,0);

	$pdf->SetXY(110,143);
	$pdf->Cell(86,10,$ciudad,0);

	$pdf->SetXY(110,158);
	$pdf->Cell(41,10,$estado,0);

	$pdf->SetXY(154,158);
	$pdf->Cell(41,10,$cp,0);

	$pdf->SetXY(20,195);
	$pdf->Cell(86,7,$rowPNom['nombre'],0);


	$pdf->SetXY(110,195);
	$pdf->Cell(20,7,substr($row['fecha'],8,2),0);

	$pdf->SetXY(142,195);
	$pdf->Cell(20,7,substr($row['fecha'],5,2),0);

	$pdf->SetXY(175,195);
	$pdf->Cell(20,7,substr($row['fecha'],0,4),0);

	$pdf->SetXY(20,210);
	$pdf->Cell(86,7,$aplica_para,0);

	$pdf->SetXY(20,224);
	$pdf->Cell(86,7,$numero_serie,0);

	if(!in_array($tienda, array(29,30))) {
		$precio = (int) $rowDP['precio_empleado'];

		$pdf->SetXY(24,254);
		$pdf->Cell(46,7,$precio,0,1,'R');
	}
	$y = 254;

	if(!in_array($tienda, array(29,30))) {
		if ($rowP['es_garantia']==1) {
			$pdf->SetXY(119,$y);
			$pdf->Cell(7,7,'X',0);
		}
		if ($rowP['es_garantia']==2) {
			$pdf->SetXY(141,$y);
			$pdf->Cell(7,7,'X',0);
		}
		if ($rowP['es_garantia']==3) {
			$pdf->SetXY(163,$y);
			$pdf->Cell(7,7,'X',0);
		}
		if ($rowP['es_garantia']==4) {
			$pdf->SetXY(184,$y);
			$pdf->Cell(7,7,'X',0);
		}
	}else{
		
		if ($rowP['es_garantia']==1) {
			$pdf->SetXY(22,$y);
			$pdf->Cell(7,7,'X',0);
		}
		if ($rowP['es_garantia']==2) {
			$pdf->SetXY(44,$y);
			$pdf->Cell(7,7,'X',0);
		}
		if ($rowP['es_garantia']==3) {
			$pdf->SetXY(66,$y);
			$pdf->Cell(7,7,'X',0);
		}
		if ($rowP['es_garantia']==4) {
			$pdf->SetXY(87,$y);
			$pdf->Cell(7,7,'X',0);
		}

	}

	$fec_ini = substr($fecha_ini,0,2).substr($fecha_ini,3,2).substr($fecha_ini,8,2);

	$pdf->SetXY(62,284);
	$pdf->Cell(35,7,$fecha_ini,0);

	$pdf->SetXY(160,284);
	$pdf->Cell(35,7,$fecha_fin,0);



	$pdf->SetFont('Arial','',14);
	$pdf->SetXY(52,290);
	$pdf->Cell(112,10,'ADQUIRIDA EN WHIRLPOOL '.$txt_origen,0,1,'C');


	//$pdf->MultiCell(120,4,$rowP['terminos_garantia'],0,'L');
	$pdf->AddPage('','legal');
	if(!in_array($tienda, array(29,30))) {
	$pdf->Image('D:/inetpub/wwwroot/admin/images/misc/fondo_garantia2.jpg',0,0,216);
	}
	else
	{
		$pdf->Image('D:/inetpub/wwwroot/admin/images/misc/fondo_garantia2_service.jpg',0,0,216);
	}

	

	if($output == 'pdf'){
		$pdf->Output();
	}else{
		return $pdf->Output($filename,'S');
	}


}


?>
