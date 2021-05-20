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
   if (document.forma.estadosel.value == "") {
     alert("Debe seleccionar un estado.");
   document.forma.estadosel.focus();
     return;
   }
  if (document.forma.municipio.value == "") {
     alert("Debe seleccionar un municipio.");
   document.forma.municipio.focus();
     return;
   }
  if (document.forma.tipo_producto.value == "") {
     alert("Debe seleccionar un tipo de producto.");
   document.forma.tipo_producto.focus();
     return;
   }
  if (document.forma.idservicio.value == "") {
     alert("Debe seleccionar un tipo de servicio.");
   document.forma.idservicio.focus();
     return;
   }
  if (document.forma.cobertura_.value == "") {
     alert("Debe seleccionar una  cobertura.");
   document.forma.cobertura_.focus();
     return;
   }
 

   document.forma.action='graba_cobertura.php';
   document.forma.submit();
  }
  function rechaza() {
   document.forma.action='borra_precioservicios.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_cobertura.php';
   document.forma.submit();
  }
 

$(function() {
    $("#estadosel").change(function(event) {
       var idest= event.target.value;
        $("#estsucursales").val(idest);
        $("#sucursales").val('');
        if (event.target.value) { 

          $.get("lib.php?estado=" + event.target.value + "", function(data, status){
            $("#municipio").empty();
            $("#municipio").append("<option value=''>Cualquier municipio...</option>");
              for (j = 0; j < data.length; j++) {
                    $("#municipio").html(data);
                }
            }); 
           estad(idest);
        } else {
            $("#municipio").empty();
            $("#municipio").append("<option value=''>Cualquier municipio...</option>");
        }
    });
  });

 function estad(idest) {  
    $("#idservicio").change(function(event) { 
        if (event.target.value == 2 || event.target.value == 3) {   
           $("#sucursales").show();  
        }
        else
          {$("#sucursales").hide(); }
    }); 
}
</script>
</head>  
<body> 
<div id="container">
  <? $tit='Administrar Cobertura '; include('top.php'); ?>
  <?include('../conexion.php'); 
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
            <td><div align="right">Estado:</div></td>
            <td><select name="estado" class="campo" id="estadosel">
                <option value="">Seleccione estado </option>
                  <?  
                    $resultadoEDO = mysql_query("SELECT * FROM estados ORDER BY estado",$conexion);
                    while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
                      echo '<option value="'.$rowEDO['idEstado'].'"';
                      if ($rowEDO['idEstado']==$estado) echo 'selected';
                        echo '>'.$rowEDO['estado'].'</option>';
                      } 
                  ?>
              </select> </td>
          </tr>
          
           <tr>
           <td><div align="right">Municipio:</div></td>
            <td><select name="municipio" class="campo" id="municipio">
               <option value="">Seleccione municipio...</option>
                  <? $resultadoEDO = mysql_query("SELECT distinct mnpio,cve_mnpio FROM cp_sepomex where cve_estado=$estado order by mnpio asc",$conexion);
                  while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
                    echo '<option value="'.$rowEDO['cve_mnpio'].'"';
                    if ($rowEDO['cve_mnpio']==$municipio) echo 'selected';
                      echo '>'.$rowEDO['mnpio'].'</option>';
                    } ?>            
                  </select></td>
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
            </select>      
          </td>
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
      
 
          <tr id="sucursales" style="display: none;">
            <td><div align="right">Sucursal:</div></td>
            <td> <select  multiple="multiple"  name="sucursal[]" class="campo" id="sucursal">
                 <option value="0">N/A</option>
                  <?$resultadoSUC = mysql_query("SELECT sucursales.*, estados.estado AS nombre_estado FROM sucursales INNER JOIN estados ON sucursales.cve_estado = estados.cve_estado WHERE 1 ORDER BY cve_estado, nombresucursal",$conexion);
                     while ($rowSUC = mysql_fetch_array($resultadoSUC)) {
                        echo '<option value="'.$rowSUC['idsuc'].'"';
                        if ($rowSUC['idSucursal']==$idsucursal) echo 'selected';
                        echo '>'.$rowSUC['nombresucursal'].'   '. " (ID: ". $rowSUC['idsuc']. ")" .'  </option>';
                       }  ?>
              </select></td>
          </tr> 


          <tr>
            <td><div align="right">Cobertura:</div></td>
            <td> <select name="cobertura_" class="campo" id="cobertura_">
               <option value="">Seleccione cobertura...</option>
              <option value="SI" <? if ($cobertura_=='SI') echo 'selected';?>>SI</option>
              <option value="NO" <? if ($cobertura_=='NO') echo 'selected';?>>NO</option> 
            </select> </td>
          </tr> 
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            </td>
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
