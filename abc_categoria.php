<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=3;
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


<script language="JavaScript">
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

  function valida() {
    if (document.forma.nombre.value == "") {
     alert("Falta categoría.");
	 document.forma.nombre.focus();
     return;
     }

    if (document.forma.mostrar_en_mas.checked == true && (document.forma.orden_mas.value==""||document.forma.orden_mas.value==0)) {

     alert("Ingresar un orden válido.");
   document.forma.orden_mas.focus();
     return;
     }
 
   document.forma.action='graba_categoria.php';
   document.forma.submit();
  }
  function rechaza() {
   document.forma.action='borra_categoria.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_categoria.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Categor&iacute;as'; include('top.php'); ?>
	<?
        include('../conexion.php');

		$categoria=$_POST['categoria'];
		$autorizar=$_GET['autorizar'];
		
		if (empty($categoria)) $categoria=$_GET['categoria'];
        
        if (!empty($categoria)) {
          $resultado= mysql_query("SELECT * FROM categoria WHERE clave='$categoria'",$conexion);
          $row = mysql_fetch_array($resultado);
          $tipo_producto= $row['tipo_producto'];
        }
 
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">


          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Categor&iacute;a:</div></td>
            <td><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="115" maxlength="100" />            </td>
          </tr>
          <tr>
            <td><div align="right">Orden en listado:</div></td>
            <td><input name="orden" type="text" class="campo" id="orden" value="<?= $row['orden']; ?>" size="3" maxlength="2" />            </td>
          </tr>
          <!--tr>
            <td align="right">Mostrar en MAS</td>
            <td><input name="mostrar_en_mas" type="checkbox" id="mostrar_en_mas" value="1" <? if($row['mostrar_en_mas']) echo 'checked';?> />
&nbsp; &nbsp; &nbsp;Exclusivo para MAS:
<label>
  <input type="radio" name="solo_para_mas" id="solo_para_mas" value="1" <? if ($row['solo_para_mas']==1) echo 'checked';?> />
  S&iacute;</label>
&nbsp;&nbsp;
<label>
  <input type="radio" name="solo_para_mas" id="solo_para_mas" value="0" <? if ($row['solo_para_mas']==0) echo 'checked';?> />
  No</label></td>
          </tr-->
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <!--tr>
            <td><div align="right">Criterio de Inventario:</div></td>
            <td><select name="tipo_inventario" id="tipo_inventario" class="campo">
              <option value="0" <? if ($row['tipo_inventario']==0) echo 'selected ';?>>Existencias requeridas. Si no hay el mínimo, S&Iacute; mostrar, pero no vender.</option>
              <option value="1" <? if ($row['tipo_inventario']==1) echo 'selected ';?>>Existencias requeridas. Si no hay el mínimo, NO mostrar</option>
              <option value="2" <? if ($row['tipo_inventario']==2) echo 'selected ';?>>Existencias no requeridas. Siempre vender</option>
            </select></td>
          </tr>
          <tr>
            <td><div align="right">M&iacute;nimo:</div></td>
            <td><input name="minimo" type="text" class="campo" id="minimo" value="<?= $row['minimo']; ?>" size="3" maxlength="2" /> 
              piezas disponibles</td>
          </tr-->
          <tr>
            <td><div align="right">
              <input name="minimo_venta" type="checkbox" id="minimo_venta" value="1" <? if ($row['minimo_venta']==1) echo 'checked';?> />
            </div></td>
            <td>Aplicar m&iacute;nimo de venta en TW a esta categor&iacute;a</td>
          </tr>
          <tr>
            <td align="right">Tipo de producto:</td>
            <td><select name="tipo_producto" class="camporeq" id="tipo_producto">
              <option value=""   <? if ($row['tipo_producto']=='') echo 'selected';?>>Selecciona</option>
               <? $resultadotpr = mysql_query("SELECT * FROM tipo_producto ORDER BY nombre",$conexion);
                      while ($rowtpr = mysql_fetch_array($resultadotpr)) {
                      echo '<option value="'.$rowtpr['nombre'].'"';
                     if ($rowtpr['nombre']==$tipo_producto) echo 'selected';
                     echo '>'.$rowtpr['clave'].' </option>';
                 } ?>
            </select> 
            (Solo en caso que el producto no tenga subcategor&iacute;a, se toma este por default)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">
              <input name="mostrar_en_mas" type="checkbox" id="mostrar_en_mas" value="1" <? if($row['mostrar_en_mas']) echo 'checked';?> />
            </div></td>
            <td>Visualizacion de Categoria en Sitio Mas</td>
          </tr>
          <tr>
            <td><div align="right">Orden en listado Sitio MAS:</div></td>
            <td><input name="orden_mas" type="text" onkeypress="return isNumberKey(event)" class="campo" id="orden_mas" value="<?= $row['orden_mas']; ?>" size="3" maxlength="2" />
            </td>
          </tr>          
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <? if (empty($autorizar)) { ?>
          <!--tr>
            <td><div align="right">Tomar de la planta:</div></td>
            <td><select name="planta" class="campo" id="planta">
              <option value="">Seleccionar...</option>
              <?  
					$resultadoPLA = mysql_query("SELECT * FROM cedis ORDER BY clave",$conexion);
					while ($rowPLA = mysql_fetch_array($resultadoPLA)) {
					  echo '<option value="'.$rowPLA['clave'].'"';
					  if ($rowPLA['clave']==$row['planta']) echo 'selected';
				  	  echo '>'.$rowPLA['clave']." - ".$rowPLA['nombre'].'</option>';
				    }
				  ?>
            </select></td>
          </tr-->
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <!--tr>
            <td><label>
              <div align="right">
                <input name="actualiza_criterio" type="checkbox" id="actualiza_criterio" value="1" />
                </div>
            </label></td>
            <td>Actualizar criterio de inventario al grabar</td>
          </tr-->
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="categoria" type="hidden" id="categoria" value="<?= $categoria; ?>" />            </td>
          </tr>
          <? } else { ?>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="AUTORIZAR" />
	            <input name="rechazar" type="button" class="boton" onclick="rechaza();" value="RECHAZAR" />
                <input name="categoria" type="hidden" id="categoria" value="<?= $categoria; ?>" /> 
                <input name="autorizar" type="hidden" id="autorizar" value="<?= $autorizar; ?>" />            </td>
          </tr>
          <? } ?>
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
