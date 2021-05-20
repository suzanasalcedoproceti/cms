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
	$nuevo = $_GET['nuevo'];
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

<script language="JavaScript">

  function valida() {
   if (document.forma.estado.value == "") {
     alert("Falta estado.");
	 document.forma.estado.focus();
     return;
   }
   if (document.forma.ciudad.value == "") {
     alert("Falta nombre de la ciudad.");
	 document.forma.ciudad.focus();
     return;
   }
   if (document.forma.trans_zone.value == "") {
     alert("Falta indicar el código de Trans-Zone.");
	 document.forma.trans_zone.focus();
     return;
   }
   if ((document.forma.low_ocu.value == 1  || document.forma.ltl_ocu.value == 1) && document.forma.sucursal_ocurre.value=="") {
     alert("Debes seleccionar la Sucursal Almex correspondiente");
	 document.forma.sucursal_ocurre.focus();
     return;
   }
   document.forma.action='graba_cp.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_cp.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Códigos Postales'; include('top.php'); ?>
	<?
        include('../conexion.php');

		$cp=$_POST['cp'];
		if (empty($cp)) $cp=$_GET['cp'];
        
        if (!empty($cp)) {
          $resultado= mysql_query("SELECT * FROM cp WHERE cp='$cp'",$conexion);
          $row = mysql_fetch_array($resultado);
		  $estado=$row['estado'];
        }
        
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input type="hidden" name="nuevo" value="<?=$nuevo;?>" />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">CP:</div></td>
            <td><input name="clave" type="text" class="campo" id="clave" value="<?= $row['cp']; ?>" size="6" maxlength="5" <? if ($row['cp']) echo 'readonly';?> /></td>
          </tr>
          <tr>
            <td><div align="right">Estado:</div></td>
            <td><select name="estado" class="campo" id="estado">
                <option value="" selected="selected">Selecciona estado...</option>
                <?
					$resultadoEDO = mysql_query("SELECT * FROM estado ORDER BY clave",$conexion);
					while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
					  echo '<option value="'.$rowEDO['clave'].'"';
					  if ($rowEDO['clave']==$row['estado']) echo ' selected ';
				  	  echo '>'.$rowEDO['nombre'].'</option>';
				    }
			  ?>
            </select></td>
          </tr>
          <tr>
            <td><div align="right">Ciudad:</div></td>
            <td><label>
              <input name="ciudad" type="text" class="campo" id="ciudad" value="<?=$row['ciudad'];?>" size="90" maxlength="100" />
            </label></td>
          </tr>
          <tr>
            <td><div align="right">Trans Zone:</div></td>
            <td><input name="trans_zone" type="text" class="campo" id="trans_zone" value="<?= $row['trans_zone']; ?>" size="12" maxlength="10" /></td>
          </tr>
          <tr>
            <td><div align="right">Entrega Low Ticket a Domicilio?</div></td>
            <td><select name="low_dom" class="campo" id="low_dom">
              <option value="1" <? if ($row['low_dom']==0) echo 'selected ';?>>SI</option>
              <option value="0" <? if ($row['low_dom']==0) echo 'selected ';?>>NO</option>
            </select>            </td>
          </tr>
          <tr>
            <td><div align="right">Entrega Low Ticket a Ocurre?</div></td>
            <td><select name="low_ocu" class="campo" id="low_ocu">
                <option value="1" <? if ($row['low_ocu']==0) echo 'selected ';?>>SI</option>
                <option value="0" <? if ($row['low_ocu']==0) echo 'selected ';?>>NO</option>
              </select>            </td>
          </tr>
          <tr>
            <td><div align="right">Entrega LTL a Domicilio?</div></td>
            <td><select name="ltl_dom" class="campo" id="ltl_dom">
                <option value="1" <? if ($row['ltl_dom']==0) echo 'selected ';?>>SI</option>
                <option value="0" <? if ($row['ltl_dom']==0) echo 'selected ';?>>NO</option>
              </select>            </td>
          </tr>
          <tr>
            <td><div align="right">Entrega LTL a Ocurre?</div></td>
            <td><select name="ltl_ocu" class="campo" id="ltl_ocu">
                <option value="1" <? if ($row['ltl_ocu']==0) echo 'selected ';?>>SI</option>
                <option value="0" <? if ($row['ltl_ocu']==0) echo 'selected ';?>>NO</option>
              </select>            </td>
          </tr>
          <tr>
            <td><div align="right">SKU Low Ticket:</div></td>
            <td><input name="sku_low" type="text" class="campo" id="sku_low" value="<?= $row['sku_low']; ?>" size="25" maxlength="50" /></td>
          </tr>
          <tr>
            <td><div align="right">SKU LTL:</div></td>
            <td><input name="sku_ltl" type="text" class="campo" id="sku_ltl" value="<?= $row['sku_ltl']; ?>" size="25" maxlength="50" /></td>
          </tr>
          <tr>
            <td><div align="right">Cedis origen de LTL:</div></td>
            <td><input name="cedis_origen_ltl" type="text" class="campo" id="cedis_origen_ltl" value="<?= $row['cedis_origen_ltl']; ?>" size="5" maxlength="4" /></td>
          </tr>
          <tr>
            <td><div align="right">Sucursal Almex:</div></td>
            <td><select name="sucursal_ocurre" class="campo" id="sucursa_ocurre">
              <option value="">Selecciona....</option>
              <? $resultadoAL = mysql_query("SELECT * FROM sucursal_ocurre ORDER BY nombre");
			     while ($rowAL = mysql_fetch_array($resultadoAL)) { ?>
              <option value="<?=$rowAL['clave'];?>" <? if ($rowAL['clave']==$row['sucursal_ocurre']) echo 'selected';?>>
                <?=$rowAL['nombre'];?>
              </option>
              <? } ?>
            </select></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
