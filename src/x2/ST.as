/**
 * String
 */ 
package x2{
	import mx.utils.StringUtil;
	
	
	public class ST{
		//public function ST(){}
		
		//特殊字元取代, for dataType="S2"
		//public static const casFind:Array = ["'","\""];
		public static const casFind:Array = ["'",'"',"&","+"];
		//public static const casReplace:Array = ["#a#","#b#","#c#"];		//for save db
		public static const casReplace:Array = ["#a#","#b#","#c#","#d#"];		//for save db
		public static const casReplace2:Array = ["'",'\\"',"&","+"];		//db to flex json
		//public static const casReplace:Array = ["#!#","#@#","###"];		//for save db
		
		/**
		 * replace string for escape string
		 * @param ps_str source string
		 * @param pn_encodeType 1(encode)/0(decode:casReplace->casFind)/-1(decode:casReplace->casReplace2)
		 * @return result string
		 */ 
		public static function escape(ps_str:String, pn_encodeType:int):String{
			if (ps_str == null || ps_str == "")
				return "";
			
			var tas_find:Array;
			var tas_replace:Array;
			switch (pn_encodeType){
				case 1:
					tas_find = casFind;
					tas_replace = casReplace;
					break;
				case 0:
					tas_find = casReplace;
					tas_replace = casFind;				
					break;
				case -1:
					tas_find = casReplace;
					tas_replace = casReplace2;				
					break;
			}
			
			for (var i:int=0; i<tas_find.length; i++){
				//ps_str = ps_str.replace(tas_find[i] as String , tas_replace[i] as String);	//這種方法只能取代一次 2012-1-5
				ps_str = ps_str.split(tas_find[i] as String).join(tas_replace[i] as String);	//可以取代全部
				//var t_ptn:RegExp = new RegExp(tas_find[i] as String, "g");
				//ps_str = ps_str.replace(t_ptn, tas_replace[i] as String);
			}
			return ps_str;
		}
		
		
		//=== for flex 4.5 ===
		//字串包含斷行符號(\n)時, flex 4.6 內定的 json lib 無法正確 decode, 所以改用外部的 json lib ()
		import com.json.*;
		public static function jsonToStr(p_data:Object):String{
			return com.json.JSON.encode(p_data);
		}		
		
		/**
		 * @param ps_data
		 * @param pb_escape 是否處理特殊字元
		 */ 
		public static function toJson(ps_data:String, pb_escape:Boolean=false):Object{
			return com.json.JSON.decode(pb_escape ? escape(ps_data, -1) : ps_data);
		}
		
		
		
		/**
		 * 日期字串轉換成日期
		 * @param ps_date 日期字串.(yyyy/MM/dd or yyyy-MM-dd)
		 * @return 日期, 如果輸入日期字串是空白, 則傳回 null.
		 */ 	
		public static function toDate(ps_date:String):Date {
			if (Fun.isEmpty(ps_date)){
				return null;	
			}
			
			var ts_date:String = ps_date.replace(/-/g, "/");
			var tn_date:Number = Date.parse(ts_date); 
			return isNaN(tn_date) ? null : new Date(tn_date);
			//var td_date:Date = new Date(Date.parse(ts_date)); 
			//return isNaN(td_date) ? null : td_date ;
		}
		
		
		
		/**
		 * 把日期字串的內容寫入日期,時,分 3 個欄位
		 * @param ps_dt 日期字串(yyyy/MM/dd hh:mm)
		 * @param p_data 日期欄位
		 * @param p_hour 小時欄位
		 * @param p_min 分鐘欄位
		 * @return true(成功)/false(失敗)
		 */ 	
		public static function toDHM(ps_dt:String, p_date:Object, p_hour:Object, p_min:Object):Boolean {
			if (Fun.isEmpty(ps_dt)){
				return false;
			}else{	
				var td_date:Date = ST.toDate(ps_dt);
				if (td_date != null){
					Fun.setItem(p_date, ps_dt.substr(0,10));
					Fun.setItem(p_hour, td_date.hours);
					Fun.setItem(p_min, td_date.minutes);
					return true;
				}else{
					Fun.setItem(p_date, "");
					Fun.setItem(p_hour, 0);
					Fun.setItem(p_min, 0);
					return false;
				}					
			}
		}
		
		
		/**
		 * 傳回日期,時,分 3 個欄位的日期字串
		 * @param p_data 日期欄位
		 * @param p_hour 小時欄位
		 * @param p_min 分鐘欄位
		 * @return 日期字串(yyyy/MM/dd hh:mm)
		 */ 	
		public static function dhmToStr(p_date:Object, p_hour:Object, p_min:Object):String {
			var ts_date:String = Fun.getItem(p_date) as String;
			var tn_hour:int = int(Fun.getItem(p_hour));
			var tn_min:int = int(Fun.getItem(p_min));
			var ts_time:String = preZero(2,tn_hour)+":"+preZero(2,tn_min);
			return ts_date.substr(0,10)+" "+ts_time;
		}
		
		
		/**
		 * 把時分字串的內容寫入時,分 2 個欄位
		 * @param ps_hm 時分字串(hh:mm)
		 * @param p_hour 小時欄位
		 * @param p_min 分鐘欄位
		 * @return true(成功)/false(失敗)
		 */ 	
		public static function toHM(ps_hm:String, p_hour:Object, p_min:Object):Boolean {
			if (Fun.isEmpty(ps_hm)){
				return false;
			}else{	
				var tas_hm:Array = ps_hm.split(":");
				if (tas_hm == null || tas_hm.length != 2){
					return false;
				}else{
					Fun.setItem(p_hour, int(tas_hm[0]));
					Fun.setItem(p_min, int(tas_hm[1]));
				}
				return true;
			}
		}
		
		
		/**
		 * 傳回時,分 2 個欄位的字串
		 * @param p_hour 小時欄位
		 * @param p_min 分鐘欄位
		 * @return 時分字串(hh:mm)
		 */ 	
		public static function hmToStr(p_hour:Object, p_min:Object):String {
			var tn_hour:int = Fun.getItem(p_hour) as int;
			var tn_min:int = Fun.getItem(p_min) as int;
			return preZero(2,tn_hour)+":"+preZero(2,tn_min);
		}
		
		
		/**
		 * 建立字串
		 * @param ps_key 欄位名稱
		 * @param ps_file resorce 設定檔名稱, 預設為 Setting (系統公用設定檔)
		 * @return 欄位值字串
		 */ 
		//public static function strBuild(ps_text:String, pa_str:Array):String{
		public static function format(ps_text:String, pa_str:Array):String{
			for (var i:int=0;i<pa_str.length; i++){
				ps_text = replace(ps_text, "{"+i+"}", String(pa_str[i]));
			}
			return ps_text;		
		}
		
		
		/**
		 * 取代字串中的部分文字
		 * @param ps_text 原始字串
		 * @param ps_find 尋找的部分字串
		 * @param ps_replace 新的部分字串
		 * @return 新字串
		 */ 
		public static function replace(ps_text:String, ps_find:String, ps_replace:String):String{
			var tn_find:int;
			while (true){
				tn_find = ps_text.indexOf(ps_find);
				if (tn_find < 0){
					break;
				}else{
					ps_text = ps_text.substring(0, tn_find)+ps_replace+ps_text.substring(tn_find+ps_find.length);
				}
			}
			
			return ps_text;
		}
		
		
		/**
		 * 日期轉換成日期字串
		 * @param p_date 日期
		 * @return 日期字串, 如果輸入日期是 null, 則傳回 ""
		 */ 	
		public static function dateToStr(p_date:Date):String {
			if (p_date == null){
				return "";
			}else{	
				//return p_date.fullYear.toString()+"/"+(p_date.month+1).toString()+"/"+(p_date.date).toString();
				return p_date.fullYear.toString()+"/"+preZero(2, p_date.month+1)+"/"+preZero(2, p_date.date);
			}
		}
		
		
		//重複字串
		public static function repeat(ps_str:String, pn_times:int):String{
			return StringUtil.repeat(ps_str, pn_times);
			//return (pn_times == 0) ? "??" : "--";
		}
		
		
		/**
		 * get right string.
		 * @return right string
		 */ 
		public static function right(ps_str:String, pn_len:int):String {
			return ps_str.substr(ps_str.length - pn_len, pn_len);
		}
		
		
		//cut string
		public static function cut(ps_sour:String, ps_find:String):String{
			var tn_pos:int = ps_sour.indexOf(ps_find);
			return (tn_pos > 0) ? ps_sour.substr(0, tn_pos): ps_sour ;
		}
		
		
		/** 
		 * 視需要把欄位值加上雙引號.
		 * @param ps_type inputType of field. 
		 * @param p_value null or column value. 
		 * @return field value with string format.
		 */ 
		public static function addQuote(ps_type:String, p_value:Object):String {
			var ts_value:String = String(p_value);
			//if (ps_type == "S" || ps_type == "D"){	//string, datetime
			if (ps_type != "N"){
				return "'" + ts_value + "'";
			}else{
				return ts_value;
			}
		}
		
		
		/**
		 * 視需要在目錄變數的右邊加上反斜線.
		 * @param ps_dir 目錄變數. 
		 * @return 新字串.
		 */ 
		public static function addSlash(ps_dir:String):String{
			return (ps_dir.substr(ps_dir.length - 1, 1) == "/") ? ps_dir : ps_dir +"/";
		}
		
		
		/** 
		 * 在某個值前面加上 0
		 * @param pn_len 結果字串的長度. 
		 * @param p_data 傳入值, 可為字串或數字 
		 * @return 新字串.
		 */ 
		public static function preZero(pn_len:int, p_data:Object):String {
			//var ts_num:String = pn_num.toString();
			var ts_num:String = String(p_data);
			var tn_len:int = pn_len - ts_num.length;	    
			if (tn_len <= 0){
				return ts_num;
			}else{
				//var ts_zero:String = "0000000000";
				//return ts_zero.substr(0, tn_len)+ts_num;
				return StringUtil.repeat("0", tn_len)+ts_num;
			}			
		}		
		
	}//class
}//package