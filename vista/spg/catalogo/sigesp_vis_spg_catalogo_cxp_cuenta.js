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

var dataStoreCuentaGasto="";
var formBusquedaCuentaGasto="";
var gridCuentaGasto="";
var ventanaEstructura="";


function creardataStoreCuentaGasto(codestpro,cuentaspg,dencuentaspg,cuentascg)
{
		var JSONObject ={
			'operacion': 'catalogootrocredito',
			'codestpro1':codestpro[0],
			'codestpro2':codestpro[1],
			'codestpro3':codestpro[2],
			'codestpro4':codestpro[3],
			'codestpro5':codestpro[4],
			'estcla':codestpro[5],
			'spg_cuenta':cuentaspg,
			'denominacion':dencuentaspg,
			'sc_cuenta':cuentascg
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/spg/sigesp_ctr_spg_spgcuenta.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoCuentaGasto = eval('(' + datos + ')');
			if(objetoCuentaGasto.raiz != null && objetoCuentaGasto.raiz!="")
			{
				dataStoreCuentaGasto.loadData(objetoCuentaGasto);
			}
			else
			{
				Ext.Msg.alert('Mensaje','No existen cuentas de gasto con el criterio seleccionado, verifique por favor');
			}
		}	
	})
}

function actdataStoreCuentaGasto(criterio,cadena)
{
	dataStoreCuentaGasto.filter(criterio,cadena,true,false);
}

function creargridCuentaGasto()
{
	 //creardataStoreCuentaGasto();
	
	registroCuentaGasto = Ext.data.Record.create([
	                  							{name: 'codestpro1'},    
	                  							{name: 'codestpro2'},
	                  							{name: 'codestpro3'},
	                  							{name: 'codestpro4'},
	                  							{name: 'codestpro5'},
	                  							{name: 'estcla'},
	                  							{name: 'programatica'},
	                  							{name: 'spg_cuenta'},
	                  							{name: 'denominacion'},
	                  							{name: 'sc_cuenta'}
	                  						]);	
	                  	
	                  	   var objetoCuentaGasto={"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"codestpro4":'',
	                  									   "codestpro5":'',"estcla":'',"programatica":'',"spg_cuenta":'',"denominacion":'',"sc_cuenta":''}]};
	
	dataStoreCuentaGasto =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(objetoCuentaGasto),
		reader: new Ext.data.JsonReader({
		root: 'raiz',             
		id: "id"   
		}
		,
	    registroCuentaGasto  
		),
		data: objetoCuentaGasto
  	});	
	
	 gridCuentaGasto = new Ext.grid.GridPanel({
	 width:770,
	 height:200,
	 autoScroll:true,
     border:true,
     style:"margin-top: 10px;",
     ds: dataStoreCuentaGasto,
     cm: new Ext.grid.ColumnModel([
          {header: "Programatica", width: 30, sortable: true,   dataIndex: 'programatica'},                         
          {header: "Cuenta", width: 30, sortable: true,   dataIndex: 'spg_cuenta'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denominacion'},
          {header: "Cuenta Contable", width: 50, sortable: true, dataIndex: 'sc_cuenta'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      },
      listeners:{
    	         'celldblclick': function(grid,fila,columna,evento){
    	  															 pasarDatosgridCuentaGasto(gridCuentaGasto.getSelectionModel().getSelected());
    	  															 gridCuentaGasto.destroy();
    	  			                        		   				 ventanaEstructura.destroy();
                                                                   }
      
       }
      });            
}

function formatoEstructuraPresupuestaria(estructura)
{
 var formatoEstructura="";
 switch(parseInt(empresa['numniv']))
 {
  case 1 : formatoEstructura = estructura.substr(-empresa['loncodestpro1']);
           break;
           
  case 2 : 
	      var estructura1 = estructura.substr(0,25);
	      var estructura2 = estructura.substr(25,25);
	      formatoEstructura = estructura1.substr(-empresa['loncodestpro1'])+" - "+estructura2.substr(-empresa['loncodestpro2']);
	      break
	      
  case 3 : 
      var estructura1 = estructura.substr(0,25);
      var estructura2 = estructura.substr(25,25);
      var estructura3 = estructura.substr(50,25);
      formatoEstructura = estructura1.substr(-empresa['loncodestpro1'])+" - "+estructura2.substr(-empresa['loncodestpro2'])+" - "+estructura3.substr(-empresa['loncodestpro3']);
  break;
  
  case 4 : 
      var estructura1 = estructura.substr(0,25);
      var estructura2 = estructura.substr(25,25);
      var estructura3 = estructura.substr(50,25);
      var estructura4 = estructura.substr(75,25);
      formatoEstructura = estructura1.substr(-empresa['loncodestpro1'])+" - "+estructura2.substr(-empresa['loncodestpro2'])+" - "+estructura3.substr(-empresa['loncodestpro3'])+" - "+estructura4.substr(-empresa['loncodestpro4']);
  break;
  
  case 5 : 
      var estructura1 = estructura.substr(0,25);
      var estructura2 = estructura.substr(25,25);
      var estructura3 = estructura.substr(50,25);
      var estructura4 = estructura.substr(75,25);
      var estructura5 = estructura.substr(100,25);
      formatoEstructura = estructura1.substr(-empresa['loncodestpro1'])+" - "+estructura2.substr(-empresa['loncodestpro2'])+" - "+estructura3.substr(-empresa['loncodestpro3'])+" - "+estructura4.substr(-empresa['loncodestpro4'])+" - "+estructura5.substr(-empresa['loncodestpro4']);
 }
 
 return formatoEstructura;
}

function pasarDatosgridCuentaGasto(registro)
{
	Ext.getCmp('spg_cuenta').setValue(registro.get('spg_cuenta'));
	Ext.getCmp('codestpro1').setValue(registro.get('codestpro1'));
	Ext.getCmp('codestpro2').setValue(registro.get('codestpro2'));
	Ext.getCmp('codestpro3').setValue(registro.get('codestpro3'));
	Ext.getCmp('codestpro4').setValue(registro.get('codestpro4'));
	Ext.getCmp('codestpro5').setValue(registro.get('codestpro5'));
	Ext.getCmp('codestpro').setValue(formatoEstructuraPresupuestaria(registro.get('codestpro1')+registro.get('codestpro2')+registro.get('codestpro3')+registro.get('codestpro4')+registro.get('codestpro5')));
	Ext.getCmp('estcla').setValue(registro.get('estcla'));			
}

function mostrarNumDigNiv1(estructura)
{
 var formatoEstructura="";
 formatoEstructura = estructura.substr(-empresa['loncodestpro1'])
 return formatoEstructura;
}

function mostrarNumDigNiv2(estructura)
{
 var formatoEstructura="";
 formatoEstructura = estructura.substr(-empresa['loncodestpro2'])
 return formatoEstructura;
}

function mostrarNumDigNiv3(estructura)
{
 var formatoEstructura="";
 formatoEstructura = estructura.substr(-empresa['loncodestpro3'])
 return formatoEstructura;
}

function mostrarNumDigNiv4(estructura)
{
 var formatoEstructura="";
 formatoEstructura = estructura.substr(-empresa['loncodestpro4'])
 return formatoEstructura;
}

function mostrarNumDigNiv5(estructura)
{
 var formatoEstructura="";
 formatoEstructura = estructura.substr(-empresa['loncodestpro5'])
 return formatoEstructura;
}

function mostrarEstatus(est){
	
	if (est=='P'){
			return 'Proyecto';
	}else if (est=='A'){
			return 'Acci&#243;n Centralizada';	
	}else if (est=='-'){
			return 'POR DEFECTO';	
	}
}

function limpiarGridCuentas()
{
	dataStoreCuentaGasto.removeAll();
}

function agregarListenersEstructura(objfieldsetestructura)
{
	for(var i = 1; i<= parseInt(empresa['numniv']); i++)
	{
		objfieldsetestructura.agregarListenerBoton(i,limpiarGridCuentas);
	}
}
function mostrarCatalogoCuentaGasto()
{
	var fieldSetEstructura = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		mostrarDenominacion:true,
		idtxt:'1'
		});
    creargridCuentaGasto();
    agregarListenersEstructura(fieldSetEstructura);
      //Ext.getCmp(fieldSetEstructura.fieldSetEstPre.findById('btnest1'+(parseInt(empresa['numniv'])-1))).addListener('click',function(){alert("Hola");});
      //fieldSetEstructura.agregarListenerBoton(empresa['numniv'],limpiarGridCuentas);
	  ventanaEstructura = new Ext.Window({
		                        id:'venestrutuctura',
		                        width:800,
		                        height:550,
		                        layout:'fit',
		                        border:false,
		                        modal: true,
		                        closable:false,
		                        renderTo:Ext.getBody(),
		                        frame:true,
		                        title:"Catalogo de Cuentas Presupuestaria",
		                        items:[{
		                                xtype:'form',
		                                id:'formestructura',
		                                defaultType:'textfield',
		                                frame:true,
		                                method:'post',
		                                items:[
		                                       fieldSetEstructura.fieldSetEstPre,
		                                       {
		                                        fieldLabel: 'Cuenta de Gasto',
		                                        name: 'codcuegasto',
		                           				id:'codcuegasto',
		                           				width:200,
		                           				xtype:'textfield',
		                           				changeCheck: function(){
		                           							var v = this.getValue();
		                           							actdataStoreCuentaGasto('spg_cuenta',v);
		                           							if(String(v) !== String(this.startValue))
		                           							{
		                           								this.fireEvent('change', this, v, this.startValue);
		                           							} 
		                           							},							 
		                           							initEvents : function()
		                           							{
		                           								AgregarKeyPress(this);
		                           							}               
		                                 			},{
		                           			                fieldLabel: 'Denominaci&#243;n',
		                           			                name: 'dencuegasto',
		                           			                id:'dencuegasto',
		                           			                width:250,
		                           			                xtype:'textfield',
		                           							changeCheck: function()
		                           							{
		                           										var v = this.getValue();
		                           										actdataStoreCuentaGasto('denominacion',v);
		                           										if(String(v) !== String(this.startValue))
		                           										{
		                           											this.fireEvent('change', this, v, this.startValue);
		                           										} 
		                           										},							 
		                           										initEvents : function()
		                           										{
		                           											AgregarKeyPress(this);
		                           										}
		                           			            },{
		                           			                fieldLabel: 'Cuenta Contable',
		                           			                name: 'codcuecontable',
		                           			                id:'codcuecontable',
		                           			                width:200,
		                           			                xtype:'numberfield',
		                           							changeCheck: function()
		                           							{
		                           										var v = this.getValue();
		                           										actdataStoreCuentaGasto('sc_cuenta',v);
		                           										if(String(v) !== String(this.startValue))
		                           										{
		                           											this.fireEvent('change', this, v, this.startValue);
		                           										} 
		                           										},							 
		                           										initEvents : function()
		                           										{
		                           											AgregarKeyPress(this);
		                           										}
		                           			            },
		                           			            {
														 xtype:'button',
		                           			             iconCls: "menubuscar",
		                           			             text: "Buscar cuentas...",
														 style:   "margin-left: 300px;margin-top: 5px",
														 id:'btnbuscar',
														 handler: function(){
		                           			            	                  var estructura = null;
		                           			            	                  estructura = fieldSetEstructura.obtenerArrayEstructura();
			                           			            	              /*if(fieldSetEstructura.obtenerValorNivel(empresa['numniv']) == "")
			                           			            	              {
			                           			            	            	Ext.Msg.show({
			                           			            	 			   	title:'Mensaje',
			                           			            	 			   	msg: 'Debe indicar la  estructura presupuestaria completa, verifique por favor',
			                           			            	 			   	buttons: Ext.Msg.OK,
			                           			            	 			   	animEl: 'elId',
			                           			            	 			   	icon: Ext.MessageBox.ERROR,
			                           			            	 			   	closable:false
			                           			            	 				}); 
			                           			            	              }
			                           			            	              else
			                           			            	              {*/
			                           			            	            	creardataStoreCuentaGasto(estructura,Ext.getCmp('codcuegasto').getValue(),Ext.getCmp('dencuegasto').getValue(),Ext.getCmp('codcuecontable').getValue()); 
			                           			            	              //}
		                           			            	                 
		                           			            	                }
		                           			            },
		                           			            gridCuentaGasto
		                                      ]
		                        	   }],
                        	   buttons:[{
					                    text:'Aceptar',  
					                    handler: function()
					                    { 
					                    	pasarDatosgridCuentaGasto(gridCuentaGasto.getSelectionModel().getSelected());
    	  									gridCuentaGasto.destroy();
    	  			                        ventanaEstructura.destroy();                      
					                    }
					                    }
					                    ,{
                                    		text: 'Salir',
                                    		handler: function()
                                    		{
                        		   				gridCuentaGasto.destroy();
                        		   				ventanaEstructura.destroy();
                                            }
                                   		}]});

	  ventanaEstructura.show();
 }
 
