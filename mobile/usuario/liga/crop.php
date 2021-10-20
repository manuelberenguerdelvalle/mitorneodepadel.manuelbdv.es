<?php

/**
 * Jcrop image cropping plugin for jQuery
 * Example cropping script
 * @copyright 2008-2009 Kelly Hallman
 * More info: http://deepliquid.com/content/Jcrop_Implementation_Theory.html
 */
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	if ( $_SESSION['pagina'] == 'gestion_liga'){
		$targ_w = $targ_h = 300;
		$jpeg_quality = 99;
	
		$src = 'temp/temp_'.$_SESSION['bd'].$_SESSION['id_liga'].'.jpg';
		$img_r = imagecreatefromjpeg($src);
		$dst_r = imagecreatetruecolor( $targ_w, $targ_h );
	
		imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
		$targ_w,$targ_h,$_POST['w'],$_POST['h']);
		//se guarda en la carpeta mas superior para compartir con el torneo
		imagejpeg($dst_r,'../../../logos/'.$_SESSION['bd'].$_SESSION['id_liga'].'.jpg',$jpeg_quality);
		unlink($src);//borro
		echo '1';
		//exit;
	}
}
// If not a POST request, display page below:

?>