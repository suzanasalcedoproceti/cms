<?
    if (!include('ctrl_acceso.php')) return;
  include('funciones.php');
  include('../conexion.php');
  $modulo=9;
  if (!op($modulo))  {
    $aviso = 'Usuario sin permiso para acceder a este módulo';
    $aviso_link = 'principal.php';
    include('mensaje_sistema.php');
    return;
  }


$resultadoEdoPlanta = mysql_query("SELECT *
    FROM estados
    ORDER BY cv_estado ",$conexion);
while($rowEdoPlanta = mysql_fetch_assoc($resultadoEdoPlanta)) {
  $edoPlantas[$rowEdoPlanta['estado']][$rowEdoPlanta['cv_estado']][$rowEdoPlanta['cluster']] = $rowEdoPlanta;
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
    form.action='lista_estado.php';
    form.submit();
  }

  function ordena(orden) {
    document.forma.ord.value = orden;
  document.forma.numpag.value = 1;
    document.forma.action='lista_estado.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.estado.value = id;
    document.forma.action='borra_estado.php';
    document.forma.submit();
  }
</script>

<script>

 function edit_row(no)
{
 $("#edit_button"+no).hide();  
 $("#showedt"+no).show(); 
 $("#showedtcl"+no).show(); 
 $("#showedtclh"+no).hide(); 
 var cluster= $("#inputedtcl"+no).val(); 
 var clusterlh= $("#inputedt"+no).val(); 
 }

function save_row(no)
{
 $("#edit_button"+no).show();  
 $("#showedt"+no).hide();  
    var estado = no; 
    var clusterlh= $("#inputedt"+no).val(); 
     var cluster= $("#inputedtcl"+no).val();  
    $.post("graba_estadoajax.php", {estado: no,cluster: cluster}, function(data)
    { location.reload(true); });   
}

function cancel_row(no)
{
  $("#edit_button"+no).show();  
  $("#showedt"+no).hide();  
  $("#showedtclh"+no).show();  
  $("#showedtcl"+no).hide();  
 
}
</script>
</head>
<body>
<div id="container">
  <? $tit='Listado de Estados'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $estado = $_POST['estado'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='estado';
   if ($ord=='estado') $orden='ORDER BY cve_estado';?>
  <div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="90%" border="0" align="left" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td bgcolor="#BBBBBB"><? 

                     // construir la condición de búsqueda
                     $condicion = "WHERE 1 ";


                     // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT 1 FROM estados $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
              
           echo 'Total de estados en la lista: <b>'.$totres.'</b>';
      
        ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="estado" type="hidden" id="estado" />
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
                     echo "P&aacute;gina ";
           echo '<input type="text" name="pagina" value="'.$numpag.'" size="2" onchange="javascript:ir(document.forma,this.value);" style="text-align:center"/>';
           echo " de ".$totpags;
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
      
        <table width="80%" border="0" align="center" cellpadding="2" cellspacing="3">
      <tr class="texto" >
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td align="center" >&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td align="center" >&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
          </tr>
          <tr class="texto" >
            <td nowrap="nowrap" bgcolor="#F4F4F2"><b>Estado</b></td>    
            <td align="center" nowrap="nowrap" bgcolor="#F4F4F2"><b>Cluster</b></td> 
            <td bgcolor="#F4F4F2"><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?  $i=0;
            $query = "SELECT  *   FROM estados
            $condicion $orden LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
             $i++;  ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $row['estado']; ?></td>
     
           <td bgcolor="#FFFFFF" align="center" id="edtcluster<?= $row['idEstado'];?>">
              <div id="showedtclh<?= $row['idEstado']; ?>"> 
                <?= $row['cluster']; ?> 
              </div>
              <div id="showedtcl<?= $row['idEstado']; ?>" style="display: none;"> 
               <select name="cluster" class="camporeq" id="inputedtcl<?= $row['idEstado']; ?>">
                 <option value=""  selected>Selecciona</option> 
                 <?
                 $ser =$row['cluster'];
                 for ($v = 1; $v <= 32; $v++) {
                   if ($row['cluster']==$v) {$sel='selected';} else  {$sel=' ';}
                        echo "(<option value='$v' $sel> $v </option >'.'<br>')"; 
                 } ?>
              
               </select> 
              </div> 
           </td>
            
          
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
              <input type="button" id="edit_button<?= $row['idEstado']; ?>" value="Editar" class="edit" onclick="edit_row(<?= $row['idEstado'];?>)"> 
            <div id="showedt<?= $row['idEstado']; ?>" style="display: none;">   

              <input type="button" id="save_button<?= $row['idEstado']; ?>" value="Grabar" class="save" onclick="save_row(<?= $row['idEstado']; ?>)">
              <input type="button" id="scancel_button<?= $row['idEstado']; ?>" value="Cancelar" class="save" onclick="cancel_row(<?= $row['idEstado']; ?>)">
           
            </div>

              <div id="showedt" style="display: none;">  <input name="cluster" type="text" class="campo" id="inputedt<?=$i;?>" value="<?= $row['cluster']; ?>" size="5" style="display: none;" /> (presione enter para editar)
           id="inputedt<?=$i; ?>"

             <a href="abc_estado.php?estado=<?= $row['idEstado']; ?> "><img src="images/editar.png" alt="Editar estado" width="14" height="16" border="0" align="absmiddle" /></a> </div></td>
      </tr>
          <?
                 } // WHILE
                 mysql_close();
              ?>
        </table><br>
        <table width="90%" border="0" align="left" cellpadding="7" cellspacing="0" class="texto">
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
                     echo "P&aacute;gina ";
           echo '<input type="text" name="pagina2" value="'.$numpag.'" size="2" onchange="javascript:ir(document.forma,this.value);" style="text-align:center"/>';
           echo " de ".$totpags;
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
