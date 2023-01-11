/***********************************************************************************
* @fecha de modificacion: 05/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

Ext.namespace('com.sigesp.vista');

com.sigesp.vista.comDetalleComprobante = function(options) 
{
	//Creando el Json para la configuracion de los items del formulario de comprobante
	var cadenaCampos="[";
	for (var i = 0; i < options.arrCampos.length; i++)
	{
       	if(i==options.arrCampos.length-1)
       	{
			cadenaCampos =  cadenaCampos + "{xtype:'"+options.arrCampos[i].tipo+"'," +
										   " fieldLabel: '"+options.arrCampos[i].etiqueta+"'," +
										   " labelSeparator: ''," +
										   " value: '"+options.arrCampos[i].valor+"'," +
					                       " id:'"+options.arrCampos[i].id+"'," +
					                       " width:"+options.arrCampos[i].ancho+"," +
					                       " readOnly: true" +
					                       "}";
		}
       	else
       	{
       		
			cadenaCampos =  cadenaCampos + "{xtype:'"+options.arrCampos[i].tipo+"'," +
			   							   " fieldLabel: '"+options.arrCampos[i].etiqueta+"'," +
			   							   " labelSeparator: ''," +
			   							   " value: '"+options.arrCampos[i].valor+"'," +
			   							   " id:'"+options.arrCampos[i].id+"'," +
			   							   " width:"+options.arrCampos[i].ancho+"," +
			   							   " readOnly: true" +
			   							   "},";
		}
	}
	
	cadenaCampos = cadenaCampos + "]";
	var objetoCampos = Ext.util.JSON.decode(cadenaCampos);
	//Fin creando el Json para la configuracion de los items del formulario de comprobante	
	
	//Creando formulario con la informacion del comprobante
	this.formComprobante = new Ext.FormPanel({
    	frame:true,
    	title: "<H1 align='center'>Informaci&#243;n del Comprobante</H1>",
    	bodyStyle:'padding:5px 5px 0',
    	width: options.anchoFormulario,
		height: options.altoFromulario,
    	items: objetoCampos
	});
	//Fin creando formulario la informacion del comprobante
	
	//Si tiene detalle presupuestari creamos grid detalle presupuesto
	if(options.tienePresupuesto)
	{
		//Creando la grid de detalles presupuestarios
		this.gridPresupuesto = new Ext.grid.GridPanel({
			title: "<H1 align='center'>"+options.tituloGridPresupuestario+"</H1>",
	 		width:options.anchoGridPG,
	 		height:options.altoGridPG,
	 		style : 'margin-top:10px',
	 		autoScroll:true,
	 		enableColumnHide: false,
	 		border:true,
	 		ds: options.dsPresupuestoGasto,
	   		cm: options.cmPresupuestoGasto,
	   		stripeRows: true,
	  		viewConfig: {forceFit:true}
	  	});
		//Fin creando la grid de detalles presupuestarios
		
		//Creando panel para mostrar el total presupuestario
		var leftTotPre = parseInt(options.anchoGridPG)-280;
		this.formTotPre = new Ext.FormPanel({
	    	frame:true,
	    	title: "<H1 align='center'>Total Presupuestario</H1>",
	    	width:options.anchoGridPG,
			height: 70,
	    	items:[{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:'+leftTotPre+'px;top:5px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 130,
					items: [{
						xtype:'textfield',
						fieldLabel:'Total',
						style:'font-weight: bold; border:none;background:#f1f1f1',
						id:'totpre',
						width:150,
						labelSeparator:'',
					}]
				}]
	    	}]	
		});
		//Fin creando panel para mostrar el total presupuestario
		
		//Creando funciones para buscar y cargar datos de presupuesto
		this.cargarDatosPresupuestoGasto = function ()
		{
			var datos = arguments[0].responseText;
			var objetodata = eval('(' + datos + ')');
			if(objetodata!='')
			{
				if(objetodata.raiz == null || objetodata.raiz.length==0)
				{
					this.gridPresupuesto.hide();
					this.formTotPre.hide();
				}
				else
				{
					this.gridPresupuesto.getStore().loadData(objetodata);
					var totalPre = 0;
					this.gridPresupuesto.store.each(function (registrostore)
					{
						var valor = formatoNumericoMostrar(registrostore.get('monto'),2,'','.','','','-','');
						totalPre = totalPre + parseFloat(valor);
					});
					
					if(totalPre <= 0)
					{ 
						this.formTotPre.hide();
					}
					Ext.getCmp('totpre').setValue(formatoNumericoMostrar(totalPre,2,'.',',','','','-',''));
				}
			}
		}
		
		this.buscarDataPresupuestoGasto = function()
		{
			Ext.Ajax.request({
				url : options.rutaControlador,
				params : options.paramPresupuesto,
				method: 'POST',
				success: this.cargarDatosPresupuestoGasto.createDelegate(this, arguments, 2)
			});
		}
		//Creando funciones para buscar y cargar datos de presupuesto
	}
	//Si tiene detalle presupuestario creamos grid detalle presupuesto
	
	
	
	//Si tiene detalle contable creamos grid detalle contable
	if(options.tieneContable)
	{
		//creando datastore y columnmodel para la grid de detalles presupuestarios
		this.reMovContable = Ext.data.Record.create([
		    {name: 'cuenta'}, 
		    {name: 'denominacion'},
		    {name: 'debe'},
		    {name: 'haber'}
		]);
		
		this.dsMovContable =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},this.reMovContable)
		});
		//fin creando datastore y columnmodel para la grid de detalles presupuestarios
		
		
		//Creando la grid de detalles presupuestarios
		this.gridContable = new Ext.grid.GridPanel({
			title: "<H1 align='center'>Detalle Contable</H1>",
	 		width:options.anchoGridCO,
	 		height:options.altoGridCO,
	 		style : 'margin-top:10px',
	 		autoScroll:true,
	 		enableColumnHide: false,
	 		border:true,
	 		ds: this.dsMovContable,
	   		cm: new Ext.grid.ColumnModel([
	   		    {header: "Cuenta", width: 60, sortable: true, dataIndex: 'cuenta'},
	   		    {header: "Denominacion", width: 100, sortable: true, dataIndex: 'denominacion'},
	   		    {header: "Debe", width: 40, sortable: true, dataIndex: 'debe'},
	   		    {header: "Haber", width: 40, sortable: true, dataIndex: 'haber'}
	   		]),
	   		stripeRows: true,
	  		viewConfig: {forceFit:true}
		});
		//Fin creando la grid de detalles presupuestarios
		
		//Creando panel para mostrar el total contable
		var leftTotCon = parseInt(options.anchoGridPG)-280;
		this.formTotCon = new Ext.FormPanel({
	    	frame:true,
	    	title: "<H1 align='center'>Total Contable</H1>",
	    	width:options.anchoGridCO,
			height: 90,
	    	items:[{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:'+leftTotCon+'px;top:5px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 130,
					items: [{
						xtype:'textfield',
						fieldLabel:'Total Debe',
						style:'font-weight: bold; border:none;background:#f1f1f1',
						id:'totdeb',
						width:150,
						labelSeparator:'',
					}]
				}]
	    	},{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:'+leftTotCon+'px;top:35px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 130,
					items: [{
						xtype:'textfield',
						fieldLabel:'Total Haber',
						style:'font-weight: bold; border:none;background:#f1f1f1',
						id:'tothab',
						width:150,
						labelSeparator:'',
					}]
				}]
	    	}]	
		});
		//Fin creando panel para mostrar el total contable
		
		//Creando funciones para buscar y cargar datos de contabilidad
		this.cargarDatosContable = function ()
		{
			var datos = arguments[0].responseText;
			var objetodata = eval('(' + datos + ')');
			if(objetodata!=''){
				if(objetodata.raiz == null || objetodata.raiz.length==0)
				{
					this.gridContable.hide();
					this.formTotCon.hide();
				}
				else
				{
					this.dsMovContable.loadData(objetodata);
					var totalDebe  = 0;
					var totalHaber = 0;
					this.dsMovContable.each(function (registrostore)
					{
						var debe  = replaceAll(registrostore.get('debe'), '.', '');
						debe  = replaceAll(debe, ',', '.');
					    debe  = formatoNumericoMostrar(debe,2,'','.','','','-','');
						var haber  = replaceAll(registrostore.get('haber'), '.', '');
						haber  = replaceAll(haber, ',', '.');
					    haber  = formatoNumericoMostrar(haber,2,'','.','','','-','');
						totalDebe = totalDebe + parseFloat(debe);
						totalHaber = totalHaber + parseFloat(haber);
					});
					if(totalDebe <= 0) { 
						this.formTotCon.hide();
					}
					Ext.getCmp('totdeb').setValue(formatoNumericoMostrar(totalDebe,2,'.',',','','','-',''));
					Ext.getCmp('tothab').setValue(formatoNumericoMostrar(totalHaber,2,'.',',','','','-',''));
				}
			}
		}
		
		this.buscarDataContable = function(){
			Ext.Ajax.request({
				url : options.rutaControlador,
				params : options.paramContable,
				method: 'POST',
				success: this.cargarDatosContable.createDelegate(this, arguments, 2)
			});
		}
		//Creando funciones para buscar y cargar datos de contabilidad
	}
	//Fin Si tiene detalle contable creamos grid detalle contable
	
	//Creando la instacia de la window para la ventana detalle comprobante
	this.venComprobante = new Ext.Window({
		title: options.tituloVentana,
		bodyStyle:'padding:5px 5px 0',
		autoScroll:true,
    	width:options.anchoVentana,
    	height:options.altoVentana,
    	modal: true,
    	closable:true,
    	plain: false,
		items:[this.formComprobante]
	});
	//Fin creando la instacia de la window para la ventana detalle comprobante
			

	this.mostrarVentana = function()
	{
		if(options.tienePresupuesto)
		{
			var fnBuscarPresupuestoGasto = this.buscarDataPresupuestoGasto.createDelegate(this);
			fnBuscarPresupuestoGasto();
			this.venComprobante.add(this.gridPresupuesto);
			this.venComprobante.add(this.formTotPre);
			this.venComprobante.doLayout();
			if(options.tieneContable)
			{
				var fnBuscarContable = this.buscarDataContable.createDelegate(this);
				fnBuscarContable();
				this.venComprobante.add(this.gridContable);
				this.venComprobante.add(this.formTotCon);
				this.venComprobante.doLayout();
				this.venComprobante.show();
			}
			else
			{
				this.venComprobante.show();			
			}
		}
		else
		{
			if(options.tieneContable)
			{
				var fnBuscarContable = this.buscarDataContable.createDelegate(this);
				fnBuscarContable();
				this.venComprobante.add(this.gridContable);
				this.venComprobante.add(this.formTotCon);
				this.venComprobante.doLayout();
				this.venComprobante.show();
			}			
		}
	}
}