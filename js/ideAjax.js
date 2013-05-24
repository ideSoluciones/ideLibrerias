function cargarRemplazando(casoUso, destino, formulario){
	formulario = formulario || null;
	casoUso = casoUso || "";

	var datosFormulario="";
	var ajxFile=pathCliente+"/"+casoUso;
	
	if (formulario!=null){
		datosFormulario = $(formulario).serialize();
		ajxFile = $(formulario).attr("action");

		if ($(formulario).validate().valid()){
		}
	}
	//alert(destino);
	
	$.post(
		ajxFile,
		datosFormulario, 
		function(data){
			$(destino).html(data);
			$(destino).hide().delay(1000).slideDown()
		}
	);
};
function cargarDatosRemplazando(casoUso, destino, datosFormulario){
/*	alert(	"casoUso: "+casoUso+"\n"+
			"Destino: "+destino+"\n"+
			"Los datos son: "+datosFormulario);*/
	casoUso = casoUso || "";
	$.post(
		casoUso,
		datosFormulario, 
		function(data){
			$(destino).html(data);
		}
	);
};

function cargarDatos(casoUso, funcion, formulario){
	formulario = formulario || null;
	casoUso = casoUso || "";

	var datosFormulario="";
	var ajxFile=pathCliente+"/"+casoUso;
	
	if (formulario!=null){
		datosFormulario = $(formulario).serialize();
		ajxFile = $(formulario).attr("action");
		if ($(formulario).validate().valid()){
		}
	}

			$.ajax({
				type: 'POST',
				url:  ajxFile,
				data: datosFormulario, 
				success: function(data){
					funcion(data);
				}
			});
};

function cargarDatosFuncion(casoUso, funcion, datosFormulario){
	datosFormulario = datosFormulario || "";

	$.ajax({
		type: 'POST',
		url:  casoUso,
		data: datosFormulario, 
		success: function(data){
			funcion(data);
		}
	});
};

function enviarCampos(id,codigo){
	var form=$("input[idForm='"+id+"'] ,select[idForm='"+id+"'], textarea[idForm='"+id+"']").serialize();
	$.ajax({
		type: 'POST',
		url:  pathCliente+'/ajax',
		data: "o=f&c="+codigo+"&"+form,
		success: function(data){
			new ajax_operacion(data);
		}
	});
}

function peticionAjax(clave,datos){
	var datos = base64_decode(datos) || "";
	datos=eval(datos);
	
	var param="";
	var primero=true;

	for(var i=0;i<datos.length;i++){
		if(!primero){param+=",";}else{primero=false;}
		if(datos[i].valor!=undefined){
			var val = new String(datos[i].valor);
			param+="{'nombre':'"+datos[i].nombre+"','valor':'"+val.replace(/\n/g,"\\n")+"'}";
		}
	}

	$.ajax({
		type: 'POST',
		url:  pathCliente+'/ajax',
		data: 'o=p&p={"c":"'+clave+'","p":"['+param+']"}',
		success: function(data){
			new ajax_operacion(data);
		}
	});
};

function ajax_operacion(parametros){
	this.agregarEvento=function(objeto,evento,js){
		$(objeto).bind(evento, function() {
			eval(js);
		});
	}
	this.alerta=function(mensaje){
		alert(mensaje);
	}
	this.anexar=function(objeto,propiedad,valor){
		var tmp=$(objeto).attr(propiedad);
		if(propiedad=="innerHTML"){
			$(objeto).html((tmp||"")+" "+valor);
		}else{
			$(objeto).attr(propiedad,(tmp||"")+" "+valor);
		}
	}
	this.asignar=function(objeto,propiedad,valor){
		if(propiedad=="innerHTML"){
			$(objeto).html(valor);
		}else{
			$(objeto).attr(propiedad,valor);
		}
	}
	this.borrar=function(objeto,propiedad){
		$(objeto).removeAttr(propiedad);
	}
	this.asignarValor=function(objeto,valor){
		$(objeto).val(valor);
	}
	this.crear=function(objeto,etiqueta,propiedades,texto){
		$(objeto).append("<"+etiqueta+" "+propiedades+">"+texto+"</"+etiqueta+">");
	}
	this.script=function(script){
		eval(script);
	}
	this.incluirCSS=function(url){
		$('head').append('<link rel="stylesheet" href="'+url+'" type="text/css" />');
	}
	this.incluirJS=function(url){
		$('head').append('<script type="text/javascript" src="'+url+'"></script>');
	}
	this.incluirScript=function(script){
		$('head').append('<script type="text/javascript">'+script+'</script>');
	}
	this.borrarObjeto=function(objeto){
		$(objeto).detach();
	}
	this.css=function(estilo){
		$('head').append('<style>'+estilo+'</script>');
	}
	try{
		var tmp=eval(parametros);
		if(is_array(tmp)){
			for(var i=0;i<tmp.length;i++){
				switch(tmp[i].nombre){
					case "agregarEvento":
						this.agregarEvento(tmp[i].objeto,tmp[i].evento,base64_decode(tmp[i].js));
						break;
					case "alerta":
						this.alerta(tmp[i].mensaje);
						break;
					case "anexar":
						this.anexar(tmp[i].objeto,tmp[i].propiedad,base64_decode(tmp[i].valor));
						break;
					case "asignar":
						this.asignar(tmp[i].objeto,tmp[i].propiedad,base64_decode(tmp[i].valor));
						break;
					case "borrar":
						this.borrar(tmp[i].objeto,tmp[i].propiedad);
						break;
					case "asignarValor":
						this.asignarValor(tmp[i].objeto,base64_decode(tmp[i].valor));
						break;
					case "crear":
						this.crear(tmp[i].objeto,tmp[i].etiqueta,tmp[i].propiedades,tmp[i].texto);
						break;
					case "llamar":
					case "script":
						this.script(tmp[i].script);
						break;
					case "incluirCSS":
						this.incluirCSS(tmp[i].url);
						break;
					case "incluirJS":
						this.incluirJS(tmp[i].url);
						break;
					case "incluirScript":
						this.incluirScript(tmp[i].script);
						break;
					case "borrarObjeto":
						this.borrarObjeto(tmp[i].objeto);
						break;
					case "css":
						this.css(tmp[i].estilo);
						break;
				}
			}
		}
	}catch(e){
		var error="<b>Error:</b> "+e.name+"<br>";
		error+="<b>Archivo:</b> "+e.fileName+"<br>";
		error+="<b>Linea:</b> "+e.lineNumber+"<br>";
		error+="<b>Mensaje:</b> "+e.message+"<br>";
		error+="<b>Pila:</b> <div style='border:2px solid;overflow:auto;width:450px;height:100px;'>"+htmlspecialchars(e.stack)+"</div>";
		error+="<b>Parametros:</b> <div style='border:2px solid;overflow:auto;width:450px;height:100px;'>"+parametros+"</div>";
		$("body").append("<div id='mensajeErrorAjax' style='dysplay:none;' title='Error en la respuesta Ajax'>"+error+"</div>");
		$("#mensajeErrorAjax").dialog({
			height: 400,
			width:500,
			modal: true
		});
	}
}
