<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=4;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
    include('../conexion.php');
//	include("_checa_vars.php");
	
	$clave=$_POST['clave'];
	if (empty($clave)) $clave=$_GET['clave'];
	// obtener datos de producto para edición	
    $query = "SELECT bitacora FROM producto WHERE clave=$clave";
	$resultado = mysql_query($query,$conexion);
	$row = mysql_fetch_array($resultado);
	$mensaje = str_replace(chr(10),'<br>',$row['bitacora']);
	// obtener crédito disponible de la agencia
	$link = "javascript:cerrar();";
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Panel de Control</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>

<script type="text/javascript">
 function cerrar() {
 	   parent.Shadowbox.close();
 }
</script>
</head>
<body class="body_sb">
   <form name="forma" id="forma" method="post">
   <input name="accion" id="accion" value="" type="hidden" />
   <? include("mensaje.php"); ?>
   <p><input name="desc" type="button" class="boton" onclick="parent.recarga(); parent.Shadowbox.close();" value=" Cerrar " id="desc" />   </p>

  </form>
  


</body>
</html>