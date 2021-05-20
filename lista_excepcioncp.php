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
    form.action='lista_excepcioncp.php';
    form.submit();
  }
  function ordena(orden,imgs) {
    document.forma.ord.value = orden; 
    document.forma.imgorden.value = imgs;  
    document.forma.numpag.value = 1;
    document.forma.action='lista_excepcioncp.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.idexcepcioncp.value = id;
    document.forma.action='borra_excepcioncp.php';
    document.forma.submit();
  }
  function exportar() {
    document.forma.target = '_self';
    document.forma.action='lista_excepcioncp_xls.php';
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
 $("#showcp"+no).hide(); 
 $("#showedtcp"+no).show(); 
 $("#tipo_prod"+no).hide(); 
 $("#tipo_producto"+no).show();
 $("#tiposerv"+no).hide(); 
 $("#tipo_servicio"+no).show();
  
 
}

function save_row(no)
{
 $("#edit_button"+no).show();  
 $("#showedt"+no).hide();  
 $("#cobertura"+no).hide(); 
 $("#sucursal"+no).hide();  
    var idcp = no;
    var cpexc=cpexc;
    var cpexcnuevo= $("#cpnew"+no).val(); 
    var tipo_producto= $("#tipo_producto"+no).val();  
 
    $.post("graba_excepcioncpajax.php", {idcp:idcp,cpexcnuevo:cpexcnuevo,tipo_producto:tipo_producto}, function(data)
    {  location.reload(true);
       console.log(data) });   
}

function cancel_row(no)
{
  $("#edit_button"+no).show();  
  $("#showedt"+no).hide();  
  $("#idscrs"+no).show();  
  $("#showcp"+no).show(); 
  $("#showedtcp"+no).hide(); 
  $("#tipo_prod"+no).show(); 
  $("#tipo_producto"+no).hide();
  $("#tiposerv"+no).show(); 
  $("#tipo_servicio"+no).hide();
}
 
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Excepciones'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $orden = $_POST['ord'];  
   $imgs=$_POST['imgorden'];
   

   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($orden)) $orden='ASC'; 
   if (empty($imgs)) $imgs='subir.png'; 
 
   include('../conexion.php'); 
  
?>
  <div class="main">  

    <form action="" method="post" name="forma" id="forma">
        <table width="95%" border="0" align="left" cellpadding="7" cellspacing="0" class="texto">
     
          <tr>
            <td><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar Excepciones" onClick="document.forma.action='abc_excepciones.php'; document.forma.submit();" /></td>
            <td>
              <input name="buttonimporta" type="submit" class="boton_agregar" id="buttonimporta" value="Importar Excepciones" onClick="document.forma.action='importa_excepcioncp.php'; document.forma.submit();" />
              <div align="left" style="display: inline-block;">
                  <input name="exp_xls" type="button" class="boton" onclick="javascript:exportar();" value="Descargar Registros" />
                </div></td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
      
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables 
                     // construir la condición de búsqueda
       
                      $condicion = "  WHERE  1  ";   
                       $resultadotot= mysql_query("SELECT 1 FROM excepciones_cp $condicion",$conexion);
                     $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;

              
           echo 'Total de excepciones en la lista: <b>'.$totres.'</b>';
      
        ?> </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="idexcepcioncp" type="hidden" id="idexcepcioncp" />
                <input name="ord" type="hidden" id="ord" value="<?= $orden; ?>" />
                <input name="imgorden" type="hidden" id="imgorden" value="<?= $imgs; ?>" /> 
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

         <table width="95%" border="0" align="left" cellpadding="2" cellspacing="2" class="texto">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">  
            <td nowrap="nowrap" ><div align="center"> 
             <? if($orden=='DESC'){$ordend='ASC';$imgsw='subir.png';}
                if($orden=='ASC'){$ordend='DESC';$imgsw='orden.png'; } ?>   
                 <a href="javascript:ordena('<?=$ordend?>','<?=$imgsw?>');" class="texto">
                 <strong>CP</strong> &nbsp;<img src="images/<?=$imgs;?>" width="14" height="15" border="0" align="absmiddle" /></a>  </div></td>            
            <td align="center" nowrap="nowrap"><b> Tipo producto </b></td>
             <td align="center" nowrap="nowrap"><b> Tipo servicio </b></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr> 
          <? $i=0;            
             $queryex = "SELECT * FROM excepciones_cp  ORDER BY cp $orden LIMIT $regini,$ver"; 
             $resultadoex= mysql_query($queryex,$conexion);  
              while ($row = mysql_fetch_array($resultadoex)){  
              $idservicio = $row['idservicio'];
              $cpexc = $row['cp'];
              $tipo_producto = $row['tipo_producto'];
              $i++;
            $resultadoserv = mysql_query("SELECT descripcion FROM servicios WHERE idservicio = $idservicio",$conexion);
            $rowserv = mysql_fetch_assoc($resultadoserv);
            $nombreserv = $rowserv['descripcion'];     
          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF" align="center"><div id="showcp<?= $row['idexcepciones_cp']; ?>"> <?= $row['cp']; ?></div>
              <div id="showedtcp<?= $row['idexcepciones_cp']; ?>" style="display: none;">  
              <input name="cp" type="text" class="campo" id="cpnew<?= $row['idexcepciones_cp']; ?>"  value="<?= $cpexc?> " onkeypress="return isNumberKey(event)"  size="5" maxlength="5" />
              </div> 
            </td>  
             
             <td bgcolor="#FFFFFF" align="center">
               <div id="tipo_prod<?= $row['idexcepciones_cp']; ?>"><?= substr($tipo_producto, 1) ; ?></div>
              
               <select name="tipo_producto" class="campo" id="tipo_producto<?= $row['idexcepciones_cp']; ?>"" style="width: 140px; display: none;">
                             <option value="">Seleccione...</option>
                             <? $resultadoEDO = mysql_query("SELECT * FROM tipo_producto ORDER BY nombre",$conexion);
                              while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
                                echo '<option value="'.$rowEDO['nombre'].'"';
                                if ($rowEDO['nombre']==$tipo_producto ) echo 'selected';
                                  echo '>'.$rowEDO['clave'].' </option>';
                                } 
                              ?>
               </select> 
            </td>  
            <td bgcolor="#FFFFFF" align="center"><?=  $nombreserv; ?> </td>           
           
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
              <input type="button" id="edit_button<?= $row['idexcepciones_cp']; ?>" value="Editar" class="edit" onclick="edit_row(<?= $row['idexcepciones_cp'];?>)"> 
            <div id="showedt<?= $row['idexcepciones_cp']; ?>" style="display: none;">  
              <input type="button" id="save_button<?= $row['idexcepciones_cp']; ?>" value="Grabar" class="save" onclick="save_row(<?= $row['idexcepciones_cp']; ?>)">
              <input type="button" id="cancel_button<?= $row['idexcepciones_cp']; ?>" value="Cancelar" class="save" onclick="cancel_row(<?= $row['idexcepciones_cp']; ?>)">
           <? if ($rel<=0) { ?><a onclick="return confirm('\u00bfEst\u00e1s seguro que deseas\nBorrar la excepcion?')" href="javascript:borra('<?= $row['idexcepciones_cp']; ?>');">
<input type="button" value="Borrar" class="save"> </a><? } else  { ?><input type="button" value="Borrar" class="save"><? } ?>
            </div> 
           </td>

      </tr>
          <?
              } // WHILE 
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
