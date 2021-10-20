// JavaScript Document
function generar(resp){
	var dataString = 'respuesta=S';
	if(resp == 'S'){//valido que la fecha sea correcta
		var url = "generar_fase.php"; // El script a dónde se realizará la petición.
		$.ajax({
		   type: "POST",
		   url: url,
		   data: dataString, // Adjuntar los campos del formulario enviado.
		   success: function(data)
		   {
			   //var respuesta = "#respuesta"+id; 
			   //$('#respuesta').html(data); // Mostrar la respuestas del script PHP.
			   //alert(data);
			   swal("Nueva fase eliminatoria", "La nueva fase eliminatoria se ha generado correctamente.", "success");
			   setTimeout ("window.location.reload();", 2500);
		   }
		});
	}
	return false;
}