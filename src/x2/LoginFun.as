/** 自訂函數 :
 * //whenLogin(Object):Object
 * //afterLogin() 
 * 欄位: loginId, userPwd, fLang(optional), fAgentId(optional)
 * label : labLoginId, labUserPwd, labLang, labAgentId
 */


	//import com.Rijndael;
	
	import flash.events.KeyboardEvent;
	import flash.external.ExternalInterface;	
	import mx.collections.ArrayList;	
	import x2.*;
	

	//=== property begin ===
	public var fWhenOpen:Function;			//function call when login ok.
	public var fWhenSubmit:Function;			//function call when login ok.
	public var fLoginOk:Function;			//function call after login ok.
	public var fLoginFail:Function;			//function call after login failed, 如果沒有指定, 則會關閉系統.
	public var oData:Object;					//data send to server side

	[Bindable]
	public var bAgent:Boolean=false;		//是否顯示代理人欄位
	//=== property end ===


	[Bindable]
	private var iR:Object;
	
	[Bindable]	//langeage 欄位的 dataProvider
	private var ia_lang:ArrayList = new ArrayList();
	
	//instance varibles
	private var i_lang:Object = null;			//多國語欄位
	private var i_labLang:Object = null;		//多國語label欄位
	private var i_agentId:Object = null;		//代理人欄位
	private var i_labAgentId:Object = null;		//代理人label欄位	
	private var in_errorTimes:int = 0;			//error login times
	

	private function showLang(p_lang:Object=null):void{
		//return;
		
		if (p_lang != null)
			Fun2.sLang = Fun.getItem(p_lang) as String;
		
		//temp
		//iR = Fun.getLang("_Login");
		iR = Fun.getLang("_Login");
		//iR = Fun.getLang("Main");
		//Fun.msg("I", "show iR");
		//Fun.msg("I", "title=" + iR.userID);
		//Fun.msg("I", iR.toString());
		
		//set lang combobox if need
		if (i_lang != null){		
			ia_lang.removeAll();
			var ta_row:Array = String(iR.langList).split(",");
			for (var i:int=0; i<ta_row.length; i=i+2){
				ia_lang.addItem({data:ta_row[i], label:ta_row[i+1]});
			}
			
			if (Fun.getItem(i_lang) as String != Fun2.sLang)
				Fun.setItem(i_lang, Fun2.sLang);			
		}
		
		//show fields label
		this.title = iR.title;
		fSchoolCode.label = iR.schoolCode;
		fLoginId.label = iR.loginId;
		fUserPwd.label = iR.userPwd;
		cmdOk.label = iR.ok;
		cmdExit.label = iR.exit;
		cmdForgot.label = iR.forgot;
		
		if (i_labLang != null)
			i_labLang.label = iR.lang;
		
		if (i_labAgentId != null)
			i_labAgentId.label = iR.agentId;
		
	}
		
	
	//when user press enter, trigger ok button click
	private function keyEnter(p_event:KeyboardEvent):void{
		//if (p_event.keyCode == Keyboard.ENTER){
		if (p_event.keyCode == 13){
			okClick();
		}	    	
	}
	
	
	private function okClick():void{
		//check log times
		if (in_errorTimes >= 3){
			exitSys();
		}
		
		//check input
		//var ts_code:String = schoolCode.text;		
		//var ts_id:String = loginId.text;
		var ts_id:String = loginId.selectedItem.data;
		var ts_pwd:String = userPwd.text;	    	
		if (Fun.isEmpty(ts_id)){
			Fun.msg("E", iR.idEmpty);
			return;
		}
		
		//return error msg if any
		var t_data:Object = {
			fun: "Login" 
			//testMode: (gbTestMode) ? "Y" : "N"
		}; 
		t_data[Fun.csLoginId] = ts_id; 
		t_data[Fun.csPwd] = ts_pwd;
		oData = t_data;
		//t_data[Fun.csAgentId] = Fun.getItem(agentId) as String;
		
		//run whenLogin() 		
		//try {
			if (fWhenSubmit != null){
				if (!fWhenSubmit(t_data)){
					return;
				}
			}
		//}catch (e:Error){
			//do nothing !
		//}
		
		//Fun.async("", "_Login", t_data, okClick2);
		Fun.async("", "_Login", oData, okClick2, null);
	}
	

	//傳回 key
	private function okClick2(p_data:Object):void{
		if (p_data == null || p_data == "_"){	//no error,	php 無法傳回空字串 !!
			if (fLoginOk != null){
				fLoginOk();
				Fun.closePopup(this);
			}
		}else if (p_data.errorCode != null){
			in_errorTimes++;
			//msg(iR[p_data.errorCode]+" ("+in_errorTimes+")");
			Fun.msg("E", iR[p_data.errorCode]+" ("+in_errorTimes+")");
		}else{
			in_errorTimes++;
			Fun.msg("E", p_data.error);
		}
	}
	
	
	//close application
	private function exitSys():void{		
		if (fLoginFail != null){
			fLoginFail();
		}else{
			Fun.exitSys();
		}
	}