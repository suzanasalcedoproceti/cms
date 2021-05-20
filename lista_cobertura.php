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
<link rel='stylesheet' href='css/bootstrap.min.css'>
<link rel='stylesheet' href='css/bootstrap-multiselect.css'>
 
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<link href="css/thickbox.css" rel="stylesheet" type="text/css" >
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript" src="js/jquery.js"></script> 
 <!-- Include Twitter Bootstrap and jQuery: -->
 
 
<script language="JavaScript"> 

  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='lista_cobertura.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	  document.forma.numpag.value = 1;
    document.forma.action='lista_cobertura.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.sucursal.value = id;
    document.forma.action='borra_cobertura.php';
    document.forma.submit();
  }
  function exportar() {
    document.forma.target = '_self';
    document.forma.action='lista_cobertura_xls.php';
    document.forma.numpag.value=1;
    document.forma.submit();
    document.forma.target = '_self';
    document.forma.action='';
  }
</script>
<script type="text/javascript">


 function edit_row(no)
{
 $("#edit_button"+no).hide();  
 $("#showedt"+no).show(); 
 $("#cobertura"+no).show(); 
 $("#sucursal"+no).hide(); 
 $(".btn-group"+ no).show();
 $("#cobert"+no).hide(); 
 $("#idscrs"+no).hide();  
 $("#sucursal"+no).multiselect();

}

function save_row(no)
{
 $("#edit_button"+no).show();  
 $("#showedt"+no).hide();  
 $("#cobertura"+no).hide(); 
 $("#sucursal"+no).hide(); 
    var idcobertura = no;
    var idsucursal= $("#sucursal"+no).val(); 
    var cobertura= $("#cobertura"+no).val();  
    var rest= " " +idsucursal +" ";  
     
    $.post("graba_sucursalcoberturaajax.php", { idcobertura: no,cobertura: cobertura,idsucursal:rest}, function(data)
    {  location.reload(true);
       console.log(data) });   

}

function cancel_row(no)
{
  $("#edit_button"+no).show();  
  $("#showedt"+no).hide();  
  $("#idscrs"+no).show();  
  $("#sucursal"+no).hide();  
  $(".btn-group"+ no).hide();  
  $("#cobertura"+no).hide(); 
  $("#cobert"+no).show();  

}

function borra(id) { 
    document.forma.IDcobertura.value = id; 
    document.forma.action='borra_cobertura.php';
    document.forma.submit();
  }

$(function() {

    $("#estado").change(function(event) {

        if (event.target.value) {

          $.get("lib.php?estado=" + event.target.value + "", function(data, status){
            $("#municipio").empty();
            $("#municipio").append("<option value=''>Cualquier municipio...</option>");

              for (j = 0; j < data.length; j++) {
                    $("#municipio").html(data);
                }
            });
        } else {
            $("#municipio").empty();
            $("#municipio").append("<option value=''>Cualquier municipio...</option>");
        }
    });
  });
</script>


</head> 
<body>
<div id="container">
  <? $tit='Listado de Coberturas'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $estado = $_POST['estado'];
   $idestado = $_POST['idEstado'];
   $municipio = $_POST['municipio'];
   $tipo_producto = $_POST['tipo_producto'];
   $tipo_servicio = $_POST['tipo_servicio'];
   $cobertura_=$_POST['cobertura_'];  

   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='estado'; 
   if ($ord=='estado') $orden='ORDER BY cve_estado'; 
   include('../conexion.php'); 
 
?>
	<div class="main">   



    <table border="0" align="left" cellpadding="2" cellspacing="0" class="texto" width="380">
          <tr>
            <td>  <form action="" method="post" name="forma2" id="forma2"> 
              <input type="hidden" name="cobertura_" id="nocobertura_" value="NO"> 
             <input name="button" type="submit" class="boton_agregar" id="button" value="Listado sin cobertrura" onClick="document.forma2.action='lista_cobertura.php'; document.forma2.submit();" />
             </form>
             </td>
            <td>
              <form action="" method="post" name="forma3" id="forma3">
               <input name="button" type="submit" class="boton_agregar" id="button" value="Agregar cobertura" onClick="document.forma3.action='abc_cobertura.php'; document.forma3.submit();" />
             </form> 
             </td>
            <td>
              <form action="" method="post" name="forma4" id="forma4">
               <input name="button" type="submit" class="boton_agregar" id="button" value="Importar cobertura" onClick="document.forma4.action='importa_cobertura.php'; document.forma4.submit();" />
             </form> 
             </td>
             <td><div align="left">
                  <input name="exp_xls" type="button" class="boton" onclick="javascript:exportar();" value="Descargar Registros" />
                </div></td>
          </tr>
  </table>
 
 <br><br>
      <form action="" method="post" name="forma" id="forma">   
        <table width="95%" border="0" align="left" cellpadding="7" cellspacing="0" class="texto"> 
          <tr>
            <td colspan="2">Filtros:
              <select name="estado" class="campo" id="estado">
                <option value="">Cualquier estado...</option>
                  <? $resultadoEDO = mysql_query("SELECT * FROM estados ORDER BY estado",$conexion);
            					while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
            					  echo '<option value="'.$rowEDO['idEstado'].'"';
            					  if ($rowEDO['idEstado']==$estado) echo 'selected';
            				  	  echo '>'.$rowEDO['estado'].'</option>';
            				    } ?>
                </select>  
                <select name="municipio" class="campo" id="municipio">
                   <option value="">Cualquier municipio...</option>
                     <? $resultadoEDO = mysql_query("SELECT distinct mnpio,cve_mnpio FROM cp_sepomex where cve_estado=$estado order by mnpio asc",$conexion);
                      while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
                        echo '<option value="'.$rowEDO['cve_mnpio'].'"';
                        if ($rowEDO['cve_mnpio']==$municipio) echo 'selected';
                          echo '>'.$rowEDO['mnpio'].'</option>';
                        } ?>            
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
               <? $resultadoEDO = mysql_query("SELECT * FROM servicios ORDER BY idservicio",$conexion);
                  while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
                    echo '<option value="'.$rowEDO['idservicio'].'"';
                    if ($rowEDO['idservicio']==$tipo_servicio) echo 'selected';
                      echo '>'.$rowEDO['tipo_servicio'].' '.$rowEDO['descripcion'].'</option>';
                    }   ?>
            </select>
            <select name="cobertura_" class="campo" id="cobertura_">
               <option value="">Cualquier cobertura...</option>
              <option value="SI" <? if ($cobertura_=='SI') echo 'selected';?>>SI</option>
              <option value="NO" <? if ($cobertura_=='NO') echo 'selected';?>>NO</option> 
            </select> 
            <input name="boton_buscar" type="submit" class="boton_buscar" id="boton_buscar" value="Buscar" onclick="document.forma.submit();">
          </td>
          </tr>
 
          <tr>
            <td bgcolor="#BBBBBB"><?
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
                     // construir la condición de búsqueda
                      $condicion = "  WHERE  1  ";  
                     if (!empty($estado))  
                     $condicion.= " AND  cve_estado='$estado'";
                     if (!empty($municipio))                     
                     $condicion.= "  AND  cve_mnpio='$municipio'";
                     if (!empty($tipo_producto))
                     $condicion.= "  AND  tipo_producto='$tipo_producto'";
                     if (!empty($tipo_servicio))  
                     $condicion.= "  AND  idServicio='$tipo_servicio'";
                     if (isset($_POST['cobertura_'])) 
                     {$cobertura_=$_POST['cobertura_'];
                       if ($cobertura_!='') $condicion.= "AND  cobertura='$cobertura_'";  
                      }
                    else
                      { $cobertura_='SI'; $condicion.= "AND  cobertura='$cobertura_'"; } 

                     // construir la condición de búsqueda 
                       $resultadotot= mysql_query("SELECT 1 FROM cobertura $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0; 
					 echo 'Total de coberturas en la lista: <b>'.$totres.'</b>';
			
			  ?></td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="IDcobertura" type="hidden" id="IDcobertura" />
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
                         echo '<a href="javascript:ir(document.forma,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="P&aacute;gina anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "P&aacute;gina  ".$numpag." de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";
                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Última p&aacute;gina"></a>';
                     }
              ?>            </td>
          </tr>
        </table>



       <table width="95%" border="0" align="left" cellpadding="7" cellspacing="1" class="texto">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td><div align="center"><strong>Estado</strong></div></td>
            <td><div align="center"><strong>Municipio</strong></div></td>
            <td><div align="center"><strong>Tipo de producto</strong></div></td> 
            <td><div align="center"><strong>Tipo Servicio</strong></div></td> 
            <td><div align="center"><strong>Cobertura</strong></div></td>
            <td><div align="center"><strong>Sucursal (ID)</strong></div></td>
            <td><div align="center"><strong>Subcategor&iacute;a 1</strong></div></td>
            <td><div align="center"><strong>Subcategor&iacute;a 2</strong></div></td>
            <td ><div align="center"><strong>Subcategor&iacute;a 3</strong></div></td>
            <td><div align="center"><strong>Subcategor&iacute;a 4</strong></div></td>
          
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
			      $query = "SELECT * from cobertura
						$condicion $orden LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			    	  $idCobertura = $row['idCobertura'];
              $cve_mpio = $row['cve_mnpio'];
              $cve_edo = $row['cve_estado'];
              $idserv = $row['idServicio']; 
              $tipo_producto=$row['tipo_producto'];
          
            $resultadoMpo = mysql_query("SELECT estado,mnpio FROM cp_sepomex WHERE cve_mnpio = $cve_mpio and cve_estado=$cve_edo",$conexion);
            $rowmpo = mysql_fetch_assoc($resultadoMpo);
            $nombrempio = $rowmpo['mnpio'];
            $nombreedo = $rowmpo['estado'];
            $resultadosrv = mysql_query("SELECT * FROM servicios WHERE idServicio = $idserv",$conexion);
            $rowsrv= mysql_fetch_assoc($resultadosrv);
            $tiposerv = $rowsrv['tipo_servicio'];
            $descripcion = $rowsrv['descripcion'];
            $resultadoclstr = mysql_query("SELECT * FROM  estados where cve_estado= $cve_edo",$conexion);
            $rowclstr= mysql_fetch_assoc($resultadoclstr);
            $cluster = $rowclstr['cluster'];   
            $resultadosucrs = mysql_query("SELECT * FROM  cobertura_sucursal where idCobertura= $idCobertura",$conexion); 

             while ($rowsucrs = mysql_fetch_array($resultadosucrs)){ 
            $idcobertura_sucursal = $rowsucrs['idcobertura_sucursal']; 
            $cobertura_suc = $rowsucrs['idsuc']; 
             
           }
          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF" align="center"><?=  $nombreedo ;?></td> 
            <td bgcolor="#FFFFFF" align="center"><?=  $nombrempio;?></td> 
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= substr($row['tipo_producto'], 1);?></td>
             <td bgcolor="#FFFFFF" align="center"><?= $tiposerv ; ?> (<?= $descripcion ; ?>) </td>   
            <td bgcolor="#FFFFFF" align="center"><div id="cobert<?= $row['idCobertura']; ?>"><?= $row['cobertura'];?></div>
              <select name="cobertura" class="camporeq" id="cobertura<?= $row['idCobertura']; ?>" style="display: none;">
              <option value=""   <? if ($row['cobertura']=='') echo 'selected';?>>Selecciona</option>
              <option value="SI" <? if ($row['cobertura']=='SI') echo 'selected';?>>SI</option>
              <option value="NO" <? if ($row['cobertura']=='NO') echo 'selected';?>>NO</option> 
            </select>  
            </td>           
            <td align="center" bgcolor="#FFFFFF"><div id="idscrs<?= $row['idCobertura']; ?>">
              <? $resultadosucursales = mysql_query("SELECT * FROM  cobertura_sucursal where idCobertura= $idCobertura",$conexion); 
                $trae_sucursal = array(); 
              
                while ($rowsucursales = mysql_fetch_assoc($resultadosucursales)){ 
                  $idcobertura_sucursal = $rowsucursales['idcobertura_sucursal']; 
                  $cobertura_suc= $rowsucursales['idsuc']; 
                  if($cobertura_suc == 0) {$cobertura_sucursales= 'N/A';} else{$cobertura_sucursales=$cobertura_suc;} 
                  echo "(". $cobertura_sucursales .")  ";  } 
                  ?>      
              </div>  
              <div class="btn-group<?= $row['idCobertura']; ?>" />
            <div style="text-align: left;" id="btnselect<?= $row['idCobertura']; ?>"> 
            <select multiple="multiple"  name="sucursal[] " class="selectm"   id="sucursal<?= $row['idCobertura']; ?>" style="display: none; text-align: ">   

                   <option value="0" <? if ($cobertura_suc==0) echo 'selected'; ?>>N/A</option>
                  <? $resultadoSUC = mysql_query("SELECT sucursales.*, estados.estado AS nombre_estado FROM sucursales INNER JOIN estados ON sucursales.cve_estado = estados.cve_estado WHERE 1 ORDER BY cve_estado, nombresucursal ",$conexion); 
                     while ($rowSUC = mysql_fetch_array($resultadoSUC)) {
                       $idss=$rowSUC['idsuc'];  
                       $resultadosucrs2 = mysql_query("SELECT * FROM  cobertura_sucursal where idsuc= $idss AND idCobertura= $idCobertura",$conexion); 
                       $rowsucrs2= mysql_fetch_assoc($resultadosucrs2);
                       $cobertura_suc2= $rowsucrs2['idsuc'];   

                       echo '<option value="'.$rowSUC['idsuc'].'"';
                       if ($idss==$cobertura_suc2) echo 'selected';
                       echo '>'.$rowSUC['nombresucursal'].'  '. " (ID: ". $rowSUC['idsuc']. ")" .'  </option>';
                      
                       }  ?>
              </select> 
            </div>
            </td> 
              <? $queryw= "SELECT subtipo_producto  FROM precioservicios where cluster= $cluster AND tipo_producto = '$tipo_producto' AND idServicio=$idserv";
                $resultadow= mysql_query($queryw,$conexion);
               while ($roww = mysql_fetch_array($resultadow)){ 
                $subtipo_prd=$roww['subtipo_producto'];    
                $prefsubtipo=substr($roww['subtipo_producto'], -0 , 1);  
              }?>       
           <td bgcolor="#FFFFFF" align="center" width="80"> <? 
               $prefsubtipo1= $prefsubtipo.'1';   
               $resulta = mysql_query("SELECT subtipo_producto  FROM precioservicios where cluster= $cluster AND tipo_producto = '$tipo_producto' AND subtipo_producto='$prefsubtipo1' AND idServicio=$idserv",$conexion);
                $rowcr= mysql_fetch_assoc($resulta);
                $ss1 = $rowcr['subtipo_producto'];  
                if(empty($ss1)) $preciosub1="NO";
                if(!empty($ss1)) $preciosub1="SI";
                echo $preciosub1; ?>  </td> 
            <td bgcolor="#FFFFFF" align="center" width="80"> <?
             $prefsubtipo1= $prefsubtipo.'2';   
             $resulta = mysql_query("SELECT subtipo_producto  FROM precioservicios where cluster= $cluster AND tipo_producto = '$tipo_producto' AND subtipo_producto='$prefsubtipo1' AND idServicio=$idserv",$conexion);
                $rowcr= mysql_fetch_assoc($resulta);
                $ss2 = $rowcr['subtipo_producto'];  
                if(empty($ss2)) $preciosub2="NO";
                if(!empty($ss2)) $preciosub2="SI";
                echo $preciosub2; ?>  </td> 
          <td bgcolor="#FFFFFF" align="center" width="80"> <?
             $prefsubtipo1= $prefsubtipo.'3';   
             $resulta = mysql_query("SELECT subtipo_producto  FROM precioservicios where cluster= $cluster AND tipo_producto = '$tipo_producto' AND subtipo_producto='$prefsubtipo1' AND idServicio=$idserv",$conexion);
              $rowcr= mysql_fetch_assoc($resulta);
              $ss3 = $rowcr['subtipo_producto']; 
           
			  if($ss3<=0) $preciosub3="NO";
              if($ss3>0) $preciosub3="SI";
              echo $preciosub3;?>  </td> 
            <td bgcolor="#FFFFFF" align="center" width="80"> <?
             $prefsubtipo1= $prefsubtipo.'4';   
			 $rr="SELECT subtipo_producto  FROM precioservicios where cluster= $cluster AND tipo_producto = '$tipo_producto' AND subtipo_producto='$prefsubtipo1' AND idServicio=$idserv";
					 
             $resulta = mysql_query("SELECT subtipo_producto  FROM precioservicios where cluster= $cluster AND tipo_producto = '$tipo_producto' AND subtipo_producto='$prefsubtipo1' AND idServicio=$idserv",$conexion);
              $rowcr= mysql_fetch_assoc($resulta);
              $ss4 = $rowcr['subtipo_producto'];  
              if($ss4<=0) $preciosub4="NO";
              if($ss4>0) $preciosub4="SI";
              echo $preciosub4;?>  </td>         
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
              <input type="button" id="edit_button<?= $row['idCobertura']; ?>" value="Editar" class="edit" onclick="edit_row(<?= $row['idCobertura'];?>)"> 
            <div id="showedt<?= $row['idCobertura']; ?>" style="display: none;">  
              <input type="button" id="save_button<?= $row['idCobertura']; ?>" value="Grabar" class="save" onclick="save_row(<?= $row['idCobertura']; ?>)">
              <input type="button" id="cancel_button<?= $row['idCobertura']; ?>" value="Cancelar" class="save" onclick="cancel_row(<?= $row['idCobertura']; ?>)">
           <? if ($rel<=0) { ?><a onclick="return confirm('\u00bfEst\u00e1s seguro que deseas\nBorrar la cobertura?')" href="javascript:borra('<?= $row['idCobertura']; ?>');">
<input type="button" value="Borrar" class="save"> </a><? } else  { ?><input type="button" value="Borrar" class="save"><? } ?>
            </div> 
           </td>

		  </tr>
          <? } // WHILE
               mysql_close(); ?>
        </table>
        <table width="95%" border="0" align="left" cellpadding="7" cellspacing="0" class="texto">
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

 
 <script src='js/jquery.min.js'></script><script src='js/bootstrap.min.js'></script><script src='js/bootstrap-multiselect.js'></script>

</body>
</html>
