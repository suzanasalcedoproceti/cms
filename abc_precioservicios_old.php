<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=9;
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
<script type="text/javascript" src="js/jquery-3.2.1.js"></script>
<script language="JavaScript">
  function isEmail(string) {
    if (string.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1)
        return true;
    else
        return false;
  }

  function valida() { 
  

   document.forma.action='graba_precioservicios.php';
   document.forma.submit();
  }
  function rechaza() {
   document.forma.action='borra_precioservicios.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_preciosservicios.php';
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
	<? $tit='Administrar Precio Servicio '; include('top.php'); ?>
	<?
        include('../conexion.php');

		$idPrecioserv=$_POST['idPrecioserv'];

		if (empty($idPrecioserv)) $idPrecioserv=$_GET['idPrecioserv']; 
        if (!empty($idPrecioserv)) {
      
          $resultado= mysql_query("SELECT * FROM precioservicios WHERE idPrecioservicio='$idPrecioserv'",$conexion);
          $row = mysql_fetch_array($resultado);
		      $cluster=$row['cluster'];
          $tipo_producto=$row['tipo_producto'];
          $subtipo_producto=$row['subtipo_producto'];
          $idservicio=$row['idServicio'];
          $precio=$row['precio'];
          $costo=$row['costo'];

      $resultadosrv = mysql_query("SELECT * FROM servicios WHERE idServicio = $idservicio",$conexion);
            $rowsrv= mysql_fetch_assoc($resultadosrv);
            $tiposerv = $rowsrv['tipo_servicio'];
            $descripcion = $rowsrv['descripcion'];
        }
  
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
<input name="colscr" type="hidden" id="colscr" value="<?= $row['colonia']; ?>"/> 
        <table width="80%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
           <tr>
            <td><div align="right">Cluster:</div></td>
            <td><select name="cluster" class="campo" id="cluster">
                  <option value="">Seleccione</option> 
                     <?  
                     //"SELECT distinct cluster,estado,cve_estado  FROM estados ORDER BY cluster"
                    $resultadoEDO = mysql_query("SELECT distinct cluster FROM estados GROUP BY cluster",$conexion);
                    while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
                      echo '<option value="'.$rowEDO['cluster'].'"';
                      if ($rowEDO['cluster']==$idcluster) echo 'selected';
                        echo '>'.$rowEDO['cluster'].'</option>';
                      } ?>
              </select>  </td>
          </tr>
      

          <tr>
            <td><div align="right">Tipo producto:</div></td>
            <td><select name="tipo_producto" class="camporeq" id="tipo_producto">
              <option value=""   <? if ($row['tipo_producto']=='') echo 'selected';?>>Selecciona</option>
                  <? $resultadotpr = mysql_query("SELECT * FROM tipo_producto ORDER BY nombre",$conexion);
                      while ($rowtpr = mysql_fetch_array($resultadotpr)) {
                      echo '<option value="'.$rowtpr['nombre'].'"';
                     if ($rowtpr['nombre']==$tipo_producto) echo 'selected';
                     echo '>'.$rowtpr['clave'].' </option>';
                  } ?>
            </select>     </td>
          </tr>
           <tr>
            <td><div align="right">Subtipo producto:</div></td>
            <td><select name="subtipo_producto" class="camporeq" id="subtipo_producto">
              <option value=""   <? if ($row['subtipo_producto']=='') echo 'selected';?>>Selecciona</option>
              <? $tipo_ = substr($row['tipo_producto'], 1);
              if($tipo_)
              {
                for ($i=1; $i < 5; $i++) { 
                  $selected = ($row['subtipo_producto']==$tipo_.$i) ? "\"selected\"" : "";
                  echo "<option value=\"$tipo_$i\" $selected>$tipo_$i</option>";
                }
              }
              ?>
            </select></td>
          </tr>
          <tr>
            <td><div align="right">Tipo servicio:</div></td>
            <td><select name="idservicio" class="campo" id="idservicio"" style="width: 140px;">
                  <option value="">Seleccione</option> 
                     <?  
                    $resultadoserv = mysql_query("SELECT *  FROM servicios ORDER BY idservicio",$conexion);
                    while ($rowserv = mysql_fetch_array($resultadoserv)) {
                      echo '<option value="'.$rowserv['idservicio'].'"';
                      if ($rowserv['idservicio']==$idservicio) echo 'selected';
                        echo '>'.$rowserv['tipo_servicio'].' ( '.$rowserv['descripcion'].' )</option>';
                      } ?>
              </select>
 
            </td>
          </tr>  

          <tr>
            <td><div align="right">Precio:</div></td>
            <td><input name="precio" type="text" class="campo" id="precio" value="<?= $precio; ?>" size="10" maxlength="9"   /></td>
          </tr> 
           <tr>
            <td><div align="right">Costo:</div></td>
            <td><input name="costo" type="text" class="campo" id="costo" value="<?= $costo; ?>" size="10" maxlength="9"   /></td>
          </tr> 
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="idPrecioserv" type="hidden" id="idPrecioserv" value="<?= $idPrecioserv; ?>" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>

       <br> <br>
      </form>    
    </div>
</div>
</body>
</html>
