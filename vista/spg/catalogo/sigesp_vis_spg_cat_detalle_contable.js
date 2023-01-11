/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

	function AgregarCuentas(contador,grid,procede)
	{
		
		//Creando el campo de cuenta contable
		var reCuentaContable = Ext.data.Record.create([
			{name: 'sc_cuenta'}, //campo obligatorio                             
			{name: 'denominacion'}, //campo obligatorio
			{name: 'status'}
		]);
			
		//componente catalogo de cuenta contable
		comcampocatcuentacontable = new com.sigesp.vista.comCatalogoCuentaContable({
			idComponente:'scf'+contador,
			anchofieldset: 900,
			validarCuenta:true,
			valorStatus: '',
			reCatalogo: reCuentaContable,
			rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcuentacontable.php',
			parametros: "ObjSon={'operacion': 'buscarCuentaContables'",
			posicion:'position:absolute;left:5px;top:125px', 
			tittxt:'Cuenta Contable',
			idtxt:'cuenta_con',
			campovalue:'sc_cuenta',
			anchoetiquetatext:215,
			anchotext:150,
			anchocoltext:0.43, 
			idlabel:'deno_cuenta',
			labelvalue:'denominacion',
			anchocoletiqueta:0.35, 
			anchoetiqueta:300,
			binding:'C',
			hiddenvalue:'',
			defaultvalue:'---',
			allowblank:false,
			numFiltroNoVacio: 1
		});
		//fin componente catalogo de cuenta contable
		
		//creando store para la operacion
		var operacontable = [['Debe','D'],
		                     ['Haber','H']
	                  		]; // Arreglo que contiene los Documentos que se pueden controlar
		
		var stoperacontable = new Ext.data.SimpleStore({
			fields : ['etiqueta','valor'],
			data : operacontable
		});
		//fin creando store para el combo operacion
	
		//creando objeto combo operacion
		var cmboperacontable = new Ext.form.ComboBox({
			store : stoperacontable,
			fieldLabel : 'Operaci&#243;n',
			labelSeparator : '',
			editable : false,
			emptyText:'Debe',
			displayField : 'etiqueta',
			valueField : 'valor',
			id : 'codopecont', // falta colocar el campo id correctamente
			width : 150,
			typeAhead : true,
			triggerAction : 'all',
			forceselection : true,
			binding : true,
			mode : 'local'
		});
		
		//Creacion del formulario de agregar presupuesto
		var frmAgregarPresupuesto = new Ext.FormPanel({
			width: 870,
			height: 235, 
			style: 'position:absolute;left:5px;top:0px',
			frame: true,
			autoScroll:false,
			items:[{
				xtype:"fieldset", 
				title:'Datos del Documento',
				border:true,
				width: 850,
				height: 210,
				cls: 'fondo',
				items:[{
					style:'position:absolute;left:15px;top:15px',
					layout:"column",
					defaults:{border: false},
					items: [{
						layout:"form",
						border:false,
						labelWidth:215,
						items: [{
							xtype:'textfield',
							labelSeparator:'',
							fieldLabel:'Documento',
							name:'docgasto',
							id:'agrdocing',	
							autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
							width: 185,
							value:Ext.getCmp('comprobante').getValue(),
						}]
					}]
				},
				{
					style:'position:absolute;left:15px;top:45px',
					layout:"column",
					defaults:{border: false},
					items: [{
						layout:"form",
						border:false,
						labelWidth:215,
						items: [{
							xtype:'textfield',
							labelSeparator:'',
							fieldLabel:'Descripci&#243;n',
							autoCreate: {tag: 'input', type: 'text', size: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.;,!@%&/\()�?�-+*[]{}');"},
							name:'desgasto',
							id:'catdesing',									
							width: 600,
							value:Ext.getCmp('descripcion').getValue(),
						}]
					}]
				},
				{
					style:'position:absolute;left:15px;top:75px',
					layout:"column",
					defaults:{border: false},
					items: [{
						layout:"form",
						border:false,
						labelWidth:215,
						items: [{
							xtype:'textfield',
							labelSeparator:'',
							fieldLabel:'Procedencia',
							name:'progasto',
							id:'catproing',										
							width: 185,
							readOnly:true,
							value:procede
						}]
					}]
				},
				{
					style:'position:absolute;left:15px;top:105px',
					layout:"column",
					defaults:{border: false},
					items: [{
						layout:"form",
						border:false,
						labelWidth:215,
						items: [cmboperacontable]
					}]
				},comcampocatcuentacontable.fieldsetCatalogo,
				{
					style:'position:absolute;left:15px;top:165px',
					layout:"column",
					defaults:{border: false},
					items: [{
						layout:"form",
						border:false,
						labelWidth:215,
						items: [{
							xtype:'textfield',
							labelSeparator:'',
							fieldLabel:'Monto',
							name:'mongasto',
							id:'catmoning',											
							width: 185,
							autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'0123456789.');"},
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
				}]
			}]  
		});

		var ventanaAgregarPresupuesto = new Ext.Window({
			title: "<H1 align='center'>Entrada de Movimientos Contables</H1>",
			width:880,
			height:300, 
			modal: true,
			closable:false,
			plain: false,
			frame:true,
			items:[frmAgregarPresupuesto],
			buttons: [{
				text:'Aceptar',  
				handler: function(){
					if(Ext.getCmp('agrdocing').getValue()=='' || Ext.getCmp('catdesing').getValue()=='' ||
					   Ext.getCmp('catmoning').getValue()=='' || Ext.getCmp('cuenta_con').getValue()==''){
						Ext.Msg.show({
							title:'Mensaje',
							msg:'Debe completar todos los datos',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
					else{
						var operacion = 'D';
						if(Ext.getCmp('codopecont').getValue()!=''){
							operacion = Ext.getCmp('codopecont').getValue();
						}
						var reDetCon = Ext.data.Record.create([
						   	{name: 'sc_cuenta'},
						   	{name: 'denominacion'},
						   	{name: 'procede'},
						   	{name: 'operacion'},
						   	{name: 'documento'},
						   	{name: 'monto'},
						]);
						var detgasInt = new reDetCon({
							'sc_cuenta':Ext.getCmp('cuenta_con').getValue(),
							'documento':Ext.getCmp('agrdocing').getValue(),
							'descripcion':Ext.getCmp('catdesing').getValue(),
							'procede_doc':Ext.getCmp('catproing').getValue(),
							'operacion':operacion,
							'monto':Ext.getCmp('catmoning').getValue()
						});
						if(grid.getStore().getCount()==0){
							grid.store.insert(0,detgasInt);
						}
						else{
							var entro=false;
							grid.store.each(function (reDetCon){
								if(reDetCon.get('sc_cuenta')==Ext.getCmp('cuenta_con').getValue() &&
								   reDetCon.get('operacion')==operacion && 
								   Ext.getCmp('agrdocing').getValue()==reDetCon.get('documento')){
									var total = parseFloat(ue_formato_operaciones(reDetCon.get('monto')));
									var montocont = parseFloat(ue_formato_operaciones(Ext.getCmp('catmoning').getValue()));
									reDetCon.set('monto',formatoNumericoMostrar(total+montocont,2,'.',',','','','-',''));
									entro=true;
								}
							})
							if(!entro){
								grid.store.insert(0,detgasInt);
							}
						}
						acumularTotalContable();
						ventanaAgregarPresupuesto.close();
					}	
				}
			},{
		   		text: 'Salir',
	   			handler:function(){
					ventanaAgregarPresupuesto.close();
	   		    }
	   		}]
		});
		ventanaAgregarPresupuesto.show();
	}
	//FIN DEL FORMULARIO CONTABLE//