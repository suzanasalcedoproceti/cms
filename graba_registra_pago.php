<?
    if (!include('ctrl_acceso.php')) return;
return; // no se esta usando
	include('funciones.php');
	include('lib.php');
	$modulo=15;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
  include('../conexion.php');

  // obtener datos de configuración  (ya se toma de la categoria)
  /*$resultadoCFG = mysql_query("SELECT disponibilidad_venta FROM config WHERE reg = 1");
  $rowCFG = mysql_fetch_array($resultadoCFG);
  $disponibilidad_venta = $rowCFG['disponibilidad_venta']+0;
  */
  return; // no se usa este script, no está actualizado vs pago_cep

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
</head>

<body>
<div id="container">
  <? $tit='Cambiar estatus de pedido a Pagado (PAGOS CON TDC)'; include('top.php'); ?>
<div class="main">
      <p>
        <? 

		$link='principal.php';


		$error=FALSE;
		$mensaje='';
		
		// extrae variables del formulario
		extract ($_POST, EXTR_OVERWRITE);

		// checar que no se haya hecho antes (recargar)
		
		$query = "SELECT * FROM pedido WHERE folio = $pedido AND estatus = '0'";
		$resultado = mysql_query($query,$conexion);
		$enc = mysql_num_rows($resultado);
		if ($enc > 0) {
			$row = mysql_fetch_array($resultado);
			$clave_cliente = $row['cliente'];

			$query = "UPDATE pedido SET estatus = '1', 
							codigo_autorizacion = '$codigo_autorizacion',
							mensaje = '$mensaje_banco',
							mensaje_largo = '$mensaje_largo',
							pagado_cms = 1,
							act = 1-act  
					  WHERE folio = $pedido 
						AND estatus = '0'
					";
			$resultado= mysql_query($query,$conexion);
			$reg = mysql_affected_rows();
			if ($reg <= 0) { 
				$error=TRUE; $mensaje.='ERROR<br>No se actualizó el pedido...'; $link='javascript:history.go(-1);'; 
			} else {
				
				$mensaje.='Se actualizó el pedido...';
				
				$folio_pedido = $pedido;
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
		
						include("../genera_mail_pedido.php");
		
			
				} // if envia_mail
				
				
			} // si se actualizo
		} else { 
			$error=TRUE; $mensaje='ERROR<br>Ya se había cambiado el estatus del pedido...'; $link='lista_pedidos.php'; 
		}
		mysql_close();
				
      ?>
      </p>
      <p>&nbsp;</p>
      <? include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>
