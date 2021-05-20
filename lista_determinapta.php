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
    form.action='lista_determinapta.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
  document.forma.numpag.value = 1;
    document.forma.action='lista_determinapta.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.sucursal.value = id;
    document.forma.action='borra_determinapta.php';
    document.forma.submit();
  }
  function exportar() {
    document.forma.target = '_self';
    document.forma.action='lista_determinapta_xls.php';
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
$("#idpta"+no).hide(); 
$("#idpntaa"+no).show(); 
$("#idptb"+no).hide(); 
$("#idpntab"+no).show();  
$("#idptc"+no).hide(); 
$("#idpntac"+no).show();  
$("#idptd"+no).hide(); 
$("#idpntad"+no).show();  
$("#idpte"+no).hide(); 
$("#idpntae"+no).show();
$("#idptf"+no).hide(); 
$("#idpntaf"+no).show();
$("#idptg"+no).hide(); 
$("#idpntag"+no).show();
$("#idpth"+no).hide(); 
$("#idpntah"+no).show();
 }

function save_row(no)
{
 $("#edit_button"+no).show();  
 $("#showedt"+no).hide();   
    var idpntaa = $("#idpntaa"+no).val();  
    var idpntab = $("#idpntab"+no).val(); 
    var idpntac = $("#idpntac"+no).val();
    var idpntad = $("#idpntad"+no).val(); 
    var idpntae = $("#idpntae"+no).val(); 
    var idpntaf = $("#idpntaf"+no).val();
    var idpntag = $("#idpntag"+no).val(); 
    var idpntah = $("#idpntah"+no).val(); 
    var idDeterminacion= $("#idtermina"+no).val();  
    $.post("graba_determinaptaajax.php", {idDeterminacion: no,cedis1: idpntaa,cedis2:idpntab,cedis3:idpntac,cedis4: idpntad,cedis5: idpntae,cedis6: idpntaf,cedis7: idpntag,cedis8: idpntah}, function(data)
    { location.reload(true);});   
}

function cancel_row(no)
{
$("#edit_button"+no).show();  
$("#showedt"+no).hide();  
$("#idpta"+no).show(); 
$("#idptb"+no).show();
$("#idptc"+no).show(); 
$("#idptd"+no).show();
$("#idpte"+no).show(); 
$("#idptf"+no).show();
$("#idptg"+no).show();
$("#idpth"+no).show();
$("#idpntaa"+no).hide();
$("#idpntab"+no).hide();
$("#idpntac"+no).hide();
$("#idpntad"+no).hide();
$("#idpntae"+no).hide();
$("#idpntaf"+no).hide();
$("#idpntag"+no).hide();
$("#idpntah"+no).hide();
}
 
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado determina planta'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $estado = $_POST['estado']; 
   $tipo_producto = $_POST['tipo_producto'];
   $tipo_servicio = $_POST['tipo_servicio'];
 



   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='estado';

   if ($ord=='estado') $orden='ORDER BY cluster';
  
   
   include('../conexion.php');
  $query = "SELECT * FROM planta order by planta;";
        $resultado= mysql_query($query,$conexion);
        while ($tableRow = mysql_fetch_assoc($resultado)) {
          $plantas[] = $tableRow;
        }
?>
  <div class="main"> 
  <div style="float: left;"> 

    <table border="0" align="center" cellpadding="2" cellspacing="0" class="texto" width="380" ">
           <tr> 
            <td>
               <form action="" method="post" name="forma3" id="forma3">
              <td>  
               <input name="button" type="submit" class="boton_agregar" id="button" value="Agregar determina planta" onClick="document.forma3.action='abc_determinapta.php'; document.forma3.submit();" /> 
               </td>
              
               <td>   <input name="button" type="submit" class="boton_agregar" id="button" value="Importar determina planta" onClick="document.forma3.action='importa_determinaplanta.php'; document.forma3.submit();" />
               </td>
             </form>
 
             </td>
             <td><div align="left">
                  <input name="exp_xls" type="button" class="boton" onclick="javascript:exportar();" value="Descargar Registros" />
                </div></td>
          </tr>
  </table>

</div>
 <br><br>
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
               <? $resultadoEDO = mysql_query("SELECT * FROM tipo_producto ORDER BY nombre",$conexion);
                  while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
                    echo '<option value="'.$rowEDO['nombre'].'"';
                    if ($rowEDO['nombre']==$tipo_producto) echo 'selected';
                      echo '>'.$rowEDO['clave'].'</option>';
                    }  ?>
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
                     $condicion.= " AND  cluster='$estado'"; 
                     if (!empty($tipo_producto))
                     $condicion.= " AND  tipo_producto='$tipo_producto'";
                     if (!empty($tipo_servicio))
                     $condicion.= " AND  idServicio='$tipo_servicio'";

                     // construir la condición de búsqueda 
                       $resultadotot= mysql_query("SELECT * FROM determina_planta $condicion",$conexion);
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
            <td nowrap="nowrap" ><div align="center"><strong>Cedis1</strong></div></td> 
            <td nowrap="nowrap" ><div align="center"><strong>Cedis2</strong></div></td> 
            <td nowrap="nowrap" ><div align="center"><strong>Cedis3</strong></div></td> 
            <td nowrap="nowrap" ><div align="center"><strong>Cedis4</strong></div></td> 
            <td nowrap="nowrap" ><div align="center"><strong>Cedis5</strong></div></td> 
            <td nowrap="nowrap" ><div align="center"><strong>Cedis6</strong></div></td> 
            <td nowrap="nowrap" ><div align="center"><strong>Cedis7</strong></div></td> 
            <td nowrap="nowrap" ><div align="center"><strong>Cedis8</strong></div></td> 
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <? $query = "SELECT * from determina_planta
             $condicion ORDER BY cluster LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
              $idDeterminacion = $row['idDeterminacion'];
              $cluster = $row['cluster'];
              $tipo_producto = $row['tipo_producto'];
              $idservicio = $row['idServicio'];
              $cedis1 = $row['cedis1']; 
              $cedis2 = $row['cedis2']; 
              $cedis3 = $row['cedis3']; 
              $cedis4 = $row['cedis4']; 
              $cedis5 = $row['cedis5']; 
              $cedis6 = $row['cedis6']; 
              $cedis7 = $row['cedis7']; 
              $cedis8 = $row['cedis8']; 

            $resultadosrv = mysql_query("SELECT * FROM servicios WHERE idServicio = $idservicio",$conexion);
            $rowsrv= mysql_fetch_assoc($resultadosrv);
            $tiposerv = $rowsrv['tipo_servicio'];
            $descripcion = $rowsrv['descripcion']; 


          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF" align="center"><?=  $cluster ;?>
              <input name="idDeterminacion" type="hidden"  id="idtermina<?=$row['idDeterminacion'];?>" value=<?=$row['idDeterminacion'];?> />
            </td>  
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= substr($tipo_producto, 1);?></td> 
            <td bgcolor="#FFFFFF" align="center"><?= $tiposerv;?></td> 
            <td bgcolor="#FFFFFF" align="center"><?= $descripcion;?></td> 
            <td bgcolor="#FFFFFF" align="center">
              <div id="idpta<?= $row['idDeterminacion']; ?>"><?= $cedis1; ?></div>
              <select name="cedis1" class="campo" id="idpntaa<?=$row['idDeterminacion'];?>" style="display: none;">
                <option value=''>Seleccionar</option>
                <?php foreach ($plantas as $value): ?>
                      <option value="<?php echo $value['planta'].'/'. $value['loc'] ?>" <?php echo ($cedis1 == $value['planta'].'/'. $value['loc']) ? selected : '' ?>><?php echo $value['planta'].'/'. $value['loc'] ?></option>
                <?php endforeach; ?>
              </select>
            </td>    
             <td bgcolor="#FFFFFF" align="center">
              <div id="idptb<?= $row['idDeterminacion']; ?>"><?= $cedis2; ?></div>
              <select name="cedis2" class="campo" id="idpntab<?=$row['idDeterminacion'];?>" style="display: none;">
                <option value=''>Seleccionar</option>
                <?php foreach ($plantas as $value): ?>
                      <option value="<?php echo $value['planta'].'/'. $value['loc'] ?>" <?php echo ($cedis2 == $value['planta'].'/'. $value['loc']) ? selected : '' ?>><?php echo $value['planta'].'/'. $value['loc'] ?></option>
                <?php endforeach; ?>
              </select>
            </td>       
             <td bgcolor="#FFFFFF" align="center">
              <div id="idptc<?= $row['idDeterminacion']; ?>"><?= $cedis3; ?></div>
              <select name="cedis3" class="campo" id="idpntac<?=$row['idDeterminacion'];?>" style="display: none;">
                <option value=''>Seleccionar</option>
                <?php foreach ($plantas as $value): ?>
                      <option value="<?php echo $value['planta'].'/'. $value['loc'] ?>" <?php echo ($cedis3 == $value['planta'].'/'. $value['loc']) ? selected : '' ?>><?php echo $value['planta'].'/'. $value['loc'] ?></option>
                <?php endforeach; ?>
              </select>
            </td>       
             <td bgcolor="#FFFFFF" align="center">
              <div id="idptd<?= $row['idDeterminacion']; ?>"><?= $cedis4; ?></div>
              <select name="cedis4" class="campo" id="idpntad<?=$row['idDeterminacion'];?>" style="display: none;">
                <option value=''>Seleccionar</option>
                <?php foreach ($plantas as $value): ?>
                      <option value="<?php echo $value['planta'].'/'. $value['loc'] ?>" <?php echo ($cedis4 == $value['planta'].'/'. $value['loc']) ? selected : '' ?>><?php echo $value['planta'].'/'. $value['loc'] ?></option>
                <?php endforeach; ?>
              </select>
            </td>       
             <td bgcolor="#FFFFFF" align="center">
              <div id="idpte<?= $row['idDeterminacion']; ?>"><?= $cedis5; ?></div>
              <select name="cedis5" class="campo" id="idpntae<?=$row['idDeterminacion'];?>" style="display: none;">
                <option value=''>Seleccionar</option>
                <?php foreach ($plantas as $value): ?>
                      <option value="<?php echo $value['planta'].'/'. $value['loc'] ?>" <?php echo ($cedis5 == $value['planta'].'/'. $value['loc']) ? selected : '' ?>><?php echo $value['planta'].'/'. $value['loc'] ?></option>
                <?php endforeach; ?>
              </select>
            </td>       
             <td bgcolor="#FFFFFF" align="center">
              <div id="idptf<?= $row['idDeterminacion']; ?>"><?= $cedis6; ?></div>
              <select name="cedis6" class="campo" id="idpntaf<?=$row['idDeterminacion'];?>" style="display: none;">
                <option value=''>Seleccionar</option>
                <?php foreach ($plantas as $value): ?>
                      <option value="<?php echo $value['planta'].'/'. $value['loc'] ?>" <?php echo ($cedis6 == $value['planta'].'/'. $value['loc']) ? selected : '' ?>><?php echo $value['planta'].'/'. $value['loc'] ?></option>
                <?php endforeach; ?>
              </select>
            </td>       
             <td bgcolor="#FFFFFF" align="center">
              <div id="idptg<?= $row['idDeterminacion']; ?>"><?= $cedis7; ?></div>
              <select name="cedis7" class="campo" id="idpntag<?=$row['idDeterminacion'];?>" style="display: none;">
                <option value=''>Seleccionar</option>
                <?php foreach ($plantas as $value): ?>
                      <option value="<?php echo $value['planta'].'/'. $value['loc'] ?>" <?php echo ($cedis7 == $value['planta'].'/'. $value['loc']) ? selected : '' ?>><?php echo $value['planta'].'/'. $value['loc'] ?></option>
                <?php endforeach; ?>
              </select>
            </td>       
             <td bgcolor="#FFFFFF" align="center">
              <div id="idpth<?= $row['idDeterminacion']; ?>"><?= $cedis8; ?></div>
              <select name="cedis8" class="campo" id="idpntah<?=$row['idDeterminacion'];?>" style="display: none;">
                <option value=''>Seleccionar</option>
                <?php foreach ($plantas as $value): ?>
                      <option value="<?php echo $value['planta'].'/'. $value['loc'] ?>" <?php echo ($cedis8 == $value['planta'].'/'. $value['loc']) ? selected : '' ?>><?php echo $value['planta'].'/'. $value['loc'] ?></option>
                <?php endforeach; ?>
              </select>
            </td>                         
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
              <input type="button" id="edit_button<?= $row['idDeterminacion']; ?>" value="Editar" class="edit" onclick="edit_row(<?= $row['idDeterminacion'];?>)"> 
             <div id="showedt<?= $row['idDeterminacion']; ?>" style="display: none;">   
               <input type="button" id="save_button<?= $row['idDeterminacion']; ?>" value="Grabar" class="save" onclick="save_row(<?= $row['idDeterminacion']; ?>)">
               <input type="button" id="scancel_button<?= $row['idDeterminacion']; ?>" value="Cancelar" class="save" onclick="cancel_row(<?= $row['idDeterminacion']; ?>)">
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
