package flow{
	//import flash.display.Sprite;
	import flash.display.*;
	import flash.events.*;
	import flash.geom.*;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	
	//import mx.controls.*;
	import mx.core.UIComponent;
	//import spark.core.
	//import spark.components.*;
	//import mx.managers.CursorManager;
	//import flow.FlowFun;
	

	/**
	 * Process 通常是一個資料維護畫面.
	 */ 
	public class Process extends UIComponent
	{
		//public static constant
		/**
		 * Process, 圓,黑,細線
		 */ 
		public static const csProc:String = "P";

		/**
		 * Auto Process, 方,黑,粗線
		 */ 
		public static const csAutoProc:String = "A";

		/**
		 * Start Process, 圓,綠,粗線
		 */ 
		public static const csStartProc:String = "S";
		
		/**
		 * End Process.
		 */ 
		public static const csEndProc:String = "E";
		
		//constant
		public static const cnInitWidth:int = 100;		//minimum width, also used in WorkArea !!
		public static const cnInitHeight:int = 50;		//minimum height, also used in WorkArea !!
		protected const cnRadius:int = 16;				//end proc radius
		protected const cnNodes:int = 4;				//nodes amount
        protected const cnLineColor:int = 0x0;			//line color
		protected const cnMargin:int = 1;			
		protected const cnTextMargin:int = 20;			//文字 margin			
        //
		private const cnProcColor:int = 0x0;			//process 框線顏色, 預設為黑色
		private const cnAutoColor:int = 0x0;			//auto process 框線顏色, 預設為黑色
		private const cnStartColor:int = 0x00FF00;		//start process 框線顏色, 預設為綠色
		private const cnEndColor:int = 0xFF0000;		//end process 框線裡面的顏色, 預設為紅色
		
		//用以下3個變數來控制 dragdrop !!
		protected var ib_select:Boolean=false;		//select item or not
		protected var ib_down:Boolean=false;		//mouse down or not
		protected var ib_move:Boolean=false;		//mouse move or not
		
		//instance variables
		protected var ib_chgText:Boolean=false;		//change text or not
		protected var in_colorNode:int=0;			//0(未處理), -1(未設定), 1(已設定), reset color when mouse move in for drag LineNode
		private var ib_showNodes:Boolean=false;		//目前的狀態是否顯示節點		
		//
		//protected var in_textW:Number, in_textH:Number;	
		protected var in_nowNode:int = -1;			//current drop node, -1 means none
		protected var in_seq:int = -1;				//procSeq from/to database
		protected var in_rate:Number;				//斜率, =y/x(for decide target node)
		protected var in_order:int = 0;				//z-order in WorkArea				
		protected var is_type:String;				//csProc, csAutoProc, csEndProc
		protected var ia_node:Array;				//4 Node objects to be dropped
		protected var i_area:WorkArea;				//parent canvas object		
		protected var i_text:TextField;				//TextInput did not work !!
		//private var i_rect:Rectangle;				//實際區域 for moving
		//protected var i_cursor:int;				//cursor id
		
		
		/** 
		 * public constructor(Process and End-Process)
		 * @param {string} ps_type P(process),E(end node), A(auto node)
		 * @param {workarea} p_area wroking area
		 * @param {object} p_data process property. 
		 */ 
		//public function Process(ps_type:String, p_area:WorkArea, p_data:Object, pn_nodes:int=8){			 			 			
		public function Process(ps_type:String, p_data:Object){			 			 			
		//public function Process(ps_type:String, p_data:Object){			 			 			
			//super();			
			super();
			
			//set instance variables and property 
			i_area = FlowFun.gArea;
			is_type = ps_type;
			in_seq = p_data.procSeq;
			//in_nodes = pn_nodes;
 			//
			//this.buttonMode = true;
			//this.useHandCursor = true;
			if (p_data.fWidth == null){		//表示建立一個新的程序
				if (ps_type == "E"){
					var tn_size:int = cnRadius * 2;
					p_data.procName = in_seq;		//因為 wfProcess index seq_name(procSeq+procName) 為不可重複, 所以在這裡設定 procType="E" 的 procName 唯一值 !!
					p_data.fWidth = tn_size;
					p_data.fHeight = tn_size;					
				}else{
					p_data.fWidth = cnInitWidth;
					p_data.fHeight = cnInitHeight;
				}
			}
			this.x = p_data.posX;
			this.y = p_data.posY;
			this.width = (p_data.fWidth % 2 == 0) ? p_data.fWidth : p_data.fWidth+1;
			this.height = (p_data.fHeight % 2 == 0) ? p_data.fHeight : p_data.fHeight+1;
			            			
			draw();
            						
			//initialize text field for flow name		
			i_text = new TextField();
            i_text.autoSize = TextFieldAutoSize.CENTER;	//必須加上這一行, 否則不會顯示文字 !!
			//i_text.y = int((this.height - i_text.height)/2);
			//i_text.border = true;
			if (ps_type == "E"){
				i_text.visible = false;	//不顯示, 但是名稱存在!!
			}
			this.addChild(i_text);
			//setText(((ps_type != "E") ? p_data.procName : ""), false);
			setText(p_data.procName, false);
            			
            			
			//add event listener
			this.addEventListener(MouseEvent.MOUSE_DOWN, onMouseDown);		//select or star drag
			//this.addEventListener(MouseEvent.MOUSE_UP, onMouseUp);		//stop drag and connect line node if need
			this.addEventListener(MouseEvent.MOUSE_MOVE, onMouseMove);		//ensure moving boundary
			this.addEventListener(MouseEvent.MOUSE_OUT, onMouseOut);		//un-high light
			this.addEventListener(MouseEvent.ROLL_OVER, i_area.setMoveCursor);	//change cursor
			this.addEventListener(MouseEvent.ROLL_OUT, i_area.noMoveCursor);	//change cursor			
						
			//drawBound(p_data);
		}
			

		//設定節點, 畫框線, 計算斜率
		protected function draw():void{
			var tn_size:int = Node.cnSize+cnMargin;
			var tn_w2:Number = this.width/2;
			var tn_h2:Number = this.height/2;
			//var tn_off:int = 1;
			if (ia_node == null){
				//create nodes
				ia_node = new Array(cnNodes);
				ia_node[0] = new ProcNode(this, 0, new Point(tn_w2, tn_size));
				ia_node[1] = new ProcNode(this, 1, new Point(tn_size, tn_h2));
				ia_node[2] = new ProcNode(this, 2, new Point(this.width - tn_size, tn_h2));
				ia_node[3] = new ProcNode(this, 3, new Point(tn_w2, this.height - tn_size));
				
				//set not visible
				for (var i:int=0;i<cnNodes;i++){
					this.addChild(ia_node[i]);
					ia_node[i].visible = false;
				}       	
			}else{
				ia_node[0].x = tn_w2; ia_node[0].y = tn_size;
				ia_node[1].x = tn_size; ia_node[1].y = tn_h2;
				ia_node[2].x = this.width - tn_size; ia_node[2].y = tn_h2;
				ia_node[3].x = tn_w2; ia_node[3].y = this.height - tn_size;			
			}
			
			drawBound();
			
			//計算斜率 in_rate
			in_rate = ia_node[1].y/ia_node[0].x;			
		}

		
		/**
		 * overrided in child (end proc), 畫框線
		 * @param ps_type process 種類: A(auto)/P(process)
		 */ 
		public function drawBound(ps_type:String=""):void{
			if (ps_type != ""){
				is_type = ps_type;
			}
			
			var tn_size:int = Node.cnSize + cnMargin;
			var tn_w:Number = this.width - tn_size * 2;
			var tn_h:Number = this.height - tn_size * 2;
			var t_g:Graphics = this.graphics;
			t_g.clear();		
			switch (is_type){
				case "P":	//process
					t_g.lineStyle(1, cnProcColor, 1, true);
					t_g.beginFill(i_area.nBgColor, 1);
					t_g.drawRoundRect(tn_size, tn_size, tn_w, tn_h, 10);
					break;
				case "A":	//auto
					t_g.lineStyle(2, cnAutoColor, 1, true);
					t_g.beginFill(i_area.nBgColor, 1);
					t_g.drawRect(tn_size, tn_size, tn_w, tn_h);
					break;
				case "S":	//start process
					t_g.lineStyle(2, cnStartColor, 1, true);
					t_g.beginFill(i_area.nBgColor, 1);
					t_g.drawRoundRect(tn_size, tn_size, tn_w, tn_h, 10);
					break;
				case "E":	//end proc
					//var tn_color:int = 0XFF0000;
					//var tn_radius:int = 16;
					t_g.lineStyle(1, 0x0, 1, false);
					t_g.beginFill(cnEndColor, 0.3);
					//t_g.drawCircle(cnRadius, cnRadius, cnRadius - Node.cnSize);	//左上角為 (0,0), 與 Process相同 !!
					t_g.drawCircle(cnRadius, cnRadius, cnRadius - Node.cnSize);	//左上角為 (0,0), 與 Process相同 !!
					break;
				default:
					return;
			}
			t_g.endFill();			
		}
		
		
//		//overrided in child, 畫框線
//		//決定節點座標和畫形狀, for Process & AutoProc only.
//		protected function drawBound(ps_type:String):void{
//		//protected function init(p_area:WorkArea, ps_type:String, p_data:Object):void{
//			
//			//draw border
//			setType(is_type);
//			/*
//			var tn_size:int = Node.cnSize + cnMargin;
//			var tn_w:Number = this.width - tn_size * 2;
//			var tn_h:Number = this.height - tn_size * 2;
//			this.graphics.beginFill(i_area.nBgColor, 1);
//			if (is_type == "A"){
//				this.graphics.lineStyle(2, cnLineColor, 1, true);
//				this.graphics.drawRect(tn_size, tn_size, tn_w, tn_h);
//			}else{
//				this.graphics.lineStyle(1, cnLineColor, 1, true);
//				this.graphics.drawRoundRect(tn_size, tn_size, tn_w, tn_h, 10);				
//			}
//			this.graphics.endFill();
//			*/
//			
//			/*
//			//reset nodes, left to right, up to down
//			var i:int;
//			if (iaNode.length > 0 && iaNode[0] != null){
//	        	for (i=0;i<cnNodes;i++){
//	        		this.removeChild(iaNode[i]);
//	        	}				
//			}
//			*/
//			
//        	/*
//			//create text field
//			var tn_size:int = Node.cnSize + cnMargin;
//			var tn_w:Number = this.width - tn_size * 2;
//			var tn_hOff:int = 5;
//			i_text.x = (tn_w - i_text.width)/2;
//			i_text.y = tn_hOff;
//            i_text.doubleClickEnabled = false;
//            i_text.multiline = true;
//			//i_text.text = (isType == cnproc) ? p_data.procName : isType;
//			//i_text.addEventListener(MouseEvent.DOUBLE_CLICK, setTextStatus);	//??
//			this.addChild(i_text);
//			*/
//			
//			//計算斜率 in_rate
//			//trace(ia_node[0].x + " , " + ia_node[1].y);
//			in_rate = ia_node[1].y/ia_node[0].x;
//			//in_r2 = 1/in_r1;
//			
//			/*
//			var t_format:TextFormat = new TextFormat();
//			t_format.font = "FFF Urban Bold";
//			t_format.size = 14;
//			//t_format.color = 0x348256
//            //i_text.setTextFormat(t_format);
//			*/
//						
//			//redraw
//			//render();
//		}
		
		
		/**
		 * select object and begin dragging
		 * Process, nodes, textField will trigger this event.
		 */ 
		private function onMouseDown(p_event:MouseEvent):void{
			
			ib_down = true;
			i_area.initDrag(WorkArea.csProc, this, this);	//will set ib_select
			
			//catch stage MouseUp Event, 控制 WorkArea 區域外 MouseUp event.
			stage.addEventListener(MouseEvent.MOUSE_UP, onMouseUp);
			
			//i_area.selectItem(this, WorkArea.csProc);
			//ib_select = true;
		}	
		
		
		/**
		 * 1.移動 process
		 * 2.decide target dropped processNode
		 */ 
		private function onMouseMove(p_event:MouseEvent):void{
			//trace("Process mouse move: " + p_event.eventPhase);
			
			//case of moving process
			//if (p_event.buttonDown) {
			if (ib_select){
				//trace("select");				
				if (!ib_down){
					return;
				}	
				/*
				if (!ib_move){
					ib_move = true;		
					//i_rect = new Rectangle(0, 0, i_area.width - this.width, i_area.height - this.height);
					i_rect = new Rectangle(0, 0, i_area.measuredWidth - this.width, i_area.measuredHeight - this.height);
				}
				*/
				
				//持續呼叫這個函數可以減少延遲的現象 !! 如果 t_rect 使用 instance variables, 並且一次設定, 則延遲的現象會比較嚴重 !! 
				ib_move = true;		
				var t_rect:Rectangle = new Rectangle(0, 0, i_area.measuredWidth - this.width, i_area.measuredHeight - this.height);
				this.startDrag(false, t_rect);	
				
				moveLineNodes();
				return;	
			}
			
			
			//=== case of dragging LineNode below ===
			//if (p_event.eventPhase != 2 || i_area.sDragType != WorkArea.csNode){
			if (i_area.sDragType != WorkArea.csNode){
				return;
			} 
						
			//remove nodes color
			//if (ib_colorNode){
				//ib_colorNode = true;
				//colorNodes(false);	
			//}	
			
			var tn_node:int = getNodeNo(p_event);
			//trace("node: "+tn_node);
			
			/*
			var ts_str:String = in_r1+","+in_r2+","+
				p_event.localX/p_event.localY+" , "+
				p_event.localY/p_event.localX+" , "+
				p_event.localX+" , "+p_event.localY+" , "+
				tn_node; 
			trace(ts_str);
			*/
			
			if (tn_node == -1){
				//trace("-1");
				return;
			}else if (in_nowNode == -1){
				//temp remark
				//i_area.gDropProcNode = ia_node[tn_node];
				//trace("nowNode -1");
				colorNodes(false);
				showNodes(true);
			}else if (in_nowNode == tn_node){
				//trace("=");
				return;
			}
			
			//change old node color to gray if need
			//var ta_in:Array;
			if (in_nowNode >= 0){
				Node(ia_node[in_nowNode]).setColor(Node.cnNoColor);
			} 
			
			//change current node color to red if need			
			Node(ia_node[tn_node]).setColor(Node.cnRed);
			in_nowNode = tn_node;
			
			//set global
			i_area.xDropProcNode = ia_node[tn_node];
		}
		
		
		//stop moving process, lineNode mouseUp 事件發生時, 並不會觸動此事件 !!
		private function onMouseUp(p_event:MouseEvent):void{
			if (!ib_down){
				return;
			}
			
			ib_down = false;
			this.stopDrag();
			stage.removeEventListener(MouseEvent.MOUSE_UP, onMouseUp);

			if (ib_move){
				ib_move = false;
				
				if (i_area.fMoveProc != null){
					i_area.fMoveProc(this, null);
				}
			}
			
			i_area.noDrag();			
		}
		
		
		/**
		 * un-high light this object
		 * 進入或離開 text 時會觸發這個事件
		 */ 
		protected function onMouseOut(p_event:MouseEvent):void{
			//觸發 ProcNode MouseOut event 時, 也會觸發 Process MouseOut event, 
			//所以不可用 event.phase 來判斷 !!
			//if (i_area.sDragType != WorkArea.csNode || Object(p_event.relatedObject).parent == this){
			//if (i_area.sDragType != WorkArea.csNode || p_event.eventPhase == 3){
			if (i_area.sDragType != WorkArea.csNode){
				trace("process 1");
				return;
			//}else if (p_event.eventPhase == 3 p_event.relatedObject != null){	//eventPhase == 2
			}else if (p_event.eventPhase == 3){	//eventPhase == 2
				//here!!
				trace("process 2");
				return;
			}else if (p_event.relatedObject != null && p_event.relatedObject != i_area.xDragItem){
				trace("process 3");
				return;
			}
			
			trace("Process mouse out");
			/*
			trace(in_nowNode+","+p_event.localX+","+p_event.localY+","+this.width+","+this.height);
			//if (inNowNode >= 0){
			if (Object(p_event.relatedObject).parent == this){
				return;
			}
			if (p_event.localX > 0 && p_event.localX < this.width && p_event.localY > 0 && p_event.localY < this.height){
				return;
			}
			
				try {
					if (Object(p_event.relatedObject).className != "ProcNode"){
						return;
					}
				}catch (e:Error){
					return;
				}
				*/
				
			if (in_nowNode != -1){
				//trace("inNowNode="+inNowNode);				
				Node(ia_node[in_nowNode]).setColor(Node.cnNoColor);
				in_nowNode = -1;
				showNodes(false);
				
				//set global
				i_area.xDropProcNode = null;
			}
		}
		
		
		//public 
		/**
		 * 1.draw rect 
		 * 5.set text field position
		 * 3.reset nodes
		 * 2.calculate in_x?, in_y?
		 * //4.reset contect sprite
		 * called by:
		 *  1.constructor,
		 *  2.change text (will resize rect)
		 */ 
		private function zz_render():void{
			var tb_draw:Boolean = true;
			/*
			if (i_rect == null){
				tb_draw = true;
			}else{
				if(i_text.width != in_textW || i_text.height != in_textH){
					this.removeChild(i_rect);				
					tb_draw = true;
				}
			}
			*/
			 
				
			//draw rect and nodes
			if (tb_draw){
				
				//var tn_wOff:int = 10;
				/*
				var tn_w:Number = i_text.width + (tn_wOff * 2);
				var tn_h:Number = i_text.height + (tn_hOff * 2);
				if (tn_w < cnMinWidth){
					tn_w = cnMinWidth;
				}
				if (tn_h < cnMinHeight){
					tn_h = cnMinHeight;
				}
				*/
				

				//setTextStatus(null); 
			
				/*
			 	//set instance and in_x?, in_y?
				in_textW = i_text.width;
				in_textH = i_text.height;
			 	in_x1 = tn_w2/2;
			 	in_x2 = tn_w2 + in_x1;
			 	in_y1 = tn_h2/2;
			 	in_y2 = tn_h2 + in_y1;
			 	*/
			 				        	
   			}
		}
		
		
		//中斷所有節點的 lineNode
		public function breakLineNodes():void{
			for (var i:int=0; i<cnNodes; i++){
				ProcNode(ia_node[i]).breakLineNodes();
			}
		}
		
		
		public function setOrder(pn_order:int):void{
			in_order = pn_order;
		}
		
		public function getOrder():int{
			return in_order;
		}

		/*
		protected function onRollOver(p_event:MouseEvent):void{
			i_cursor = CursorManager.setCursor(FlowFun.gMove, 2, -10, -10);
		}
		protected function onRollOut(p_event:MouseEvent):void{
			CursorManager.removeCursor(i_cursor);
		}
		*/




		//=== get/set begin ===
		//get current node no when mouse move
		protected function getNodeNo(p_event:MouseEvent):int{
			var tn_x:int, tn_y:int;
			if (p_event.eventPhase == 2){
				tn_x = p_event.localX;
				tn_y = p_event.localY;
			}else{
				//tn_x = p_event.target.x + p_event.localX;
				tn_x = p_event.localX;
				tn_y = p_event.target.y + p_event.localY;				
			}
			//trace("x:"+tn_x+" y:"+tn_y);
			
			var tn_x0:int = ia_node[0].x;
			var tn_y0:int = ia_node[1].y;
			//var tn_r1:Number = tn_y/tn_x;
			var tn_rate:Number = Math.abs((tn_y-tn_y0)/(tn_x-tn_x0));	//計算目前位置到中心點的斜率 !!
			if (tn_x <= tn_x0 && tn_y <= tn_y0){
				return (tn_rate >= in_rate ? 0 : 1);
			}else if (tn_x >= tn_x0 && tn_y <= tn_y0){
				return (tn_rate >= in_rate ? 0 : 2);
			}else if (tn_x <= tn_x0 && tn_y >= tn_y0){
				return (tn_rate >= in_rate ? 3 : 1);
			}else if (tn_x >= tn_x0 && tn_y >= tn_y0){
				return (tn_rate >= in_rate ? 3 : 2);
			}else{
				return -1;
			}
			
			/*
			if (Math.abs(p_event.localX) > Math.abs(p_event.localY)){
				return (p_event.localX > 0) ? 2 : 1;
			}else{
				return (p_event.localY > 0) ? 3 : 0;
			}
			*/
		}
		/*
		protected function getNodeNo(p_event:MouseEvent):int{
			var tn_node:int;
			if (p_event.localY < in_y1){
				tn_node = 0;
			}else if (p_event.localY > in_y2){
				tn_node = 6;
			}else{
				tn_node = 3;
			}
			
			if (p_event.localX < in_x1){
				//tn_node += 0;
			}else if (p_event.localX > in_x2){
				tn_node += 2;
			}else{
				tn_node += 1;	//will do nothing !!
			}
								
			//return for center area
			if (tn_node == 4){
				return -1;		
			}
			if (tn_node > 4){
				tn_node--;
			}
			
			return tn_node;
		}
		*/
		

		//return in_seq
		//public function checkSeq(pn_seq:int):Boolean{
		public function getSeq():int{
			return in_seq;
		}
		

		//get node object
		public function getNode(pn_node:int):ProcNode{
			return ia_node[pn_node];
		}
		

		//get text
		public function getText():String{
			return i_text.text;
		}

		public function setText(ps_text:String, pb_redraw:Boolean=true):void{
			i_text.text = ps_text;

			//set position
			//var tn_size:int = Node.cnSize + cnMargin;
			//var tn_w:Number = this.width - tn_size * 2;
			//var tn_hOff:int = 5;
			//i_text.x = (tn_w - i_text.width)/2;
			//i_text.y = tn_hOff;
			
			if (pb_redraw){
				var tn_width:int = i_text.width + (cnTextMargin * 2);
				if (tn_width < cnInitWidth){
					tn_width = cnInitWidth;
				}
				if (this.width != tn_width){
					this.width = tn_width;
					draw();
					moveLineNodes();
				}
			}
			
			i_text.x = int((this.width - i_text.width)/2);
			i_text.y = int((this.height - i_text.height)/2);
			
			//i_text.doubleClickEnabled = false;
			//i_text.multiline = true;
			//i_text.text = (isType == cnproc) ? p_data.procName : isType;
			//i_text.addEventListener(MouseEvent.DOUBLE_CLICK, setTextStatus);	//??
			
		}
		

		//get data
		public function getData():Object{
			var t_data:Object = {
				procSeq: in_seq,
				procName: i_text.text,
				procType: is_type,				
				posX: this.x,
				posY: this.y,
				fWidth: this.width,
				fHeight: this.height
			};
			return t_data;
		}

		
		/*
		//set text field editable or not		
		private function setTextStatus(p_event:MouseEvent):void{
			if (p_event != null){
				ib_chgText = true; 			
			}
			i_text.background = ib_chgText;
           	i_text.border = ib_chgText;			
			i_text.type = (ib_chgText) ? TextFieldType.INPUT : TextFieldType.DYNAMIC;	//INPUT for editable, DYNAMIC for readonly
			//ib_chgText = true; 			
		}
		
		
		//set text field to readonly
		private function setTextStatus2(p_event:Event):void{
			setTextStatus(null);
		}
		*/
		//=== get/set begin ===
		
		
		public function getType():String{
			return is_type;
		}
		
		
		//show selection or not
		public function select(pb_select:Boolean):void{
			if (ib_select != pb_select){
				ib_select = pb_select;
				showNodes(pb_select);
			}
			
			//disable text field if need
			/*
			if (!pb_select){
				//trace("process select false");
				
				if (is_type == csProc){
					i_text.setSelection(-1, -1);
					//setTextStatus(null);
					//render();
					
					//trigger working area geResizeProc event if need
					if (ib_chgText){
						ib_chgText = false;
						var t_event:Event = new Event("geResizeProc");	//??
						i_area.dispatchEvent(t_event);
					}			
				}
			}
			*/
			
		}
				
		
		private function showNodes(pb_show:Boolean):void{
			//set nodes status
			if (ib_showNodes != pb_show){
				ib_showNodes = pb_show;
				
				for (var i:int=0; i<cnNodes; i++){
					ia_node[i].visible = pb_show;
				}
			}
		}
		
		
		//move all lineNodes position 
		//protected function moveLineNodes(p_event:MouseEvent):void{			
		protected function moveLineNodes():void{			
			for (var i:int=0; i<cnNodes; i++){
				var ta_lineNode:Array = ia_node[i].getLineNodes();
				for (var j:int=0; j<ta_lineNode.length; j++){
					Line(ta_lineNode[j].parent).moveToProcNode(LineNode(ta_lineNode[j]).getType(), ia_node[i]);
				}	
			}			
		}


		//set all nodes color
		//called by: 1.this, 2:lineNode mouseUp event
		public function colorNodes(pb_status:Boolean):void {
			//set instance variables
			//ib_colorNode = pb_status;
			var tn_set:int = (pb_status) ? 1 : -1;
			var i:int;
			if (in_colorNode == tn_set){
				return;
			}else if (pb_status){
				var t_node:ProcNode;
				var tn_color:int;
				for (i=0;i<cnNodes;i++){
					t_node = ia_node[i] as ProcNode;
					tn_color = (t_node.isConnect()) ? Node.cnRed : Node.cnNoColor;
					t_node.setColor(tn_color);
				}
			}else{
				for (i=0;i<cnNodes;i++){
					(ia_node[i] as ProcNode).setColor(Node.cnNoColor);
				}
			}			
		}
		

		/*
		//set in_lineNodes, called by ProcNode.connectLineNode()
		public function setLineNodes(pb_status:Boolean):void {
			if (pb_status){
				in_lineNodes++;
			}else{
				in_lineNodes--;				
			}
		}
		*/
		
		
		/*
		protected function msg(ps_msg:String):void {
			Alert.show(ps_msg,"Information");
		}
		*/
	}
}