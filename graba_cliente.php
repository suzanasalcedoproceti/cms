<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=8;
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
	<? $tit='Administrar Clientes'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_cliente.php';

		$usuario=$_SESSION['usr_valido'];
		$cliente = $_POST['cliente'];
		include('../conexion.php');

		$error=FALSE;
		
		// extrae variables del formulario
		
		$nombre =$_POST['nombre'];
		$apellido_paterno =$_POST['apellido_paterno'];
		$apellido_materno =$_POST['apellido_materno'];
		
		$numero_empleado=$_POST['numero_empleado'];
		$invitados_disponibles =$_POST['invitados_disponibles']+0;
		$orden=$_POST['orden']+0;
		if (!empty($cliente) && $nombre && $apellido_paterno) {  

			$pe_disponibles = $_POST['pe_disponibles'];
			$query = "UPDATE cliente SET nombre='$nombre', apellido_paterno='$apellido_paterno', apellido_materno='$apellido_materno',
						 numero_empleado='$numero_empleado',
						 invitados_disponibles=$invitados_disponibles,
						 pe_disponibles = $pe_disponibles,
						 act=1-act
						 WHERE clave=$cliente";
			$resultado= mysql_query($query,$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>0) $mensaje='Se actualizó el registro...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; }

				if (!$error) {
					
					// ahora se tiene el dato directo en cliente.pe_disponibles
					/*
					// actualizar precios especiales disponibles (solo empleados whirpool)
					$resultado= mysql_query("SELECT empresa FROM cliente WHERE clave=$cliente",$conexion);
					$row = mysql_fetch_array($resultado);
					$empresa=$row['empresa'];
					$resEMP= mysql_query("SELECT empresa_whirlpool FROM empresa WHERE clave='$empresa'",$conexion);
					$rowEMP= mysql_fetch_array($resEMP); 
					if ($rowEMP['empresa_whirlpool']) {
					
						$pe_disponibles = $_POST['pe_disponibles'];
	
						// obtener datos de configuracion
						$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
						$rowCFG = mysql_fetch_array($resultadoCFG);
					
						$limite_precios_especiales = $rowCFG['limite_precios_especiales']+0;
						$cantidad_comprada = $limite_precios_especiales - $pe_disponibles;
						if ($cantidad_comprada<0) $cantidad_comprada = 0;
						
						$ano = date("Y");
						$resultadoPRS = mysql_query("SELECT 1 FROM precios_especiales WHERE ano = '$ano' AND cliente = $cliente",$conexion);
						$encPRS = mysql_num_rows($resultadoPRS);
						if ($encPRS>0) {
							$resultadoPRS = mysql_query("UPDATE precios_especiales SET cantidad = $cantidad_comprada, act=1-act WHERE ano = '$ano' AND cliente = $cliente LIMIT 1",$conexion);
						} else {
							if ($cantidad_comprada>0) {
								$resultadoPRS = mysql_query("INSERT INTO precios_especiales (ano, cliente, cantidad) VALUES ('$ano',$cliente,$cantidad_comprada)",$conexion);
							}
							
						}

						
					} // empresa whirlpool
					*/
					
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
