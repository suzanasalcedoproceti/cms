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
	<? $tit='Administrar Clientes de Proyectos'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_cliente_proyectos.php';

		$usuario=$_SESSION['usr_valido'];
		$cliente = $_POST['cliente']+0;
		include('../conexion.php');

		$error=FALSE;
		
		// extrae variables del formulario
		
		$nombre =$_POST['nombre'];
		if ($nombre=='') return;
		$apellido_paterno = $_POST['apellido_paterno'];
		$apellido_materno = $_POST['apellido_materno'];
		
		$email=$_POST['email'];
		$password=$_POST['password'];
		$empresa=$_POST['empresa']+0;
		
		$persona_moral = $_POST['persona_moral']+0;
		$fact_calle=$_POST['fact_calle'];
		$fact_exterior=$_POST['fact_exterior'];
		$fact_interior=$_POST['fact_interior'];
		$fact_colonia=$_POST['fact_colonia'];
		$fact_ciudad=$_POST['fact_ciudad'];
		$fact_estado=$_POST['fact_estado'];
		$fact_cp=$_POST['fact_cp'];
		$fact_telefono=$_POST['fact_telefono'];
		$fact_email=$_POST['fact_email'];
		$razon_social=$_POST['razon_social'];
		$rfc=$_POST['rfc'];
		$fecha_hoy=date("Y-m-d H:i:s");
		$activo = $_POST['activo']+0;
		$origen = 'cms';
		$solo_consultas = $_POST['solo_consultas']+0;
		
		$acceso_web = 1;
		// validar correo existente
		$resultado= mysql_query("SELECT 1 FROM cliente WHERE clave != $cliente AND email = '$email' ",$conexion);
		$totres = mysql_num_rows ($resultado);

		// validar si ya existe e correo en otros clientes, solo si se está modificando el correo de este cliente..
		// correo actual
		$resultado= mysql_query("SELECT email FROM cliente WHERE clave = $cliente",$conexion);
		$rowCHM = mysql_fetch_array($resultado);
		$correo_actual = $rowCHM['email'];
		$resultado= mysql_query("SELECT 1 FROM cliente WHERE clave != $cliente AND email = '$email' ",$conexion);
		$totresCHM = mysql_num_rows ($resultado);
		
		if ($totresCHM>0  && $correo_actual != $email) {
				$mensaje.='<b>ERROR</b><br>Ya existe un cliente registrado con el correo: <strong>'.$email.'</strong>';
				$link='javascript:history.go(-1);';
				$rotulo='Regresar';
				$subido=FALSE; 

		} else {

                if ($cliente<=0) {
				
					  	// obtener configuracion
					  	$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
					  	$rowCFG = mysql_fetch_array($resultadoCFG);					
						$tipo_cliente = 'P';
					
						// genera token (30 caracteres alfanuméricos)
						srand((double)microtime()*1000000);
						$token='';
						for ($i=1; $i<=30; $i++) {
							if (rand(0,1)==0) // letra
							  $token=$token.chr(rand(97,122));
							else // numero
							  $token=$token.chr(rand(48,57));
						}			
						
						if ($nombre && $apellido_paterno) {

							$agregar = true;
							if ($agregar) {							
						  
								$query = "INSERT cliente (empresa,
														   tipo,
														   solo_consultas,
														   nombre,
														   apellido_paterno,
														   apellido_materno,
														   email, 
														   password, 
														   token,
														   persona_moral,
														   fact_calle,
														   fact_exterior,
														   fact_interior,
														   fact_colonia,
														   fact_ciudad,
														   fact_estado,
														   fact_cp,
														   fact_telefono,
														   fact_email,
														   razon_social,
														   rfc,
														   fecha,
														   origen,
														   acceso_web,
														   activo
														   )
												  VALUES ($empresa, 
														   '$tipo_cliente',
														   $solo_consultas,
														   '$nombre',
														   '$apellido_paterno',
														   '$apellido_materno',
														   '$email', 
														   '$password', 
														   '$token',
														   $persona_moral,
														   '$fact_calle',
														   '$fact_exterior',
														   '$fact_interior',
														   '$fact_colonia',
														   '$fact_ciudad',
														   '$fact_estado',
														   '$fact_cp',
														   '$fact_telefono',
														   '$fact_email',
														   '$razon_social',
														   '$rfc',
														   '$fecha_hoy',
														   '$origen',
														   $acceso_web,
														   $activo
														)";
																	  
								$resultado= mysql_query($query,$conexion); 
								$reg= mysql_affected_rows();
								$new_id= mysql_insert_id();
								if ($reg <= 0) {
									$mensaje='ERROR<br>No se agregó el cliente'.mysql_error(); $link='javascript:history.go(-1);'; 
								} else { 
			
									$mensaje='Se agregó un nuevo cliente...';
								} // reg > 0
							} else {
							
								 $mensaje='ERROR<br>No se agregó el cliente; el número de empleado ya estaba repetido..'; $link='javascript:history.go(-1);'; 
							
							}
							
						} else {
						
							$mensaje='ERROR<br>No se agregó el cliente...'; $link='javascript:history.go(-1);'; 
						
						}
				} else { // !empty cliente
				
				  if ($nombre && $apellido_paterno) {
				  
					  $query = "UPDATE cliente SET 
								   empresa=$empresa,
								   solo_consultas = $solo_consultas,
								   password='$password',
								   nombre='$nombre',
								   apellido_paterno='$apellido_paterno',
								   apellido_materno='$apellido_materno',
								   pers_rfc='$pers_rfc',
								   email='$email', 
								   persona_moral=$persona_moral,
								   fact_calle='$fact_calle',
								   fact_exterior='$fact_exterior',
								   fact_interior='$fact_interior',
								   fact_colonia='$fact_colonia',
								   fact_ciudad='$fact_ciudad',
								   fact_estado='$fact_estado',
								   fact_cp='$fact_cp',
								   fact_telefono='$fact_telefono',
								   fact_email='$fact_email',
								   razon_social='$razon_social',
								   rfc='$rfc',
								   activo=$activo,									  
								   act=1-act
							 WHERE clave=$cliente LIMIT 1";
					
					  $resultado= mysql_query($query,$conexion);
	
					  $reg= mysql_affected_rows();
					  if ($reg > 0) $mensaje='Se actualizó el Cliente...';
					  else { $mensaje='ERROR<br>No se actualizó el Cliente'; $link='javascript:history.go(-1);'; }
				  } else {
				     $mensaje='ERROR<br>No se actualizó el Cliente...'; $link='javascript:history.go(-1);';
				  }
				  
				} // empty

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