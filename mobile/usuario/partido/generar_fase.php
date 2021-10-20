<?php
include_once ("../../../class/mysql.php");
include_once ("../../funciones/f_obten.php");
include_once ("../../funciones/f_partidos.php");
include_once ("../../funciones/f_fechasHoras.php");
include_once ("../../funciones/f_email.php");
include_once ("../../funciones/f_general.php");
include_once ("../../../class/partido.php");
include_once ("../../../class/noticia.php");
include_once ("../../../class/puntos.php");
include_once ("../../../class/puntuacion.php");
include_once ("../../../class/usuario.php");
require_once ("../../../PHPMailer/PHPMailerAutoload.php");
require_once ("../../../PHPMailer/class.smtp.php");
header("Content-Type: text/html;charset=ISO-8859-1");
session_start();
$pagina = $_SESSION['pagina'];
$opcion = $_SESSION['opcion'];
$id_liga = $_SESSION['id_liga'];
$id_division = $_SESSION['id_division'];
$usuario = unserialize($_SESSION['usuario']);
$id_usuario = $usuario->getValor('id_usuario');
$bd_usuario = $_SESSION['bd'];
if ( $pagina != 'gestion_partido' && $opcion != 1 ){
	header ("Location: ../cerrar_sesion.php");
}
else {
	include_once ("../../funciones/f_recoger_post.php");
	if($respuesta == 'S'){
		$id_puntuacion = obten_consultaUnCampo('session','id_puntuacion','puntuacion','usuario',$id_usuario,'liga',$id_liga,'division',$id_division,'aplicacion','T','');
		$min_eliminatoria = obten_consultaUnCampo('session','MIN(eliminatoria)','partido','division',$id_division,'jornada',0,'','','','','');
		if($min_eliminatoria > 1){//ELIMINATORIAS HASTA SEMIS
			if(obten_consultaUnCampo('session','COUNT(id_partido)','partido','division',$id_division,'eliminatoria',$min_eliminatoria,'','','','','') == eliminatoriaPartidosFinalizados($id_division,$min_eliminatoria) ){//num partidos igual partidos finalizados
				$clasificados = array();
				$clasificados = obtenGanadoresEliminatorias($id_division,$min_eliminatoria);
				$num_eliminatoria = $min_eliminatoria / 2;
				$set4_local = obten_consultaUnCampo('session','set4_local','partido','division',$id_division,'eliminatoria',$min_eliminatoria,'','','','','');
				if($set4_local == -1){$sets = 3;}
				else if($set4_local == 0){$sets = 5;}
				else{
					$set4_local = obten_consultaUnCampo('session','set4_local','partido','division',$id_division,'visitante',$clasificados[0],'eliminatoria',$min_eliminatoria,'','','');
					if($set4_local == -1){$sets = 3;}
					else{$sets = 5;}
				}
				$num_equipos = count($clasificados);
				$div_entera = intval($num_equipos / 2);
				$resto = $num_equipos % 2;
				if($resto == 0){$cuadros = $div_entera;}
				else{
					$cuadros = $div_entera + 1;
					//obtener numero de partidos del ultimo equipo que pasa automatico, si ya ha pasado automatico cambiamos por el primero
					$clasif_dir = obten_consultaUnCampo('session','COUNT(id_partido)','partido','division',$id_division,'local',$clasificados[$num_equipos-1],'visitante',0,'ganador',$clasificados[$num_equipos-1],'');
					$clasif_dir += obten_consultaUnCampo('session','COUNT(id_partido)','partido','division',$id_division,'visitante',$clasificados[$num_equipos-1],'local',0,'ganador',$clasificados[$num_equipos-1],'');
					if($clasif_dir > 0){//ya se ha clasificado directamente alguna vez cambiamos por el primero
						$a = $clasificados[0];
						$clasificados[0] = $clasificados[$num_equipos-1];
						$clasificados[$num_equipos-1] = $a;
					}
				}
				for($i=0; $i<$cuadros; $i++){
					if($i == $cuadros-1 && $resto > 0){
						if($num_eliminatoria == 2){$local = $clasificados[1];}//siempre que sea semifinales y sean impares (resto > 0) el que se clasifica automatico es el de la pos [1]
						else{$local = $clasificados[$num_equipos-1];}
						
						$partido = new Partido(NULL,0,NULL,NULL,$local,0,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,$local,'N',1,0,$id_division,0,0,0,0,0,0,$id_liga,0,$num_eliminatoria,'');
					}
					else{
						$local = $clasificados[$i];
						$visitante = $clasificados[$i+$cuadros];
						if($sets == 3){// 3 sets
							$partido = new Partido(NULL,0,NULL,NULL,$local,$visitante,0,0,0,-1,-1,0,0,0,-1,-1,NULL,NULL,0,NULL,$id_division,NULL,NULL,NULL,NULL,NULL,0,$id_liga,0,$num_eliminatoria,'');
						}
						else{// 5 sets
							$partido = new Partido(NULL,0,NULL,NULL,$local,$visitante,0,0,0,0,0,0,0,0,0,0,NULL,NULL,0,NULL,$id_division,NULL,NULL,NULL,NULL,NULL,0,$id_liga,0,$num_eliminatoria,'');
						}
					}
					$partido->insertar();
					if($id_puntuacion > 0){//si hay puntuacion cargamos
						$id_partido = obten_consultaUnCampo('session','id_partido','partido','division',$id_division,'eliminatoria',$num_eliminatoria,'local',$local,'visitante',$visitante,'');
						$puntuacion = new Puntuacion($id_puntuacion,'','','','','','','','','','','','','','','','','');
						$valor_puntuacion = $puntuacion->getValor(obten_nombreEliminatoriaBd($num_eliminatoria));
						if($valor_puntuacion > 0){
							if($num_eliminatoria == 16 ){$tipo = 3;}
							else if($num_eliminatoria == 8){$tipo = 4;}
							else if($num_eliminatoria == 4){$tipo = 5;}
							else if($num_eliminatoria == 2){$tipo = 6;}
							else if($num_eliminatoria == 1){$tipo = 7;}
							else{}//no se hace nada
							$j1_loc = obten_consultaUnCampo('session','jugador1','equipo','id_equipo',$local,'','','','','','','');
							$j2_loc = obten_consultaUnCampo('session','jugador2','equipo','id_equipo',$local,'','','','','','','');
							$j1_vis = obten_consultaUnCampo('session','jugador1','equipo','id_equipo',$visitante,'','','','','','','');
							$j2_vis = obten_consultaUnCampo('session','jugador2','equipo','id_equipo',$visitante,'','','','','','','');
							if($j1_loc > 0){
								$puntos_j1_loc = new Puntos('',$id_usuario,$j1_loc,$bd_usuario,$id_liga,$id_division,$id_partido,obten_fechahora(),$valor_puntuacion,$tipo);
								$puntos_j1_loc->insertar();
							}				
							if($j2_loc > 0){
								$puntos_j2_loc = new Puntos('',$id_usuario,$j2_loc,$bd_usuario,$id_liga,$id_division,$id_partido,obten_fechahora(),$valor_puntuacion,$tipo);
								$puntos_j2_loc->insertar();
							}
							if($j1_vis > 0){
								$puntos_j1_vis = new Puntos('',$id_usuario,$j1_vis,$bd_usuario,$id_liga,$id_division,$id_partido,obten_fechahora(),$valor_puntuacion,$tipo);
								$puntos_j1_vis->insertar();
							}
							if($j2_vis > 0){
								$puntos_j2_vis = new Puntos('',$id_usuario,$j2_vis,$bd_usuario,$id_liga,$id_division,$id_partido,obten_fechahora(),$valor_puntuacion,$tipo);
								$puntos_j2_vis->insertar();
							}
							unset($puntos_j1_loc,$puntos_j1_vis,$puntos_j2_loc,$puntos_j2_vis);
						}//fin valor puntuacion
					}//fin if puntuacion
					unset($partido);
				}//fin for
			}
		}//FIN IF ELIMINATORIAS MAYOR A FINAL
		else if($min_eliminatoria == 1){}//ya se ha generado la final no hago nada
		else{//LIGUILLA
			if( obten_consultaUnCampo('session','COUNT(id_partido)','partido','division',$id_division,'eliminatoria',0,'','','','','') == liguillaPartidosFinalizados($id_division) ){
				$max_grupos = obten_consultaUnCampo('session','MAX(grupo)','partido','division',$id_division,'eliminatoria',0,'','','','','');
				$clasificados = array();
				$cont = 0;
				for($i=1; $i<=$max_grupos; $i++){//bucle grupos
					$equipos = array(0,0,0);//equipos
					$sets_favor = array(0,0,0);//sets favor
					$sets_contra = array(0,0,0);//sets contra
					$dif = array(0,0,0);//diferencia
					$ganados = array(0,0,0);//ganados
					$aux = array(0,0);
					for($j=0; $j<3; $j++){//rellenar datos
						$equipos[$j] = obten_consultaUnCampo('session','local','partido','division',$id_division,'grupo',$i,'jornada',($j+1),'','','');
						$ganados[$j] = obten_consultaUnCampo('session','COUNT(ganador)','partido','division',$id_division,'grupo',$i,'ganador',$equipos[$j],'','','');
						$aux = obten_sumaSets($equipos[$j],'local');
						$sets_favor[$j] = $aux[0];
						$sets_contra[$j] = $aux[1];
						$aux = obten_sumaSets($equipos[$j],'visitante');
						$sets_favor[$j] += $aux[0];
						$sets_contra[$j] += $aux[1];
						$dif[$j] = $sets_favor[$j] - $sets_contra[$j];
					}//fin rellenar datos
					if($ganados[0] == 2){//clasificado
						$clasificados[$cont] = $equipos[0];$cont++;
						if($ganados[1] == 1){//equipo 1
							$clasificados[$cont] = $equipos[1];$cont++;
						}
						else{//equipo 2
							$clasificados[$cont] = $equipos[2];$cont++;
						}
					}
					else if($ganados[0] == 0){//eliminado
						$clasificados[$cont] = $equipos[1];$cont++;
						$clasificados[$cont] = $equipos[2];$cont++;
					}
					else{//empatados 1 partido ganado
							for($j=0; $j<3 ; $j++){
								if($j == 0){$sig = 1;$ant = 2;}//0
								else if($j == 1){$sig = 2;$ant = 0;}//1
								else{$sig = 0;$ant = 1;}//2					
								if($dif[$j] > $dif[$sig] && $dif[$j] > $dif[$ant]){//IF EQUIPO 0
									$clasificados[$cont] = $equipos[$j];$cont++;
								}
								else if($dif[$j] > $dif[$sig] && $dif[$j] < $dif[$ant]){
									$clasificados[$cont] = $equipos[$j];$cont++;
								}//fin else if
								else if($dif[$j] < $dif[$sig] && $dif[$j] > $dif[$ant]){
									$clasificados[$cont] = $equipos[$j];$cont++;
								}//fin else if
								else if($dif[$j] > $dif[$sig] && $dif[$j] == $dif[$ant]){//dos mayores iguales
									$clasificados[$cont] = $equipos[$j];$cont++;
								}//fin else if
								else if($dif[$j] == $dif[$sig] && $dif[$j] > $dif[$ant]){//dos menores iguales
									if($sets_favor[$j] > $sets_favor[$sig]){//es mayor
										$clasificados[$cont] = $equipos[$j];$cont++;
									}
									else if($sets_favor[$j] == $sets_favor[$sig]){//es igual por id equipo
										if($equipos[$j] < $equipos[$sig]){
											$clasificados[$cont] = $equipos[$j];$cont++;
										}
									}
									else{}//es menor no hago nada
								}//fin else if
								else if($dif[$j] == $dif[$sig] && $dif[$j] == $dif[$ant]){
									if($sets_favor[$j] > $sets_favor[$ant] && $sets_favor[$j] > $sets_favor[$sig]){//es mayor
										$clasificados[$cont] = $equipos[$j];$cont++;
									}
									else if($sets_favor[$j] == $sets_favor[$ant] && $sets_favor[$j] > $sets_favor[$sig]){//es mayor
										$clasificados[$cont] = $equipos[$j];$cont++;
									}
									else if($sets_favor[$j] > $sets_favor[$ant] && $sets_favor[$j] == $sets_favor[$sig]){//es mayor
										$clasificados[$cont] = $equipos[$j];$cont++;
									}
									else if($sets_favor[$j] == $sets_favor[$ant] && $sets_favor[$j] == $sets_favor[$sig]){//es igual por id equipo
										if($equipos[$j] > $equipos[$ant] && $equipos[$j] > $equipos[$sig]){//si es id mayor no hago nada no se clasifica
										}
										else{
											$clasificados[$cont] = $equipos[$j];$cont++;
										}
									}
									else{}//es menor no hago nada}
								}//fin else if
								else{}//FIN IF EQUIPO 0
							}//fin de for
					}//fin else empatados
				}//fin for grupos
				$num_eliminatoria = count($clasificados) / 2;
				$set4_local = obten_consultaUnCampo('session','set4_local','partido','division',$id_division,'local',$clasificados[0],'eliminatoria',0,'','','');
				if($set4_local == -1){$sets = 3;}
				else if($set4_local == 0){$sets = 5;}
				else{
					$set4_local = obten_consultaUnCampo('session','set4_local','partido','division',$id_division,'visitante',$clasificados[0],'eliminatoria',0,'','','');
					if($set4_local == -1){$sets = 3;}
					else{$sets = 5;}
				}
				for($i=0; $i<$num_eliminatoria; $i++){
					$local = $clasificados[$i];
					$visitante = $clasificados[$i+$num_eliminatoria];
					if($sets == 3){// 3 sets
						$partido = new Partido(NULL,0,NULL,NULL,$local,$visitante,0,0,0,-1,-1,0,0,0,-1,-1,NULL,NULL,0,NULL,$id_division,NULL,NULL,NULL,NULL,NULL,0,$id_liga,0,$num_eliminatoria,'');
					}
					else{// 5 sets
						$partido = new Partido(NULL,0,NULL,NULL,$local,$visitante,0,0,0,0,0,0,0,0,0,0,NULL,NULL,0,NULL,$id_division,NULL,NULL,NULL,NULL,NULL,0,$id_liga,0,$num_eliminatoria,'');
					}
					$partido->insertar();
					if($id_puntuacion > 0){//si hay puntuacion cargamos
						$id_partido = obten_consultaUnCampo('session','id_partido','partido','division',$id_division,'eliminatoria',$num_eliminatoria,'local',$local,'visitante',$visitante,'');
						$puntuacion = new Puntuacion($id_puntuacion,'','','','','','','','','','','','','','','','','');
						$valor_puntuacion = $puntuacion->getValor(obten_nombreEliminatoria($num_eliminatoria));
						if($valor_puntuacion > 0){
							if($num_eliminatoria == 16 ){$tipo = 3;}
							else if($num_eliminatoria == 8){$tipo = 4;}
							else if($num_eliminatoria == 4){$tipo = 5;}
							else if($num_eliminatoria == 2){$tipo = 6;}
							else if($num_eliminatoria == 1){$tipo = 7;}
							else{}//no se hace nada
							$j1_loc = obten_consultaUnCampo('session','jugador1','equipo','id_equipo',$local,'','','','','','','');
							$j2_loc = obten_consultaUnCampo('session','jugador2','equipo','id_equipo',$local,'','','','','','','');
							$j1_vis = obten_consultaUnCampo('session','jugador1','equipo','id_equipo',$visitante,'','','','','','','');
							$j2_vis = obten_consultaUnCampo('session','jugador2','equipo','id_equipo',$visitante,'','','','','','','');
							if($j1_loc > 0){
								$puntos_j1_loc = new Puntos('',$id_usuario,$j1_loc,$bd_usuario,$id_liga,$id_division,$id_partido,obten_fechahora(),$valor_puntuacion,$tipo);
								$puntos_j1_loc->insertar();
							}				
							if($j2_loc > 0){
								$puntos_j2_loc = new Puntos('',$id_usuario,$j2_loc,$bd_usuario,$id_liga,$id_division,$id_partido,obten_fechahora(),$valor_puntuacion,$tipo);
								$puntos_j2_loc->insertar();
							}
							if($j1_vis > 0){
								$puntos_j1_vis = new Puntos('',$id_usuario,$j1_vis,$bd_usuario,$id_liga,$id_division,$id_partido,obten_fechahora(),$valor_puntuacion,$tipo);
								$puntos_j1_vis->insertar();
							}
							if($j2_vis > 0){
								$puntos_j2_vis = new Puntos('',$id_usuario,$j2_vis,$bd_usuario,$id_liga,$id_division,$id_partido,obten_fechahora(),$valor_puntuacion,$tipo);
								$puntos_j2_vis->insertar();
							}
							unset($puntos_j1_loc,$puntos_j1_vis,$puntos_j2_loc,$puntos_j2_vis);
						}//fin valor puntuacion
					}//fin if puntuacion
					unset($partido);
				}//fin for
			}//fin if liguilla
		}//fin else
		$email_admin = $_SESSION['email'];
		 include_once ("../../funciones/f_conexion_email.php");//Set who the message is to be sent from
		$mail->setFrom('info@mitorneodepadel.es', 'mitorneodepadel.es');//Set an alternative reply-to address
		$mail->addReplyTo('info@mitorneodepadel.es', 'mitorneodepadel.es');//Set who the message is to be sent to
		for($i=0; $i<count($clasificados); $i++){
			$id_jugador1 = obten_consultaUnCampo('session','jugador1','equipo','id_equipo',$clasificados[$i],'','','','','','','');
			$id_jugador2 = obten_consultaUnCampo('session','jugador2','equipo','id_equipo',$clasificados[$i],'','','','','','','');
			$mail->AddBCC(obten_consultaUnCampo('session','email1','inscripcion','id_jugador1',$id_jugador1,'','','','','','',''));
			$mail->AddBCC(obten_consultaUnCampo('session','email2','inscripcion','id_jugador2',$id_jugador2,'','','','','','',''));
		}
		$asunto = utf8_decode('Nueva fase eliminatoria');
		$mail->Subject = $asunto;
		$cuerpo = '<br><br>Se ha generado la nueva fase eliminatoria '.obten_nombreEliminatoria($min_eliminatoria/2).'<br><br>';
		$cuerpo .= 'Ahora a Ganar!!<br><br>';
		$body = email_jugadorAdmin("<br>¡Gracias por utilizar nuestros servicios en www.mitorneodepadel.es!<br>",$cuerpo);
		$mail->msgHTML($body);
		$mail->AltBody = 'This is a plain-text message body';//Replace the plain text body with one created manually
		$mail->send();
		unset($mail);
		$descripcion_noticia .= 'El Administrador ha generado la pr&oacute;xima eliminatoria '.obten_nombreEliminatoria($min_eliminatoria/2).', suerte!';
		$resumen_noticia = utf8_decode('Sección: Partido -> Siguiente Fase.');
		$fecha_noticia = obten_fechahora();
		$noticia = new Noticia(NULL,$id_liga,$id_division,$resumen_noticia,utf8_decode($descripcion_noticia),$fecha_noticia,'');
		$noticia->insertar();
		unset($noticia);
		unset($liga,$division,$clasificados);
		echo '0';
	}//fin de if respuesta
}//fin else
?>