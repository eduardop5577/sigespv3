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

function ventanaCentroCostos(arrEmpresa) {
	
	var formCentroCosto = new Ext.FormPanel({
        frame:true,
        width: 370,
		height:225,
        items: [{
        	layout:"column",
			border:false,
			width: 355,
			style: 'position:absolute;left:15px;top:10px',
			items:[{
				layout:"form",
				columnWidth:0.5,
				border:false,
				items:[{
					xtype:'label',
					style:'font-weight: bold;',
					text:"Digito"
				}]
			},{
				layout:"form",
				columnWidth:0.5,
				border:false,
				style:'margin-left:10px',
				items:[{
					xtype:'label',
					style:'font-weight: bold;',
					text:"Maneja Centro de Costos"
				}]
			}]
        },{
        	layout:"column",
			border:false,
			width: 355,
			style: 'position:absolute;left:15px;top:40px',
			items:[{
				layout:"form",
				columnWidth:0.5,
				border:false,
				items:[{
					xtype:'label',
					text:"Activo"
				}]
			},{
				layout:"form",
				columnWidth:0.5,
				border:false,
				style:'margin-left:60px',
				items:[{
					xtype: "checkbox",
            		fieldLabel: "",
            		hideLabel : true,
            		id: 'cencosact',
           			inputValue: '1',
           			binding:true,
           			defaultvalue:'0'
				}]
			}]
        },{
        	layout:"column",
			border:false,
			width: 355,
			style: 'position:absolute;left:15px;top:70px',
			items:[{
				layout:"form",
				columnWidth:0.5,
				border:false,
				items:[{
					xtype:'label',
					text:"Pasivo"
				}]
			},{
				layout:"form",
				columnWidth:0.5,
				border:false,
				style:'margin-left:60px',
				items:[{
					xtype: "checkbox",
            		fieldLabel: "",
            		hideLabel : true,
            		id: 'cencospas',
           			inputValue: '1',
           			binding:true,
           			defaultvalue:'0'
				}]
			}]
        },{
        	layout:"column",
			border:false,
			width: 355,
			style: 'position:absolute;left:15px;top:100px',
			items:[{
				layout:"form",
				columnWidth:0.5,
				border:false,
				items:[{
					xtype:'label',
					text:"Ingreso"
				}]
			},{
				layout:"form",
				columnWidth:0.5,
				border:false,
				style:'margin-left:60px',
				items:[{
					xtype: "checkbox",
            		fieldLabel: "",
            		hideLabel : true,
            		id: 'cencosing',
           			inputValue: '1',
           			binding:true,
           			defaultvalue:'0'
				}]
			}]
        },{
        	layout:"column",
			border:false,
			width: 355,
			style: 'position:absolute;left:15px;top:130px',
			items:[{
				layout:"form",
				columnWidth:0.5,
				border:false,
				items:[{
					xtype:'label',
					text:"Gasto"
				}]
			},{
				layout:"form",
				columnWidth:0.5,
				border:false,
				style:'margin-left:60px',
				items:[{
					xtype: "checkbox",
            		fieldLabel: "",
            		hideLabel : true,
            		id: 'cencosgas',
           			inputValue: '1',
           			binding:true,
           			defaultvalue:'0'
				}]
			}]
        },{
        	layout:"column",
			border:false,
			width: 355,
			style: 'position:absolute;left:15px;top:160px',
			items:[{
				layout:"form",
				columnWidth:0.5,
				border:false,
				items:[{
					xtype:'label',
					text:"Resultado"
				}]
			},{
				layout:"form",
				columnWidth:0.5,
				border:false,
				style:'margin-left:60px',
				items:[{
					xtype: "checkbox",
            		fieldLabel: "",
            		hideLabel : true,
            		id: 'cencosres',
           			inputValue: '1',
           			binding:true,
           			defaultvalue:'0'
				}]
			}]
        },{
        	layout:"column",
			border:false,
			width: 355,
			style: 'position:absolute;left:15px;top:190px',
			items:[{
				layout:"form",
				columnWidth:0.5,
				border:false,
				items:[{
					xtype:'label',
					text:"Capital"
				}]
			},{
				layout:"form",
				columnWidth:0.5,
				border:false,
				style:'margin-left:60px',
				items:[{
					xtype: "checkbox",
            		fieldLabel: "",
            		hideLabel : true,
            		id: 'cencoscap',
           			inputValue: '1',
           			binding:true,
           			defaultvalue:'0'
				}]
			}]
        }]
	});
	
	function actualizarCencos(arrEmpresa) {
		
		if(arrEmpresa['cencosact']=='1'){
			Ext.getCmp('cencosact').setValue(true);
		}
		
		if(arrEmpresa['cencospas']=='1'){
			Ext.getCmp('cencospas').setValue(true);
		}
		
		if(arrEmpresa['cencosing']=='1'){
			Ext.getCmp('cencosing').setValue(true);
		}
		
		if(arrEmpresa['cencosgas']=='1'){
			Ext.getCmp('cencosgas').setValue(true);
		}
		
		if(arrEmpresa['cencosres']=='1'){
			Ext.getCmp('cencosres').setValue(true);
		}
		
		if(arrEmpresa['cencoscap']=='1'){
			Ext.getCmp('cencoscap').setValue(true);
		}
	}
	
	var ventanaCentroCosto = new Ext.Window({
		title: "<H1 align='center'>Configuraci&#243;n Centro de Costos</H1>",
		autoScroll:true,
        width:400,
        height:300,
        modal: true,
        closable:false,
        plain: false,
        items:[formCentroCosto],
        buttons: [{
        	text:'<b>Procesar</b>',
        	iconCls: 'menuprocesar',
            handler: function() {
            	var cadjson = "{'oper':'actcencos','codemp':'"+arrEmpresa['codemp']+"',"+getJsonFormulario(formCentroCosto)+"}";
            	var objjson = Ext.util.JSON.decode(cadjson);
		        if(typeof(objjson) == 'object'){
		        	var parametros = 'ObjSon=' + cadjson;
					Ext.Ajax.request({
						url : '../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
						params : parametros,
						method: 'POST',
						success: function (resultado, request) {
							var repuesta = resultado.responseText;
							if(repuesta == '1') {
								Ext.Msg.show({
									title:'Mensaje',
							        msg: 'La configuraci&#243;n del centro de costro se registro exitosamente',
							        buttons: Ext.Msg.OK,
							        icon: Ext.MessageBox.INFO
							    });
							}
							else {
								Ext.Msg.show({
									title:'Mensaje',
							        msg: 'Ocurri&#243; un error al guardar la configuraci&#243;n de centro de costos',
							        buttons: Ext.Msg.OK,
							        icon: Ext.MessageBox.INFO
							    });
							}
						}
					});
		      		ventanaCentroCosto.destroy();
		        }
			}
		},
		{
			text: '<b>Salir</b>',
            handler: function(){
            	ventanaCentroCosto.destroy();
			}
		}]
	});
    
    ventanaCentroCosto.show();
    actualizarCencos(arrEmpresa)
}