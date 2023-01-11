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
 
//Objeto que construye el fieldset para seleccionar una estructura presupuestaria
com.sigesp.vista.comFSEstructuraFuenteCuenta =  function(options)
{
	this.fsEstructura = null;
	this.fnOnAceptar  = options.fnOnAceptar;
	this.CuentaMovimiento    = 1;
	this.grupocuenta    = '';	
	this.filtrosindistintos = 0;
	if(options.CuentaMovimiento != undefined)
	{
		this.CuentaMovimiento    = options.CuentaMovimiento;	
	}
	if(options.filtrosindistintos != undefined)
	{
		this.filtrosindistintos = options.filtrosindistintos;
	}
	this.operacion    = '';
	
	this.mostrarNumDigNiv1=function(estructura)
	{
		return estructura.substr(-empresa['loncodestpro1'])
	}
	
	this.mostrarNumDigNiv2=function(estructura)
	{
		return estructura.substr(-empresa['loncodestpro2'])
	}

	this.mostrarNumDigNiv3=function(estructura)
	{
		return estructura.substr(-empresa['loncodestpro3'])
	}

	this.mostrarNumDigNiv4=function(estructura)
	{
		return estructura.substr(-empresa['loncodestpro4'])
	}

	this.mostrarNumDigNiv5=function(estructura)
	{
		return estructura.substr(-empresa['loncodestpro5'])
	}
	
	this.limpiarEstructuras=function(nivel)
	{
		var contador = nivel +1;
		for(var i=contador; i<parseInt(empresa['numniv']); i++)
		{
			this.fsEstructura.findById('codest'+options.idtxt+i).reset();
			if(this.fsEstructura.findById('denest'+options.idtxt+i) != null)
			{
				this.fsEstructura.findById('denest'+options.idtxt+i).setText('');
			}
		}
		if(!options.sinFuente)
		{
			this.fsEstructura.findById('codfuentefin'+options.idtxt).reset();
			if(this.fsEstructura.findById('denfuentefin'+options.idtxt) != null)
			{
				this.fsEstructura.findById('denfuentefin'+options.idtxt).setText('');
			}
		}
		if(!options.sinCuenta)
		{
			this.fsEstructura.findById('codcuenta'+options.idtxt).reset();
			if(this.fsEstructura.findById('dencuenta'+options.idtxt) != null)
			{
				this.fsEstructura.findById('dencuenta'+options.idtxt).setText('');
			}
		}
	}
	
	this.mensajeValidacionNivel=function(nivel)
	{
		Ext.Msg.show({
		   	title:'Mensaje',
		   	msg: 'No ha seleccionado ninguna estructura del nivel '+empresa['nomestpro'+nivel]+', verifique por favor',
		   	buttons: Ext.Msg.OK,
		   	animEl: 'elId',
		   	icon: Ext.MessageBox.WARNING,
		   	closable:false
		});
	}
	
	this.setDataEstructura = function(operacion)
	{
		switch(arguments[0])
		{//segun el nivel cargamos los disferente resultados en los datastore correspondientes
			case 'nivel1':
				var recordEstNiv1 = this.gridEstructura1.getSelectionModel().getSelected();
				if((recordEstNiv1 != null)||(this.filtrosindistintos==1))
				{
					this.fsEstructura.findById('codest'+options.idtxt+'0').setValue(this.mostrarNumDigNiv1(recordEstNiv1.get('codestpro1')));
					if(this.fsEstructura.findById('denest'+options.idtxt+'0') != null)
					{
						this.fsEstructura.findById('denest'+options.idtxt+'0').setText(recordEstNiv1.get('denestpro1'));	
					}
					this.fsEstructura.findById('estcla'+options.idtxt).setValue(recordEstNiv1.get('estcla'));
					this.limpiarEstructuras(0);
					this.gridEstructura1.destroy();
					this.venCatalogo.destroy();
					if(options.onAceptar)
					{
						this.fnOnAceptar();
					}
				}
				else
				{
					this.mensajeValidacionNivel(1);
				}
				break;
				
			case 'nivel2':
				var recordEstNiv2 = this.gridEstructura2.getSelectionModel().getSelected();
				if((recordEstNiv2 != null)||(this.filtrosindistintos==1))
				{
					this.fsEstructura.findById('codest'+options.idtxt+'1').setValue(this.mostrarNumDigNiv2(recordEstNiv2.get('codestpro2')));
					if(this.fsEstructura.findById('denest'+options.idtxt+'1') != null)
					{
						this.fsEstructura.findById('denest'+options.idtxt+'1').setText(recordEstNiv2.get('denestpro2'));	
					}
					this.limpiarEstructuras(1);
					this.gridEstructura2.destroy();
					this.venCatalogo.destroy();
					if(options.onAceptar)
					{
						this.fnOnAceptar();
					}
				}
				else
				{
					this.mensajeValidacionNivel(2);
				}
				break;
				
			case 'nivel3':
				var recordEstNiv3 = this.gridEstructura3.getSelectionModel().getSelected();
				if((recordEstNiv3 != null)||(this.filtrosindistintos==1))
				{
					this.fsEstructura.findById('codest'+options.idtxt+'2').setValue(this.mostrarNumDigNiv3(recordEstNiv3.get('codestpro3')));
					if(this.fsEstructura.findById('denest'+options.idtxt+'2') != null)
					{
						this.fsEstructura.findById('denest'+options.idtxt+'2').setText(recordEstNiv3.get('denestpro3'));	
					}
					this.limpiarEstructuras(2);
					this.gridEstructura3.destroy();
					this.venCatalogo.destroy();
					if(options.onAceptar)
					{
						this.fnOnAceptar();
					}
				}
				else
				{
					this.mensajeValidacionNivel(3);
				}
				break;
				
			case 'nivel4':
				var recordEstNiv4 = this.gridEstructura4.getSelectionModel().getSelected();
				if((recordEstNiv4 != null)||(this.filtrosindistintos==1))
				{
					this.fsEstructura.findById('codest'+options.idtxt+'3').setValue(this.mostrarNumDigNiv4(recordEstNiv4.get('codestpro4')));
					if(this.fsEstructura.findById('denest'+options.idtxt+'3') != null)
					{
						this.fsEstructura.findById('denest'+options.idtxt+'3').setText(recordEstNiv4.get('denestpro4'));	
					}
					this.limpiarEstructuras(3);
					this.gridEstructura4.destroy();
					this.venCatalogo.destroy();
					if(options.onAceptar)
					{
						this.fnOnAceptar();
					}
				}
				else
				{
					this.mensajeValidacionNivel(4);
				}
				break;
				
			case 'nivel5':
				var recordEstNiv5 = this.gridEstructura5.getSelectionModel().getSelected();
				if((recordEstNiv5 != null)||(this.filtrosindistintos==1))
				{
					this.fsEstructura.findById('codest'+options.idtxt+'4').setValue(this.mostrarNumDigNiv5(recordEstNiv5.get('codestpro5')));
					if(this.fsEstructura.findById('denest'+options.idtxt+'4') != null)
					{
						this.fsEstructura.findById('denest'+options.idtxt+'4').setText(recordEstNiv5.get('denestpro5'))
					}
					this.gridEstructura5.destroy();
					this.venCatalogo.destroy();
					if(options.onAceptar)
					{
						this.fnOnAceptar();
					}
				}
				else
				{
					this.mensajeValidacionNivel(5);
				}
				break;
				
			case 'nivelN':
				var recordEstNivN = this.gridEstructuraN.getSelectionModel().getSelected();
				if((recordEstNivN != null)||(this.filtrosindistintos==1))
				{
					if(this.filtrosindistintos ==0)
					{
						for (var i = parseInt(empresa['numniv']) - 1 ; i >= 0; i--)
						{
							var estructura = "";
							switch(i)
							{
								case 4: estructura=this.mostrarNumDigNiv5(recordEstNivN.get('codestpro'+(i+1)));
								break;
								
								case 3: estructura=this.mostrarNumDigNiv4(recordEstNivN.get('codestpro'+(i+1)));
								break;
								
								case 2: estructura=this.mostrarNumDigNiv3(recordEstNivN.get('codestpro'+(i+1)));
								break;
								
								case 1: estructura=this.mostrarNumDigNiv2(recordEstNivN.get('codestpro'+(i+1)));
								break;
								
								case 0: estructura=this.mostrarNumDigNiv1(recordEstNivN.get('codestpro'+(i+1)));
								break;
							}
							
							this.fsEstructura.findById('codest'+options.idtxt+i).setValue(estructura);
							if(this.fsEstructura.findById('denest'+options.idtxt+i) != null)
							{
								this.fsEstructura.findById('denest'+options.idtxt+i).setText(recordEstNivN.get('denestpro'+(i+1)))
							}
						};
					}
					else
					{
						var i = parseInt(empresa['numniv']);
						if (i == 3)
						{
							estructura=this.mostrarNumDigNiv3(recordEstNivN.get('codestpro'+(i)));	
						}
						else
						{
							estructura=this.mostrarNumDigNiv5(recordEstNivN.get('codestpro'+(i)));	
						}
						this.fsEstructura.findById('codest'+options.idtxt+(i-1)).setValue(estructura);
						if(this.fsEstructura.findById('denest'+options.idtxt+(i-1)) != null)
						{
							this.fsEstructura.findById('denest'+options.idtxt+(i-1)).setText(recordEstNivN.get('denestpro'+(i)))
						}
					}
					
					this.fsEstructura.findById('estcla'+options.idtxt).setValue(recordEstNivN.get('estcla'));
					this.gridEstructuraN.destroy();
					this.venCatalogo.destroy();
					if(options.onAceptar)
					{
						this.fnOnAceptar();
					}
				}
				break;
				
			case 'fuente':
				var recordFueFin = this.gridFuenteFinanciamiento.getSelectionModel().getSelected();
				if(recordFueFin != null)
				{
					this.fsEstructura.findById('codfuentefin'+options.idtxt).setValue(recordFueFin.get('codfuefin'));
					if(this.fsEstructura.findById('denfuentefin'+options.idtxt) != null)
					{
						this.fsEstructura.findById('denfuentefin'+options.idtxt).setText(recordFueFin.get('denfuefin'))
					}

					this.gridFuenteFinanciamiento.destroy();
					this.venCatalogo.destroy();

					if(options.onAceptar)
					{
						this.fnOnAceptar();
					}

				}
				else
				{
					Ext.Msg.show({
					   	title:'Mensaje',
					   	msg: 'No ha seleccionado una fuente de financiamiento, verifique por favor',
					   	buttons: Ext.Msg.OK,
					   	animEl: 'elId',
					   	icon: Ext.MessageBox.WARNING,
					   	closable:false
					});
				}
				break;
				
			case 'cuenta':
				var recordCuenta = this.gridCuenta.getSelectionModel().getSelected();
				if(recordCuenta != null)
				{
					this.fsEstructura.findById('codcuenta'+options.idtxt).setValue(recordCuenta.get('spg_cuenta'));
					if(options.datosocultos==1)
					{
						for (var i = options.camposocultos.length - 1; i >= 0; i--)
						{
							Ext.getCmp(options.camposocultos[i]).setValue(recordCuenta.get(options.camposocultos[i]));
						}
					}
					if(this.fsEstructura.findById('dencuenta'+options.idtxt) != null)
					{
						this.fsEstructura.findById('dencuenta'+options.idtxt).setText(recordCuenta.get('denominacion'))
					}
					this.gridCuenta.destroy();
					this.venCatalogo.destroy();
					if(options.onAceptar)
					{
						this.fnOnAceptar();
					}
				}
				else
				{
					Ext.Msg.show({
					   	title:'Mensaje',
					   	msg: 'No ha seleccionado una cuenta presupuestaria de gasto, verifique por favor',
					   	buttons: Ext.Msg.OK,
					   	animEl: 'elId',
					   	icon: Ext.MessageBox.WARNING,
					   	closable:false
					});
				}
				break;
		}
	}
	
		
	this.cargarData = function(){
		var datos = arguments[0].responseText;
		var objSpgEp = eval('(' + datos + ')');//objeto nivel 1
		var contenidoMensaje = 'No existen datos para mostrar';
		if(objSpgEp != '')
		{
			if(objSpgEp.raiz == null || objSpgEp.raiz =='')
			{
				Ext.MessageBox.show({
		 			title:'Advertencia',
		 			msg: contenidoMensaje,
		 			buttons: Ext.Msg.OK,
		 			icon: Ext.MessageBox.WARNING
		 		});
			}
			else
			{
				switch(this.operacion)
				{//segun el nivel cargamos los disferente resultados en los datastore correspondientes
					case 'nivel1':
						this.gridEstructura1.store.loadData(objSpgEp);
						break;
					case 'nivel2':
						this.gridEstructura2.store.loadData(objSpgEp);
						break;
					case 'nivel3':
						this.gridEstructura3.store.loadData(objSpgEp);
						break;
					case 'nivel4':
						this.gridEstructura4.store.loadData(objSpgEp);
						break;
					case 'nivel5':
						this.gridEstructura5.store.loadData(objSpgEp);
						break;
					case 'nivelN':
						this.gridEstructuraN.store.loadData(objSpgEp);
						break;
					case 'fuente':
						this.gridFuenteFinanciamiento.store.loadData(objSpgEp);
						break;
					case 'cuenta':
						this.gridCuenta.store.loadData(objSpgEp);
						break;
				}
			}
		}
		else
		{
			Ext.MessageBox.show({
		 		title:'Advertencia',
		 		msg: contenidoMensaje,
		 		buttons: Ext.Msg.OK,
		 		icon: Ext.MessageBox.WARNING
		 	});
		}
	}
	
	this.enviarOperacion=function(operacion)
	{
		var cadenaJson = "{'operacion':'" + operacion + "'," +
				         "'cantnivel':'" + parseInt(empresa['numniv']) + "'," +
				         "'CuentaMovimiento':" + this.CuentaMovimiento + "," +
				         "estcla:'"+this.fsEstructura.findById('estcla'+options.idtxt).getValue()+"',";
		for (var i = 0;i<parseInt(empresa['numniv']);i++)
		{
			if(i==parseInt(empresa['numniv'])-1)
			{
				cadenaJson= cadenaJson + "'codest"+i+"':'" + String.leftPad(this.fsEstructura.findById('codest'+options.idtxt+i).getValue(),25,'0') + "'}";
			}
			else
			{
				cadenaJson= cadenaJson + "'codest"+i+"':'" + String.leftPad(this.fsEstructura.findById('codest'+options.idtxt+i).getValue(),25,'0') + "',";
			}
		}
		this.operacion = operacion;
		var parametros = 'ObjSon='+cadenaJson; 
		Ext.Ajax.request({
			url : '../../controlador/spg/sigesp_ctr_spg_comfsestructurafuentecuenta.php',
			params : parametros,
			method: 'POST',
			success: this.cargarData.createDelegate(this, arguments, 2)
		});
		
		
	}
	
	this.buscarCuenta = function ()
	{
		var codcuenta = Ext.getCmp('codcuenta').getValue();
		var dencuenta = Ext.getCmp('dencuenta').getValue();
		var codcuentascg = Ext.getCmp('codcuentascg').getValue();
		var cadenaJson = "{'operacion':'cuenta'," +
				         "'cantnivel':'" + parseInt(empresa['numniv']) + "'," +
				         "'CuentaMovimiento':" + this.CuentaMovimiento + "," +
				         "estcla:'"+this.fsEstructura.findById('estcla'+options.idtxt).getValue()+"'," +
				         "codfuefin:'"+this.fsEstructura.findById('codfuentefin'+options.idtxt).getValue()+"'," +
				         "codcuenta:'"+codcuenta+"',dencuenta:'"+dencuenta+"',codcuentascg:'"+codcuentascg+"',";
		for (var i = 0;i<parseInt(empresa['numniv']);i++)
		{
			if(i==parseInt(empresa['numniv'])-1)
			{
				cadenaJson= cadenaJson + "'codest"+i+"':'" + String.leftPad(this.fsEstructura.findById('codest'+options.idtxt+i).getValue(),25,'0') + "'";
			}
			else
			{
				cadenaJson= cadenaJson + "'codest"+i+"':'" + String.leftPad(this.fsEstructura.findById('codest'+options.idtxt+i).getValue(),25,'0') + "',";
			}
		}
		if(options.nofiltroest != '' && options.nofiltroest != undefined)
		{
			cadenaJson = cadenaJson + ",'nofiltroest':'"+options.nofiltroest+"'";
		}
		else
		{
			cadenaJson = cadenaJson + ",'nofiltroest':''";
		}
		
		if(options.grupocuenta != '' && options.grupocuenta != undefined)
		{
			cadenaJson = cadenaJson + ",'grupocuenta':'"+options.grupocuenta+"'}";
		}
		else
		{
			cadenaJson = cadenaJson + ",'grupocuenta':''}";
		}
		this.operacion = 'cuenta';
		var parametros = 'ObjSon='+cadenaJson; 
		Ext.Ajax.request({
			url : '../../controlador/spg/sigesp_ctr_spg_comfsestructurafuentecuenta.php',
			params : parametros,
			method: 'POST',
			success: this.cargarData.createDelegate(this, arguments, 2)
		});
	}
	
	this.actualizaGridN = function(cadena,valor,nivel)
	{
		switch(nivel)
		{
			case 'nivel1':
				this.gridEstructura1.store.filter(cadena,valor,true,false);
				break;
			case 'nivel2':
				this.gridEstructura2.store.filter(cadena,valor,true,false);
				break;
			case 'nivel3':
				this.gridEstructura3.store.filter(cadena,valor,true,false);
				break;
			case 'nivel4':
				this.gridEstructura4.store.filter(cadena,valor,true,false);
				break;
			case 'nivel5':
				this.gridEstructura5.store.filter(cadena,valor,true,false);
				break;
			case 'nivelN':
				this.gridEstructuraN.store.filter(cadena,valor,true,false);
				break;
			case 'fuente':
				this.gridFuenteFinanciamiento.store.filter(cadena,valor,true,false);
				break;
			case 'cuenta':
				this.gridCuenta.store.filter(cadena,valor,true,false);
				break;
		}
	}
	
	this.crearStoreEstructuraN = function(numniv)
	{
		var reNivelN = null;
		switch(parseInt(numniv))
		{
			case 1:
				reNivelN = Ext.data.Record.create([
					{name: 'codestpro1'},    
					{name: 'denestpro1'},
					{name: 'estcla'}
				]);
				break;
			case 2:
				reNivelN = Ext.data.Record.create([
					{name: 'codestpro1'},
					{name: 'denestpro1'},
					{name: 'codestpro2'},    
					{name: 'denestpro2'},
					{name: 'estcla'}
				]);
				break;
			case 3:
				reNivelN = Ext.data.Record.create([
					{name: 'codestpro1'},
					{name: 'denestpro1'},
					{name: 'codestpro2'},
					{name: 'denestpro2'},
					{name: 'codestpro3'},    
					{name: 'denestpro3'},
					{name: 'estcla'}
				]);
				break;
			case 4:
				reNivelN = Ext.data.Record.create([
					{name: 'codestpro1'},
					{name: 'denestpro1'},
					{name: 'codestpro2'},
					{name: 'denestpro2'},
					{name: 'codestpro3'},
					{name: 'denestpro3'},
					{name: 'codestpro4'},    
					{name: 'denestpro4'},
					{name: 'estcla'}
				]);
				break;
			case 5:
		    	reNivelN = Ext.data.Record.create([
					{name: 'codestpro1'},
					{name: 'denestpro1'},
					{name: 'codestpro2'},
					{name: 'denestpro2'},
					{name: 'codestpro3'},
					{name: 'denestpro3'},
					{name: 'codestpro4'},
					{name: 'denestpro4'},
					{name: 'codestpro5'},    
					{name: 'denestpro5'},
					{name: 'estcla'}
				]);
				break;
		}
		
		var dsEstructuraNivelN =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reNivelN)
		});
		
		return dsEstructuraNivelN;
	}
	
	this.actualizarGrid1 = function(nomfiltro)
	{
		this.actualizaGridN(arguments[0],arguments[2].value,'nivel1');
	}
	
	this.actualizarGrid2 = function(nomfiltro)
	{
		this.actualizaGridN(arguments[0],arguments[2].value,'nivel2');
	}
	
	this.actualizarGrid3 = function(nomfiltro)
	{
		this.actualizaGridN(arguments[0],arguments[2].value,'nivel3');
	}
	
	this.actualizarGrid4 = function(nomfiltro)
	{
		this.actualizaGridN(arguments[0],arguments[2].value,'nivel4');
	}
	
	this.actualizarGrid5 = function(nomfiltro)
	{
		this.actualizaGridN(arguments[0],arguments[2].value,'nivel5');
	}
	
	this.actualizarGridN = function(nomfiltro)
	{
		this.actualizaGridN(arguments[0],arguments[2].value,'nivelN');
	}
	
	this.actualizarGridFuente = function(nomfiltro)
	{
		this.actualizaGridN(arguments[0],arguments[2].value,'fuente');
	}
	
	this.actualizarGridCuenta = function(nomfiltro)
	{
		this.actualizaGridN(arguments[0],arguments[2].value,'cuenta');
	}
	
	this.obtenerGridCatalogo = function(operacion)
	{
		//aqui creamos los grid....
		switch(operacion)
		{
			case 'nivel1':
				var reSpgEp1 = Ext.data.Record.create([
					{name: 'codestpro1'},    
					{name: 'denestpro1'},
					{name: 'estcla'}
				]);
		
				var dsSpgEp1 =  new Ext.data.Store({reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSpgEp1)});
				
				var fsBusquedaSpgEp1 = new Ext.form.FieldSet({
					xtype:"fieldset", 
					title:'B&#250;squeda',
					width: 600,
					height:100,
					border:true,
					style: 'position:absolute;left:5px;top:5px',
		    		defaults: {labelSeparator:''},
					cls:'fondo',
					items: [{
						xtype: 'textfield',
						fieldLabel: 'C&#243;digo',
		                name: 'C&#243;digo',
						id: 'codestniv1',
						autoCreate: {tag: 'input', type: 'text', maxlength: 25},
						width: 200,
						changeCheck: this.actualizarGrid1.createDelegate(this, ['codestpro1'], 0),							 
						initEvents : function() {
							AgregarKeyPress(this);
						}               
		  			},{
		  				xtype: 'textfield',
		                fieldLabel: 'Denominaci&#243;n',
		                name: 'denominacion',
		                id:'denestniv1',
		                autoCreate: {tag: 'input', type: 'text', maxlength: 254},
						width: 400,
						changeCheck: this.actualizarGrid1.createDelegate(this, ['denestpro1'], 0),							 
						initEvents : function(){
							AgregarKeyPress(this);
						}
					}]
				});
						
				var formBusquedaSpgEp1 = new Ext.FormPanel({
			        labelWidth: 80, 
			        frame:true,
			        width: 630,
					height:130,
			        items: [fsBusquedaSpgEp1]
				});
					    	
				this.gridEstructura1 = new Ext.grid.GridPanel({
					width:770,
		 			height:400,
		 			tbar: formBusquedaSpgEp1,
		 			autoScroll:true,
		 			enableColumnHide: false,
	     			border:true,
	     			ds: dsSpgEp1,
	     			cm: new Ext.grid.ColumnModel([
	          			{header: empresa['nomestpro1'], width: 30, sortable: true,   dataIndex: 'codestpro1', renderer: this.mostrarNumDigNiv1, align:'center'},
	          			{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro1'},
						{header: "Tipo", width: 20, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp}
	       			]),
	       			stripeRows: true,
	      			viewConfig: {forceFit:true},
	      			listeners:{'celldblclick' : this.setDataEstructura.createDelegate(this, ['nivel1'], 0)}
				});
				this.enviarOperacion(operacion);
				break;
				
			case 'nivel2':
				var reSpgEp2 = Ext.data.Record.create([
					{name: 'codestpro1'},
					{name: 'codestpro2'},    
					{name: 'denestpro2'}
				]);
				
				var dsSpgEp2 =  new Ext.data.Store({reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSpgEp2)});
				
				var fsBusquedaSpgEp2 = new Ext.form.FieldSet({
					xtype:"fieldset", 
					title:'B&#250;squeda',
					width: 600,
					height:100,
					border:true,
					defaultType: 'textfield',
					style: 'position:absolute;left:5px;top:5px',
		    		defaults: {labelSeparator:''},
					cls:'fondo',
					items: [{
						fieldLabel: 'C&#243;digo',
		                name: 'C&#243;digo',
						id:'codestniv2',
						autoCreate: {tag: 'input', type: 'text', maxlength: 25},
						width: 200,
						changeCheck: this.actualizarGrid2.createDelegate(this, ['codestpro2'], 0),							 
						initEvents : function() {
							AgregarKeyPress(this);
						}               
		  			},{
		                fieldLabel: 'Denominaci&#243;n',
		                name: 'denominacion',
		                id:'denestniv2',
		                autoCreate: {tag: 'input', type: 'text', maxlength: 254},
						width: 400,
						changeCheck: this.actualizarGrid2.createDelegate(this, ['denestpro2'], 0),							 
						initEvents : function(){
							AgregarKeyPress(this);
						}
					}]
				});
		
		
				var formBusquedaSpgEp2 = new Ext.FormPanel({
			        labelWidth: 80, 
			        frame:true,
			        width: 630,
					height:130,
			        items: [fsBusquedaSpgEp2]
				});
				
				this.gridEstructura2 = new Ext.grid.GridPanel({
		 			width:770,
		 			height:400,
		 			tbar: formBusquedaSpgEp2,
		 			autoScroll:true,
	     			border:true,
	     			enableColumnHide: false,
	     			ds: dsSpgEp2,
	     			cm: new Ext.grid.ColumnModel([
	          			{header: empresa['nomestpro1'], width: 30, sortable: true,   dataIndex: 'codestpro1', renderer: this.mostrarNumDigNiv1, align:'center'},
						{header: empresa['nomestpro2'], width: 30, sortable: true,   dataIndex: 'codestpro2', renderer: this.mostrarNumDigNiv2, align:'center'},
	          			{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro2'}
					]),
	       			stripeRows: true,
	      			viewConfig: {forceFit:true},
	      			listeners:{'celldblclick' : this.setDataEstructura.createDelegate(this, ['nivel2'], 0)}
				});
				this.enviarOperacion(operacion);
				break;
			
			case 'nivel3':
				var reSpgEp3 = Ext.data.Record.create([
					{name: 'codestpro1'},
					{name: 'codestpro2'},
					{name: 'codestpro3'},    
					{name: 'denestpro3'}
				]);
				
				var dsSpgEp3 =  new Ext.data.Store({reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSpgEp3)});	
					
		    	var fsBusquedaSpgEp3 = new Ext.form.FieldSet({
		    		xtype:"fieldset", 
					title:'B&#250;squeda',
					width: 600,
					height:100,
					border:true,
					defaultType: 'textfield',
					style: 'position:absolute;left:5px;top:5px',
		    		defaults: {labelSeparator:''},
					cls:'fondo',
					items: [{
						fieldLabel: 'C&#243;digo',
		                name: 'C&#243;digo',
						id:'codestniv3',
						autoCreate: {tag: 'input', type: 'text', maxlength: 25},
						width: 200,
						changeCheck: this.actualizarGrid3.createDelegate(this, ['codestpro3'], 0),							 
						initEvents : function() {
							AgregarKeyPress(this);
						}               
		  			},{
		                fieldLabel: 'Denominaci&#243;n',
		                name: 'denominacion',
		                id:'denestniv3',
		                autoCreate: {tag: 'input', type: 'text', maxlength: 254},
						width: 400,
						changeCheck: this.actualizarGrid3.createDelegate(this, ['denestpro3'], 0),							 
						initEvents : function(){
							AgregarKeyPress(this);
						}
					}]
				});
				//Fin del fieldset del formBusquedaCat
				
				var formBusquedaSpgEp3 = new Ext.FormPanel({
					labelWidth: 80, 
			        frame:true,
			        width: 630,
					height:130,
			       	items: [fsBusquedaSpgEp3]
				});
				
				this.gridEstructura3 = new Ext.grid.GridPanel({
					width:770,
					height:400,
					tbar: formBusquedaSpgEp3,
					autoScroll:true,
					border:true,
					enableColumnHide: false,
					ds: dsSpgEp3,
					cm: new Ext.grid.ColumnModel([
						{header: empresa['nomestpro1'], width: 30, sortable: true,   dataIndex: 'codestpro1', renderer: this.mostrarNumDigNiv1, align:'center'},
						{header: empresa['nomestpro2'], width: 30, sortable: true,   dataIndex: 'codestpro2', renderer: this.mostrarNumDigNiv2, align:'center'},
						{header: empresa['nomestpro3'], width: 30, sortable: true,   dataIndex: 'codestpro3', renderer: this.mostrarNumDigNiv3, align:'center'},
						{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro3'}
					]),
					stripeRows: true,
					viewConfig: {forceFit:true},
					listeners:{'celldblclick' : this.setDataEstructura.createDelegate(this, ['nivel3'], 0)}
				});
				this.enviarOperacion(operacion)
				break;
				
			case 'nivel4':
				var reSpgEp4 = Ext.data.Record.create([
					{name: 'codestpro1'},
					{name: 'codestpro2'},
					{name: 'codestpro3'},
					{name: 'codestpro4'},    
					{name: 'denestpro4'}
				]);
		
				var dsSpgEp4 =  new Ext.data.Store({reader: new Ext.data.JsonReader({root: 'raiz', id: "id"},reSpgEp4)});	
					
		    	var fsBusquedaSpgEp4 = new Ext.form.FieldSet({
					xtype:"fieldset", 
					title:'B&#250;squeda',
					width: 600,
					height:100,
					border:true,
					defaultType: 'textfield',
					style: 'position:absolute;left:5px;top:5px',
		    		defaults: {width: 230, labelSeparator:''},
					cls:'fondo',
					items: [{
						fieldLabel: 'C&#243;digo',
		                name: 'C&#243;digo',
						id:'codestniv4',
						autoCreate: {tag: 'input', type: 'text', maxlength: 25},
						width: 200,
						changeCheck: this.actualizarGrid4.createDelegate(this, ['codestpro4'], 0),							 
						initEvents : function() {
							AgregarKeyPress(this);
						}               
		  			},{
		                fieldLabel: 'Denominaci&#243;n',
		                name: 'denominacion',
		                id:'denestniv4',
		                autoCreate: {tag: 'input', type: 'text', maxlength: 254},
						width: 400,
						changeCheck: this.actualizarGrid4.createDelegate(this, ['denestpro4'], 0),							 
						initEvents : function(){
							AgregarKeyPress(this);
						}
					}]
				});
				//Fin del fieldset del formBusquedaCat
				
				var formBusquedaSpgEp4 = new Ext.FormPanel({
			        labelWidth: 80, 
			        frame:true,
			        width: 630,
					height:130,
			       items: [fsBusquedaSpgEp4]
				});
		
				this.gridEstructura4 = new Ext.grid.GridPanel({
					width:770,
					height:400,
					tbar: formBusquedaSpgEp4,
					autoScroll:true,
					border:true,
					enableColumnHide: false,
					ds: dsSpgEp4,
					cm: new Ext.grid.ColumnModel([
						{header: empresa['nomestpro1'], width: 30, sortable: true,   dataIndex: 'codestpro1', renderer: this.mostrarNumDigNiv1, align:'center'},
						{header: empresa['nomestpro2'], width: 30, sortable: true,   dataIndex: 'codestpro2', renderer: this.mostrarNumDigNiv2, align:'center'},
						{header: empresa['nomestpro3'], width: 30, sortable: true,   dataIndex: 'codestpro3', renderer: this.mostrarNumDigNiv3, align:'center'},
						{header: empresa['nomestpro4'], width: 30, sortable: true,   dataIndex: 'codestpro4', renderer: this.mostrarNumDigNiv4, align:'center'},
						{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro4'}
					]),
					stripeRows: true,
					viewConfig: {forceFit:true},
					listeners:{'celldblclick' : this.setDataEstructura.createDelegate(this, ['nivel4'], 0)}
				});
				this.enviarOperacion(operacion)
				break;
				
			case 'nivel5':
				var reSpgEp5 = Ext.data.Record.create([
					{name: 'codestpro1'},
					{name: 'codestpro2'},
					{name: 'codestpro3'},
					{name: 'codestpro4'},
					{name: 'codestpro5'},    
					{name: 'denestpro5'}
				]);
		
				var dsSpgEp5 =  new Ext.data.Store({reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSpgEp5)});	
				
				var fsBusquedaSpgEp5 = new Ext.form.FieldSet({
					xtype:"fieldset", 
					title:'B&#250;squeda',
					width: 600,
					height:100,
					border:true,
					defaultType: 'textfield',
					style: 'position:absolute;left:5px;top:5px',
		    		defaults: {width: 230, labelSeparator:''},
					cls:'fondo',
					items: [{
						fieldLabel: 'Codigo',
			            name: 'C&#243;digo',
						id:'codestniv5',
						autoCreate: {tag: 'input', type: 'text', maxlength: 25},
						width: 200,
						changeCheck: this.actualizarGrid5.createDelegate(this, ['codestpro5'], 0),							 
						initEvents : function(){
							AgregarKeyPress(this);
						}               
			      	},{
			      		fieldLabel: 'Denominaci&#243;n',
						name: 'denominacion',
						id:'denestniv5',
						autoCreate: {tag: 'input', type: 'text', maxlength: 254},
						width: 400,
						changeCheck: this.actualizarGrid5.createDelegate(this, ['denestpro5'], 0),
						initEvents : function(){
							AgregarKeyPress(this);
						}
					}]
				});
				//Fin del fieldset del formBusquedaCat
				
				var formBusquedaSpgEp5 = new Ext.FormPanel({
			        labelWidth: 80, 
			        frame:true,
			        width: 630,
					height:130,
			        items: [fsBusquedaSpgEp5]
				});
		    	
				this.gridEstructura5 = new Ext.grid.GridPanel({
					width:770,
					height:400,
					tbar: formBusquedaSpgEp5,
					autoScroll:true,
					border:true,
					enableColumnHide: false,
					ds: dsSpgEp5,
					cm: new Ext.grid.ColumnModel([
						{header: empresa['nomestpro1'], width: 30, sortable: true,   dataIndex: 'codestpro1', renderer: this.mostrarNumDigNiv1, align:'center'},
						{header: empresa['nomestpro2'], width: 30, sortable: true,   dataIndex: 'codestpro2', renderer: this.mostrarNumDigNiv2, align:'center'},
						{header: empresa['nomestpro3'], width: 30, sortable: true,   dataIndex: 'codestpro3', renderer: this.mostrarNumDigNiv3, align:'center'},
						{header: empresa['nomestpro4'], width: 30, sortable: true,   dataIndex: 'codestpro4', renderer: this.mostrarNumDigNiv4, align:'center'},
						{header: empresa['nomestpro5'], width: 30, sortable: true,   dataIndex: 'codestpro5', renderer: this.mostrarNumDigNiv5, align:'center'},
						{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro5'}
					]),
					stripeRows: true,
					viewConfig: {forceFit:true},
					listeners:{'celldblclick' : this.setDataEstructura.createDelegate(this, ['nivel5'], 0)}
				});
				this.enviarOperacion(operacion)
				break;
				
			case 'nivelN':
				var nniv = parseInt(empresa['numniv'])
				var fsCatalogoN = new Ext.form.FieldSet({
					xtype:"fieldset", 
					title:'B&#250;squeda',
					width: 600,
					height:100,
					border:true,
					defaultType: 'textfield',
					style: 'position:absolute;left:5px;top:5px',
		    		defaults: {labelSeparator:''},
					cls:'fondo',
					items: [{
						fieldLabel: 'C&#243;digo',
			            name: 'Codigo',
						id:'codestnivN',
						autoCreate: {tag: 'input', type: 'text', maxlength: 25},
						width: 200,
						changeCheck: this.actualizarGridN.createDelegate(this, ['codestpro'+empresa['numniv']], 0),							 
						initEvents : function() {
							AgregarKeyPress(this);
						}               
			      	},{
			      		fieldLabel: 'Denominaci&#243;n',
						name: 'denominacion',
						id:'denestnivN',
						autoCreate: {tag: 'input', type: 'text', maxlength: 254},
						width: 400,
						changeCheck: this.actualizarGridN.createDelegate(this, ['denestpro'+empresa['numniv']], 0),							 
						initEvents : function() {
							AgregarKeyPress(this);
						}
					}]
				});
								
				var formBusquedaSpgEpN = new Ext.FormPanel({
			        labelWidth: 80,
			        frame:true,
			        width: 630,
					height:130,
			        items: [fsCatalogoN]
				});
				var modelogridN="[";
				for(var x=1;x<=nniv;x++){
					if(x==nniv){
						modelogridN = modelogridN + "{header: '"+empresa['nomestpro'+x]+"', width: 25, sortable: true,   dataIndex: 'codestpro"+x+"', align:'center'},"+
													"{header: 'Denominaci&#243;n', width: 50, sortable: true,   dataIndex: 'denestpro"+x+"'},"+
													"{header: 'Tipo', width: 25, sortable: true, dataIndex: 'estcla'}";
					}else{
						modelogridN = modelogridN + "{header: '"+empresa['nomestpro'+x]+"', width: 30, sortable: true,   dataIndex: 'codestpro"+x+"', align:'center'},";
					}	
				}
				modelogridN = modelogridN + "]";
				var objetomodelo = Ext.util.JSON.decode(modelogridN);
				var dsSpgEpN = this.crearStoreEstructuraN(nniv);
				this.gridEstructuraN = new Ext.grid.GridPanel({
					width:770,
		 			height:400,
		 			tbar: formBusquedaSpgEpN,
		 			autoScroll:true,
	     			border:true,
	     			enableColumnHide: false,
	     			ds: dsSpgEpN,
	     			cm: new Ext.grid.ColumnModel(objetomodelo),
	       			stripeRows: true,
	      			viewConfig: {forceFit:true},
	      			listeners:{'celldblclick' : this.setDataEstructura.createDelegate(this, ['nivelN'], 0)}
				});
				this.enviarOperacion(operacion);
				switch (nniv)
				{
					case 1:
						this.gridEstructuraN.getColumnModel().setRenderer(0,this.mostrarNumDigNiv1);
						this.gridEstructuraN.getColumnModel().setRenderer(2,mostrarEstatusComCmp);
						break;
						
					case 2:
						this.gridEstructuraN.getColumnModel().setRenderer(0,this.mostrarNumDigNiv1);
						this.gridEstructuraN.getColumnModel().setRenderer(1,this.mostrarNumDigNiv2);
						this.gridEstructuraN.getColumnModel().setRenderer(3,mostrarEstatusComCmp);
						break;
					
					case 3:
						this.gridEstructuraN.getColumnModel().setRenderer(0,this.mostrarNumDigNiv1);
						this.gridEstructuraN.getColumnModel().setRenderer(1,this.mostrarNumDigNiv2);
						this.gridEstructuraN.getColumnModel().setRenderer(2,this.mostrarNumDigNiv3);
						this.gridEstructuraN.getColumnModel().setRenderer(4,mostrarEstatusComCmp);
						break;
					
					case 4:
						this.gridEstructuraN.getColumnModel().setRenderer(0,this.mostrarNumDigNiv1);
						this.gridEstructuraN.getColumnModel().setRenderer(1,this.mostrarNumDigNiv2);
						this.gridEstructuraN.getColumnModel().setRenderer(2,this.mostrarNumDigNiv3);
						this.gridEstructuraN.getColumnModel().setRenderer(3,this.mostrarNumDigNiv4);
						this.gridEstructuraN.getColumnModel().setRenderer(5,mostrarEstatusComCmp);
						break;
					
					case 5:
						this.gridEstructuraN.getColumnModel().setRenderer(0,this.mostrarNumDigNiv1);
						this.gridEstructuraN.getColumnModel().setRenderer(1,this.mostrarNumDigNiv2);
						this.gridEstructuraN.getColumnModel().setRenderer(2,this.mostrarNumDigNiv3);
						this.gridEstructuraN.getColumnModel().setRenderer(3,this.mostrarNumDigNiv4);
						this.gridEstructuraN.getColumnModel().setRenderer(4,this.mostrarNumDigNiv5);
						this.gridEstructuraN.getColumnModel().setRenderer(6,mostrarEstatusComCmp);
						break;
				}
				break;
			case 'fuente':
				var reFuenteFin = Ext.data.Record.create([
					{name: 'codfuefin'},    
					{name: 'denfuefin'}
				]);
				
				var dsFuenteFin =  new Ext.data.Store({reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reFuenteFin)});
				
				var fsBusquedaFuenteFin = new Ext.form.FieldSet({
					xtype:"fieldset", 
					title:'B&#250;squeda',
					width: 600,
					height:100,
					border:true,
					defaultType: 'textfield',
					style: 'position:absolute;left:5px;top:5px',
		    		defaults: {labelSeparator:''},
					cls:'fondo',
					items: [{
						fieldLabel: 'C&#243;digo',
		                id:'codfuentefin',
						autoCreate: {tag: 'input', type: 'text', maxlength: 2},
						width: 100,
						changeCheck: this.actualizarGridFuente.createDelegate(this, ['codfuefin'], 0),							 
						initEvents : function() {
							AgregarKeyPress(this);
						}               
		  			},{
		                fieldLabel: 'Denominaci&#243;n',
		                id:'denfuentefin',
		                autoCreate: {tag: 'input', type: 'text', maxlength: 254},
						width: 400,
						changeCheck: this.actualizarGridFuente.createDelegate(this, ['denfuefin'], 0),							 
						initEvents : function(){
							AgregarKeyPress(this);
						}
					}]
				});
		
		
				var formBusquedaFuenteFin = new Ext.FormPanel({
			        labelWidth: 80, 
			        frame:true,
			        width: 630,
					height:130,
			        items: [fsBusquedaFuenteFin]
				});
				
				this.gridFuenteFinanciamiento = new Ext.grid.GridPanel({
		 			width:770,
		 			height:400,
		 			tbar: formBusquedaFuenteFin,
		 			autoScroll:true,
	     			border:true,
	     			enableColumnHide: false,
	     			ds: dsFuenteFin,
	     			cm: new Ext.grid.ColumnModel([
	          			{header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codfuefin', align:'center'},
						{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denfuefin'}
					]),
	       			stripeRows: true,
	      			viewConfig: {forceFit:true},
	      			listeners:{'celldblclick' : this.setDataEstructura.createDelegate(this, ['fuente'], 0)}
				});
				this.enviarOperacion(operacion);
				break;
			case 'cuenta':
				var reCuenta = Ext.data.Record.create([
					{name: 'spg_cuenta'},    
					{name: 'denominacion'},
					{name: 'sc_cuenta'},
					{name: 'disponible'}
				]);
				
				var dsCuenta =  new Ext.data.Store({reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCuenta)});
				
				var fsBusquedaCuenta = new Ext.form.FieldSet({
					xtype:"fieldset", 
					title:'B&#250;squeda',
					width: 600,
					height:150,
					border:true,
					defaultType: 'textfield',
					style: 'position:absolute;left:5px;top:5px',
		    		defaults: {labelSeparator:''},
					cls:'fondo',
					items: [{
						fieldLabel: 'C&#243;digo',
		                id:'codcuenta',
						autoCreate: {tag: 'input', type: 'text', maxlength: 25},
						width: 150,
						changeCheck: this.actualizarGridCuenta.createDelegate(this, ['spg_cuenta'], 0),							 
						initEvents : function() {
							AgregarKeyPress(this);
						}               
		  			},{
		                fieldLabel: 'Denominaci&#243;n',
		                id:'dencuenta',
		                autoCreate: {tag: 'input', type: 'text', maxlength: 254},
						width: 400,
						changeCheck: this.actualizarGridCuenta.createDelegate(this, ['denominacion'], 0),							 
						initEvents : function(){
							AgregarKeyPress(this);
						}
					},{
		                fieldLabel: 'Cuenta contable',
		                id:'codcuentascg',
		                autoCreate: {tag: 'input', type: 'text', maxlength: 25},
						width: 400,
						changeCheck: this.actualizarGridCuenta.createDelegate(this, ['sc_cuenta'], 0),							 
						initEvents : function(){
							AgregarKeyPress(this);
						}
					},{
						xtype: "button",
						iconCls: "menubuscar", 
						id:'btncatcuenta',
						style: 'position:absolute;left:500px;top:110px',
						handler:this.buscarCuenta.createDelegate(this)
					}]
				});
				
		
				var formBusquedaCuenta = new Ext.FormPanel({
			        labelWidth: 150, 
			        frame:true,
			        width: 630,
					height:180,
			        items: [fsBusquedaCuenta]
				});
				
				this.gridCuenta = new Ext.grid.GridPanel({
		 			width:770,
		 			height:400,
		 			tbar: formBusquedaCuenta,
		 			autoScroll:true,
	     			border:true,
	     			enableColumnHide: false,
	     			ds: dsCuenta,
	     			cm: new Ext.grid.ColumnModel([
	          			{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'spg_cuenta'},
						{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denominacion'},
						{header: "Contable", width: 25, sortable: true, dataIndex: 'sc_cuenta'},
						{header: "Disponibilidad", width: 15, sortable: true, dataIndex: 'disponible', renderer:formatoMontoGrid}
					]),
	       			stripeRows: true,
	      			viewConfig: {forceFit:true},
	      			listeners:{'celldblclick' : this.setDataEstructura.createDelegate(this, ['cuenta'], 0)}
				});
				
				break;
		}
	} 
	//fin crear grid..........
	
	this.cerrarVentanaEstructura = function()
	{
		this.venCatalogo.destroy();
	}
	
	this.catalogoEstructuraNivel1 = function()
	{
		this.obtenerGridCatalogo('nivel1');				   
	    this.venCatalogo = new Ext.Window({
	    	title: 'Cat&#225;logo de '+empresa['nomestpro1'],
			autoScroll:true,
	        width:800,
	        height:475,
	        modal: true,
	        closable:false,
	        plain: false,
	        items:[this.gridEstructura1],
	        buttons: [{
				text:'Aceptar',  
				handler: this.setDataEstructura.createDelegate(this, ['nivel1'], 0)
			},{
				text: 'Salir',
				handler: this.cerrarVentanaEstructura.createDelegate(this)
	       	}]
	    });
	    this.venCatalogo.show();
	}
	
	this.catalogoEstructuraNivel2 = function()
	{
		if((this.fsEstructura.findById('codest'+options.idtxt+'0').getValue() =="")&&(this.filtrosindistintos==0))
		{
			this.mensajeValidacionNivel(1)
		}
		else
		{
			this.obtenerGridCatalogo('nivel2');				   
		    this.venCatalogo = new Ext.Window({
		    	title: 'Cat&#225;logo de '+empresa['nomestpro2'],
				autoScroll:true,
		        width:800,
		        height:475,
		        modal: true,
		        closable:false,
		        plain: false,
		        items:[this.gridEstructura2],
		        buttons: [{
					text:'Aceptar',  
					handler: this.setDataEstructura.createDelegate(this, ['nivel2'], 0)
				},{
					text: 'Salir',
					handler: this.cerrarVentanaEstructura.createDelegate(this)
		       	}]
		    });
		    this.venCatalogo.show();
		}
	}
	
	this.catalogoEstructuraNivel3 = function() {
		if((this.fsEstructura.findById('codest'+options.idtxt+'1').getValue() =="")&&(this.filtrosindistintos==0))
		{
			this.mensajeValidacionNivel(2)
		}
		else
		{
			this.obtenerGridCatalogo('nivel3');				   
		    this.venCatalogo = new Ext.Window({
		    	title: 'Cat&#225;logo de '+empresa['nomestpro3'],
				autoScroll:true,
		        width:800,
		        height:475,
		        modal: true,
		        closable:false,
		        plain: false,
		        items:[this.gridEstructura3],
		        buttons: [{
					text:'Aceptar',  
					handler: this.setDataEstructura.createDelegate(this, ['nivel3'], 0)
				},{
					text: 'Salir',
					handler: this.cerrarVentanaEstructura.createDelegate(this)
		       	}]
		    });
		    this.venCatalogo.show();
		}
	}
	
	this.catalogoEstructuraNivel4=function(){
		if((this.fsEstructura.findById('codest'+options.idtxt+'2').getValue() =="")&&(this.filtrosindistintos==0))
		{
			this.mensajeValidacionNivel(3)
		}
		else
		{
			this.obtenerGridCatalogo('nivel4');				   
			this.venCatalogo = new Ext.Window({
				title: 'Cat&#225;logo de '+empresa['nomestpro4'],
				autoScroll:true,
		        width:800,
		        height:475,
		        modal: true,
		        closable:false,
		        plain: false,
		        items:[this.gridEstructura4],
		        buttons: [{
		        	text:'Aceptar',  
					handler: this.setDataEstructura.createDelegate(this, ['nivel4'], 0)
				},{
					text: 'Salir',
					handler: this.cerrarVentanaEstructura.createDelegate(this)
				}]
	      	});
	      	this.venCatalogo.show();
		}
	}
	
	this.catalogoEstructuraNivel5=function(){
		if((this.fsEstructura.findById('codest'+options.idtxt+'3').getValue() =="")&&(this.filtrosindistintos==0))
		{
			this.mensajeValidacionNivel(4)
		}
		else
		{
			this.obtenerGridCatalogo('nivel5');
			this.venCatalogo = new Ext.Window({
		    	title: 'Cat&#225;logo de '+empresa['nomestpro5'],
				autoScroll:true,
		        width:800,
		        height:475,
		        modal: true,
		        closable:false,
		        plain: false,
		        items:[this.gridEstructura5],
		        buttons: [{
		        	text:'Aceptar',  
					handler: this.setDataEstructura.createDelegate(this, ['nivel5'], 0)
				},{
					text: 'Salir',
					handler: this.cerrarVentanaEstructura.createDelegate(this)
		        }]
	      	});
	      	this.venCatalogo.show();
		}
	}
	
	this.catalogoEstructuraNivelN=function(){
		if((this.fsEstructura.findById('codest'+options.idtxt+'0').getValue()=="")&&
		   (parseInt(arguments[0])==parseInt(empresa['numniv']))){
			this.obtenerGridCatalogo('nivelN');
	    	this.venCatalogo = new Ext.Window({
	    		title: 'Cat&#225;logo de Estructuras Presupuestarias',
				autoScroll:true,
		        width:800,
		        height:475,
		        modal: true,
		        closable:false,
		        plain: false,
		        items:[this.gridEstructuraN],
		        buttons: [{
		        	text:'Aceptar',  
					handler: this.setDataEstructura.createDelegate(this, ['nivelN'], 0)
				},{
					text: 'Salir',
					handler: this.cerrarVentanaEstructura.createDelegate(this)
		        }]
	      	});
	      	this.venCatalogo.show();
		}
		else
		{
			var funcion = null;
			if(arguments[0]=='3')
			{
				funcion = this.catalogoEstructuraNivel3.createDelegate(this);
			}
			else if(arguments[0]=='5')
			{
				funcion = this.catalogoEstructuraNivel5.createDelegate(this);
			}
			funcion();
		}
	}
	
	this.catalogoFuenteFinanciamiento=function()
	{
		var nivFin = parseInt(empresa['numniv'])-1;
			this.obtenerGridCatalogo('fuente');
			this.venCatalogo = new Ext.Window({
		    	title: 'Cat&#225;logo de Fuente de Financiamiento',
				autoScroll:true,
		        width:800,
		        height:475,
		        modal: true,
		        closable:false,
		        plain: false,
		        items:[this.gridFuenteFinanciamiento],
		        buttons: [{
		        	text:'Aceptar',  
					handler: this.setDataEstructura.createDelegate(this, ['fuente'], 0)
				},{
					text: 'Salir',
					handler: this.cerrarVentanaEstructura.createDelegate(this)
		        }]
	      	});
	      	this.venCatalogo.show();
	}
	
	this.catalogoCuenta=function(){
		var nivFin = parseInt(empresa['numniv'])-1;
			this.obtenerGridCatalogo('cuenta');
			this.venCatalogo = new Ext.Window({
		    	title: 'Cat&#225;logo de Cuentas Presupuestarias de Gasto',
				autoScroll:true,
		        width:800,
		        height:475,
		        modal: true,
		        closable:false,
		        plain: false,
		        items:[this.gridCuenta],
		        buttons: [{
		        	text:'Aceptar',  
					handler: this.setDataEstructura.createDelegate(this, ['cuenta'], 0)
				},{
					text: 'Salir',
					handler: this.cerrarVentanaEstructura.createDelegate(this)
		        }]
	      	});
	      	this.venCatalogo.show();
	}
	
	this.obtenerFieldSetEstructura=function() {
		var anchoFS          = 0;
		var anchoColCampo    = 0;
		var anchoColBoton    = 0;
		var anchoColEtiqueta = 0;
		var altoFS           = 0;
		
		if(!options.mostrarDenominacion){
			anchoFS = 450;
			anchoColCampo    = 0.92; 
			anchoColBoton    = 0.08;
			anchoColEtiqueta = 0;
			this.ocultarDenominacion  = true;
		}
		else{
			anchoFS = 850;
			anchoColCampo    = 0.55; 
			anchoColBoton    = 0.05;
			anchoColEtiqueta = 0.40;
		}
		
		var fieldset = null;
   		switch(parseInt(empresa['numniv'])) {
   			case 1 :
   				fieldset =new Ext.form.FieldSet({
   					width: anchoFS,
					height: 80,
					title: options.titform,
					style: options.estilo,
					cls :'fondo',
					autoScroll:true,
					items: [{
						xtype: 'hidden',
						name: 'estcla'+options.idtxt,
						id: 'estcla'+options.idtxt
					},{//NIVEL 1
						layout : "column", 
						defaults : {border : false, labelWidth: 200}, 
					   	items : [{
					   		layout : "form", 
							border : false,
							columnWidth : anchoColCampo,
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro1'], 
								name: 'codigo'+options.idtxt+'0', 
								id: 'codest'+options.idtxt+'0',
								style:"text-align:right",
								readOnly:true, 
								width: 185,
								qtip:empresa['nomestpro1']
							}]
						},{
							columnWidth : anchoColBoton, 
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'0',
								handler:this.catalogoEstructuraNivel1.createDelegate(this)
							}]
					   	},{
					   		columnWidth : anchoColEtiqueta, 
					   		bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'0', 
								id: "denest"+options.idtxt+'0',
								hidden: this.ocultarDenominacion 
							}] 
					   }]
					}]
				});
  				break;	
  			
			case 2 :
				fieldset =new Ext.form.FieldSet({
					width: anchoFS,
					height: 120,
					title: options.titform,
					style: options.estilo,
					cls :'fondo',
					autoScroll:true,
					items: [{
						xtype: 'hidden',
						name: 'estcla'+options.idtxt,
						id: 'estcla'+options.idtxt
					},{//NIVEL 1
						layout : "column", 
						defaults : {border : false, labelWidth: 200}, 
					   	items : [{
					   		layout : "form", 
							border : false,
							columnWidth : anchoColCampo,
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro1'], 
								name: 'codigo'+options.idtxt+'0', 
								id: 'codest'+options.idtxt+'0',
								style:"text-align:right",
								readOnly:true, 
								width: 185,
								qtip:empresa['nomestpro1']
							}]
						},{
							columnWidth : anchoColBoton, 
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'0',
								handler:this.catalogoEstructuraNivel1.createDelegate(this)
							}]
					   	},{
					   		columnWidth : anchoColEtiqueta, 
					   		bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'0', 
								id: "denest"+options.idtxt+'0',
								hidden: this.ocultarDenominacion 
							}] 
					   }]
					},{//NIVEL 2
						layout : "column", 
						defaults : {border : false, labelWidth:200},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro2'], 
								name: 'codigo'+options.idtxt+'1', 
								id: 'codest'+options.idtxt+'1',
								style:"text-align:right",
								readOnly:true, 
								width: 185 
							}]
						},{
							layout : "form", 
							border : false, 
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'1',
								handler:this.catalogoEstructuraNivel2.createDelegate(this)
							}]
						},{
							layout : "form", 
							border : false,    
							columnWidth : anchoColEtiqueta, 
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'1', 
								id: "denest"+options.idtxt+'1',
								width:200,
								hidden:this.ocultarDenominacion 
							}] 
						}]
					}]
				});
				break;
				
			case 3 :
				altoFS = 180;
				if(!options.sinFuente){
					altoFS = altoFS + 35;
				}
				
				if(!options.sinCuenta){
					altoFS = altoFS + 35;
				}
				fieldset =new Ext.form.FieldSet({
					width: anchoFS,
					height: altoFS,
					title: options.titform,
					style: options.estilo,
					cls :'fondo',
					autoScroll:true,
					items: [{
						xtype: 'hidden',
						name: 'estcla'+options.idtxt,
						id: 'estcla'+options.idtxt
					},{//NIVEL 1
						layout : "column", 
						defaults : {border : false, labelWidth: 200}, 
					   	items : [{
					   		layout : "form", 
							border : false,
							columnWidth : anchoColCampo,
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro1'], 
								name: 'codigo'+options.idtxt+'0', 
								id: 'codest'+options.idtxt+'0',
								style:"text-align:right",
								readOnly:true, 
								width: 185,
								qtip:empresa['nomestpro1']
							}]
						},{
							columnWidth : anchoColBoton, 
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'0',
								handler:this.catalogoEstructuraNivel1.createDelegate(this)
							}]
					   	},{
					   		columnWidth : anchoColEtiqueta, 
					   		bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'0', 
								id: "denest"+options.idtxt+'0',
								hidden: this.ocultarDenominacion 
							}] 
					   }]
					},{//NIVEL 2
						layout : "column", 
						defaults : {border : false, labelWidth:200},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro2'], 
								name: 'codigo'+options.idtxt+'1', 
								id: 'codest'+options.idtxt+'1',
								style:"text-align:right",
								readOnly:true, 
								width: 185 
							}]
						},{
							layout : "form", 
							border : false, 
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'1',
								handler:this.catalogoEstructuraNivel2.createDelegate(this)
							}]
						},{
							layout : "form", 
							border : false,    
							columnWidth : anchoColEtiqueta, 
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'1', 
								id: "denest"+options.idtxt+'1',
								width:200,
								hidden:this.ocultarDenominacion 
							}] 
						}]
					},{//NIVEL 3
						layout : "column", 
						defaults : {border : false, labelWidth: 200},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro3'], 
								name: 'codigo'+options.idtxt+'2', 
								id: 'codest'+options.idtxt+'2',
								style:"text-align:right",
								readOnly:true, 
								width: 185 
							}]
						},{
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'2',
								handler:this.catalogoEstructuraNivelN.createDelegate(this, ['3'], 0)
							}]
						},{
							columnWidth : anchoColEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'2', 
								id: "denest"+options.idtxt+'2', 
								hidden:this.ocultarDenominacion
							}] 
						}]
				 	},{//FUENTE FINANCIAMIENTO
						layout : "column", 
						defaults : {border : false, labelWidth: 200},
						hidden: options.sinFuente,
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype :  "textfield",
								labelSeparator: '',
								fieldLabel: 'Fuente Financiamiento', 
								id: 'codfuentefin'+options.idtxt, 
								readOnly:true, 
								style:"text-align:right",
								value:'--',
								width: 185 
							}]
						},{
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype: "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'3',
								handler:this.catalogoFuenteFinanciamiento.createDelegate(this)
							}]
						},{
							columnWidth : anchoColEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype: "label",
								id: "denfuentefin"+options.idtxt, 
								hidden:this.ocultarDenominacion
							}] 
						}]
					},{//CUENTA PRESUPUESTARIA
						layout : "column", 
						defaults : {border : false, labelWidth: 200},
						hidden: options.sinCuenta,
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype :  "textfield",
								labelSeparator: '',
								fieldLabel: 'Cuenta', 
								id: 'codcuenta'+options.idtxt, 
								readOnly:true, 
								style:"text-align:right", 
								width: 185 
							}]
						},{
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype: "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'4',
								handler:this.catalogoCuenta.createDelegate(this)
							}]
						},{
							columnWidth : anchoColEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype: "label",
								id: "dencuenta"+options.idtxt+'4', 
								hidden:this.ocultarDenominacion
							}] 
						}]
					}]
				});
  				break;
  			
  			case 4 : 
		  		fieldset =new Ext.form.FieldSet({
					width: anchoFS,
					height: 200,
					title: options.titform,
					style: options.estilo,
					cls :'fondo',
					autoScroll:true,
					items: [{
						xtype: 'hidden',
						name: 'estcla'+options.idtxt,
						id: 'estcla'+options.idtxt
					},{//NIVEL 1
						layout : "column", 
						defaults : {border : false, labelWidth: 200}, 
					   	items : [{
					   		layout : "form", 
							border : false,
							columnWidth : anchoColCampo,
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro1'], 
								name: 'codigo'+options.idtxt+'0', 
								id: 'codest'+options.idtxt+'0', 
								readOnly:true,
								style:"text-align:right",
								width: 185,
								qtip:empresa['nomestpro1']
							}]
						},{
							columnWidth : anchoColBoton, 
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'0',
								handler:this.catalogoEstructuraNivel1.createDelegate(this)
							}]
					   	},{
					   		columnWidth : anchoColEtiqueta, 
					   		bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'0', 
								id: "denest"+options.idtxt+'0',
								hidden: this.ocultarDenominacion 
							}] 
					   }]
					},{//NIVEL 2
						layout : "column", 
						defaults : {border : false, labelWidth:200},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro2'], 
								name: 'codigo'+options.idtxt+'1', 
								id: 'codest'+options.idtxt+'1',
								style:"text-align:right",
								readOnly:true, 
								width: 185 
							}]
						},{
							layout : "form", 
							border : false, 
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'1',
								handler:this.catalogoEstructuraNivel2.createDelegate(this)
							}]
						},{
							layout : "form", 
							border : false,    
							columnWidth : anchoColEtiqueta, 
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'1', 
								id: "denest"+options.idtxt+'1',
								width:200,
								hidden:this.ocultarDenominacion 
							}] 
						}]
					},{//NIVEL 3
						layout : "column", 
						defaults : {border : false, labelWidth: 200},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro3'], 
								name: 'codigo'+options.idtxt+'2', 
								id: 'codest'+options.idtxt+'2',
								style:"text-align:right",
								readOnly:true, 
								width: 185 
							}]
						},{
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'2',
								handler:this.catalogoEstructuraNivelN.createDelegate(this, ['3'], 0)
							}]
						},{
							columnWidth : anchoColEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'2', 
								id: "denest"+options.idtxt+'2', 
								hidden:this.ocultarDenominacion
							}] 
						}]
				 	},{//NIVEL 4
				 		layout : "column", 
						defaults : {border : false,labelWidth: 200}, 
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro4'], 
								name: 'codigo'+options.idtxt+'3', 
								id: 'codest'+options.idtxt+'3', 
								readOnly:true, 
								style:"text-align:right", 
								width: 185 
							}]
						},{
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px', 
							items : [{
								xtype: "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'3',
								handler:this.catalogoEstructuraNivel4.createDelegate(this)
							}]
						},{
							columnWidth : anchoColEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype:  "label",
								name: "denon"+options.idtxt+'3', 
								id: "denest"+options.idtxt+'3', 
								hidden:this.ocultarDenominacion
							}] 
						}]
					}]
				});
				break;
			
			case 5 :
				altoFS = 250;
				if(!options.sinFuente){
					altoFS = altoFS + 35;
				}
				
				if(!options.sinCuenta){
					altoFS = altoFS + 35;
				}
				fieldset =new Ext.form.FieldSet({
					width: anchoFS,
					height: altoFS,
					title: options.titform,
					style: options.estilo,
					cls :'fondo',
					autoScroll:true,
					items: [{
						xtype: 'hidden',
						name: 'estcla'+options.idtxt,
						id: 'estcla'+options.idtxt
					   },{//NIVEL 1
						layout : "column", 
						defaults : {border : false, labelWidth: 200}, 
					   	items : [{
					   		layout : "form", 
							border : false,
							columnWidth : anchoColCampo,
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro1'], 
								name: 'codigo'+options.idtxt+'0', 
								id: 'codest'+options.idtxt+'0', 
								readOnly:true,
								style:"text-align:right",
								width: 185,
								qtip:empresa['nomestpro1']
							}]
						},{
							columnWidth : anchoColBoton, 
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'0',
								handler:this.catalogoEstructuraNivel1.createDelegate(this)
							}]
					   	},{
					   		columnWidth : anchoColEtiqueta, 
					   		bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'0', 
								id: "denest"+options.idtxt+'0',
								hidden: this.ocultarDenominacion 
							}] 
					   }]
					},{//NIVEL 2
						layout : "column", 
						defaults : {border : false, labelWidth:200},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro2'], 
								name: 'codigo'+options.idtxt+'1', 
								id: 'codest'+options.idtxt+'1',
								style:"text-align:right",
								readOnly:true, 
								width: 185 
							}]
						},{
							layout : "form", 
							border : false, 
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'1',
								handler:this.catalogoEstructuraNivel2.createDelegate(this)
							}]
						},{
							layout : "form", 
							border : false,    
							columnWidth : anchoColEtiqueta, 
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'1', 
								id: "denest"+options.idtxt+'1',
								width:200,
								hidden:this.ocultarDenominacion 
							}] 
						}]
					},{//NIVEL 3
						layout : "column", 
						defaults : {border : false, labelWidth: 200},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro3'], 
								name: 'codigo'+options.idtxt+'2', 
								id: 'codest'+options.idtxt+'2',
								style:"text-align:right",
								readOnly:true, 
								width: 185 
							}]
						},{
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'2',
								handler:this.catalogoEstructuraNivelN.createDelegate(this, ['3'], 0)
							}]
						},{
							columnWidth : anchoColEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'2', 
								id: "denest"+options.idtxt+'2', 
								hidden:this.ocultarDenominacion
							}] 
						}]
				 	},{//NIVEL 4
				 		layout : "column", 
						defaults : {border : false,labelWidth: 200}, 
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro4'], 
								name: 'codigo'+options.idtxt+'3', 
								id: 'codest'+options.idtxt+'3', 
								readOnly:true, 
								style:"text-align:right", 
								width: 185 
							}]
						},{
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px', 
							items : [{
								xtype: "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'3',
								handler:this.catalogoEstructuraNivel4.createDelegate(this)
							}]
						},{
							columnWidth : anchoColEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype:  "label",
								name: "denon"+options.idtxt+'3', 
								id: "denest"+options.idtxt+'3', 
								hidden:this.ocultarDenominacion
							}] 
						}]
					},{//NIVEL 5
						layout : "column", 
						defaults : {border : false, labelWidth: 200},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype :  "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro5'], 
								name: 'codigo'+options.idtxt+'4', 
								id: 'codest'+options.idtxt+'4', 
								readOnly:true, 
								style:"text-align:right", 
								width: 185 
							}]
						},{
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype: "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'4',
								handler:this.catalogoEstructuraNivelN.createDelegate(this, ['5'], 0)
							}]
						},{
							columnWidth : anchoColEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype: "label",
								name: "denon"+options.idtxt+'4', 
								id: "denest"+options.idtxt+'4', 
								hidden:this.ocultarDenominacion
							}] 
						}]
					},{//FUENTE FINANCIAMIENTO
						layout : "column", 
						defaults : {border : false, labelWidth: 200},
						hidden: options.sinFuente,
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype :  "textfield",
								labelSeparator: '',
								fieldLabel: 'Fuente Financiamiento', 
								id: 'codfuentefin'+options.idtxt,
								readOnly:true, 
								style:"text-align:right", 
								width: 185,
								value:'--'
							}]
						},{
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype: "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'5',
								handler:this.catalogoFuenteFinanciamiento.createDelegate(this)
							}]
						},{
							columnWidth : anchoColEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype: "label",
								id: "denfuentefin"+options.idtxt, 
								hidden:this.ocultarDenominacion
							}] 
						}]
					},{//CUENTA PRESUPUESTARIA
						layout : "column", 
						defaults : {border : false, labelWidth: 200},
						hidden: options.sinCuenta,
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : anchoColCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype :  "textfield",
								labelSeparator: '',
								fieldLabel: 'Cuenta', 
								id: 'codcuenta'+options.idtxt, 
								readOnly:true, 
								style:"text-align:right", 
								width: 185 
							}]
						},{
							columnWidth : anchoColBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype: "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'6',
								handler:this.catalogoCuenta.createDelegate(this)
							}]
						},{
							columnWidth : anchoColEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype: "label",
								id: "dencuenta"+options.idtxt+'6', 
								hidden:this.ocultarDenominacion
							}] 
						}]
					}]
				});
		 		break;
		}
		return fieldset;
	}
	
	this.fsEstructura = this.obtenerFieldSetEstructura();
	
	this.obtenerCodigoEstructuraNivel = function(nivel, formato)
	{
		var codigo="";
		switch(nivel)
		{
			case 1:
			  	if(formato)
				{
					codigo = this.fsEstructura.findById('codest'+options.idtxt+'0').getValue();
			  	}
			  	else
				{
			  		codigo = String.leftPad(this.fsEstructura.findById('codest'+options.idtxt+'0').getValue(),25,'0');
			  	}
				break;
			
			case 2:  
				if(this.fsEstructura.findById('codest'+options.idtxt+'1') != null)
				{
					if(formato)
					{
						codigo = this.fsEstructura.findById('codest'+options.idtxt+'1').getValue();
					}
					else
					{
						codigo = String.leftPad(this.fsEstructura.findById('codest'+options.idtxt+'1').getValue(),25,'0');
					}
			    }
			    else
				{
			    	codigo = String.leftPad("",25,'0'); 
			    }
				break;
			
			case 3:
				if(this.fsEstructura.findById('codest'+options.idtxt+'2') != null)
				{
					if(formato)
					{
						codigo = this.fsEstructura.findById('codest'+options.idtxt+'2').getValue();
					}
					else
					{
						codigo = String.leftPad(this.fsEstructura.findById('codest'+options.idtxt+'2').getValue(),25,'0');
					}
				}
			    else
				{
			    	codigo = String.leftPad("",25,'0'); 
			    }
				break;
			
			case 4:
				if(this.fsEstructura.findById('codest'+options.idtxt+'3') != null)
				{
					if(formato)
					{
						codigo = this.fsEstructura.findById('codest'+options.idtxt+'3').getValue();
					}
					else
					{
						codigo = String.leftPad(this.fsEstructura.findById('codest'+options.idtxt+'3').getValue(),25,'0');
					}
			    }
			    else
				{
			    	codigo = String.leftPad("",25,'0'); 
			    }
				break;
			
			case 5:
				if(this.fsEstructura.findById('codest'+options.idtxt+'4') != null)
				{
					if(formato)
					{
						codigo = this.fsEstructura.findById('codest'+options.idtxt+'4').getValue();
					}
					else
					{
						codigo = String.leftPad(this.fsEstructura.findById('codest'+options.idtxt+'4').getValue(),25,'0');
					}
			    }
			    else
				{
			    	codigo = String.leftPad("",25,'0'); 
			    }
				break;
		}
		
		return codigo;
	}
	
	this.obtenerEstClaEstructura=function()
	{
		return this.fsEstructura.findById('estcla'+options.idtxt).getValue();
	}
	
	this.obtenerArrayEstructura=function()
	{
    	var codestpro = new Array();
    	var j = 0;
    	
    	for(var i = 0; i<5; i++)
		{
    		j++;
    		codestpro[i] = this.obtenerCodigoEstructuraNivel(j,false);
    	}
    	codestpro[i] = this.obtenerEstClaEstructura();	
    	if(!options.sinFuente)
		{
    		i++;
    		codestpro[i] = this.fsEstructura.findById('codfuentefin'+options.idtxt).getValue();
    	}
    	if(!options.sinCuenta)
		{
    		i++;
    		codestpro[i] = this.fsEstructura.findById('codcuenta'+options.idtxt).getValue();
    	}
    	
    	return codestpro;
    }
    
    this.obtenerArrayEstructuraFormato =function()
	{
    	var codestpro = new Array(5);
    	var j = 0;
    	for(var i = 0; i<5; i++)
		{
    		j++;
    		codestpro[i] = this.obtenerCodigoEstructuraNivel(j,true);
    	}
    	codestpro[5] = this.obtenerEstClaEstructura();	
    	
    	return codestpro;
    }
    
    this.obtenerEstructuraFormato =function()
	{
    	var formatoEstructura='';
		for(var i=0; i<parseInt(empresa['numniv']); i++){
			if(i==0)
			{
				formatoEstructura = this.fsEstructura.findById('codest'+options.idtxt+i).getValue();
			}
			else
			{
				formatoEstructura += '-'+this.fsEstructura.findById('codest'+options.idtxt+i).getValue();
			}
		}
				 
		return formatoEstructura;
    }
    
    this.validarEstructuraCompleta =function()
	{
    	var estValida = true;
		for(var i=0; i<parseInt(empresa['numniv']); i++)
		{
			if(this.fsEstructura.findById('codest'+options.idtxt+i).getValue() == '')
			{
				estValida = false;
				break;
			}
		}
				 
		return estValida;
    }
    
}