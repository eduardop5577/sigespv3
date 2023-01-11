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
var parametros='';
var ruta = '';
var RecordDef;
var RecordDefes;
var RecordDefmun;
var formFormula='';
var DatosNuevo ="";
var codpai='';
var codest='';
var DataStore="";
var DataStoreEstado="";
var DataStoreMunicipio="";
var ComboTipo=null;
var banderaGrabar 		= true;													// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= true;													// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= true;													// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var banderaCatalogo = 'estandar';
var banderaImprimir = false;

var Oper=new Array();
var ruta ='../../controlador/cfg/sigesp_ctr_cfg_estado.php';

var Actualizar=null;

var Campos =new Array(
		['codpai','novacio|'],
		['codest','novacio|'],
		['desest','novacio|']
)

Ext.onReady(function()
		{
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var myJSONObject ={
			"oper": 'catalogocombopais'	
	};	
	ObjSon=JSON.stringify(myJSONObject);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : ruta,
		params : parametros,
		method: 'POST',
		success: function (resultado, request) { 
		datos = resultado.responseText;  
		if(datos!='')
		{
			var DatosNuevo = eval('(' + datos + ')');
		}

		//Creaci�n del combo pa�s
		RecordDef = Ext.data.Record.create([
		                                    {name:'codpai', mapping:'codpai'},
		                                    {name: 'despai', mapping:'despai'}
		                                    ]);

		DataStore =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(DatosNuevo),
			reader: new Ext.data.JsonReader({
				root: 'raiz',              
				id: "codpai"    
			},
			RecordDef
			),
			data: DatosNuevo
		});

		var ComboTipo = new Ext.form.ComboBox({
			store :DataStore,
			forceSelection: true,
			fieldLabel:'Pa&#237;s',
			displayField:'despai',
			diplayValue:'codpai',
			name: 'pais',
			id:'codpai',
			width:200,
			listWidth: 180, 
			typeAhead: true,
			triggerAction:'all',
			mode:'local',
			valor:0,
			listeners: {
			'change': function(combo, nuevovalor,antiguovalor)
			{
			if(nuevovalor != antiguovalor)
			{
				if(String.trim(Ext.getCmp('codest')) != "")
				{
					Ext.getCmp('codest').setValue('');
					Ext.getCmp('desest').setValue('');
				}
			}
			}
		}

		})//Fin de combo pais
		ComboTipo.addListener('select',function(combo,record,index){ComboTipo.valor=codpai=record.get('codpai');});

		//Creaci�n del formulario
		var Xpos = ((screen.width/2)-(700/2));
		var formulario = new Ext.FormPanel({
			applyTo: 'formulario_estado',
			width: 700,
			height: 150,
			title: 'Estados',
			frame:true,
			labelWidth:200,
			defaults: {width:180},
			defaultType: 'textfield',
			style:'position:absolute;margin-left:'+Xpos+'px;margin-top:150px;',
			items: [
			        ComboTipo,
			        {
			        	xtype: 'textfield',
			        	fieldLabel: 'C&#243;digo',
			        	name: 'codigo',
			        	id: 'codest',
			        	width:30,
			        	autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '3'},
			        	readOnly: true,
			        	listeners:{
			        		'focus':function(objeto){
			        			if((codpai == null)||(codpai=="")) {
			        				Ext.Msg.alert('Mensaje','Debe seleccionar un pais valido');
			        			}
							}
			        	}
			        },{
			        	xtype: 'textfield',
			        	fieldLabel: 'Descripci&#243;n',
			        	name: 'descripcion',
			        	autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ., ');"},
			        	id: 'desest'
			        }]
		})	//Fin del formulario
	}//fin de success
	})//fin de ajax request	

		});

function irNuevo()
{
	Ext.getCmp('codpai').enable();
	Ext.getCmp('codest').enable();
	Ext.getCmp('desest').setValue('');
	if(Ext.getCmp('codpai').valor != "")
	{
		var myJSONObject ={
				"oper":"nuevo",
				"codpai":Ext.getCmp('codpai').valor
		};
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
			url : '../../controlador/cfg/sigesp_ctr_cfg_estado.php',
			params : parametros,
			method: 'POST',
			success: function ( result, request) 
			{ 
			datos = result.responseText;
			var codigo = eval('(' + datos + ')');
			if(codigo != "")
			{
				Ext.getCmp('codest').setValue(codigo);
			}
			}	
		})
	}
	else
	{
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe indicar el pa&#237;s, verifique por favor',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});

	}
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
		Json=cargarJson(operacion);
		myJSONObject=Ext.util.JSON.decode(Json);	
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		obtenerMensaje('procesar','','Guardando Datos');
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',	
			success: function ( resultad, request ) 
			{ 
				datos = resultad.responseText;
				Ext.Msg.hide();
				var respuesta = datos.split("|");
				if (respuesta[1] == '1')
				{
					Ext.MessageBox.alert('mensaje','Registro '+mensaje + ' con &#233;xito');
					limpiarCampos();
					Ext.getCmp('codpai').enable();
					Ext.getCmp('codest').enable();
					Ext.getCmp('codpai').valor="";
					limpiarCampos();
					codpai="";
					codest="";
					Actualizar=null;
				}
				else
				{
					Ext.MessageBox.alert('Error', respuesta[0]);
				}
			},
			failure: function (result, request)
			{ 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', resultad.responseText);
			}
		});
	}

}

function irEliminar()
{
	if(Actualizar)
	{
		function respuesta(btn)
		{
			if(btn=='yes')
			{
				obtenerMensaje('procesar','','Eiminando Datos');
				Json=cargarJson('eliminar');
				Ob=Ext.util.JSON.decode(Json);
				ObjSon=JSON.stringify(Ob);
				parametros = 'ObjSon='+ObjSon;
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
							Ext.MessageBox.alert('mensaje','Registro eliminado con &#233;xito');
							limpiarCampos();
							Ext.getCmp('codpai').enable();
							Ext.getCmp('codest').enable();
							Ext.getCmp('codpai').valor="";
							limpiarCampos();
							codpai="";
							codest="";
							Actualizar=null;
						}
						else
						{
							if(respuesta[1]=='-9')
							{
								Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, esta vinculado con otros registros');
							}
							else
							{
								if(respuesta[1]=='-8')
								{
									Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, no puede eliminar registros intermedios');
								}
								else
								{
									Ext.MessageBox.alert('Error', 'El registro no pudo ser eliminado');
								}
							}
						}
					},
					failure: function ( result, request) { 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', result.responseText); 
					} 
					});
			}
		};
		Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', respuesta);
	}
	else
	{
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Opci&#243;n inv&#225;lida, el registro debe estar previamente guardado, verifique por favor',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});  
	}	

}

function verificarPrefijo()
{

	var myJSONObject ={
			"oper":"verificarcodigo",
			"codpai":Ext.getCmp('codpai').valor,
			"codest":Ext.getCmp('codest').getValue()
	};

	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_estado.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
		datos = result.responseText;
		var respuesta = eval('(' + datos + ')');
		estado = Ext.getCmp('codest').getValue();
		if((respuesta.existe)&&(Actualizar == null))
		{
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Ya existe un estado con c&#243;digo '+estado+' asociado al pais '+Ext.getCmp('codpai').getValue()+', debe indicar uno distinto',
				buttons: Ext.Msg.OK,
				fn: '',
				animEl: 'elId',
				icon: Ext.MessageBox.ERROR
			});
			Ext.getCmp('codest').setValue('');
		}
		}	
	});
}

function irCancelar()
{
	Ext.getCmp('codpai').enable();
	Ext.getCmp('codest').enable();
	Ext.getCmp('codest').setValue('');
	Ext.getCmp('desest').setValue('');
}

