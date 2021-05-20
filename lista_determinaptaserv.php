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
<link href="css/thickbox.css" rel="stylesheet" type="text/css" >
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/thickbox.js"></script>

<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='lista_determinaptaserv.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
  document.forma.numpag.value = 1;
    document.forma.action='lista_determinaptaserv.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.sucursal.value = id;
    document.forma.action='borra_determinaptaserv.php';
    document.forma.submit();
  }
  function exportar() {
    document.forma.target = '_self';
    document.forma.action='lista_determinaptaserv_xls.php';
    document.forma.numpag.value=1;
    document.forma.submit();
    document.forma.target = '_self';
    document.forma.action='';
  }
</script>

<script>



function edit_row(no)
{
 $("#edit_button"+no).hide();  
 $("#showedt"+no).show(); 
$("#idptaserv"+no).show(); 
$("#idptaservc"+no).hide();
  
 }

function save_row(no)
{
 $("#edit_button"+no).show();  
 $("#showedt"+no).hide();  
    var idplantaservicio = no; 
    var cedis= $("#idptaserv"+no).val();  
    $.post("graba_ptaservicioajax.php", { idplantaservicio: no,cedis: cedis}, function(data)
    { location.reload(true);  });   
}

function cancel_row(no)
{
  $("#edit_button"+no).show();  
  $("#showedt"+no).hide();  
  $("#idptaserv"+no).hide(); 
$("#idptaservc"+no).show();
}
 
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Planta servicio'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $estado = $_POST['estado'];  
   $tipo_producto = $_POST['tipo_producto'];
   $tipo_servicio = $_POST['tipo_servicio'];
   $cedis = $_POST['cedis'];  

   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='cluster';

   if ($ord=='estado') $orden='ORDER BY cluster'; 
   include('../conexion.php');
  $query = "SELECT * FROM planta order by planta;";
        $resultado= mysql_query($query,$conexion);
        while ($tableRow = mysql_fetch_assoc($resultado)) {
          $plantas[] = $tableRow;
        }
 
?>
  <div class="main"> 
 <form action="" method="post" name="forma2" id="forma2">
<table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar planta servicio" onClick="document.forma2.action='abc_plantaservicios.php'; document.forma2.submit();" /></td>
            <td>   <input name="button" type="submit" class="boton_agregar" id="button" value="Importar determina planta" onClick="document.forma2.action='importa_determinaptaserv.php'; document.forma2.submit();" />
               </td>
            <td><div align="left">
                  <input name="exp_xls" type="button" class="boton" onclick="javascript:exportar();" value="Descargar Registros" />
                </div></td>
            
          </tr>
  </table>

 </form>


      <form action="" method="post" name="forma" id="forma"> 
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
 
          <tr>
            <td colspan="2">Filtros:
              <select name="estado" class="campo" id="estado">
                <option value="">Cualquier cluster...</option>
                  <?  
          $resultadoEDO = mysql_query("SELECT DISTINCT (cluster) FROM estados ORDER BY estado",$conexion);
          while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
            echo '<option value="'.$rowEDO['cluster'].'"';
            if ($rowEDO['cluster']==$estado) echo 'selected';
              echo '>'.$rowEDO['cluster'].'</option>';
            }

          ?>
          </select> 
          <select name="tipo_producto" class="campo" id="tipo_producto">
               <option value="">Cualquier tipo de producto...</option>
               <?  
          $resultadoEDO = mysql_query("SELECT * FROM tipo_producto ORDER BY nombre",$conexion);
          while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
            echo '<option value="'.$rowEDO['nombre'].'"';
            if ($rowEDO['nombre']==$tipo_producto) echo 'selected';
              echo '>'.$rowEDO['clave'].'</option>';
            }

          ?>
          </select>
          <select name="tipo_servicio" class="campo" id="tipo_servicio">
               <option value="">Cualquier tipo de servicio...</option>
               <?  
          $resultadoEDO = mysql_query("SELECT * FROM servicios ORDER BY idservicio",$conexion);
          while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
            echo '<option value="'.$rowEDO['idservicio'].'"';
            if ($rowEDO['idservicio']==$tipo_servicio) echo 'selected';
              echo '>'.$rowEDO['tipo_servicio'].' '.$rowEDO['descripcion'].'</option>';
            }

          ?>
            </select>
            
            <input name="boton_buscar" type="submit" class="boton_buscar" id="boton_buscar" value="Buscar" onclick="document.forma.submit();">
          </td>
          </tr>
 
          <tr>
            <td bgcolor="#BBBBBB"><?
      
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
             

                     // construir la condición de búsqueda
                     $condicion = "WHERE 1 ";


                     if (!empty($estado))
                     $condicion.= " AND  cluster=' $estado'";
                     if (!empty($tipo_producto))
                     $condicion.= " AND  tipo_producto='$tipo_producto'";
                     if (!empty($tipo_servicio))
                     $condicion.= " AND  idServicio='$tipo_servicio'";
                     if (!empty($cedis))
                     $condicion.= " AND  Cedis='$cedis'";
 
                     // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT 1 FROM determina_plantaservicio $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
              
           echo 'Total de coberturas en la lista: <b>'.$totres.'</b>';
      
        ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="sucursal" type="hidden" id="sucursal" />
                <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
                <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
                <?


                     $regini = ($numpag * $ver) - $ver;
           if ($regini<0) $regini=0;

                     // poner flechitas anterior, primero, último, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera página"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Página anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "Página ".$numpag." de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";
                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente página"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Última página"></a>';
                     }
              ?>            </td>
          </tr>
        </table>



        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" ><div align="center"><strong>Cluster</strong></div></td>
            <td nowrap="nowrap" ><div align="center"><strong>Tipo producto</strong></div></td> 
            <td nowrap="nowrap" ><div align="center"><strong>Tipo Servicio</strong></div></td>
            <td nowrap="nowrap" ><div align="center"><strong>Descripci&oacute;n Servicio</strong></div></td>
            <td nowrap="nowrap" ><div align="center"><strong>Cedis</strong></div></td> 
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
            $query = "SELECT * from determina_plantaservicio
            $condicion $orden LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
              $idplantaservicio = $row['idplantaservicio'];
              $cluster = $row['cluster'];
              $tipo_producto = $row['tipo_producto'];
              $idservicio = $row['idservicio'];
              $cedis = $row['Cedis']; 

           $resultadosrv = mysql_query("SELECT * FROM servicios WHERE idServicio = $idservicio",$conexion);
            $rowsrv= mysql_fetch_assoc($resultadosrv);
            $tiposerv = $rowsrv['tipo_servicio'];
            $descripcion = $rowsrv['descripcion'];
          

          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF" align="center"><?=  $cluster ;?></td>  
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= substr($row['tipo_producto'], 1);?></td> 
              <td bgcolor="#FFFFFF" align="center"><?= $tiposerv;?></td> 
              <td bgcolor="#FFFFFF" align="center"><?= $descripcion;?></td> 
            <td bgcolor="#FFFFFF" align="center"><div id="idptaservc<?= $row['idplantaservicio']; ?>"><?= $cedis; ?></div>
              <select name="cluster" class="campo" id="idptaserv<?=$row['idplantaservicio'];?>" style="display: none;">
                <option value=''>Seleccionar</option>
                <?php foreach ($plantas as $value): ?>
                      <option value="<?php echo $value['planta'].'/'. $value['loc'] ?>" <?php echo ($cedis == $value['planta'].'/'. $value['loc']) ? selected : '' ?>><?php echo $value['planta'].'/'. $value['loc'] ?></option>
                <?php endforeach; ?>
              </select>
              <!--input name="cluster" type="text" class="campo" id="idptaserv<?=$row['idplantaservicio'];?>" value="<?= $cedis; ?>" size="10" style="display: none;" /-->

            </td>          
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
              <input type="button" id="edit_button<?= $row['idplantaservicio']; ?>" value="Editar" class="edit" onclick="edit_row(<?= $row['idplantaservicio'];?>)"> 
             <div id="showedt<?= $row['idplantaservicio']; ?>" style="display: none;">   
              <input type="button" id="save_button<?= $row['idplantaservicio']; ?>" value="Grabar" class="save" onclick="save_row(<?= $row['idplantaservicio']; ?>)">
              <input type="button" id="scancel_button<?= $row['idplantaservicio']; ?>" value="Cancelar" class="save" onclick="cancel_row(<?= $row['idplantaservicio']; ?>)">           
            </div>

            <div style="display: none;">
              <a href="abc_plantaservicios.php?idplantaservicio=<?= $row['idplantaservicio']; ?>"><img src="images/editar.png" alt="Editar Precio servicio" width="14" height="16" border="0" align="absmiddle" /></a>
              <? if ($rel<=0) { ?><a onclick="return confirm('¿Estás seguro que deseas\nBorrar la Sucursal?')" href="javascript:borra('<?= $row['idsuc']; ?>');"><img src="images/borrar.png" alt="Borrar Sucursal" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absmiddle" /><? } ?>
              </div>            
            </td>

      </tr>
          <? } // WHILE 
 
            mysql_close();
          ?>
        </table>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB" align="right"><?

                     // poner flechitas anterior, primero, &uacute;ltimo, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="P&aacute;gina anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "P&aacute;gina ".$numpag." de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";
                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="&Uacute;ltima p&aacute;gina"></a>';
                     }
              ?>
            </td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
