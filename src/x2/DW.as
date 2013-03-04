package x2
{
	/**
	 * 1.function type: C(create),U(update),D(delete),V(view)
	 */
	
	import flash.events.*;
	
	import mx.collections.ArrayCollection;
	
	import spark.components.DataGrid;
	import spark.components.TextInput;
	import spark.components.TitleWindow;
	//import mx.events.FlexEvent;
	
	
	public class DW
	{

		//================
		//=== constant ===
		//================	

		/**
		 * (DW2) 用來控制 function icon.
		 */ 
		public const csFun:String = "_fun";
		
		/**
		 * dirty 變數名稱, 內容分成2類: 異動欄位/功能("_"為開始字元), 每一筆資料包含以下變數:<br/>
		 *　fun: (功能)use _cFun, 功能代碼: C(新增),U(修改但無異動),UU(修改並且異動),DU(刪除修改),DUU(刪除 UU),DC(刪除新增)<br/>
		 *　key: (功能) 鍵值欄位值, 使用 keyFid()為欄位名稱.<br/>
		 *　error: (功能) 欄位的錯誤訊息字串, 使用 errorFid()為為欄位名稱.(fid-value), 如果沒有錯誤則為空白.<br/>
		 *　異動的欄位: (欄位)ex: memberId=true
		 *　//_filterRow: use ic_filterRow, for 1-n2-n3 only, and n2 n3 are New.
		 */ 
		public const csDirty:String = "_dirty";	

		//for DW2 filter function !!
		//public const csRowSeq:String = "_rowSeq";	//不是目前的筆數, 而是自動累加的筆數, 	
		//public const csUpSeq:String = "_upSeq";	
		
	
		//===============
		//=== public  ===
		//===============	

		/**
		 * 工具列物件, DW2 only.
		 */ 
		public var xTool:Object;		//DW2 toolbar object, DW2 only
		
		/**
		 * 組態設定, 包含以下變數:<br/>
		 * deleteFirst: (DW2) 儲存異動前是否先刪除目前的資料, 預設 false.<br/>
		 * autoAdd: (DW) true(default, add one row if empty when "U")/false(will set to read mode).<br/>
		 * page: 用在 ComStepEdit only, tab page num(), default 0.<br/>
		 * editable: 是否可編輯, 預設 true.<br/>
		 * updatable: 是否更新到資料庫, 預設 true.<br/>
		 * grid: (DW2,Optional) dataGrid object, if null, no input function.<br/>
		 * autos: 自動欄位名稱陣列, 依序為 ["creator","created","reviser","revised"], will convert to field object if need.<br/>
		 * 
		 * upDW: (2nd DW), parent DW object, if null will not retrieve record, 無實際用途, 只在 getQueryInfo()用來得讀取資料時的上層欄位代號, 必須與後端info的設定一致<br/>
		 * upQKeys: fid array, query key field name in parent DW, 用來讀取編輯畫面某個DW的所有資料.<br/>
		 * qKeys: fid array, will conver to query key field objects, 用來查詢編輯畫面某個DW的所有資料, 配合 upQKeys[].<br/>
		 * 
		 * upEditDW: (2nd DW), parent DW object for 設定關聯欄位的值, 如果空白則等於 upDW.<br/>
		 * upEKeys: fid array, will conver to query key field objects, 用來設定關聯欄位的值, 如果空白則等於 upQKeys[].<br/>
		 * eKeys: fid array, will conver to query key field objects, 用來設定關聯欄位的值, 如果空白則等於 qKeys[], 配合 upEKeys[].<br/>
		 * 
		 * keys: fid array of PK for update record, will conver to field object, could empty !!<br/>
		 * inputType: 預設編輯型態: I(identity),U(新增,修改,default!),C(新增,不能修改),R(唯讀, 包含隱藏欄位),
		 *   2(child of 'I' field, 傳送到後端時, 如果有值, 則不會從 Up DW 取得值 !! 2010-12-31).
		 *   //如果上一層不是第 0個DW, 則必須手動設定 getUpdateInfo() 裡的 upRow 欄位, 直接設定 grid 裡面的 _upRow 欄位, getUpdateInfo() 將會讀取 !! (2010-12-31)
		 *   <br/>
		 * dataType: 預設的欄位資料型態, S(字串, default),N(數字),D(日期),DT(dateTime), S2(可包含跳脫字元的字串, 只能放在編輯畫面).<br/>
		 * mapping: 是否對應到清單畫面的 DataGrid, 預設為 false, 可為 boolean 值或 DataGrid 欄位名稱.<br/>
		 * //autoUserId: 是否使用 Fun.userId 做為 autos[] 的用戶欄位, 預設是, 如果不是, 則會使用 Fun.loginId for autos)<br/>
		 * items: 欄位設定, 包含以下變數:<br/>
		 *   page: optional, tab page num(), if null, will get from DW<br/>
	 	 *   source: (DW) 來源欄位物件, auto for DW2.(GridColumn)<br/>
	 	 *   //component: (DW2) auto. //??<br/>
		 *   fid: (DW2) 欄位代號, auto for DW.<br/>
		 *   dataType: 參考上面的 dataType.<br/>
		 *   inputType: 參考上面的 inputType.<br/>
		 *   mapping: 參考上面的 mapping.<br/>
		 *   check: 驗証規則, 運算子有:between, ex:["between",0,24] <br/>
		 *   //fieldType: //?? (DW2), auto for DW, ex:ComboBox, ComboBox2, DateField...<br/>
		 *   fname: 欄位名稱, 自動讀取 formItem.label 或 DataGrid header, 如果讀不到, 則預設為 [this field].<br/>
		 *   fCheckItem: function of check item, return error msg if any, (Object:fid):string<br/>
		 *   init: 初始值, 必須是簡單資料型態(string, int, boolean...), 如果內容是變動的, 必須特別注意 !!<br/>
		 *   msg: 用戶停駐在欄位上時的顯示訊息.<br/>
		 *   relat: ?? object: {type(string, match Fun2.getRelatName), source(optional, field name array), target(field name string)}.<br/>
		 *   required: 是否為必填, 自動讀取, 但如果欄位包在 Group 裡面, 則必須手動設定.<br/>
		 *   update: 是否更新資料庫, 如果是(default), 則 inf 檔案必須有此欄位<br/>
		 *   read: 是否讀取資料庫, 如果是(default), 則 inf 檔案必須有此欄位<br/>
		 *   send: 是否傳送這個欄位到後端, 預設為 false, 全部的 DW 裡面的 send 變數會放到同一個 JsonObjct 再傳到後端<br/>
		 *   fno: 欄位序號, 自動設定.<br/>
		 */ 
		public var oConfig:Object;
		
		/**
		 * 程式代號
		 */ 
		public var sApp:String="";		//app id.
		
		/**
		 * (Optional)顯示欄位訊息的 TextInput 物件, 如果不設定, 則不顯示.
		 */ 		
		public var xMsgBox:TextInput;		//message text field object		
		
		/**
		 * DW 序號.
		 */ 		
		public var nDW:int;			//dw number, base on 0, set by AP
			
		
	
		//==================	
		//=== protected  ===
		//==================	
		
		/**
		 * 資料筆數.
		 */ 		
		protected var in_rows:int;
		
		/**
		 * Identity 欄位序號, -1表示無
		 */ 		
		protected var in_ident:int=-1;				
		
		/**
		 * "欄位代號" 的名稱, id for DW, dataField for DW2
		 */ 		
		protected var is_fidName:String;
		
		/**
		 * 目前的功能代號
		 */ 		
		protected var is_fun:String;
				
		/**
		 * 是否正在儲存模式, 如果否, 會用  i_ac 來捉取和設定資料, 如果 true, 則使用 i_ac.source
		 * 當 i_ac 設定 filterFunction 時, 這兩者會不同, i_ac.source 不會受 filter 影響
		 */  
		protected var ib_saving:Boolean;	
		
		/**
		 * 使用者儲存前的編輯狀態: C(changed), E(error), ""(無)
		 */ 		
		//protected var is_lastStatus:String;
		
		/**
		 * 是否為多筆, false(DW), true(DW2)	
		 */ 		
		protected var ib_rows:Boolean;
		
		/**
		 * when alert show, field will focus again, use this variable to avoid it !!
		 */ 		
		protected var ib_focus:Boolean;		
		
		/**
		 * 資料是否異動. (包含系統自動設定初始值)
		 */ 		
		protected var ib_change:Boolean;
		
		/**
		 * 用戶是否異動資料.
		 */ 		
		protected var ib_userChange:Boolean;
		
		/**
		 * DW2 的 DataGrid.dataProvider 物件
		 */ 		
		protected var i_ac:ArrayCollection;
		
		/**
		 * i_ac.source array, DW2 only
		 */ 		
		//protected var ia_row:Array;
		
		/**
		 * (DW2) 是否有 grid 物件
		 */ 		
		protected var ib_grid:Boolean;
		
		//當存在 filterFunction時, 利用這2個欄位值來建立與上層 DW 的關聯
		//儲存的內容皆為 i_ac.source 的資料序號, 不含 filter !!		
		protected var in_nowSeq:int;
		
		/**
		 * 目前的欄位值
		 */ 		
		//protected var i_value:Object;	
		private var i_value:Object;	
		
		/**
		 * 欄位代號和欄位名稱的對應資料Object.
		 */ 		
		protected var i_fidToFno:Object={}; 
		
		/**
		 * 用來傳遞變數
		 */ 		
		protected var i_object:Object={};
		
		/**
		 * 日期欄位陣列
		 */ 		
		protected var ia_fDate:Array=[];
		
		/**
		 * S2欄位陣列 item 序號
		 */ 		
		protected var ias_fS2:Array=[];
		
		/**
		 * 欄位資料陣列, 每一筆資料包含變數: dw, itemno
		 */ 		
		protected var ia_field:Array=[];
		
		/**
		 * 等於 oConfig.items[]
		 */ 		
		protected var ia_item:Array;
		
		/**
		 * 編輯畫面要讀取的欄位代號字串陣列, 用來讀取資料庫.
		 */ 		
		protected var ias_fid:Array=[];				
		
		

		/**
		 * 初始化物件 for DW, overrided in DW2: 
		 *   1.set this instant, 
		 *   2.call initPublic(), 
		 *   3.set other instant
		 * will show data if need
		 * @param pb_readDB 是否讀取資料庫.
		 * @return 錯誤訊息 if any.
		 */
		public function init(pb_readDB:Boolean):String{
			//set instance for DW
			ib_rows = false;
			is_fidName = "id";									
			ia_item = oConfig.items;
			
			//adjust oConfig
			if (oConfig.mapping == null) oConfig.mapping = false;
			if (oConfig.autoAdd == null) oConfig.autoAdd = true;
			//if (oConfig.autoUserId == null) oConfig.autoUserId = true;
		
			initDW();
			
			/*
			var ts_error:String = initPublic();
			if (ts_error != "")
				return ts_error + "(aoDW[" + nDW + "])";	
			*/
			
			
			//set item property: fno, fid, fname, required, mapping
			var ts_class:String;			
			var ts_fid:String;
			var t_item:Object;
			var t_field:Object;
			var tb_form:Boolean;
			var t_formItem:Object;
			var i:int
			var tn_items:int = ia_item.length;
			for (i=0;i<tn_items;i++){
				t_item = ia_item[i];
				if (t_item.source == null){
					return "aoDW["+nDW+"].ia_item["+i+"].source can not be null !";	
				}
			
				//先設定這2個屬性
				t_field = t_item.source;	
				ts_fid = t_field.id;
				t_item.fid = ts_fid;
				t_item.fno = i;
				i_fidToFno[ts_fid] = i;
				ia_field[i] = {dw:nDW, itemno:i};
				
				if (t_field.visible){
					t_formItem = t_field.hasOwnProperty("owner") ? t_field.owner : null;
					if (t_formItem == null){
						tb_form = false;
					}else if(t_formItem.className == "FormItem"){
						tb_form = true;
						
					//如果欄位在 HBox裡面, 則由 FormItem.required決定 required屬性
					}else if(t_formItem.className == "HGroup"){
						t_formItem = t_formItem.owner; 
						tb_form = (t_formItem.className == "FormItem");
					}else{
						tb_form = false;
					}
					//tb_form = (t_formItem == null) ? false : (t_formItem.className == "FormItem");
					
					if (t_item.required == null){
						t_item.required = (tb_form) ? t_formItem.required : false;
					}
				}else{
					if (t_item.required == null){
						t_item.required = false;
					}						
				}
				
				//set item.mapping
				if (t_item.mapping == null) t_item.mapping = oConfig.mapping;
				if (t_item.mapping == true)	t_item.mapping = ts_fid;
				
				
				initItem(i);				
				if (t_item.dataType == "S" && t_field.hasOwnProperty("restrict") &&  t_field.restrict == null){
					t_field.restrict = Fun.csTextLimit;
				}
				
				
				//日期欄位第一次輸入不正確的資料時, 系統不會觸發 change event !!
	 			//add field got focus event handler
	 			ts_class = t_field.className;
	 			//if (ts_class.substr(0,9) == "TextInput" || ts_class.substr(0,12) == "NumericInput" || ts_class.substr(0,9) == "DateField"){
				//if (String("Text1,Text2,TextInput,Num1,Num2,Date1,Date2,DateField,").indexOf(ts_class+",") >= 0){
				if (String("Text1,TextInput,Num1,Date1,DateField,").indexOf(ts_class+",") >= 0){
					//t_field.addEventListener(Event.CHANGE, changeText);	 				
					t_field.addEventListener(FocusEvent.FOCUS_IN, fieldFocusIn);
					t_field.addEventListener(FocusEvent.FOCUS_OUT, fieldFocusOut);
	 			}else{	//radio field
					t_field.addEventListener(FocusEvent.FOCUS_IN, fieldFocusIn);	//for show field msg
					t_field.addEventListener(Event.CHANGE, dw1CheckField);	 				
	 			}
			}				
			
			/*
			//return initPublic(p_data);
			//return initPublic(pb_readDB);
			var ts_error:String = initPublic();
			if (ts_error != "")
				return ts_error;
			*/
			/*
			for (i=0;i<tn_items;i++){
				t_item = ia_item[i];
				t_field = t_item.source;	
				if (t_item.dataType == "S" && t_field.hasOwnProperty("restrict") &&  t_field.restrict == null){
					t_field.restrict = Fun.csTextLimit;
				}
			}
			*/
			return "";
		}

		
		//override in DW2 !!
		public function initVar():void{   
			ib_userChange = false;			
			ib_saving = false;
		}
		
		
		protected function initDW():void{
			if (oConfig.editable == null) oConfig.editable = true;
			if (oConfig.updatable == null) oConfig.updatable = true;
			if (oConfig.inputType == null) oConfig.inputType = "U";
			if (oConfig.dataType == null) oConfig.dataType = "S";
			if (oConfig.deleteFirst == null) oConfig.deleteFirst = false;	//DW2 only		
			//
			if (!oConfig.keys) oConfig.keys = [];
			if (!oConfig.qKeys) oConfig.qKeys = [];
			if (!oConfig.autos) oConfig.autos = [];
			//
			if (!oConfig.upEditDW) oConfig.upEditDW = oConfig.upDW;
			if (!oConfig.upEKeys) oConfig.upEKeys = oConfig.upQKeys;
			if (!oConfig.eKeys) oConfig.eKeys = oConfig.qKeys;
						
		}
		
		/**
		 * 設定共同的 config 變數
		 * @param pb_readDB 是否讀取資料庫.
		 * @return 錯誤訊息 if any.
		 */
		/*
		//protected function initPublic(pb_readDB:Boolean):String{
		protected function initPublic():String{
			
			ib_saving = false;
						
			//check
			//if (gnDW > 0 && oConfig.upDW == null && pb_readDB){
			//	return "oConfig.upDW can not be empty for child DW !";
			//}			
			
			//adjust oConfig
			if (oConfig.editable == null) oConfig.editable = true;
			if (oConfig.updatable == null) oConfig.updatable = true;
			if (oConfig.inputType == null) oConfig.inputType = "U";
			if (oConfig.dataType == null) oConfig.dataType = "S";
			if (oConfig.deleteFirst == null) oConfig.deleteFirst = false;	//DW2 only		
			//
			if (!oConfig.keys) oConfig.keys = [];
			if (!oConfig.qKeys) oConfig.qKeys = [];
			if (!oConfig.autos) oConfig.autos = [];
			//
			if (!oConfig.upEditDW) oConfig.upEditDW = oConfig.upDW;
			if (!oConfig.upEKeys) oConfig.upEKeys = oConfig.upQKeys;
			if (!oConfig.eKeys) oConfig.eKeys = oConfig.qKeys;
			

			//set item property		
			var t_item:Object;
			var t_field:Object;	
			var ts_fid:String;
			var tn_fno:int;
			var tn_date:int = 0;
			var tn_read:int = 0;
			var tn_fS2:int = 0;
			for (var i:int=0;i<ia_item.length;i++){
				
				t_item = ia_item[i];
				t_field = t_item.source;
				
				//set in_ident
				if (in_ident == -1){
					if (t_item.inputType == "I"){	//identify column
						t_item.update = false;
						in_ident = i;
					}	
				}
															
				//default update(update this field to DB) to true.
				if (t_item.page == null) t_item.page = (oConfig.page != null) ? oConfig.page : 0;
				if (t_item.dataType == null) t_item.dataType = oConfig.dataType;
				if (t_item.inputType == null) t_item.inputType = oConfig.inputType;
				if (t_item.update == null) t_item.update = true;
				if (t_item.read == null) t_item.read = true;
				if (t_item.send == null) t_item.send = false;				
				if (t_item.dataType == "D"){
					ia_fDate[tn_date] = t_item.source;
					tn_date++;	
				}
				
				//set i_fidToFno, ia_field, ias_fid
				ts_fid = t_item.fid;
				tn_fno = t_item.fno;
				i_fidToFno[ts_fid] = tn_fno;
				ia_field[tn_fno] = {dw:nDW, itemno:i};
				if (t_item.read){					
					ias_fid[tn_read] = ts_fid;
					tn_read++;
				}

				
				if (t_item.dataType == "S2"){
					ias_fS2[tn_fS2] = ts_fid;
					tn_fS2++;	
				}
			}

			return "";
		}
		*/
		
		
		protected function initItem(pn_item:int):void{
			var t_item:Object = ia_item[pn_item];
			var t_field:Object = t_item.source;
			
			//set in_ident
			if (in_ident == -1){
				if (t_item.inputType == "I"){	//identify column
					t_item.update = false;
					in_ident = pn_item;
				}	
			}
			
			//default update(update this field to DB) to true.
			if (t_item.page == null) t_item.page = (oConfig.page != null) ? oConfig.page : 0;
			if (t_item.dataType == null) t_item.dataType = oConfig.dataType;
			if (t_item.inputType == null) t_item.inputType = oConfig.inputType;
			if (t_item.update == null) t_item.update = true;
			if (t_item.read == null) t_item.read = true;
			if (t_item.send == null) t_item.send = false;				
			if (t_item.dataType == "D"){
				ia_fDate[ia_fDate.length] = t_item.source;
				//tn_date++;	
			}
			
			//set i_fidToFno, ia_field, ias_fid
			var ts_fid:String = t_item.fid;
			//var tn_fno:int = t_item.fno;
			//i_fidToFno[ts_fid] = tn_fno;
			//ia_field[tn_fno] = {dw:nDW, itemno:pn_item};
			if (t_item.read){					
				ias_fid[ias_fid.length] = ts_fid;
				//tn_read++;
			}
						
			if (t_item.dataType == "S2"){
				ias_fS2[ias_fS2.length] = ts_fid;
				//tn_fS2++;	
			}			
		}
		

		/**
		 * 設定 ib_saving, 儲存資料之前必須設定為 false
		 */ 
		public function setSaving(pb_saving:Boolean):void{
			ib_saving = pb_saving;
		}
		
		
		/**
		 * get DW type, S(單筆),M(多筆),2(多筆 type2) 2011-1-4
		 * override in DW2
		 */ 
		public function getType():String{
			return "S";
		}

		
		/**
		 * 傳回 item 的欄位名稱.(field name)
		 * @param pn_item item 序號.
		 * @return 欄位名稱
		 */ 
		public function getFname(pn_item:int):String{
			var t_item:Object = ia_item[pn_item];
			if (t_item.fname != null){
				return " ["+t_item.fname+"] ";
			}
			
			//如果欄位在 HBox裡面, 則使用 FormItem的 label文字 !!
			var t_formItem:Object = Object(t_item.source).owner;
			if (t_formItem == null){
				return " [this field] "
			}else if(t_formItem.className == "FormItem"){
				return " ["+t_formItem.label.replace(/:|：| |＊|　/g, "")+"] ";			
			}else if(t_formItem.owner.className == "FormItem"){
				return " ["+t_formItem.owner.label.replace(/:|：| |＊|　/g, "")+"] ";			
			}else{
				return " [this field] "
			}
			/*
			if (t_formItem != null && t_formItem.className == "FormItem"){
				return " ["+t_formItem.label.replace(/:|：| |＊|　/g, "")+"] ";			
			}else{
				return " [this field] "
			}
			*/
		}


		//??
		//public function getDirty(pn_row:int=0):Object{
		//public function zz_getDirty(pn_row:int=0):Object{
		//	return ia_dirty[pn_row];
		//}
		

		/**
		 * 傳回某一筆資料的 dirty 功能代號.
		 * @pn_row 資料列序號.
		 * @return 功能代號
		 */ 
		public function getDirtyFun(pn_row:int=0):String{
			//return ia_dirty[pn_row][ic_fun];
			return i_ac[pn_row][csFun];
		}

		/*
		//set dirty fun
		public function setDirtyFun(pn_row:int, ps_fun:String):void{
			i_ac[pn_row][ic_fun] = ps_fun;
		}
		*/


		/**
		 * (DW2) 傳回 DataGrid.dataProvider ArrayCollection.
		 */ 
		public function getAC():ArrayCollection{
			return i_ac;				
		}
		

		/**
		 * 傳回某個鍵值欄位的舊值
		 * @param ps_fid 欄位代號
		 * @pn_row (Optional) 資料列序號
		 * @return 欄位值(字串或數字) 
		 */ 
		private function getOldKey(ps_fid:String, pn_row:int=0, pn_rowType:int=0):Object{
			var ts_keyFid:String = keyFid(ps_fid);
			//return ia_dirty[pn_row][ts_keyFid];
			var t_row:Object = getRow(pn_row, pn_rowType);
			return t_row[csDirty][ts_keyFid];
		}
		

		/**
		 * 傳回某個欄位物件的欄位值, DW2 override.
		 * @param p_field 欄位物件 
		 * @param pn_row (Optional)資料列序號
		 * @return 欄位值
		 */ 
		public function getItem(p_field:Object, pn_row:int=0, pn_rowType:int=0):Object{
			return Fun.getItem(p_field);
		}
		

		/**
		 * 傳回某個欄位代號的欄位值
		 * @param ps_fid 欄位代號 
		 * @param pn_row (Optional)資料列序號
		 * @return 欄位值
		 */ 
		public function getItemByFid(ps_fid:String, pn_row:int=0, pn_rowType:int=0):Object{
			var tn_item:int = fidToInfo(ps_fid).itemno;
			return getItem(ia_item[tn_item].source);
		}

		
		/**
		 * get field info for query records, called by comEdit
		 * 查詢編輯畫面所需要的資料
		 * @param {object} p_row row data in list window, 包含 pkey 的欄位代號/值
		 * @return {object} has elements: fids[], keys[fid,dataType,value(1st dw only,set in getQueryValue),upFid(child only)]
		 */
		public function getQueryInfo(p_row:Object):Object{
			if (nDW != 0 && oConfig.upDW == null){
				return null;
			}
			
			var t_data:Object = {fids:ias_fid, keys:[]};
			var ta_key:Array = (nDW == 0) ? oConfig.keys : oConfig.qKeys;
			//var tb_field:Boolean = (!ib_rows || oConfig.grid != null);
			for (var i:int=0; i<ta_key.length; i++){
				//var tn_item:int = (tb_field) ? fieldToInfo(ta_key[i]).itemno : fidToInfo(ta_key[i]).itemno;
				var tn_item:int = fidToInfo(ta_key[i]).itemno;
				var t_item:Object = ia_item[tn_item];
				t_data.keys[i] = [t_item.fid, t_item.dataType];
				
				if (nDW == 0){
					t_data.keys[i][2] = p_row[t_item.fid];
				}else{
					t_data.keys[i][3] = oConfig.upQKeys[i];					
				}
			}
			
			return t_data;
		}
		

		/**
		 * 傳回目前的資料列序號, old is getRow()
		 */ 
		protected function getNowRow():int{
			return (ib_rows) ? oConfig.grid.selectedIndex : 0;
		}


		/**
		 * 傳回資料內容, 考慮 filter
		 * pn_rowType: 0(一般模式), 1(不考慮filter), 2(考慮filter) 
		 */ 
		public function getRow(pn_row:int, pn_rowType:int=0):Object{
			if (pn_rowType == 0)
				return (ib_saving) ? i_ac.source[pn_row] : i_ac[pn_row];
			else if (pn_rowType == 1)
				return i_ac[pn_row];
			else
				return i_ac.source[pn_row];
				
		}


		/**
		 * 傳回要送到後端的 Object 資料, 單筆DW only.
		 * @return Object
		 */ 
		public function getSend():Object{
			//get send fields/values
			if (ib_rows){
				return null;
			}else{
				var t_send:Object = {};
				var ts_fid:String;
				for (var i:int=0;i<ia_item.length;i++){
					if (ia_item[i].send){
						ts_fid = ia_item[i].fid;
						t_send[ts_fid] = String(getItemByFid(ts_fid));
					}
				}
				
				return t_send;
			}			
		}


		/**
		 * get array of updated info for update database, called by comEdit.
		 * @param {string} ps_now current server datetime if need.
		 * @return {array} json array, array length is rows amount.
		 *   dw: (optional) dw no of this row, default to 0.
		 *   row: (optional) grid row position, for not "D"(delete) fun, default to 0. 
		 *   upRow: (optional) up DW row position in grid(not "D") 2010-12-31, 直接讀取 _upRow 欄位, -1(default)表示讀取DW[0]第0筆 !!
		 *   fun: C,U,D
		 *   ident: (optional) fno, -1(default) means none.(not "D")
		 *   keys: array of [fid,dataType,value(empty for New and Identify column)]
		 *   fields: string array of updated fields: [fid,dataType,value,inputType], (not "D") 
		 */
		public function getUpdateInfo(ps_now:String=""):Array{
			if (!oConfig.updatable){
				return null;
			}
			
			var ta_row:Array = i_ac.source;
			if (ta_row == null){
				return null;
			}
			
			//get sql statement and adjust ia_dirty[]
			var ta_data:Array = []	//return variables
			var tn_ary:int = -1;	//initial value
			var t_field:Object;
			var ts_fid:String;
			var j:int;			
			var t_dirty:Object;
			var ts_userId:String = (Fun2.cbAutoUserId) ? Fun.sUserId : Fun.sLoginId;
			//var ta_row:Array = i_ac.source;
			var tn_rows:int = ta_row.length;
			var t_row:Object;
			var ta_upRow:Array;
			var tn_upRow:int = 0;	//如果上層為 DW2, 則系統會設定此值
			for (var tn_row:int=0;tn_row<tn_rows;tn_row++){
				t_row = ta_row[tn_row];
				t_dirty = t_row[csDirty];
				if (t_dirty == null){
					continue;
				}

				//do nothing if not change !
				var ts_rowFun:String = t_row[csFun];
				if (ts_rowFun == "U" || ts_rowFun == "DC"){
					continue;
				} 
				
				
				//set auto fields/related fields 
				var tb_field:Boolean = false;
				var ta_auto:Array = oConfig.autos;
				switch (ts_rowFun){
					case "C":
						//set auto fields
						//if (ps_now != ""){
	                        if (ta_auto.length >= 1 && !Fun.isEmpty(ta_auto[0])){     //creator
								ts_fid = ta_auto[0];
	                            setItemByFid(ts_fid, ts_userId, true, tn_row);
	                        }
	                        //if (oConfig.autos[1]){     //created time
							if (ta_auto.length >= 2 && !Fun.isEmpty(ta_auto[1])){     //created time
								ts_fid = ta_auto[1];
	                            setItemByFid(ts_fid, ps_now, true, tn_row);
	                        }						
						//}
						
						//set related key column value
						if (nDW > 0){
							var ta_upKey:Array = oConfig.upEKeys; 
							var ta_key:Array = oConfig.eKeys;
							var t_upDW:DW = DW(oConfig.upEditDW);
							//var tn_upRow:int;
							if (t_upDW.isRows()){
								if (ta_upRow == null)
									ta_upRow = t_upDW.getAC().source;
								
								tn_upRow = AR.find(ta_upRow, Fun.csRowSeq, t_row[Fun.csUpSeq]); 								
							}
							for (j=0;j<ta_key.length;j++){
								var t_data:Object = t_upDW.getItemByFid(ta_upKey[j], tn_upRow);
	                            setItemByFid(ta_key[j], t_data, true, tn_row);
	                            /*		       							
								if (tb_field){ 
	                            	setItem(ta_qKey[j], t_data, true, tn_row);
	       						}else{
	                            	setItemByFid(ta_qKey[j], t_data, true, tn_row);	       							
	       						}
	       						*/								
							}							
						}
						
						//get field info
						tb_field = true;
						break;
					
					case "UU":
						ts_rowFun = "U";
					//case "U":
						//set auto fields
						//if (ps_now != ""){
	                        //if (oConfig.autos.length >= 2 && oConfig.autos[2]){     //modifier
							if (ta_auto.length >= 3 && !Fun.isEmpty(ta_auto[2])){     //creator
								ts_fid = ta_auto[2];
	                            setItemByFid(ts_fid, ts_userId, true, tn_row);
	                        }
	                        //if (oConfig.autos.length >= 3 && oConfig.autos[3]){     //modified time
							if (ta_auto.length >= 4 && !Fun.isEmpty(ta_auto[3])){     //creator
								ts_fid = oConfig.autos[3];
	                            setItemByFid(ts_fid, ps_now, true, tn_row);
	                        }
      					//}
						
						//get sql statement
						tb_field = true;
						//tb_key = true;						
						break;
					case "D":		//delete DW1 row
					case "DU":		//delete not changed row
					case "DUU":		//delete changed row
						ts_rowFun = "D";
						break;		
					default:
						continue;
						break;		
				}//switch
				
				
				//get fields updated info
				var ta_field:Array = [];
				var tn_field:int = 0;
				var t_value:Object;
				if (tb_field){
					for (j=0;j<ia_item.length;j++){
						if (!ia_item[j].update){
							continue;
						}
						
						//only insert/update columns modified fields and inputType=="2"(when "C" fun) !!
						ts_fid = ia_item[j].fid;	
						if (t_dirty[ts_fid] || (ts_rowFun == "C" && ia_item[j].inputType == "2")){
							//var ts_type2:String = (is_fun == "C") ? ia_item[j].inputType : "U";	//欄位的異動狀態
							var ts_type2:String = (ts_rowFun == "C") ? ia_item[j].inputType : "U";	//欄位的異動狀態
							
							//convert Date field value to "null" if "1900/01/01"
							t_value = getItemByFid(ts_fid, tn_row);
							//here!! S2
							switch (ia_item[j].dataType){
								case "S2":
									t_value = ST.escape(t_value as String, 1);
									break;
								case "DT":
									if (t_value == "" || String(t_value).substr(0,10) == "1900/01/01")
										t_value = "null";
									
									break;
							}
							/*		
							if (ia_item[j].dataType == "DT" && 
								(t_value == "" || String(t_value).substr(0,10) == "1900/01/01")){
								t_value = "null";
							}
							*/		
							ta_field[tn_field] = [ts_fid, ia_item[j].dataType, t_value, ts_type2];						
							tn_field++;
						}
					}												
				}


				//利用 DW2 upSeq 來找 row no.
				var tn_upSeq:int;
				if (!ib_rows || ts_rowFun != "C" || !DW(oConfig.upEditDW).isRows())
					tn_upSeq = 0;
				else{
					tn_upSeq = -1;
					var tn_upDW:int = DW(oConfig.upEditDW).nDW;
					//var ta_upRow:Array = DW(oConfig.upEditDW).getAC().source;
					//如果上層DW2的相對應記錄也是新增狀態, 則必須找到它的資料位置
					if (t_row[Fun.csUpFun] == "C"){
						if (ta_upRow == null)
							ta_upRow = t_upDW.getAC().source;
						
						tn_upSeq = AR.find(ta_upRow, Fun.csRowSeq, t_row[Fun.csUpSeq]); 								
						/*
						for (j=0; j<ta_data.length; j++){
							if (ta_data[j].nDW == tn_upDW && ta_data[j].rowSeq == t_row[Fun.csUpSeq]){
								tn_upSeq = j;
								break;
							}
						}
						*/
						if (tn_upSeq == -1){
							Fun.msg("E", "Can not find DW2 upSeq in DW.getUpdateInfo().");
							return null;
						}
					}
				}
				
				tn_ary++;
				ta_data[tn_ary] = {
					dw: nDW,
					row: tn_row,
					rowSeq: (ib_rows) ? t_row[Fun.csRowSeq] : 0,
					upRow: tn_upSeq,
					fun: ts_rowFun,
					ident: (ts_rowFun == "C") ? in_ident : -1,
					keys: keysToArray(ts_rowFun, tn_row, 2),
					fields: ta_field
				};										
			}//for
			
			return (ta_data.length == 0) ? null : ta_data ;
		}
			
				
		/**
		 * 把 pkey 欄位的相關資訊輸出成陣列, 每一陣列元素包含 fid, type, value
		 */ 
		public function keysToArray(ps_fun:String, pn_row:int, pn_rowType:int=0):Array{		
			//get keys fields info
			var tn_item:int;
			var ts_fid:String;
			var t_item:Object;	
			var t_key:Object;				
			var ta_key:Array = [];
			for (var j:int=0;j<oConfig.keys.length;j++){
				ts_fid = oConfig.keys[j];						
				tn_item = fidToInfo(ts_fid).itemno;
				t_item = ia_item[tn_item];
				if (ps_fun == "C"){
					t_key = getItemByFid(ts_fid, pn_row, pn_rowType);
				}else{
					t_key = getOldKey(ts_fid, pn_row, pn_rowType);
				}
				ta_key[j] = [t_item.fid, t_item.dataType, t_key];
			}
			
			return ta_key;
		}
		
		
		/** 
		 * set item value and dirty flag, refer to Fun.setItem(), has overloading method !
		 * @param {object} p_field field object.
		 * @param {object} p_value value with any datatype. 
		 * @param {boolean} pb_flag set dirty flag or not, default to false. 
		 * @param {int} pn_row row no, default to 0.
		 */
		public function setItem(p_field:Object, p_value:Object, pb_flag:Boolean=false, pn_row:int=0):void{
			//update dirty flag if need
			if (pb_flag){
				this.setDirtyByField(p_field, pn_row);
			}
			
			Fun.setItem(p_field, p_value);
		}
		
		
		/**
		 * 
		 */ 
		public function setItemByFid(ps_fid:String, p_value:Object, pb_flag:Boolean=false, pn_row:int=0, pn_rowType:int=0):void{
			var tn_item:int = fidToInfo(ps_fid).itemno;
			this.setItem(ia_item[tn_item].source, p_value, pb_flag, pn_row);			
		}



		//=== set dirty instance variables begin ===
		//在系統參數設定畫面會用到這個函數, 儲存資料後, 因為畫面會停留在目前的編輯模式, 所以必須呼叫這個函數.
		//called by showData(), 新增時, pa_data可空白, 系統會設定 dirty var
		//修改時, pa_data.length 必須等於 i_ac.length
		/**
		 * 
		 */ 
		public function initDirty(ps_fun:String, pa_row:Array=null):void{
		//public function initDirty(ps_fun:String, pb_setKey:Boolean=true):void{
			/*
			if (pa_row == null || pa_row.length == 0){
				return;
			}
			*/
			
			//set instance
			if (is_fun == null){
				is_fun = ps_fun;
			}
			ib_change = false;
			ib_userChange = false;

			if (ps_fun == "V"){
				return;
			}

			var i:int;
			var ta_row:Array = i_ac.source;
			var tn_len:int = ta_row.length;
			for (i=0;i<tn_len;i++){
				ta_row[i][csFun] = ps_fun;
				ta_row[i][csDirty] = {};				
			}

			var ts_fid:String;				
			if (ps_fun == "C" && pa_row != null){
				for (i=0;i<tn_len;i++){
					for (ts_fid in pa_row[i]){
						if (ts_fid.substr(0,1) != "_"){
							ta_row[i][csDirty][ts_fid] = true;
						}				
					}
				}
			}
			
			//set key fields info
			var ts_key:String;				
			if (ps_fun == "U" || ps_fun == "D"){
				for (var j:int=0;j<oConfig.keys.length;j++){
					ts_fid = oConfig.keys[j];
					ts_key = keyFid(ts_fid);
					for (i=0;i<tn_len;i++){
						ta_row[i][csDirty][ts_key] = pa_row[i][ts_fid];
					}
				}
			}
		}


		/**
		 * 清除所有資料的 dirty 資料.
		 */ 
		public function clearDirty():void{
			var t_dirty:Object;
			var ta_row:Array = i_ac.source;
			for (var i:int=0; i<ta_row.length; i++){
				t_dirty = ta_row[i][csDirty];
				for (var ts_fid:String in t_dirty){
					if (ts_fid.substr(0,1) != "-"){
						t_dirty[ts_fid] = false;
					}
				}				
			}
		}
		
		
		/** 
		 * 設定某個欄位物件的 dirty 資料.
		 * @param p_field 欄位物件
		 * @param pn_row (Optional)資料列序號
		 */
		public function setDirtyByField(p_field:Object, pn_row:int=0):void{
			setDirtyByFid(p_field[is_fidName], pn_row);
		}
		
		
		/**
		 * 以欄位代號設定某個欄位的 dirty 資料.
		 * @param ps_fid 欄位代號
		 * @param pn_row (Optional)資料列序號
		 * @param pn_rowType (Optional)資料種類: 0(by ib_saving), 1(i_ac), 2(i_ac.source) 
		 */ 
		public function setDirtyByFid(ps_fid:String, pn_row:int=0, pn_rowType:int=0):void{
			var t_row:Object = getRow(pn_row, pn_rowType);
			var t_dirty:Object = (t_row[csDirty] != null) ? t_row[csDirty] : {} ;
			t_dirty[ps_fid] = true;

			//adjust fun type if need
			var ts_fun:String = "";
			switch (t_row[csFun]){
				case "U":
					ts_fun = "UU";
					break;
				case "DU":
					ts_fun = "DUU";
					break;
			}			
			
			if (ts_fun != ""){
				t_row[csFun] = ts_fun;
				
				//for update row icon !!				
				if (ib_rows){
					t_row[csFun] = ts_fun;														
				}
			}
			//set instance
			//is_lastStatus = "C";	//change
			ib_change = true;		
			ib_userChange = true;				
		}


		/**
		 * 設定一筆資料的 dirty error.
		 * @param ps_fid 欄位代號
		 * @param ps_error 錯誤訊息
		 * @param pn_row (Optional)資料列序號
		 * @param pn_rowType (Optional) row type.
		 */ 
		public function setDirtyByError(ps_fid:String, ps_error:String, pn_row:int=0, pn_rowType:int=0):void{
			var t_row:Object = getRow(pn_row, pn_rowType);
			var ts_fidErr:String = errorFid(ps_fid);					
			t_row[csDirty][ts_fidErr] = ps_error;
			setDirtyByFid(ps_fid, pn_row, pn_rowType);				
		}


		/**
		 * 設定一筆 dirty 資料
		 * @param ps_fun 功能代號
		 * @param pn_row 資料列序號
		 * @param p_row 原始資料, 如果功能代號不為"C", 則裡面必須包含鍵值欄位的值.
		 */ 
		public function setDirtyRow(ps_fun:String, pn_row:int, p_row:Object=null, pn_rowType:int=0):void{
			var t_row:Object = getRow(pn_row, pn_rowType);
			if (t_row[csDirty] == null){
				t_row[csDirty] = {};
			}
			
			t_row[csFun] = ps_fun;;
			
			//set key fields info
			if (ps_fun != "C" && p_row != null){
				var ts_fid:String;				
				for (var j:int=0;j<oConfig.keys.length;j++){
					ts_fid = oConfig.keys[j];
					t_row[csDirty][keyFid(ts_fid)] = p_row[ts_fid];
				}
			}			
		}	
		

		/**
		 * 資料是否被異動.
		 */ 
		public function isChanged():Boolean{
			//return (is_lastStatus == "C");
			return ib_change;			
		}
		
		
		/**
		 * 用戶是否異動資料
		 */ 
		public function isUserChanged():Boolean{
			return ib_userChange;			
		}
		

		/**
		 * (DW)檢查某個欄位是否被異動
		 * @param p_field 欄位物件
		 * @param pn_row 資料列序號
		 * @return true/false
		 */ 
		public function isFieldChanged(p_field:Object, pn_row:int=0):Boolean{
			var ts_fid:String = p_field[is_fidName];
			return isFidChanged(ts_fid, pn_row); 
		}
		
		
		/**
		 * (DW)檢查陣列裡的任一個欄位是否被異動
		 * @param pa_field 欄位陣列
		 * @param pn_row 資料列序號
		 * @return true/false
		 */ 
		public function isFieldsChanged(pa_field:Array, pn_row:int=0):Boolean{
			for (var i:int=0;i<pa_field.length;i++){
				var ts_fid:String = pa_field[i][is_fidName];
				if (isFidChanged(ts_fid, pn_row)){
					return true;
				}
			} 
			
			return false;
		}
		
		
		/**
		 * 以欄位代號檢查某個欄位是否被異動
		 * @param ps_fid 欄位代號
		 * @param pn_row 資料列序號
		 * @return true/false
		 */ 
		public function isFidChanged(ps_fid:String, pn_row:int=0, pn_rowType:int=0):Boolean{
		//public function isFidChanged(ps_fid:String, pn_row:int=0):Boolean{
			//if (ia_dirty.length <= pn_row || ia_dirty[pn_row] == null){
			var t_row:Object = getRow(pn_row, pn_rowType);
			var t_dirty:Object = t_row[csDirty]; 
			var ts_fun:String = t_row[csFun];
			if (t_dirty == null){
				return false;
			}else if (ts_fun != "C" && ts_fun != "UU"){
				return false;				
			}else{
				//var ts_fid:String = p_field[is_fidName];
				return (t_dirty.hasOwnProperty(ps_fid) && t_dirty[ps_fid] == true); 
			}			
		}

		
		/**
		 * 以欄位代號檢查某個欄位是否被異動
		 * @param pas_fid 欄位代號陣列
		 * @param pn_row 資料列序號
		 * @return true/false
		 */ 
		public function isFidsChanged(pas_fid:Array, pn_row:int=0, pn_rowType:int=0):Boolean{
		//public function isFidsChanged(pas_fid:Array, pn_row:int=0):Boolean{
			for (var i:int=0;i<pas_fid.length;i++){
				//var ts_fid:String = pa_field[i][is_fidName];
				if (isFidChanged(pas_fid[i], pn_row, pn_rowType)){
					return true;
				}
			} 
			
			return false;
		}
		

		/**
		 * 是否多筆, true(DW2), false(DW)
		 */ 
		public function isRows():Boolean{
			return ib_rows;
		}

		
		/**
		 * 把一筆資料寫入所有item的欄位
		 * @param p_row 資料 object.
		 * @param pb_dirty 是否更新 dirty 變數.
		 */ 
		public function rowToField(p_row:Object, pb_dirty:Boolean):void{
		//public function dataToField(p_data:Object, pb_dirty:Boolean):void{
			for (var ts_fid:String in p_row){
				this.setItemByFid(ts_fid, p_row[ts_fid], pb_dirty);
			}						
		}				


		/** 
		 * 檢查欄位資料是否正確.
		 * @param p_field 欄位物件(DW), DataGridColumn(DW2)
		 * @param pn_row 資料列序號, 必要 for DW2
		 * @return: true(正確), false(錯誤, 顯示訊息)
		 */ 
		public function checkField(p_field:Object, pn_row:int=-1):Boolean{
			if (is_fun != "C" && is_fun != "U"){
				return true;
			}
			
			var tn_row:int = pn_row; 
			if (!ib_rows){
				tn_row = 0;
			}else if(tn_row < 0){
				tn_row = oConfig.grid.selectedIndex;
			}
			
			
			//check normal rules first
			var tn_item:int = fieldToInfo(p_field).itemno;
			var ts_fid:String = p_field[is_fidName];
			var ts_error:String = checkField2(p_field, tn_row);
			if (ts_error != ""){
				setDirtyByError(ts_fid, ts_error, tn_row);
				this.focusItem(ia_item[tn_item]);
				Fun.msg("E",ts_error);
				//t_field.stage.focus = null;
				//is_lastStatus = "E";	//error
				return false;
			}
			
			//reset i_value
			//i_value = null;

			/*			
			//call item.fCheckItem() if existed.
			var tn_item:int = fieldToInfo(p_field).itemno;
			if (ia_item[tn_item].fCheckItem != null){
				ts_error = ia_item[tn_item].fCheckItem({fid:ia_item[tn_item].fid});
				if (ts_error != ""){		
					setDirtyByError(ts_fid, ts_error, tn_row);
					Fun.msg("E",ts_error);
					return false;
				}
			}
			*/
			
			//set instance
			//ib_change = true; 			
			setDirtyByError(ts_fid, "", tn_row);	//clear error msg.
			//is_lastStatus = "C";	//change
			

			if (ib_rows){
				//i_ac[tn_row][ic_fun] = ts_fun;		//for update row icon !!
				
				//Fun.refreshGrid(oConfig.grid);			//refresh grid
				Fun.refreshGrid(oConfig.grid, tn_row);			//refresh grid
			}
			 
			return true;			
		}

		
		/*
		 * normal check field (related, rules).
		 * called by :
		 *   (1).checkField() (field focus out.)
		 * @param {object} p_field field object. 
		 * @param {int} pn_row row number. 
		 * @return {string} error msg if any.
		 */
		private function checkField2(p_field:Object, pn_row:int=0):String{
		//public function normalCheck(p_field:Object, pn_row:int=0):String{
			
			//check related field if need
			//var ts_error:String = "";
			var tn_item:int = fieldToInfo(p_field).itemno;
			var t_item:Object = ia_item[tn_item];
			var t_value:Object = getItem(p_field, pn_row);
			var ts_value:String = String(t_value);
			if ((ts_value.indexOf("'") >= 0 || ts_value.indexOf('"') >= 0) && t_item.dataType != "S2"){
				//return t_item.fname+"不可包含 ' 和 "+'" 符號 !';				
				//return this.getFname(tn_item)+Fun.resStr("noQuote");
				return this.getFname(tn_item)+Fun.R.noQuote;
			}
			
			
			//檢查日期欄位
			if (ia_item[tn_item].dataType == "D" && ts_value != ""){
				try {
					var ts_date:String = (ts_value.indexOf("/") >= 0) ? ts_value : (ts_value.substr(0,4)+"/"+ts_value.substr(4,2)+"/"+ts_value.substr(6,2));
					var t_date:Date = new Date(Date.parse(ts_date));
					if (isNaN(t_date.date)){
						//return "日期格式輸入錯誤! (yyyy/mm/dd)";
						return Fun.R.dateWrong;
					}else{
						ts_date = ST.dateToStr(t_date);
						if (ts_date != ts_value){
							if (ib_rows){
								setItem(p_field, ts_date, true, pn_row);
								//Fun.refreshGrid(oConfig.grid);
								//p_field.validNow();
							}else{
								p_field.text = ts_date;
							}
						}
					}
				}catch (e:Error){
					//return "日期格式輸入錯誤! (yyyy/mm/dd)";
					return Fun.R.dateWrong;
				} 
			}
			
			
			/*
			var t_relat:Object = t_item.relat;			
			if (t_relat){				
				var t_data:Object = {};
				t_data[t_item.fid] = t_value;
												
				//get source field value for input parameters
				var i:int;
				var ta_field:Array = t_relat.source;
				if (ta_field != null && ta_field.length > 0){ 
					for (i=0;i<ta_field.length;i++){
						t_data[ta_field[i]] = this.getItemByFid(ta_field[i], pn_row);
					}
				}
				
				ta_field = t_relat.target;
				var t_info:Object = Fun2.getRelatInfo(sApp, t_relat.type, t_data, false);
				if (t_info == null){
					return this.getFname(tn_item)+Fun.R.inputWrong;
				}
				
				//write target fields
				for (i=0;i<ta_field.length;i++){
					this.setItemByFid(ta_field[i], t_info[ta_field[i]], true, pn_row);
				}	
				
				//refersh grid
				//if (ib_rows){
				//	Fun.refreshGrid(oConfig.grid);
				//}
			}
			*/
			
			
			//check item rules
			if (t_item.check){
				//var t_value:Object = getItem(p_field);
				var tn_value:int;
				switch (t_item.check[0]){
					case "between":
						tn_value = Number(t_value);
						if (tn_value < t_item.check[1] || tn_value > t_item.check[2]){
							return this.getFname(tn_item)+Fun.R.between+" ("+t_item.check[1]+","+t_item.check[2]+")";
						}
					default:
						break;
				}
			}
			
			//call item.fCheckItem() if existed.
			//var ts_error:String = "";
			if (t_item.fCheckItem != null){
				//did not work !!
				try {
					return t_item.fCheckItem({fid:t_item.fid});
					//ts_error = t_item.fCheckItem({fid:t_item.fid});
				}catch (e:Error){
					//Fun.msg("E", "Syntax wrong: item.fCheckItem(Object):Boolean !");
					return "Syntax wrong: item.fCheckItem(Object):String !";
				}
			}
			
			/* 改由程式視需要呼叫 refreshGrid()
			//refersh grid (include ImageDG2RowFun)
			if (ib_rows){
				Fun.refreshGrid(oConfig.grid);
			}
			*/
			
			return ""; 
		}


		/**
		 * CHANGE Event handler for 客製化文數字欄位 only(textInput, numInput, dateField).<br/>
		 */ 
		/*
		public function changeText(p_event:Event):void{
			is_lastStatus = "C";	//changed
		}			
		*/
		
		//user change all kind of fields
		private function dw1CheckField(p_event:Event):void{
			//trace("dw1CheckField");
			
			checkField(p_event.currentTarget);			
		}

				
		/**
		 * called by DW, DW2 child component, 顯示欄位訊息
		 * 設定目前欄位的值(i_value)和 ib_focus
		 * FOCUS_IN Event handler for 客製化文數字欄位.(textInput, numInput, dateField).<br/>
		 */ 
		public function fieldFocusIn(p_event:Event):void{
		//protected function fieldFocusIn(p_event:FocusEvent):void{
			//set instance 
			//is_lastStatus = "";	
			//trace("focus in");
			
			ib_focus = true;
			
			//show field tip
			var t_field:Object = getCol(p_event.currentTarget);
			var tn_row:int = (!ib_rows) ? 0 : oConfig.grid.selectedIndex;
			if (tn_row == -1){	//新增第一列時, 會捉到 -1 !!
				tn_row = 0;
			}
			/*
			var t_field:Object;
			var tn_row:int;
			if (!ib_rows){
				t_field = p_event.currentTarget;
				tn_row = 0;
			}else{
				//2011-9-3 change for spark
				//t_field = p_event.currentTarget.styleName;
				t_field = p_event.currentTarget.owner.column;
				tn_row = oConfig.grid.selectedIndex;
				if (tn_row == -1){	//新增第一列時, 會捉到 -1 !!
					tn_row = 0;
				}
				if (t_field == null){
					//t_field = p_event.currentTarget.parent.styleName;
					t_field = p_event.currentTarget.parent.owner.column;
				}
			}
			*/
			
			//set i_value
			i_value = getItem(t_field, tn_row);
			
			
			if (xMsgBox != null){			
				//var t_item:Object = ia_item[tn_item];
				var tn_item:int = fieldToInfo(t_field).itemno;
				var ts_msg:String = ia_item[tn_item].msg; 
				if (!ts_msg){
					ts_msg = "";
				}			
				xMsgBox.text = ts_msg;
			}
			
			/*
			//ime
			if (t_item.hasOwnProperty("ime") && t_field.hasOwnProperty("imeMode")){
				(t_field as Object).imeMode = (t_item == true) ? "CHINESE" : "ALPHANUMERIC_HALF"; 
			}
			*/
		}
		

		/**
		 * called by DW, DW2 child component
		 * FOCUS_OUT Event handler for 客製化的文數字可輸入欄位 .(textInput, numInput, dateField).<br/>
		 * 無法使用 is_lastStatus 變數, 因為 mousedown will cause change event !!
		 * @para pb_check 是否檢查資料, 如果是 combo 時不檢查.
		 */ 
		public function fieldFocusOut(p_event:Event, pb_check:Boolean=true):void{
			//if (!ib_focus || is_lastStatus != "C"){
			if (!ib_focus){
				return;
			}
			
			//set flag
			ib_focus = false;
			//trace("focusOut");
			
			var t_field:Object = getCol(p_event.currentTarget);
			var tn_row:int = (!ib_rows) ? 0 : oConfig.grid.selectedIndex;
			/*
			if (!ib_rows){
				t_field = p_event.currentTarget;
				tn_row = 0;				
			}else{
				t_field = p_event.currentTarget.styleName;
				if (t_field == null){
					t_field = p_event.currentTarget.parent.styleName;
				}
				tn_row = oConfig.grid.selectedIndex;
			}
			*/
			
			//is_lastStatus = checkField(t_field, tn_row) ? "C" : "E";
			//if (pb_check && i_value != getItem(t_field, tn_row)){			
			if (pb_check){			
				checkField(t_field, tn_row);
			}
		}

		
		//2012-2-14 add
		public function doAfterOpen(ps_fun:String):void {
			if (ps_fun != "D" && i_ac.length > 0 && ib_grid){
				(oConfig.grid as DataGrid).setSelectedIndex(0);
			}
			
		}
		
				
		/**
		 * 顯示資料, 並且執行  setMode(), setDirty()
		 * @param ps_fun 功能代號: C(create),U(update),V(view),D(delete),P(print,future!!)
		 * @param pa_row 資料陣列 for fun != "C" only.
		 */
		public function showData(ps_fun:String, pa_row:Array=null):void {
			if (pa_row == null){
				pa_row = [];
			}
			
			//set instance variables
			is_fun = ps_fun;
			//ib_change = false;
			//is_lastStatus = "";
			
			//initial ia_dirty[] first
			var i:int;
			//var tn_rows:int;
			//var ts_fun:String = ps_fun;		//may be change !!
			/*
			if (ps_fun == "C"){
				tn_rows = (ib_rows) ? 0 : 1;
			}else{
				tn_rows = pa_row.length; 
			} 
			*/
			
			/*
			if (ps_fun != "V"){
				//if (!ib_rows && gbAddIfNull && (pa_row == null || pa_row.length == 0)){
				if (!ib_rows){
					tn_rows = 1;
					i_ac = new ArrayCollection();
					i_ac.addItem({});
					
					//change to create !!
					if (ps_fun == "U" && (pa_row == null || pa_row.length == 0)){
						ps_fun = "C";	
						//ts_rowFun = "C";
					}
				}
				
				var ts_rowFun:String = ps_fun;
				for (i=0;i<tn_rows;i++){
					i_ac[i][ic_dirty] = {};
					i_ac[i][ic_dirty][ic_fun] = ts_rowFun;
				}					
			}
			*/
			

			//show data and set dirty variables
			in_nowSeq = 0;
			var t_field:Object;
			if (!ib_rows){	//DW
				//change to create if need !!
				if (oConfig.autoAdd && ps_fun == "U" && pa_row.length == 0){
					//pa_row = [];
					ps_fun = "C";
					//ts_rowFun = "C";
				}

				if (ps_fun == "C" || pa_row.length == 1){
					in_rows = 1;
					i_ac = new ArrayCollection();
					//ia_row = i_ac.source;
					i_ac.addItem({});
					
					initDirty(ps_fun, pa_row);
				}else{
					in_rows = 0;
				}
				
				//i_ac[0][ic_fun] = ps_fun;
				//i_ac[0][ic_dirty] = {};
				
				if (ps_fun == "C"){
					//set initial value
					var t_value:Object;
					for (i=0;i<ia_item.length;i++){
						t_field = ia_item[i].source;
						if (ia_item[i].init != null){
							this.setItem(t_field, ia_item[i].init, true); 	//set dirty flag
						}else{
							//temp replace
							this.setItem(t_field, "");
							/*
							t_value = getItem(t_field);
							if (t_value != null && t_value != ""){
								ia_dirty[0][ia_item[i].fid] = true;
							}else{
								setItem(t_field, "");
							}
							*/ 
						}
					}
				}else{
					//show data
					if (pa_row.length != 0){
						var t_row:Object = pa_row[0];
						var tn_fno:int;
						for (var ts_fid2:String in t_row){
							if (i_fidToFno.hasOwnProperty(ts_fid2)){
								tn_fno = i_fidToFno[ts_fid2];
								t_field = ia_item[tn_fno].source;
								//ts_fid = t_field[is_fidName];
								//考慮跳脫字元 !! 2012-1-5
								//if (ia_item[tn_fno].dataType == "S2"){
								//	this.setItem(t_field, ST.escape(t_row[ts_fid2], -1));									
								//}else{
									this.setItem(t_field, t_row[ts_fid2]);
								//}
							}
						}
						
						/*
						for (i=0;i<ia_item.length;i++){
							t_field = ia_item[i].source;
							ts_fid = t_field[is_fidName];
							this.setItem(t_field, pa_row[0][ts_fid]);
						}
						*/
						
						//adjust date 1900/01/01 to empty. 
						var ts_date:String;
						for (i=0;i<ia_fDate.length;i++){
							ts_date = String(Fun.getItem(ia_fDate[i])).substr(0,10);
							if (ts_date == "1900/01/01"){
								ts_date = "";
							}
								
							this.setItem(ia_fDate[i], ts_date);
						}
					}
				}
			}else{	//DW2
				if (xTool != null){
					xTool.enabled = (ps_fun == "C" || ps_fun == "U");
				}
			
				/* ??
				//remove filter function first
				if (i_ac.filterFunction != null){
					i_ac.filterFunction = null;
					i_ac.refresh();
				}
				*/				
				
				i_ac.source = new Array();
				
				//if (oConfig.upDW != null){
					//i_ac.removeAll();
					
					//if (ps_fun != "C"){
					//var tn_len:int = pa_row.length; 
					in_nowSeq = pa_row.length - 1;
					var tn_fS2:int = ias_fS2.length;
					for (i=0; i<=in_nowSeq; i++){	
						pa_row[i][Fun.csRowSeq] = i;
						
						//轉換 S2 欄位
						/*
						for (var j:int=0; j<tn_fS2; j++){
							pa_row[i][ias_fS2[j]] = ST.escape(pa_row[i][ias_fS2[j]], false);							
						}
						*/
						i_ac.addItem(pa_row[i]);
						
						//i_ac[i][ic_fun] = ps_fun;
						//i_ac[i][ic_dirty] = {};
						//i_ac[i][ic_dirty][ic_fun] = ps_fun;
						
					}
					
					
					/** 
					 * 2012-5-14 Malcom remark
					 * 改變 datagrid 目前 high light 的資料列會觸發 griditemrender
					 * 如果在 comEdit fAfterOpen 寫程式碼修改 datagrid 的資料內容時, 則有些欄位會被清空, 造成意外的結果, 
					 * 所以以下的程式碼必須移到 comEdit fAfterOpen 之後才能執行 !!
					 */ 					  
					//if (i_ac.length > 0 && ib_grid){
					//	DataGrid(oConfig.grid).setSelectedIndex(0);
					//}
					
				//}
				
				initDirty(ps_fun, pa_row);
				
				//temp add
				//i_ac.refresh();
			}
			
			/*			
			//save key column value and initial ia_dirty[]
			if (ps_fun != "V"){
				//save key column value to ia_dirty[]
				if (ps_fun == "U" || ps_fun == "D"){				
					var ts_fid:String;
					var tn_item:int;
					//var ta_key:Array = oConfig.keys;
					for (i=0;i<tn_rows;i++){
						for (var j:int=0;j<oConfig.keys.length;j++){
							//t_field = oConfig.keys[j];
							ts_fid = oConfig.keys[j];
							//tn_item = fidToInfo(ts_fid).itemno;
							//ts_fid = ia_item[tn_item].fid;
							//ia_dirty[i][keyFid(ts_fid)] = pa_row[i][ts_fid];
							i_ac[i][ic_dirty][keyFid(ts_fid)] = pa_row[i][ts_fid];
						}
					}
				}											
			}
			*/
			
			//set mode if need
			if (ps_fun != "D"){
				this.setMode(ps_fun);
			}
			
			//set change flag
			ib_change = (ps_fun == "C" && pa_row != null) ? true : false;
			//ib_userChange = false;
		}		

		
		/**
		 * (move to showData() ??)重新設定 instance 變數, called by edit window afterOpen()
		 */ 
		/*
		public function zz_resetVar():void{
			ib_userChange = false;			
		}
		*/
		
		/**
		 * 設定欄位編輯模式, overrided in DW2.<br/>
		 * 如果是TextArea欄位將不設反白(for copy field)
		 * @param ps_fun 功能代號: C(create),U(update),V(view)
		 */
		public function setMode(ps_fun:String):void{
			/*
			if (ib_rows && !ib_grid){
				return;
			}
			*/
			
			var j:int;
			var t_field:Object;
			//if (!oConfig.updatable || in_rows == 0){
			if (!oConfig.editable || in_rows == 0){
				for (j=0;j<ia_item.length;j++){
					t_field = ia_item[j].source;
					t_field.enabled = false;					
				}								
			}else{
				var tb_edit:Boolean;
				for (j=0;j<ia_item.length;j++){				
					t_field = ia_item[j].source;
					
					switch (ps_fun){
						case "C":	//create new
							tb_edit = (ia_item[j].inputType != "R" && ia_item[j].inputType != "I");
							break;
						case "U":	//update
							tb_edit = (ia_item[j].inputType == "U");
							break;
						default:
							tb_edit = false;
							break;
					}
				
					if (t_field.className == "TextArea"){
						t_field.editable = tb_edit;						
					}else{
						t_field.enabled = tb_edit;
					}				
				}
			}
		}

		
		/**
		 * 傳回資料筆數.
		 */ 
		public function rowsCount():int{
			return (ib_rows) ? i_ac.source.length : in_rows;
		}

				
		//abstract method !!
		/** 
		 * return current error msg
		 * @param {Boolean} pb_focus: focus field or not when find error.
		 * @param {int} item page, if -1 means all pages, or will only check item in this page.
		 * @return {string} error msg if any. 
		 */ 
		protected function errorMsg(pb_focus:Boolean=true, pn_page:int=-1):String{
			var ts_fidErr:String;
			var t_dirty:Object;
			for (var tn_col:int=0;tn_col<ia_item.length;tn_col++){
				if (pn_page >=0 && ia_item[tn_col].page != pn_page){
					continue;
				}
				
				ts_fidErr = errorFid(ia_item[tn_col].fid); 
				for (var tn_row:int=0;tn_row<i_ac.length;tn_row++){
					//if (i_ac[tn_row][csFun] == "U")		//如果資料沒有任何異動, 則不檢查 !!
					//	continue;
					
					//if (ia_item[tn_col].error != ""){
					//if (t_dirty && ia_dirty[tn_row][ts_fidErr] && ia_dirty[tn_row][ts_fidErr] != ""){
					t_dirty = i_ac[tn_row][csDirty];
					if (t_dirty != null && t_dirty[ts_fidErr] != null && t_dirty[ts_fidErr] != ""){
						var t_field:Object = ia_item[tn_col].source;	//??
						//t_field.setFocus();	//will error !!
						if (pb_focus){
							this.focusItem(ia_item[tn_col]);
						}
						return t_dirty[ts_fidErr];
					}
				}				
			}
			
			//case of not found error
			return "";		
		}
			
			
		/**
		 * 檢查所有資料列, (called by comEdit when save)	
		 * @param pb_focus 如果錯誤時, 是否將游標停駐在欄位上.
		 * @param pn_page 要檢查的 page 序號, 如果為 -1 表示檢查全部 pages. 
		 * @return 錯誤訊息 if any. 
		 */ 
		public function checkRows(pb_focus:Boolean=true, pn_page:int=-1):String{
		//public function checkData():String{
			if (!oConfig.updatable){
				return "";
			}
			
			//check current error status
			var ts_error:String = errorMsg(pb_focus, pn_page);
			if (ts_error != ""){
				return ts_error;
			}
			
			//check mandortary fields
			//var t_field:Object;
			var ts_fid:String;			
			for (var i:int=0;i<ia_item.length;i++){
				if (pn_page >=0 && ia_item[i].page != pn_page){
					continue;
				}

				var ts_rowFun:String;
				if (ia_item[i].required){
					//t_field = ia_item[i].source;
					ts_fid = ia_item[i].fid;
					for (var tn_row:int=0;tn_row<i_ac.length;tn_row++){
						ts_rowFun = i_ac[tn_row][csFun];
						if (ts_rowFun.substr(0,1) == "D"){
							continue;
						}else if (Fun.isEmpty(this.getItemByFid(ts_fid,tn_row))){
							if (pb_focus){
								this.focusItem(ia_item[i]);
							}
							//return ia_item[i].fname+"不可空白 !";
							//return this.getFname(i)+Fun.R.resStr("notEmpty");
							return this.getFname(i)+Fun.R.notEmpty;
						}
					}
				}
			}
			
			//case of not found error
			return "";
		}


		/**
		 * 自動欄位中是否有 serverDT 欄位. (called by comEdit)
		 */ 
		public function hasServerDT():Boolean{
			return (oConfig.autos[1] || oConfig.autos[3]); 
		}
		
					
		/**
		 * 傳回某個欄位物件的欄位資訊(dw, itemno).<br/>
		 * field -> fid(is_fidName) -> fno(i_fidToFno) -> ia_field
		 * 2011/4/6 change to public 
		 */ 
		protected function fieldToInfo(p_field:Object):Object{
			return fidToInfo(p_field[is_fidName]);
		}


		/**
		 * 傳回某個欄位代號的欄位資訊(dw, itemno). 
		 */ 
		protected function fidToInfo(ps_fid:String):Object{
			if (i_fidToFno.hasOwnProperty(ps_fid)){
				var tn_fno:int = i_fidToFno[ps_fid];
				return ia_field[tn_fno];				
			}else{
				Fun.msg("E", "aoDW["+nDW+"] "+ps_fid +" is Wrong in DW.fidToInfo() !");
				return null;				
			}			
		}
		
		
		/**
		 * 傳回某個欄位代號的 item object.
		 */ 
		public function fidToField(ps_fid:String):Object{
			var t_item:Object = fidToItem(ps_fid);
			return (t_item != null) ? t_item.source : null;
		}


		/**
		 * 傳回某個欄位代號的 item object.
		 */ 
		public function fidToItem(ps_fid:String):Object{
			if (i_fidToFno.hasOwnProperty(ps_fid)){
				var tn_fno:int = i_fidToFno[ps_fid];
				var tn_item:int = ia_field[tn_fno].itemno;
				return ia_item[tn_item];				
			}else{
				return null;				
			}						
		}


		//return error fid in dirty[]
		/**
		 * 傳回 dirty 欄位名稱 for error.
		 */ 
		public function errorFid(ps_fid:String):String{return "_err_"+ps_fid;}


		/**
		 * 傳回 dirty 鍵值欄位名稱.
		 */ 
		public function keyFid(ps_fid:String):String{return "_key_"+ps_fid;}


		/**
		 * ?? open popup window for query someone field.
  		 * @param {string} ps_fid query field id.
  		 * @param {object} p_data data with query field value, has below elements :
  		 *   //upWin: parent window.
  		 *   //dw: dw no of this field.
  		 *   toFields: fields array to return, ex:[memberType,memberId,memberName].
  		 * @return {object} window object.
		 */
		/*
		public function queryField(ps_type:String, pa_toField:Array=null, pf_callback:Function=null):Object {
			//check first
			if (is_fun != "C" && is_fun != "U"){
				return null;
			}
			
			//adjust
			if (pa_toField == null){
				pa_toField = []
			}
			
  			//if (!pa_toField || pa_toField.length == 0){
  			//	Fun.msg("E","DW.queryField().pa_toField[ ] can not be Empty !");
  			//	return;
  			//}
			
			var t_data:Object = Fun2.getQueryField(ps_type);
			if (t_data == null){
  				Fun.msg("E","Fun2.getQueryField() has wrong type of '"+ps_type+"' !");
  				return null;				
			}
			
			
			//set and open query window 
			var tw_query:TitleWindow = t_data.win;
			tw_query.title = t_data.title; 
			Fun.openPopup(tw_query, Object(this).parent);
			//tw_query.showCloseButton = true;		//show close button
			
			//set global variable before show popup
			//Fun.gObject = "queryField"; 
			
			//set grd_list and binding dataprovider									  						//set result ds and select string
			var t_query:comQuery = (tw_query as Object).qry_1;
			t_query.sApp = sApp;
			t_query.sInf = t_data.inf;
			//t_query.gfCallback = (pf_callback != null) ? pf_callback : queryField2;
			t_query.fSelectRow = queryField2;		//callback fn when select row.
			t_query.init(); 
			
			//set instance
			i_object = {				
				fields: pa_toField,
				fnCallback: pf_callback
			}
			
			return tw_query;
		}
		*/
		

		/*
		 * (DW)callback function of queryField()
  		 * @param p_row return data.
		 */   		
		private function queryField2(p_row:Object):void {
  			if (!ib_rows){
  				var ta_field:Array = i_object.fields as Array;
  				for (var i:int=0;i<ta_field.length;i++){
	  				this.setItem(ta_field[i], p_row[ta_field[i][is_fidName]], true);  					
  				}
  				
  				if (i_object.fnCallback != null){
  					i_object.fnCallback(p_row);
  				}
  			}
		}


		/**
		 * (DW)將游標停駐在某個欄位.
		 * @param p_field 欄位物件.
		 */ 
		public function focusField(p_field:Object):void {
			var tn_item:int = fieldToInfo(p_field).itemno;
			focusItem(ia_item[tn_item]);
		}
		
		
		/**
		 * (DW)將游標停駐在某個 item.
		 * @param p_item item Object.
		 */ 
		public function focusItem(p_item:Object):void {
			
			//set tabNavigator selectedIndex if found.
			//最多十層			
			var t_item:Object;
			if (ib_rows){
				//t_item = p_item.source.owner;	//will DG2
				t_item = this.oConfig.grid;
			}else{
				t_item = p_item.source;			//get field
			}
			
			var t_page:Object;
			for (var i:int=0;i<10;i++){
				//if (t_item.hasOwnProperty("owner") == null){
				if (!t_item.hasOwnProperty("owner")){
					break;
				}
				
				t_item = t_item.owner;
				if (t_item.hasOwnProperty("className") && t_item.className == "TabNavigator"){
					t_item.selectedChild = t_page;
					break;
				}else{
					t_page = t_item;
				}				 
			}
			
			if (!ib_rows){
				//p_item.source.setFocus();
				//Fun.setFocus(null, p_item.source, true);
				Fun.focusField(p_item.source, true);
			}
		}
		
		
		/**
		 * 傳回欄位物件, overrided in DW2
		 * @param p_field field Object.
		 */ 
		protected function getCol(p_field:Object):Object {
			return p_field;
		}
		
		
		/**
		 * (DW)設定 item 屬性
		 * @param p_field field 欄位.
		 * @param ps_prop item 屬性名稱.
		 * @param p_value item 屬性值.
		 */
		/*
		public function setItemProp(p_field:Object, ps_prop:String, p_value:Object):void {
			var tn_itemno:int = fieldToInfo(p_field).itemno;
			ia_item[tn_itemno][ps_prop] = p_value;
		}
		*/
		
	}//class
	
}//package