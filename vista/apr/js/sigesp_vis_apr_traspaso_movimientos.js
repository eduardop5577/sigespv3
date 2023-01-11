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
ruta =  '../../controlador/apr/sigesp_ctr_apr_movimientos.php'; 
var sistemas = new Array();
var sno;
var nominapanel;
var dataNomina;
var gridNominas;
var aperturado = new Array();
barraherramienta    = true;
sistemas[0]='sss';
sistemas[1]='sep';
sistemas[2]='soc';
sistemas[3]='cxp';
sistemas[4]='scb';
sistemas[5]='sno';
sistemas[6]='siv';
sistemas[7]='saf';
sistemas[8]='scv';
sistemas[9]='sob';
sistemas[10]='scg';
sistemas[11]='spg';
sistemas[12]='spi';
aperturado['sss']=0;
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
Ext.onReady
(
	function()
	{
		Ext.QuickTips.init();
		Ext.Ajax.timeout=36000000000;

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
		// Nómina
		sno = new Ext.form.Checkbox(
		{
			xtype:'checkbox',
			fieldLabel:'Nómina',
			labelStyle: 'width:250px',
			name:'nomina',
			id:'chbsno'		
		});		


		//componentes del formulario
		Xpos = ((screen.width/2)-(650/2)); 
		Ypos = ((screen.height/2)-(790/2))+20;
		panel = new Ext.FormPanel({
			title: 'Trasferir Movimientos',
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
				items:[sss,sep,soc,cxp,scb,sno,siv,saf,scv,sob,scg,spg,spi]
			}]
		});
	panel.render(document.body);
	verificarMovimientos();
})


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
	function verificarMovimientos()
	{
		var objdata ={
			'operacion': 'verificarMovimientos',
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
			for (contador = 0; ((contador < total) && valido); contador++)
		{
			codsis=sistemas[contador];
			if(aperturado[codsis]==0)
			{
				valido = procesarMovimientos(codsis);
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
	function procesarMovimientos(codsis)
	{
		obtenerMensaje('procesar','','Procesando Datos');
		valido=false;
		var objdata ={
			'operacion': 'procesar',
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
