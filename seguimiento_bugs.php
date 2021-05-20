<?
if (!include('ctrl_acceso.php')) return;
include('funciones.php');
include('lib.php');
$permiso_bugs = op(28);

$folio = $_POST['folio'];
include('../conexion.php');
if (!$permiso_bugs) 
	$filtro_usr = " AND usuario = ".$_SESSION['usr_valido']." ";

$resultado = mysql_query("SELECT bug.*, bug_tipo.nombre FROM bug LEFT JOIN bug_tipo ON bug.tipo = bug_tipo.clave WHERE folio = $folio $filtro_usr");
$row = mysql_fetch_array($resultado);

if ($row['aplicacion']=='CMS') {
	$usuario=$row['usuario'];
	$resUSU= mysql_query("SELECT nombre FROM usuario WHERE clave='$usuario'",$conexion);
	$rowUSU= mysql_fetch_array($resUSU);
	$nombre_tienda = 'N/A';
}

if ($row['aplicacion']=='POS') {
	
	$tienda = $row['tienda'];
	$resTIE = mysql_query("SELECT nombre FROM tienda WHERE clave = $tienda");
	$rowTIE = mysql_fetch_array($resTIE);
	$nombre_tienda = $rowTIE['nombre'];

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
<link href="js/src/shadowbox.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/src/shadowbox.js"></script>
<script type="text/javascript">
	Shadowbox.init({
		language: 'es',
		players:  ['img', 'html', 'iframe', 'qt', 'wmp', 'swf', 'flv']
	});
</script>


<script language="JavaScript">
  function valida() {
   o = document.forma;
   if (o.descripcion.value=='') {
   	 alert("Ingresa tus comentarios");
	 o.descripcion.focus();
	 return;
   }
   document.forma.action='graba_seguimiento_bug.php';
   document.forma.submit();
  }
 function descarta() {
   document.forma.action='lista_bugs.php';
   document.forma.submit();
  }
 

</script>
</head>

<body>
<div id="container">
	<? $tit='Seguimiento de errores y recomendaciones'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
       <input type="hidden" name="folio" id="folio" value="<?=$folio;?>" />
       <input type="hidden" name="asunto" id="asunto" value="<?=$row['asunto'];?>" />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="2" class="texto">
          <tr>
            <td colspan="2">&nbsp;</td>
            <td width="4%">&nbsp;</td>
            <td width="57%">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4" align="left" bgcolor="#999999"><div align="left"><strong>DATOS</strong></div></td>
          </tr>

          <tr>
            <td><div align="right"><strong>Fecha Registro:</strong></div></td>
            <td width="31%"><?=fecha($row['fecha']);?></td>
            <td rowspan="6" align="left" valign="top">&nbsp;</td>
            <td align="left" valign="top"><? if (file_exists("images/cms/bugs/".$folio.".jpg")) { ?>
              <strong>Pantalla</strong>
              <? } ?></td>
          </tr>
          <tr>
            <td width="8%"><div align="right"><strong>Aplicaci&oacute;n:</strong></div></td>
            <td><?=$row['aplicacion'];?></td>
            <td rowspan="6" align="left" valign="top"><? if (file_exists("images/cms/bugs/".$folio.".jpg")) { ?>
              <a href="images/cms/bugs/<?=$folio;?>.jpg" rel="shadowbox"><img src="images/cms/bugs/<?=$folio;?>.jpg" style="max-height:120px" /></a>
              <? } ?></td>
          </tr>
          <tr>
            <td width="8%"><div align="right"><strong>M&oacute;dulo:</strong></div></td>
            <td nowrap="nowrap"><?=$row['modulo'];?></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Tienda:</strong></div></td>
            <td><?= $nombre_tienda;?></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Tipo de reporte:</strong></div></td>
            <td><?= $row['nombre'];?></td>
          </tr>          
          <tr>
            <td><div align="right"><strong>Estatus:</strong></div></td>
            <td><? switch ($row['estatus']) {
										case 'A' : echo 'Abierto'; break;
										case 'R' : echo 'En Revisión'; break;
										case 'C' : echo 'Completado'; break;
										case 'X' : echo 'Cancelado'; break;
									 } ?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4" align="left" bgcolor="#999999"><div align="left"><strong>ASUNTO: <br /><?=$row['asunto'];?></strong></div></td>
          </tr>
          <? 
		     $query = "SELECT bug_detalle.* FROM bug_detalle WHERE bug = $folio ORDER BY folio";
		     $resultadoBD = mysql_query($query);
		  	 while ($rowBD = mysql_fetch_array($resultadoBD)) { 
		  ?>
          <tr>
            <td colspan="4" bgcolor="#dddddd">
            <div class="fLeft"><strong><?=$rowBD['nombre_usuario'];?></strong></div>
            <div style="float:right"><strong>Posteado:</strong> <?=fecha($rowBD['fecha'])." &nbsp; ".$rowBD['hora'];?></div></td>
          </tr>
          <tr>
            <td colspan="4" bgcolor="#FFFFFF"><?= str_replace(chr(10),'<br>',$rowBD['detalle']);?></td>
          </tr>
          <tr>
            <td colspan="4">&nbsp;</td>
          </tr>
          <? } // while ?>
          <tr>
            <td colspan="4">&nbsp;</td>
          </tr>

          <? if ($permiso_bugs && $row['estatus']!='C' && $row['estatus']!='X') { ?>
          <tr>
            <td colspan="4" align="left" bgcolor="#999999"><div align="left"><strong>SEGUIMIENTO</strong></div></td>
          </tr>          <tr>
            <td><div align="right"><strong>Descripci&oacute;n:</strong></div></td>
            <td colspan="3"><textarea name="descripcion" cols="70" rows="5" class="campo" id="descripcion"></textarea></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Estatus:</strong></div></td>
            <td colspan="3">
             <select name="estatus" class="campo" id="estatus">
              <option value="R" selected >En Revisión</option>
             <? if ($permiso_bugs) { ?>
              <option value="C" >Completado</option>
              <option value="X" >Cancelado</option>
             <? } ?>
            </select></td>
          </tr>
          <? } ?>

          <tr>
            <td>&nbsp;</td>
            <td colspan="3">
            <? if ($permiso_bugs && $row['estatus']!='C' && $row['estatus']!='X') { ?>
                <input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
            <? } ?>
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR" id="desc" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
          </tr>
        </table>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td align="right">&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
