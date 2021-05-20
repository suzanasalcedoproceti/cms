<? 
//
// CONTROL DE CAMBIOS
//
// Noviembre 2012 FARN;
//    Se agrego creacion de TXT para garantias.. y se agregó costo de envíos

    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('lib.php');
	$modulo=15;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
  include("../conexion.php");
  // obtener datos de configuración 
  $resultadoCFG = mysql_query("SELECT disponibilidad_venta, url FROM config WHERE reg = 1");
  $rowCFG = mysql_fetch_array($resultadoCFG);
  // la disponibilidad de venta ya se toma de la categoria, no de config
  //$disponibilidad_venta = $rowCFG['disponibilidad_venta']+0;

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
  <? $tit='Cambiar estatus de pedido a Pagado (PAGO DIRECTO CEP)'; include('top.php'); ?>
<div class="main">
      <p>
        <? 

		$link='principal.php';

		include('../conexion.php');

		$error=FALSE;
		$mensaje='';

		// obtener IP
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
		  $ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
		  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
		  $ip=$_SERVER['REMOTE_ADDR'];
		}

		
		// extrae variables del formulario
		extract ($_POST, EXTR_OVERWRITE);

		$pedido = mysql_real_escape_string($_POST['pedido']);
		$codigo_autorizacion = mysql_real_escape_string($_POST['codigo_autorizacion']);
		$mensaje_banco = mysql_real_escape_string($_POST['mensaje_banco']);
	
		$usuario = $_SESSION['usr_valido'];
		
		$txt_bitacora = "CMS: ".date("d/m/Y H:i:s")." Usr: ".$usuario." (".$_SESSION['ss_nombre'].") IP: ".$ip." | ";

		// checar que no se haya hecho antes (recargar)
		
		$query = "SELECT * FROM pedido WHERE folio = $pedido AND estatus = '4'";
		$resultado = mysql_query($query,$conexion);
		$enc = mysql_num_rows($resultado);
		if ($enc > 0) {
			$row = mysql_fetch_array($resultado);
			$clave_cliente = $row['cliente'];
			
			$fecha_pago = date("Y-m-d");
			$confirmo_pago = $_SESSION['ss_nombre'];
			
			$query = "UPDATE pedido SET estatus = '1', fecha_pago = '$fecha_pago', confirmo_pago = '$confirmo_pago',
							codigo_autorizacion = '$codigo_autorizacion',
							mensaje = '$mensaje_banco',
							mensaje_largo = '$mensaje_banco',
							pagado_cms = 1,
							detalle_pago_cms = CONCAT(detalle_pago_cms,'$txt_bitacora'),
							act = 1-act  
					  WHERE folio = $pedido 
						AND estatus = '4'
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
						
				} // if genera_sap

				// enviar mail
				if ($envia_mail) {

						// obtener nombre y correo del cliente 
						$cliente = $row['cliente'];
		
						$txt_formas_pago= '';
						if ($row['fdp_cep']>0) $txt_formas_pago = "Pago Directo: <strong>".number_format($row['fdp_cep'],2)."</strong> <br>";
						if ($row['fdp_puntos']>0) $txt_formas_pago .= "Puntos: <strong>".number_format($row['fdp_puntos'],2)."</strong><br>";
						if ($row['fdp_puntos_flex']>0) $txt_formas_pago .= "Puntos Flex: <strong>".number_format($row['fdp_puntos_flex'],2)."</strong><br>";
						if ($row['fdp_puntos_pep']>0) $txt_formas_pago .= "Puntos PEP: <strong>".number_format($row['fdp_puntos_pep'],2)."</strong><br>";

						$pagado = 1; // CEP CMS
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
