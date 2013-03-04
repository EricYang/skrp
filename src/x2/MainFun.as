/** 自訂函數 :
 * //whenOpen()
 * //afterOpen() 
 * 
 * 注意事項 :
 *   可傳入參數 : prog, lang, 
 */
	
	import flash.external.ExternalInterface;
	
	import mx.collections.ArrayCollection;
	
	import spark.components.BorderContainer;
	import spark.components.NavigatorContent;
	
	import x2.*;	
	    	   	
	    	   		
	//get from server side.
	//private var ib_checkLogin:Boolean = false;	//check login or not
	//private var ib_openAllMenu:Boolean = false;	//false(open all menu)
	
	//private var in_maxApp:int = 1;
	//private var in_loginWin:int = 0;	//是否開啟登入畫面, 1(是),-1(否),0(由傳入參數決定)
	//
	//private var i_para:Object={};		//傳入參數, 包含變數: L(login:Y/N), prog(自動開啟的程式代碼), 
	private var i_appContainer:Object;	//程式的 container: tabNavigator(in_maxApp > 1), Canvas(in_maxApp == 1) 
	private var is_app:String;			//current opened application id
	//private var is_lang:String;			//initial language

	[Bindable]
	private var iR:Object;		
    		
	
	/*
	private var ib_afterOpen:Boolean = false;
	private function callAfterOpen():void {
		if (ib_afterOpen){
			return;
		}
		
		ib_afterOpen = true;
		try {
			Object(this).afterOpen();
		}catch (e:Error){
			//do nothing !
		}		
	}
	*/


	//set global variables
	private function setGlobal(p_data:Object):void {
		//var ts_key:String = Fun.decode(p_data[Fun.csKey], false, 1) as String;
		//Fun.aKeyByte = Fun.getKeyByte(ts_key);
		Fun.sUserId = p_data[Fun.csUserId];
		Fun.sUserName = p_data[Fun.csUserName];
		//Fun.sLoginTime = p_data[Fun.csLoginTime];
		Fun.nLoginTime = p_data[Fun.csLoginTime];
		Fun.sLoginId = p_data[Fun.csLoginId];
		Fun.sAgentId = (p_data.hasOwnProperty(Fun.csAgentId)) ? String(p_data[Fun.csAgentId]) : "" ;
		Fun.sDeptId = (p_data.hasOwnProperty(Fun.csDeptId)) ? String(p_data[Fun.csDeptId]) : "" ;
		Fun.sDeptIds = (p_data.hasOwnProperty(Fun.csDeptIds)) ? String(p_data[Fun.csDeptIds]) : "" ;
		//Fun.dToday = ST.toDate(Fun.sLoginTime);
		Fun.dToday = new Date(Fun.nLoginTime * 1000);
		Fun.sToday = ST.dateToStr(Fun.dToday);

		//temp add
		//var t_date:Date = new Date(Fun.nLoginTime * 1000);
		
		//new
		Fun.sSessId = p_data[Fun.csSessId];
	}


	//get input parameters for main app
	private function getPara():Object{		
		var ts_url:String = ExternalInterface.call("window.location.href.toString");
		if (ST.right(ts_url, 1) == "#"){
			ts_url = ts_url.substr(0, ts_url.length - 1);
		}
		
		var tn_pos:int = ts_url.indexOf("?");
		if (tn_pos < 0){
			return null;
		}
		
		ts_url = ts_url.substr(tn_pos+1) 
		var ta_para:Array = ts_url.split("&");
		var t_data:Object = {};
		for (var i:int=0; i<ta_para.length; i++) {			
			var ts_para:String = ta_para[i];
			tn_pos = ts_para.indexOf("=");
			if (tn_pos > 0){
				//Fun.msg("I", ts_para.substr(0,tn_pos)+":"+ts_para.substr(tn_pos+1));		
				t_data[ts_para.substr(0,tn_pos)] = ts_para.substr(tn_pos+1);
			}
		}
		
		return t_data;
	}
	
	
	/**
	 * 更新 menu 元件的狀態及內容
	 * @param p_menu menu contorl, 可以設定其 dataProvider 屬性的控制項
	 * @pa_menu will be menu control dataProvider array
	 * @p_data 從後端傳回的資料
	 */ 
	private function updateMenu(p_menu:Object, pa_menu:Array, p_data:Object):void{
		
		//設定 menu, recursive filter menu array
		//var i:int;
		if (p_data[Fun.csAutoLogin] == "Y"){
			p_menu.enabled = true;
			Fun.msg("I", "Auto Login Mode !");
		}else if (p_data.hasOwnProperty(Fun.csMenu)){
			if (p_data[Fun.csMenu] is String){
				filterMenu(pa_menu, ST.toJson(p_data[Fun.csMenu]) as Array);	//java 傳回字串 !!				
			}else{
				filterMenu(pa_menu, p_data[Fun.csMenu] as Array);	//php 傳回陣列 !!				
			}
			
			/*
			var ta_menu:Array = ;
			for (var i:int=a_menu.length - 1; i>=0; i--){
				filterMenu(ia_menu, i, ta_menu);				
			}
			*/
			//re-set dataProvider property to active new property ! others way did not work !!
			p_menu.dataProvider = new ArrayCollection(pa_menu);
			p_menu.enabled = true;
		}				
	}	


	/**
	 * 移除無權限的功能清單(recursive call)
	 * @param pa_menu menu will be filter out.
	 * @param pa_list 可執行的程式清單.
	 * @param pn_item 目前處理的 menu item.(for recursive)
	 * @return true(filtered, find)/false(not filter)
	 */ 
	//private function filterMenu(pa_all:Array, pa_ok:Array, pn_item:int=0):Boolean{
	private function filterMenu(pa_all:Array, pa_ok:Array):Boolean{
		var tb_find:Boolean = false;
		for (var i:int=pa_all.length - 1; i>=0; i--){
			var tb_findItem:Boolean = false;
			var t_item:Object = pa_all[i];			
			if (t_item.hasOwnProperty("children") && t_item.children != null){
				//recursive
				tb_findItem = filterMenu(t_item.children as Array, pa_ok);
			}else if (t_item.doing){
				tb_findItem = true;		//如果是建構中, 則顯示 menu item				
			}else{
				//find menu item
				for (var j:int=0; j<pa_ok.length; j++){
					if (t_item.data == pa_ok[j].data){
						tb_findItem = true;
						t_item.fun = pa_ok[j].fun;
						break;
					}
				}
			}
			
			//remove item if not found 
			if (tb_findItem){
				tb_find = true;
			}else{
				pa_all.splice(i, 1);
			}
		}
		
		return tb_find;			
	}
	

	private function setItemGlobal(p_item:Object, pa_cond:Array=null):void{
		//set access right to global variables.
		is_app = p_item.data;
		var ts_swf:String = (p_item.swf != null) ? p_item.swf : is_app; 
		Fun.sApp = is_app; 
		Fun.oVar.item = {
			data: is_app,
			fun: p_item.fun,
			para: p_item.para,
			inf: (p_item.inf != null) ? p_item.inf : ts_swf,
			swf: ts_swf,
			type: (p_item.type != null) ? p_item.type : "", 
			//help: (p_item.help != null) ? p_item.help : ts_swf,	//new
			help: (p_item.help != null) ? p_item.help : "",	//new
			cond: pa_cond,
			label: p_item.label
		};
	}

	/*
	//load swf by Item
	private function loadSwf(p_container:BorderContainer, p_item:Object):void{
		var t_swf:SWFLoader = new SWFLoader();
		t_swf.x = 0;
		t_swf.y = 0;
		t_swf.percentHeight = 100; 
		t_swf.percentWidth = 100;	    
		var ts_time:String = new Date().getTime().toString();	//for no cache !!	    
		t_swf.load(p_item.data+".swf?tt="+ts_time);
		
		if (Fun2.nMaxApp == 1){
			if (p_container.numChildren > 0){
				p_container.removeChildAt(0);
			}
			p_container.addChild(t_swf);
		}else{
			var t_item:Object = (p_item != null) ? p_item : Fun.oGlobal.item;
			var t_page:NavigatorContent = new NavigatorContent();
			t_page.label = t_item.label;
			t_page.id = t_item.data;
			t_page.x = 0;
			t_page.y = 0;		    
			t_page.addChild(t_swf);
			
			var t_tab:TabNavigator = p_container as TabNavigator;
			var t_child:Object = t_tab.addChild(t_page);
			t_tab.selectedChild = Container(t_child);
			
			//t_child.setFocus();
			//setClosePos();			
		}
		
		setItemGlobal(p_item);
	}
	*/
