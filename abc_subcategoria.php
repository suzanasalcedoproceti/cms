<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=2;
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
<script type="text/javascript" src="js/jquery.js"></script>

<script language="JavaScript">
  function valida() {
    if (document.forma.categoria.value == "") {
     alert("Ingresa la categor\u00eda.");
	 document.forma.categoria.focus();
     return;
     }
    if (document.forma.nombre.value == "") {
     alert("Falta nombre de la subcategor\u00eda.");
	 document.forma.nombre.focus();
     return;
     }
    if (document.forma.tipo_producto.value == "") {
     alert("Indica el tipo de producto correspondiente.");
	 document.forma.tipo_producto.focus();
     return;
     }
    if (document.forma.subtipo_producto.value == "") {
     alert("Indica el subtipo de producto correspondiente.");
   document.forma.subtipo_producto.focus();
     return;
     }
   document.forma.action='graba_subcategoria.php';
   document.forma.submit();
  }
  function rechaza() {
   document.forma.action='borra_subcategoria.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_subcategoria.php';
   document.forma.submit();
  }

$(function() {

    $("#tipo_producto").change(function(event) {

        if (event.target.value) {
          tipo_ = event.target.value.substring(1);
            $("#subtipo_producto").empty();
            $("#subtipo_producto").append("<option value=''>Selecciona</option>");
              for (j = 1; j < 5; j++) {
                    $("#subtipo_producto").append("<option value='"+tipo_+j+"'>"+tipo_+j+"</option>");
                }
        } else {
            $("#subtipo_producto").empty();
            $("#subtipo_producto").append("<option value=''>Selecciona</option>");
        }
    });
  });
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Subcategor&iacute;as'; include('top.php'); ?>
	<?
        include('../conexion.php');

		$subcategoria=$_POST['subcategoria'];

		$autorizar=$_GET['autorizar'];
		if (empty($subcategoria)) $subcategoria=$_GET['subcategoria']; 
  
        if (!empty($subcategoria)) {
          $editar=1;
          $disabled='disabled';
          $resultado= mysql_query("SELECT * FROM subcategoria WHERE clave='$subcategoria'",$conexion);
          $row = mysql_fetch_array($resultado);
		       $categoria=$row['categoria'];
           $nombrecat=$row['nombre'];


          $resultadocat= mysql_query("SELECT * FROM categoria WHERE clave='$categoria'",$conexion);
          $rowcat = mysql_fetch_array($resultadocat); 
           $nombrecat=$rowcat['nombre'];
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
            <td><span class="row1">
              <?php if($editar==1){ echo $nombrecat;   ?>
               <input type="hidden" name="categoria" id="categoria" value="<?=$row['categoria'];?>">
 
              <?php } else {?>
             <select name="categoria" class="camporeq" id="categoria"  >
                    <option value="" selected="selected">Selecciona categor&iacute;a...</option>
                    <?
                      $resultadoCAT = mysql_query("SELECT clave, nombre FROM categoria WHERE NOT accesorios AND NOT garantias ORDER BY orden, nombre",$conexion);
                      while ($rowCAT = mysql_fetch_array($resultadoCAT)){
                        echo '<option value="'.$rowCAT['clave'].'"';
                        if ($rowCAT['clave']==$categoria) echo 'selected';
                        echo '>'.$rowCAT['nombre'].'</option>';
                      }
                      ?>
                  </select>
              <?php } ?>
            </span></td>
          </tr>
          <tr>
            <td><div align="right">Subcategor&iacute;a:</div></td>
            <td>
              <?php if($editar==1){ echo $row['nombre']; ?>
                <input name="nombre" type="hidden" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="100" maxlength="100" /> 
              <?php  } else {?>
              <input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="100" maxlength="100" />  
            <?php } ?>
           </td>
          </tr>
          <tr>
            <td align="right">Tipo de producto:</td>
            <td>
               <?php if($editar==1){ echo substr($row['tipo_producto'], 1); ?>
                 <input type="hidden" name="tipo_producto" id="tipo_producto" value="<?=$row['tipo_producto']?>">
               <?php  } else {?>
                  <select name="tipo_producto" class="camporeq" id="tipo_producto">
                  <option value=""   <? if ($row['tipo_producto']=='') echo 'selected';?>>Selecciona</option>
                  <? $resultadotpr = mysql_query("SELECT * FROM tipo_producto ORDER BY nombre",$conexion);
                      while ($rowtpr = mysql_fetch_array($resultadotpr)) {
                      echo '<option value="'.$rowtpr['nombre'].'"';
                     if ($rowtpr['nombre']==$tipo_producto) echo 'selected';
                     echo '>'.$rowtpr['clave'].' </option>';
                  } ?>
                  </select>
               <?php } ?></td>
          </tr>
          <tr>
            <td align="right">Subtipo de producto:</td>
            <td> 
              <select name="subtipo_producto" class="camporeq" id="subtipo_producto">
              <option value=""   <? if ($row['subtipo_producto']=='') echo 'selected';?>>Selecciona</option>
              <? $tipo_ =   substr($row['tipo_producto'], 1);
             
              if($tipo_)
              {
                for ($i=1; $i < 5; $i++) { 
                  $selected = ($row['subtipo_producto']==$tipo_.$i) ? " selected " : "";

                  echo "<option value=\"$tipo_$i\" $selected>$tipo_$i </option>";
                }
              }
              ?>
            </select></td>
          </tr>          
          <tr>
            <td><div align="right">Orden en listado:</div></td>
            <td><input name="orden" type="text" class="campo" id="orden" value="<?= $row['orden']; ?>" size="4" maxlength="3" />
            </td>
          </tr>
          <tr>
            <td align="right">Mostrar en MAS</td>
            <td><input name="mostrar_en_mas" type="checkbox" id="mostrar_en_mas" value="1" <? if($row['mostrar_en_mas']) echo 'checked';?> />
            <?php /*
              &nbsp; &nbsp; &nbsp;Exclusivo para MAS:
              <label>
                <input type="radio" name="solo_para_mas" id="solo_para_mas" value="1" <? if ($row['solo_para_mas']==1) echo 'checked';?> />
                S&iacute;</label>
              &nbsp;&nbsp;
              <label>
                <input type="radio" name="solo_para_mas" id="solo_para_mas" value="0" <? if ($row['solo_para_mas']==0) echo 'checked';?> />
                No</label>
			*/
			?>
			</td>
          </tr>
           <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="right"><b>Override Logistica</b></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Almac&eacute;n</div></td>
            <td>
              <?php 
                $resultadocedis = mysql_query("SELECT distinct planta FROM planta",$conexion);
               

              ?>
              <select name="cedis" id="cedis">
                  <option>Seleccione</option>
              <?php
                while ($rowcedis = mysql_fetch_assoc($resultadocedis)) {
                  $selected = ($rowcedis['planta'] == $row['cedis']) ? 'selected="true"' : '';
                  echo '<option value="'.$rowcedis['planta'].'" '.$selected.'>'.$rowcedis['planta'].'</option>';
                }
              ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><div align="right">Storage Location:</div></td>
            <td><input name="loc" type="text" class="campo" id="loc" value="<?= $row['loc']; ?>" size="4" maxlength="4" />
            </td>
          </tr>
         
          <tr>
            <td align="right">Activa Override</td>
            <td><input name="override" type="checkbox" id="override" value="1" <? if($row['override']) echo 'checked';?> />
          </tr> 

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <? if (empty($autorizar)) { ?>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="subcategoria" type="hidden" id="subcategoria" value="<?= $subcategoria; ?>" />            </td>
          </tr>
          <? } else { ?>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="AUTORIZAR" />
	            <input name="rechazar" type="button" class="boton" onclick="rechaza();" value="RECHAZAR" />
                <input name="subcategoria" type="hidden" id="subcategoria" value="<?= $subcategoria; ?>" /> 
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
