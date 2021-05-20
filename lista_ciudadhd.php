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


$resultadoMpo = mysql_query("SELECT *
    FROM municipios
    ORDER BY cv_estado ",$conexion);
while($rowMpo = mysql_fetch_assoc($resultadoMpo)) {
  $municipios[$rowMpo['mnpio']][$rowMpo['cv_estado']][$rowMpo['cv_mnpio']] = $rowEdoPlanta;
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
  function ir(form, Pag,estad) {
    form.numpag.value = Pag;
    form.idestadox.value = estad;
    form.action='lista_ciudadhd.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_ciudadhd.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.ciudad.value = id;
    document.forma.action='borra_ciudadhd.php';
    document.forma.submit();
  }
</script>
<script>

 function edit_row(no,idedo,idmpo)
{
 $("#edit_button"+no).hide();  
 $("#showedt"+no).show(); 
 $("#showedttr"+no).show();
 $("#showedttranszone"+no).hide(); 
 
 }

function save_row(no,idedo,idmpo)
{ 
 $("#edit_button"+no).show();  
 $("#showedt"+no).hide();  
    var estado = idedo;
    var mpo=idmpo;
    var trans_zone= $("#inputedttrans"+no).val(); 
    $.post("graba_ciudadajax.php", { estado: idedo,mpo:idmpo,trans_zone:trans_zone}, function(data)
    {  location.reload(true); });   
}

function cancel_row(no,idedo,idmpo)
{
  $("#edit_button"+no).show();  
  $("#showedt"+no).hide();  
  $("#showedttr"+no).hide();
 $("#showedttranszone"+no).show(); 
}
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Municipios'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $estado = $_POST['estado'];
   $idestado = $_POST['idestado'];
   if (empty($ver)) $ver='30';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='ciudad';

   if ($ord=='ciudad') $orden='ORDER BY cve_estado, cve_mnpio';
  
   
?>
  <div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
        
         <tr>
            <td>Estado:
              <select name="idestado" class="campo" id="idestado" onchange="document.forma.submit();">
                  <option value="">Seleccione</option>
                    <option value="0">Todos</option>
                  <? $resCAT= mysql_query("SELECT * FROM estados ",$conexion);
                    while ($rowCAT = mysql_fetch_array($resCAT)) { 
                     echo '<option value="'.$rowCAT['idEstado'].'"';
                       if ($rowCAT['idEstado']==$idestado) echo ' selected';
                       echo '>'.$rowCAT['estado'].'</option>';
                     }
              ?>
              </select></td>
            <td align="right">&nbsp;</td>
          </tr>

          <tr>
            <td bgcolor="#BBBBBB"><?               
                     $condicion = "WHERE 1 ";
                     if (!empty($idestado)) {$condicion.= " AND cve_estado='$idestado'"; 
                                         $condicionbsq.= " AND estados.cve_estado='$idestado'";};
                  
                      if ($idestado==0) {$condicion = "WHERE 1 "; $condicionbsq = "WHERE 1 ";};
 
                       $resultadotot= mysql_query("SELECT 1 FROM municipios $condicion",$conexion);
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
                 <input name="idestadox" type="hidden" id="idestadox" value="<?= $idestado; ?>  " />
                <?


          $regini = ($numpag * $ver) - $ver; 
          if ($regini<0) $regini=0;

                     // poner flechitas anterior, primero, último, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,1,'.$idestado.');"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera página"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).','.$idestado.');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Página anterior"></a>';
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
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).','.$idestado.');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente página"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.','.$idestado.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Última página"></a>';
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
    
            <td align="center" nowrap="nowrap" bgcolor="#F4F4F2"><b>Municipio</b></td>
                
           <td align="center" nowrap="nowrap" bgcolor="#F4F4F2"><b>Trans-Zone</b></td>
            <td bgcolor="#F4F4F2"><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <? $i=0;
       $query = "SELECT municipios.*,  estados.estado as estado,estados.`idEstado`
            FROM municipios 
            INNER JOIN estados ON municipios.cve_estado = estados.cve_estado
            $condicionbsq $orden LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
              $cve_mnpio =$row['cve_mnpio'];
              $id_mnpio =$row['idMunicipio']; 
              $i++;             
          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $row['estado']; ?></td>
     
            <td bgcolor="#FFFFFF" align="left"><?= $row['mnpio']; ?></td>
            <td bgcolor="#FFFFFF" align="center" id="edttrans<?= $row['idEstado'];?>">
                <div id="showedttranszone<?= $i; ?>"> 
                 <?= $row['trans_zone']; ?>
               </div>
              
               <div id="showedttr<?= $i; ?>" style="display: none;">  
                 <input name="trans_zone" type="text" class="campo" id="inputedttrans<?=$i;?>" value="<?= $row['trans_zone'];?>" size="10" />
              </div>
            </td>
            
          
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
              <input type="button" id="edit_button<?= $i; ?>" value="Editar" class="edit" onclick="edit_row(<?=$i .",". $row['idEstado'],",".$cve_mnpio;?>)"> 
                <div id="showedt<?= $i; ?>" style="display: none;">   
              <input type="button" id="save_button<?= $i;?>" value="Grabar" class="save" onclick="save_row(<?=$i .",". $row['idEstado'],",".$cve_mnpio;?>)">
              <input type="button" id="cancel_button<?= $i;?>" value="Cancelar" class="save" onclick="cancel_row(<?=$i .",". $row['idEstado'],",".$cve_mnpio;?>)">
           
            </div>  </td>
      </tr>
          <?
                 } // WHILE 
                 mysql_close();
              ?>
        </table><br>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td bgcolor="#BBBBBB" align="right"><?

                     // poner flechitas anterior, primero, &uacute;ltimo, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,1,'.$idestado.');"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).','.$idestado.');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="P&aacute;gina anterior"></a>';
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
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).','.$idestado.');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.','.$idestado.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="&Uacute;ltima p&aacute;gina"></a>';
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
