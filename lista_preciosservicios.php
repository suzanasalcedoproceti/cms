<?
    if (!include('ctrl_acceso.php')) return;
  include('funciones.php');
  $modulo=9;
  if (!op($modulo))  {
    $modulo=33;
    if (!op($modulo))  {
      $aviso = 'Usuario sin permiso para acceder a este módulo';
      $aviso_link = 'principal.php';
      include('mensaje_sistema.php');
      return;
    }
  }
  if (strpos($_SESSION['ss_opciones'], '33') !== false) {
   $ventas = 1;

 }else{
   $ventas = 0;
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
    form.action='lista_preciosservicios.php';
    form.submit();
  }

  function borra(id) {
  
    document.forma.sucursal.value = id;

    document.forma.action='borra_precioservicio.php';
    document.forma.submit();
  }
  function exportar() {
    document.forma.target = '_self';
    document.forma.action='lista_preciosservicios_xls.php';
    document.forma.numpag.value=1;
    document.forma.submit();
    document.forma.target = '_self';
    document.forma.action='';
  }
</script>
<script>

 function edit_row(no,ventas)
{ 

if(ventas!=0){

 $("#idprecio"+no).show();
 $("#precioserv"+no).hide();
 $("#showedt"+no).hide(); 
 $("#showedtvtas"+no).show();
 $("#showedtvtas"+no).css("display","inline-block");

 }
 else{
 $("#edit_button"+no).hide();  
 $("#showedt"+no).show(); 
 $("#idtiposerv"+no).hide();
 $("#idtiposervicio"+no).show(); 
 $("#costoserv"+no).hide();
 $("#idpreciocosto"+no).show(); 
 $("#tipo_prod"+no).hide(); 
 $("#tipo_producto"+no).show();
 $("#subtipo_prod"+no).hide();
 $("#subtipo_producto"+no).show();
 $("#clusters"+no).hide();
 $("#cluster"+no).show(); 
 $("#showedt"+no).show(); 
 $("#showedtvtas"+no).hide(); 
 $("#subcluster"+no).hide();
 $("#subclusteredt"+no).show();
 }
 
 $("#del_button"+no).hide(); 


     $("#tipo_producto"+no).change(function(event) {   
        if (event.target.value) {    
          $.get("lib.php?tipoprod=" + event.target.value + "", function(datasub, status){
            $("#subtipo_producto"+no).empty();
            $("#subtipo_producto"+no).append("<option value=''>Seleccione...</option>");
              for (j = 0; j < datasub.length; j++) {
                    $("#subtipo_producto"+no).html(datasub); 
                }
            });

        }
    }); 

    $("#cluster"+no).change(function(event) { 
    alert("ipas");
        if (event.target.value) {    
          $.get("lib.php?cve_cluster=" + event.target.value + "", function(datasub, status){
            $("#subclusteredt"+no).empty();
            $("#subclusteredt"+no).append("<option value=''>Seleccione...</option>");
              for (j = 0; j < datasub.length; j++) {
                    $("#subclusteredt"+no).html(datasub); 
                }
            });

        }
    });

 }

function save_row(no)
{
 $("#edit_button"+no).show();  
 $("#showedt"+no).hide();   

    var idprecioserv = no;    
    var idprecio= $("#idprecio"+no).val(); 
    var  idpreciocosto =$("#idpreciocosto"+no).val(); 
    var idtiposervicio= $("#idtiposervicio"+no).val(); 
    var tipo_producto= $("#tipo_producto"+no).val(); 
    var subtipo_producto= $("#subtipo_producto"+no).val();
    var cluster= $("#cluster"+no).val();
    var subcluster= $("#subclusteredt"+no).val();

     $("#idpreciocosto"+no).hide(); 

   
    $.post("graba_precioservicioajax.php", { idprecioserv: no,idprecio: idprecio,idpreciocosto:idpreciocosto,idtiposervicio:idtiposervicio,tipo_producto:tipo_producto,subtipo_producto:subtipo_producto,cluster:cluster,subcluster:subcluster}, function(data)
    {  location.reload(true); 
    console.log(data);});   
}

function cancel_row(no)
{
  $("#edit_button"+no).show();  
  $("#showedt"+no).hide();
  $("#showedtvtas"+no).hide();  
  $("#idprecio"+no).hide(); 
  $("#precioserv"+no).show();
  $("#costoserv"+no).show();
  
  $("#idtiposerv"+no).show();
  $("#idtiposervicio"+no).hide(); 
  $("#tipo_producto"+no).hide(); 
  $("#tipo_prod"+no).show(); 
  $("#subtipo_producto"+no).hide(); 
  $("#subtipo_prod"+no).show();
  $("#cluster"+no).hide(); 
  $("#clusters"+no).show(); 
  $("#del_button"+no).show();
  $("#idpreciocosto"+no).hide(); 

}
 
 $(function() {
    $("#tipo_productosel").change(function(event) {  
        if (event.target.value) {    
          $.get("lib.php?clavecluster=" + event.target.value + "", function(datasub, status){
            $("#sbtipo_").empty();
            $("#sbtipo_").append("<option value=''>Cualquier subtipo_producto...</option>");
              for (j = 0; j < datasub.length; j++) {
                    $("#sbtipo_").html(datasub); 
                }
            });

        }
    });  
  });  

 


    $(function() {
    $("#estado").change(function(event) {   
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
 
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Precio Servicio'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $estado = $_POST['estado'];
   $idestado = $_POST['idEstado'];
   $subcluster = $_POST['sbcluster'];
   $tipo_productosel= $_POST['tipo_productosel'];  
   $subtipo_productosel = $_POST['subtipo_productow'];
   $tipo_servicio = $_POST['tipo_servicio'];
   $cobertura_ = $_POST['cobertura_']; 
   $bsq=$_GET['bsq']; 
   $sbtipo_productosel=$_POST['subtipo_productow']; 
   $tipo_productoq = $_POST['tipo_producto']; 
   
   if (empty($tipo_productosel)) $condicionsub="WHERE 1";
   if (!empty($tipo_productosel))$condicionsub="WHERE clave ='$tipo_productosel' ";
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='estado'; 
   if ($ord=='estado') $orden='ORDER BY cluster'; 
   include('../conexion.php');  
  
?>
<div class="main">  

<form action="" method="post" name="forma" id="forma">
      <table width="95%" border="0" align="left" cellpadding="7" cellspacing="0" class="texto">
         <tr>
            <td><?php if($ventas!=1){ ?>
              <input name="button" type="submit" class="boton_agregar" id="button" value="Agregar precio servicio" onClick="document.forma.action='abc_precioservicios.php'; document.forma.submit();" />&nbsp; 
              <input name="buttonventas" type="submit" class="boton_agregar" id="buttoncosto" value="Importar costo servicios" onClick="document.forma.action='importa_preciosserviciosventas.php'; document.forma.submit();" />
              <?php } else {?><input name="button" type="submit" class="boton_agregar" id="buttonventas" value="Importar precio servicios" onClick="document.forma.action='importa_preciosservicios.php'; document.forma.submit();" /><?php }?></td>
            <td><div align="left">
                  <input name="exp_xls" type="button" class="boton" onclick="javascript:exportar();" value="Descargar Registros" />
                </div></td>
          </tr>

          <tr>
              <td>
                 <table width="100%"  cellpadding="7" cellspacing="0" class="texto">
                     <tr><td>Cluster: <br>
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
                          </td> 
 
                         <td>Subclsuter: <br>   
                          <select name="sbcluster" class="campo"  id="sbcluster" style="width: 180px;">
                          <option value="">Cualquier subcluster...</option>
                              <?  $resultadosubcl = mysql_query("SELECT *  FROM subcluster   
                                where cluster=$estado and  subcluster.subcluster> 0  group by subcluster.subcluster",$conexion);
                              while ($rowsubcl = mysql_fetch_array($resultadosubcl)) {
                                echo '<option value="'.$rowsubcl['subcluster'].'"';
                                if ($rowsubcl['subcluster']==$subcluster) echo 'selected';
                                  echo '>'.$rowsubcl['subcluster'].'</option>';
                                } 
                              ?>  
                                        
                           </select> 
                           </td> 


                         <td>Tipo producto:<br>
                            <select name="tipo_productosel" class="campo" id="tipo_productosel">
                             <option value="">Cualquier tipo de producto...</option>
                             <?  
                              $resultadoEDO = mysql_query("SELECT * FROM tipo_producto ORDER BY nombre",$conexion);
                              while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
                                echo '<option value="'.$rowEDO['nombre'].'"';
                                if ($rowEDO['nombre']==$tipo_productosel) echo 'selected';
                                  echo '>'.$rowEDO['clave'].' </option>';
                                } 
                              ?>
                             </select>
                         </td> 
                         <td>Tipo subtipo_producto: <br>  
                          <select name="subtipo_productow" class="campo"  id="sbtipo_" style="width: 180px;">
                           <option value="">Cualquier subtipoproducto...</option>
                              <?   
                              $resultadosb = mysql_query("SELECT subtipo_producto FROM subtipo_producto  
                               $condicionsub  order by idsubtipo_producto asc",$conexion);
                              while ($rowsb = mysql_fetch_array($resultadosb)) {
                                echo '<option value="'.$rowsb['subtipo_producto'].'"';
                                if ($rowsb['subtipo_producto']==$sbtipo_productosel) echo 'selected';
                                  echo '>'.$rowsb['subtipo_producto'].'</option>';
                                } 
                              ?>            
                           </select> 
                           </td> 
                       <td>Tipo servicio: <br>
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
                       <input name="boton_buscar" type="submit" class="boton_buscar" id="boton_buscar" value="Buscar" onclick="document.forma.submit();"></td>
                    </tr>

                  </table>
              </td>
          </tr>
 

          
          <tr>
 
          <tr>
            <td bgcolor="#BBBBBB"><?
      
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
                  // construir la condición de búsqueda
                     $condicion = "  WHERE  1  ";  

                     if (!empty($estado))
                     $condicion.= " AND  cluster='$estado'";   
                     if (!empty($subcluster))
                     $condicion.= " AND  subcluster='$subcluster'"; 
                     if (!empty($tipo_productosel))
                     $condicion.= " AND  tipo_producto='$tipo_productosel'";
                     if (!empty($subtipo_productosel))
                     $condicion.= " AND  subtipo_producto='$subtipo_productosel'";
                     if (!empty($tipo_servicio))
                     $condicion.= " AND  idServicio='$tipo_servicio'"; 
                               
                     // construir la condición de búsqueda
                       $resultadotot= mysql_query("SELECT 1 FROM precioservicios $condicion",$conexion);
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
                     echo "P&aacute;gina ".$numpag." de ".$totpags;
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



          <table width="95%" border="0" align="left" cellpadding="6" cellspacing="2">
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
            <td nowrap="nowrap" ><div align="center"><strong>Subcluster</strong></div></td>
            <td nowrap="nowrap" ><div align="center"><strong>Tipo producto</strong></div></td>
            <td nowrap="nowrap" ><div align="center"><strong>Subtipo producto</strong></div></td> 
            <td nowrap="nowrap" ><div align="center"><strong>Tipo Servicio</strong></div></td>
            <td nowrap="nowrap" ><div align="center"><strong>Descripci&oacute;n Servicio</strong></div></td>
            <td nowrap="nowrap" ><div align="center"><strong>Precio</strong></div></td> 
            <td nowrap="nowrap" ><div align="center"><strong>Costo</strong></div></td> 
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <? $x=0;
            $query="SELECT * FROM  precioservicios $condicion $orden LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
              $idPrecioservicio = $row['idPrecioservicio'];
              $cluster = $row['cluster'];
              $subcluster = $row['subcluster'];
              $tipo_producto = $row['tipo_producto'];
              $subtipo_producto = $row['subtipo_producto'];
              $idservicio = $row['idServicio'];
              $precio = $row['precio'];
              $costo = $row['costo'];
              $x++;

            $resultadoMpo = mysql_query("SELECT estado,mnpio FROM cp_sepomex WHERE cve_mnpio = $cve_mpio and cve_estado=$cve_edo",$conexion);
            $rowmpo = mysql_fetch_assoc($resultadoMpo);
            $nombrempio = $rowmpo['mnpio'];
            $nombreedo = $rowmpo['estado'];

            $resultadosrv = mysql_query("SELECT * FROM servicios WHERE idServicio = $idservicio",$conexion);
            $rowsrv= mysql_fetch_assoc($resultadosrv);
            $tiposerv = $rowsrv['tipo_servicio'];
            $descripcion = $rowsrv['descripcion'];

          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF" align="center"><div id="clusters<?= $row['idPrecioservicio']; ?>"><?=  $cluster ;?></div>
              
              <select name="cluster" class="campo" id="cluster<?= $row['idPrecioservicio']; ?>" style="width: 90px; display: none;">
                  <option value="">Seleccione</option> 
                     <?  
                    $resultadocluster = mysql_query("SELECT DISTINCT (cluster) FROM estados ORDER BY estado",$conexion);
                    while ($rowcluster= mysql_fetch_array($resultadocluster)) {
                      echo '<option value="'.$rowcluster['cluster'].'"';
                      if ($rowcluster['cluster']==$cluster) echo 'selected';
                        echo '>'.$rowcluster['cluster'].'</option>';
                      } ?>
              </select>

            </td>  

 
             <td bgcolor="#FFFFFF" align="center"> 
              <div id="subcluster<?= $row['idPrecioservicio']; ?>"><?= $subcluster;?></div> 

             <select name="subcluster" class="campo" id="subclusteredt<?= $row['idPrecioservicio']; ?>" style="width: 90px; display: none;"> 

               <option value="">Seleccione...</option>
                              <?  $resultadosubcl = mysql_query("SELECT *  FROM subcluster   
                                where cluster=$cluster and  subcluster.subcluster> 0  group by subcluster.subcluster",$conexion);
                              while ($rowsubcl = mysql_fetch_array($resultadosubcl)) {
                                echo '<option value="'.$rowsubcl['subcluster'].'"';
                                if ($rowsubcl['subcluster']==$subcluster) echo 'selected';
                                  echo '>'.$rowsubcl['subcluster'].'</option>';
                                } 
                              ?>            
              </select>   

             </td>  




            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
              <div id="tipo_prod<?= $row['idPrecioservicio']; ?>"><?= substr($row['tipo_producto'], 1);?></div>  
                <select name="tipo_producto" class="campo" id="tipo_producto<?= $row['idPrecioservicio']; ?>" style="width: 90px; display: none;">  
                       <? $resultadotpr = mysql_query("SELECT * FROM tipo_producto ORDER BY nombre",$conexion);
                            while ($rowtpr = mysql_fetch_array($resultadotpr)) {
                                    echo '<option value="'.$rowtpr['nombre'].'"';
                                    if ($rowtpr['nombre']==$tipo_producto) echo 'selected';
                                      echo '>'.$rowtpr['clave'].' </option>';
                                    } 
                       ?>
                </select>  
            </td>
             <td bgcolor="#FFFFFF" align="center">
              <div id="subtipo_prod<?= $row['idPrecioservicio']; ?>"><?= $subtipo_producto ; ?></div>

             <select name="subtipo_producto" class="campo" id="subtipo_producto<?= $row['idPrecioservicio']; ?>" style="width: 90px; display: none;">  
                      <option value="">Seleccione...</option>
                         <?  $resultadoedt = mysql_query("SELECT subtipo_producto FROM subtipo_producto  
                               WHERE clave='$tipo_producto' order by idsubtipo_producto asc",$conexion);
                              while ($rowedt = mysql_fetch_array($resultadoedt)) {
                                echo '<option value="'.$rowedt['subtipo_producto'].'"';
                                if ($rowedt['subtipo_producto']==$subtipo_producto) echo 'selected';
                                  echo '>'.$rowedt['subtipo_producto'].'</option>';
                                } 
                          ?>            
              </select>   
             </td>  

              <td bgcolor="#FFFFFF" align="center">  
               <div id="idtiposerv<?= $row['idPrecioservicio']; ?>"><?= $tiposerv; ?> </div> 
               <select name="idservicio" class="campo" id="idtiposervicio<?= $row['idPrecioservicio']; ?>"" style="width: 140px; display: none;">
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
              <td bgcolor="#FFFFFF" align="center"><?= $descripcion;?></td> 
            <td bgcolor="#FFFFFF" align="center"><div id="precioserv<?= $row['idPrecioservicio']; ?>"><?= $precio; ?></div>
              <input name="cluster" type="text" class="campo" id="idprecio<?=$row['idPrecioservicio'];?>" value="<?= $row['precio']; ?>" size="9" maxlength="9"  style="display: none;"/>

            </td>   
            <td bgcolor="#FFFFFF" align="center"><div id="costoserv<?= $row['idPrecioservicio']; ?>"><?= $costo; ?></div>
              <input name="cluster" type="text" class="campo" id="idpreciocosto<?=$row['idPrecioservicio'];?>" value="<?= $row['costo']; ?>" size="9" maxlength="9"  style="display: none;"/>

            </td>        
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
              <input type="button" id="edit_button<?= $row['idPrecioservicio']; ?>" value="Editar" class="edit" onclick="edit_row(<?= $row['idPrecioservicio'].','. $ventas;?>)"> 
              <?if ($ventas==0) {?><a onclick="return confirm('\u00bfEst\u00e1s seguro que deseas\nBorrar el precio servicio?')" href="javascript:borra('<?= $row['idPrecioservicio']; ?>');">
<input type="button" id="del_button<?= $row['idPrecioservicio']; ?>" value="Borrar" class="save"> </a><? } else  { ?> <? } ?>
              <div id="showedt<?= $row['idPrecioservicio']; ?>" style="display: none;">   
                <input type="button" id="save_button<?= $row['idPrecioservicio']; ?>" value="Grabar" class="save" onclick="save_row(<?= $row['idPrecioservicio']; ?>)">
                <input type="button" id="scancel_button<?= $row['idPrecioservicio']; ?>" value="Cancelar" class="save" onclick="cancel_row(<?= $row['idPrecioservicio']; ?>)">     
                <? if ($rel<=0) { ?><a onclick="return confirm('\u00bfEst\u00e1s seguro que deseas\nBorrar el precio servicio?')" href="javascript:borra('<?= $row['idPrecioservicio']; ?>');">
<input type="button" id="del_button<?= $row['idPrecioservicio']; ?>" value="Borrar" class="save"> </a><? } else  { ?><input type="button" id="del_button<?= $row['idPrecioservicio']; ?>" value="Borrar" class="save"><? } ?>       
             </div> 
             <div id="showedtvtas<?= $row['idPrecioservicio']; ?>" style="display: none;">   
                <input type="button" id="save_button<?= $row['idPrecioservicio']; ?>" value="Grabar" class="save" onclick="save_row(<?= $row['idPrecioservicio']; ?>)">
                <input type="button" id="scancel_button<?= $row['idPrecioservicio']; ?>" value="Cancelar" class="save" onclick="cancel_row(<?= $row['idPrecioservicio']; ?>)">   
             </div> 
              

               
            </td>

      </tr>
          <? } // WHILE 
            mysql_close();
          ?>
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
</body>
</html>
