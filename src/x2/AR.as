/**
 * Array
 */ 
package x2{
	import mx.collections.ArrayList;
	
	public class AR{
		//public function AR(){}

		
		//get codeTable ds label				
		//public static function codeDSLabel(pa_data:Array, p_data:Object):String {
		/**
		 * 傳回陣列中某個欄位值所對應的顯示文字, 一般用來 DataGridColumn 的 labelFunction.<br/>
		 * 這個陣列的資料必須包含 data, label 欄位.
		 * @param pa_data 來源陣列.
		 * @p_data 要尋找的欄位值, 可為字串或數字.
		 * @return 對應的顯示文字, 如果找不到則傳回欄位值字串.
		 */ 
		public static function getLabel(pa_data:Array, p_data:Object):String {
			if (p_data == null){
				return "";
			}
			
			var ts_data:String = String(p_data);
			for (var i:int=0;i<pa_data.length;i++){
				//if (pa_data[i].data == p_data){
				if (String(pa_data[i].data) === ts_data){
					return pa_data[i].label;
				}
			}
			
			//case of not found
			return ts_data;
		}
		
		public static function getListLabel(pa_data:ArrayList, p_data:Object):String {
			if (p_data == null){
				return "";
			}else{
				return getLabel(pa_data.source, p_data);
			}
		}
		
		
		//尋找陣列裡的某個元素
		//傳回陣列位置, -1: not found
		public static function find(pa_data:Array, ps_fname:String, p_value:Object):int {
			if (pa_data != null){
				for (var i:int=0;i<pa_data.length;i++){
					if (String(pa_data[i][ps_fname]) == String(p_value)){
						return i;
					}
				}
			}
			
			//case of not found
			return -1;
		}
		
		
		/**
		 * 加一筆資料到一個陣列的第一行.(for ComboBox)
		 * @param pa_data 來源陣列
		 * @param p_row 新資料
		 * @return 新陣列.
		 */ 
		public static function addRow(pa_row:Array, p_row:Object):Array {
			if (pa_row == null){
				pa_row = [];
			}
			
			pa_row.splice(0, 0, p_row);
			return pa_row;
		}
		
		
		/**
		 * 加一筆空白資料到一個陣列第一行.(for ComboBox)
		 * @param pa_data 來源陣列
		 * @param pb_addEmpty 是否增加空白列.
		 * @param pb_label (true)是否使用 Fun2.csLabelForEmpty 做為 label, 如果否則為空白.
		 * @param pb_editWin (true)是否編輯畫面
		 * @return 新陣列.
		 */ 
		public static function addEmpty(pa_row:Array, pb_addEmpty:Boolean, pb_editWin:Boolean=true):Array {
			if (!pb_addEmpty){
				return pa_row;
			}else{
				var t_row:Object = {
					data: "",
					label: (pb_editWin) ? "" : Fun2.csEmptyLabel
				};
				return addRow(pa_row, t_row)
			}
		}
		
		
		/**
		 * 將陣列中的某些欄位做特殊字元的轉換(解碼)
		 * @param pa_source source array
		 * @param pas_fid 要轉換的欄位代號陣列
		 */ 
		/*
		public static function arrayEscape(pa_source:Array, pas_fid:Array):void{
			if (pas_fid == null || pas_fid.length == 0)
				return;
			
			var tn_s2:int = pas_fid.length;
			for (var i:int=0;i<pa_source.length;i++){				
				for (var j:int=0; j<tn_s2; j++){
					pa_source[i][pas_fid[j]] = ST.escape(pa_source[i][pas_fid[j]], 0);
				}
			}
		}
		*/
		
		/**
		 * 產生亂序的陣列, 不影響原來的陣列
		 * @param pa_source source array
		 * @param pb_content 傳回的陣列是否包含內容, 如果否, 則只會包含陣列序號(內容為數字, 儲存舊陣列的序號)
		 */ 
		//public static function random(pa_source:Array, pb_content:Boolean):Array{
		public static function random(pa_source:Array, ps_fid:String):Array{
		//public static function random(pa_source:Array):Array{
			
			//initial, set to -1
			var i:int;
			var tn_len:int = pa_source.length;
			var tan_new:Array = [];
			for (i=0; i<tn_len; i++){
				tan_new[i] = -1;					
			}
			
			var j:int;
			var tn_rand:int;
			var tb_find:Boolean;
			for (i=0; i<tn_len; i++){
				tn_rand = Math.random() * tn_len;
				if (tan_new[tn_rand] == -1){
					tan_new[tn_rand] = i;
				}else{
					//從最後面開始找
					tb_find = false
					for (j=tn_len - 1; j>=0; j--){
						if (tan_new[j] == -1){
							tan_new[j] = i;
							tb_find = true;
							break;
						}							
					}
					
					//從最前面開始找
					if (!tb_find){
						tb_find = false
						for (j=0; j<tn_len; j++){
							if (tan_new[j] == -1){
								tan_new[j] = i;
								tb_find = true;
								break;
							}							
						}	
						
						//case of wrong !!
						if (!tb_find){
							Fun.msg("E", "Wrong in Fun.as randArray()");
						}
					}
				}
			}
			
			//return tan_new;
			
			//if (!ps_fid == "")
			//	return tan_new;
			
			
			var ta_new:Array = [];
			var tn_ary:int;
			
			if (ps_fid == ""){
				for (i=0; i<tn_len; i++){
					ta_new[i] = pa_source[tan_new[i]];
				}
			}else{
				for (i=0; i<tn_len; i++){
					ta_new[i] = pa_source[tan_new[i]][ps_fid];
					/*
					tn_ary = tan_new[i];
					ta_new[i] = {};
					for (var fid:String in pa_source[tn_ary]){
					ta_new[i][fid] = pa_source[tn_ary][fid];
					}
					*/
				}				
			}
			
			return ta_new;
		}
		
		
		/**
		 * 排序陣列, 可以指定每個欄位的 asc/desc
		 * @param p_array source array
		 * @param pas_field 欄位陣列, ex["f1","f2"]
		 * @param pas_asc 排序陣列, ex["A","D"]
		 * @return 排序後的陣列
		 */ 
		public static function sort(p_array:Array, pas_field:Array, pas_asc:Array):void{
			/*
			http://wiki.geckos.cn/As3_array_sorton%E6%96%B9%E6%B3%95
			http://www.actionscript.org/forums/showthread.php3?t=60763
			
			var cmp = function (a, b, l) {
			var p = l.shift();
			var c = a[p];
			var d = b[p];
			//Descending
			return (c>d) ? -1 : (c<d) ? 1 : (l.length) ? cmp(a, b, l) : 0;
			//Ascending
			//return (c<d) ? -1 : (c>d) ? 1 : (l.length) ? cmp(a, b, l) : 0;
			};
			this.sort(function (a, b) {
			return cmp(a, b, l.split(","));
			});
			*/
		}						
	}//class
}//package