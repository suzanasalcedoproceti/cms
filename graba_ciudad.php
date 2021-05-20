<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=9;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
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
	<? $tit='Administrar Ciudades'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_ciudad.php';

		$usuario=$_SESSION['usr_valido'];
		include('../conexion.php');
	
		$error=FALSE;

		// comenzar transaccción	
		$resultado = mysql_query("SET AUTOCOMMIT = 0");
		$resultado = mysql_query("START TRANSACTION");
		
		// extrae variables del formulario
		$ciudad=$_POST['ciudad'];
		if (empty($ciudad)) $ciudad=$_GET['ciudad'];
		$estado=$_POST['estado'];
		$nombre=$_POST['nombre'];
		$tipo_producto=$_POST['tipo_producto'];
		$tipo_entrega=$_POST['tipo_entrega'];
		$sku=$_POST['sku'];
		$sucursal=$_POST['sucursal']+0;
		$cobertura=$_POST['cobertura']+0;
		$trans_zone=$_POST['trans_zone'];
		$purch_no_c = $_POST['purch_no_c'];
		$envio_sin_costo = $_POST['envio_sin_costo']+0;
		
		$rm_origen = $_POST['rm_origen'];
		$entrega_rm_dom = $_POST['entrega_rm_dom']+0;
		$entrega_rm_ocu = $_POST['entrega_rm_ocu']+0;
		$sku_rm_dom = $_POST['sku_rm_dom'];
		$sku_rm_ocu = $_POST['sku_rm_ocu'];
		$sucursal_rm_ocu = $_POST['sucursal_rm_ocu']+0;

		$rs_origen = $_POST['rs_origen'];
		$entrega_rs_dom = $_POST['entrega_rs_dom']+0;
		$entrega_rs_ocu = $_POST['entrega_rs_ocu']+0;
		$sku_rs_dom = $_POST['sku_rs_dom'];
		$sku_rs_ocu = $_POST['sku_rs_ocu'];
		$sucursal_rs_ocu = $_POST['sucursal_rs_ocu']+0;


		if (!empty($ciudad)) {
			$resultadoClave = mysql_query("SELECT * FROM ciudad_planta where ciudad=$ciudad and tipo_producto='$tipo_producto' and tipo_entrega='tipo_entrega' LIMIT 1",$conexion);
			$enc= mysql_num_rows($resultadoClave);
			if($enc>0)
			{
				$rowCdPlanta = mysql_fetch_array($resultadoClave);
				$claveCdPlanta = $rowCdPlanta['clave'];
				$resultado= mysql_query("UPDATE ciudad_planta SET sku='$sku',
				                                           sucursal='$sucursal',
				                                           cobertura='$cobertura'
															 WHERE clave=$claveCdPlanta",$conexion);

				$reg= mysql_affected_rows();
				if   ($reg>0) $mensaje='Se actualizó el registro...';
				else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'.mysql_error(); $link='javascript:history.go(-1);'; }

			}
			else
			{
			  $resultado= mysql_query("INSERT ciudad_planta (ciudad,
				                                          tipo_producto,
														  tipo_entrega,
														  cobertura,
														  sku,
														  sucursal
														  )
												  VALUES ($ciudad,
												          '$tipo_producto',
														  '$tipo_entrega',
														  $cobertura,
														  '$sku',
														  $sucursal
														  )",$conexion); 
			  $reg= mysql_affected_rows();
			  $new_id= mysql_insert_id();
			  if     ($reg>0) $mensaje='Se actualizó el registro...';
			  else   { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'.mysql_error(); $link='javascript:history.go(-1);'; }
			}
		} else {  // si no es registro editado 

			  $resultado= mysql_query("INSERT ciudad (estado,
				                                          nombre,
														  trans_zone,
														  purch_no_c,
														  envio_sin_costo,
														  rm_origen,
														  entrega_rm_dom,
														  entrega_rm_ocu,
														  sku_rm_dom,
														  sku_rm_ocu,
														  sucursal_rm_ocu,
														  
														  rs_origen,
														  entrega_rs_dom,
														  entrega_rs_ocu,
														  sku_rs_dom,
														  sku_rs_ocu,
														  sucursal_rs_ocu
														  )
												  VALUES ('$estado',
												          '$nombre',
														  '$trans_zone',
														  '$purch_no_c',
														  $envio_sin_costo,
														  '$rm_origen',
														  $entrega_rm_dom,
														  $entrega_rm_ocu,
														  '$sku_rm_dom',
														  '$sku_rm_ocu',
														  $sucursal_rm_ocu,

														  '$rs_origen',
														  $entrega_rs_dom,
														  $entrega_rs_ocu,
														  '$sku_rs_dom',
														  '$sku_rs_ocu',
														  $sucursal_rs_ocu
														  )",$conexion); 
	
			  $reg= mysql_affected_rows();
			  $new_id= mysql_insert_id();
			  if     ($reg>0 AND empty($ciudad)) $mensaje='Se agregó un nuevo registro...';
			  else   { $error=TRUE; $mensaje='ERROR<br>No se agregó el registro...'.mysql_error(); $link='javascript:history.go(-1);'; }
			  
			  

		}  // nuevo
			

		// revisar si hubo error o no  
		if ($error) mysql_query("ROLLBACK"); 
		else mysql_query("COMMIT");
				
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
