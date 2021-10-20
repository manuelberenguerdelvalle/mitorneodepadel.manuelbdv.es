<?php
//CONTACTO DESDE FUERA DEL TODO
//para los que estan a dos subniveles
include_once ("../../funciones/f_general.php");
include_once ("../../funciones/f_obten.php");
include_once ("../../funciones/f_email.php");
include_once ("../../../class/mysql.php");
require_once ("../../../PHPMailer/PHPMailerAutoload.php");
require_once ("../../../PHPMailer/class.smtp.php");
session_start();
$modo =  limpiaTexto($_POST['modo']);
$enviar_email = false;
if($modo == 0){//correo enviado desde usuario/registro/elegir_plan.php, cuando desean contactar por el ClonPadel
	$contacto =  strtolower(limpiaTexto3($_POST['contacto']));
	$telefono =  limpiaTexto3($_POST['telefono']);
	$mensaje =  limpiaTexto3($_POST['mensaje']);
	if($contacto != '' || $telefono != '' || $mensaje != ''){//valido que haya algún campo con datos
		$titulo = '<br>Contacto ClonTorneoPadel<br>';
		$subject = utf8_decode('mitorneodepadel.es - Consulta sobre ClonTorneoPadel.');
		$cuerpo = '<br><br>Has recibido una consulta sobre ClonTorneoPadel del contacto: '.$contacto.', con teléfono: '.$telefono.'.<br><br>';
		$destinatario = obten_consultaUnCampo('unicas','c1','datos','id_datos','19','','','','','','','');
		$enviar_email = true;
	}//fin validar correo
}//fin modo 0
else if($modo == 1){//correo enviado de interesado en patrocinar liga de pago
	$contacto =  strtolower(limpiaTexto3($_POST['contacto']));
	if($contacto != '' && valida_correo($contacto)){//valido que sea diferente de vacio y tenga
		$asunto =  limpiaTexto3($_POST['asunto']);
		$mensaje =  limpiaTexto3($_POST['mensaje']);
		$id_liga =  limpiaTexto($_POST['id_liga']);
		$id_division =  limpiaTexto($_POST['id_division']);
		$bd =  limpiaTexto($_POST['bd']);
		$posicion =  limpiaTexto3($_POST['posicion']);
		if($id_liga != '' && $id_division != '' && $bd != ''){
			if($_SESSION['bd'] != $bd){
				$_SESSION['bd'] = $bd;
			}
			$titulo = '<br>Patrocinador de Torneo<br>';
			$nom_liga = obten_consultaUnCampo('session','nombre','liga','id_liga',$id_liga,'','','','','','','');
			$num_division = obten_consultaUnCampo('session','num_division','division','id_division',$id_division,'','','','','','','');
			$id_usuario =  obten_consultaUnCampo('session','usuario','liga','id_liga',$id_liga,'','','','','','','');
			$destinatario = obten_consultaUnCampo('unicas_torneo','email','usuario','id_usuario',$id_usuario,'','','','','','','');
			$subject = utf8_decode('mitorneodepadel.es - Patrocinador interesado en tu Torneo '.$nom_liga.' división '.$num_division);
			$cuerpo = '<br>Torneo: '.$nom_liga.' división '.$num_division.'<br><br>Anuncio seleccionado: '.obten_texto_posPubli($posicion).'.<br><br>Asunto: '.$asunto.'<br><br>Contacto:  '.$contacto.'<br><br>';
			//echo '--'.$asunto.'--'.$mensaje.'--'.$id_liga.'--'.$id_division.'--'.$bd.'--'.$posicion.'--'.$nom_liga.'--'.$num_division.'--'.$id_usuario.'--'.$destinatario;
			$enviar_email = true;
		}//fin validar liga,division,bd
	}//fin validar correo
}//fin modo 1
else if($modo == 2){//email enviado desde el contacto general de la web
	$contacto =  strtolower(limpiaTexto3($_POST['contacto']));
	if($contacto != '' && valida_correo($contacto)){//valido que sea diferente de vacio y tenga
		$asunto =  limpiaTexto3($_POST['asunto']);
		$mensaje =  limpiaTexto3($_POST['mensaje']);
		$titulo = '<br>Contacto Web<br>';
		//REVISAR id_usuario
		$destinatario = obten_consultaUnCampo('unicas_torneo','email','usuario','id_usuario',$id_usuario,'','','','','','','');
		$subject = utf8_decode('mitorneodepadel.es - Contacto general desde la web');
		$cuerpo = '<br>Contacto: '.$contacto.'<br><br>Asunto: '.$asunto.'<br><br>';
		$destinatario = obten_consultaUnCampo('unicas','c1','datos','id_datos','19','','','','','','','');
		$enviar_email = true;
	}//fin validar correo
}//fin modo 2
else if($modo == 3){//email enviado desde el contacto de liga al administrador
	$contacto =  strtolower(limpiaTexto3($_POST['contacto']));
	if($contacto != '' && valida_correo($contacto)){//valido que sea diferente de vacio y tenga
		$asunto =  limpiaTexto3($_POST['asunto']);
		$mensaje =  limpiaTexto3($_POST['mensaje']);
		$id_liga =  limpiaTexto($_POST['id_liga']);
		$id_division =  limpiaTexto($_POST['id_division']);
		$nom_liga = obten_consultaUnCampo('session','nombre','liga','id_liga',$id_liga,'','','','','','','');
		$num_division = obten_consultaUnCampo('session','num_division','division','id_division',$id_division,'','','','','','','');
		$id_usuario =  obten_consultaUnCampo('session','usuario','liga','id_liga',$id_liga,'','','','','','','');
		$destinatario = obten_consultaUnCampo('unicas_torneo','email','usuario','id_usuario',$id_usuario,'','','','','','','');
		$subject = utf8_decode('mitorneodepadel.es - Contacto desde tu Torneo '.$nom_liga.' división '.$num_division);
		$titulo = '<br>Contacto Torneo '.$nom_liga.' división '.$num_division.'<br>';
		$cuerpo = '<br>Contacto: '.$contacto.'<br><br>Asunto: '.$asunto.'<br><br>';
		$enviar_email = true;
	}//fin validar correo
}//fin modo 3
else if($modo == 4){//correo enviado desde desplegables/mostrar_resultados.php, cuando el usuario no tiene cuenta paypal y el registro se supone que es manual
	$contacto =  strtolower(limpiaTexto3($_POST['contacto']));
	if($contacto != '' && valida_correo($contacto)){//valido que sea diferente de vacio y tenga
		$asunto =  limpiaTexto3($_POST['asunto']);
		$mensaje =  limpiaTexto3($_POST['mensaje']);
		$id_liga =  limpiaTexto($_POST['id_liga']);
		$id_division =  limpiaTexto($_POST['id_division']);
		$bd =  limpiaTexto($_POST['bd']);
		if($id_liga != '' && $id_division != '' && $bd != ''){
			if($_SESSION['bd'] != $bd){
				$_SESSION['bd'] = $bd;
			}
			$nom_liga = obten_consultaUnCampo('session','nombre','liga','id_liga',$id_liga,'','','','','','','');
			$num_division = obten_consultaUnCampo('session','num_division','division','id_division',$id_division,'','','','','','','');
			$id_usuario =  obten_consultaUnCampo('session','usuario','liga','id_liga',$id_liga,'','','','','','','');
			$destinatario = obten_consultaUnCampo('unicas_torneo','email','usuario','id_usuario',$id_usuario,'','','','','','','');
			$titulo = '<br>Contacto Torneo '.$nom_liga.' división '.$num_division.'<br>';
			$subject = utf8_decode('mitorneodepadel.es - Inscripción manual en tu Torneo '.$nom_liga.' división '.$num_division);
			$cuerpo = 'No es posible incribirse en tu Torneo de manera automática online ya que no tienes el dni o una cuenta de PayPal asociada.<br>Por lo tanto puedes rellenar los datos o cambiar a inscripci&oacute;n con pago manual.<br><br>Asunto: '.$asunto.'<br><br>Contacto: '.$contacto.'.<br><br>';
			$enviar_email = true;
		}//fin validar liga,division,bd
	}//fin validar correo
}//fin modo 4
else{
}


if($enviar_email){//ENVIAR EMAIL
	include_once ("../../funciones/f_conexion_email.php");//Set who the message is to be sent from
	$mail->setFrom('info@mitorneodepadel.es', 'mitorneodepadel.es');//Set an alternative reply-to address
	if($modo == 0){
		$mail->addReplyTo('info@mitorneodepadel.es', 'mitorneodepadel.es');//Set who the message is to be sent to
		$mail->addAddress($destinatario);//enviarmos al emisor
	}
	else if($modo == 1){
		$mail->addReplyTo($destinatario, 'Administrador de Torneo '.$nom_liga.' division '.$num_division);//Set who the message is to be sent to
		$mail->addAddress($destinatario);//enviarmos al emisor
	}
	else if($modo == 2){
		$mail->addReplyTo($contacto);//Set who the message is to be sent to
		$mail->addAddress($destinatario);//enviarmos al emisor
	}
	else if($modo == 3){
		$mail->addReplyTo($contacto);//Set who the message is to be sent to
		$mail->addAddress($destinatario);//enviarmos al emisor
	}
	else if($modo == 4){
		$mail->addReplyTo($contacto);//Set who the message is to be sent to
		$mail->addAddress($destinatario);//enviarmos al emisor
	}
	else{
	}
	//$mail->addAddress('manuel.berdelva@gmail.com');//enviarmos al emisor
	//$mail->addCC($contacto);//en copia al receptor
	//$mail->AddBCC($contacto);//en copia oculta
	$mail->Subject = $subject ;
	$cuerpo .= 'Mensaje: '.$mensaje;
	$body = email_jugadorAdmin($titulo,$cuerpo);
	$mail->msgHTML($body);
	$mail->AltBody = 'This is a plain-text message body';//Replace the plain text body with one created manually
	$mail->send();
}
?>
