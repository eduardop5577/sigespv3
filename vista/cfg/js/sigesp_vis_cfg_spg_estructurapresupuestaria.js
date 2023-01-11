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

barraherramienta    = true;
var datos = null;
var grid = null;
var win = null;
var parametros = '';
var cantidad = 0;
var ruta = '../../controlador/cfg/sigesp_ctr_cfg_spg_estructurapresupuestaria.php';
var RecordDef;
var grid1 = '';
var grid2 = '';
var grid3 = '';
var grid4 = '';
var grid5 = '';
var valor1 = '';
var valor2 = '';
var valor3 = '';
var valor4 = '';
var DataStore1 = '';
var DataStore2 = '';
var DataStore3 = '';
var DataStore4 = '';
var DataStore5 = '';
var Listo1 = false;
var Listo2 = false;
var Oper = '';
var DatosNuevo = "";
var tabs = '';
var mijson = "";
var plEstructuraPresupuestaria = null;
var estcencos = false; 

Ext.onReady(function()
{
	function getDatos(Metodo)
	{
        var myJSONObject = {
            "oper": Metodo,
            "numest": '1',
            "codestpro": "",
            "denestpro": "",
            "numcar": ""
        };
        
        var ObjSon = Ext.util.JSON.encode(myJSONObject);
        var parametros = 'ObjSon=' + ObjSon;
        Ext.Ajax.request({
            url: ruta,
            params: parametros,
            method: 'POST',
            success: function(resultado, request)
			{
            	var datos = resultado.responseText;
                if (datos != '')
				{
                    arr = datos.split("|");
                    var jsonserv = arr[1];
                    cantidad = arr[0];
                    var mijson = eval('(' + jsonserv + ')');
                    switch (Metodo)
					{
                        case 'getSesion':
                            for (i = 0; i < parseInt(cantidad); i++)
							{
                            	agregarTab(mijson.raiz[i].nombre_pest, 'grid' + i);
                            }
                            tabs.activate('0');
                            habilitarUna(0);
                         break;
                    }
                }
            }
        });
    }
    
    function ManejarTabActivo(tab)
	{
        num = parseInt(tab.id) + 1;
        if (grid1 == '' && Listo1 == false)
		{
            getgrid(1);
        }
        if (tab.id == 1 && grid2 == '' || tab.id == 2 && grid3 == '' || tab.id == 3 && grid4 == '' || tab.id == 4 && grid5 == '')
		{
            getgrid(num);
        }
        else
		{
			switch (parseInt(tab.id))
			{
				case 0:
					Ext.getCmp('codniv1').setValue('');
                   	grid1.getColumnModel().setHidden(3, true);
                   	grid1.getColumnModel().setHidden(4, true);
                   	grid1.getColumnModel().setHidden(5, true);
				 	break;

				case 1:
                    valor1 = formatoEstructuraNivel1(grid1.getSelectionModel().getSelected().get('codestpro1'));
                    estcla = grid1.getSelectionModel().getSelected().get('estcla');
                    den1 = grid1.getSelectionModel().getSelected().get('denestpro1');
                    tabanterior = tabs.getItem('0').title;
                    Ext.getCmp('codniv1').setValue(tabanterior+':'+valor1+'-'+den1);
					Ext.getCmp('codniv2').setValue('');
                    if (grid2 != '')
					{
                        ActualizarData(estcla,valor1, '0', '3', '4', '2');
                    }
                    if(grid1.getSelectionModel().getSelected().get('estcencos')=='1')
					{
                    	estcencos = true;
                    }
                    else
					{
                    	estcencos = false;
                    }
                    break;
                    
                case 2:
                	valor2 = formatoEstructuraNivel2(grid2.getSelectionModel().getSelected().get('codestpro2'));
                    den2 = grid2.getSelectionModel().getSelected().get('denestpro2');
                    estcla = grid1.getSelectionModel().getSelected().get('estcla');
                    tabanterior = tabs.getItem('1').title;
                    Ext.getCmp('codniv2').setValue(tabanterior+':'+valor2+'-'+den2);
					Ext.getCmp('codniv3').setValue('');
					if (grid3 != '')
					{
                        ActualizarData(estcla,valor1, valor2, '3', '4', '3');
                    }
                    if(estcencos)
					{
						grid3.getColumnModel().setHidden(3, false);                    
                    }
                    else
					{
                    	grid3.getColumnModel().setHidden(3, true);
                    }
                   	grid3.getColumnModel().setHidden(2, true);
                   	grid3.getColumnModel().setHidden(3, true);
                    break;
                    
                case 3:
                	valor3 = formatoEstructuraNivel3(grid3.getSelectionModel().getSelected().get('codestpro3'));
                    den3 = grid3.getSelectionModel().getSelected().get('denestpro3');
                    estcla = grid1.getSelectionModel().getSelected().get('estcla');
                    tabanterior = tabs.getItem('2').title;
                    Ext.getCmp('codniv3').setValue(tabanterior+':'+valor3+'-'+den3);
					Ext.getCmp('codniv4').setValue('');
					if (grid4 != '')
					{
                        ActualizarData(estcla,valor1, valor2, valor3, '0', '4');
                    }
                    break;
                    
                case 4:
                	valor4 = formatoEstructuraNivel4(grid4.getSelectionModel().getSelected().get('codestpro4'));
                    den4 = grid4.getSelectionModel().getSelected().get('denestpro4');
                    estcla = grid1.getSelectionModel().getSelected().get('estcla');
                    tabanterior = tabs.getItem('3').title;
                    Ext.getCmp('codniv4').setValue(tabanterior+':'+valor4+'-'+den4);
                    if (grid5 != '')
					{
                        ActualizarData(estcla,valor1, valor2, valor3, valor4, '5');
                    }
                    if(estcencos)
					{
						grid5.getColumnModel().setHidden(3, false);                    
                    }
                    else
					{
                    	grid5.getColumnModel().setHidden(3, true);
                    }
                   	grid5.getColumnModel().setHidden(2, true);
                   	grid5.getColumnModel().setHidden(3, true);
                    break;
            }
        }
    }
	    
    function deshabilitarAnt(tab)
	{
    	tabs.getItem(tab).disable();
    }
    
    function habilitarSiguiente()
	{
    	var tabActiva = tabs. getActiveTab();
    	var siguiente = parseInt(tabActiva.id) + 1;
    	
    	if (Oper != "incluyendo")
		{
	    	switch (parseInt(tabActiva.id))
			{
	    		case 0:
	    			if(grid1.getSelectionModel().getSelected() == undefined)
					{
	                	Ext.Msg.show({
							title:'Advertencia',
							msg: 'Debe seleccionar un resgitro de la estructura '+tabs.getItem('0').title,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
						});
	    			}
	    			else
					{
	    				tabs.activate(siguiente.toString());
	    				tabs.getItem(siguiente).enable();
	    				tabs.getItem(parseInt(tabActiva.id)).disable();
	    			}
	                break;
	            
	            case 1:
	    			if(grid2.getSelectionModel().getSelected() == undefined)
					{
	                	Ext.Msg.show({
							title:'Advertencia',
							msg: 'Debe seleccionar un resgitro de la estructura '+tabs.getItem('1').title,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
						});
	    			}
	    			else
					{
	    				tabs.activate(siguiente.toString());
	    				tabs.getItem(siguiente).enable();
	    				tabs.getItem(parseInt(tabActiva.id)).disable();
	    			}
	                break;
	             
	             case 2:
	    			if(grid3.getSelectionModel().getSelected() == undefined)
					{
	                	Ext.Msg.show({
							title:'Advertencia',
							msg: 'Debe seleccionar un resgitro de la estructura '+tabs.getItem('2').title,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
						});
	    			}
	    			else
					{
	    				tabs.activate(siguiente.toString());
	    				tabs.getItem(siguiente).enable();
	    				tabs.getItem(parseInt(tabActiva.id)).disable();
	    			}
	                break;
	             
	             case 3:
	    			if(grid4.getSelectionModel().getSelected() == undefined)
					{
	                	Ext.Msg.show({
							title:'Advertencia',
							msg: 'Debe seleccionar un resgitro de la estructura '+tabs.getItem('2').title,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
						});
	    			}
	    			else
					{
	    				tabs.activate(siguiente.toString());
	    				tabs.getItem(siguiente).enable();
	    				tabs.getItem(parseInt(tabActiva.id)).disable();
	    			}
	                break;
	                
	             case 4:
	    			if(grid5.getSelectionModel().getSelected() == undefined)
					{
	                	Ext.Msg.show({
							title:'Advertencia',
							msg: 'Debe seleccionar un resgitro de la estructura '+tabs.getItem('2').title,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
						});
	    			}
	    			else
					{
	    				tabs.activate(siguiente.toString());
	    				tabs.getItem(siguiente).enable();
	    				tabs.getItem(parseInt(tabActiva.id)).disable();
	    			}
	                break;
	    	}
    	}
    	else
		{
    		Ext.Msg.show({
				title:'Advertencia',
				msg: 'No puede dejar un registro vacio, guardelo o presiones deshacer para poder avanzar',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.WARNING
			});
    	}
    }
    
    function habilitarAnterior()
	{
    	var tabActiva = tabs. getActiveTab();
    	var anterior = parseInt(tabActiva.id) - 1;
    	
    	switch (parseInt(tabActiva.id))
		{
    		case 1:
    			tabs.activate(anterior.toString());
    			tabs.getItem(anterior).enable();
    			tabs.getItem(parseInt(tabActiva.id)).disable();
    			break;
             
             case 2:
    			tabs.activate(anterior.toString());
    			tabs.getItem(anterior).enable();
    			tabs.getItem(parseInt(tabActiva.id)).disable();
    			break;
             
             case 3:
    			tabs.activate(anterior.toString());
    			tabs.getItem(anterior).enable();
    			tabs.getItem(parseInt(tabActiva.id)).disable();
    			break;
                
             case 4:
    			tabs.activate(anterior.toString());
    			tabs.getItem(anterior).enable();
    			tabs.getItem(parseInt(tabActiva.id)).disable();
    			break;1
    	}
    }
    
    function habilitarUna(tab, paso)
	{
        UltiActual = cantidad - 1;
        for (var r = 0; r < cantidad; r++)
		{
            num2 = r + 1;
            if (r == tab)
			{
                if (r > 0)
				{
                    if (r == UltiActual)
					{
                        tabs.getItem(r - 1).enable();
                    }
                    else
					{
                        tabs.getItem(num2).enable();
                        tabs.getItem(r - 1).enable();
                        r++;
                    }
                }
                else
				{
                    if (paso)
					{
                        tabs.getItem(num2).enable();
                        r++;
                    }
                    tabs.getItem(r).enable();
                }
            }
            else
			{
                tabs.getItem(r).disable();
            }
        }
    }
    
    function mostrarEstatus(est)
	{
		if (est=='P')
		{
			return 'Proyecto';
		}
		else if (est=='A')
		{
			return 'Accion Centralizada';	
		}
		else if (est=='-')
		{
				return 'POR DEFECTO';	
		}
	}
    
	function mostrarIntercomp(intercomp)
	{
		if (intercomp=='1')
		{
				return 'Si';
		}
		else if (intercomp=='0')
		{
				return 'No';	
		}
	}
    
    function getgrid(numero)
	{
        //combo tipo de estructura
        var Tipo = [['Proyecto', 'P'], ['Accion Centralizada', 'A']]
        var storeTipo = new Ext.data.SimpleStore({
            fields: ['col', 'tipo'],
            data: Tipo
        });
        
        var ComboTipo = new Ext.form.ComboBox({
            store: storeTipo,
            editable: false,
            displayField: 'col',
            valueField: 'tipo',
            name: 'tipo',
            id: 'tipo',
            typeAhead: true,
            triggerAction: 'all',
            mode: 'local'
        })
        //fin combo estructura
        
        //combo intercompa&#241ia
        var intercomp = [['1', 'Si'], ['0', 'No']]
        var storeInter = new Ext.data.SimpleStore({
            fields: ['col', 'tipointer'],
            data: intercomp // from states.js
        });
        
        var ComboInter = new Ext.form.ComboBox({
            store: storeInter,
            editable: false,
            forceSelection: true,
            displayField: 'tipointer',
            valueField: 'col',
            name: 'interComp',
            id: 'Intercomp',
            typeAhead: true,
            triggerAction: 'all',
            mode: 'local'
        })
        //fin combo intercompa&#241ia
        
        //combo centro de costo
        var centrocostos = [['1', 'Si'], ['0', 'No']]
        var stcentrocosto = new Ext.data.SimpleStore({
            fields: ['codCencos', 'labelCencos'],
            data: centrocostos // from states.js
        });
        
        var cmbCentroCosto = new Ext.form.ComboBox({
            store: stcentrocosto,
            editable: false,
            forceSelection: true,
            displayField: 'labelCencos',
            name:'cenCos',
            valueField: 'codCencos',
            id: 'estcencos',
            typeAhead: true,
            triggerAction: 'all',
            mode: 'local'
        })
        //fin combo centro de costo
        
        Auxnum = numero;
        var myJSONString = "{'oper': 'catestpro', 'numest':" + numero + ",'codestpro" + numero + "': '','denestpro" + numero + "': ''";
        
        if (parseInt(Auxnum) > 1)
		{
            for (var ind = 1; ind < Auxnum; ind++)
			{
                myJSONString = myJSONString + ",'codestpro" + ind + "':''";
            }
        }

		RecordDef = Ext.data.Record.create([{
            name: 'codemp'
        }, {
            name: 'codestpro1'
        }, {
            name: 'codestpro2'
        }, {
            name: 'codestpro3'
        }, {
            name: 'codestpro4'
        }, {
            name: 'codestpro5'
        }, {
            name: 'estcla'
        }, {
            name: 'denestpro1'
        }, {
            name: 'denestpro2'
        }, {
            name: 'denestpro3'
        }, {
            name: 'denestpro4'
        }, {
            name: 'denestpro5'
        }, {
            name: 'estint'
        }, {
            name: 'sc_cuenta'
        }, {
            name: 'codfuefin'
        }, {
            name: 'estcencos'
        },{
        	name: 'codcencos'
        },{
        	name: 'editable'
        }]);
        
        switch (numero)
		{
            case 1:
                ActualizarData('-','0', '0', '3', '4', '1');
                DataStore1 = new Ext.data.Store({
                    proxy: new Ext.data.MemoryProxy(DatosNuevo),
                    reader: new Ext.data.JsonReader({
                        root: 'raiz',
                        id: "id"
                    }, RecordDef)
                });
                
                grid1 = new Ext.grid.EditorGridPanel({
                    width: 800,
                    height: 300,
                    autoScroll: true,
                    style:'margin-left:15px;',
                    border: true,
                    enableColumnHide: false, 
                    ds: DataStore1,
                    cm: new Ext.grid.ColumnModel([{
                        header: "C&#243digo",
                        width: 210,
                        sortable: true,
                        dataIndex: 'codestpro' + numero,
                        renderer:formatoEstructuraNivel1,
                        align:'center',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            id: 'codestr',
							editable: false,
                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: empresa['loncodestpro1'], onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-_');"}
                        })
                    }, {
                        header: "Denominaci&#243n",
                        width: 320,
                        sortable: true,
                        dataIndex: 'denestpro' + numero,
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-__@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ ');"}
                        })
                    }, {
                        header: "Tipo",
                        width: 130,
                        sortable: true,
                        dataIndex: 'estcla',
                        editor: ComboTipo,
						renderer:mostrarEstatus
                    }, {
                        header: "Intercompa&#241ia",
                        width: 100,
                        sortable: true,
                        dataIndex: 'estint',
                        editor: ComboInter,
						renderer:mostrarIntercomp
                    }, {
                        header: "Centro de Costos",
                        width: 100,
                        sortable: true,
                        dataIndex: 'estcencos',
                        editor: cmbCentroCosto,
						renderer:mostrarIntercomp
                    }, {
                        header: "Cuenta Contable",
                        width: 110,
                        sortable: true,
                        dataIndex: 'sc_cuenta',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            id: 'scgcuenta',
                            enableKeyEvents: true,
                            readOnly: true,
                            listeners: {
                                'keypress': function(Obj, e){
                                	Ext.Msg.hide();
                                    var whichCode = e.keyCode;
                                    if (whichCode == 38) {
                                        if (grid1.getSelectionModel().getSelected().get('estint') == 0) {
                                            Ext.MessageBox.alert('Mensaje', 'Debe colocar el valor de intercompañia en Si');
                                        }
                                        else {
                                            mostrarcatalogocuentas(grid1.getSelectionModel().getSelections());
                                        }
                                        
                                    }
                                }
                            }
                        })
                    }, {
                        header: "Fuente Financiamiento",
                        width: 100,
                        sortable: true,
                        dataIndex: 'codfuefin',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            enableKeyEvents: true,
                            readOnly: true,
                            id: 'codfuefin',
                            listeners: {
                                'keypress': function(Obj, e){
                                	Ext.Msg.hide();
                                    var whichCode = e.keyCode;
                                    if (whichCode == 38) {
                                        mostrarcatalogofuentes(grid1.getSelectionModel().getSelected())
                                    }
                                }
                            }
                        })
                    }, {
                        header: "C&#243digo Centro de Costos",
                        width: 100,
                        sortable: true,
                        dataIndex: 'codcencos',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            enableKeyEvents: true,
                            readOnly: true,
                            id: 'codcencos',
                            listeners: {
                                'keypress': function(Obj, e){
                                	Ext.Msg.hide();
                                    var whichCode = e.keyCode;
                                    if (whichCode == 38) {
                                    	var registro = grid1.getSelectionModel().getSelected();
                                        //creando datastore y columnmodel para el catalogo de agencias
										var reCentroCosto = Ext.data.Record.create([
															{name: 'codcencos'},
															{name: 'denominacion'}
											]);
										
										var dsCentroCosto =  new Ext.data.Store({
												reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCentroCosto)
											});
															
										var cmCentroCosto = new Ext.grid.ColumnModel([
									          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codcencos'},
									          				{header: "Descripci&#243;n", width: 40, sortable: true, dataIndex: 'denominacion'}
									        ]);
										//fin creando datastore y columnmodel para el catalogo de agencias
										
										var comCatCentroCostos = new com.sigesp.vista.comCatalogo({
											titvencat: 'Catalogo de Centro de Costos',
											anchoformbus: 450,
											altoformbus:130,
											anchogrid: 450,
											altogrid: 400,
											anchoven: 500,
											altoven: 400,
											datosgridcat: dsCentroCosto,
											colmodelocat: cmCentroCosto,
											arrfiltro:[{etiqueta:'C&#243;digo',id:'cocecos',valor:'codcencos'},
													   {etiqueta:'Descripci&#243;n',id:'descecos',valor:'denominacion'}],
											rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_centrocostos.php',
											parametros: 'ObjSon='+Ext.util.JSON.encode({'oper': 'catalogo'}),
											tipbus:'L',
											setdatastyle:'G',
											registroGrid:registro
										});
										
										comCatCentroCostos.mostrarVentana();
                                    }
                                }
                            }
                        })
                    }]),
                    selModel: new Ext.grid.RowSelectionModel({
                        singleSelect: true
                    }),
                    viewConfig: {
                        forceFit: true
                    },
                    stripeRows: true,
                    tbar:[{
                        text:'Deshacer',
                        tooltip:'Deshacer cambios',
                        iconCls:'remover',
                        id:'deshacer',
            			handler: function(){
            				Oper = '';
            				ActualizarData('-','0','0','3','4','1');
    					} 
                    }]
                });
                grid1.on({
                    'celldblclick':{
                    	fn: function(Grid, numFila, numColumna, e)
						{
                            if(numColumna=='0')
							{
								var v1 = Grid.getSelectionModel().getSelected().get('editable');
								if (v1 != 1)
								{
									Ext.Msg.show({
										title:'Mensaje',
										msg: 'El Código Presupuestario no puede ser editado',
										icon: Ext.MessageBox.INFO
									}); 
									Grid.startEditing(numFila,1);
								}
                            }
                            if(numColumna=='5')
							{
                        		Ext.Msg.show({
									title:'Mensaje',
									msg: 'Presione la tecla flecha arriba para abrir el catalogo de cuentas contables',
									icon: Ext.MessageBox.INFO
								});    
                            }
                            if(numColumna=='6')
							{
                        		Ext.Msg.show({
									title:'Mensaje',
									msg: 'Presione la tecla flecha arriba para abrir el catalogo de fuentes de financiamiento',
									icon: Ext.MessageBox.INFO
								});    
                            }
                            if(numColumna=='7')
							{
                        		Ext.Msg.show({
									title:'Mensaje',
									msg: 'Presione la tecla flecha arriba para abrir el catalogo de centros de costos',
									icon: Ext.MessageBox.INFO
								});    
                            }
                    	}
                    }
                })
                grid1.render('grid0');
                if (numero == cantidad)
				{
                    grid1.getColumnModel().setHidden(6, false);
                    grid1.getColumnModel().setHidden(7, false);
                }
                else
				{
                    grid1.getColumnModel().setHidden(6, true);
                    grid1.getColumnModel().setHidden(7, true);
                }
                if(cantidad == 5)
				{
                	grid1.getColumnModel().setHidden(2, true);
                }
                else
				{
                	grid1.getColumnModel().setHidden(2, false);
                }
                break
                
            case 2:
                DataStore2 = new Ext.data.Store({
                    proxy: new Ext.data.MemoryProxy(DatosNuevo),
                    reader: new Ext.data.JsonReader({
                        root: 'raiz', // The property which contains an Array of row objects
                        id: "id"
                    }, RecordDef)
                });
                
                grid2 = new Ext.grid.EditorGridPanel({
                    width: 800,
                    height: 300,
                    autoScroll: true,
                    style:'margin-left:15px;',
                    border: true,
                    enableColumnHide: false, 
                    ds: DataStore2,
                    cm: new Ext.grid.ColumnModel([{
                        header: "C&#243digo",
                        width: 170,
                        sortable: true,
                        dataIndex: 'codestpro' + numero,
                        renderer:formatoEstructuraNivel2,
                        align:'center',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: empresa['loncodestpro2'], onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-_');"}
                        })
                    }, {
                        header: "Denominaci&#243n",
                        width: 350,
                        sortable: true,
                        dataIndex: 'denestpro' + numero,
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-__@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ ');"}
                        })
                    }, {
                        header: "Fuente Financiamiento",
                        width: 100,
                        sortable: true,
                        dataIndex: 'codfuefin',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            enableKeyEvents: true,
                            id: 'codfuefin',
                            readOnly:true,
                            listeners: {
                                'keypress': function(Obj, e){
                                    var whichCode = e.keyCode;
                                    if (whichCode == 38) {
                                        mostrarcatalogofuentes(grid2.getSelectionModel().getSelected())
                                    }
                                }
                            }
                        })
                    }]),
                    selModel: new Ext.grid.RowSelectionModel({
                        singleSelect: false
                    }),
                    viewConfig: {
                        forceFit: true
                    },
                    stripeRows: true,
                    tbar:[{
                        text:'Deshacer',
                        tooltip:'Deshacer cambios',
                        iconCls:'remover',
                        id:'deshacer',
            			handler: function(){
            				Oper = '';
            				var v1 = grid1.getSelectionModel().getSelected().get('codestpro1');
            				var estcla = grid1.getSelectionModel().getSelected().get('estcla');
            				ActualizarData(estcla,v1,'0','0','0','2');
            			} 
                    }]
                });
                
                grid2.on({
                    'celldblclick':{
                    	fn: function(Grid, numFila, numColumna, e)
						{
                            if(numColumna=='0')
							{
								var v1 = Grid.getSelectionModel().getSelected().get('editable');
								if (v1 != 1)
								{
									Ext.Msg.show({
										title:'Mensaje',
										msg: 'El Código Presupuestario no puede ser editado',
										icon: Ext.MessageBox.INFO
									}); 
									Grid.startEditing(numFila,1);
								}
                            }
                            if(numColumna=='2')
							{
                        		Ext.Msg.show({
									title:'Mensaje',
									msg: 'Presione la tecla flecha arriba para abrir el catalogo de fuentes de financiamiento',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});    
                            }
                    	}
                    }
                })
                
                if (numero == cantidad)
				{
                    grid2.getColumnModel().setHidden(2, false);
                }
                else
				{
                    grid2.getColumnModel().setHidden(2, true);
                }
                grid2.render('grid1');
                break
				
            case 3:
                DataStore3 = new Ext.data.Store({
                    proxy: new Ext.data.MemoryProxy(DatosNuevo),
                    reader: new Ext.data.JsonReader({
                        root: 'raiz', // The property which contains an Array of row objects
                        id: "id"
                    }, RecordDef)
                });
                
                grid3 = new Ext.grid.EditorGridPanel({
                    width: 800,
                    height: 300,
                    autoScroll: true,
                    style:'margin-left:15px;',
                    border: true,
                    enableColumnHide: false, 
                    ds: DataStore3,
                    cm: new Ext.grid.ColumnModel([{
                        header: "C&#243digo",
                        width: 170,
                        sortable: true,
                        renderer:formatoEstructuraNivel3,
                        align:'center',
                        dataIndex: 'codestpro' + numero,
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: empresa['loncodestpro3'], onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-_');"}
                        })
                    }, {
                        header: "Denominaci&#243n",
                        width: 330,
                        sortable: true,
                        dataIndex: 'denestpro3',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ ');"}
                        })
                    }, {
                        header: "Fuente Financiamiento",
                        width: 150,
                        sortable: true,
                        dataIndex: 'codfuefin',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            id: 'codfuefin',
                            enableKeyEvents: true,
                            readOnly: true,
                            listeners: {
                                'keypress': function(Obj, e){
                                    var whichCode = e.keyCode;
                                    Ext.Msg.hide();
                                    if (whichCode == 38) {
                                        mostrarcatalogofuentes(grid3.getSelectionModel().getSelected())
                                    }
                                }
                            }
                        })
                    }, {
                        header: "C&#243digo Centro de Costos",
                        width: 100,
                        sortable: true,
                        dataIndex: 'codcencos',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            enableKeyEvents: true,
                            readOnly: true,
                            id: 'codcencos',
                            listeners: {
                                'keypress': function(Obj, e){
                                	Ext.Msg.hide();
                                    var whichCode = e.keyCode;
                                    if (whichCode == 38) {
                                    	var registro = grid3.getSelectionModel().getSelected();
                                        //creando datastore y columnmodel para el catalogo de agencias
										var reCentroCosto = Ext.data.Record.create([
															{name: 'codcencos'},
															{name: 'denominacion'}
											]);
										
										var dsCentroCosto =  new Ext.data.Store({
												reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCentroCosto)
											});
															
										var cmCentroCosto = new Ext.grid.ColumnModel([
									          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codcencos'},
									          				{header: "Descripci&#243;n", width: 40, sortable: true, dataIndex: 'denominacion'}
									        ]);
										//fin creando datastore y columnmodel para el catalogo de agencias
										
										var comCatCentroCostos = new com.sigesp.vista.comCatalogo({
											titvencat: 'Catalogo de Centro de Costos',
											anchoformbus: 450,
											altoformbus:130,
											anchogrid: 450,
											altogrid: 400,
											anchoven: 500,
											altoven: 400,
											datosgridcat: dsCentroCosto,
											colmodelocat: cmCentroCosto,
											arrfiltro:[{etiqueta:'C&#243;digo',id:'cocecos',valor:'codcencos'},
													   {etiqueta:'Descripci&#243;n',id:'descecos',valor:'denominacion'}],
											rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_centro_costos.php',
											parametros: 'ObjSon='+Ext.util.JSON.encode({'oper': 'catalogo'}),
											tipbus:'L',
											setdatastyle:'G',
											camposoadicionales : [{tipo:'cadena',id:'codcencos'}],
											registroGrid:registro
										});
										
										comCatCentroCostos.mostrarVentana();
                                    }
                                }
                            }
                        })
                    }]),
                    selModel: new Ext.grid.RowSelectionModel({
                        singleSelect: false
                    }),
                    viewConfig: {
                        forceFit: true
                    },
                    stripeRows: true,
                    tbar:[{
                        text:'Deshacer',
                        tooltip:'Deshacer cambios',
                        iconCls:'remover',
                        id:'deshacer',
            			handler: function(){
            				Oper = '';
            				var v1 = grid1.getSelectionModel().getSelected().get('codestpro1');
            				var estcla = grid1.getSelectionModel().getSelected().get('estcla');
            				var v2 = grid2.getSelectionModel().getSelected().get('codestpro2');
            				ActualizarData(estcla,v1,v2,'0','0','3');
            			} 
                    }]
                });
                
                grid3.on({
                    'celldblclick':{
                    	fn: function(Grid, numFila, numColumna, e)
						{
                            if(numColumna=='0')
							{
								var v1 = Grid.getSelectionModel().getSelected().get('editable');
								if (v1 != 1)
								{
									Ext.Msg.show({
										title:'Mensaje',
										msg: 'El Codigo Presupuestario no puede ser editado',
										icon: Ext.MessageBox.INFO
									}); 
									Grid.startEditing(numFila,1);
								}
                            }
							if(numColumna=='2')
							{
                        		Ext.Msg.show({
									title:'Mensaje',
									msg: 'Presione la tecla flecha arriba para abrir el catalogo de fuentes de financiamiento',
									icon: Ext.MessageBox.INFO
								});    
                            }
                            
                            if(numColumna=='3'){
                        		Ext.Msg.show({
									title:'Mensaje',
									msg: 'Presione la tecla flecha arriba para abrir el catalogo de centros de costos',
									icon: Ext.MessageBox.INFO
								});    
                            }
                    	}
                    } 
                });
                
                if (numero == cantidad)
				{
                    grid3.getColumnModel().setHidden(2, false);
                }
                else
				{
                    grid3.getColumnModel().setHidden(2, true);
                }
                grid3.render('grid2');
                break
            case 4:
                DataStore4 = new Ext.data.Store({
                    proxy: new Ext.data.MemoryProxy(DatosNuevo),
                    reader: new Ext.data.JsonReader({
                        root: 'raiz', // The property which contains an Array of row objects
                        id: "id"
                    }, RecordDef)
                });
                
                grid4 = new Ext.grid.EditorGridPanel({
                    width: 800,
                    height: 300,
                    autoScroll: true,
                    style:'margin-left:15px;',
                    border: true,
                    enableColumnHide: false, 
                    ds: DataStore4,
                    cm: new Ext.grid.ColumnModel([{
                        header: "C&#243digo",
                        width: 220,
                        sortable: true,
                        renderer:formatoEstructuraNivel4,
                        align:'center',
                        dataIndex: 'codestpro' + numero,
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: empresa['loncodestpro4'], onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-_');"}
                        })
                    }, {
                        header: "Denominaci&#243n",
                        width: 350,
                        sortable: true,
                        dataIndex: 'denestpro' + numero,
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíoúÁÉÍÓÚ ');"}
                        })
                    }, {
                        header: "Fuente Financiamiento",
                        width: 100,
                        sortable: true,
                        dataIndex: 'codfuefin',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            enableKeyEvents: true,
                            id: 'codfuefin',
                            readOnly:true,
                            listeners: {
                                'keypress': function(Obj, e){
                                    var whichCode = e.keyCode;
                                    if (whichCode == 38) {
                                        mostrarcatalogofuentes(grid4.getSelectionModel().getSelected())
                                    }
                                }
                            }
                        })
                    }]),
                    selModel: new Ext.grid.RowSelectionModel({
                        singleSelect: false
                    }),
                    viewConfig: {
                        forceFit: true
                    },
                    stripeRows: true,
                    tbar:[{
                        text:'Deshacer',
                        tooltip:'Deshacer cambios',
                        iconCls:'remover',
                        id:'deshacer',
            			handler: function(){
            				Oper = '';
            				var v1 = grid1.getSelectionModel().getSelected().get('codestpro1');
            				var estcla = grid1.getSelectionModel().getSelected().get('estcla');
            				var v2 = grid2.getSelectionModel().getSelected().get('codestpro2');
            				var v3 = grid3.getSelectionModel().getSelected().get('codestpro3');
            				ActualizarData(estcla,v1,v2,v3,'0','4');
            			} 
                    }]
                });
                
                if (numero == cantidad)
				{
                    grid4.getColumnModel().setHidden(2, false);
                }
                else
				{
                    grid4.getColumnModel().setHidden(2, true);
                }
                
                grid4.on({
                    'celldblclick':{
                    	fn: function(Grid, numFila, numColumna, e)
						{
                            if(numColumna=='0')
							{
								var v1 = Grid.getSelectionModel().getSelected().get('editable');
								if (v1 != 1)
								{
									Ext.Msg.show({
										title:'Mensaje',
										msg: 'El Codigo Presupuestario no puede ser editado',
										icon: Ext.MessageBox.INFO
									}); 
									Grid.startEditing(numFila,1);
								}
                            }
                            if(numColumna=='2')
							{
                        		Ext.Msg.show({
									title:'Mensaje',
									msg: 'Presione la tecla flecha arriba para abrir el catalogo de fuentes de financiamiento',
									icon: Ext.MessageBox.INFO
								});    
                            }
                        }
                    } 
                });
                grid4.render('grid3');
                break
				
            case 5:
                DataStore5 = new Ext.data.Store({
                    proxy: new Ext.data.MemoryProxy(DatosNuevo),
                    reader: new Ext.data.JsonReader({
                        root: 'raiz', // The property which contains an Array of row objects
                        id: "id"
                    }, RecordDef)
                
                });
                grid5 = new Ext.grid.EditorGridPanel({
                    width: 800,
                    height: 300,
                    autoScroll: true,
                    style:'margin-left:15px;',
                    border: true,
                    enableColumnHide: false, 
                    id: 'grid5',
                    ds: DataStore5,
                    cm: new Ext.grid.ColumnModel([{
                        header: "C&#243digo",
                        width: 220,
                        sortable: true,
                        renderer:formatoEstructuraNivel5,
                        align:'center',
                        dataIndex: 'codestpro' + numero,
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: empresa['loncodestpro5'], onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-_');"}
                        })
                    }, {
                        header: "Denominaci&#243n",
                        width: 350,
                        sortable: true,
                        dataIndex: 'denestpro' + numero,
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ ');"}
                        })
                    }, {
                        header: "Fuente Financiamiento",
                        width: 100,
                        sortable: true,
                        dataIndex: 'codfuefin',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            enableKeyEvents: true,
                            readOnly:true,
                            listeners: {
                                'keypress': function(Obj, e){
                                    var whichCode = e.keyCode;
                                    Ext.Msg.hide();
                                    if (whichCode == 38) {
                                        mostrarcatalogofuentes(grid5.getSelectionModel().getSelected())
                                    }
                                }
                            }
                        })
                    }, {
                        header: "C&#243digo Centro de Costos",
                        width: 100,
                        sortable: true,
                        dataIndex: 'codcencos',
                        editor: new Ext.form.TextField({
                            allowBlank: false,
                            enableKeyEvents: true,
                            id: 'codcencos',
                            readOnly:true,
                            listeners: {
                                'keypress': function(Obj, e){
                                	Ext.Msg.hide();
                                    var whichCode = e.keyCode;
                                    if (whichCode == 38) {
                                    	var registro = grid5.getSelectionModel().getSelected();
                                        //creando datastore y columnmodel para el catalogo de agencias
										var reCentroCosto = Ext.data.Record.create([
															{name: 'codcencos'},
															{name: 'denominacion'}
											]);
										
										var dsCentroCosto =  new Ext.data.Store({
												reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCentroCosto)
											});
															
										var cmCentroCosto = new Ext.grid.ColumnModel([
									          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codcencos'},
									          				{header: "Descripci&#243;n", width: 40, sortable: true, dataIndex: 'denominacion'}
									        ]);
										//fin creando datastore y columnmodel para el catalogo de agencias
										
										var comCatCentroCostos = new com.sigesp.vista.comCatalogo({
											titvencat: 'Catalogo de Centro de Costos',
											anchoformbus: 450,
											altoformbus:130,
											anchogrid: 450,
											altogrid: 400,
											anchoven: 500,
											altoven: 400,
											datosgridcat: dsCentroCosto,
											colmodelocat: cmCentroCosto,
											arrfiltro:[{etiqueta:'C&#243;digo',id:'cocecos',valor:'codcencos'},
													   {etiqueta:'Descripci&#243;n',id:'descecos',valor:'denominacion'}],
											rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_centro_costos.php',
											parametros: 'ObjSon='+Ext.util.JSON.encode({'oper': 'catalogo'}),
											tipbus:'L',
											setdatastyle:'G',
											camposoadicionales : [{tipo:'cadena',id:'codcencos'}],
											registroGrid:registro
										});
										
										comCatCentroCostos.mostrarVentana();
                                    }
                                }
                            }
                        })
                    }]),
                    selModel: new Ext.grid.RowSelectionModel({
                        singleSelect: false
                    }),
                    viewConfig: {
                        forceFit: true
                    },
                    stripeRows: true,
                    tbar:[{
                        text:'Deshacer',
                        tooltip:'Deshacer cambios',
                        iconCls:'remover',
                        id:'deshacer',
            			handler: function(){
            				Oper = '';
            				var v1 = grid1.getSelectionModel().getSelected().get('codestpro1');
            				var estcla = grid1.getSelectionModel().getSelected().get('estcla');
            				var v2 = grid2.getSelectionModel().getSelected().get('codestpro2');
            				var v3 = grid3.getSelectionModel().getSelected().get('codestpro3');
            				var v4 = grid4.getSelectionModel().getSelected().get('codestpro4');
            				ActualizarData(estcla,v1,v2,v3,v4,'5');
            			} 
                    }]
                });
                grid5.on({
                    'celldblclick':{
                    	fn: function(Grid, numFila, numColumna, e)
						{
                            if(numColumna=='0')
							{
								var v1 = Grid.getSelectionModel().getSelected().get('editable');
								if (v1 != 1)
								{
									Ext.Msg.show({
										title:'Mensaje',
										msg: 'El Codigo Presupuestario no puede ser editado',
										icon: Ext.MessageBox.INFO
									}); 
									Grid.startEditing(numFila,1);
								}
                            }
                            if(numColumna=='2')
							{
                        		Ext.Msg.show({
									title:'Mensaje',
									msg: 'Presione la tecla flecha arriba para abrir el catalogo de fuentes de financiamiento',
									icon: Ext.MessageBox.INFO
								});    
                            }
                            if(numColumna=='3')
							{
                        		Ext.Msg.show({
									title:'Mensaje',
									msg: 'Presione la tecla flecha arriba para abrir el catalogo de centros de costos',
									icon: Ext.MessageBox.INFO
								});    
                            }
                    	}
                    }
                });
                
                if (numero == cantidad)
				{
                    grid5.getColumnModel().setHidden(2, false);
                }
                else
				{
                    grid5.getColumnModel().setHidden(2, true);
                }
                grid5.render('grid4');
                break
        }
    }
    
    function ActualizarData(estcla,cod1, cod2, cod3, cod4, nivel)
	{
        cod1 = ue_rellenarcampo(cod1, 25);
        cod2 = ue_rellenarcampo(cod2, 25);
        cod3 = ue_rellenarcampo(cod3, 25);
        cod4 = ue_rellenarcampo(cod4, 25);
        var myJSONObject = {
            "oper": 'filtrarEst',
            "numest": nivel,
            "estcla": estcla,
            "cod1": cod1,
            "cod2": cod2,
            "cod3": cod3,
            "cod4": cod4
        };
        ObjSon = Ext.util.JSON.encode(myJSONObject);
        parametros = 'ObjSon=' + ObjSon;
        Ext.Ajax.request({
            url: ruta,
            params: parametros,
            method: 'POST',
            success: function(resultado, request)
			{
                datos = resultado.responseText;
                if (datos != '')
				{
                    var DatosNuevo = eval('(' + datos + ')');
                    if (DatosNuevo.raiz == null)
					{
                        DatosNuevo = {
                            "raiz": [{
                                "codestpro1": '',
                                "codestpro2": '',
                                "codestpro3": '',
                                "codestpro4": '',
                                "codestpro5": '',
                                "estcla": '',
                                "estint": '',
                                "sc_cuenta": '',
                                "denestpro1": '',
                                "denestpro2": '',
                                "denestpro3": '',
                                "denestpro4": '',
                                "denestpro5": '',
                                "codcencos": '',
                                "editable": '1'
                            }]
                        };
                    }
                    switch (nivel)
					{
                        case '1':
                            grid1.store.loadData(DatosNuevo);
                            break;
                        case '2':
                            grid2.store.loadData(DatosNuevo);
                            break;
                        case '3':
                            grid3.store.loadData(DatosNuevo);
                            break;
                        case '4':
                            grid4.store.loadData(DatosNuevo);
                            break;
                        case '5':
                            grid5.store.loadData(DatosNuevo);
                            break;
                    }
                }
            }
        });
    }
    
    function agregarTab(titulo, Elemento)
	{
    	tabs.add({
            title: titulo,
            listeners: {
                activate : ManejarTabActivo
            },
            contentEl: Elemento,
            id: Elemento.substr(Elemento.length - 1, 1),
            closable: false
        }).show();
    }
    
    function getobject()
	{
    	Ext.QuickTips.init();
        tabs = new Ext.TabPanel({
			frame : true,
			autoScroll : true,
			width : 950,
			height : 550,
			style: 'position:absolute;left:15px;top:95px'
		});
        
        plEstructuraPresupuestaria = new Ext.Panel({
			title: 'Estructura Presupuestaria',
			width : 1000,
			height : 600,
			frame:true,
			style : 'position:absolute;left:30px;top:60px',
			renderTo : 'tabs7',
			tbar:[{
				text:'Anterior',
            	tooltip:'Haga click para regresar al nivel anterior',
            	iconCls:'menuatras',
            	id:'agregar',
				handler: habilitarAnterior
        	}, '-', {
            	text:'Siguiente',
            	tooltip:'Haga click para avanzar al siguiente nivel',
            	iconCls:'menuitem',
            	id:'remover',
				handler: habilitarSiguiente
			}],
			items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:0px;top:0px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 0,
					items: [{
								xtype: 'textfield',
								fieldLabel: '',
								labelSeparator :'',
								style:'border:none;background:#f1f1f1;font-weight: bold',
								id: 'codniv1',
								width: 850,
								hideLabel:true
							}]
				}]
				},{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:0px;top:20px',
					items: [{
					layout: "form",
					border: false,
					labelWidth: 0,
					items: [{
								xtype: 'textfield',
								fieldLabel: '',
								labelSeparator :'',
								style:'border:none;background:#f1f1f1;font-weight: bold',
								id: 'codniv2',
								width: 850,
								hideLabel:true
						}]
				}]
				},{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:0px;top:40px',
				items: [{
							layout: "form",
							border: false,
							labelWidth: 0,
							items: [{
										xtype: 'textfield',
										fieldLabel: '',
										labelSeparator :'',
										style:'border:none;background:#f1f1f1;font-weight: bold',
										id: 'codniv3',
										width: 850,
										hideLabel:true
									}]
							}]
				},{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:0px;top:60px',
				items: [{
							layout: "form",
							border: false,
							labelWidth: 0,
							items: [{
										xtype: 'textfield',
										fieldLabel: '',
										labelSeparator :'',
										style:'border:none;background:#f1f1f1;font-weight: bold',
										id: 'codniv4',
										width: 850,
										hideLabel:true
									}]
						}]
				},{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:0px;top:80px',
					items: [{
								layout: "form",
								border: false,
								labelWidth: 0,
								items: [{
											xtype: 'textfield',
											fieldLabel: '',
											labelSeparator :'',
											style:'border:none;background:#f1f1f1;font-weight: bold',
											id: 'codniv5',
											width: 850,
											hideLabel:true
										}]
							}]
				},tabs]
		});
    }
    getobject();
    getDatos('getSesion');
});

function ActualizarData(estcla,cod1, cod2, cod3, cod4, nivel)
{
    cod1 = ue_rellenarcampo(cod1, 25);
    cod2 = ue_rellenarcampo(cod2, 25);
    cod3 = ue_rellenarcampo(cod3, 25);
    cod4 = ue_rellenarcampo(cod4, 25);
    var myJSONObject = {
        "oper": 'filtrarEst',
        "numest": nivel,
        "estcla": estcla,
        "cod1": cod1,
        "cod2": cod2,
        "cod3": cod3,
        "cod4": cod4
    };
    
    ObjSon = Ext.util.JSON.encode(myJSONObject);
    parametros = 'ObjSon=' + ObjSon;
    Ext.Ajax.request({
        url: ruta,
        params: parametros,
        method: 'POST',
        success: function(resultado, request)
		{
            datos = resultado.responseText;
            if (datos != '')
			{
                var DatosNuevo = eval('(' + datos + ')');
                if (DatosNuevo.raiz == null)
				{
                    DatosNuevo = {
                        "raiz": [{
                            "codemp": '',
                            "codestpro1": '',
                            "codestpro2": '',
                            "codestpro3": '',
                            "codestpro4": '',
                            "codestpro5": '',
                            "estcla": '',
                            "estint": '',
                            "sc_cuenta": '',
                            "denestpro1": '',
                            "denestpro2": '',
                            "denestpro3": '',
                            "denestpro4": '',
                            "denestpro5": '',
                            "codcencos": '',
                            "editable": '1'
                        }]
                    };
                }
                switch (nivel)
				{
                    case '1':
                        grid1.store.loadData(DatosNuevo);
                        break;
                    case '2':
                        grid2.store.loadData(DatosNuevo);
                        break;
                    case '3':
                        grid3.store.loadData(DatosNuevo);
                        break;
                    case '4':
                        grid4.store.loadData(DatosNuevo);
                        break;
                    case '5':
                        grid5.store.loadData(DatosNuevo);
                        break;
                }
            }
        }
    });
}

function ObtenerGrid(tab)
{
    switch (tab)
	{
        case '0':
            return grid1;
            break;
        case '1':
            return grid2;
            break;
        case '2':
            return grid3;
            break;
        case '3':
            return grid4;
            break;
        case '4':
            return grid5;
            break;
    }
}

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
    tabActual = tabs.getActiveTab().id;
    GridActual = ObtenerGrid(tabActual);
    if (Oper != "incluyendo")
	{
        var p = new RecordDef({
            codestpro1: '',
            codestpro2: '',
            codestpro3: '',
            codestpro5: '',
            codestpro4: '',
            denestpro2: '',
            estcla: '',
            estint: '',
            sc_cuenta: '',
            denestpro1: '',
            denestpro2: '',
            denestpro3: '',
            denestpro4: '',
            denestpro5: '',
            codcencos: '',
            estcencos:'',
            editable:'1'
        
        });
        
        next = GridActual.store.getCount();
        if (next == 1)
		{
            codigo1 = GridActual.store.getRange(0, 1);
            codigo2 = codigo1[0].get('codestpro1');
            if (codigo2 == '')
			{
                GridActual.startEditing(0, 0);
                GridActual.getSelectionModel().selectRow(0);
				GridActual.getSelectionModel().setEditable(1,true);
                filaActual = 0;
            }
            else
			{
                GridActual.store.insert(1, p);
                GridActual.startEditing(1, 0);
                GridActual.getSelectionModel().selectRow(1);
				GridActual.getSelectionModel().setEditable(1,true);
                filaActual = 1;
            }
        }
        else
		{
            if (next == 0)
			{
                GridActual.store.insert(0, p);
                GridActual.startEditing(0, 0);
                GridActual.getSelectionModel().selectRow(0);
                filaActual = 0;
            }
            else
			{
                codigo1 = GridActual.store.getRange(0, 1);
                codigo2 = codigo1[1].get('codestpro1');
                if (codigo2 == '')
				{
                    GridActual.startEditing(1, 0);
                    GridActual.getSelectionModel().selectRow(1);
                    filaActual = 1;
                }
                else
				{
                    GridActual.store.insert(next, p);
                    GridActual.startEditing(next, 0);
                    GridActual.getSelectionModel().selectRow(next);
                    filaActual = next;
                }
            }
		}
        Oper = "incluyendo";
    }
    else
	{
		irNuevo();
    }
}

function irGuardar()
{
    tabActual = tabs.getActiveTab().id;
    TipoProyecto = grid1.getSelectionModel().getSelected().get('estcla'); 
    Nivel = parseInt(tabActual) + 1;
    if (Oper == "incluyendo")
	{
        if (cantidad == Nivel)
		{
            eve = 'incluirUltimo';
        }
        else
		{
            eve = 'incluirestpro';
        }
        Mens = 'Incluido';
    }
    else
	{
        eve = 'actualizarvarios';
        Mens = 'Modificado';
    }
    
    switch (Nivel)
	{
        case 1:
            numDatos = DataStore1.getModifiedRecords();
            var reg = "{'oper':'" + eve + "','codmenu':'"+codmenu+"','numest':'1','datos':[";
            for (var i = 0; i <= numDatos.length - 1; i++)
			{
            	var estint = 0;
            	var estcencos = 0;
            	if(numDatos[i].get('estint')!="")
				{
            		estint = numDatos[i].get('estint');
            	}
            	if(numDatos[i].get('estcencos')!="")
				{
            		estint = numDatos[i].get('estcencos');
            	}
                codest1 = trim(numDatos[i].get('codestpro1'));
                if (i == 0)
				{
                    if (codest1 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el Codigo de la estructura Nivel 1');
                        return false;
                    }
                    if (numDatos[i].get('denestpro1') == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el nombre de la estructura');
                        return false;
                    }
					
					var estcla = numDatos[i].get('estcla');
                    if ((estcla == '') || (estcla == '-'))
					{
						if (cantidad == 5)	
						{
							var estcla = 'P';
						}
						else
						{
							Ext.MessageBox.alert('Mensaje', 'Debe Indicar el tipo de estructura');
							return false;
						}
                    }
		            codest1 = ue_rellenarcampo(codest1, 25);
                    reg = reg + "{'codestpro1':'" + codest1 + "','estcla':'" + estcla + "','denestpro1':'" + numDatos[i].get('denestpro1') + "','estint':'" + estint + "','estcencos':'" + estcencos + "','sc_cuenta':'" + numDatos[i].get('sc_cuenta') + "'";
                }
                else
				{
		            codest1 = ue_rellenarcampo(codest1, 25);
                    reg = reg + "{'codestpro1':'" + codest1 + "','estcla':'" + estcla + "','denestpro1':'" + numDatos[i].get('denestpro1') + "','estint':'" + estint + "','estcencos':'" + estcencos + "','sc_cuenta':'" + numDatos[i].get('sc_cuenta') + "'";
                }
                if (Nivel == cantidad)
				{
                    reg = reg + ",'codfuefin':'" + numDatos[i].get('codfuefin') + "'}";
                }
                else
				{
                    reg = reg + "}";
                }
            }
            reg = reg + "]}";
            break;
            
        case 2:
            numDatos = DataStore2.getModifiedRecords();
            var reg = "{'oper':'" + eve + "','codmenu':'"+codmenu+"','numest':'2','datos':[";
            for (var i = 0; i <= numDatos.length - 1; i++)
			{
                codest2 = trim(numDatos[i].get('codestpro2'));
                valor1 = trim(valor1);
                if (i == 0)
				{
                    if (valor1 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 1');
                        return false;
                    }
                    if (codest2 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 2');
                        return false;
                    }
                    if (numDatos[i].get('denestpro2') == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el nombre de la estructura');
                        return false;
                    }
					codest2 = ue_rellenarcampo(codest2, 25);
					valor1 = ue_rellenarcampo(valor1, 25);
                    reg = reg + "{'codestpro1':'" + valor1 + "','codestpro2':'" + codest2 + "','estcla':'" + TipoProyecto + "','denestpro2':'" + numDatos[i].get('denestpro2') + "'";
                }
                else
				{
					codest2 = ue_rellenarcampo(codest2, 25);
					valor1 = ue_rellenarcampo(valor1, 25);
                    reg = reg + ",{'codestpro1':'" + valor1 + "','codestpro2':'" + codest2 + "','estcla':'" + TipoProyecto + "','denestpro2':'" + numDatos[i].get('denestpro2') + "'";
                }
                if (Nivel == cantidad)
				{
                    reg = reg + ",'codfuefin':'" + numDatos[i].get('codfuefin') + "'}";
                }
                else
				{
                    reg = reg + "}";
                }
            }
            reg = reg + "]}";
            break;
            
        case 3:
            numDatos = DataStore3.getModifiedRecords();
            var reg = "{'oper':'" + eve + "','codmenu':'"+codmenu+"','numest':'3','datos':[";
            for (var i = 0; i <= numDatos.length - 1; i++)
			{
                codest3 = trim(numDatos[i].get('codestpro3'));
                valor1 = trim(valor1);
                valor2 = trim(valor2);
                if (i == 0)
				{
                    if (valor1 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 1');
                        return false;
                    }
                    if (valor2 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 2');
                        return false;
                    }
                    if (codest3 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 3');
                        return false;
                    }
                    if (numDatos[i].get('denestpro3') == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el nombre de la estructura');
                        return false;
                    }
					codest3 = ue_rellenarcampo(codest3, 25);
					valor1 = ue_rellenarcampo(valor1, 25);
					valor2 = ue_rellenarcampo(valor2, 25);
                    reg = reg + "{'codestpro1':'" + valor1 + "','codestpro2':'" + valor2 + "','codestpro3':'" + codest3 + "','estcla':'" + TipoProyecto + "','denestpro3':'" + numDatos[i].get('denestpro3') + "'";
                }
                else
				{
					codest3 = ue_rellenarcampo(codest3, 25);
					valor1 = ue_rellenarcampo(valor1, 25);
					valor2 = ue_rellenarcampo(valor2, 25);
                    reg = reg + ",{'codestpro1':'" + valor1 + "','codestpro2':'" + valor2 + "','codestpro3':'" + codest3 + "','estcla':'" + TipoProyecto + "','denestpro3':'" + numDatos[i].get('denestpro3') + "'";
                }
                
                if(numDatos[i].get('codfuefin')==undefined || numDatos[i].get('codfuefin')=='')
				{
            		reg = reg + ",'codfuefin':'--'";
            	}
            	else
				{
            		reg = reg + ",'codfuefin':'" + numDatos[i].get('codfuefin') + "'";
            	}
            	
            	if(numDatos[i].get('codcencos')==undefined || numDatos[i].get('codcencos')=='')
				{
            		reg = reg + ",'codcencos':'000'}";
            	}
            	else
				{
            		reg = reg + ",'codcencos':'" + numDatos[i].get('codcencos') + "'}";
            	}
            }
            reg = reg + "]}";
            break;
            
        case 4:
            numDatos = DataStore4.getModifiedRecords();
            var reg = "{'oper':'" + eve + "','codmenu':'"+codmenu+"','numest':'4','datos':[";
            for (var i = 0; i <= numDatos.length - 1; i++) {
                codest4 = trim(numDatos[i].get('codestpro4'));
                valor1 = trim(valor1);
                valor2 = trim(valor2);
                valor3 = trim(valor3);
                if (i == 0)
				{
                    if (valor1 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 1');
                        return false;
                    }
                    if (valor2 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 2');
                        return false;
                    }
                    if (valor3 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 3');
                        return false;
                    }
                    if (codest4 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 4');
                        return false;
                    }
                    if (numDatos[i].get('denestpro4') == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el nombre de la estructura');
                        return false;
                    }
					codest4 = ue_rellenarcampo(codest4, 25);
					valor1 = ue_rellenarcampo(valor1, 25);
					valor2 = ue_rellenarcampo(valor2, 25);
					valor3 = ue_rellenarcampo(valor3, 25);
                    reg = reg + "{'codestpro1':'" + valor1 + "','codestpro2':'" + valor2 + "','codestpro3':'" + valor3 + "','codestpro4':'" + codest4 + "','estcla':'" + TipoProyecto + "','denestpro4':'" + numDatos[i].get('denestpro4') + "'";
                }
                else
				{
					codest4 = ue_rellenarcampo(codest4, 25);
					valor1 = ue_rellenarcampo(valor1, 25);
					valor2 = ue_rellenarcampo(valor2, 25);
					valor3 = ue_rellenarcampo(valor3, 25);
                    reg = reg + ",{'codestpro1':'" + valor1 + "','codestpro2':'" + valor2 + "','codestpro3':'" + valor3 + "','codestpro4':'" + codest4 + "','estcla':'" + TipoProyecto + "','denestpro4':'" + numDatos[i].get('denestpro4') + "'";
                }
                if (Nivel == cantidad)
				{
                    reg = reg + ",'codfuefin':'" + numDatos[i].get('codfuefin') + "'}";
                }
                else
				{
                    reg = reg + "}";
                }
            }
            reg = reg + "]}";
            break;
            
        case 5:
            numDatos = DataStore5.getModifiedRecords();
            var reg = "{'oper':'" + eve + "','codmenu':'"+codmenu+"','numest':5,'datos':[";
            for (var i = 0; i <= numDatos.length - 1; i++)
			{
                codest5 = trim(numDatos[i].get('codestpro5'));
                valor1 = trim(valor1);
                valor2 = trim(valor2);
                valor3 = trim(valor3);
                valor4 = trim(valor4);
                if (i == 0)
				{
                    if (valor1 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 1');
                        return false;
                    }
                    if (valor2 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 2');
                        return false;
                    }
                    if (valor3 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 3');
                        return false;
                    }
                    if (valor4 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 4');
                        return false;
                    }
                    if (codest5 == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el codigo de la estructura nivel 5');
                        return false;
                    }
                    if (numDatos[i].get('denestpro5') == '')
					{
                        Ext.MessageBox.alert('Mensaje', 'Debe Indicar el nombre de la estructura');
                        return false;
                    }
					codest5 = ue_rellenarcampo(codest5, 25);
					valor1 = ue_rellenarcampo(valor1, 25);
					valor2 = ue_rellenarcampo(valor2, 25);
					valor3 = ue_rellenarcampo(valor3, 25);
					valor4 = ue_rellenarcampo(valor4, 25);
                    reg = reg + "{'codestpro1':'" + valor1 + "','codestpro2':'" + valor2 + "','codestpro3':'" + valor3 + "','codestpro4':'" + valor4 + "','codestpro5':'" + codest5 + "','estcla':'" + TipoProyecto + "','denestpro5':'" + numDatos[i].get('denestpro5') + "'";
                }
                else
				{
					codest5 = ue_rellenarcampo(codest5, 25);
					valor1 = ue_rellenarcampo(valor1, 25);
					valor2 = ue_rellenarcampo(valor2, 25);
					valor3 = ue_rellenarcampo(valor3, 25);
					valor4 = ue_rellenarcampo(valor4, 25);
                    reg = reg + ",{'codestpro1':'" + valor1 + "','codestpro2':'" + valor2 + "','codestpro3':'" + valor3 + "','codestpro4':'" + valor4 + "','codestpro5':'" + codest5 + "','estcla':'" + TipoProyecto + "','denestpro5':'" + numDatos[i].get('denestpro5') + "'";
                }
                
                 if (Nivel == cantidad)
				 {
                	if(numDatos[i].get('codfuefin')==undefined || numDatos[i].get('codfuefin')=='')
					{
                		reg = reg + ",'codfuefin':'--'";
                	}
                	else
					{
                		reg = reg + ",'codfuefin':'" + numDatos[i].get('codfuefin') + "'";
                	}
                	
                	if(numDatos[i].get('codcencos')==undefined || numDatos[i].get('codcencos')=='')
					{
                		reg = reg + ",'codcencos':'000'}";
                	}
                	else
					{
                		reg = reg + ",'codcencos':'" + numDatos[i].get('codcencos') + "'}";
                	}
                }
                else
				{
                    reg = reg + "}";
                }
            }
            reg = reg + "]}";
            break;
    }
    Obj = eval('(' + reg + ')');
    ObjSon = JSON.stringify(Obj);
    parametros = 'ObjSon=' + ObjSon;
    Ext.Ajax.request({
        url: ruta,
        params: parametros,
        method: 'POST',
        success: function(resultad, request)
		{
            datos = resultad.responseText;
            var Registros = datos.split("|");
            Cod = Registros[1];
            if (Cod != '')
			{
                Ext.MessageBox.alert('Mensaje', 'Registro ' + Mens + ' con exito ');
                GridActual = ObtenerGrid(tabActual);
                GridActual.store.commitChanges();
                ActualizarData(TipoProyecto,valor1, valor2, valor3, valor4, Nivel.toString());
                oper = '';
            }
            else
			{
                Ext.MessageBox.alert('Mensaje', 'El registro con cota ');
            }
        },
        failure: function(result, request){
            Ext.MessageBox.alert('Error', result.responseText);
        }
    });
    Oper = '';
}

function irEliminar()
{
    TipoProyecto = grid1.getSelectionModel().getSelected().get('estcla');
    tabActual = tabs.getActiveTab().id;
    Nivel = parseInt(tabActual) + 1;
    if (cantidad == Nivel)
	{
        eve = 'eliminarUltimo';
    }
    else
	{
        eve = 'eliminarUltimo';
    }
    switch (Nivel)
	{
        case 1:
            valor1 = grid1.getSelectionModel().getSelected().get('codestpro1');
            valor1 = ue_rellenarcampo(valor1, 25);
            var reg = "{'oper':'" + eve + "','codmenu':'"+codmenu+"','numest':'1','datos':[";
            reg = reg + "{'codestpro1':'" + valor1 + "','estcla':'" + TipoProyecto + "'}";
            reg = reg + "]}";
            break;
            
        case 2:
            valor2 = grid2.getSelectionModel().getSelected().get('codestpro2');
            valor1 = ue_rellenarcampo(valor1, 25);
            valor2 = ue_rellenarcampo(valor2, 25);
            var reg = "{'oper':'" + eve + "','codmenu':'"+codmenu+"','numest':'2','datos':[";
            reg = reg + "{'codestpro1':'" + valor1 + "','codestpro2':'" + valor2 + "','estcla':'" + TipoProyecto + "'}";
            reg = reg + "]}";
            break;
            
        case 3:
            valor3 = grid3.getSelectionModel().getSelected().get('codestpro3');
            valor1 = ue_rellenarcampo(valor1, 25);
            valor2 = ue_rellenarcampo(valor2, 25);
            valor3 = ue_rellenarcampo(valor3, 25);
            var reg = "{'oper':'" + eve + "','codmenu':'"+codmenu+"','numest':'3','datos':[";
            reg = reg + "{'codestpro1':'" + valor1 + "','codestpro2':'" + valor2 + "','codestpro3':'" + valor3 + "','estcla':'" + TipoProyecto + "'}";
            reg = reg + "]}";
            break;
            
        case 4:
            valor4 = grid4.getSelectionModel().getSelected().get('codestpro4');
            valor1 = ue_rellenarcampo(valor1, 25);
            valor2 = ue_rellenarcampo(valor2, 25);
            valor3 = ue_rellenarcampo(valor3, 25);
            valor4 = ue_rellenarcampo(valor4, 25);
            var reg = "{'oper':'" + eve + "','codmenu':'"+codmenu+"','numest':'4','datos':[";
            reg = reg + "{'codestpro1':'" + valor1 + "','codestpro2':'" + valor2 + "',,'codestpro3':'" + valor3 + "','codestpro4':'" + valor4 + "','estcla':'" + TipoProyecto + "'}";
            reg = reg + "]}";
            break;
            
        case 5:
            valor5 = grid5.getSelectionModel().getSelected().get('codestpro5');
            valor1 = ue_rellenarcampo(valor1, 25);
            valor2 = ue_rellenarcampo(valor2, 25);
            valor3 = ue_rellenarcampo(valor3, 25);
            valor4 = ue_rellenarcampo(valor4, 25);
            valor5 = ue_rellenarcampo(valor5, 25);
            codfuefin = grid5.getSelectionModel().getSelected().get('codfuefin');
            var reg = "{'oper':'" + eve + "','codmenu':'"+codmenu+"','numest':'5','datos':[";
            reg = reg + "{'codestpro1':'" + valor1 + "','codestpro2':'" + valor2 + "',,'codestpro3':'" + valor3 + "','codestpro4':'" + valor4 + "','codestpro5':'" + valor5 + "','estcla':'" + TipoProyecto + "','codfuefin':'" + codfuefin + "'}";
            reg = reg + "]}";
            break;
    }
    var Result;
    Ext.MessageBox.confirm('Confirmar', 'Desea eliminar este registro?', Result);
    function Result(btn)
	{
        if (btn == 'yes')
		{
            parametros = 'ObjSon=' + reg;
            Mensa = "Eliminado";
            Ext.Ajax.request({
                url: ruta,
                params: parametros,
                method: 'POST',
                success: function(resultad, request)
				{
                    datos = resultad.responseText;
                    var Registros = datos.split("|");
                    if (Registros[1] == '1')
					{
                        Ext.MessageBox.alert('Mensaje', 'Registro ' + Mensa + ' con &#233;xito');
                        ActualizarData(TipoProyecto,valor1, valor2, valor3, valor4, Nivel.toString())
                    }
                    else
					{
                        Ext.MessageBox.alert('Error', 'No se pudo eliminar el registro, verifique que no posea registros asociados');
                    }
                },
                failure: function(result, request)
				{
                    Ext.MessageBox.alert('Error', 'Hubo un error en el proceso, por favor intente de nuevo');
                }
            });
        }
    };
}

function formatoEstructuraNivel1(estructura)
{
	var formato="";
	formato = estructura.substr(-empresa['loncodestpro1']);
	return formato;
}

function formatoEstructuraNivel2(estructura)
{
	var formato="";
	formato = estructura.substr(-empresa['loncodestpro2']);
	return formato;
}

function formatoEstructuraNivel3(estructura)
{
	var formato="";
	formato = estructura.substr(-empresa['loncodestpro3']);
	return formato;
}

function formatoEstructuraNivel4(estructura)
{
	var formato="";
	formato = estructura.substr(-empresa['loncodestpro4']);
	return formato;
}

function formatoEstructuraNivel5(estructura)
{
	var formato="";
	formato = estructura.substr(-empresa['loncodestpro5']);
	return formato;
}