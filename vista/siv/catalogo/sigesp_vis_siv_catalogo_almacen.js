/**************************************************************************
*@archivo javascript para el cat�logo de almacen
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var datos              = null;
var gridAlmacen         = null;
var ventanaAlmacen      = null;
var iniciargrid        = false;
var parametros         = '';
var rutaAlmacen         = '../../controlador/siv/sigesp_ctr_siv_almacen.php';
var rutaPermisos = '../../controlador/sss/sigesp_ctr_sss_usuariospermisos.php';


/******************************************************************************
* @Funci�n gen�rica para el uso del cat�logo de almacen
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
*******************************************************************************/
	function catalogoAlmacen()
	{	
		this.mostrarCatalogoAlmacen = mostrarCatalogoAlmacen;
	}


/*************************************************************************
* @Funci�n que acualiza el catalgo para buscar por determinado campo
* @parametros: criterio: campo por el que se actualiza
* 				cadena: campo a actualizar
* @retorno:
* @fecha de creaci�n: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
**************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
**************************************************************************/
	function actualizarDataAlmacen(criterio,cadena)
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
		url : rutaAlmacen,
		params : parametros,
		method: 'POST',
		success: function ( resultado, request )
		{ 
			datos = resultado.responseText;
			if (datos!='')
			{
				var DatosNuevo = eval('(' + datos + ')');
				gridAlmacen.store.loadData(DatosNuevo);
			}
		}
		});
	}
	
	
/***********************************************************************
* Obtener el valor de los caracteres de la caja texto
* @par�metros: obj --> caja de texto.
* @retorna: valor obtenido del objeto.
* @fecha de creaci�n: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
**************************************************************************
* @fecha modificaci�n:
* @descripci�n:
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
* @Funci�n para validar que el registro seleccionado de
* @la grid del catalogo no exista en la grid del formulario
* @parametros:
* @retorno: true si el registro ya est�.
* @fecha de creaci�n: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
*****************************************************************************/
	function validarExistenciaAlm()
	{
		AlmacenCat    = gridAlmacen.getSelectionModel().getSelections();
		cantAlmacen   = gridAlmacen.store.getCount()-1;
		arrAuxAlmacen = gridAlmacen.store.getRange(0,cantAlmacen);
		existe=false;
		totalgrid=(AlmacenCat.length-1);
		totalcatalogo=(arrAuxAlmacen.length-1);
		for (i=1; i<=totalgrid; i++)
		{
	  		auxReg1 = AlmacenCat[i].get('codalm');
	  		for (j=0; j<=totalcatalogo; j++)
	  		{
				if (arrAuxAlmacen[j].get('codalm')==auxReg1)
				{
					existe=true;
					return true;
				}
			}
		}		
		return existe;
	}


/****************************************************************************
* @Funci�n para insertar el registro seleccionado
* @de la grid del catalgo a la grid del formulario.
* @fecha de creaci�n: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
*****************************************************************************/
	function pasarDatosGridAlm(datos)
	{
		p = new RecordDefAlm
		({
		'codalm':'',
		'nomfisalm':''
		});
		gridAlm.store.insert(0,p);
		p.set('codalm',datos.get('codalm'));
		p.set('nomfisalm',datos.get('nomfisalm'));
	}


/***********************************************************************************
* @Funci�n que carga los usuarios del Almacen
* @par�metros: 
* @retorno: 
* @fecha de creaci�n: 02/12/2011.
* @autor: Ing. Yesenia Moreno. 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/	
	function cargarUsuariosAlmacen()
	{
		codalm = Ext.getCmp('txtcodalm').getValue();
		var objdata ={
				'oper': 'catalogodetalle',
				'codtippersss': codalm,
				'codsis': 'SIV',
				'campo': 'codalm',
				'tabla': 'siv_almacen',
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
* @Funci�n que busca el listado de personal.
* @par�metros: 	form: id del formulario, 
* 				fieldset: id del fieldset,
* 				array: arreglo con los campos del formulario
* 				arrValores: arreglo con los campos de la base de datos.
* @fecha de creaci�n: 02/12/2011.
* @autor: Ing. Yesenia Moreno.
*****************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
******************************************************************************/
	function mostrarCatalogoAlmacen(arrTxt, arrValores)
	{
		var objdata ={
			'oper': 'catalogo', 
			'sistema': sistema,
			'vista': vista
		};
		objdata=JSON.stringify(objdata);
		parametros = 'objdata='+objdata;
		Ext.Ajax.request({
		url : rutaAlmacen,
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
					{name: 'codalm'},     
					{name: 'nomfisalm'}
					]);
			      
			      	gridAlmacen = new Ext.grid.GridPanel({
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
							{header: 'C�digo', width: 30, sortable: true,   dataIndex: 'codalm'},
			                {header: 'Nombre', width: 50, sortable: true, dataIndex: 'nomfisalm'},
						]),
						 sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
			            viewConfig: {
			                            forceFit:true
			                        },
						autoHeight:true,
						stripeRows: true
			                   });
					
					if (pantalla=='usuariosalmacen')
					{
						gridAlmacen.getSelectionModel().singleSelect = true;	 
					}
					else 
					{
						gridAlmacen.getSelectionModel().singleSelect = false;	
					}
					
			                  
					var panelAlmacen = new Ext.FormPanel({
						labelWidth: 75, 
						frame:true,
						title: 'B�squeda',
						bodyStyle:'padding:5px 5px 0',
						width: 350,
						height:120,
						defaults: {width: 230},
						defaultType: 'textfield',
						items: [{
							fieldLabel: 'C�digo',
							name: 'codalm',
							id:'codalm',
							width:50,
							changeCheck: function()
							{
								  var v = this.getValue();
								  actualizarDataAlmacen('codalm',v);
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
							fieldLabel: 'Nombre',
							name: 'nomfisalm',
							id:'nomfisalm',
							changeCheck: function()
							{
								  var v = this.getValue();
								  actualizarDataAlmacen('nomfisalm',v);
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
						ventanaAlmacen = new Ext.Window(
						{
							title: 'Cat&aacute;logo de Almacen',
					    	autoScroll:true,
			                width:500,
			                height:400,
			                modal: true,
			                closeAction:'hide',
			                plain: false,
			                items:[panelAlmacen,gridAlmacen],
			                buttons: [{
			                	text:'Aceptar',  
			                    handler: function()
								{                     	
									if (pantalla=='usuariosalmacen')
									{
										for (i=0;i<arrTxt.length;i++)
										{											
											Ext.getCmp(arrTxt[i]).setValue(gridAlmacen.getSelectionModel().getSelected().get(arrValores[i]));
										}										
										cargarUsuariosAlmacen();
									}
									else
									{
										if (validarExistenciaAlm()==true)
										{
											Ext.Msg.alert('Mensaje','Registro ya agregado');
										}											
										else
										{
											seleccionados = gridAlmacen.getSelectionModel().getSelections();
											for (i=0; i<seleccionados.length; i++)
											{
												pasarDatosGridAlm(seleccionados[i]);
											}
										}
									}
									panelAlmacen.destroy();
			                      	ventanaAlmacen.destroy();									
								}
								},{
			                     text: 'Salir',
			                     handler: function()
			                     {
			                      	panelAlmacen.destroy();
			                      	ventanaAlmacen.destroy();
			                     }
							}]
						});
			        
					ventanaAlmacen.show();
					if(!iniciargrid)
					{
						gridAlmacen.render('miGrid');
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
