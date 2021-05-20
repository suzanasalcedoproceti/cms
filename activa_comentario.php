<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=12;
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
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<?  
	   $inactivos = $_POST['inactivos'];
	   $comentario = $_POST['comentario'];
	   $accion = $_POST['accion'];
	?>
<form action="lista_comentario.php" method="post" name="brinca" id="brinca">
  <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>">
  <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>">
  <input name="inactivos" type="hidden" id="inactivos" value="<?= $inactivos; ?>">
</form>

<?
                include('../conexion.php');

                if (!empty($comentario)) {

                  $resultado= mysql_query("UPDATE comentario SET activo='$accion',
														         act=1-act
													 WHERE clave=$comentario",$conexion);

				  $reg= mysql_affected_rows();
				  if ($reg > 0)  {
				  
				    $mensaje='Se actualizó el Comentario...';
					
					echo '<script languaje="JavaScript">';
					echo '  document.brinca.submit(); ';
					echo '</script>';
				  
				  }

				  else { $mensaje='ERROR<br>No se actualizó el Comentario...'; $link='javascript:history.go(-1);'; }

				}
                mysql_close();
            ?>

  <table width="770" border="0" cellspacing="0" cellpadding="3" class="texto">
    <tr> 
      <td colspan="3">
<div align="right"></div></td>
    </tr>
    <tr> 
      <td><?= $mensaje; ?></td>
      <td>&nbsp;</td>
      <td>[ <a href="menu.php" class="texto">Regresar al Men&uacute;</a> ]</td>
    </tr>
  </table>
</body>
</html>