<?php
include_once ("../../../class/mysql.php");
include_once ("../../funciones/f_obten.php");
include_once ("../../funciones/f_partidos.php");
include_once ("../../funciones/f_general.php");
include_once ("../../../class/partido.php");
include_once ("../../../class/puntos.php");
include_once ("../../../class/puntuacion.php");
header("Content-Type: text/html;charset=ISO-8859-1");
session_start();
$pagina = $_SESSION['pagina'];
$id_liga = $_SESSION['id_liga'];
$id_division = $_SESSION['id_division'];
$bd_usuario = $_SESSION['bd'];
$id_usuario = obten_consultaUnCampo('session','usuario','liga','id_liga',$id_liga,'','','','','','','');
if ( $pagina != 'ver_liga' ){
	header ("Location: ../cerrar_sesion.php");
}
else {
	include_once ("../../funciones/f_recoger_post.php");
	$email = limpiaTexto3($_POST['email']);//el email lo vuelvo a recoger para que no elimine caracteres
	//el password si me vale con el limpiaTexto
	if(!empty($id_partido)){
		$id_puntuacion = obten_consultaUnCampo('session','id_puntuacion','puntuacion','usuario',$id_usuario,'liga',$id_liga,'division',$id_division,'aplicacion','T','');
		if($id_puntuacion > 0){//si hay puntuacion cargamos
			$puntuacion = new Puntuacion($id_puntuacion,'','','','','','','','','','','','','','','','','');
		}
		$operacion = '';//esta variable determina si hay que insertar 'I' o eliminar 'D' puntos
		$partido = new Partido($id_partido,'','','','','','','','','','','','','','','','','','','','','','','','','');
		$local = $partido->getValor('local');
		$visitante = $partido->getValor('visitante');
		$id_jugador = obten_consultaUnCampo('unicas','id_jugador','jugador','email',$email,'password',$password,'','','','','');
		//comprobar jugador en equipo local
		//comprobar jugador en equipo visitante
		$encontrado = obten_consultaUnCampo('session','id_equipo','equipo','id_equipo',$local,'jugador1',$id_jugador,'','','','','');
		if($encontrado == ''){$encontrado = obten_consultaUnCampo('session','id_equipo','equipo','id_equipo',$local,'jugador2',$id_jugador,'','','','','');}
		if($encontrado == ''){$encontrado = obten_consultaUnCampo('session','id_equipo','equipo','id_equipo',$visitante,'jugador1',$id_jugador,'','','','','');}
		if($encontrado == ''){$encontrado = obten_consultaUnCampo('session','id_equipo','equipo','id_equipo',$visitante,'jugador2',$id_jugador,'','','','','');}
	}
	if($encontrado != ''){//encontrado
		//$descripcion_noticia = '';
		$partido->setValor('modificado',$id_jugador);
		//$descripcion_noticia .= 'El Administrador ha modificado el partido de la jornada '.$partido->getValor("jornada").':<br>'.obtenNombreJugadorMostrar($local,'jugador1').' - '.obtenNombreJugadorMostrar($local,'jugador2');
		$partido->setValor('set1_local',$set1_local);
		$partido->setValor('set2_local',$set2_local);
		$partido->setValor('set3_local',$set3_local);
		//$descripcion_noticia .= ' - '.$partido->getValor("set1_local").' - '.$partido->getValor("set2_local").' - '.$partido->getValor("set3_local");
		if(isset($set4_local)){
			$partido->setValor('set4_local',$set4_local);
			//$descripcion_noticia .= ' - '.$partido->getValor("set4_local");
		}
		if(isset($set5_local)){
			$partido->setValor('set5_local',$set5_local);
			//$descripcion_noticia .= ' - '.$partido->getValor("set5_local");
		}
		//$descripcion_noticia .= '<br>'.obtenNombreJugadorMostrar($visitante,'jugador1').' - '.obtenNombreJugadorMostrar($visitante,'jugador2');
		$partido->setValor('set1_visitante',$set1_visitante);
		$partido->setValor('set2_visitante',$set2_visitante);
		$partido->setValor('set3_visitante',$set3_visitante);
		//$descripcion_noticia .= ' - '.$partido->getValor("set1_visitante").' - '.$partido->getValor("set2_visitante").' - '.$partido->getValor("set3_visitante");
		if(isset($set4_visitante)){
			$partido->setValor('set4_visitante',$set4_visitante);
			//$descripcion_noticia .= ' - '.$partido->getValor("set4_visitante");
		}
		if(isset($set5_visitante)){
			$partido->setValor('set5_visitante',$set5_visitante);
			//$descripcion_noticia .= ' - '.$partido->getValor("set5_visitante");
		}
		//$descripcion_noticia .= '<br>';
		if($partido->getValor('fecha') == '0000-00-00'){//si esta vacia
			$partido->setValor('fecha',date('Y-m-d'));
		}
		if($partido->getValor('hora') == '00:00:00'){//si esta vacia
			$partido->setValor('hora',date('H:i:s'));
		}
		//$descripcion_noticia .= ' Fecha='.$partido->getValor("fecha").' Hora='.$partido->getValor("hora");
		if(isset($pista) && $pista != ''){$partido->setValor('pista',$pista);}
		//$descripcion_noticia .= ' - Pista='.obten_consultaUnCampo('session','nombre','pista','id_pista',$partido->getValor("pista"),'','','','','','','');//FUNCION OBTEN NOMBRE PISTA
		////$descripcion_noticia .= ' Pista='.obten_nombrePista($partido->getValor("pista"));//FUNCION OBTEN NOMBRE PISTA
		if(isset($arbitro_principal)){$partido->setValor('arbitro_principal',$arbitro_principal);}
		if(isset($arbitro_auxiliar)){$partido->setValor('arbitro_auxiliar',$arbitro_auxiliar);}
		if(isset($arbitro_adjunto)){$partido->setValor('arbitro_adjunto',$arbitro_adjunto);}
		if(isset($arbitro_silla)){$partido->setValor('arbitro_silla',$arbitro_silla);}
		if(isset($arbitro_ayudante)){$partido->setValor('arbitro_ayudante',$arbitro_ayudante);}
		if( $partido->getValor('set1_local') == 0 &&  $partido->getValor('set2_local') == 0 && $partido->getValor('set3_local') == 0 && $partido->getValor('set1_visitante') == 0 && $partido->getValor('set2_visitante') == 0 && $partido->getValor('set3_visitante') == 0 ){// si los 3 sets local y visitante = 0
			if( $partido->getValor('set4_local') <= 0 && $partido->getValor('set5_local') <= 0 && $partido->getValor('set4_visitante') <= 0 && $partido->getValor('set5_visitante') <= 0 ){//si los sets 4,5 local y visitante estan a 0 o -1 es porque el partido está ACTIVO = 0
				$partido->setValor('estado',0);
				$partido->setValor('ganador',0);
				$operacion = 'D';
			}
			//SI POR CASUALIDAD HUBIERA ALGUN PARTIDO CON LOS 3 PRIMEROS SETS LOCAL Y VISITANTE A 0, Y ALGUNO DE LOS 4,5 SETS CON DATOS REALES EL PROGRAMA DE MANTENIMIENTO LO REALIZARIA
		}
		else{//si hay datos en los 3 primeros sets calcular el ganador
			$ganador = obtenGanador($partido->getValor('local'),$partido->getValor('visitante'),$partido->getValor('set1_local'),$partido->getValor('set2_local'),$partido->getValor('set3_local'),$partido->getValor('set4_local'),$partido->getValor('set5_local'),$partido->getValor('set1_visitante'),$partido->getValor('set2_visitante'),$partido->getValor('set3_visitante'),$partido->getValor('set4_visitante'),$partido->getValor('set5_visitante'));
			$tiebreak = hayTiebreak($partido->getValor('set1_local'),$partido->getValor('set2_local'),$partido->getValor('set3_local'),$partido->getValor('set4_local'),$partido->getValor('set5_local'),$partido->getValor('set1_visitante'),$partido->getValor('set2_visitante'),$partido->getValor('set3_visitante'),$partido->getValor('set4_visitante'),$partido->getValor('set5_visitante'));
			if($ganador != -1){//ENTRO SI NO HAY ERROR, SI ES -1 ES PORQUE HA HABIDO ALGUN ERROR DE INSERCION EN LOS SETS
				$partido->setValor('ganador',$ganador);
				$partido->setValor('tiebreak',$tiebreak);
				$partido->setValor('estado',1);
				$operacion = 'I';
				$partido->modificar();
				echo '0';//ok
			}
			else{
				echo '1';//error	
			}//fin else datos en sets incorrectos
		}//fin else de sets insertados
		if($id_puntuacion > 0 && ($partido->getValor('grupo') > 0 || $partido->getValor('eliminatoria') == 1)){//si a insertado en algun momento en puntuaciones
			//solo es posible insertar/eliminar puntuaciones al actualizar partido si es partido de grupo, o si es la final
			$inicio = 0;
			$fin = 1;
			if($ganador == $local){//si el equipo ganador es local
				$j1 = $jugador1_loc;
				$j2 = $jugador2_loc;
			}
			else{//si el equipo ganador es el visitante
				$j1 = $jugador1_vis;
				$j2 = $jugador2_vis;
			}
			if($partido->getValor('grupo') > 0 && $partido->getValor('eliminatoria') == 0){//es partido de GRUPO en torneo
				$tipo = 2;
				$tipo_puntuacion = 'victoria';
			}//fin partido grupo
			else if($partido->getValor('grupo') == 0 && $partido->getValor('eliminatoria') ==1){//es partido de ELIIMINATORIA en torneo
				//modificamos el bucle para que haga 2 actuaciones
				$inicio = 8;
				$fin = 10;
			}//fin partido eliminatoria
			else{}//no hace nada
			
			for(;$inicio < $fin; $inicio++){//inicio for 
				if($inicio == 8){
					$tipo = 8;
					$tipo_puntuacion = 'primero';
				}
				if($inicio == 9){
					$tipo = 9;
					$tipo_puntuacion = 'segundo';
					if($ganador != $local){//asignamos el equipo perdedor el segundo
						$j1 = $jugador1_loc;
						$j2 = $jugador2_loc;
					}
					else{//si el equipo ganador es el visitante
						$j1 = $jugador1_vis;
						$j2 = $jugador2_vis;
					}
				}
				$hay_puntos = obten_consultaUnCampo('unicas','COUNT(id_puntos)','puntos','liga',$id_liga,'division',$id_division,'partido',$id_partido,'tipo',$tipo,'');
				if($j1 > 0){//si no es temporal
					if($operacion == 'I' && $hay_puntos == 0 && $puntuacion->getValor($tipo_puntuacion) > 0){//insertamos si no hay puntos
						$puntosj1 = new Puntos('',$id_usuario,$j1,$bd_usuario,$id_liga,$id_division,$id_partido,obten_fechahora(),$puntuacion->getValor($tipo_puntuacion),$tipo);
						$puntosj1->insertar();
					}
				}//fin j1
				if($j2 > 0){//si no es temporal
					if($operacion == 'I' && $hay_puntos == 0 && $puntuacion->getValor($tipo_puntuacion) > 0){//insertamos si no hay puntos
						$puntosj2 = new Puntos('',$id_usuario,$j2,$bd_usuario,$id_liga,$id_division,$id_partido,obten_fechahora(),$puntuacion->getValor($tipo_puntuacion),$tipo);
						$puntosj2->insertar();
					}
				}//fin j2
				if($operacion == 'D' && $hay_puntos > 0){//eliminamos si hay puntos
						realiza_deleteGeneral('unicas','puntos','usuario',$id_usuario,'liga',$id_liga,'division',$id_division,'partido',$id_partido,'tipo',$tipo,'');
				}
				unset($puntosj1,$puntosj2);
			}//fin for
		}//fin de puntuacion
		unset($partido);
	}//fin ifjugador o datos de acceso incorrectos
	else{//vacio no hago nada
		echo '1';//error
	}
	
}//fin else

?>