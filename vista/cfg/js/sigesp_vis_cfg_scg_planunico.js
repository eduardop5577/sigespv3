/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta    = true; 
var Actualizar      = null;
var formRecursoEgreso = null;
var banderaNuevo    = false; // Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var banderaGrabar 	= false;// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar = false;// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaCatalogo = 'estandar';
var ruta            = '../../controlador/cfg/sigesp_ctr_cfg_scg_planunico.php'; // Ruta del Controlador de la Pantalla

var formplan =empresa["formplan"];
formplan=replaceAll(formplan,'-','');
formplan=replaceAll(formplan,' ','');
longplan=formplan.length;

var Campos =new Array(
		['sig_cuenta','novacio|','true'],
	    ['denominacion','novacio|','false']
);
	    
Ext.onReady(function(){
 	
 	Ext.QuickTips.init();
 	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = 150;
	formRecursoEgreso = new Ext.FormPanel({
		applyTo: 'formulario_plan_unico_re',
		width: 700,
		height: 150,
		title: 'Cat&#225;logo de recursos y egresos',
		frame:true,
		labelWidth:150,
		defaultType: 'textfield',
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
		items: [{
			xtype: 'textfield',
			labelSeparator : '',
			fieldLabel: 'C&#243;digo',
			name: 'codigo',
			id: 'sig_cuenta',
			autoCreate:{tag: 'input',type: 'text',maxlength: '9', onkeypress: "return keyRestrict(event,'0123456789');"},
			listeners:{'blur' : function(campo)
						{
							valor = rellenarCampoCerosDerecha(String.trim(campo.getValue()),9);
							campo.setValue(valor);
						}	
					  },
			width: 100
		},{
			xtype: 'textfield',
			labelSeparator : '',
			fieldLabel: 'Denominaci&#243;n',
			name: 'denominaci&#243;n',
			id: 'denominacion',
			autoCreate:{tag: 'input',type: 'text',maxlength: '254', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=°;:[]{}·ÈÌÛ˙¡…Õ”⁄ ');"},
			width: 500
		}]
	});
});

function irBuscar()
{
	//creando datastore y columnmodel para el catalogo de agencias
	var reCuentaSCG = Ext.data.Record.create([
		{name: 'sig_cuenta'},
		{name: 'denominacion'}
	]);
	
	var dsCuentaSPG =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCuentaSCG)
	});
						
	var cmCuentaSPG = new Ext.grid.ColumnModel([
		{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'sig_cuenta'},
		{header: "Descripci&#243;n", width: 80, sortable: true, dataIndex: 'denominacion'}
    ]);
	//fin creando datastore y columnmodel para el catalogo de agencias
	
	var comCatalogoCuenta = new com.sigesp.vista.comCatalogo({
		titvencat: 'Cat&#225;logo de cuentas del plan &#250;nico de recursos y egresos',
		anchoformbus: 570,
		altoformbus:150,
		anchogrid: 570,
		altogrid: 430,
		anchoven: 600,
		altoven: 500,
		datosgridcat: dsCuentaSPG,
		colmodelocat: cmCuentaSPG,
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codcue',valor:'sig_cuenta',ancho:150,longitud:'25',anyMatch:false},
				   {etiqueta:'Descripci&#243;n',id:'dencue',valor:'denominacion',ancho:350,longitud:'254'}],
		rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_scg_planunico.php',
		parametros: "ObjSon={'oper': 'catalogo'",
		tipbus:'P',
		setdatastyle:'F',
		formulario:formRecursoEgreso
	});
	
	comCatalogoCuenta.mostrarVentana();
}


function irNuevo()
{
	limpiarFormulario(formRecursoEgreso);
}

function irCancelar()
{
	 irNuevo();
}

function irGuardar()
{
	if(Actualizar==null)
	{
		operacion='incluir';
		mensaje='incluido';
	}
	else
	{	
		operacion='actualizar';
		mensaje='modificado';			
	}
	if(validarObjetos2()==false)
	{
		return false;
	}
	else
	{
		var Json    = cargarJson(operacion);
		try
		{
			var objjson = Ext.util.JSON.decode(Json);
			if (typeof(objjson) == 'object')
			{
				var parametros = 'ObjSon=' + Json;
				obtenerMensaje('procesar','','Guardando Datos');
				Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',	
					success: function ( resultad, request )
					{ 
						Ext.Msg.hide();
						var datos = resultad.responseText;
						var respuesta = datos.split("|");
						if (respuesta[1] == '1')
						{
							Ext.MessageBox.alert('mensaje','Registro '+mensaje + ' con &#233;xito');
							Actualizar=null;
						}
						else
						{
							if(respuesta[1] == '-10')
							{
								Ext.MessageBox.alert('Error', 'La combinaci&#243;n afectaci&#243;n presupuestaria y tipo ya existe');
							}
							else
							{
								if(respuesta[0]!='')
								{
									Ext.MessageBox.alert('Error', respuesta[0]);
								}
								else
								{
									Ext.MessageBox.alert('Error', 'Ocurri&#243; un error el registro no pudo ser '+mensaje);
								}
							}
						}
						limpiarFormulario(formRecursoEgreso);
					},
					failure: function (result, request)
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', resultad.responseText);
						mascara.hide();
					}
				});
			}
		}
		catch(e)
		{
			//no imprimo la excepcion
		}
	}
}

function irEliminar()
{
	function eliminarRegistro(btn)
	{
		if(btn=='yes')
		{
			var Json    = cargarJson('eliminar');
			try
			{
				var objjson = Ext.util.JSON.decode(Json);
				if (typeof(objjson) == 'object')
				{
					var parametros = 'ObjSon=' + Json;
					obtenerMensaje('procesar','','Eiminando Datos');
					Ext.Ajax.request({
						url : ruta,
						params : parametros,
						method: 'POST',	
						success: function ( resultad, request )
						{ 
							Ext.Msg.hide();
							var datos = resultad.responseText;
							var Registros = datos.split("|");
						 	if (Registros[1] == '1')
							{
						 		Ext.MessageBox.alert('mensaje','Registro Eliminado con &#233;xito');
								Actualizar=null;
							}
							else
							{
								if(Registros[1]=='-9')
								{
									Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, esta vinculado con otros registros');
							  	}
								else
								{
									if (Registros[0]!='')
									{
										Ext.MessageBox.alert('Error', Registros[0]);
								  	}
									else
									{
										Ext.MessageBox.alert('Error', 'El registro no pudo ser eliminado');
								  	}
								}
							}
							limpiarFormulario(formRecursoEgreso);
						},
						failure: function (result, request)
						{ 
							Ext.Msg.hide();
							Ext.MessageBox.alert('Error', resultad.responseText);
						}
					});
				}
			}
			catch(e)
			{
				//no imprimo la excepcion
			}
		}
	}
	Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', eliminarRegistro);
}