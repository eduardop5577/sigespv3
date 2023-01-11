/***********************************************************************************
* @Proceso para traspasar los datos Básicos de una Base de Datos a Otra
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
var actualizar = false;
ruta =  '../../controlador/apr/sigesp_ctr_apr_datos_basicos.php'; 
var sistemas = new Array();
var sno;
var nominapanel;
var dataNomina;
var gridNominas;
var aperturado = new Array();
barraherramienta    = true;
sistemas[0]='v2';
sistemas[1]='conversion';
sistemas[2]='bsf';
sistemas[3]='sss';
sistemas[4]='rpc';
sistemas[5]='scg';
sistemas[6]='spg';
sistemas[7]='spi';
sistemas[8]='saf';
sistemas[9]='cxp';
sistemas[10]='siv';
sistemas[11]='sep';
sistemas[12]='soc';
sistemas[13]='scb';
sistemas[14]='scv';
sistemas[15]='sob';
sistemas[16]='sno';
sistemas[17]='srh';
sistemas[18]='his';
sistemas[19]='reiniciar';
aperturado['sss']=0;
aperturado['rpc']=0;
aperturado['scg']=0;
aperturado['spg']=0;
aperturado['spi']=0;
aperturado['saf']=0;
aperturado['cxp']=0;
aperturado['siv']=0;
aperturado['sep']=0;
aperturado['soc']=0;
aperturado['scb']=0;
aperturado['scv']=0;
aperturado['sob']=0;
aperturado['sno']=0;
aperturado['srh']=0;
aperturado['his']=0;
aperturado['bsf']=0;
aperturado['conversion']=0;
aperturado['v2']=0;
aperturado['reiniciar']=0;

Ext.onReady
(
	function()
	{
		Ext.QuickTips.init();
		Ext.Ajax.timeout=36000000000;

		// turn on validation errors beside the field globally
		//Ext.form.Field.prototype.msgTarget = 'side';

	// verificar si se reinicia el password
		var reiniciar = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Reiniciar Password Usuarios',
			labelStyle: 'width:250px',
			checked:true,
			name:'reiniciar',
			id:'chbreiniciar'		
		});

		// verificar si es conversion v2
		var v2 = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Apertura de V2',
			labelStyle: 'width:250px',
			checked:true,
			name:'v2',
			id:'chbv2'		
		});

		// verificar si es conversion bsf
		var bsf = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Conversión de BSF',
			labelStyle: 'width:250px',
			name:'bsf',
			id:'chbbsf'		
		});
		// verificar si es apertura ó conversión
		var conversion = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Conversión de Datos',
			labelStyle: 'width:250px',
			name:'conversion',
			id:'chbconversion'		
		});
		// Módulo de seguridad
		var sss = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Datos Básicos',
			labelStyle: 'width:250px',
			name:'seguridad',
			id:'chbsss'		
		});
		// Módulo de Proveedores y Beneficiarios
		var rpc = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Proveedores y Beneficiarios',
			labelStyle: 'width:250px',
			name:'proveedores',
			id:'chbrpc'		
		});		
		// Contabilidad General
		var scg = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Contabilidad General',
			labelStyle: 'width:250px',
			name:'contabilidad',
			id:'chbscg'		
		});
		// Presupuesto de Gasto
		var spg = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Presupuesto de Gasto',
			labelStyle: 'width:250px',
			name:'gasto',
			id:'chbspg'		
		});
		// Presupuesto de Ingreso
		var spi = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Presupuesto de Ingreso',
			labelStyle: 'width:250px',
			name:'ingreso',
			id:'chbspi'		
		});
		// Activos Fijos
		var saf = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Activos Fijos',
			labelStyle: 'width:250px',
			name:'activos',
			id:'chbsaf'		
		});
		// Cuentas por Pagar
		var cxp = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Cuentas por Pagar',
			labelStyle: 'width:250px',
			name:'cuentasporpagar',
			id:'chbcxp'		
		});		
		// Inventario
		var siv = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Inventario',
			labelStyle: 'width:250px',
			name:'inventario',
			id:'chbsiv'		
		});		
		// Solicitud de Ejecución Presupuestaria
		var sep = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Solicitud de Ejecución Presupuestaria',
			labelStyle: 'width:250px',
			name:'solicitud',
			id:'chbsep'		
		});
		// Compras
		var soc = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Compras',
			labelStyle: 'width:250px',
			name:'compras',
			id:'chbsoc'		
		});
		// Bancos
		var scb = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Bancos',
			labelStyle: 'width:250px',
			name:'banco',
			id:'chbscb'		
		});
		// Control de Viaticos
		var scv = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Control de Viaticos',
			labelStyle: 'width:250px',
			name:'viaticos',
			id:'chbscv'		
		});
		// Obras
		var sob = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Obras',
			labelStyle: 'width:250px',
			name:'obras',
			id:'chbsob'		
		});		
		// Recursos Humanos
		var srh = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Recursos Humanos',
			labelStyle: 'width:250px',
			name:'recursoshumanos',
			id:'chbsrh'		
		});		
		// Nómina
		sno = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Nómina',
			labelStyle: 'width:250px',
			name:'nomina',
			id:'chbsno'		
		});		
		
		his = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Historicos de Nómina',
			labelStyle: 'width:250px',
			name:'historico',
			id:'chbhis'		
		});		

		ObjNominas={'raiz':[{'codemp':'','codnom':'','desnom':'','codnuenom':'','transferir':'0'}]};

		//componentes del formulario
		Xpos = ((screen.width/2)-(650/2)); 
		Ypos = ((screen.height/2)-(790/2))+20;
		panel = new Ext.FormPanel({
			title: 'Trasferir Datos Básicos',
			bodyStyle:'padding:5px 5px 0px',
			width:600,
			style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',
			tbar: [],
			items:[{
				xtype:'fieldset',
				title:'',
				id:'fsformtransferir',
				autoHeight:true,
				autoWidth:true,
				cls :'fondo',		
				items:[conversion,bsf,v2,reiniciar,sss,rpc,scg,spg,spi,saf,cxp,siv,sep,soc,scb,scv,sob,sno,srh,his]
			}]
		});
	panel.render(document.body);
	verificarApertura();
	obtenerDatosNominas();
	sno.addListener('check',activarGrid);
})


/***********************************************************************************
* @Función para llenar el combo de las empresas.     
* @parametros: 
* @retorno:
* @fecha de creación: 01/08/2008.
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function obtenerDatosNominas()
	{		
		var objdata ={
			'operacion': 'obtenerDatosNomina',
			'codsis': 'APR',
			'sistema': sistema,
			'vista': vista
			};
		
		objdata=Ext.util.JSON.encode(objdata);
		
		parametros = 'objdata='+objdata; 
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado,request)
			{
				datos = resultado.responseText;
				var objresultado = eval('(' + datos + ')');
				if (objresultado.raiz[0].valido==true)
				{
					anioactual=objresultado.raiz[0].anioactual;
					ObjNominas = objresultado;
				}
				else
				{
					Ext.MessageBox.alert('Error', objresultado.raiz[0].mensaje); 
				}
			},
			failure: function ( resultado, request)
			{ 
				Ext.MessageBox.alert('Error', resultado.responseText); 
			}
		});	
	}
	

/***********************************************************************************
* @Función para limpiar todos los campos del formulario  
* @parametros: 
* @retorno:
* @fecha de creación: 20/10/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function irCancelar()
	{
		total = sistemas.length;
		valido = true;
		for (contador = 0; ((contador < total) && valido); contador++)
		{
			codsis=sistemas[contador];
			if(aperturado[codsis]==0)
			{
				eval("Ext.getCmp('chb"+codsis+"').setValue('0')");
			}
		}
	}


/***********************************************************************************
* @Función para verificar a que módulos se les realizó la apertura.
* @parametros: 
* @retorno:
* @fecha de creación: 20/10/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function verificarApertura()
	{
		var objdata ={
			'operacion': 'verificarapertura',
			'codsis': 'APR',
			'sistema': sistema,
			'vista': vista
			};
		
		objdata=Ext.util.JSON.encode(objdata);
		
		parametros = 'objdata='+objdata; 
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado,request)
			{
				datos = resultado.responseText;
				var objresultado = eval('(' + datos + ')');
				if (objresultado.raiz[0].valido==true)
				{
					total = objresultado.raiz.length;
					for (cont=0; cont<total; cont++) 
					{
						if(objresultado.raiz[cont].codsis=='SNR')
						{
							objresultado.raiz[cont].codsis='HIS';
						}
						if(objresultado.raiz[cont].codsis!='')
						{
							codsis = objresultado.raiz[cont].codsis;
							codsis = codsis.toLowerCase();
							eval("Ext.getCmp('chb"+codsis+"').setValue('1')");
							eval("Ext.getCmp('chb"+codsis+"').disable()");
							aperturado[codsis]='1';
						}
					}
				}
				else
				{
					Ext.MessageBox.alert('Error', objresultado.raiz[0].mensaje); 
				}
			},
			failure: function ( resultado, request)
			{ 
				Ext.MessageBox.alert('Error', resultado.responseText); 
			}
		});	
	}


/***********************************************************************************
* @Función para procesar la apertura.
* @parametros: 
* @retorno:
* @fecha de creación: 20/10/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function irProcesar()
	{
		total = sistemas.length;
		valido = true;
		conversion = Ext.getCmp('chbconversion').getValue();
		for (contador = 0; ((contador < total) && valido); contador++)
		{
			codsis=sistemas[contador];
			if(aperturado[codsis]==0)
			{
				if((eval("Ext.getCmp('chb"+codsis+"').getValue()")=='1')&&(codsis!='conversion')&&(codsis!='bsf')&&(codsis!='v2')&&(codsis!='reiniciar'))
				{
					if (codsis == 'sno')
					{
						valido = procesarAperturaNomina(codsis);
					}
					else
					{
						valido = procesarApertura(codsis);
					}
				}
			}
		}
	}


/***********************************************************************************
* @Función para procesar la apertura.
* @parametros: 
* @retorno:
* @fecha de creación: 20/10/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function procesarApertura(codsis)
	{
		obtenerMensaje('procesar','','Procesando Datos');
		valido=false;
		conversion = Ext.getCmp('chbconversion').getValue();
		bsf = Ext.getCmp('chbbsf').getValue();
		v2 = Ext.getCmp('chbv2').getValue();
		reiniciar = Ext.getCmp('chbreiniciar').getValue();
		var objdata ={
			'operacion': 'procesar',
			'codsis': codsis,
			'conversion': conversion,			
			'bsf': bsf,			
			'v2': v2,			
			'reiniciar': reiniciar,			
			'sistema': sistema,
			'vista': vista
			};
		objdata=Ext.util.JSON.encode(objdata);
		parametros = 'objdata='+objdata; 
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado,request)
			{
				datos = resultado.responseText;
				Ext.Msg.hide();
				Ext.MessageBox.alert('Mensaje', datos); 
				var objresultado = eval('(' + datos + ')');
				if (objresultado.raiz.valido==true)
				{
					valido = true;
					eval("Ext.getCmp('chb"+codsis+"').setValue('1')");
					eval("Ext.getCmp('chb"+codsis+"').disable()");
					aperturado[codsis]='1';					
					Ext.MessageBox.alert('Mensaje', objresultado.raiz.mensaje+' Sistema '+codsis); 
				}
				else
				{
					Ext.MessageBox.alert('Error', objresultado.raiz.mensaje+' Sistema '+codsis); 
					
				}
				return valido;
			},
			failure: function ( resultado, request)
			{ 
				Ext.MessageBox.alert('Error', resultado.responseText); 
				return valido;
			}
		});	
	}	
	
	
/***********************************************************************************
* @Función para procesar la apertura del módulo de nómina ya que esta tiene unos parámetros distíntos.
* @parametros: 
* @retorno:
* @fecha de creación: 04/11/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function procesarAperturaNomina(codsis)
	{
		//obtenerMensaje('procesar','','Procesando Datos');
		valido=true;
		if ((validarObjetos('txtfecinimen','10','novacio|fecha')!='0') && (validarObjetos('txtfecinisem','10','novacio|fecha')!='0'))   
		{
			fecinimen = Ext.getCmp('txtfecinimen').getValue().format(Date.patterns.bdfecha);
			fecinisem = Ext.getCmp('txtfecinisem').getValue().format(Date.patterns.bdfecha);
			conversion = Ext.getCmp('chbconversion').getValue();
			prestamos = Ext.getCmp('chbprestamos').getValue();
			if(!conversion)
			{
				conversion=0;	
			}
			else
			{
				conversion=1;	
			}
			if(!prestamos)
			{
				prestamos=0;	
			}
			else
			{
				prestamos=1;	
			}
			var objdata ="{'operacion': 'procesarsno','codsis': codsis, 'sistema':sistema, 'vista': vista,"+
						 "'fecinimen': '"+fecinimen+"','fecinisem': '"+fecinisem+"','prestamosactivos': '"+prestamos+"', 'conversion': '"+conversion+"'";
			arrNomina = gridNominas.store.getModifiedRecords();
			objdata = objdata+ ",datosNomina:[";
			total = arrNomina.length;
			if (total>0)
			{				
				for (i=0; i < total; i++)
				{
					if (arrNomina[i].get('transferir'))
					{
						if (i > 0)
						{
							objdata = objdata +",";
						}
						objdata = objdata +"{'codnom': '"+arrNomina[i].get('codnom')+"','codnuenom': '"+ arrNomina[i].get('codnuenom')+ "'}";
					}
				}				
			}
			objdata = objdata + "]}";
			objdata= eval('(' + objdata + ')');	
			objdata=Ext.util.JSON.encode(objdata);
			parametros = 'objdata='+objdata; 
			Ext.Ajax.request({
				url : ruta,
				params : parametros,
				method: 'POST',
				success: function (resultado,request)
				{
					datos = resultado.responseText;
					Ext.Msg.hide();
					var objresultado = eval('(' + datos + ')');
					if (objresultado.raiz.valido==true)
					{
						valido = true;
						eval("Ext.getCmp('chb"+codsis+"').setValue('1')");
						eval("Ext.getCmp('chb"+codsis+"').disable()");
						aperturado[codsis]='1';					
						Ext.MessageBox.alert('Mensaje', objresultado.raiz.mensaje+' Sistema '+codsis); 
					}
					else
					{
						Ext.MessageBox.alert('Error', objresultado.raiz.mensaje+' Sistema '+codsis); 
						
					}
					return valido;
				},
				failure: function ( resultado, request)
				{ 
					Ext.MessageBox.alert('Error', resultado.responseText); 
					return valido;
				}
			});
		}
	}
	
		
/***********************************************************************************
* @Función para eliminar la apertura del sistema seleccionado
* @parametros: 
* @retorno:
* @fecha de creación: 23/10/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function irEliminar()
	{
		total = (sistemas.length-1);
		valido = true;
		for (contador = total; ((contador >= 0) && valido); contador--)
		{
			codsis=sistemas[contador];
			if(aperturado[codsis]==1)
			{
				if(eval("Ext.getCmp('chb"+codsis+"').getValue()")=='1')
				{
					valido = procesarEliminar(codsis);
				}
			}
		}
	}
	

/***********************************************************************************
* @Función para Eliminar la apertura
* @parametros: 
* @retorno:
* @fecha de creación: 23/10/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function procesarEliminar(codsis)
	{
		panel.load({url:'', waitMsg:'Procesando...'});
		valido=false;
		var objdata ={
			'operacion': 'eliminar',
			'codsis': codsis,
			'sistema': sistema,
			'vista': vista
			};
		
		objdata=Ext.util.JSON.encode(objdata);
		parametros = 'objdata='+objdata;
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado,request)
			{
				datos = resultado.responseText;
				var objresultado = eval('(' + datos + ')');
				if (objresultado.raiz.valido==true)
				{
					valido = true;
					eval("Ext.getCmp('chb"+codsis+"').setValue('0')");
					eval("Ext.getCmp('chb"+codsis+"').enable()");
					aperturado[codsis]='0';										
					Ext.MessageBox.alert('Mensaje', objresultado.raiz.mensaje+' Sistema '+codsis); 
				}
				else
				{
					Ext.MessageBox.alert('Error', objresultado.raiz.mensaje+' Sistema '+codsis);
				}
				return valido;
			},
			failure: function ( resultado, request)
			{ 
				Ext.MessageBox.alert('Error', resultado.responseText); 
				return valido;
			}
		});	
	}	
	
/***********************************************************************************
* @Función para Descargar los archivos generados pór el módulo de apertura
* @parametros: 
* @retorno:
* @fecha de creación: 27/10/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function irDescargar()
	{
		objCatDescarga = new catalogoDescarga();
		objCatDescarga.mostrarCatalogo();
	}	


/***********************************************************************************
* @Función que muestra el Grid de las Nóminas
* @parametros: 
* @retorno:
* @fecha de creación: 03/11/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function activarGrid()
	{
		if (sno.checked)
		{
				document.body.scrolling = true;
				var checkColumn = new Ext.grid.CheckColumn({
		       header: 'Transferir',
		       dataIndex: 'transferir',
		       width: 55
		    });
    
			dataNomina = Ext.data.Record.create([
				{name: 'codemp'},
				{name: 'codnom'},	
				{name: 'desnom'},	
				{name: 'codnuenom', type: 'string', Format: '0000'},	
				{name: 'transferir', type: 'bool'}	
			]);			
			gridNominas = new Ext.grid.EditorGridPanel({
				width:560,
				height:100,
				id:'gridNominas',
		        plugins:checkColumn,
		        clicksToEdit:1,
   				autoScroll:true,
	           	border:true,
	           	ds: new Ext.data.Store({
					proxy: new Ext.data.MemoryProxy(ObjNominas),
					reader: new Ext.data.JsonReader({
			    		root: 'raiz',                
			    		id: 'id'   
	            	},
					dataNomina
					),
					data: ObjNominas
	           }),
	           cm: new Ext.grid.ColumnModel([
					{header: 'Empresa', width: 45, sortable: true, dataIndex: 'codemp'},
					{header: 'Cod. Act', width: 45, sortable: true, dataIndex: 'codnom'},
					{header: 'Nombre', width: 150, sortable: true, dataIndex: 'desnom'},
					{header: 'Cod. Nuevo', width: 45, sortable: true, dataIndex: 'codnuenom', 
					 editor: new Ext.form.TextField({minLength:4, maxLength:4, allowBlank:false, regex :/(^([0-9]{4,4})|^)$/,regexText:'Formato Inválido.' })},
					checkColumn
				]),
				
	           	viewConfig: {forceFit:true},
				stripeRows: true
			});
			gridNominas.startEditing(0, 0);
			periodoactual='01/01/'+anioactual;
			//{header: 'Transferir', width: 45, sortable: true, dataIndex: 'transferir', editor: new Ext.form.Checkbox()},
			var fecinimen = new Ext.form.DateField(
			{
				fieldLabel:'Fecha Inicio Nominas Mensuales',
				labelStyle: 'width:140px',
				name:'Fecha Inicio Mensual',
				id:'txtfecinimen',
				format:'d/m/Y',
				value: periodoactual,
				width:120
			});
			
			var fecinisem = new Ext.form.DateField(
			{
				fieldLabel:'Fecha Inicio Nominas Semanales',
				labelStyle: 'width:140px',
				name:'Fecha Inicio Semanal',
				id:'txtfecinisem',
				format:'d/m/Y',
				value: periodoactual,
				width:120
			});
			var prestamos = new Ext.form.Checkbox(
			{
				xtype:'checkbox',
				fieldLabel:'Transferir solo Prestamos Activos',
				labelStyle: 'width:250px',
				name:'prestamos',
				id:'chbprestamos'		
			});		

			nominapanel = new Ext.form.FieldSet({
					title:'Información Nómina',
					id:'fsformnomina',
					autoHeight:true,
					autoWidth:true,
					cls :'fondo',		
					items:[{	
					  	layout:'column',
					  	border:false,
					  	baseCls: 'fondo',
					  	items:[{
					  		columnWidth:.5,
							layout: 'form',
							border:false,
							baseCls: 'fondo',
							items: [fecinimen]
						},{	
							columnWidth:.5,
							layout: 'form',
							border:false,
							baseCls: 'fondo',
							items: [fecinisem]
						}]},prestamos,gridNominas]
			});
			panel.add(nominapanel);
		}
		else
		{
			panel.remove(nominapanel);
		}
		panel.render(document.body);
	}	