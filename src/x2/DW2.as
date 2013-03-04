/**
 * 1.如果有filter功能, 則在 ComEdit.fAfterOpen 執行
 */

package x2{
	
	import flash.events.*;
	
	import mx.collections.*;
	
	import spark.components.*;
	import spark.events.GridSelectionEvent;

	//import mx.events.CollectionEvent;
	
	
	public class DW2 extends DW {
		
		//=================	
		//=== public ===
		//=================
		
		/**
		 * 執行 CUD 之前要執行的函數.(para object:fun/return boolean)
		 */ 
		public var fWhenFun:Function;

		/**
		 * 執行 CD 之後要執行的函數.(para object:fun/void)
		 */ 
		public var fAfterFun:Function;
		
		/**
		 * filter function. (para object:fun/void)
		 */ 
		public var fFilter:Function;
		
		//當存在 filterFunction時, 利用這2個欄位值來建立與上層 DW 的關聯
		//儲存的內容皆為 i_ac.source 的資料序號, 不含 filter !!		
		//private var in_nowSeq:int;
				
		
		//keep deleted records keys, same as DW.getUpdateInfo().keys :
		//keys: array of [fid,dataType,value]
		private var ia_delete:Array = [];
			
				
		/**
		 * 欄位數目太多時, 使用 grid 和 form 來實作 DW2, 這種類型在此稱為tp2(type2)
		 * 使用時, grid 的欄位名稱必須和 form 的欄位id 一致.(grid的欄位數必須大於等於form的欄位數)
		 * 操作時, 系統會自動同步 grid 和 form 的欄位資料, 
		 * 以下為相關變數
		 */		 
		//grid 目前的 row position  		
		private var in_tp2Row:int;
		
		
		//form 欄位物件, 不可包含關聯欄位和 identity 欄位 !!
		private var ia_tp2Field:Array;
		private var in_tp2Field:int = 0;	//用這個欄位來分辨是否為 type2		
		private var ia_tp2Fid:Array = [];	//grid 欄位名稱
		
		
		//private var in_Row:int=-1;		//for 1-n2-n3, current row no of n2, for n2 and n3 are New
		
		//private const ic_rowFun:String = "_rowFun";		//must match imgDG2RowFun.ic_rowFun !!
		//public var gbStatus:Boolean = true;		//used in dataGrid sub control only !!
						
		/*
		public function DW2(){
			super();
						
			//set instance variables
			//ib_rows = true;				//true(multiple), false(single)	
			//is_fidName = "dataField";	//name of field id, dw is "id", dw2 is "dateField"
		}
		*/
		
		//return classname property
		public function get className():String{
			return "DW2";
		}
		
		/**
		 * see DW.init()
		 */ 		
		override public function init(pb_readDB:Boolean):String{
			//if (oConfig.grid == null){
			//	return "oConfig.grid 不可空白 !"
			//}
			
			//instance value for dw2
			ib_grid = (oConfig.grid != null);
			ib_rows = true;				//true(multiple), false(single)	
			is_fidName = "dataField";	//name of field id, dw is "id", dw2 is "dateField"
			//i_ac = new ArrayCollection();
			ia_item = oConfig.items;
			ia_delete = [];
			//i_tab = p_tab;
						
			if (xTool != null){
				xTool.gDW2 = this;
			}
			
			initDW();
			
			//set item property: fno, source, fname, instance
			//var tb_grid:Boolean = (oConfig.grid != null); 
			var i:int;
			if (ib_grid){		//dw2 with grid
				var ts_fid:String;
				var t_item:Object;
				var t_field:Object;				
				var ta_col:Array;
				//if (ib_grid){
					//check
					if (oConfig.grid.className != "DG2"){
						return "Grid of DW["+nDW+"].className should be DG2 !" 	
					}
					
					//spark 必須使用 columns.source (array) 
					//ta_col = oConfig.grid.columns;
					ta_col = oConfig.grid.columns.source;
				//}
				
				for (i=0;i<ia_item.length;i++){
					t_item = ia_item[i];				
					ts_fid = t_item.fid;
					var tn_find:int;
					//if (ib_grid){	
						tn_find = -1;					
						for (var tn_col:int=0;tn_col<ta_col.length;tn_col++){
							if (ta_col[tn_col].dataField == ts_fid){
								tn_find = tn_col;
								break;
							}  
						}
						if (tn_find == -1){
							return "aoDW["+nDW+"].ia_item["+i.toString()+"].fid='"+ts_fid+"' did not exist in DataGrid! (DW2.init())";	
						}
							
						t_item.source = ta_col[tn_find];
						
						//set read only
						if (t_item.inputType == "R"){
							ta_col[tn_col].editable = false;
						}					
					//}
										
					//set instance variables 
					t_item.fno = tn_find;					
					i_fidToFno[ts_fid] = tn_find;
					ia_field[tn_find] = {dw:nDW, itemno:i};
					initItem(i);
					
					//fname
					//t_item.fname = " [ "+ta_col[tn_col].headerText+" ] ";						
				}
																					
				//convert
				//fieldToFid(oConfig.upQKeys);			
			}else{
				for (i=0;i<ia_item.length;i++){
					t_item = ia_item[i];				
					t_item.fno = i;					
					i_fidToFno[t_item.fid] = i;			
					ia_field[i] = {dw:nDW, itemno:i};
					initItem(i);
				}				
			}


			//set instance variables
			//var ts_error:String = initPublic(p_data);
			//var ts_error:String = initPublic(pb_readDB);
			/*
			var ts_error:String = initPublic();
			if (ts_error != ""){
				return ts_error;
			}
			*/
			
			/*
			if (!tb_grid){
				return "";
			}
			*/
			
			//add column for set font color, do before initPublic() !!
			if (ib_grid){
				var t_grid:DG2 = DG2(oConfig.grid);
				t_grid.gDW2 = this;
				
				//temp remark
				//t_grid.selectable = true;
				
				i_ac = t_grid.dataProvider as ArrayCollection;
				if (i_ac == null){
					i_ac = new ArrayCollection();					
					t_grid.dataProvider = i_ac;
				}
				
				if (fFilter != null)
					i_ac.filterFunction = filterRow;
				
			}else{
				i_ac = new ArrayCollection();					
			}
			//ia_row = i_ac.source;
			
			/*				
			if (oConfig.editable != null){
				t_grid.editable = oConfig.editable;
			}
			*/
			
			//drawRowBackground ??
			//t_grid.x2f_drawRowBackground = drawRowBackground;
			//=== end ===
									
			
 			//add field got focus event handler
			//t_grid.addEventListener(DataGridEvent.ITEM_FOCUS_IN, fieldFocusIn);				 				 			
			//t_grid.addEventListener(DataGridEvent.ITEM_FOCUS_OUT, fieldFocusOut);
			//t_grid.addEventListener(DataGridEvent.ITEM_EDIT_BEGINNING, fieldFocusOut);
			
			//i_ac.addEventListener(CollectionEvent.COLLECTION_CHANGE, checkField2);
			//t_grid.addEventListener(DataGridEvent.ITEM_EDIT_BEGINNING, checkField);
			//t_grid.addEventListener(mx.events.FlexEvent.DATA_CHANGE, checkField); 			
			//t_grid.addEventListener(mx.events.ListEvent.CHANGE, checkField);
			
			return "";
		}

				
		//private var i_filterRow:Object = {};
		//p_row row in up DW, 有 filter 功能時, 必須在上層 DW 呼叫這個函數
		private var in_upSeq:int;
		private function filterRow(p_row:Object):Boolean{
			if (p_row[Fun.csUpSeq] == null)	//for old row
				return fFilter(p_row);
			else	//for new row			
				return (p_row[Fun.csUpSeq] == in_upSeq);						
		}
		
		//be set in ComEdit
		//p_row: 上一層DW的資料列, 如果null, 則使用上層DW目前的選取列
		public function filter(p_row:Object=null):void{
			if (p_row == null){
				p_row = DW2(oConfig.upEditDW).getNowRow();
				
				if (p_row == null)
					return;	
			}
			
			in_upSeq = p_row[Fun.csRowSeq];
			i_ac.refresh();
		}
		
		
		override public function initVar():void{
			super.initVar();
			
			//in_nowSeq = 0;	//在 DW 設定 !!			
			ia_delete = [];
		}
		
		
		/**
		 * ?? set sub control status
		 */ 
		public function setSubStatus(p_object:Object):void{
			var tb_status:Boolean = (oConfig.updatable && oConfig.editable && is_fun != "V");
			if (p_object.enabled != tb_status){
				p_object.enabled = tb_status;
			}
		}

		
		/**
		 * get DW type, S(單筆),M(多筆),2(多筆 type2) 2011-1-4
		 */ 
		override public function getType():String{
			return (in_tp2Field == 0) ? "M" : "2";
		}

		
		/**
		 * see DW
		 */ 	
		override public function getFname(pn_item:int):String{
			if (ia_item[pn_item].fname != null){
				return " ["+ia_item[pn_item].fname+"] "
			}else{
				return " ["+(ia_item[pn_item].source as Object).headerText.replace(/:|：| |＊|　/g, "")+"] "
			}
		}

		
		/**
		 * (DW2)檢查某個欄位是否被異動
		 * @param ps_fid 欄位名稱
		 * @param pn_row 資料列序號
		 * @return true/false
		 */ 
		/*
		override public function isFieldChanged(ps_fid:Object, pn_row:int=0):Boolean{
			//var ts_fid:String = p_field[is_fidName];
			return isFidChanged(fs_fid, pn_row); 
		}
		*/
		
		/**
		 * see DW.init()
		 */ 		
		override public function setMode(ps_fun:String):void{
			//will trigger sub control render event !!
			if (ib_grid){
				var t_grid:DataGrid = oConfig.grid;
				oConfig.editable = (ps_fun != "V");
				if (oConfig.updatable){
					//t_grid.enabled = (ps_fun == "C" || ps_fun == "U");
					t_grid.dispatchEvent(new Event("render"));
				}else{
					t_grid.enabled = false;
				}
			}
		}


		/*
		 * convert field name to dataGridColumn.
		 * @param {array} field name array.
		 * @return {string} error msg if any.
		 */ 
		/* 
		protected function zz_fidsToField(pa_name:Array):String {
			if (pa_name == null){
				return "";
			}
			
			var tn_fno:int;
			for (var i:int=0;i<pa_name.length;i++){
				//tn_fno = fidToFno(pa_name[i]);
				tn_fno = i_fidToFno[pa_name[i]];				
				pa_name[i] = oConfig.grid.columns[tn_fno];
			}
			
			return "";
		}
		*/
		
		
		/**
		 * see DW
		 */ 				
		override public function getItem(p_field:Object, pn_row:int=0, pn_rowType:int=0):Object{
			return getItemByFid(p_field[is_fidName], pn_row, pn_rowType);
		}
		
		
		/**
		 * see DW
		 */ 		
		override public function getItemByFid(ps_fid:String, pn_row:int=0, pn_rowType:int=0):Object{
			var t_row:Object = getRow(pn_row, pn_rowType);
			return t_row[ps_fid];
		}


		/**
		 * see DW
		 */ 		
		override public function setItem(p_field:Object, p_value:Object, pb_flag:Boolean=false, pn_row:int=0):void{
			//update dirty flag if need
			setItemByFid(p_field[is_fidName], p_value, pb_flag, pn_row);
		}
		
		
		/**
		 * see DW
		 */ 		
		override public function setItemByFid(ps_fid:String, p_value:Object, pb_flag:Boolean=false, pn_row:int=0, pn_rowType:int=0):void{
			//update dirty flag if need
			if (pb_flag){
				setDirtyByFid(ps_fid, pn_row, pn_rowType);
			}
			
			var t_row:Object = getRow(pn_row, pn_rowType);
			t_row[ps_fid] = p_value;
			//var t_data:Object = i_ac.getItemAt(pn_row);
			//t_data[ps_fid] = p_value;
		}


		/*		
		public function setItemByFid2(ps_fid:String, p_value:Object, pb_flag:Boolean=false, pn_row:int=0):void{
			//update dirty flag if need
			if (pb_flag){
				setDirtyByFid(ps_fid, pn_row);
			}
			
			var t_data:Object = ia_row[pn_row];
			t_data[ps_fid] = p_value;
		}
		*/
		

		import spark.components.gridClasses.CellRegion;
		/**
		 * 增加一列資料, 並填上初始值.
		 * @param p_data (Optional)欄位初始值
		 * @param pb_refersh 是否更新對應的 datagrid, 如果grid包含  fn, 則必須為 false.
		 * @param pn_addPos 將新增的資料加在這個位置, 如果為-1, 則表示新增在最後面
		 * //param ?? pn_upRow up dw row, for 1-n-n.
		 * @return 新資料列的位置
		 */ 
		public function addRow(p_data:Object=null, pb_refresh:Boolean=true, pn_addPos:int=-1):int{
		//public function addRow(p_data:Object=null, pn_upRow:int=-1):void{
			
			//check first
			//如果上一層沒有選取資料, 則這一層無法新增
			var t_upDW:DW = oConfig.upEditDW;
			var tb_isRows:Boolean = t_upDW.isRows();
			var t_upGrid:DataGrid;
			if (tb_isRows){
				t_upGrid = DataGrid(t_upDW.oConfig.grid);
				if (t_upGrid != null && t_upGrid.selectedItem == null){
					Fun.msg("I", Fun.R.selectUpRow);
					return -1;
				}
			}
						
			if (fWhenFun != null){
				if (!fWhenFun({fun:"C"})){
					return -1;
				}
			}
			
			
			//oConfig.grid.dataProvider.addItem({});
			//var t_data:Object = {};
			//t_data[csFun] = "C";
			//i_ac.addItem(t_data);
			in_nowSeq++;
			
			var tn_row:int;
			var t_row:Object;
			if (pn_addPos == -1){
				tn_row = i_ac.source.length;
				i_ac.addItem({});
			}else{
				tn_row = pn_addPos;
				i_ac.addItemAt({}, tn_row);
			}
			t_row = i_ac.source[tn_row];
			t_row[csFun] = "C";
			t_row[csDirty] = {};
			t_row[Fun.csRowSeq] = in_nowSeq;
			//var t_upDW:DW = oConfig.upEditDW; 
			if (tb_isRows){
				//記錄上層 DW2 目前選取記錄的 RowSeq, for filter !!
				//var t_upGrid:DataGrid = DataGrid(t_upDW.oConfig.grid);
				if (t_upGrid != null){	//如果沒有上層datagrid, 則必須自行設定 upSeq
					t_row[Fun.csUpSeq] = t_upGrid.selectedItem[Fun.csRowSeq];
					t_row[Fun.csUpFun] = t_upDW.getDirtyFun(t_upGrid.selectedIndex);
				}
			}else{
				t_row[Fun.csUpSeq] = 0;
				t_row[Fun.csUpFun] = "";				
			}
			/*
			var tn_row:int = ia_row.length;
			ia_row[tn_row] = {};			
			ia_row[tn_row][csFun] = "C"; 
			ia_row[tn_row][csDirty] = {};
			*/
			//ia_dirty[tn_row] = {};
			//ia_dirty[tn_row][ic_fun] = "C";	
			
			/*
			//keep Row for case of 1-n2-n3
			//set ia_dirty[][ic_Row] only when n2 and n3 are New
			if (oConfig.DW != null){
				var t_dw:DW2 = oConfig.DW as DW2;
				if (pn_upRow == -1){
					pn_upRow = t_dw.getRow();
				}
				var t_dirty:Object = t_dw.getDirty(pn_upRow);
				if (t_dirty[ic_fun] == "C"){
					ia_dirty[tn_row][ic_Row] = pn_upRow;						
				} 
			}
			*/
			
			//set instance
			//is_fieldStatus = "C";	//change
			
			//set field initial value by item.init
			for (var i:int=0;i<ia_item.length;i++){
				//var t_field:Object = ia_item[i].source;
				var ts_fid:String = ia_item[i].fid;
				if (ia_item[i].init != null){
					setItemByFid(ts_fid, ia_item[i].init, true, tn_row, 2); 	//set dirty flag
				}else{
					//temp replace
					setItemByFid(ts_fid, "", false, tn_row, 2);
				}
			}
			
			//set initial value by p_data
			if (p_data != null){
				//get from p_data
				for (var ts_fname:String in p_data){
					/*
					if (ts_fname == "_upRow"){
						setItemByFid(ts_fname, p_data[ts_fname], false, tn_row, 2); 	//set dirty flag
					}else if (i_fidToFno[ts_fname] != null){
					*/
					//全部欄位都寫入, 但是只有要更新的欄位才會設定 dirty flag=true
					if (i_fidToFno[ts_fname] != null && p_data[ts_fname] != ""){	//不是空白的初始才寫入 !! 2011-1-3
						setItemByFid(ts_fname, p_data[ts_fname], true, tn_row, 2); 	//set dirty flag
					}else{
						setItemByFid(ts_fname, p_data[ts_fname], false, tn_row, 2); 	//set dirty flag						
					}
				}				
			}
			
			//select row
			if (ib_grid){
				//DataGrid(oConfig.grid).selectedIndex = tn_row;
				var t_grid:DataGrid = oConfig.grid as DataGrid; 
				t_grid.setSelectedIndex(tn_row);
				
				//2011-12-15, trigger selectionChange event, for filter function !! 
				t_grid.dispatchEvent(new GridSelectionEvent(GridSelectionEvent.SELECTION_CHANGE, true, false, null, new CellRegion(tn_row)));				
			}
			if (in_tp2Field > 0){
				tp2SelectRow(tn_row);
				tp2SetMode(true);
			}
			
			if (fAfterFun != null){
				fAfterFun({fun:"C"});
			}
			if (ib_grid && pb_refresh){
				Fun.refreshGrid(oConfig.grid);	//有 filter function 時, 如果只更新一筆資料會發生錯誤 (2011-12-15)!!
				//Fun.refreshGrid(oConfig.grid, tn_row);
				//ArrayCollection(oConfig.grid.dataProvider).itemUpdated(p_data);
			}
			
			return tn_row;
		}	


		/**
		 * 刪除一筆資料.
		 * @param pn_row 資料列序號, 如果為-1, 表示目前資料列 
		 * @param pb_msg 是否顯示訊息.
		 */ 		
		public function deleteRow(pn_row:int=-1, pb_msg:Boolean=true):void{
			//var tb_byUser:Boolean;
			if (pn_row == -1){
				//tb_byUser = true;
				//pn_row = oConfig.grid.selectedIndex;
				if (oConfig.grid.selectedIndex < 0){
					if (pb_msg){
						Fun.msg("E", Fun.R.selectDelete);
					}
				}else{
					if (fWhenFun != null){
						if (!fWhenFun({fun:"D"})){
							return ;
						}
					}
					
					Fun.ans(Fun.R.sureDeleteRow, 1, deleteRow2); 
				}				
			}else{
				deleteRow2(pn_row, false);
				//tb_byUser = false;
			}
		}		

		
		//return ia_delete
		public function getDeleted():Array{
			return (ia_delete == null || ia_delete.length == 0) ? null : ia_delete;
		}
		
		
		//callback function of deleteRow()
		private function deleteRow2(pn_row:int=-1, pb_refresh:Boolean=true):void{
			var t_grid:DataGrid = (ib_grid) ? oConfig.grid as DataGrid : null;
			if (pn_row == -1 && ib_grid){	//case of by user
				pn_row = t_grid.selectedIndex;
			}
			
			//set instance
			ib_change = true;
			ib_userChange = true;			
			
			//save deleted row to ia_delete if need
			//var t_dirty:Object = i_ac[pn_row][ic_dirty]; 
			var ts_fun:String = i_ac[pn_row][csFun];
			if (ts_fun != null && ts_fun.substr(0,1) == "U"){
				var tn_len:int = ia_delete.length;
				ia_delete[ia_delete.length] = keysToArray("U", pn_row, 1);
			}
			 
			//delete grid
			i_ac.removeItemAt(pn_row);
			if (ib_grid)
				t_grid.dispatchEvent(new GridSelectionEvent(GridSelectionEvent.SELECTION_CHANGE, true, false, null, new CellRegion(pn_row)));				
			
			//refresh grid
			if (pb_refresh){
				if (ib_grid){
					//Fun.refreshGrid(oConfig.grid);
					Fun.refreshGrid(t_grid);
				}
				
				if (fAfterFun != null){
					fAfterFun({fun:"D"});
				}				
			}
		}


		/**
		 * see DW.
		 */ 
		override public function getUpdateInfo(ps_now:String=""):Array{
			if (!oConfig.updatable){
				return null;
			}
			
			/*
			//here !!
			//新增一筆資料 for 刪除所有 record !!
			if (tn_ary == -1 && tb_delete){
				tn_ary++;
				ta_data[tn_ary] = {
					dw: gnDW,
					//row: tn_row,
					//fRow: (t_dirty[ic_Row] != null) ? t_dirty[ic_Row] : -1,
					fun: "DA",	//delete all !!
					//ident: (ts_fun == "C") ? in_ident : -1,
					//fields: ta_field,
					keys: ta_key
				};				
			}
			*/
			
			//加上 ia_delete[]
			var i:int;
			var t_data:Object = {};
			var ta_data:Array = [];
			var tn_len:int = ia_delete.length;
			for (i=0;i<tn_len; i++){
				t_data = {
					dw: nDW,
					fun: "D",
					keys: ia_delete[i]
				}
				ta_data[i] = t_data;
			}
			
			var ta_data2:Array = super.getUpdateInfo(ps_now);
			if (ta_data2 != null){
				for (i=0;i<ta_data2.length;i++){
					ta_data[tn_len+i] = ta_data2[i]; 
					
					//here 
					//ta_data[tn_len+i].rowSeq: 0,
					//ta_data[tn_len+i].upRow: 0,
				}
			}
			
			return (ta_data.length == 0) ? null : ta_data ;			
		}
		
		
		//初始化
		public function tp2Init(pa_field:Array):void{
			ia_tp2Field = pa_field;
			in_tp2Field = pa_field.length;
			in_tp2Row = 0;
			for (var i:int=0;i<in_tp2Field;i++){
				ia_tp2Fid[i] = pa_field[i].id;
			};
			
			var tb_row:Boolean = (rowsCount() > 0);
			tp2SetMode(is_fun == "U" && tb_row);
			if (tb_row){
				//DataGrid(oConfig.grid).selectedIndex = 0;
				tp2SelectRow();
			}			
		}
		

		//設定編輯模式
		private var ib_tp2Status:Boolean = true;
		public function tp2SetMode(pb_status:Boolean):void{
			if (pb_status == ib_tp2Status){
				return;
			}
			
			ib_tp2Status = pb_status;
			var i:int;
			if (pb_status){
				var t_item:Object;
				for (i=0;i<in_tp2Field;i++){
					t_item = this.fidToItem(ia_tp2Fid[i]);
					ia_tp2Field[i].enabled = (t_item.inputType != "R");
				};				
			}else{
				for (i=0;i<in_tp2Field;i++){
					ia_tp2Field[i].enabled = false;
				};
			}
		}
		
		
		//select row
		public function tp2SelectRow(pn_row:int=-1):void{
			if (pn_row == -1){
				pn_row = DataGrid(oConfig.grid).selectedIndex;
			}
			if (pn_row < 0){
				//clear form field values
				
				return;
			}else if (in_tp2Row != pn_row){
				tp2FormToRow(in_tp2Row);
			}
			
			in_tp2Row = pn_row;
			DataGrid(oConfig.grid).setSelectedIndex(pn_row);
			tp2RowToForm(pn_row);
		}
		

		/**
		 * 將某一筆資料寫入欄位物件陣列.
		 * @param pn_row DataGrid 資料列序號.
		 * @param ?? pa_field field-fid pair array.
		 */ 
		public function tp2RowToForm(pn_row:int):void{
			//check
			if (pn_row > i_ac.length){
				return;
			}
			
			var t_value:Object;
			var t_row:Object = i_ac[pn_row];
			for (var i:int=0;i<in_tp2Field;i++){
				t_value = (t_row[ia_tp2Fid[i]] != null) ? t_row[ia_tp2Fid[i]] : "";
				Fun.setItem(ia_tp2Field[i], t_value);
			}
		}
				
		
		/**
		 * 欄位物件陣列的值寫入某一筆資料
		 * @param pn_row DataGrid 資料列序號.
		 * @param ?? pa_field field-fid pair array.
		 */ 
		public function tp2FormToRow(pn_row:int=-1):void{
			//check
			if (pn_row == -1){
				pn_row = DataGrid(oConfig.grid).selectedIndex;
			}else if (pn_row >= i_ac.length){
				return;
			}
			
			if (pn_row == -1){
				return;
			}
			
			var t_value:Object;
			var ts_fid:String;
			var t_row:Object = i_ac[pn_row];
			var tn_item:int;
			//var ts_date:String;
			for (var i:int=0;i<in_tp2Field;i++){
				t_value = Fun.getItem(ia_tp2Field[i]);
				ts_fid = ia_tp2Fid[i]; 
				if (t_value != null && t_value != t_row[ts_fid]){
					//如果是日期欄位, 則只需比較日期部分 !! (Form 必須使用 DateField1 欄位, not DateField!!)
					tn_item = ia_field[i_fidToFno[ts_fid]].itemno;
					if (ia_item[tn_item].dataType == "D"){
						if (t_value != String(t_row[ts_fid]).substr(0,t_value.length)){
							this.setItemByFid(ts_fid, t_value, true, pn_row);							
						}
					}else{
						this.setItemByFid(ts_fid, t_value, true, pn_row);
					}
				}
			}
			
			//if (ib_grid){
				//Fun.refreshGrid(oConfig.grid, pn_row);
				Fun.refreshGrid(oConfig.grid);
			//}					
		}


		/*
		//compare form and grid row, check form is changed or not. 
		public function isFormChanged(pn_row:int, pa_field:Array):Boolean{
			var t_value:Object;
			var ts_fid:String;
			var t_row:Object = ia_row[pn_row];
			for (var i:int=0;i<pa_field.length;i=i+2){
				t_value = Fun.getItem(pa_field[i]);
				ts_fid = pa_field[i+1]; 
				if (Fun.isEmpty(t_value) && t_row[ts_fid] == null){
					continue;
				}else if (t_value != t_row[ts_fid]){
					return true;
				}
			}
			
			return false;
		}
		*/
		
		
		/**
		 * 暫不使用, 每個cell會有獨立的元件 !!
		 */ 
		public function setComponent(ps_fid:String, p_comp:Object):void{
			var tn_item:int = fidToInfo(ps_fid)["itemno"];
			//if (ia_item[tn_item].component == null){
				
				ia_item[tn_item].component = p_comp;
				//if (in_t1 == 0){
					//p_comp.enabled = false;
				//}
				//in_t1++;
			//}
		}

		
		/**
		 * set this.upSeq refer to up edit dw.
		 * pn_upRow: 畫面上的 row no 
		 * pn_row: this 畫面上的 row no.
		 */ 
		public function setUpSeq(pn_upRow:int, pn_row:int):void{
			var t_upDW:DW = oConfig.upEditDW as DW; 
			i_ac[pn_row][Fun.csUpSeq] = t_upDW.isRows() ? t_upDW.getAC()[pn_upRow][Fun.csRowSeq] : 0;
		}
		
		
		/**
		 * 傳回欄位物件, overrided in DW2
		 * @param p_field field Object.
		 */ 
		override protected function getCol(p_field:Object):Object {
			//最多查5層
			var t_field:Object = p_field;			
			for (var i:int=0;i<5;i++){
				if (t_field.owner.hasOwnProperty("column")){
					return t_field.owner.column;
				}else{
					t_field = t_field.owner;
					continue;
				}
			}
		
			//case of not found
			return null;
		}
		
		
		/*
		 * set row bg color.
		 * @return {uint} color for row background.
		 */
		/* 
		public function zz_drawRowBackground(p_data:Object):uint {
			var ts_fun:String = ia_dirty[p_data.dataIndex][ic_fun]; 			
			if (ts_fun.substr(0,1) == "D"){
				return Fun.cRed;
			}else{
				return 0;
			}			
		}
		*/
		
		/*
		override public function fieldFocusOut(p_event:FocusEvent):void{
			//??
			//update dirty flag
			var t_field:Object = p_event.currentTarget;
			ia_dirty[0][t_field.id] = true;
			
			//set related field if need
			var tn_fno:int = t_field.data.fno;
			var t_item:Object = oConfig.items[tn_fno];
			var t_relat:Object = t_item.relat;			
			if (t_relat){
				var t_data:Object = {};
				t_data[t_item.colName] = getItem(t_field);
				if (t_relat.source){
					for (var i:int=0;i<t_relat.source.length;i++){
						var t_field2:Object = t_relat.source[i];
						var tn_dw2:int = t_field2.data.dw;
						var tn_fno2:int = t_field2.data.fno;
						var ts_colName:String = oConfig.items[tn_fno2].colName;
						t_data[ts_colName] = getItem(t_field2);
					}
				}
				var ts_name:String = Fun2.getName(t_relat.type,t_data);
				if (!ts_name){
					ts_name = "";
					t_item.error = "資料輸入錯誤 !";
				}
				setItem(t_relat.dest, ts_name, true);
			}
		}
		*/
		
	}//class
}//x2