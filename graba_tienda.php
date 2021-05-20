<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=17;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este m&oacute;dulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
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
</head>

<body>
<div id="container">
	<? $tit='Administrar Tiendas'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_tienda.php';

		$usuario=$_SESSION['usr_valido'];
		$autorizado=op_aut($modulo);
		include('../conexion.php');

		$error=FALSE;

		// extrae variables del formulario
		$tienda=$_POST['tienda'];
		
		$tienda_service=$_POST['tienda_service']+0;
		if (empty($tienda)) $tienda=$_GET['tienda'];
		$listas_precios_garantias_extendidas=$_POST['listas_precios_garantias_extendidas']+0;
		$nombre=$_POST['nombre'];
		$activa=$_POST['activa']+0;
		$login=$_POST['login'];
		$marcas_omitidas=$_POST['marcas'];
		$c1 = $_POST['c1'];
		$c2 = $_POST['c2'];
		$c3 = $_POST['c3'];
		$rm_local = $_POST['rm_local'];
		$store_location = $_POST['store_location']+0;
		$store_location_rm08 = $_POST['store_location_rm08'];
		$entrega_dom_tienda = $_POST['entrega_dom_tienda']+0;
		$publico_general = $_POST['publico_general']+0;
		$empresa_asociada = $_POST['empresa_asociada']+0;
		$pagos_pinpad = $_POST['pagos_pinpad']+0;
		$modo_pinpad = $_POST['modo_pinpad'];
		$puerto_pinpad = $_POST['puerto_pinpad'];
		$descuentos_permitidos = $_POST['descuentos_permitidos'];
		$po_number = $_POST['po_number'];
		$payer_rg = $_POST['payer_rg'];
		$po_method = $_POST['po_method'];
		$cliente_sap = $_POST['cliente_sap'];


		$queryval="SELECT planta,loc  FROM planta WHERE  clave='$rm_local'";
		$resultadoval= mysql_query($queryval,$conexion);
		$rowval = mysql_fetch_array($resultadoval);
		$rowno=mysql_num_rows($resultadoval);
		$storerm= $rowval['planta'];
		$storeloc= $rowval['loc'];
		

		if (!empty($tienda)) { 
			$estatus=1;
			$original=$tienda;

			$query = "UPDATE tienda SET nombre='$nombre',
										 login='$login',
										 c1='$c1',
										 c2='$c2',
										 c3='$c3',
										 rm_local='$storerm',
										 store_location='$storeloc',
										 store_location_rm08='$storeloc',
										 entrega_dom_tienda=$entrega_dom_tienda,
										 publico_general=$publico_general,
										 empresa_asociada=$empresa_asociada,
										 pagos_pinpad=$pagos_pinpad,
										 modo_pinpad='$modo_pinpad',
										 puerto_pinpad='$puerto_pinpad',
										 descuentos_permitidos='$descuentos_permitidos',
										 po_number='$po_number',
										 payer_rg='$payer_rg',
										 po_method='$po_method',
										 cliente_sap='$cliente_sap',
										 tienda_service='$tienda_service',
										 listas_precios_garantias_extendidas='$listas_precios_garantias_extendidas',
										 activa=$activa,
										 act=1-act
										 WHERE clave=$tienda";
			$resultado= mysql_query($query,$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>0) {
				$mensaje='Se actualiz&oacute el registro...';
			} else { 
				$error=TRUE; $mensaje='ERROR<br>No se actualiz&oacute; el registro...'.$query; $link='javascript:history.go(-1);'; 
			}

		} else {  // si no es registro editado autorizado
		
			// checar que no exista login
				
			$resultado= mysql_query("SELECT * FROM tienda WHERE clave!='$clave' AND login='$login'",$conexion);
			$totres = mysql_num_rows ($resultado);
		
			if ($totres>0) {
				  $mensaje.='<b>ERROR</b><br>Ya existe una tienda con el login: '.$login;
				  $link='javascript:history.go(-1);';
				  $rotulo='Regresar';
				  $error = true;
			} else {
				  $query = "INSERT tienda (nombre,
										   login,
										   c1, c2, c3,
										   rm_local,
										   store_location,
										   store_location_rm08,
										   entrega_dom_tienda,
										   publico_general,
										   empresa_asociada,
										   pagos_pinpad,
										   modo_pinpad,
										   puerto_pinpad,
										   descuentos_permitidos,
										   po_number,
										   payer_rg,
										   po_method,
										   cliente_sap,
										   tienda_service,
										   listas_precios_garantias_extendidas,
										   activa)
								  VALUES ('$nombre',
										  '$login',
										  '$c1', '$c2', '$c3',
										  '$storerm',
										  '$storeloc',
										  '$storeloc',
										  $entrega_dom_tienda,
										  $publico_general,
										  $empresa_asociada,
										  $pagos_pinpad,
										  '$modo_pinpad',
										  '$puerto_pinpad',
										  '$descuentos_permitidos',
										  '$po_number',
										  '$payer_rg',
										  '$po_method',
										  '$cliente_sap',
										  '$tienda_service',
										  '$listas_precios_garantias_extendidas',
										  $activa)";
				  $resultado= mysql_query($query,$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  $tienda = $new_id;
				  if     ($reg>0) $mensaje='Se agreg&oacute; un nuevo registro...';
				  else   { $error=TRUE; $mensaje='ERROR<br>No se agreg&oacute; el registro...'; $link='javascript:history.go(-1);'; }
			}			  

		} 
	 	if (!$error) {  /// actualizar relacion de marcas omitidas por tienda
			$resultado = mysql_query("DELETE FROM marca_omitida WHERE tienda = $tienda");
			$op=explode(',',$marcas_omitidas);
			for ($i=0; $i<=count($op)-2; $i++) {
			  $clavemar=trim($op[$i]);
			  $resMAR= mysql_query("INSERT marca_omitida (tienda, marca) VALUES ($tienda, $clavemar)",$conexion);
			}		
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