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

var parametros=''; //arreglo de datos 
var ruta = '';  //ruta del controlador
var RecordDef; //record de pais
var RecordDefes; //record de estado
var RecordDefmun; //record de municipio
var RecordDefparroquia; //record de parroquia
var DatosNuevo ='';
var codpai='';  //codigo de pais
var codest='';  //codigo de estado  
var codmun='';  //codigo de municipio 
var codpar='';  //codigo de parroquia
var DataStore='';  //datastore de pais    
var DataStoreEstado='';  //datastore de estado
var DataStoreMunicipio='';  //datastore de municipio
var DataStoreParroquia='';  //datastore de parroquia
var ComboTipo = null;
var Comboest  = null;
var Combomun  = null;
var Comboparroquia = null;

var Oper=new Array();
var ruta ='../../controlador/cfg/sigesp_ctr_cfg_comunidad.php';

var Actualizar=null;

var Campos =new Array(
		['codpai','novacio|'],
		['codest','novacio|'],
		['codmun','novacio|'],
		['codpar','novacio|'],
		['codcom','novacio|'],
		['nomcom','novacio|']
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
		                                    {name:'codpai'},
		                                    {name: 'despai'}
		                                    ]);

		DataStore =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(DatosNuevo),
			reader: new Ext.data.JsonReader({
				root: 'raiz',              
				id: "id"   
			},
			RecordDef
			),
			data: DatosNuevo
		});

		ComboTipo = new Ext.form.ComboBox({
			store :DataStore,
			fieldLabel:'Pa&#237;s',
			forceSelection: true,
			displayField:'despai',
			diplayValue:'codpai',
			name: 'pais',
			id:'codpai',
			width:180,
			listWidth: 180, 
			typeAhead: true,
			mode:'local',
			triggerAction:'all',
			valor:0,
			listeners: {
			'change': function(combo, nuevovalor,antiguovalor)
			{
			if(nuevovalor != antiguovalor)
			{
				if(String(Ext.getCmp('codest').getValue()).trim != "")
				{
					Ext.getCmp('codest').setValue('');
					Ext.getCmp('codmun').setValue('');
					Ext.getCmp('codest').setValue('');
					Ext.getCmp('codpar').setValue('');
					Ext.getCmp('codcom').setValue('');
					Ext.getCmp('nomcom').setValue('');
					Ext.getCmp('codest').valor=0;
					Ext.getCmp('codmun').valor=0;
					Ext.getCmp('codpar').valor=0;
					codest="";
					codmun="";
					codpar="";
				}
			}
			}
		}

		})//Fin de combo pa�s

		//Creaci�n del combo estado

		RecordDefes = Ext.data.Record.create([
		                                      {name: 'codpai'},
		                                      {name: 'codest'},
		                                      {name: 'desest'}
		                                      ]);

		DataStoreEstado =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(DatosNuevo),
			reader: new Ext.data.JsonReader({
				root: 'raiz',             
				id: "id"   
			},
			RecordDefes
			)				
		});

		Comboest = new Ext.form.ComboBox({
			store: DataStoreEstado,
			forceSelection: true,
			fieldLabel:'Estado',
			displayField:'desest',
			diplayValue:'codest',
			name: 'estado',
			width:180,
			listWidth: 180, 
			id:'codest',
			typeAhead: true,
			mode:'local',
			triggerAction:'all',
			valor:0,
			listeners: {
			'change': function(combo, nuevovalor,antiguovalor)
			{
			if(nuevovalor != antiguovalor)
			{
				if(String(Ext.getCmp('codmun').getValue()).trim != "")
				{
					Ext.getCmp('codmun').setValue('');
					Ext.getCmp('codest').setValue('');
					Ext.getCmp('codpar').setValue('');
					Ext.getCmp('codcom').setValue('');
					Ext.getCmp('nomcom').setValue('');
					Ext.getCmp('codest').valor=0;
					Ext.getCmp('codmun').valor=0;
					Ext.getCmp('codpar').valor=0;
					codmun="";
					codpar="";
				}
			}
			}
		}
		})
		///fin combo estado

		//Creaci�n de combo municipio
		RecordDefmun = Ext.data.Record.create([
		                                       {name: 'codpai'},
		                                       {name: 'codest'},
		                                       {name: 'codmun'},
		                                       {name: 'denmun'}
		                                       ]);

		DataStoreMunicipio =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(DatosNuevo),
			reader: new Ext.data.JsonReader({
				root: 'raiz',               
				id: "id"   
			},
			RecordDefmun

			),
			data: DatosNuevo
		});

		Combomun = new Ext.form.ComboBox({
			store:DataStoreMunicipio,
			fieldLabel:'Municipio',
			forceSelection: true,
			displayField:'denmun',
			diplayValue:'codmun',
			name: 'municipio',
			width:180,
			listWidth: 180, 
			id:'codmun',
			listWidth: 180,
			typeAhead: true,
			mode:'local',
			triggerAction:'all',
			valor:0,
			listeners: {
			'change': function(combo, nuevovalor,antiguovalor)
			{
			if(nuevovalor != antiguovalor)
			{
				if(String(Ext.getCmp('codpar').getValue()).trim != "")
				{
					Ext.getCmp('codpar').setValue('');
					Ext.getCmp('codcom').setValue('');
					Ext.getCmp('nomcom').setValue('');
					Ext.getCmp('codpar').valor=0;
					codpar="";
				}
			}
			}
		}
		})
		//Fin de combo municipio	

		//Creaci�n de combo parroquia
		RecordDefparroquia = Ext.data.Record.create([
		                                             {name: 'codpai'},
		                                             {name: 'codest'},
		                                             {name: 'codmun'},
		                                             {name: 'codpar'},
		                                             {name: 'denpar'}
		                                             ]);

		DataStoreParroquia =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(DatosNuevo),
			reader: new Ext.data.JsonReader({
				root: 'raiz',               
				id: "id"   
			},
			RecordDefparroquia			     
			),
			data: DatosNuevo
		});

		Comboparroquia = new Ext.form.ComboBox({
			store: DataStoreParroquia,
			fieldLabel:'Parroquia',
			forceSelection: true,
			displayField:'denpar',
			diplayValue:'codpar',
			width:180,
			id:'codpar',
			listWidth: 180,
			typeAhead: true,
			mode:'local',
			triggerAction:'all',
			valor:0,
			listeners: {
			'change': function(combo, nuevovalor,antiguovalor)
			{
			if(nuevovalor != antiguovalor)
			{
				if(String(Ext.getCmp('codcom').getValue()).trim != "")
				{
					Ext.getCmp('codcom').setValue('');
					Ext.getCmp('nomcom').setValue('');
				}
			}
			}
		}
		});
		//Fin de combo parroquia	

		//Creaci�n del formulario	
		var Xpos = ((screen.width/2)-(700/2));
		var formulario = new Ext.FormPanel({
			applyTo: 'formulario_comunidad',
			width: 700,
			height: 200,
			title: 'Comunidades',
			frame:true,
			labelWidth:200,
			defaultType: 'textfield',
			style:'position:absolute;margin-left:'+Xpos+'px;margin-top:150px;',
			items: [
			        ComboTipo,
			        Comboest,
			        Combomun,
			        Comboparroquia,
			        {
			        	xtype: 'textfield',
			        	fieldLabel: 'C&#243;digo',
			        	name: 'codigo',
			        	id: 'codcom',
			        	width:30,
			        	readOnly:true
			        },{
			        	xtype: 'textfield',
			        	fieldLabel: 'Descripci&#243;n',
			        	name: 'nombre',
			        	id: 'nomcom',
			        	autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '80', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ., ');"},
			        	width:350
			        }]
		})	//Fin del formulario

		ComboTipo.addListener('select',agregar_combo_estado);
		Comboest.addListener('select',agregar_combo_municipio);
		Combomun.addListener('select',agregar_combo_parroquia);
		Comboparroquia.addListener('select',function(combo,record,index){Comboparroquia.valor = codpar=record.get('codpar')});

	}//fin de success
	})//fin de ajax request	

	function agregar_combo_estado(par,rec)
	{
		ComboTipo.valor = codpai = rec.get('codpai');
		var myJSONObject ={
				"oper": 'catalogocomboestado',
				"codpai":codpai
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
			DataStoreEstado.loadData(DatosNuevo);
		}
		})	
	}


	function agregar_combo_municipio(par,rec)
	{
		ComboTipo.valor = codpai = rec.get('codpai');
		Comboest.valor  = codest = rec.get('codest');
		var myJSONObject ={
				"oper": 'catalogocombomuni',
				"codpai":codpai,
				"codest":codest
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
			DataStoreMunicipio.loadData(DatosNuevo);
		}
		})	
	}

	function agregar_combo_parroquia(par,rec)
	{
		ComboTipo.valor = codpai = rec.get('codpai');
		Comboest.valor  = codest = rec.get('codest');
		Combomun.valor  = codmun = rec.get('codmun');
		var myJSONObject ={
				"oper": 'catalogocomboparroquia',
				"codpai":codpai,
				"codest":codest,
				"codmun":codmun
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
			DataStoreParroquia.loadData(DatosNuevo);
		}
		})	
	}

});

function irCancelar()
{
	Ext.getCmp('codpai').enable();
	Ext.getCmp('codest').enable();
	Ext.getCmp('codmun').enable();
	Ext.getCmp('codpar').enable();
	Ext.getCmp('codcom').enable();
	Ext.getCmp('nomcom').setValue('');
	Ext.getCmp('codpai').setValue('');
	Ext.getCmp('codest').setValue('');
	Ext.getCmp('codmun').setValue('');
	Ext.getCmp('codpar').setValue('');
	Ext.getCmp('codcom').setValue('');
}

function irNuevo()
{
	Ext.getCmp('codpai').enable();
	Ext.getCmp('codest').enable();
	Ext.getCmp('codmun').enable();
	Ext.getCmp('codpar').enable();
	Ext.getCmp('codcom').enable();
	Ext.getCmp('nomcom').setValue('');

	if((codpai  != "")&&(codest != "")&&(codmun != "")&&(codpar != ""))
	{
		var myJSONObject ={
				"oper":"nuevo",
				"codpai":codpai,
				"codest":codest,
				"codmun":codmun,
				"codpar":codpar
		};
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
			url : '../../controlador/cfg/sigesp_ctr_cfg_comunidad.php',
			params : parametros,
			method: 'POST',
			success: function ( result, request) 
			{ 
			datos = result.responseText;
			var codigo = eval('(' + datos + ')');
			if(codigo != "")
			{
				Ext.getCmp('codcom').setValue(codigo);
			}
			}	
		})
	}
	else
	{
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe indicar el pais, estado, municipio y parroquia, verifique por favor',
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
				Ext.Msg.hide();
				datos = resultad.responseText;
				var respuesta = datos.split("|");
				if (respuesta[1] == '1')
				{
					Ext.MessageBox.alert('mensaje','Registro '+mensaje + ' con &#233;xito');
					Ext.getCmp('codpai').enable();
					Ext.getCmp('codest').enable();
					Ext.getCmp('codmun').enable();
					Ext.getCmp('codpar').enable();
					Ext.getCmp('codcom').enable();
					Ext.getCmp('codpai').valor=0;
					Ext.getCmp('codest').valor=0;
					Ext.getCmp('codmun').valor=0;
					Ext.getCmp('codpar').valor=0;
					limpiarCampos();
					codpai="";
					codest="";
					codmun="";
					codpar="";
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
				obtenerMensaje('procesar','','Eliminando Datos');
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
						datos = resultad.responseText;
						var respuesta = datos.split("|");
						if (respuesta[1] == '1')
						{
							Ext.MessageBox.alert('mensaje','Registro eliminado con &#233;xito');
							limpiarCampos();
							Ext.getCmp('codpai').enable();
							Ext.getCmp('codest').enable();
							Ext.getCmp('codmun').enable();
							Ext.getCmp('codpar').enable();
							Ext.getCmp('codcom').enable();
							Ext.getCmp('codpai').valor=0;
							Ext.getCmp('codest').valor=0;
							Ext.getCmp('codmun').valor=0;
							Ext.getCmp('codpar').valor=0;
							codpai="";
							codest="";
							codmun="";
							codpar="";
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
					failure: function ( result, request)
					{ 
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
			"codest":Ext.getCmp('codest').valor,
			"codmun":Ext.getCmp('codmun').valor,
			"codpar":Ext.getCmp('codpar').valor,
			"codcom":Ext.getCmp('codcom').getValue()
	};

	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_comunidad.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
		datos = result.responseText;
		var respuesta = eval('(' + datos + ')');
		comunidad = Ext.getCmp('codcom').getValue();
		if((respuesta.existe)&&(Actualizar==null))
		{
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Ya existe una comunidad con c&#243;digo '+comunidad+' asociado a la parroquia '+Ext.getCmp('codpar').getValue()+', municipio '+Ext.getCmp('codmun').getValue()+', estado '+Ext.getCmp('codest').getValue()+' y pais '+Ext.getCmp('codpai').getValue()+', debe indicar uno distinto',
				buttons: Ext.Msg.OK,
				fn: '',
				animEl: 'elId',
				icon: Ext.MessageBox.ERROR
			});
			Ext.getCmp('codcom').setValue('');
		}
		}	
	});
}
