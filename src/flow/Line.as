package flow{
	import flash.display.Graphics;
	import flash.display.Shape;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	
	import mx.core.UIComponent;
	
	import spark.primitives.*;
	
	/**
	 * Line 是流程線, 用來連結 2 個程序(Process, AutoProc, EndProc)
	 */ 
	public class Line extends UIComponent{
	//public class Line extends spark.primitives.Line{

		//constant		
        private const cnLen:int = 50;		//initial line length(pixel)
        private const cnArrowH:int = 8;		//arrow high
        private const cnArrowW2:int = 3;	//arrow width/2
        //private const cnLineColor:int = 0x000000;		//line color
        private const cnDegree90:Number = Math.PI/2;	//for calculate
		private const cnYesColor:int = 0x000000;	//同意的線段顏色, 預設為黑色
		private const cnNoColor:int = 0xFF0000;	//不同意的線段顏色, 預設為灰色
        		
		//用以下3個變數來控制 dragdrop !!
		protected var ib_select:Boolean=false;		//select item or not
		protected var ib_down:Boolean=false;		//mouse down or not
		protected var ib_move:Boolean=false;		//mouse move or not
		
		//private variables
		//private var ib_movable:Boolean = false;	//can move line or not		
		//private var ib_firstMove:Boolean = false;	//開啟移動時, 要中斷 procNode
		private var ib_calcRect:Boolean = true;	//是否重新計算 "實際區域", 移動節點後必須重新計算 !!
		private var ib_agree:Boolean = true;	//表示線段是否為同意 (顏色會不一樣)
		//
		private var in_baseX:int = 0;			//original x offset pos before moving
		private var in_baseY:int = 0;			//original Y offset pos before moving
		private var in_nowNode:int = -1;		//current selected node, -1 means select none node.				
		private var in_order:int = 0;			//z-order in WorkArea
		private var in_minX:int, in_minY:int;	//實際區域的 x 座標
		private var in_maxX:int, in_maxY:int;	//實際區域的 y 座標
		//
		private var i_area:WorkArea;		//parent working area object		
		private var ia_point:Array = [];	//point array, 相對於 in_x0, in_y0 的位置
		private var ia_node:Array = [];		//node array objects for selection

		//updated node info
        private const ic_init:int = -2;
		//private var ib_moveNode:Boolean;		//move node or not		
		private var in_startProc:int=ic_init;	//for update db, -1 means not changed !!				
		private var in_startProcNode:int=ic_init;	
		private var in_endProc:int=ic_init;						
		private var in_endProcNode:int=ic_init;						
		//private var i_rect:Rectangle;			//實際區域 for moving
		//
		protected var i_text:TextField;			//顯示文字		
		private var in_textPos:int = 1;
		
		
		/**
		 * @param p_data 輸入參數, 包含x,y,points
		 * @param pb_select 是否選取這個物件. 
		 */ 
		public function Line(p_data:Object, pb_select:Boolean=false){
			super();

			//temp add
			//this.setStyle("focusThickness",2);
			
			//set instance
			i_area = FlowFun.gArea;
			if (p_data.isAgree != null){
				ib_agree = (p_data.isAgree == 1);
			}
			/*
			if (p_data.yesColor != null){
				in_yesColor = p_data.yesColor;
			}
			if (p_data.noColor != null){
				in_noColor = p_data.noColor;
			}
			*/
			//ib_moveNode = pb_select;
			//ibSelect = true;
			
			//initial point array
			var ts_pos:String;
			var ta_point:Array = p_data.points;
			var tn_points:int = ta_point.length;
			//var j:int = 0;
			for (var i:int=0;i<tn_points;i++){
				
				if (i == 0){
					ts_pos = LineNode.csStart;	//start
				}else if(i == tn_points - 1){
					ts_pos = LineNode.csEnd;	//end
				//}else if (i==1 || i==tn_points-2){
				//	continue;
				}else{
					ts_pos = LineNode.csMid;	//middle
				}
				
				ia_point[i] = new Point(ta_point[i].x, ta_point[i].y);
        		ia_node[i] = new LineNode(this, ia_point[i], ts_pos);
				this.addChild(ia_node[i]);
				//this.setChildIndex(ia_node[j], 0);
				ia_node[i].visible = false;
				//j++;
			}
			//iaPoint[0] = new Point(0,0);
			//iaPoint[1] = new Point(0,cnLen);
	
			
			//set property
			//this.buttonMode = true;
			//this.useHandCursor = true;
			//this.graphics.lineStyle(1, in_yesColor);
			
			//initialize text field for line name		
			i_text = new TextField();
			i_text.autoSize = TextFieldAutoSize.CENTER;	//必須加上這一行, 否則不會顯示文字 !!
			i_text.backgroundColor = i_area.nBgColor; 
			i_text.background = true;
			setText(p_data);
			moveText(p_data.textPos);
			this.addChild(i_text);
			
			//draw line			
			this.x = p_data.posX;
			this.y = p_data.posY;
			drawLine();
			
			
			//add event listener
			this.addEventListener(MouseEvent.MOUSE_DOWN, onMouseDown);
			this.addEventListener(MouseEvent.MOUSE_MOVE, onMouseMove);
			//this.addEventListener(MouseEvent.MOUSE_UP, onMouseUp);		//控制 WorkArea 區域外 MouseUp event.
			this.addEventListener(MouseEvent.ROLL_OVER, i_area.setMoveCursor);	//select or star drag
			this.addEventListener(MouseEvent.ROLL_OUT, i_area.noMoveCursor);	//select or star drag
									
			
			/*
			for (i=0;i<tn_points;i++){
				this.addChild(ia_node[i]);
				ia_node[i].visible = false;
			}
			*/
			
			//test
			//setNodeCursor(true);			
		}
		
		
		/**
		 * select line(if need) for moving
		 * no proxy !! (different from Process dragging)
		 * 移動 text(eventPhase == 3) 時同時移動 line
		 * 移動 lineNode 時不處理,(在 lineNode move event 自行處理 !!)
		 */  		
		private function onMouseDown(p_event:MouseEvent):void{
			if (p_event.eventPhase == 3){
				try {
					//如果是 textField時會 error, 所以使用 try catch !!
					if (p_event.target.className == "LineNode"){
						return;
					}
				}catch(e:Error){
					//do nothing
				}
			}
			
			//trace("mouse down");
			
			//var t_item:Object = p_event.currentTarget;	//always be Process object !!
			//trace("Line mouse down");
			//ib_move = true;
			ib_down = true;
			i_area.initDrag(WorkArea.csLine, this, this);	//will set ib_select
			//i_area.selectItem(this, WorkArea.csLine);
			
			//catch stage MouseUp Event, 控制 WorkArea 區域外 MouseUp event.
			stage.addEventListener(MouseEvent.MOUSE_UP, onMouseUp);
			//setNodeCursor(true);
			
			if (ib_calcRect){
				ib_calcRect = false;
				
				//get real x,y and rectangle if need					
				in_minX = 10000;
				in_minY = 10000;
				in_maxX = -10000, 
				in_maxY = -10000;
				for (var i:int=0;i<ia_point.length;i++){
					if (in_minX > ia_point[i].x)
						in_minX = ia_point[i].x;
					if (in_minY > ia_point[i].y)
						in_minY = ia_point[i].y;
					if (in_maxX < ia_point[i].x)
						in_maxX = ia_point[i].x;
					if (in_maxY < ia_point[i].y)
						in_maxY = ia_point[i].y;
				}				
			}			
		}	
		
		
		//private var in_int:int = 0;
		//設定整個線段可移動的範圍
		private function onMouseMove(p_event:MouseEvent):void{
			if (!ib_down){
				return;
			}
			
			//trace("mouse move");
			//release all lineNodes connection at first time.
			if (!ib_move){	//開始移動
				ib_move = true;
				//i_rect = new Rectangle(Node.cnSize-in_minX, Node.cnSize-in_minY, i_area.width - (in_maxX - in_minX + Node.cnSize*2), i_area.height - (in_maxY - in_minY + Node.cnSize*2));
				//i_rect = new Rectangle(Node.cnSize-in_minX, Node.cnSize-in_minY, i_area.measuredWidth - (in_maxX - in_minX + Node.cnSize*2), i_area.measuredHeight - (in_maxY - in_minY + Node.cnSize*2));				
				breakProcNodes();
			}
			
			//這段程式碼放在 ib_firstMove 外面, 可以減少延遲的現象 !!
			var t_rect:Rectangle = new Rectangle(Node.cnSize-in_minX, Node.cnSize-in_minY, i_area.measuredWidth - (in_maxX - in_minX + Node.cnSize*2), i_area.measuredHeight - (in_maxY - in_minY + Node.cnSize*2));				
			this.startDrag(false, t_rect);			
		}
		
		
		//stop moving line or lineNode
		private function onMouseUp(p_event:MouseEvent):void{
			if (!ib_down){
				return;
			}
			
			ib_down = false;
			this.stopDrag();		
			stage.removeEventListener(MouseEvent.MOUSE_UP, onMouseUp);
			
			//trace("mouse up");
			//check
			if (ib_move){
				ib_move = false;
				//ib_firstMove = false;
				
				if (i_area.fMoveLine != null){
					i_area.fMoveLine(this, null);
				}
			}
			
			i_area.noDrag();			
			
			/*
			if (!ib_select){
				return;
			}
			
			//ib_movable = false;	
			
			//stop moving line
			if (p_event.eventPhase == 2){
				this.stopDrag();
				
				if (ib_firstMove){
					//disconnect start lineNode
					LineNode(ia_node[0]).disconnectProcNode();
					
					//disconnect end lineNode
					LineNode(ia_node[ia_node.length - 1]).disconnectProcNode();
					
					var t_event:Event = new Event("geLineMove");
					FlowFun.gArea.dispatchEvent(t_event);
					
					ib_firstMove = false;
				}
				//stop moveing lineNode	    				
			}else{
				i_area.removeEventListener(MouseEvent.MOUSE_MOVE, moveNode);
				
				if (in_nowNode >= 0){
					//ib_moveNode = true;
				}
			} 
			*/
		}
		
		
		/**
		 * 畫目前正在拖曳的線
		 * @param ps_type csStart, csEnd, csMid
		 * @param p_node 目前正在移動的 LineNode. 
		 */ 
		/*
		public function lining(ps_type:String, p_node:LineNode):void{
		}		
		*/
				
		//p_data with orderNo, cName
		public function setText(p_data:Object):void{
			if (p_data.cName == null || p_data.cName == ""){
				i_text.text = "";				
			}else{
				var ts_no:String = ((p_data.orderNo != null && p_data.orderNo != 0) ? p_data.orderNo : "") + (p_data.passType=="1" ? "" : p_data.passType) ;
				i_text.text = (ts_no != "" ? ("("+ts_no+").") : "") + p_data.cName;
			}					
		}
		
		
		//@pn_check check text pos
		public function moveText(pn_pos:int=0):void{
			//check
			if (i_text.text == ""){
				return;
			}

			if (pn_pos > 0){
				if(pn_pos >= ia_node.length){
					pn_pos = ia_node.length - 1;
				}
				in_textPos = pn_pos; 					
			}
			
			i_text.x = (ia_point[in_textPos - 1].x + ia_point[in_textPos].x)/2 + 2; 
			i_text.y = (ia_point[in_textPos - 1].y + ia_point[in_textPos].y - i_text.height)/2;
		}
		
		
		public function setOrder(pn_order:int):void{
			in_order = pn_order;
		}
		
		public function getOrder():int{
			return in_order;
		}

		/*
		public function setColor(pn_color:int):void{
			in_yesColor = pn_color;
		}
		*/
		
		//已經有 UIComponent.getRect(), 所以使用 getRange()
		/*
		public function getRange():Rectangle{
			//var t_rect:Rectangle = this.parent.getRect(this.parent);
			var t_rect:Rectangle = this.getRect(this);
			//var tn_maxX:Number=-10000, tn_maxY:Number=-10000;
			var tn_minX:Number=10000, tn_minY:Number=10000;
			for (var i:int=0;i<ia_point.length;i++){
				//if (tn_maxX < iaPoint[i].x){
				//	tn_maxX = iaPoint[i].x;
				//}
				if (tn_minX > ia_point[i].x){
					tn_minX = ia_point[i].x;
				}
				//if (tn_maxY < iaPoint[i].y){
				//	tn_maxY = iaPoint[i].y;
				//}
				if (tn_minY > ia_point[i].y){
					tn_minY = ia_point[i].y;
				}
			}				
			
			//tn_maxX -= tn_minX; 
			//tn_maxY -= tn_minY; 
					
			t_rect.x = this.x + tn_minX;		
			t_rect.y = this.y + tn_minY;		
			//t_rect.x = - tn_minX;		
			//t_rect.y = - tn_minY;		
			//t_rect.width -= tn_maxX;
			//t_rect.height -= tn_maxY;	
			//this.width = t_rect.width;
			//this.height = t_rect.height;
			return t_rect;	
		}
		*/
		
		/*
		public function getDragRange():Rectangle{
			var t_rect:Rectangle = new Rectangle(Node.cnSize-in_minX, Node.cnSize-in_minY, i_area.width - (in_maxX - in_minX + Node.cnSize*2), i_area.height - (in_maxY - in_minY + Node.cnSize*2));
			return t_rect;
		}
		*/
		
		private function getColor():int{
			return (ib_agree) ? cnYesColor : cnNoColor;
		}
		
		
		/**
		 * 畫線和箭頭, 並且移動文字位置, but does not re-draw nodes
		 * called by:
		 *  1.constructor
		 *  //2.move line
		 *  3.move node
		 * @param {int} pn_agree -1(不設定), 1(同意), 0(不同意), will change color if need.
		 */ 
		public function drawLine(pn_agree:int=-1):void{
			
			if (pn_agree != -1){
				ib_agree = (pn_agree == 1);
			}
			
			//clear line and arrow (不會清除上面的 LineNode)
			var t_g:Graphics = this.graphics;
			t_g.clear();		
			//if (i_arrow != null){	
			//	this.removeChild(i_arrow);
			//}
			
			
			//draw line
			drawLine2(true);	//畫實線
			drawLine2(false);	//畫背景線, 增加拖曳的區域 !!

			moveText();
			/*
			//移動文字位
			if (i_text.text != ""){
				i_text.x = (ia_point[0].x + ia_point[1].x)/2 + 2; 
				i_text.y = (ia_point[0].y + ia_point[1].y - i_text.height)/2; 
			}
			*/
			
            //=== draw arrow begin ===            
            //get points
			var tn_end:int = ia_point.length - 1;
            var tn_x:Number = ia_point[tn_end].x;
            var tn_y:Number = ia_point[tn_end].y;
			var tn_dist:Number = Point.distance(ia_point[tn_end], ia_point[tn_end - 1]);
			var tn_angle:Number = cnDegree90 + Math.atan2(ia_point[tn_end].y - ia_point[tn_end - 1].y, ia_point[tn_end].x - ia_point[tn_end - 1].x);
            var t_point:Point = Point.interpolate(ia_point[tn_end - 1], ia_point[tn_end], cnArrowH/tn_dist);
            t_point.x -= tn_x;
            t_point.y -= tn_y;
 			var t_off:Point = Point.polar(cnArrowW2, tn_angle);
 			var t_point2:Point = new Point(t_point.x + t_off.x, t_point.y + t_off.y);
 			var t_point3:Point = new Point(t_point.x - t_off.x, t_point.y - t_off.y);			
			
			/*
            i_arrow = new Shape();
            i_arrow.graphics.beginFill(0);
			i_arrow.graphics.lineTo(t_point2.x, t_point2.y);
			i_arrow.graphics.lineTo(t_point3.x, t_point3.y);
			i_arrow.graphics.lineTo(0, 0);
           	i_arrow.graphics.endFill();
           	
           	i_arrow.x = iaPoint[tn_end].x;
           	i_arrow.y = iaPoint[tn_end].y;
			
			this.addChild(i_arrow);
			*/
			var tn_color:int = getColor();
			t_g.lineStyle(1, tn_color, 1, true);
			t_g.moveTo(tn_x, tn_y);
            t_g.beginFill(0);
			t_g.lineTo(tn_x+t_point2.x, tn_y+t_point2.y);
			t_g.lineTo(tn_x+t_point3.x, tn_y+t_point3.y);
			t_g.lineTo(tn_x, tn_y);
			//t_g.lineTo(0, 0);
           	t_g.endFill();
			//=== end ===			
		}
		
			
		/**
		 * 畫所有的線段.
		 * @param pb_front true(畫實線), false(畫背景的透明線)
		 * @param pb_solid true(實線)/false(虛線)
		 */ 
		private function drawLine2(pb_front:Boolean, pb_solid:Boolean=true):void{
			//draw line
			var t_g:Graphics = this.graphics;
			var tn_color:int = getColor();
			if (pb_front){
				t_g.lineStyle(1, tn_color, 0.8);			
			}else{
				t_g.lineStyle(10, tn_color, 0);				
			}
			
			var tn_end:int = ia_point.length - 1;
			t_g.moveTo(ia_point[0].x, ia_point[0].y);
            for (var i:int=0; i<tn_end; i++){
				if (pb_solid)
					t_g.lineTo(ia_point[i+1].x, ia_point[i+1].y);
				else
					dashLine(t_g, ia_point[i], ia_point[i+1]);
            }
		}
		
		
		//http://keg.cs.uvic.ca/flexdevtips/dashedlines/srcview/index.html
		private function dashLine(p_g:Graphics, p_start:Point, p_end:Point):void{
			var tn_dashLen:Number = 10;
			var tn_total:Number = Point.distance(p_start, p_end);
			// divide the distance into segments
			if (tn_total <= tn_dashLen) {
				// just draw a solid line since the dashes won't show up
				p_g.lineTo(p_end.x, p_end.y);
			} else {
				// divide the line into segments of length dashLength 
				var tn_step:Number = tn_dashLen / tn_total;
				var tb_dashOn:Boolean = false;
				var t_p:Point;
				for (var i:Number = tn_step; i <= 1; i += tn_step) {
					t_p = getLinearValue(i, p_start, p_end);
					tb_dashOn = !tb_dashOn;
					if (tb_dashOn) {
						p_g.lineTo(t_p.x, t_p.y);
					} else {
						p_g.moveTo(t_p.x, t_p.y);
					}
				}
				
				// finish the line if necessary
				tb_dashOn = !tb_dashOn;
				if (tb_dashOn && !p_end.equals(t_p)) {
					p_g.lineTo(p_end.x, p_end.y);
				}
			}		
		}
		
		//http://keg.cs.uvic.ca/flexdevtips/dashedlines/srcview/index.html
		private function getLinearValue(t:Number, start:Point, end:Point):Point {
			t = Math.max(Math.min(t, 1), 0);
			var x:Number = start.x + (t * (end.x - start.x));
			var y:Number = start.y + (t * (end.y - start.y));
			return new Point(x, y);    
		}
		
		
		public function getNodeList():String{
			var ts_list:String = "";
			for (var i:int=0; i<ia_point.length; i++){
				ts_list += ia_point[i].x + "," + ia_point[i].y + ",";
			}
			return ts_list.substr(0, ts_list.length - 1);			
		}

		/**
		 * 傳回 Line 的內容 
		 * @return 傳回 Object, 欄位與資料表Line相同
		 *  (x, y, startProc, endProc, startProcNode, endProcNode, nodeList) //points(Point Array).
		 */ 
		/*
		public function getInfo():Object{
			var t_start:Object = lineNodeNoToProcInfo(0);
			var t_end:Object = lineNodeNoToProcInfo(ia_node.length - 1);
			return {
				x: this.x,
				y: this.y,
				//points: ia_point
				startProc: t_start.seq,
				startProcNode: t_start.nodeNo,
				endProc: t_end.seq,
				endProcNode: t_end.nodeNo,
				nodeList: getNodeList()
			};
			//return t_data;
		}
		*/
		
		/*
		//傳回某個 LineNode 所對應的 Process 的 {seq, nodeNo}, 如果無, 則傳回  {-1, -1}
		private function lineNodeToProcInfo(p_lineNode:LineNode):Object{
 			var t_procNode:ProcNode = p_lineNode.getProcNode();
			if (t_procNode == null){
				return {seq:-1, nodeNo:-1};
			}else{			
				return {
					seq: Process(t_procNode.parent).getSeq(),
					nodeNo: t_procNode.getNodePos()
				};
			}
		}
		*/
		
		/**
		 * 傳回 LineNode 物件
		 * @param pn_node LineNode 序號, 從 0 開始, 如果空白表示傳回最後一個節點.
		 */ 
		public function getNode(pn_node:int=-1):LineNode{
			return (pn_node != -1) ? ia_node[pn_node] : ia_node[ia_node.length - 1];
		}


		/**
		 * 傳回所有 LineNodes. 
		 */ 
		public function getNodes():Array{
			return ia_node;
		}


		/**
		 * get updated node info for update database
		 * @return {object} node list,startProc,startProcNode,endProc,endProcNode
		 */ 
		//public function getNodeList():String{
		/*
		public function getNodeInfo():Object{
			var ts_list:String = "";
			if (ib_moveNode){
				var ts_sep:String = "";
				for (var i:int=0;i<iaPoint.length;i++){
					ts_list += ts_sep+iaPoint[i].x+","+iaPoint[i].y;
					ts_sep = ",";
				}
			}
			
			//return ts_list;
			var t_data:Object = {
				nodeList: ts_list,
				startProc: (in_startProc == ic_init) ? null : in_startProc,
				startProcNode: (in_startProcNode == ic_init) ? null : in_startProcNode,
				endProc: (in_endProc == ic_init) ? null : in_endProc,
				endProcNode: (in_endProcNode == ic_init) ? null : in_endProcNode 
			};
			
			return t_data;
		}
		*/
		
		
		/** set node info, 
		 * set ib_moveNode
		 * called by LineNode.moveToProcNode()
		 * @param {string} ps_type S(start)/E(end)
		 * @parem {int} pn_seq process seq
		 * @parem {int} pn_node node seq 0-7
		 */
		/*
		public function zz_setNodeInfo(ps_pos:String, pn_seq:int=-1, pn_node:int=0):void{
			if (ps_pos == LineNode.csStart){	//start point
				in_startProc = pn_seq;
				in_startProcNode = pn_node;
			}else{
				in_endProc = pn_seq;
				in_endProcNode = pn_node; 				
			}
		}
		*/
		
		//?? find node with point, called by lineNode
		public function setNodeNo(p_point:Point):Boolean{
			if (p_point != null){
	            for (var i:int=0; i<ia_point.length; i++){
	            	if (ia_point[i].x == p_point.x && ia_point[i].y == p_point.y){
	            		in_nowNode = i;
	            		return true;
	            	}
	            }
			}

			//case else
            in_nowNode = -1;
			if (p_point != null){            	
				return false;	
			}else{
				return true;
			}
		}

		/*
		//called by LineNode mouseDown event
		private var in_mouseX:int, in_mouseY:int;
		public function initDrag(p_event:MouseEvent):void{
			in_mouseX = p_event.stageX - ia_point[in_nowNode].x;
			in_mouseY = p_event.stageY - ia_point[in_nowNode].y;			
		}
		*/
		
		
		/**
		 * 取消連結起訖節點上的 ProcNode.
		 */ 
		public function breakProcNodes():void{
			LineNode(ia_node[0]).breakProcNode(true);
			LineNode(ia_node[ia_node.length - 1]).breakProcNode(true);
		}
		
		
		//called by LineNode MouseMove event. 
		//public function moveNowNode(p_event:MouseEvent):void{
		public function moveNowNode():void{
			//new position
			//p_event.
		    //ia_point[in_nowNode].x = p_event.stageX - in_baseX;
		    //ia_point[in_nowNode].y = p_event.stageY - in_baseY;
		    
			//ia_point[in_nowNode].x += p_event.target.mouseX;	// - i_area.gnMouseX; 
			//ia_point[in_nowNode].y += p_event.target.mouseY;	// - i_area.gnMouseY;
			//ia_point[in_nowNode].x = p_event.stageX - i_area.gnMouseX;
			//ia_point[in_nowNode].y = p_event.stageY - i_area.gnMouseY;
			
			/*
			//check
			var tn_stageX:int, tn_stageY:int;
			if (p_event.stageX < i_area.gnStageX){
				tn_stageX = i_area.gnStageX;
			}else if(p_event.stageX > i_area.gnStageX2){
				tn_stageX = i_area.gnStageX2;				
			}else{
				tn_stageX = p_event.stageX;				
			}
			if (p_event.stageY < i_area.gnStageY){
				tn_stageY = i_area.gnStageY;
			}else if(p_event.stageY > i_area.gnStageY2){
				tn_stageY = i_area.gnStageY2;				
			}else{
				tn_stageY = p_event.stageY;				
			}
			var tn_x:int = tn_stageX - i_area.gnLineNodeX;
			var tn_y:int = tn_stageY - i_area.gnLineNodeY;
			//var tn_x:int = p_event.stageX - i_area.gnLineNodeX;
			//var tn_y:int = p_event.stageY - i_area.gnLineNodeY;
			
			//if (tn_x < i_area.x || tn_y < i_area.y || tn_x > i_area.x + i_area.width || tn_y > i_area.y + i_area.height){
			//	return;
			//}
			//p_event.
			//temp add
			//trace(i_area.x+","+i_area.y+","+p_event.stageX+","+p_event.stageY+","+tn_x+","+tn_y);
			//trace(tn_x+","+tn_y+","+p_event.target.mouseX+","+p_event.target.mouseY);
			*/
			
			//nodeToPoint(in_nowNode, tn_x, tn_y);
			
		    //call this function, or will has delay issue
		    //p_event.updateAfterEvent();
			ia_point[in_nowNode].x = ia_node[in_nowNode].x;
			ia_point[in_nowNode].y = ia_node[in_nowNode].y;
		    
		    //redraw line
		    drawLine();
		}

		private function nodeToPoint(pn_node:int, pn_x:int, pn_y:int):void{
			ia_point[pn_node].x = pn_x;
			ia_point[pn_node].y = pn_y;
			ia_node[pn_node].x = pn_x;
			ia_node[pn_node].y = pn_y;			
		}

		/*
		//set ib_moveNode
		public function zz_setMove():void{
			ib_moveNode = true;
		}
		*/
		

		/**
		 * drop lineNode to processNode, called by lineNode.moveToProcNode() (in mouseUp event)
		 * @param {string} ps_type :B(begin node), E(end node)
		 * @param {point} p_point to point
		 * //@param {processNode} p_node processNode to drop
		 */ 
		//public function setNodePos(ps_type:String, p_point:Point):void{
		//public function updateNodeInfo(ps_type:String, pn_seq:int, pn_node:int):void{
		public function moveToProcNode(ps_pos:String, p_procNode:ProcNode):void{
			//set move flag if need
			//ib_moveNode = true;
			
			var tn_pSeq:int;
			var tn_pNode:int;
			if (p_procNode != null){
				var t_proc:Process = p_procNode.parent as Process; 
				tn_pSeq = t_proc.getSeq();
				tn_pNode = p_procNode.getNodePos();
				
				//adjust lineNode position
				var tn_node:int = (ps_pos == LineNode.csStart) ? 0 : ia_point.length - 1;
				nodeToPoint(tn_node, t_proc.x + p_procNode.x - this.x, t_proc.y + p_procNode.y - this.y);
				/*
				var tn_x:int = t_proc.x + p_procNode.x - this.x;
				var tn_y:int = t_proc.y + p_procNode.y - this.y;
			    ia_point[tn_node].x = tn_x;
			    ia_point[tn_node].y = tn_y;
			    ia_node[tn_node].x = tn_x;
			    ia_node[tn_node].y = tn_y;
				*/
			}else{
				tn_pSeq = -1;
				tn_pNode = 0;				
			}
			
			if (ps_pos == LineNode.csStart){
				in_startProc = tn_pSeq;
				in_startProcNode = tn_pNode;
			}else{
				in_endProc = tn_pSeq;
				in_endProcNode = tn_pNode;
			}
			
		    //redraw line
		    drawLine();
		    
		    //temp add
			//iaNode[tn_node].setColor("R");		    
		}

		
		//add one node to first line, called by canvas
		public function addNode():LineNode{
			if (ia_point.length >= 5){
				//msg("5 nodes maximum !");
				return null;
			}else{
				var t_point:Point = new Point((ia_point[0].x + ia_point[1].x)/2, (ia_point[0].y + ia_point[1].y)/2);
				ia_point.splice(1, 0, t_point);
				
            	var t_node:LineNode = new LineNode(this, t_point, LineNode.csMid);
				ia_node.splice(1, 0, t_node);
				var t_lineNode:LineNode = this.addChild(t_node) as LineNode; 
				drawLine();
				return t_lineNode;
				//return true;
			}
		}
		
		
		//?? add one node to first line, called by canvas
		public function deleteNode():Boolean{
			if (ia_point.length <= 2){
				//msg("2 nodes minimum !");
				return false;
			}else{
				this.removeChild(ia_node[1]);
				ia_point.splice(1, 1);
				ia_node.splice(1, 1);
				drawLine();
				return true;
			}
		}
		

		/**
		 * 選取或取消選取此物件.
		 * @param pb_status true(選取), false(取消選取).
		 */ 
		public function select(pb_select:Boolean):void{
			if (ib_select == pb_select){
				return;
			}
			
			ib_select = pb_select;
            for (var i:int=0; i<ia_node.length; i++){
            	ia_node[i].visible = pb_select;
            }
		}

		
		/*
		//return shadow for moving
		public function getShadow():Sprite{
            var t_shape:Sprite = new Sprite();
			t_shape.graphics.lineStyle(1, 0x00FF00);
            for (var i:int=0; i< iaNode.length - 1; i++){
				t_shape.graphics.moveTo(iaNode[i].x, iaNode[i].y);
				t_shape.graphics.lineTo(iaNode[i+1].x, iaNode[i+1].y);
            }
            						
            t_shape.x = this.x;
            t_shape.y = this.y;
			return t_shape;
		}
		*/
		
		
		/** 
		 * show message
		 * @param {string} ps_msg messag string.
		 */ 
		/* 
		private function msg(ps_msg:String):void {
			Alert.show(ps_msg, "Information");
		}
		*/
	}
}