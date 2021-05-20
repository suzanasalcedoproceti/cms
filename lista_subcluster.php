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
    form.action='lista_subcluster.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	  document.forma.numpag.value = 1;
    document.forma.action='lista_subcluster.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.sucursal.value = id;
    document.forma.action='lista_subcluster.php';
    document.forma.submit();
  }
  
</script>
<script type="text/javascript">


 function edit_row(no,idsubcluster)
{
 $("#edit_button"+no).hide();  
 $("#showedt"+no).show(); 
 $("#showedttr"+no).show();
 $("#showedtcl"+no).show();
 $("#showedtclh"+no).hide();  
 var subcluster= $("#inputedtcl"+no).val(); 
 var subclusterlh= $("#inputedt"+no).val(); 

 }

  
function save_row(no,idsubcluster)
{ 
 $("#edit_button"+no).show();  
 $("#showedt"+no).hide();   
    var subcluster= $("#inputedtcl"+no).val(); 
    var subclusterlh= $("#inputedt"+no).val();   
    $.post("graba_subclusterajax.php", { idsubcluster: idsubcluster,subcluster:subcluster}, function(data)
    {  location.reload(true); });   
}


function cancel_row(no)
{
  $("#edit_button"+no).show();  
  $("#inputedtcl"+no).hide();   
  $("#inputedt"+no).hide();   
  $("#showedt"+no).hide(); 
}

function exportar() {
    document.forma.target = '_self';
    document.forma.action='lista_subcluster_xls.php';
    document.forma.numpag.value=1;
    document.forma.submit();
    document.forma.target = '_self';
    document.forma.action='';
  }

 
$(function() {

    $("#estados").change(function(event) {

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

    $(function() {
    $("#estadoss").change(function(event) {   
        if (event.target.value) {    
          $.get("lib.php?cve_cluster=" + event.target.value + "", function(datasub, status){
            $("#sbcluster").empty();
            $("#sbcluster").append("<option value=''>Seleccione...</option>");
              for (j = 0; j < datasub.length; j++) {
                    $("#sbcluster").html(datasub); 
                }
            });

        }
    });  
  });  

    $(function() {

    $("#estado").change(function(event) {
 
        if (event.target.value) {

          $.get("lib.php?cve_estado=" + event.target.value + "", function(data, status){
            $("#municipios").empty();
            $("#municipios").append("<option value=''>Cualquier municipio...</option>");

              for (j = 0; j < data.length; j++) {
                    $("#municipios").html(data);
                }
            });
        } else {
            $("#municipios").empty();
            $("#municipios").append("<option value=''>Cualquier municipio...</option>");
        }
    });
  });


$(function() {

    $("#cluster").change(function(event) {

 
        if (event.target.value) {
                      $("#municipios").empty();
            $("#municipios").append("<option value=''>Subcluster...</option>");

          $.get("lib.php?cvecluster=" + event.target.value + "", function(data, status){
            $("#estado").empty();
            $("#estado").append("<option value=''>Cualquier estado...</option>");

              for (j = 0; j < data.length; j++) {
                    $("#estado").html(data);
                }
            });
        } else { 
            $("#estado").empty();
            $("#estado").append("<option value=''>Cualquier estado...</option>");
        }
    });
  });
</script>


</head> 
<body>
<div id="container">
  <? $tit='Listado de Subcluster'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $estado = $_POST['estado'];
   $idestado = $_POST['idEstado'];
   $cluster= $_POST['cluster'];
   $cvesubcluster=$_POST['municipios'];
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
    <div style="float: left;"> 
         <table border="0" align="center" cellpadding="2" cellspacing="0" class="texto" width="380" >
           <tr> 
            <td>
              <form action="" method="post" name="forma4" id="forma4">
               <input name="button" type="submit" class="boton_agregar" id="button" value="Importar subcluster" onClick="document.forma4.action='importa_subcluster.php'; document.forma4.submit();" />
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
        <table width="95%" border="0" align="left" cellpadding="7" cellspacing="0" class="texto"> 
          <tr>
            <td colspan="2">Cluster: 
              <select name="cluster" class="campo" id="cluster">
                  <option value="">Cualquier cluster...</option>
                     <? $resultadoEDO = mysql_query("SELECT cluster FROM estados GROUP BY cluster",$conexion);
                       while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
                        echo '<option value="'.$rowEDO['cluster'].'"';
                        if ($rowEDO['cluster']==$cluster) echo 'selected';
                          echo '>'.$rowEDO['cluster'].'</option>';
                        } ?>
                </select>  
                &nbsp;&nbsp; Estado:
              <select name="estado" class="campo" id="estado">
                  <option value="">Cualquier estado...</option>
                     <? $resultadoEDO = mysql_query("SELECT *  FROM estados where cluster=$cluster",$conexion);
            					 while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
            					  echo '<option value="'.$rowEDO['idEstado'].'"';
            					  if ($rowEDO['idEstado']==$estado) echo 'selected';
            				  	  echo '>'.$rowEDO['estado'].'</option>';
            				    } ?>
                </select>   

                &nbsp;&nbsp; Subcluster: 
               <select name="municipios" class="campo" id="municipios">
                   <option value="">Seleccione...</option>  
                     <? $resultadoEDO = mysql_query("SELECT subcluster FROM subcluster   
                        where cluster= $cluster and cve_estado =$estado and  subcluster> 0 group by subcluster",$conexion);
                      while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
                        echo '<option value="'.$rowEDO['subcluster'].'"';
                        if ($rowEDO['subcluster']==$cvesubcluster) echo 'selected';
                          echo '>'.$rowEDO['subcluster'].'</option>';
                        } ?>            
                </select>
              &nbsp;&nbsp;  
            <input name="boton_buscar" type="submit" class="boton_buscar" id="boton_buscar" value="Buscar" onclick="document.forma.submit();">
          </td>
          </tr>
 
          <tr>
            <td bgcolor="#BBBBBB"><?
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
                     // construir la condición de búsqueda
                     $condicion = "  WHERE  1  ";  
                     if (!empty($cluster))  
                     $condicion.= " AND  cluster='$cluster'";
                     if (!empty($estado))  
                     $condicion.= " AND  cve_estado='$estado'";
                    if (!empty($cvesubcluster))  
                     $condicion.= " AND  subcluster='$cvesubcluster'";
                    
                     // construir la condición de búsqueda 
                   $sel="SELECT * FROM subcluster $condicion";
                       $resultadotot= mysql_query("SELECT * FROM subcluster $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0; 
					 echo 'Total de coberturas en la lista: <b>'.$totres.'</b>'; 
	  
			  ?></td>
        <td align="right" bgcolor="#BBBBBB"> 
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
              ?>            
            </td>
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
            <td><div align="center"><strong>Cluster</strong></div></td> 
            <td><div align="center"><strong>Subcluster</strong></div></td>
             
          
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
          $i=0;
			      $queryres = "SELECT * from subcluster
						$condicion LIMIT $regini,$ver";
             $resultado= mysql_query($queryres,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			    	  $idSubcluster = $row['idsubcluster'];
              $cve_mpio = $row['cve_mnpio'];
              $cve_edo = $row['cve_estado'];
              $subcluster2 = $row['subcluster'];  
      
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
            <td bgcolor="#FFFFFF" align="center"><?=  $rowclstr['cluster']?></td> 


            <td bgcolor="#FFFFFF" align="center" id="edtcluster<?=$i;?>">
              <div id="showedtclh<?=$i;?>"> 
                <?= $row['subcluster']; ?>  
              </div>
              <div id="showedtcl<?=$i;?>" style="display: none;"> 
               <select name="subcluster" class="camporeq" id="inputedtcl<?=$i;?>">
                 <option value=""  selected>Selecciona</option> 
                 <?
                 $ser =$row['subcluster'];
                 for ($v = 1; $v <= 100; $v++) {
                   if ($row['subcluster']==$v) {$sel='selected';} else  {$sel=' ';}
                        echo "(<option value='$v' $sel> $v </option >'.'<br>')"; 
                 } ?>
              
               </select> 
              </div> 
           </td> 
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">  
              <input type="button" id="edit_button<?= $i; ?>" value="Editar" class="edit" onclick="edit_row(<?=$i .",". $row['idsubcluster'];?>)"> 
                <div id="showedt<?= $i; ?>" style="display: none;">   
              <input type="button" id="save_button<?= $i;?>" value="Grabar" class="save" onclick="save_row(<?=$i .",". $row['idsubcluster'];?>)">
              <input type="button" id="cancel_button<?= $i;?>" value="Cancelar" class="save" onclick="cancel_row(<?=$i ;?>)">
           
            </div>  </td>        
      
  

		      </tr>
          <? $i++; } // WHILE

         
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
