/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* a la definicion de la Unidad Tributaria. 
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

var formulario          = '';                           						// Variable que representa y contiene el panel que contiene los objetos
var cambiar     		= false;	                    						// Variable que verifica el Estatus de la Operacion para Modificacion
var sistema             = "CFG";                        						// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_unidadtributaria.php'; 	// Ruta del Controlador de la Pantalla
var Actualizar=null;
var Campos =new Array(
						['codemp',''],
						['codunitri','novacio|'],
						['anno','novacio|'],
						['fecentvig','novacio|'],
						['gacofi','novacio|'],
						['fecpubgac','novacio|'],
						['decnro','novacio|'],
						['fecdec','novacio|'],
						['valunitri','novacio|']); // Arreglo que contiene la informacion del Registro, deben coincidir con la Tabla en la Base de Datos

Ext.onReady
(
  function()
	{
	 Ext.QuickTips.init();
	 Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	 Xpos = ((screen.width/2)-(450/2));
	 Ypos = ((screen.height/2)-(600/2));	
     var formulario = new Ext.form.FormPanel({
    	   	 title:"Definici&#243;n de Unidad Tributaria",
    		 frame:true,
    		 style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px',
    		 width: 650,
    		 height: 300,
    		 labelPad: 10,
    		 items:[{
				        xtype:"hidden",
				        name:"codemp",
				        id:"codemp"
			        },
			        {
				        layout:"form",
						border:false,
						defaultType: "textfield",
						style: "margin-top:20px;padding-left:50px;",
						labelWidth:175,
						items:[{
						        xtype:"textfield",
						        fieldLabel:"C&#243;digo",
						        labelSeparator:'',
						        labelWidth:40,
						        name:"codigo",
						        id:"codunitri",
								autoCreate: {tag: 'input', type: 'text', size: '4', autocomplete: 'off', maxlength: '4'},
						        width:75,
								disabled:true
				        	   },
				        	   {
						        xtype:"numberfield",
						        fieldLabel:"A&#241;o",
						        labelSeparator:'',
						        labelWidth:40,
						        name:"a&#241;o",
						        id:"anno",
								autoCreate: {tag: 'input', type: 'text', size: '4', autocomplete: 'off', maxlength: '4'},
						        width:75
				        	   },
				        	   {
				                xtype:"datefield",
				                fieldLabel:"Fecha de entrada en vigencia",
				                name:"fecha",
				                allowBlank:false,
				                labelSeparator:'',
				                labelWidth:200,
				                startDateField: 'fecpubgac',
				                vtype: 'daterange',
								width:100,
								id:"fecentvig",
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
					           },
				        	   {
						        xtype:"numberfield",
						        fieldLabel:"Gaceta Oficial",
						        labelWidth:40,
						        name:"gaceta",
						        labelSeparator:'',
						        id:"gacofi",
						        allowBlank:false,
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789');"},
						        width:100
				        	   },
				        	   {
				                xtype:"datefield",
				                fieldLabel:"Fecha de publicaci&#243;n",
				                name:"fecha_publicacion",
				                labelSeparator:'',
				                allowBlank:false,
				                labelWidth:200,
				                endDateField: 'fecentvig',
				               	vtype: 'daterange',
								width:100,
								id:"fecpubgac",
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
					           },
					           {
						        xtype:"textfield",
						        fieldLabel:"Decreto N&#176;/ Providencia",
						        labelSeparator:'',
						        labelWidth:40,
						        name:"decreto",
						        id:"decnro",
						        width:250,
								autoCreate: {tag: 'input', type: 'text', size: '40', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'0123456789áéíóúÁÉÍÓÚabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!¡;:/ ');"}
				        	   },
				        	   {
				                xtype:"datefield",
				                fieldLabel:"Fecha decreto",
				                labelSeparator:'',
				                name:"fecha_decreto",
				                allowBlank:false,
				                labelWidth:200,
								width:100,
								id:"fecdec",
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"},
								listeners:{
									'blur':function(objeto){
										var fechasta = new Date(Ext.getCmp('fecpubgac').getValue());
										var fecdesde = new Date(objeto.getValue());
										if(!ue_comparar_intervalo(fecdesde.format(Date.patterns.fechacorta), fechasta.format(Date.patterns.fechacorta))){
											Ext.MessageBox.show({
								    			title:'Advertencia',
												msg: 'La fecha de decreto debe ser anterior a la fecha de publicaci&#243;n',
												buttons: Ext.Msg.OK,
												icon: Ext.MessageBox.INFO
		    								});
		    								objeto.setValue('');
										}
									}
								}
					           },
					           {
									xtype:"textfield",
					        	   	fieldLabel:"Valor de la unidad tributaria",
									name:"valor",
									maxLength:15,
									labelSeparator:'',
									minLength:1,
									allowNegative:false,
									id:"valunitri",
									width:150,
									style: 'text-align:right',
									autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
									listeners:{
											'blur':function(objeto)
											{
											var numero = objeto.getValue();
											valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
											objeto.setValue(valor);
											},
											'focus':function(objeto)
											{
												var numero = formatoNumericoEdicion(objeto.getValue());
												objeto.setValue(numero);
											}
									}
								   }]
			        }]
    		});
     formulario.render("formulario_UnidadTributaria");
	}
);

function irCancelar()
{
	irNuevo();	
}

function irNuevo()
{
	limpiarCampos();
	var myJSONObject ={
		"oper":"nuevo"
	};	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_unidadtributaria.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var codigo = eval('(' + datos + ')');
		if(codigo != "")
		{
			Ext.getCmp('codunitri').setValue(codigo);
			Ext.getCmp('valunitri').setValue('0,00');
		}
	}	
	})
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
					limpiarCampos();
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
							Actualizar=null;
						}
						else
						{
							if(respuesta[1]=='-8')
							{
								Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, no puede eliminar registros intermedios');
							}
							else
							{
								if(respuesta[1]=='-9')
								{
									Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, esta vinculado con otros registros');
								}
								else
								{
									if(respuesta[1]=='-2')
									{
										Ext.MessageBox.alert('mensaje', 'El registro eliminado con &#233;xito. Debe verificar en nómina ya que esta siendo usada la función  FN[UNIDADTRIBUTARIA]');
									}
									else
									{
										Ext.MessageBox.alert('Error', 'El registro no pudo ser eliminado');
									}
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
