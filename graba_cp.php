<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=20;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	$nuevo = $_POST['nuevo'];

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
	<? $tit='Administrar Códigos Postales'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_cp.php';

		include('../conexion.php');
	
		$error=FALSE;

		// extrae variables del formulario
		$cp=$_POST['clave'];
		$estado=$_POST['estado'];
		$ciudad=$_POST['ciudad'];
		$trans_zone = $_POST['trans_zone'];
		$low_dom = $_POST['low_dom']+0;
		$low_ocu = $_POST['low_ocu']+0;
		$ltl_dom = $_POST['ltl_dom']+0;
		$ltl_ocu = $_POST['ltl_ocu']+0;
		$sku_low = $_POST['sku_low'];
		$sku_ltl = $_POST['sku_ltl'];
		$cedis_origen_ltl = $_POST['cedis_origen_ltl'];
		$sucursal_ocurre = $_POST['sucursal_ocurre']+0;

		if (!$nuevo) {   
			$query = "UPDATE cp SET estado='$estado',
									   ciudad='$ciudad',
									   trans_zone = '$trans_zone',
									   low_dom=$low_dom,
									   low_ocu=$low_ocu,
									   ltl_dom=$ltl_dom,
									   ltl_ocu=$ltl_ocu,
									   sku_low='$sku_low',
									   sku_ltl='$sku_ltl',
									   cedis_origen_ltl='$cedis_origen_ltl',
									   sucursal_ocurre=$sucursal_ocurre,
									   act=1-act
										 WHERE cp='$cp'";
										 		
			$resultado= mysql_query($query,$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>0) $mensaje='Se actualizó el registro...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'.$query; $link='javascript:history.go(-1);'; }

		} else { 
			
			// checar que no exista
			
			$resultadoC = mysql_query("SELECT * FROM cp WHERE cp = '$cp'");
			$enc = mysql_num_rows($resultadoC);
			if ($enc>0) {
				$error = TRUE;
				$mensaje = "ERROR<br>Ese CP ya existía";
				$link='javascript:history.go(-1)';

			} else {
				$query = "INSERT cp (cp,
									  estado,
									  ciudad,
									  trans_zone,
									  low_dom,
									  low_ocu,
									  ltl_dom,
									  ltl_ocu,
									  sku_low,
									  sku_ltl,
									  cedis_origen_ltl,
									  sucursal_ocurre)
							  VALUES ('$cp',
									  '$estado',
									  '$ciudad',
									  '$trans_zone',
									  $low_dom,
									  $low_ocu,
									  $ltl_dom,
									  $ltl_ocu,
									  '$sku_low',
									  '$sku_ltl',
									  '$cedis_origen_ltl',
									  $sucursal_ocurre)";
				$resultado= mysql_query($query,$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  if     ($reg>0) $mensaje='Se agregó un nuevo registro...';
				  else   { $error=TRUE; $mensaje='ERROR<br>No se agregó el registro...'.$query; $link='javascript:history.go(-1);'; }
				  
			}
		}  
			
				
      ?>
      </p>
      <p>&nbsp;</p>
      <? include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>
