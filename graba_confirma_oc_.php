<?php
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('lib.php');
	$modulo=19;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}

	$error = "";
	$tienda = $_POST['tienda']+0;
	$fecha = $_POST['fecha'];
	$folio = $_POST['folio'];
	$folio_oc = $_POST['folio_oc'];
	$tipo = $_POST['tipo'];
	$buscar = $_POST['buscar']+0;
	$ord = $_POST['ord'];
	$empresa = $_POST['empresa'];
	$vendedor = $_POST['vendedor'];

	
	$condicion = "WHERE pedido.origen = 'pos' ";

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



 $query = "SELECT pedido.*, empresa.nombre AS nombre_empresa, usuario_tienda.nombre AS nombre_vendedor, orden_compra.aprobacion, orden_compra.confirmado,
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

include('../conexion.php');
$resultadoR= mysql_query($query,$conexion);
$confirmado = 0;
$sin_confirmar = 0;
$enviados = 0;
$sin_enviar = 0;
$pedidos_sin_enviar='';
while ($rowR = mysql_fetch_array($resultadoR)){
if ($rowR['oc_estatus']==1 && $rowR['aprobacion']=="SI" && $rowR['monto_aprobado']>=$rowR['monto_financiar'] ) {
$pedido = $rowR['folio'];
$query = "SELECT * FROM pedido WHERE folio = $pedido AND estatus = '0'";
		$resultado = mysql_query($query,$conexion);
		$enc = mysql_num_rows($resultado);
		if ($enc > 0) {
			$row = mysql_fetch_array($resultado);
			$clave_cliente = $row['cliente'];

			$query = "UPDATE pedido SET estatus = '1', 
							pagado_cms = 1,
							act = 1-act  
					  WHERE folio = $pedido 
						AND estatus = '0'
					";
			$resultado= mysql_query($query,$conexion);
			$reg = mysql_affected_rows();
			//$reg=1;
			if ($reg <= 0) { 
				$sin_confirmar++;
			} else {
				$confirmado++;

				$query = "UPDATE orden_compra SET estatus = 3, 
							confirmado = 1
					  WHERE folio = $pedido";
				$resultado= mysql_query($query,$conexion);
				//print_r($row);
				$data = ['folio' => $pedido, 'codigo_seguridad'=> $row['codigo_seguridad'] ];
				$data = json_encode($data);
				$folio_pedido = $pedido;
				//echo $data;
				$optional_headers = "Content-type: application/json";
				$response = json_decode(do_post_request('https://tiendawhirlpool.com/pos3/service/odc', $data, $optional_headers));
				//print_r($response);
				/*
				// generar TXT para SAP
				if ($genera_sap || 1) {
				
						include("../libprod.php");
						
						// obtener lista de precios general
						$lista_precios = get_listax($row['empresa'],$row['cliente']);
						// eliminar prefijo del campo "precio_"
						$lista_precios = strtoupper(trim(substr($lista_precios,7,10)));
						if ($lista_precios == 'WEB') $lista_precios = 'TE';
						
						$url_admin = '.';
						$url_tw = '..';
						include("../genera_sap.php");				
				
				} // if envia_sap
				*/

				// enviar mail
				if ($envia_mail) {

						$txt_formas_pago= '';
						if ($row['fdp_tdc']>0) $txt_formas_pago = "Tarjeta de Crédito: <strong>".number_format($row['fdp_tdc'],2)."</strong><br>";
						if ($row['fdp_puntos']>0) $txt_formas_pago .= "Puntos: <strong>".number_format($row['fdp_puntos'],2)."</strong><br>";
						if ($row['fdp_puntos_flex']>0) $txt_formas_pago .= "Puntos Flex: <strong>".number_format($row['fdp_puntos_flex'],2)."</strong><br>";
						if ($row['fdp_puntos_pep']>0) $txt_formas_pago .= "Puntos PEP: <strong>".number_format($row['fdp_puntos_pep'],2)."</strong><br>";


						// obtener nombre y correo del cliente 
						$cliente = $row['cliente'];
		
						$pagado = 1; // TDC
						// obtener formato a la variable texto_mail
						$texto_mail = file_get_contents("../mail/mail_pedido.html");
					    $texto_partida = file_get_contents("../mail/detalle_pedido.html");

						require('../phpmailer/class.phpmailer.php');
						$mail = new phpmailer();
						$mail->SetLanguage('es','../phpmailer/language/');
						$error_mail = '';
						include("../genera_mail_pedido.php");
						if($error_mail) {
							$sin_enviar++;
							$pedidos_sin_enviar.= $pedido."<br>";
							} else 
							{
								$enviados++;
							}		
			
				} // if envia_mail
				
				
			} // si se actualizo
		} else { 
			 	$sin_confirmar++;
 
		}				
 }
 else
 {
 	$sin_confirmar++;
 } 

}



			$mensaje = 'Se confirmaron '.$confirmado.' ordenes.<br> ';
			if($confirmado>0)
			{
				$mensaje .= 'Se enviaron '.$enviados.' correos.<br> No se enviaron '.$sin_enviar.' correos.<br>Pedidos sin enviar:<br>'.$pedidos_sin_enviar.'<br>';
			}
			$mensaje .='No se confirmaron '.$sin_confirmar.' ordenes';
function do_post_request($url, $data, $optional_headers = null){

 $params = array('http' => array(
             'method' => 'POST',
             'content' => $data
           ));
 if ($optional_headers !== null) {
   $params['http']['header'] = $optional_headers;
 }
 $ctx = stream_context_create($params);
 $fp = @fopen($url, 'rb', false, $ctx);
 if (!$fp) {
   throw new Exception("Problem with $url, $php_errormsg");
 }
 $response = @stream_get_contents($fp);
 if ($response === false) {
   throw new Exception("Problem reading data from $url, $php_errormsg");
 }
 return $response;
 
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
<script language="JavaScript">
  function descarta() {
   document.forma.action='principal.php';
   document.forma.submit();
  }

  function exportar_c() {
    document.forma.target = '_self';
    document.forma.action='lista_oc_xls_c.php';
    document.forma.buscar.value=1;
    document.forma.submit();
  document.forma.target = '_self';
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Confirmar Orden de Compra'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma" enctype="multipart/form-data">
      	<input type="hidden" name="tienda" value="<?php echo $_POST['tienda']; ?>">
      	<input type="hidden" name="fecha" value="<?php echo $_POST['fecha']; ?>">
      	<input type="hidden" name="folio" value="<?php echo $_POST['folio']; ?>">
      	<input type="hidden" name="folio_oc" value="<?php echo $_POST['folio_oc']; ?>">
      	<input type="hidden" name="tipo" value="<?php echo $_POST['tipo']; ?>">
      	<input type="hidden" name="buscar" value="<?php echo $_POST['buscar']; ?>">
      	<input type="hidden" name="ord" value="<?php echo $_POST['ord']; ?>">
      	<input type="hidden" name="empresa" value="<?php echo $_POST['empresa']; ?>">
      	<input type="hidden" name="vendedor" value="<?php echo $_POST['vendedor']; ?>">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td><b>Ordenes actualizadas</b></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><?php if ($error) echo $error; else echo $mensaje; ?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
          	<td colspan="2"><input name="descartar" type="button" class="boton" onclick="exportar_c();" value="Descargar archivo" />&nbsp;&nbsp;&nbsp;<input name="descartar" type="button" class="boton" onclick="descarta();" value="SALIR" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
