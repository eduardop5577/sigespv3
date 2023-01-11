/***********************************************************************************
* @Proceso para traspasar las obras
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var panel = '';
var pantalla = 'traspasosol';
var actualizar = false;
var rutaSol =  '../../controlador/apr/sigesp_ctr_apr_traspaso_obras.php'; 
var datosNuevo={'raiz':[{'codobr':'','codasi':'','codcon':'','desobr':'','feccon':''}]};
var codestpro1 = '';
var codestpro2 = '';
var codestpro3 = '';
var codestpro4 = '';
var codestpro5 = '';
var gridObras = '';
barraherramienta    = true;
Ext.onReady
(
	function()
	{
		Ext.QuickTips.init();
		Ext.Ajax.timeout=3600000;
		//componentes del formulario
		Xpos = ((screen.width/2)-(850/2)); 
		Ypos = ((screen.height/2)-(650/2));
		panel = new Ext.FormPanel({
			title: 'Transferir Obras',
			bodyStyle:'padding:5px 5px 0px',
			width:850,
			frame: true,
			tbar: [],
			style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',
			items:[{				
				xtype:'fieldset',
				title:'Criterio de Cambio',
				id:'fscriterio',				
				labelWidth:150, 
				autoHeight:true,
				autoWidth:true,
				cls :'fondo',
				items:[{	
					xtype:'textfield',
					name:'codestpro1',
					readOnly:true,
					width:200,
					id:'txtcodestpro1'
				},{
					xtype:'hidden',
					id:'hidestcla'	
				},{
					xtype:'button',
					id:'btnBuscarcodestpro1',
					handler: irBuscarEstructura1,
					iconCls: 'bmenubuscar',
					tooltip: 'Buscar estructura 1',
					style:'position:absolute;left:375px;top:28px',
					width:50
				},{
					xtype:'textfield',
					hideLabel:true,
					readOnly:true,
					id:'txtdencodestpro1',
					disabled:true,
					width:400,
					style:'position:absolute;left:390px;top:-35px'		
				},{
					xtype:'textfield',
					hideLabel:true,					
					readOnly:true,
					id:'txtdencodestpro2',
					disabled:true,
					width:400,
					style:'position:absolute;left:390px;top:5px'		
				},{
					xtype:'textfield',
					name:'codestpro2',
					readOnly:true,
					width: 200,
					id:'txtcodestpro2'					
				},{
					xtype:'button',
					id:'btnBuscarcodestpro2',
					handler: irBuscarEstructura2,
					iconCls: 'bmenubuscar',
					tooltip: 'Buscar estructura 2',
					style:'position:absolute;left:375px;margin-top:-25px',
					width:50			
				},{
					xtype:'textfield',
					name:'codestpro3',
					readOnly:true,
					width:200,
					id:'txtcodestpro3'	
				},{				
					xtype:'button',
					id:'btnBuscarcodestpro3',
					handler: irBuscarEstructura3,
					iconCls: 'bmenubuscar',
					tooltip: 'Buscar estructura 3',
					style:'position:absolute;left:375px;margin-top:-25px',
					width:50			

				},{
					xtype:'textfield',
					hideLabel:true,
					readOnly:true,
					id:'txtdencodestpro3',
					disabled:true,
					width:400,
					style:'position:absolute;left:390px;top:-25px'		
				},{
				
					xtype:'textfield',
					name:'codestpro4',
					readOnly:true,
					width:200,
					id:'txtcodestpro4'	
				},{
					xtype:'button',
					id:'btnBuscarcodestpro4',
					handler: irBuscarEstructura4,
					iconCls: 'bmenubuscar',
					tooltip: 'Buscar estructura 4',
					style:'position:absolute;left:375px;margin-top:-25px',
					width:50	
				},{
					xtype:'textfield',
					hideLabel:true,
					readOnly:true,
					id:'txtdencodestpro4',
					disabled:true,
					width:400,
					style:'position:absolute;left:390px;top:-25px'				
				},{
					xtype:'textfield',
					name:'codestpro5',
					readOnly:true,
					width:200,
					id:'txtcodestpro5'
				},{
					xtype:'button',
					id:'btnBuscarcodestpro5',					
					handler: irBuscarEstructura5,
					iconCls: 'bmenubuscar',
					tooltip: 'Buscar estructura 5',
					style:'position:absolute;left:375px;margin-top:-25px',
					width:50
				},{
					xtype:'textfield',
					hideLabel:true,
					readOnly:true,
					id:'txtdencodestpro5',
					disabled:true,
					width:400,
					style:'position:absolute;left:390px;top:-25px'	
				},{
					xtype:'textfield',
					fieldLabel:'Cuenta',					 
					name:'Cuenta',
					readOnly:true,
					width:150,
					id:'txtcuenta'
				},{
					xtype:'button',
					id:'btnBuscarCuenta',
					handler: irBuscarCuenta,
					iconCls: 'bmenubuscar',
					tooltip: 'Buscar Denominación de la Cuenta',
					style:'position:absolute;left:325px;margin-top:-25px',
					width:50	
				},{
					xtype:'textfield',
					hideLabel:true,
					name:'Denominación',
					readOnly:true,
					id:'txtdencuenta',
					disabled:true,
					width:350,
					style:'position:absolute;left:340px;top:-25px'
				},{	
						xtype:'datefield',
						fieldLabel:'Fecha Comprobante',
						name:'Fecha ',
						readOnly:true,
						id:'txtfecha'	
				}]
			},{	
				xtype:'fieldset',
				title:'Criterio de Busqueda',
				id:'fsbusqueda',
				layout:'column',
				autoHeight:true,
				autoWidth:true,
				cls :'fondo',
				itemCls: 'fondo',		
				items:[{
					columnWidth:.3,
					layout: 'form',
					labelWidth:100,
					border:false,					
					items: [{	
						xtype:'datefield',
						fieldLabel:'Fecha Desde',
						name:'Fecha Desde',
						readOnly:true,
						id:'txtfecdesde'
					}]	
				},{
					columnWidth:.3,
					layout: 'form',
					labelWidth:100,
					border:false,					
					items: [{
						xtype:'datefield',
						fieldLabel:'Fecha Hasta',
						name:'Fecha Hasta',
						readOnly:true,
						id:'txtfechasta'	
				
					}]	
				}]
			},{
				buttons:[{
					text: 'Buscar',
					handler: irBuscar
				}]
			},{	
				xtype:'panel',
				autoScroll:true,
				height: 150,
				width:800,
				title:'Obras',
				contentEl:'grid-obras'				
			}]
		});
		panel.render(document.body);
		obtenergridObras();			
		verificarEstructuras();			
	
})

/**************************************************************************************
* @Función para verificar los niveles de las estructuras presupuestarias de la empresa.
* @parametros: 
* @retorno: 
* @fecha de creación: 26/11/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***************************************************************************************/	
	function verificarEstructuras()
	{
		var objdata ={
			'operacion': 'verificarEstructuras',
			'sistema': sistema,
			'vista': vista
		};
		objdata=Ext.util.JSON.encode(objdata);		
		parametros = 'objdata='+objdata; 
		Ext.Ajax.request({
			url : rutaSol,
			params : parametros,
			method: 'POST',
			success: function (resultad,request)
			{
				datos = resultad.responseText;
				var datajson = Ext.util.JSON.decode(datos);
				//alert(datajson.raiz);
				if (datajson.raiz!=null)
				{
					if (datajson.raiz.nivel1!='-')
					{						
        				var label1 = Ext.DomQuery.select('label[for="txtcodestpro1"]');
        				Ext.DomHelper.overwrite(label1[0],datajson.raiz.nivel1+':');
					}
					if (datajson.raiz.nivel2!='-')
					{
						var label2 = Ext.DomQuery.select('label[for="txtcodestpro2"]');
						Ext.DomHelper.overwrite(label2[0],datajson.raiz.nivel2+':');
					}
					if (datajson.raiz.nivel3!='-')
					{
						var label3 = Ext.DomQuery.select('label[for="txtcodestpro3"]');
						Ext.DomHelper.overwrite(label3[0],datajson.raiz.nivel3+':');
					}					
					if (datajson.raiz.nivel4=='' || datajson.raiz.nivel4=='-') //preguntar por el valor por defecto
					{
						Ext.getCmp('txtdencodestpro4').hide();
						Ext.getCmp('btnBuscarcodestpro4').hide();
						var label4 = Ext.DomQuery.select('label[for="txtcodestpro4"]');
 						Ext.DomHelper.overwrite(label4[0],'');	
                        Ext.getCmp('txtcodestpro4').hide();                       
					}
					else
					{
						var label4 = Ext.DomQuery.select('label[for="txtcodestpro4"]');
						Ext.DomHelper.overwrite(label4[0],datajson.raiz.nivel4+':');
					}
					if (datajson.raiz.nivel5=='' || datajson.raiz.nivel5=='-')
					{						
						Ext.getCmp('txtdencodestpro5').hide();
						Ext.getCmp('btnBuscarcodestpro5').hide();
						//obtener la etiqueta del elemento						
						var label5 = Ext.DomQuery.select('label[for="txtcodestpro5"]'); 
						//para quitar los :
 						Ext.DomHelper.overwrite(label5[0],'');	
                        Ext.getCmp('txtcodestpro5').hide(); 
					}
					else
					{
						var label5 = Ext.DomQuery.select('label[for="txtcodestpro5"]'); 
						Ext.DomHelper.overwrite(label5[0],datajson.raiz.nivel5+':');
					}					
				}
			},
			failure: function ( resultad, request)
			{ 
				Ext.MessageBox.alert('Error', 'No se logró procesar la información'); 
			}
		});	 
	}
	
	function obtenergridObras()
	{
		RecordDef = Ext.data.Record.create
		([
			{name: 'codobr'}, 
			{name: 'codasi'}, 
			{name: 'codcon'},
			{name: 'desobr'},
			{name: 'feccon'}
		]);
		dsconcepto =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(datosNuevo),
		reader: new Ext.data.JsonReader({
			root: 'raiz',               
			id: 'id'   
			},
			 RecordDef
			),
			data: datosNuevo
			});
		
		gridObras = new Ext.grid.GridPanel({
			width:800,
			height: 300,
			autoScroll:true,
			border:true,
			ds: dsconcepto,
			cm: new Ext.grid.ColumnModel([
			  new Ext.grid.CheckboxSelectionModel(),
				{header: 'Obra', width: 100, sortable: true,   dataIndex: 'codobr'},
				{header: 'Asignacion', width: 100, sortable: true,   dataIndex: 'codasi'},
				{header: 'Contrato', width: 100, sortable: true, dataIndex: 'codcon'},
				{header: 'Descripcion de Obra', width: 300, sortable: true, dataIndex: 'desobr'},
				{header: 'Fecha Contrato', width: 100, sortable: true, dataIndex: 'feccon'}
			]),
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
			viewConfig: {
							forceFit:true
						},
			autoHeight:true,
			stripeRows: true
		});
		gridObras.render('grid-obras');
	}


function irBuscarEstructura1()
	{
		var arreglotxt = new Array('txtcodestpro1','txtdencodestpro1','hidestcla');		
		var arreglovalores = new Array('codestpro1','denestpro1','estcla');		
		objCatEst1 = new catalogoEstructura1();
		objCatEst1.mostrarCatalogoEstructura1(arreglotxt, arreglovalores);
	
	}

	function irBuscarEstructura2()
	{
		if (Ext.getCmp('txtcodestpro1').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Seleccione la Estructura del Nivel Anterior');
		}
		else
		{
			codestpro1 = Ext.getCmp('txtcodestpro1').getValue();
			denestpro1 = Ext.getCmp('txtdencodestpro1').getValue();
			estcla     = Ext.getCmp('hidestcla').getValue();
			var arreglotxt = new Array('txtcodestpro2','txtdencodestpro2');		
			var arreglovalores = new Array('codestpro2','denestpro2');				
			objCatEst2 = new catalogoEstructura2();
			objCatEst2.mostrarCatalogoEstructura2(arreglotxt, arreglovalores,denestpro1);
		}	
	}

	function irBuscarEstructura3()
	{
		if (Ext.getCmp('txtcodestpro2').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Seleccione la Estructura del Nivel Anterior');
		}
		else
		{
			codestpro1 = Ext.getCmp('txtcodestpro1').getValue();
			codestpro2 = Ext.getCmp('txtcodestpro2').getValue();
			estcla     = Ext.getCmp('hidestcla').getValue();
			denestpro1 = Ext.getCmp('txtdencodestpro1').getValue();
			denestpro2 = Ext.getCmp('txtdencodestpro2').getValue();
			var arreglotxt = new Array('txtcodestpro3','txtdencodestpro3');		
			var arreglovalores = new Array('codestpro3','denestpro3');				
			objCatEst3 = new catalogoEstructura3();
			objCatEst3.mostrarCatalogoEstructura3(arreglotxt, arreglovalores,denestpro1,denestpro2);
		}
	}

	function irBuscarEstructura4()
	{
		if (Ext.getCmp('txtcodestpro3').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Seleccione la Estructura del Nivel Anterior');
		}
		else
		{
			codestpro1 = Ext.getCmp('txtcodestpro1').getValue();
			codestpro2 = Ext.getCmp('txtcodestpro2').getValue();
			codestpro3 = Ext.getCmp('txtcodestpro3').getValue();
			estcla     = Ext.getCmp('hidestcla').getValue();
			denestpro1 = Ext.getCmp('txtdencodestpro1').getValue();
			denestpro2 = Ext.getCmp('txtdencodestpro2').getValue();
			denestpro3 = Ext.getCmp('txtdencodestpro3').getValue();
			var arreglotxt = new Array('txtcodestpro4','txtdencodestpro4');		
			var arreglovalores = new Array('codestpro4','denestpro4');				
			objCatEst4 = new catalogoEstructura4();
			objCatEst4.mostrarCatalogoEstructura4(arreglotxt, arreglovalores,denestpro1,denestpro2,denestpro3);
		}	
	}

	function irBuscarEstructura5()
	{
		if (Ext.getCmp('txtcodestpro4').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Seleccione la Estructura del Nivel Anterior');
		}
		else
		{
			codestpro1 = Ext.getCmp('txtcodestpro1').getValue();
			codestpro2 = Ext.getCmp('txtcodestpro2').getValue();
			codestpro3 = Ext.getCmp('txtcodestpro3').getValue();
			codestpro4 = Ext.getCmp('txtcodestpro4').getValue();
			estcla     = Ext.getCmp('hidestcla').getValue();
			denestpro1 = Ext.getCmp('txtdencodestpro1').getValue();
			denestpro2 = Ext.getCmp('txtdencodestpro2').getValue();
			denestpro3 = Ext.getCmp('txtdencodestpro3').getValue();
			denestpro4 = Ext.getCmp('txtdencodestpro4').getValue();
			var arreglotxt = new Array('txtcodestpro5','txtdencodestpro5');		
			var arreglovalores = new Array('codestpro5','denestpro5');				
			objCatEst5 = new catalogoEstPre();
			objCatEst5.mostrarCatalogoEstPre(arreglotxt, arreglovalores,denestpro1,denestpro2,denestpro3,denestpro4);
		}	
	}

	function irBuscarCuenta()
	{
		if (Ext.getCmp('txtcodestpro1').getValue()=='' && Ext.getCmp('txtcodestpro2').getValue()=='' && Ext.getCmp('txtcodestpro3').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Seleccione la Estructura Presupuestaria');
		}
		else
		{
			codestpro1 = Ext.getCmp('txtcodestpro1').getValue();
			codestpro2 = Ext.getCmp('txtcodestpro2').getValue();
			codestpro3 = Ext.getCmp('txtcodestpro3').getValue();
			codestpro4 = Ext.getCmp('txtcodestpro4').getValue();
			codestpro5 = Ext.getCmp('txtcodestpro5').getValue();
			estcla     = Ext.getCmp('hidestcla').getValue();
			var arreglotxt = new Array('txtcuenta','txtdencuenta');		
			var arreglovalores = new Array('spg_cuenta','denominacion');				
			objCatCuenta = new catalogoCuenta();
			objCatCuenta.mostrarCatalogo(arreglotxt, arreglovalores);
		}	
	}
	
	function irBuscar()
	{
		if (Ext.getCmp('txtfecdesde').getValue()=='' || Ext.getCmp('txtfechasta').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Debe indicar el periodo de búsqueda');
		}
		else
		{
			var objdata ={
				'operacion': 'buscar',
				'fecdesde': Ext.get('txtfecdesde').getValue(),
				'fechasta': Ext.get('txtfechasta').getValue(),
				'sistema': sistema,
				'vista': vista
			};
			objdata=Ext.util.JSON.encode(objdata);		
			parametros = 'objdata='+objdata;
			Ext.Ajax.request({
				url : rutaSol,
				params : parametros,
				method: 'POST',
				success: function (resultad,request)
				{
					datos = resultad.responseText;
					var datajson = Ext.util.JSON.decode(datos);		
					if(datajson.raiz[0].valido==false)
					{
						Ext.Msg.alert('Mensaje', datajson.raiz[0].mensaje+' Al cargar las solicitudes.');
					}
					else
					{
						gridObras.store.loadData(datajson);
					}
				},
				failure: function ( resultad, request)
				{ 
					Ext.Msg.alert('Error', 'No se logró procesar la información'); 
				}
			});	
		}	
	}

	
/***********************************************************************************
* @Función para realizar la transferencia de Obras.
* @parametros: 
* @retorno: 
* @fecha de creación: 14/12/2015 
* @autor: Ing. Luis Anibal Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	function irProcesar() //destino,valores,donde
	{
		 fecha = Ext.getCmp('txtfecha').getValue().format('Y-m-d');
		if(fecha!="")
		{
			codestpro4="";
			codestpro5="";
			obtenerMensaje('procesar','','Transfiriendo Datos');
			if ( Ext.get('txtcodestpro4').getValue() !=''&& Ext.get('txtcodestpro4').getValue()!='') 
			{
				codestpro4 = validarObjetos('txtcodestpro4','25','rellenar');
				codestpro5 = validarObjetos('txtcodestpro5','25','rellenar');
			}
				arrSel = gridObras.getSelectionModel().getSelections();
				total = arrSel.length;			
				
				var objdata = "{'operacion': 'procesar','cuenta':'"+Ext.getCmp('txtcuenta').getValue()+
									"','fecdesde': '"+Ext.get('txtfecdesde').getValue()+
									"','fechasta': '"+Ext.get('txtfechasta').getValue()+
									"','codestpro1': '"+Ext.getCmp('txtcodestpro1').getValue()+
									"','codestpro2': '"+Ext.getCmp('txtcodestpro2').getValue()+
									"','codestpro3': '"+Ext.getCmp('txtcodestpro3').getValue()+
									"','codestpro4': '"+codestpro4+"','codestpro5': '"+codestpro5+
									"','estcla': '"+Ext.getCmp('hidestcla').getValue()+
									"','sistema': '"+sistema+"','vista': '"+vista+"','fecha': '"+fecha+"' ";				
				objdata=objdata+ ",datosObr:[";			
				if (total>0)
				{					
					for (i=0; i < total; i++)
					{
						if (i==0)
						{
							objdata = objdata +"{'codobr':'"+ arrSel[i].get('codobr')+"'}";
						}
						else
						{
							objdata = objdata +",{'codobr':'"+ arrSel[i].get('codobr')+ "'}";
						}
					}			
				
					
					objdata = objdata + ']}';
					objdata = Ext.util.JSON.decode(objdata);
					objdata = Ext.util.JSON.encode(objdata);	
					obtenerMensaje('procesar','','Transfiriendo Datos');				
					parametros = 'objdata='+objdata;
					Ext.Ajax.request({
						url : rutaSol,
						params : parametros,
						method: 'POST',
						success: function (resultado,request)
						{
							datos = resultado.responseText;
							Ext.Msg.hide();
							var datajson = Ext.util.JSON.decode(datos);	
							if(datajson.raiz.valido==true)
							{
								Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje+' Obras Transferidas ');
								//IrCancelar(); 
							}
							else
							{
								Ext.Msg.alert('Error', datajson.raiz.mensaje);
							}
						},
						failure: function ( resultad, request)
						{ 
							Ext.Msg.hide();
							Ext.Msg.alert('Error', 'No se logró procesar la información'); 
						}
					});	
				}
				else
				{
					Ext.Msg.alert('Mensaje','Debe seleccionar al menos una Obra');
				}		
				
		}
		else
		{
			alert("Debe Indicar una fecha para el comprobante");	
		}
	}


	function irCancelar()
	{
	}

	function irEliminar()
	{
	}

	function irDescargar()
	{
		objCatDescarga = new catalogoDescarga();
		objCatDescarga.mostrarCatalogo();
	}


