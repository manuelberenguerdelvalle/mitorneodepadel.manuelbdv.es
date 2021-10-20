<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Administracion de Torneos de Padel mitorneodepadel.es</title>
<script src="../javascript/jquery-1.11.1.js" type="text/javascript"></script>
<script src="../javascript/jquery.bpopup.min.js" type="text/javascript"></script>
<script language="javascript">
function abrir_bpopup(canal){
	if(canal == 'premium'){
		var cod_html = '<div class="poptitulo"><h2>Canal Torneos Premium</h2></div><div class="popcentro"><iframe id="video" class="video"  src="https://www.youtube.com/embed/xrQKauYoUOE" frameborder="0" allowfullscreen></iframe><div class="partes"><a class="link_canal" href="#" onClick="cambia_video(1);">1 - Registro</a><br><a class="link_canal" href="#" onClick="cambia_video(2);">2 - Pagos</a><br><a class="link_canal" href="#" onClick="cambia_video(3);">3 - Configuración inicial</a><br><a class="link_canal" href="#" onClick="cambia_video(4);">4 - Configurar inscripciones con pago online</a><br><a class="link_canal" href="#" onClick="cambia_video(5);">5 - Fechas de inscripción</a><br><a class="link_canal" href="#" onClick="cambia_video(6);">6 - Inscripción pago presencial</a><br><a class="link_canal" href="#" onClick="cambia_video(7);">7 - Inscripción pago online</a><br><a class="link_canal" href="#" onClick="cambia_video(8);">8 - Crear publicidad</a><br><a class="link_canal" href="#" onClick="cambia_video(9);">9 - Crear calendario y partidos</a><br><a class="link_canal" href="#" onClick="cambia_video(10);">10 - Información de la liga</a><br><a class="link_canal" href="#" onClick="cambia_video(11);">11 - Autocompletado</a><br><a class="link_canal" href="#" onClick="cambia_video(12);">12 - Privacidad</a><br><a class="link_canal" href="#" onClick="cambia_video(13);">13 - Crear noticia</a><br><a class="link_canal" href="#" onClick="cambia_video(14);">14 - Tickets</a><br><a class="link_canal" href="#" onClick="cambia_video(15);">15 - Grupos y fases</a><br><a class="link_canal" href="#" onClick="cambia_video(16);">16 - Nueva temporada</a></div></div><div class="poppie"></div>';
		document.getElementById('content_popup').innerHTML = cod_html;
	}
	else{
		var cod_html = '<div class="poptitulo"><h2>Canal Torneos Gratis</h2></div><div class="popcentro"><iframe id="video" class="video"  src="https://www.youtube.com/embed/xrQKauYoUOE" frameborder="0" allowfullscreen></iframe><div class="partes"><a class="link_canal" href="#" onClick="cambia_video(21);">1 - Registro</a><br><a class="link_canal" href="#" onClick="cambia_video(22);">2 - Configuración inicial</a><br><a class="link_canal" href="#" onClick="cambia_video(23);">3 - Fecha de inscripción</a><br><a class="link_canal" href="#" onClick="cambia_video(24);">4 - Inscripciones</a><br><a class="link_canal" href="#" onClick="cambia_video(25);">5 - Crear calendario y partidos</a><br><a class="link_canal" href="#" onClick="cambia_video(26);">6 - Información de la liga</a><br><a class="link_canal" href="#" onClick="cambia_video(27);">7 - Autocompletado</a><br><a class="link_canal" href="#" onClick="cambia_video(28);">8 - Crear noticia</a><br><a class="link_canal" href="#" onClick="cambia_video(29);">9 - Tickets</a><br><a class="link_canal" href="#" onClick="cambia_video(30);">10 - Fase eliminatorias</a></div></div><div class="poppie"></div>';
		document.getElementById('content_popup').innerHTML = cod_html;
	}
	$('#content_popup').bPopup('');
}
function cambia_video(parte){
	//PARA LAS PREMIUM
	if(parte == 1){
		$('#video').attr("src", "https://www.youtube.com/embed/xrQKauYoUOE");
	}
	else if(parte == 2){
		$('#video').attr("src", "https://www.youtube.com/embed/GmwX24ktHS0");
	}
	else if(parte == 3){
		$('#video').attr("src", "https://www.youtube.com/embed/2aGuJuRgVbs");
	}
	else if(parte == 4){
		$('#video').attr("src", "https://www.youtube.com/embed/5-wY7S9MJOE");
	}
	else if(parte == 5){
		$('#video').attr("src", "https://www.youtube.com/embed/e6d_2wJv_nI");
	}
	else if(parte == 6){
		$('#video').attr("src", "https://www.youtube.com/embed/veWgr0daZbc");
	}
	else if(parte == 7){
		$('#video').attr("src", "https://www.youtube.com/embed/XxGQdvbkCP0");
	}
	else if(parte == 8){
		$('#video').attr("src", "https://www.youtube.com/embed/eg_l3CAJ7tU");
	}
	else if(parte == 9){
		$('#video').attr("src", "https://www.youtube.com/embed/X8Z7OLi1his");
	}
	else if(parte == 10){
		$('#video').attr("src", "https://www.youtube.com/embed/cM07YzjydSw");
	}
	else if(parte == 11){
		$('#video').attr("src", "https://www.youtube.com/embed/ZyCFQbQSQO4");
	}
	else if(parte == 12){
		$('#video').attr("src", "https://www.youtube.com/embed/5ja4P5uFCDY");
	}
	else if(parte == 13){
		$('#video').attr("src", "https://www.youtube.com/embed/qIGwcRWKeFs");
	}
	else if(parte == 14){
		$('#video').attr("src", "https://www.youtube.com/embed/ZTaHp3eTE0A");
	}
	else if(parte == 15){
		$('#video').attr("src", "https://www.youtube.com/embed/ow6KuMOhdgA");
	}
	else if(parte == 16){
		$('#video').attr("src", "https://www.youtube.com/embed/eROt-VyUFU0");
	}
	//PARA LAS GRATIS
	else if(parte == 21){
		$('#video').attr("src", "https://www.youtube.com/embed/xrQKauYoUOE");
	}
	else if(parte == 22){
		$('#video').attr("src", "https://www.youtube.com/embed/2aGuJuRgVbs");
	}
	else if(parte == 23){
		$('#video').attr("src", "https://www.youtube.com/embed/e6d_2wJv_nI");
	}
	else if(parte == 24){
		$('#video').attr("src", "https://www.youtube.com/embed/veWgr0daZbc");
	}
	else if(parte == 25){
		$('#video').attr("src", "https://www.youtube.com/embed/X8Z7OLi1his");
	}
	else if(parte == 26){
		$('#video').attr("src", "https://www.youtube.com/embed/cM07YzjydSw");
	}
	else if(parte == 27){
		$('#video').attr("src", "https://www.youtube.com/embed/ZyCFQbQSQO4");
	}
	else if(parte == 28){
		$('#video').attr("src", "https://www.youtube.com/embed/qIGwcRWKeFs");
	}
	else if(parte == 29){
		$('#video').attr("src", "https://www.youtube.com/embed/ZTaHp3eTE0A");
	}
	else if(parte == 30){
		$('#video').attr("src", "https://www.youtube.com/embed/ow6KuMOhdgA");
	}
	else{
	}
}
</script>
<link rel="stylesheet" type="text/css" href="../css/bpopup.css" />
<style>
.canal {	
	width:99% !important;
	margin-top:15%;
	/*border:1px black solid;*/
	float:left;
}
.link_canal {
	text-decoration:none;
	color: #181C83;
	/*color:#e52d27;*/
}
.link_canal:hover {
	text-decoration: underline;
	/*color: #121562;*/
	/*color:#e52d27;*/
}
.video {
	width:60% !important;
	height:91% !important;
	margin-left:20%;
	margin-top:1%;
	-webkit-box-shadow: 0px 0px 5px 3px rgba(0,0,0,1);
	-moz-box-shadow:    0px 0px 5px 3px rgba(0,0,0,1);
	box-shadow:         0px 0px 5px 3px rgba(137,137,254,1);
	float:left;
}
.partes {
	width:18% !important;
	height:85% !important;
	margin-top:2%;
	/*border:1px black solid;*/
	float:right;
}
</style>
</head>

<body>

<div class="canal"><a class="link_canal" href="#" target="_parent" onClick="abrir_bpopup('premium');">-Ver Canal Torneos Premium</a></div>
<div class="canal"><a class="link_canal" href="#" target="_parent" onClick="abrir_bpopup('gratis');">-Ver Canal Torneos Gratis</a></div>

<div id="content_popup">

</div>

</body>
</html>