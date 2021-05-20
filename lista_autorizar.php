<?
    if (!include('ctrl_acceso.php')) return;
   	include('funciones.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<link href="css/thickbox.css" rel="stylesheet" type="text/css" >
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/thickbox.js"></script>

<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='lista_promo.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_promo.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.promo.value = id;
    document.forma.action='borra_promo.php';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Autorización de Contenido'; include('top.php'); ?>
  <?
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='nombre';

   if     ($ord=='nombre') $orden='ORDER BY nombre';
   elseif ($ord=='fecha') $orden='ORDER BY fecha DESC';
   elseif ($ord=='caducidad') $orden='ORDER BY caducidad DESC';
   
   $hoy=date('Y-m-d');
   
   include('../conexion.php');
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><b>Tipo de contenido</b></td>
            <td nowrap="nowrap"><b>Descripci&oacute;n</b></td>
            <td nowrap="nowrap"><strong>Editor</strong></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
		  
				 $usuario=$_SESSION['usr_valido'];

	             $resUSU= mysql_query("SELECT * FROM usuario WHERE clave='$usuario'",$conexion);
				 $rowUSU= mysql_fetch_array($resUSU);
				 
                 $autorizar=explode(',',$rowUSU['autorizar']);
 				 
				 for ($i=0; $i<=count($autorizar)-2; $i++) {

					$tipo_info=trim($autorizar[$i]);
					
					$resMEN= mysql_query("SELECT * FROM menu WHERE clave=$tipo_info",$conexion);
		            while ($rowMEN = mysql_fetch_array($resMEN)) { 
					
						$tabla=$rowMEN['tabla'];
						
						$resREG= mysql_query("SELECT * FROM $tabla WHERE estatus=3",$conexion);
			            while ($rowREG = @mysql_fetch_array($resREG)) { 
						
							$editor=$rowREG['editor'];
							$resEDI= mysql_query("SELECT * FROM usuario WHERE clave='$editor'",$conexion);
				            $rowEDI= mysql_fetch_array($resEDI); 



		?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $rowMEN['opcion']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowREG[$rowMEN['titulo']]; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowEDI['nombre']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
               	<a href="<?= $rowMEN['edicion']; ?>.php?<?= $rowMEN['ncampo']; ?>=<?= $rowREG[$rowMEN['campo']]; ?>&autorizar=1"><img src="images/editar.png" alt="Revisar Registro" width="14" height="16" border="0" align="absmiddle" /></a>
	  			<a onclick="return confirm('¿Estás seguro que deseas\nBorrar el Registro?')" href="<?= $rowMEN['borrado']; ?>.php?<?= $rowMEN['ncampo']; ?>=<?= $rowREG[$rowMEN['campo']]; ?>&autorizar=1"><img src="images/borrar.png" alt="Borrar Registro" width="14" height="15" border="0" align="absmiddle" /></a>            </td>
          </tr>
          <?
		                 } // WHILE registros
				  } // WHILE menu
				
				} // FOR autorizar
                 mysql_close();
              ?>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
