	<div class="top">
  	<? if (!empty($_SESSION['usr_valido'])) { ?>
		<div class="nombre">Usuario: <strong><?= $_SESSION['ss_nombre']; ?></strong></div>	  
        <div class="cerrar"><a href="logout.php" onClick="return confirm('&iquest;Est&aacute;s seguro que deseas\ncerrar la sesi&oacute;n?')">Cerrar sesi&oacute;n</a></div>
		<? } ?>
    </div>
    <? if (!empty($_SESSION['usr_valido'])) include('menu.php'); ?>
    <div class="<? if ($home) echo 'titulo_off'; else echo 'titulo_on'; ?>"><?= $tit; ?></div>