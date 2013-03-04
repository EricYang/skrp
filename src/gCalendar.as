
	/**
	 * 如果有其他行事曆, 此程式可共用 !!
	 */

	import mx.collections.ArrayCollection;
	import mx.controls.List;
	
	import x2.*;

	private var id_now:Date;				//current edit date		
	private var is_fun:String;				//A,D,U		
	private var is_seq:String;				//for private calendar only
	
	
	//read db and write into calendar			
	private function readDB():void{
		//retrieve db and show into calendar
		var t_data:Object = {
			data: is_app,
			ym: txtYear.text+ST.preZero(2,int(txtMonth.selectedItem.data)),			
			seq: is_seq
		};
		
		var ta_date:Array = Fun.readRows(is_app, t_data);
		if (ta_date != null){			
			var tn_day:int;
			var t_cell:Object;			
			for (var i:int=0;i<ta_date.length;i++){
				tn_day = ST.toDate(ta_date[i].calDate).getDate();
				t_cell = grid_1.getCellByDay(tn_day);
				setText(t_cell, (ta_date[i].isWork == 1), ta_date[i].comment, ta_date[i].infoFlag);
			}
		}
	}

	//set cell text
	private function setText(p_cell:Object, pb_work:Boolean, ps_text:String, ps_info:int):void{
		
		var t_cell:List = p_cell.labText ;		
		var ta_row:ArrayCollection;		
		
		if (t_cell.dataProvider == null){
			ta_row = new ArrayCollection([]);
			t_cell.dataProvider = ta_row;
		}else{
			ta_row = t_cell.dataProvider as ArrayCollection;			
		}
		ta_row.addItem({label:ps_text});

	}

	private function moveMonth(pn_move:int):void{
		grid_1.moveMonth(pn_move);
		setYM();
	}	

	private function funGo():void{
		grid_1.showDate(new Date(txtYear.text, int(txtMonth.selectedItem.data) - 1, 1));
		setYM();
	}	
		
	//update db and cell, called when user save change in edit window.	
	private function updateCell(p_data:Object):void{
		var ts_comment:String = (p_data.isWork == -1) ? "" : p_data.comment;
		
		//update cell
		var t_cell:Object = grid_1.getCellByNo();
		setText(t_cell, (p_data.isWork == 1), ts_comment, 1);
		
		//check and ajust function type
		//isWork == -1 means empty !!
		if (is_fun == "C" && p_data.isWork == -1){
			return;
		}else if (is_fun == "U" && p_data.isWork == -1){
			//change to delete record.
			is_fun = "D";
		}
		
		//adjust p_data for update db
		p_data.fun = "update";
		p_data.fun2 = is_fun;
		p_data.seq = is_seq;		//private calendar only !!	
		var ts_error:String = Fun.sync(is_app, is_app+".ashx", p_data, false) as String;
		if (ts_error != ""){
			Fun.msg("E", ts_error);
		}else{
			Fun.msg("I", "資料更新完成。");				
		}
	}