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

var datos              = null;
var gridAsigNivel         = null;
var ventanaAsigNivel      = null;
var iniciargrid        = false;
var parametros         = '';
var rutaAsigNivel         = '../../controlador/cfg/sigesp_ctr_cfg_asignivel.php';
var rutaPermisos = '../../controlador/sss/sigesp_ctr_sss_usuariosniveles.php';


/******************************************************************************
* @Función genérica para el uso del catálogo de Centro de Costos
* @parametros: 
* @retorno: 
* @fecha de creación: 13/12/2011.
* @autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*******************************************************************************/
	function catalogoAsigNivel()
	{	
		this.mostrarCatalogoAsigNivel = mostrarCatalogoAsigNivel;
	}


/*************************************************************************
* @Función que acualiza el catalgo para buscar por determinado campo
* @parametros: criterio: campo por el que se actualiza
* 				cadena: campo a actualizar
* @retorno:
* @fecha de creación: 13/12/2011.
* @autor: Ing. Yesenia Moreno.
**************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
**************************************************************************/
	function actualizarDataAsigNivel(criterio,cadena)
	{
		var myJSONObject ={
			'oper': 'catalogo',
			'cadena': cadena,
			'criterio': criterio,
			'sistema': sistema,
			'vista': vista
		};
		objdata=JSON.stringify(myJSONObject);
		parametros = 'objdata='+objdata; 
		Ext.Ajax.request({
		url : rutaAsigNivel,
		params : parametros,
		method: 'POST',
		success: function ( resultado, request )
		{ 
			datos = resultado.responseText;
			if (datos!='')
			{
				var DatosNuevo = eval('(' + datos + ')');
				gridAsigNivel.store.loadData(DatosNuevo);
			}
		}
		});
	}
	
	
/***********************************************************************
* Obtener el valor de los caracteres de la caja texto
* @parámetros: obj --> caja de texto.
* @retorna: valor obtenido del objeto.
* @fecha de creación: 13/12/2011.
* @autor: Ing. Yesenia Moreno.
**************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
************************************************************************/		
	function agregarKeyPress(Obj)
	{
		Ext.form.TextField.superclass.initEvents.call(Obj);
		if(Obj.validationEvent == 'keyup')
		{
			Obj.validationTask = new Ext.util.DelayedTask(Obj.validate, Obj);
			Obj.el.on('keyup', Obj.filterValidation, Obj);
		}
		else if(Obj.validationEvent !== false)
		{
			Obj.el.on(Obj.validationEvent, Obj.validate, Obj, {buffer: Obj.validationDelay});
		}
		if(Obj.selectOnFocus || Obj.emptyText)
		{
			Obj.on('focus', Obj.preFocus, Obj);
			if(Obj.emptyText)
			{
				Obj.on('blur', Obj.postBlur, Obj);
				Obj.applyEmptyText();
			}
		}
		if(Obj.maskRe || (Obj.vtype && Obj.disableKeyFilter !== true && (Obj.maskRe = Ext.form.VTypes[Obj.vtype+'Mask']))){
			Obj.el.on('keypress', Obj.filterKeys, Obj);
		}
		if(Obj.grow)
		{
			Obj.el.on('keyup', Obj.onKeyUp,  Obj, {buffer:50});
			Obj.el.on('click', Obj.autoSize,  Obj);
		}
		Obj.el.on('keyup', Obj.changeCheck, Obj);
	}
		
	
/*****************************************************************************
* @Función para validar que el registro seleccionado de
* @la grid del catalogo no exista en la grid del formulario
* @parametros:
* @retorno: true si el registro ya está.
* @fecha de creación: 13/12/2011.
* @autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*****************************************************************************/
	function validarExistenciaNivel()
	{
		AsigNivelCat    = gridAsigNivel.getSelectionModel().getSelections();
		cantAsigNivel   = gridAsigNivel.store.getCount()-1;
		arrAuxNomina = gridAsigNivel.store.getRange(0,cantAsigNivel);
		existe=false;
		totalgrid=(AsigNivelCat.length-1);
		totalcatalogo=(arrAuxAsigNivel.length-1);
		for (i=1; i<=totalgrid; i++)
		{
	  		auxReg1 = AsigNivelCat[i].get('codasiniv');
	  		for (j=0; j<=totalcatalogo; j++)
	  		{
				if (arrAuxAsigNivel[j].get('codasiniv')==auxReg1)
				{
					existe=true;
					return true;
				}
			}
		}		
		return existe;
	}


/****************************************************************************
* @Función para insertar el registro seleccionado
* @de la grid del catalgo a la grid del formulario.
* @fecha de creación: 13/12/2011.
* @autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*****************************************************************************/
	function pasarDatosGridCentro(datos)
	{
		p = new RecordDefAlm
		({
		'codasiniv':'',
		'codniv':'',
		'denominacion':''
		});
		gridNom.store.insert(0,p);
		p.set('codasiniv',datos.get('codasiniv'));
		p.set('codniv',datos.get('codniv'));
		p.set('despridoc',datos.get('despridoc'));
	}


/***********************************************************************************
* @Función que carga los usuarios del AsigNivel
* @parámetros: 
* @retorno: 
* @fecha de creación: 13/12/2011.
* @autor: Ing. Yesenia Moreno. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	function cargarUsuariosAsigNivel()
	{
		codasiniv = Ext.getCmp('txtcodasiniv').getValue();
		codniv = Ext.getCmp('txtcodniv').getValue();
		cmbsistema = Ext.getCmp('cmbsistema').getValue();
		var objdata ={
				'oper': 'catalogodetalle',
				'codasiniv': codasiniv,
				'codniv': codniv,
				'codtipniv': cmbsistema,
				'sistema': sistema,
				'vista': vista					
		};
		objdata=JSON.stringify(objdata);
		parametros = 'objdata='+objdata;
		Ext.Ajax.request({
			url : rutaPermisos,
			params : parametros,
			method: 'POST',
			success: function (resultado,request)
			{
				datos = resultado.responseText;
				if (datos!='')
				{
					var myObject = eval('(' + datos + ')');
					if(myObject.raiz[0].valido==true)
					{
						gridUsu.store.loadData(myObject);
					}
					else
					{
						Ext.MessageBox.alert('Error', myObject.raiz[0].mensaje+' Al cargar los usuarios.');
					}
				}
			}
		});
	}	
	
	
/*****************************************************************************
* @Función que busca el listado de personal.
* @parámetros: 	form: id del formulario, 
* 				fieldset: id del fieldset,
* 				array: arreglo con los campos del formulario
* 				arrValores: arreglo con los campos de la base de datos.
* @fecha de creación: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
******************************************************************************/
	function mostrarCatalogoAsigNivel(arrTxt, arrValores,codtipniv)
	{
		var objdata ={
			'oper': 'catalogo', 
			'sistema': sistema,
			'codtipniv': codtipniv,
			'vista': vista
		};
		objdata=JSON.stringify(objdata);
		parametros = 'objdata='+objdata;
		Ext.Ajax.request({
		url : rutaAsigNivel,
		params : parametros,
		method: 'POST',
		success: function ( resultado, request ) 
		{ 
			datos = resultado.responseText;
			if (datos!='')
			{
				var myObject = eval('(' + datos + ')');
				if (myObject.raiz[0].valido==true)
				{
					var RecordDef = Ext.data.Record.create([
					{name: 'codasiniv'},     
					{name: 'codniv'},     
					{name: 'despridoc'}
					]);
			      
			      	gridAsigNivel = new Ext.grid.GridPanel({
						width:500,
						autoScroll:true,
			            border:true,
			            ds: new Ext.data.Store({
						proxy: new Ext.data.MemoryProxy(myObject),
						reader: new Ext.data.JsonReader({
						    root: 'raiz',               
						    id: 'id'   
			                },
						RecordDef
						),
						data: myObject
			            }),
			            cm: new Ext.grid.ColumnModel([
			            new Ext.grid.CheckboxSelectionModel(),
							{header: 'Código', width: 30, sortable: true,   dataIndex: 'codasiniv'},
							{header: 'Nivel', width: 30, sortable: true,   dataIndex: 'codniv'},
			                {header: 'Denominación', width: 50, sortable: true, dataIndex: 'despridoc'},
						]),
						 sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
			            viewConfig: {
			                            forceFit:true
			                        },
						autoHeight:true,
						stripeRows: true
			                   });
					
					if (pantalla=='usuariosnivelaprobacion')
					{
						gridAsigNivel.getSelectionModel().singleSelect = true;	 
					}
					else 
					{
						gridAsigNivel.getSelectionModel().singleSelect = false;	
					}
					
			                  
					var panelAsigNivel = new Ext.FormPanel({
						labelWidth: 75, 
						frame:true,
						title: 'Búsqueda',
						bodyStyle:'padding:5px 5px 0',
						width: 350,
						height:120,
						defaults: {width: 230},
						defaultType: 'textfield',
						items: [{
							fieldLabel: 'Código',
							name: 'codasiniv',
							id:'codasiniv',
							width:50,
							changeCheck: function()
							{
								  var v = this.getValue();
								  actualizarDataAsigNivel('codasiniv',v);
								  if (String(v) !== String(this.startValue))
								  {
									  this.fireEvent('change', this, v, this.startValue);
								  } 
							},
							initEvents : function()
							{
								agregarKeyPress(this);
							}
						},{
							fieldLabel: 'Nivel',
							name: 'codniv',
							id:'codniv',
							width:50,
							changeCheck: function()
							{
								  var v = this.getValue();
								  actualizarDataAsigNivel('codniv',v);
								  if (String(v) !== String(this.startValue))
								  {
									  this.fireEvent('change', this, v, this.startValue);
								  } 
							},
							initEvents : function()
							{
								agregarKeyPress(this);
							}
						},{
							fieldLabel: 'Denominación',
							name: 'despridoc',
							id:'despridoc',
							changeCheck: function()
							{
								  var v = this.getValue();
								  actualizarDataAsigNivel('despridoc',v);
								  if (String(v) !== String(this.startValue))
								  {
									  this.fireEvent('change', this, v, this.startValue);
								  } 
							},
							initEvents : function()
							{
								agregarKeyPress(this);
							}
						}]
					});
						ventanaAsigNivel = new Ext.Window(
						{
							title: 'Cat&aacute;logo de Asignación de Niveles de aprobación',
					    	autoScroll:true,
			                width:500,
			                height:400,
			                modal: true,
			                closeAction:'hide',
			                plain: false,
			                items:[panelAsigNivel,gridAsigNivel],
			                buttons: [{
			                	text:'Aceptar',  
			                    handler: function()
								{                     	
									if (pantalla=='usuariosnivelaprobacion')
									{
										for (i=0;i<arrTxt.length;i++)
										{											
											Ext.getCmp(arrTxt[i]).setValue(gridAsigNivel.getSelectionModel().getSelected().get(arrValores[i]));
										}										
										cargarUsuariosAsigNivel();
									}
									else
									{
										if (validarExistenciaNivel()==true)
										{
											Ext.Msg.alert('Mensaje','Registro ya agregado');
										}											
										else
										{
											seleccionados = gridAsigNivel.getSelectionModel().getSelections();
											for (i=0; i<seleccionados.length; i++)
											{
												pasarDatosGridAlm(seleccionados[i]);
											}
										}
									}
									panelAsigNivel.destroy();
			                      	ventanaAsigNivel.destroy();									
								}
								},{
			                     text: 'Salir',
			                     handler: function()
			                     {
			                      	panelAsigNivel.destroy();
			                      	ventanaAsigNivel.destroy();
			                     }
							}]
						});
			        
					ventanaAsigNivel.show();
					if(!iniciargrid)
					{
						gridAsigNivel.render('miGrid');
			            iniciargrid=false;
			        }
		        }
			    else
			    {
					Ext.MessageBox.alert('Error', myObject.raiz[0].mensaje);
					close();
			    }
			}
			else
			{
				Ext.MessageBox.alert('Mensaje', 'No hay datos para mostrar');
			}		    
        },
        failure: function ( resultado, request)
		{ 
			Ext.MessageBox.alert('Error', resultado.responseText); 
        }
	   });
	};
