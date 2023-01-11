/***********************************************************************************
* @Archivo JavaScript que incluye el componente lista editable catalogo para cargar
* detalles de bienes serivicos y conceptos  
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

com.sigesp.vista.comDetalleBieSerCon = function(options){
	/*******************************************************************
	*@Seccion: Declaracion Atributos
	********************************************************************/
	this.colmodCatalogo 	= '';
	this.colmodGrid         = '';
	this.colmodGridCargos   = '';
	this.dsCatalogo     	= '';
	this.dsGrid         	= '';
	this.dsGridEli        	= '';
	this.dsGridCargos 		= '';
	this.dsGridCargosEli	= '';
	this.dsGridCuentas 		= '';
	this.dsGridCuentasCred	= '';
	this.dsGridCuentasEli	= '';
	this.itemsFormBusqueda  = '';
	this.valdisponible      = '';
	this.banderaasiento		= false;
	var etiquetatitgrid     = '';
	var combotiparticulo 	= null;
	/*******************************************************************
	*@Fin Seccion:Declaracion Atributos
	********************************************************************/
	
	
	/*******************************************************************
	*@Seccion:     Asignacion de Valores(tipo de grid)
	*@Descripcion: Aqui se asigna valor a los atributos
	*              definidos segun sea el tipo de grid
	*              se ajustna los datastore y los colummodel
	*              a las necesidades
	********************************************************************/
	switch(options.tipodetalle) {
		
		case 'B'://Tipo detalle es Bien se construyen los columnmodel de bienes
	    	
			//titulo para la grid de datos
			etiquetatitgrid = 'Bien o Material';
			
			//creando datastore del catalogo bienes
			var registro_bien = Ext.data.Record.create([
					{name: 'coditem'},    
					{name: 'denitem'},
					{name: 'denunimed'},
					{name: 'unidad'},
					{name: 'spg_cuenta'},
					{name: 'disponibilidad'},
					{name: 'canitem'},
					{name: 'preitem'},
					{name: 'existecuenta'}
				]);
	
			this.dsCatalogo =   new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "coditem"},registro_bien)
	  			});
			//fin creando datastore del catalogo bienes
				
			//creando columnmodel del catalogo de bienes
			this.colmodCatalogo = new Ext.grid.ColumnModel([new Ext.grid.CheckboxSelectionModel({}),
          			{header: "Codigo", width: 30, sortable: true,   dataIndex: 'coditem'},
          			{header: "Denominacion", width: 60, sortable: true, dataIndex: 'denitem'},
					{header: "Unidad Medida", width: 25, sortable: true, dataIndex: 'denunimed'},
					{header: "Cuenta", width: 25, sortable: true, dataIndex: 'spg_cuenta'}
        		]);
			//fin creando columnmodel del catalogo de bienes
			
			//creando el datastore para el combo de tipo articulo a usar en el formbusqueda
			var registro_tiparticulo = Ext.data.Record.create([
						{name: 'codtipart'},
						{name: 'dentipart'}
				]);
		
			var dstiparticulo =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_tiparticulo)
	  			});
			//fin creando el datastore para el combo de tipo articulo a usar en el formbusqueda
	
			//funcion para llenar el combo de tipo articulo a usar en el formbusqueda
			function getDatatiparticulo(){
				cadenaJson="{'operacion':'combotipoart'}";
				parametros = 'ObjSon='+cadenaJson;
				Ext.Ajax.request({
						url : '../../controlador/siv/sigesp_ctr_siv_catalogoarticulo.php',
						params : parametros,
						method: 'POST',
						success: function ( resultado, request)	{ 
									var datos = resultado.responseText;
									var objeto = eval('(' + datos + ')');
									if(objeto!=''){
										dstiparticulo.loadData(objeto);
									}
						}
				});
			}
			//fin funcion para llenar el combo de tipo articulo a usar en el formbusqueda
	
			//creando objeto combo tipo solicitud
			getDatatiparticulo();//lenando el datastore del combo tipo articulo
			combotiparticulo = new Ext.form.ComboBox({
					store: dstiparticulo,
					labelSeparator :'',
					fieldLabel:'Tipo',
            		editable: false,
					displayField: 'dentipart',
            		valueField: 'codtipart',
            		id: 'codtipart',
            		typeAhead: true,
           			triggerAction: 'all',
            		mode: 'local',
					width: 250
				});
			//fin creando objeto combo tipo solicitud
			
			//creando datastore para la grid de bienes
			var registro_bien = Ext.data.Record.create([
					{name: 'coditem'},    
					{name: 'denitem'},
					{name: 'denunimed'},
					{name: 'unidad'},
					{name: 'moduni'},
					{name: 'canitem'},
					{name: 'preitem'},
					{name: 'subtot'},
					{name: 'moncar'},
					{name: 'montot'},
					{name: 'spg_cuenta'},
					{name: 'registrocat'}
				]);
	
			this.dsGrid =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "coditem"},registro_bien)
	  			});
				
			this.dsGridEli =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "coditem"},registro_bien)
	  			});
			//fin creando datastore para la grid de bienes
				
			//creando columnmodel de la grid de bienes
			this.campocantidad = new Ext.form.TextField({
					maxLength:15,
					minLength:1,
					style: 'text-align:right',
					moneda:true,
					precision:2,
					autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
					listeners:{
								'blur':function(objeto){
											var numero = objeto.getValue();
											valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
											objeto.setValue(valor);
								},
								'focus':function(objeto){
											var numero = formatoNumericoEdicion(objeto.getValue());
											objeto.setValue(numero);
								}
							}
				});
			
			this.campoprecio = new Ext.form.TextField({
					maxLength:15,
					minLength:1,
					style: 'text-align:right',
					moneda:true,
					precision:2,
					autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
					listeners:{
								'blur':function(objeto){
											var numero = objeto.getValue();
											valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
											objeto.setValue(valor);
								},
								'focus':function(objeto){
											var numero = formatoNumericoEdicion(objeto.getValue());
											objeto.setValue(numero);
								}
							}
				});
			
			//creando store para el combo modalidad unidad
			var moduni = [['Seleccione', 'S'],['Detal', 'D'],['Mayor', 'M']];
			var storemoduni = new Ext.data.SimpleStore({
            		fields: ['etiqueta', 'valor'],
            		data: moduni
				});
			//fin creando store para el modalidad unidad
	
			//creando objeto combo modalidad unidad
			this.combomoduni = new Ext.form.ComboBox({
					store: storemoduni,
					editable: false,
            		displayField: 'etiqueta',
            		valueField: 'valor',
            		name: 'modounidad',
            		id: 'moduni',
					typeAhead: true,
            		triggerAction: 'all',
            		mode: 'local'
				});
			//fin creando objeto combo modalidad unidad
			
			//creando columnmodel de la grid de bienes
			function getEtiquetaMod(mod){
						
				if (mod == 'M') {
					return 'Mayor';
				}
				else if (mod == 'D') {
					return 'Detal';
				}
				else{
					return 'Seleccione';
				}
			}
			
			this.colmodGrid = new Ext.grid.ColumnModel([
          			{header: "Codigo", width: 60, sortable: true,   dataIndex: 'coditem'},
          			{header: "Denominacion", width: 60, sortable: true, dataIndex: 'denitem'},
					{header: "Unidad", width: 30, sortable: true, dataIndex: 'denunimed'},
					{header: "Modalidad", width: 30, sortable: true, dataIndex: 'moduni',editor:this.combomoduni,renderer:getEtiquetaMod},
					{header: "Cantidad", width: 30, sortable: true, dataIndex: 'canitem',editor:this.campocantidad},
					{header: "Precio", width: 35, sortable: true, dataIndex: 'preitem',editor:this.campoprecio},
					{header: "Subtotal", width: 35, sortable: true, dataIndex: 'subtot'},
					{header: "Cargos", width: 35, sortable: true, dataIndex: 'moncar'},
					{header: "Total", width: 35, sortable: true, dataIndex: 'montot'}
        		]);
			//fin creando columnmodel de la grid de bienes
			
			break;//Fin tipo detalle es Bien se construyen los columnmodel de bienes
			
		case 'S'://Tipo detalle es Servicio se construyen los columnmodel de servicio
			
			//titulo para la grid de datos
			etiquetatitgrid = 'Servicio';
			
	    	//creando datastore del catalogo de servicios
			var registro_catservicio = Ext.data.Record.create([
					{name: 'coditem'},    
					{name: 'denitem'},
					{name: 'canitem'},
					{name: 'preitem'},
					{name: 'spg_cuenta'},
					{name: 'disponibilidad'},
					{name: 'existecuenta'}
				]);
	
			this.dsCatalogo =   new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "coditem"},registro_catservicio)
	  			});
			//fin creando datastore del catalogo de servicios
				
			//creando columnmodel del catalogo de servicios
			this.colmodCatalogo = new Ext.grid.ColumnModel([new Ext.grid.CheckboxSelectionModel({}),
          			{header: "Codigo", width: 30, sortable: true,   dataIndex: 'coditem'},
          			{header: "Denominacion", width: 60, sortable: true, dataIndex: 'denitem'},
					{header: "Precio", width: 25, sortable: true, dataIndex: 'preitem'},
					{header: "Cuenta", width: 25, sortable: true, dataIndex: 'spg_cuenta'}
        		]);
			//fin creando columnmodel del catalogo de servicios
			
			//creando datastore para la grid de servicios
			var registro_servicio = Ext.data.Record.create([
					{name: 'coditem'},    
					{name: 'denitem'},
					{name: 'canitem'},
					{name: 'preitem'},
					{name: 'subtot'},
					{name: 'moncar'},
					{name: 'montot'},
					{name: 'spg_cuenta'},
					{name: 'registrocat'}
				]);
	
			this.dsGrid =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_servicio)
	  			});
			
			this.dsGridEli =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_servicio)
	  			});
			//fin creando datastore para la grid de servicios
				
			//creando campos a usar en el columnmodel de la grid de servicios
			var campocantidad = new Ext.form.TextField({
					maxLength:15,
					minLength:1,
					style: 'text-align:right',
					moneda:true,
					precision:2,
					autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
					listeners:{
								'blur':function(objeto){
											var numero = objeto.getValue();
											valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
											objeto.setValue(valor);
								},
								'focus':function(objeto){
											var numero = formatoNumericoEdicion(objeto.getValue());
											objeto.setValue(numero);
								}
							}
				});
			
			var campoprecio = new Ext.form.TextField({
					maxLength:15,
					minLength:1,
					style: 'text-align:right',
					moneda:true,
					precision:2,
					autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
					listeners:{
								'blur':function(objeto){
											var numero = objeto.getValue();
											valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
											objeto.setValue(valor);
								},
								'focus':function(objeto){
											var numero = formatoNumericoEdicion(objeto.getValue());
											objeto.setValue(numero);
								}
							}
				});
			//creando campos a usar en el columnmodel de la grid de servicios
			
			//creando columnmodel de la grid de servicios
			this.colmodGrid = new Ext.grid.ColumnModel([
          			{header: "Codigo", width: 60, sortable: true,   dataIndex: 'coditem'},
          			{header: "Denominacion", width: 60, sortable: true, dataIndex: 'denitem'},
					{header: "Cantidad", width: 30, sortable: true, dataIndex: 'canitem',editor:campocantidad},
					{header: "Precio", width: 35, sortable: true, dataIndex: 'preitem',editor:campoprecio},
					{header: "Subtotal", width: 35, sortable: true, dataIndex: 'subtot'},
					{header: "Cargos", width: 35, sortable: true, dataIndex: 'moncar'},
					{header: "Total", width: 35, sortable: true, dataIndex: 'montot'}
        		]);
			//fin creando columnmodel de la grid de servicios
			break;
			
		case 'O':
			//titulo para la grid de datos
			etiquetatitgrid = 'Conceptos';
			
	    	//creando datastore del catalogo de servicios
			var registro_catconcepto = Ext.data.Record.create([
					{name: 'coditem'},    
					{name: 'denitem'},
					{name: 'canitem'},
					{name: 'preitem'},
					{name: 'spg_cuenta'},
					{name: 'disponibilidad'},
					{name: 'existecuenta'}
				]);
	
			this.dsCatalogo =   new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "coditem"},registro_catconcepto)
	  			});
			//fin creando datastore del catalogo de servicios
			
			//creando columnmodel del catalogo de conceptos
			this.colmodCatalogo = new Ext.grid.ColumnModel([new Ext.grid.CheckboxSelectionModel({}),
          			{header: "Codigo", width: 30, sortable: true,   dataIndex: 'coditem'},
          			{header: "Denominacion", width: 60, sortable: true, dataIndex: 'denitem'},
					{header: "Precio", width: 25, sortable: true, dataIndex: 'preitem'},
					{header: "Cuenta", width: 25, sortable: true, dataIndex: 'spg_cuenta'}
        		]);
			//fin creando columnmodel del catalogo de conceptos
			
			//creando datastore para la grid de conceptos
			var registro_concepto = Ext.data.Record.create([
					{name: 'coditem'},    
					{name: 'denitem'},
					{name: 'canitem'},
					{name: 'preitem'},
					{name: 'subtot'},
					{name: 'moncar'},
					{name: 'montot'},
					{name: 'spg_cuenta'},
					{name: 'registrocat'}
				]);
	
			this.dsGrid =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_concepto)
	  			});
			
			this.dsGridEli =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_concepto)
	  			});
			//fin creando datastore para la grid de bienes
			
			//creando campos a usar en el columnmodel de la grid de servicios
			var campocantidad = new Ext.form.TextField({
					maxLength:15,
					minLength:1,
					style: 'text-align:right',
					moneda:true,
					precision:2,
					autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
					listeners:{
								'blur':function(objeto){
											var numero = objeto.getValue();
											valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
											objeto.setValue(valor);
								},
								'focus':function(objeto){
											var numero = formatoNumericoEdicion(objeto.getValue());
											objeto.setValue(numero);
								}
							}
				});
			
			var campoprecio = new Ext.form.TextField({
					maxLength:15,
					minLength:1,
					style: 'text-align:right',
					moneda:true,
					precision:2,
					autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
					listeners:{
								'blur':function(objeto){
											var numero = objeto.getValue();
											valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
											objeto.setValue(valor);
								},
								'focus':function(objeto){
											var numero = formatoNumericoEdicion(objeto.getValue());
											objeto.setValue(numero);
								}
							}
				});
			//creando campos a usar en el columnmodel de la grid de servicios
			
			//creando columnmodel de la grid de conceptos
			this.colmodGrid = new Ext.grid.ColumnModel([
          			{header: "Codigo", width: 60, sortable: true,   dataIndex: 'coditem'},
          			{header: "Denominacion", width: 60, sortable: true, dataIndex: 'denitem'},
					{header: "Cantidad", width: 30, sortable: true, dataIndex: 'canitem',editor:campocantidad},
					{header: "Precio", width: 35, sortable: true, dataIndex: 'preitem',editor:campoprecio},
					{header: "Subtotal", width: 35, sortable: true, dataIndex: 'subtot'},
					{header: "Cargos", width: 35, sortable: true, dataIndex: 'moncar'},
					{header: "Total", width: 35, sortable: true, dataIndex: 'montot'}
        		]);
			//fin creando columnmodel de la grid de conceptos
			break;
	}
	/*******************************************************************
	*@Fin Seccion:     Asignacion de Valores(tipo de grid)
	********************************************************************/
	
	//creando funciones para realizar el filtrado del ds del catalgo
	this.filtrarcodigods = function(){
		var v = this.formBusquedaCat.getComponent('coditem').getValue();
		this.dsCatalogo.filter('coditem', v)
		if (String(v) !== String(this.formBusquedaCat.getComponent('coditem').startValue)) {
			this.formBusquedaCat.getComponent('coditem').fireEvent('change', this.formBusquedaCat.getComponent('coditem'), v, this.formBusquedaCat.getComponent('coditem').startValue);
		}
	}
			
	this.filtrardescripcionds = function(){
		var v = this.formBusquedaCat.getComponent('denitem').getValue();
		this.dsCatalogo.filter('denitem', v)
		if (String(v) !== String(this.formBusquedaCat.getComponent('denitem').startValue)) {
			this.formBusquedaCat.getComponent('denitem').fireEvent('change', this.formBusquedaCat.getComponent('denitem'), v, this.formBusquedaCat.getComponent('denitem').startValue);
		}
	}
	//fin creando funciones para realizar el filtrado del ds del catalgo
			
	//creando items formulario de busqueda para la grid de bienes
	this.itemsFormBusqueda = [{
                						xtype:'textfield',
										fieldLabel: 'C&#243;digo',
                						id:'coditem',
										changeCheck:this.filtrarcodigods.createDelegate(this),							 
										initEvents : function(){
													AgregarKeyPress(this);
								    	}               
      								},{
                						xtype:'textfield',
										fieldLabel: 'Denominaci&#243;n',
                						id:'denitem',
										changeCheck: this.filtrardescripcionds.createDelegate(this),							 
										initEvents : function(){
													AgregarKeyPress(this);
										}               
    							}];
	//creando items formulario de busqueda para la grid de bienes
	
	//creando datastore para la grid de cargos 
	var registro_cargo = Ext.data.Record.create([
			{name: 'coditem'},    
			{name: 'codcar'},
			{name: 'dencar'},
			{name: 'basimp'},
			{name: 'moncar'},
			{name: 'totcar'},
			{name: 'spg_cuenta'},
			{name: 'formula'},
			{name: 'existecuenta'},
			{name: 'registrocat'}
		]);
	
	this.dsGridCargos =   new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "coditem"},registro_cargo)
		});
	
	this.dsGridCargosEli =   new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "coditem"},registro_cargo)
		});
	//fin creando datastore para la grid de cargos y datastore de eliminados
				
	//creando columnmodel para la grid de cargos
	this.colmodGridCargos = new Ext.grid.ColumnModel([
					{header: etiquetatitgrid, width: 60, sortable: true,   dataIndex: 'coditem'},
          			{header: "C&#243;digo", width: 30, sortable: true, dataIndex: 'codcar'},
					{header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'dencar'},
					{header: "Base Imponible", width: 25, sortable: true, dataIndex: 'basimp'},
					{header: "Monto Cargo", width: 25, sortable: true, dataIndex: 'moncar'},
					{header: "Sub-Total", width: 25, sortable: true, dataIndex: 'totcar'}
    	]);
	//fin creando columnmodel para la grid de cargos
	
		
	//Creando formulario de busqueda del catalogo
	this.formBusquedaCat = new Ext.FormPanel({
        	labelWidth: 80, 
			frame:true,
        	title: 'Busqueda',
        	bodyStyle:'padding:5px 5px 0',
        	width: 630,
			height:150,
        	items: this.itemsFormBusqueda
		});
		
	if(options.tipodetalle=='B'){
		this.formBusquedaCat.add(combotiparticulo);
		this.formBusquedaCat.doLayout();
	}	
	//Fin creando formulario de busqueda del catalogo
	
		
	//Creando la instacia de la grid del catalogo
	this.gridcatalogo = new Ext.grid.GridPanel({
	 		id:options.idgridcat,
			width:options.anchogridcat,
	 		height:options.altogridcat,
	 		tbar: this.formBusquedaCat,
	 		autoScroll:true,
     		border:true,
     		ds: this.dsCatalogo,
       		cm: this.colmodCatalogo,
			sm: new Ext.grid.CheckboxSelectionModel({}),
       		stripeRows: true,
      		viewConfig: {forceFit:true}
		});
	
	//esto permite cambiarle el estilo a una fila de la grid	
	this.gridcatalogo.getView().getRowClass = function(record, index){
		if(record.data.existecuenta!='0'){
			return 'Filazul';
		}
	}
	//Fin Creando la instacia de la grid del catalogo
	
	//Eventos de la ventana catalogo
	this.cerrarVentana = function(){
		this.vencatalogo.hide();
	}
	
	this.cargarDatosCat = function (){
		var resultado = arguments[0].responseText;
		var	arrresult = resultado.split("|");
		this.valdisponible = arrresult[0];
		var objetodata = eval('(' + arrresult[1] + ')');
		if(objetodata!=''){
			if(objetodata.raiz == null){
				Ext.MessageBox.alert('Informaci&#243;n','No se encontraron datos')
			}
			else{
				this.dsCatalogo.loadData(objetodata);
				Ext.MessageBox.hide();
			}
			
		}
	}
	
	this.buscarDataCatalogo = function(){
		var tipbien = '';
		var nuevosparamentros = options.parametros;
		var validobuscar = true;
		
		for (var i = 0; i < this.itemsFormBusqueda.length; i++) {
       		nuevosparamentros = nuevosparamentros +",'"+this.itemsFormBusqueda[i].id+"':'"+this.formBusquedaCat.getComponent(this.itemsFormBusqueda[i].id).getValue()+"'";
		}
		nuevosparamentros = nuevosparamentros +",'codestpro1':'"+Ext.getCmp('codestpro1').getValue()+"'";		
		nuevosparamentros = nuevosparamentros +",'codestpro2':'"+Ext.getCmp('codestpro2').getValue()+"'";
		nuevosparamentros = nuevosparamentros +",'codestpro3':'"+Ext.getCmp('codestpro3').getValue()+"'";
		nuevosparamentros = nuevosparamentros +",'codestpro4':'"+Ext.getCmp('codestpro4').getValue()+"'";
		nuevosparamentros = nuevosparamentros +",'codestpro5':'"+Ext.getCmp('codestpro5').getValue()+"'";
		nuevosparamentros = nuevosparamentros +",'estcla':'"+Ext.getCmp('estcla').getValue()+"'";
		if(options.tipodetalle=='B'){
			for( var j=0; j < Ext.getCmp('tipsepbie').items.length; j++ ){
				if (Ext.getCmp('tipsepbie').items.items[j].checked){
					tipbien = Ext.getCmp('tipsepbie').items.items[j].inputValue;
					break;
				}
			}
			nuevosparamentros = nuevosparamentros +",'tipsepbie':'"+tipbien+"','codtipart':'"+this.formBusquedaCat.getComponent('codtipart').getValue()+"'}";
			if(tipbien==''){
				Ext.MessageBox.alert('Advertencia','Debe indicar el tipo de bien');
				this.vencatalogo.hide();
				validobuscar = false;
			}
		}
		else{
			nuevosparamentros = nuevosparamentros +"}";
		}
		
		if(validobuscar){
			Ext.MessageBox.show({
				msg: 'Buscando informaci&#243;n',
				title: 'Progreso',
				progressText: 'Buscando informaci&#243;n',
				width:300,
				wait:true,
				waitConfig:{interval:250},	
				animEl: 'mb7'
			});
		
			Ext.Ajax.request({
				url : options.rutacontrolador,
				params : nuevosparamentros,
				method: 'POST',
				success: this.cargarDatosCat.createDelegate(this, arguments, 2)
			});
		}
	}
	
	//creando funcion para cargar los creditos
	this.cargarDatosCargos = function (){
		var datos = arguments[0].responseText;
		var objetodata = eval('(' + datos + ')');
		if(objetodata!=''){
			if(objetodata.raiz == null){
				Ext.MessageBox.alert('Informaci&#243;n','Este item no tiene cargos asociados')
			}
			else{
				
				var arrdata = objetodata.raiz;
				for (var i = arrdata.length - 1; i >= 0; i--){
					var cargo = new registro_cargo({
						'coditem':arrdata[i].coditem,
						'codcar':arrdata[i].codcar,
						'dencar':arrdata[i].dencar,
						'spg_cuenta':arrdata[i].spg_cuenta,
						'formula':arrdata[i].formula
					});
					this.dsGridCargos.insert(0,cargo);
				}
			}
		}
	}
	
	function obtenerCreditos (cadenajson){
		cadenajson = cadenajson +",'codestpro1':'"+Ext.getCmp('codestpro1').getValue()+"'";		
		cadenajson = cadenajson +",'codestpro2':'"+Ext.getCmp('codestpro2').getValue()+"'";
		cadenajson = cadenajson +",'codestpro3':'"+Ext.getCmp('codestpro3').getValue()+"'";
		cadenajson = cadenajson +",'codestpro4':'"+Ext.getCmp('codestpro4').getValue()+"'";
		cadenajson = cadenajson +",'codestpro5':'"+Ext.getCmp('codestpro5').getValue()+"'";
		cadenajson = cadenajson +",'estcla':'"+Ext.getCmp('estcla').getValue()+"'}";
		parametros = 'ObjSon='+cadenajson;
					
		Ext.Ajax.request({
			url : '../../controlador/cfg/sigesp_ctr_cfg_cargos.php',
			params : parametros,
			method: 'POST',
			success: this.cargarDatosCargos.createDelegate(this, arguments, 2)
		});
	}
	this.getCreditos = obtenerCreditos.createDelegate(this);
	//Fin creando funcion para cargar los creditos
	
	this.setDataGrid = function(){
		var arrvalida  = ['coditem'];
		var arregloreg =  this.gridcatalogo.getSelectionModel().getSelections();
		var formatopre = 0;
		
		for (i=0; i<arregloreg.length; i++){
			var validareg = arregloreg[i];
			if(validarExistenciaRegistroStore(validareg,this.dataGridEditable.store,arrvalida,arrvalida)){
				if(this.valdisponible==1){
					if(parseFloat(arregloreg[i].get('disponibilidad'))>0){
						arregloreg[i].set('registrocat','1');
						arregloreg[i].set('canitem','0,00');
						formatopre = formatoNumericoMostrar(arregloreg[i].get('preitem'),2,'.',',','','','-','');
						arregloreg[i].set('preitem',formatopre);
						this.dataGridEditable.store.insert(0,arregloreg[i]);
						switch(options.tipodetalle) {
							case 'B':
								cadenajson = "{'oper':'buscarcargobienes','codart':'"+arregloreg[i].get('coditem')+"'";
	    						this.getCreditos(cadenajson);
								break;
							case 'S':
								cadenajson = "{'oper':'buscarcargoservicios','codser':'"+arregloreg[i].get('coditem')+"'";
	    						this.getCreditos(cadenajson);
								break;
							case 'O':
								cadenajson = "{'oper':'buscarcargoconcepto','codcon':'"+arregloreg[i].get('coditem')+"'";
	    						this.getCreditos(cadenajson);
								break;
						}
						
						if(this.banderaasiento){
							Ext.MessageBox.alert('Advertencia','Debe crear el asiento nuevamente');
							this.dsGridCuentas.removeAll();
							this.dsGridCuentasCred.removeAll();
						}
						
					}
					else{
						Ext.MessageBox.alert('Advertencia','No hay disponibilidad para procesar la transaccion');
					}
				}
				else{
					arregloreg[i].set('registrocat','1');
					arregloreg[i].set('canitem','0,00');
					formatopre = formatoNumericoMostrar(arregloreg[i].get('preitem'),2,'.',',','','','-','');
					arregloreg[i].set('preitem',formatopre);
					this.dataGridEditable.store.insert(0,arregloreg[i]);
					switch(options.tipodetalle) {
						case 'B':
							cadenajson = "{'oper':'buscarcargobienes','codart':'"+arregloreg[i].get('coditem')+"'";
							this.getCreditos(cadenajson);
							break;
						case 'S':
							cadenajson = "{'oper':'buscarcargoservicios','codser':'"+arregloreg[i].get('coditem')+"'";
	    					this.getCreditos(cadenajson);
							break;
						case 'O':
							cadenajson = "{'oper':'buscarcargoconcepto','codcon':'"+arregloreg[i].get('coditem')+"'";
	    					this.getCreditos(cadenajson);
							break;
					}
					
					if(this.banderaasiento){
						Ext.MessageBox.alert('Advertencia','Debe crear el asiento nuevamente');
						this.dsGridCuentas.removeAll();
						this.dsGridCuentasCred.removeAll();
					}
				}
			}
			else{
				Ext.MessageBox.alert('Advertencia','El item seleccionado ya fue cargado');
			}
		}
		this.dsCatalogo.removeAll();
		this.vencatalogo.hide();
	}
		
	this.mostrarVentana = function(){
		
		if (Ext.getCmp('coduniadm').getValue() != '') {
			if (this.formBusquedaCat.getComponent('botbusqueda') == null) {
				var botbusqueda = new Ext.Button({
					id: 'botbusqueda',
					iconCls: 'menubuscar',
					style: options.posbotbus,
					handler: this.buscarDataCatalogo.createDelegate(this)
				});
				this.formBusquedaCat.add(botbusqueda);
				this.formBusquedaCat.doLayout();
			}
			this.vencatalogo.show();
		}
		else{
			Ext.MessageBox.alert('Advertencia','Debe indicar la unidad ejecutora');
		}
	}
	//Fin de los eventos de la ventana catalogo
	
	//Creando la instacia de la window para la ventana del catalogo
	this.vencatalogo = new Ext.Window({
    		title: options.titvencat,
			autoScroll:true,
        	width:options.anchocat,
        	height:options.altocat,
        	modal: true,
        	closable:false,
        	plain: false,
			items:[this.gridcatalogo],
			buttons: [{
						text:'Aceptar',  
			        	handler: this.setDataGrid.createDelegate(this)
			       	},{
			      		text: 'Salir',
			        	handler:this.cerrarVentana.createDelegate(this)
                  	}]
      	});
	//Fin creando la instacia de la window para la ventana del catalogo
	
	//creando funcion para eliminar registros de las grids
	function buscarCargo(registro){
		var i = 0;
		var arrregistro = new Array(); 
		this.dsGridCargos.each(function (registrogrid){
			if (registrogrid.get('coditem') == registro.get('coditem')) {
				arrregistro[i] = registrogrid;
				i++;
			}
		});
		
		return arrregistro;
	}
	
	this.eliminarRegistro = function (){
		var fncbuscarCargo   = buscarCargo.createDelegate(this);
		var fncajustaAsiento = this.fnAjustarAsiento.createDelegate(this);
		var registroeli = this.dataGridEditable.getSelectionModel().getSelected();
		var arrcareli = fncbuscarCargo(registroeli);
		for (var i = arrcareli.length - 1; i >= 0; i--){
			this.dataGridCargos.getStore().remove(arrcareli[i]);
			if(arrcareli[i].get('registrocat')=='1'){
				this.dsGridCargosEli.add(arrcareli[i]);
			}
		}
		this.dataGridEditable.getStore().remove(registroeli);
		if (registroeli.get('registrocat') == '1') {
			this.dsGridEli.add(registroeli);
		}
		
		fncajustaAsiento(registroeli,arrcareli,'E');
	}
	//fin creando funcion para eliminar registros de las grids
	
	//Creando grid de datos que se llenara con el catalgo
	this.dataGridEditable =new Ext.grid.EditorGridPanel({
        id: options.idgrid,
		width:options.ancho,
        height:options.alto,
       	style:options.posicion,
        title:options.titgrid,
	    ds: this.dsGrid,
       	cm: this.colmodGrid,
       	sm: new Ext.grid.CheckboxSelectionModel({}),
        frame:true,
       	viewConfig: {forceFit:true},
        columnLines: true,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar un registro',
            iconCls:'agregar',
            id:'agregar',
			handler: this.mostrarVentana.createDelegate(this)
        	}, '-', {
            text:'Eliminar',
            tooltip:'Eliminar un registro',
            iconCls:'remover',
            id:'remover',
			handler: this.eliminarRegistro.createDelegate(this)
			}]
	    
	});
	//Fin creando grid de datos que se llenara con el catalgo
	
	//creando funcion para calcular el monto total por cada bien
	function calcularCargo(registroart,subtotal){
		var cargototal = 0;
		this.dsGridCargos.each(function (registrogrid){
	 			var cargo = 0;
				if (registrogrid.get('coditem') == registroart.get('coditem')){
					var formula = registrogrid.get('formula');
					formula	    = formula.replace("$LD_MONTO",subtotal);
			        cargo		= eval(formula);
					cargototal	= cargototal + cargo;
					registrogrid.set('basimp',formatoNumericoMostrar(subtotal,2,'.',',','','','-',''));
					registrogrid.set('moncar',formatoNumericoMostrar(cargo,2,'.',',','','','-',''));
					registrogrid.set('totcar',formatoNumericoMostrar(subtotal+cargo,2,'.',',','','','-',''));
				}
				
 		});
		
		return cargototal;
	}
	
	this.calcularTotal = function(){
		var cantidad      = 0;
		var precio        = 0;
		var funcCalcCargo = calcularCargo.createDelegate(this);//creando el delegado con el scope this
		var registro      = arguments[0].record;
		
		if (registro.get('canitem') != '') {
			cantidad = parseFloat(ue_formato_operaciones(registro.get('canitem')));
		}
		if (registro.get('preitem') != '') {
			precio = parseFloat(ue_formato_operaciones(registro.get('preitem')));
		}
		if (registro.get('unidad') != '' && registro.get('unidad') != null) {
			unidad = parseFloat(ue_formato_operaciones(registro.get('unidad')));
		}
		
		if (registro.get('moduni') != null) {
			if(registro.get('moduni')=='D'){
				var subtotal = cantidad * precio;
			}
			else if(registro.get('moduni')=='M'){
				var subtotal = (cantidad * precio)*unidad;
			}
		}else{
			var subtotal = cantidad * precio;
		}
		
		
		var totalcargo = funcCalcCargo(registro,subtotal);//haciendo el llamado a la funcion delegada
		var total = subtotal + totalcargo;
		registro.set('subtot',formatoNumericoMostrar(subtotal,2,'.',',','','','-',''));
		registro.set('moncar',formatoNumericoMostrar(totalcargo,2,'.',',','','','-',''));
		registro.set('montot',formatoNumericoMostrar(total,2,'.',',','','','-',''));
		
		if(this.banderaasiento){
			var fncbuscarCargo   = buscarCargo.createDelegate(this);
			var fncajustaAsiento = this.fnAjustarAsiento.createDelegate(this);
			var arrcargos = fncbuscarCargo(registro);
			fncajustaAsiento(registro,arrcargos,'A');
		}
		
	}
	//Fin creando funcion para calcular el monto total por cada bien
	
	//agregando listener a la grid para ejecutar la funcion calcular monto		
	this.dataGridEditable.on({
                    'afteredit': {
                        fn: this.calcularTotal.createDelegate(this,arguments,1)
                    }
                });
	//Fin agregando listener a la grid para ejecutar la funcion calcular monto
	
	//creando grid para los cargos
	this.dataGridCargos = new Ext.grid.GridPanel({
        	id: 'gridcargos',
			width:options.ancho,
        	height:150,
       		style:options.posgridcargos,
        	title:'Cr&#233;ditos',
	    	ds: this.dsGridCargos,
       		cm: this.colmodGridCargos,
       		frame:true,
       		viewConfig: {forceFit:true},
        	columnLines: true
		});
	//fin creando grid para los cargos
	
	//creando datastore para la grid de cuentas 
	var registro_cuenta = Ext.data.Record.create([
			{name: 'estpro'},    
			{name: 'codcue'},
			{name: 'moncue'},
			{name: 'registrocat'}
		]);
	
	this.dsGridCuentas =   new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "estpro"},registro_cuenta)
		});
	//fin creando datastore para la grid de cuentas 
				
	//creando columnmodel para la grid de cargos
	var colmodGridCuenta = new Ext.grid.ColumnModel([
					{header: "Estructura Programatica", width: 30, sortable: true, dataIndex: 'estpro'},
					{header: "Cuenta", width: 40, sortable: true, dataIndex: 'codcue'},
					{header: "Monto", width: 25, sortable: true, dataIndex: 'moncue'}
    	]);
	//fin creando columnmodel para la grid de cargos
	
	//creando grid para asiento presupuestario
	this.dataGridCuentas = new Ext.grid.GridPanel({
        	id: 'gridcuentas',
			width:options.ancho,
        	height:150,
       		style:options.posgridcuentas,
        	title:'Cuentas',
	    	ds: this.dsGridCuentas,
       		cm: colmodGridCuenta,
       		frame:true,
       		viewConfig: {forceFit:true},
        	columnLines: true
		});
	//fin creando grid para asiento presupuestario
	
	//creando datastore para la grid de cuentas creditos y para eliminar de cuentas
	var registro_cuentacre = Ext.data.Record.create([
			{name: 'codcar'},
			{name: 'estpro'},    
			{name: 'codcue'},
			{name: 'moncue'},
			{name: 'registrocat'}
		]);
	
	this.dsGridCuentasCred =   new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "codcar"},registro_cuentacre)
		});
	
	this.dsGridCuentasEli =   new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "estpro"},registro_cuentacre)
		});
	//fin creando datastore para la grid de cuentas y para eliminar de cuentas
				
	//creando columnmodel para la grid de cargos
	var colmodGridCuentacre = new Ext.grid.ColumnModel([
					{header: "Credito", width: 30, sortable: true, dataIndex: 'codcar'},
					{header: "Estructura Programatica", width: 30, sortable: true, dataIndex: 'estpro'},
					{header: "Cuenta", width: 40, sortable: true, dataIndex: 'codcue'},
					{header: "Monto", width: 25, sortable: true, dataIndex: 'moncue'}
    	]);
	//fin creando columnmodel para la grid de cargos
	
	//creando grid para asiento presupuestario de los cargos
	this.dataGridCuentascre = new Ext.grid.GridPanel({
        	id: 'gridcuentascar',
			width:options.ancho,
        	height:150,
       		style:options.posgridcuentascar,
        	title:'Cuentas otros creditos',
	    	ds: this.dsGridCuentasCred,
       		cm: colmodGridCuentacre,
       		frame:true,
       		viewConfig: {forceFit:true},
        	columnLines: true
		});
	//fin creando grid para asiento presupuestario de los cargos
	
	//creando fieldset de totales de la sep
	this.fieldsetTotales = new Ext.form.FieldSet({
					width:500,
					height:120,
					title:'Totales',
					id:'fstotales',
					border:true,
					style:options.postotales,
			    	items:[{
								xtype: 'textfield',
								fieldLabel: 'Subtotal',
								labelSeparator :'',
								style:'align:left',
								id: 'subtot',
								width: 150
							},{
								xtype: 'textfield',
								fieldLabel: 'Otros cr&#233;ditos',
								labelSeparator :'',
								id: 'moncre',
								width: 150
							},{
								xtype: 'textfield',
								fieldLabel: 'Total General',
								labelSeparator :'',
								id: 'totgen',
								width: 150
							}]
					});
	//creando fieldset de totales de la sep
	
	//funcion para setear el formulario de la vista
	this.fnSetFromulario = function(formulario){
		this.formVista=formulario;
	}
	//fin funcion para setear el formulario de la vista
	
	//funcion para setear el formulario de la vista
	this.fndoLayout = function(){
		this.formVista.doLayout();
	}
	//fin funcion para setear el formulario de la vista
	
	
	//creando funcion crear asiento
	this.fnCrearAsiento = function(){
		var totart = 0;
		var totcar = 0;
		var totgen = 0;
		var arrest = new Array();
		arrest[1]  = this.formVista.getComponent('codestpro1').getValue();
		arrest[2]  = this.formVista.getComponent('codestpro2').getValue();
		arrest[3]  = this.formVista.getComponent('codestpro3').getValue();
		arrest[4]  = this.formVista.getComponent('codestpro4').getValue();
		arrest[5]  = this.formVista.getComponent('codestpro5').getValue();
		
		var estructura = formatoEstructura(arrest,arrlon,cantnivel);
		
		//asiento presupuestario por los articulos
		for (var i = 0; i <= this.dsGrid.getCount() - 1; i++) {
			var subtot = parseFloat(ue_formato_operaciones(this.dsGrid.getAt(i).get('subtot')));
			totart = totart + subtot;
			var cuenta = new registro_cuenta({
						'estpro':estructura,
						'codcue':this.dsGrid.getAt(i).get('spg_cuenta'),
						'moncue':this.dsGrid.getAt(i).get('subtot')
				});
				
			if(validarExistenciaRegistroStore(cuenta,this.dsGridCuentas,['codcue'],['codcue'])){
				this.dsGridCuentas.insert(0,cuenta);
			}
			else{
				var indice = this.dsGridCuentas.find('codcue',this.dsGrid.getAt(i).get('spg_cuenta'));
				var moncue = parseFloat(ue_formato_operaciones(this.dsGridCuentas.getAt(indice).get('moncue')));
				var acumulado = moncue + subtot;
				this.dsGridCuentas.getAt(indice).set('moncue',formatoNumericoMostrar(acumulado,2,'.',',','','','-',''));
			}
			
		}
		this.formVista.add(this.dataGridCuentas);
		this.formVista.doLayout();
		//fin asiento presupuestario por los articulos
		
		//asiento presupuestario por los cargos de los articulos
		for (var i = 0; i <= this.dsGridCargos.getCount() - 1; i++) {
			var subtotcar = parseFloat(ue_formato_operaciones(this.dsGridCargos.getAt(i).get('moncar')));
			totcar        = totcar + subtotcar;
			var cuentacar = new registro_cuentacre({
						'codcar':this.dsGridCargos.getAt(i).get('codcar'),
						'estpro':estructura,
						'codcue':this.dsGridCargos.getAt(i).get('spg_cuenta'),
						'moncue':this.dsGridCargos.getAt(i).get('moncar')
				});
				
			if(validarExistenciaRegistroStore(cuentacar,this.dsGridCuentasCred,['codcue'],['codcue'])){
				this.dsGridCuentasCred.insert(0,cuentacar);
			}
			else{
				var indicecar = this.dsGridCuentasCred.find('codcue',this.dsGridCargos.getAt(i).get('spg_cuenta'));
				var moncuecar = parseFloat(ue_formato_operaciones(this.dsGridCuentasCred.getAt(indicecar).get('moncue')));
				var acumulcar = moncuecar + subtotcar;
				this.dsGridCuentasCred.getAt(indicecar).set('moncue',formatoNumericoMostrar(acumulcar,2,'.',',','','','-',''));
			}
			
		}
		
		totgen = totart + totcar;
		
		this.formVista.add(this.dataGridCuentascre);
		this.fieldsetTotales.getComponent('subtot').setValue(formatoNumericoMostrar(totart,2,'.',',','','','-',''));
		this.fieldsetTotales.getComponent('moncre').setValue(formatoNumericoMostrar(totcar,2,'.',',','','','-',''));
		this.fieldsetTotales.getComponent('totgen').setValue(formatoNumericoMostrar(totgen,2,'.',',','','','-',''));
		this.formVista.add(this.fieldsetTotales);
		this.formVista.doLayout();
		this.banderaasiento = true;
		//fin asiento presupuestario por los cargos de los articulos
	}
	//fin creando funcion crear asiento
	
	//creando boton que dispara funcion crearasiento
	this.botcrearasiento = new Ext.Button({
								id:'botcrearasiento',
								text: 'Crear Asiento',
								style:options.posbotcras,
								handler: this.fnCrearAsiento.createDelegate(this)
			});
	//fin creando boton que dispara funcion crearasiento
	
	//creando funcion ajustar asiento
	this.fnAjustarAsiento = function(registroitem,arrcargos,accion){
		var fnactulizartotales        = this.fnactualizarTotales.createDelegate(this);		
		var fnactulizartotalescuentas = this.fnactualizarTotalesCuentas.createDelegate(this);
		
		//ajuste asiento presupuestario de los articulos
		var indice    = this.dsGridCuentas.find('codcue',registroitem.get('spg_cuenta'));
		var montoitem = parseFloat(ue_formato_operaciones(registroitem.get('subtot')));
		var moncue    = parseFloat(ue_formato_operaciones(this.dsGridCuentas.getAt(indice).get('moncue')));
		if(accion == 'A'){
			fnactulizartotalescuentas(registroitem);
		}
		else if(accion == 'E'){
			var monrescue = moncue-montoitem;
			if( monrescue == 0){
				if(registroitem.get('registrocat')=='1'){
					this.dsGridCuentasEli.add(this.dsGridCuentas.getAt(indice))
				}
				this.dsGridCuentas.remove(this.dsGridCuentas.getAt(indice));
			}
			else{
				this.dsGridCuentas.getAt(indice).set('moncue',formatoNumericoMostrar(monrescue,2,'.',',','','','-',''));
			}	
		}
		//fin ajuste asiento presupuestario de los articulos
		
		//ajuste asiento presupuestario por los cargos de los articulos
		for (var i = arrcargos.length - 1; i >= 0; i--) {
			var indicecar = this.dsGridCuentasCred.find('codcue',arrcargos[i].get('spg_cuenta'));
			var montocar = parseFloat(ue_formato_operaciones(arrcargos[i].get('moncar')));
			var moncuecar = parseFloat(ue_formato_operaciones(this.dsGridCuentasCred.getAt(indicecar).get('moncue')));
			if (accion == 'E') {
				var monrescuecar = moncuecar - montocar;
				if (monrescuecar == 0) {
					if(this.dsGridCuentasCred.getAt(indicecar).get('registrocat')=='1'){
						this.dsGridCuentasEli.add(this.dsGridCuentasCred.getAt(indicecar));	
					}
					this.dsGridCuentasCred.remove(this.dsGridCuentasCred.getAt(indicecar));
				}
				else {
					this.dsGridCuentasCred.getAt(indicecar).set('moncue', formatoNumericoMostrar(monrescuecar, 2, '.', ',', '', '', '-', ''));
				}
			}	
		}
		//fin ajuste asiento presupuestario por los cargos de los articulos
		
		//ajustando los totales de la sep
		fnactulizartotales();
		//fin ajustando los totales de la sep
	}
	//fin creando funcion ajustar asiento
	
	//creando funcion que actualiza los totales
	this.fnactualizarTotales = function(){
		var totalart = 0;
		var totalcar = 0;
		var totalgen = 0;
		
		this.dsGrid.each(function (registroitem){
	 			var monart  = parseFloat(ue_formato_operaciones(registroitem.get('subtot')));
				totalart	= totalart + monart;
		});
		
		this.dsGridCargos.each(function (registrocar){
	 			var moncar = parseFloat(ue_formato_operaciones(registrocar.get('moncar')));
				totalcar	= totalcar + moncar;
		});
		
		totalgen = totalart + totalcar;
		this.fieldsetTotales.getComponent('subtot').setValue(formatoNumericoMostrar(totalart,2,'.',',','','','-',''));
		this.fieldsetTotales.getComponent('moncre').setValue(formatoNumericoMostrar(totalcar,2,'.',',','','','-',''));
		this.fieldsetTotales.getComponent('totgen').setValue(formatoNumericoMostrar(totalgen,2,'.',',','','','-',''));
	}
	//fin creando funcion actualiza los totales
	
	//creando funcion que actualiza los totales grid cuentas y cuentas cargos
	this.fnactualizarTotalesCuentas = function(registroact){
		var totalart = 0;
		
		
		this.dsGrid.each(function (registroitem){
	 			if (registroact.get('spg_cuenta') == registroitem.get('spg_cuenta')) {
					var monart = parseFloat(ue_formato_operaciones(registroitem.get('subtot')));
					totalart = totalart + monart;
				}
		});
		
		var indice    = this.dsGridCuentas.find('codcue',registroact.get('spg_cuenta'));
		this.dsGridCuentas.getAt(indice).set('moncue',formatoNumericoMostrar(totalart,2,'.',',','','','-',''));
		
		var fncbuscarCargo  = buscarCargo.createDelegate(this);
		var arrcargo 		= fncbuscarCargo(registroact);
		
		for (var i = arrcargo.length - 1; i >= 0; i--) {
			var totalcar = 0;
			this.dsGridCargos.each(function (registrocar){
	 			if (registrocar.get('spg_cuenta') == arrcargo[i].get('spg_cuenta')) {
					var moncar = parseFloat(ue_formato_operaciones(registrocar.get('moncar')));
					totalcar = totalcar + moncar;
				}
			});
		    var indice    = this.dsGridCuentasCred.find('codcue',arrcargo[i].get('spg_cuenta'));
			this.dsGridCuentasCred.getAt(indice).set('moncue',formatoNumericoMostrar(totalcar,2,'.',',','','','-',''));
		}
		
	}
	//fin creando funcion actualiza los totales  grid cuentas y cuentas cargos
	
}//Fin componente lista editable catalogo

