package flow{
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	

	/**
	 * LineNode 是 Line 上面的節點, 分布在 Line 的兩端和中間.
	 * 兩端的節點可以用來連結程序(各種 Process), 中間的節點可以改變 Line 的路徑.
	 * 使用者可以拖曳任一節點.
	 */  
	public class LineNode extends Node
	{
		
		/**
		 * Line 的起始位置.
		 */ 	
		public static const csStart:String = "S";
		
		/**
		 * Line 的結束位置.
		 */ 
		public static const csEnd:String = "E";
		
		/**
		 * Line 的中間位置.
		 */ 
		public static const csMid:String = "M";
		
		
		private var ib_down:Boolean=false;	//mouse down or not
		private var ib_move:Boolean=false;	//begin move or not.
		private var is_type:String;			//LineNode 的位置: csStart, csEnd, csMid
		private var i_area:WorkArea;		//parent working area object		
		private var i_line:Line;			//擁有這個 LineNode 的 Line 物件.
		private var i_procNode:ProcNode = null;	//目前連結的 ProcNode 物件, 只適用於 is_type = csStart/csEnd				
		//private var i_rect:Rectangle;
		

		/**
		 * Constructor.
		 * @param p_lines 擁有這個 Node 的 Line.
		 * @param p_point 相對於 Line 的 xy 座標.
		 * @param ps_type 節點的位置種類
		 */  		
		public function LineNode(p_line:Line, p_point:Point, ps_type:String){
		//public function LineNode(p_point:Point, ps_type:String){
			super(p_point);
			
			i_area = FlowFun.gArea;
			i_line = p_line;
			is_type = ps_type
			
			//this.buttonMode = true;
			//this.useHandCursor = true;
            
            //default to green for un-connected
            setColor(Node.cnGreen);

			//i_line.setChildIndex(this, 0);
			
			//add event listener             
			this.addEventListener(MouseEvent.MOUSE_DOWN, onMouseDown);
			//this.addEventListener(MouseEvent.MOUSE_MOVE, onMouseMove);
			//this.addEventListener(MouseEvent.MOUSE_UP, onMouseUp);
			this.addEventListener(MouseEvent.ROLL_OVER, i_area.setDragCursor);	//select or star drag
			this.addEventListener(MouseEvent.ROLL_OUT, i_area.noDragCursor);		//select or star drag					
			
		}
		
		
		//drag node
		private function onMouseDown(p_event:MouseEvent):void{
			//set instance
			ib_down = true;
			
			/*
			//設定變數 for MouseMove event.
			i_area.gnLineNodeX = p_event.stageX - this.x;
			i_area.gnLineNodeY = p_event.stageY - this.y;
			i_area.gnStageX = p_event.stageX - i_line.x - i_area.x;
			i_area.gnStageY = p_event.stageY - i_line.y - i_area.y;
			i_area.gnStageX2 = i_area.gnStageX + i_area.width; 
			i_area.gnStageY2 = i_area.gnStageY + i_area.height; 
			*/
			
			//var t_point:Point = new Point(this.x, this.y); 
			if (!i_line.setNodeNo(new Point(this.x, this.y))){
				trace("not found LineNode!");
			}
			
			if (is_type == csStart || is_type == csEnd){
				i_area.initDrag(WorkArea.csNode, this, i_line);
			}
			//i_line.initDrag(p_event);
			
			//如果攔截 LineNode 的 MouseMove event, 拖曳經過其他物件上面時會失效 , 
			//而且有嚴重的延遲現象, 所以在這裡改攔截 stage(not WorkArea) 的 MouseMove event !!
			//如果是攔截 WorkArea, 則拖曳超過 WorkArea 範圍時, 系統不會觸動 LineNode MouseMove event, 
			//造成 point 和 node 不一致, 必須再寫 code 調整 , 比較麻煩!!
			//i_area.addEventListener(MouseEvent.MOUSE_MOVE, onMouseMove);
			stage.addEventListener(MouseEvent.MOUSE_MOVE, onMouseMove);
			
			//攔截整個 stage 的  MouseUp event !!
			stage.addEventListener(MouseEvent.MOUSE_UP, onMouseUp);
			
			/*
			//check
			if (FlowFun.goSelectItem == i_line){
				//FlowFun.startDrag(p_event, WorkArea.csNode, null, this);
			}
			*/
			
			/*
			ib_select = true;
			
			var t_point:Point = new Point(this.x, this.y);
			i_line.setNodeNo(t_point);
						
			if (is_type != "M"){			
				FlowFun.gDragLineNode = this;
				FlowFun.gbDragStart = (is_type == "S");
			}
			this.startDrag(false);
			*/
		}	

		
		//private var in_int:int = 0;
		private function onMouseMove(p_event:MouseEvent):void{
			//in_int++;
			//trace("Line Node moving " + in_int);
			if (!ib_down) {
				return;
			}
			/*
			if (!ib_move){
				ib_move = true;
				//i_rect = new Rectangle(-i_line.x, -i_line.y, i_area.width, i_area.height);
				i_rect = new Rectangle(-i_line.x, -i_line.y, i_area.measuredWidth, i_area.measuredHeight);
			}
			*/
			
			//LineNode 移動的範圍為整個 WorkArea, 不是 stage !!
			ib_move = true;
			var t_rect:Rectangle = new Rectangle(-i_line.x, -i_line.y, i_area.measuredWidth, i_area.measuredHeight);
			this.startDrag(false, t_rect);		//可改善延遲的現象

			//move node
			i_line.moveNowNode();
		}
		
		//case 1: move node, will stop dragging
		//case 2: drop to flow, will adjust node position, triggered by flow object !!
		private function onMouseUp(p_event:MouseEvent):void{
			
			if (!ib_down){
				return;
			}
			
			//trace("LineNode mouse down");
			ib_down = false;
			this.stopDrag();
			//i_line.moveNowNode();	//如果拖曳超過 WorkArea 範圍時, 系統不會觸動 LineNode MouseMove event, 造成 point 和 node 不一致, 所以手動調整 !!
			i_line.setNodeNo(null);
			stage.removeEventListener(MouseEvent.MOUSE_UP, onMouseUp);
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, onMouseMove);
			//i_area.removeEventListener(MouseEvent.MOUSE_MOVE, onMouseMove);
			
			
			//connect to or release related ProcNode		
			if (ib_move){
				ib_move = false;
				
				if (i_area.xDropProcNode != null){						
					connectProcNode(i_area.xDropProcNode);
					Process(i_area.xDropProcNode.parent).select(false);		//建立連結後, 取消選取 process
				}else if (i_procNode != null){
					breakProcNode(true);
				}
				
				if (is_type == csMid && i_area.fChangeLineNodePos != null){
					i_area.fChangeLineNodePos(Line(this.parent), null);
				}
			}
						
			i_area.noDrag();								    		
		}
		
		
		/**
		 * 傳回節點位置: csStart, csEnd, csMid.
		 */ 
		public function getType():String{
			return is_type;	
		}
		
		
		//connect(and move to) or release ProcNode
		//called by: 1.mouseUp, 2.outside app
		/**
		 * 連結到某個 ProcNode.
		 * @param p_node ProcNode
		 */ 		
		public function connectProcNode(p_procNode:ProcNode):void{

			i_procNode = p_procNode;
			i_procNode.connectLineNode(this);	//同時連結 ProcNode 到 LineNode
			
			//set color to red
			setColor(Node.cnRed);	

			//move to process node
			i_line.moveToProcNode(is_type, p_procNode);
			/*
			if (i_area.fLineNodeToProcNode != null){
				i_area.fLineNodeToProcNode(this, p_procNode, null);
			}
			*/
			
			//trigger function last !!
			if (i_area.fChangeLineNodeConnect != null){
				i_area.fChangeLineNodeConnect(this, null);
			}				
			
			/*				
			//update node info 
			var t_proc:Process = p_procNode.parent as Process;
			i_line.setNodeInfo(is_type, t_proc.getSeq(), p_procNode.getNodePos());	
			*/
			
			/*
			//connect
			if (pb_nest){
				i_procNode.connectLineNode(this);
			}
			*/							
		}
		
		
		/**
		 * 取消連結這個節點上的 ProcNode. LineNode 可以連結 ProcNode, 連結或中斷時會影響彼此的關係
		 * 為避免程式碼重覆執行, 所以將連結或中斷的部分寫在 LineNode, ProcNode 只處理自己的部分 !!
		 * 在 ProcNode 發生連結或中斷時, 呼叫 LineNode 的程式來處理
		 * @param pb_byThis true(called by Process), false(called by LineNode)
		 */ 
		public function breakProcNode(pb_byThis:Boolean):void{
			if (i_procNode == null){
				return;
			}
							
			//trigger function first
			if (i_area.fChangeLineNodeConnect != null){
				i_area.fChangeLineNodeConnect(this, null);
			}
			
			//set color to green
			setColor(Node.cnGreen);	

			if (pb_byThis){
				i_procNode.breakLineNode(this, false);
			}
						
			//set null at last
			i_procNode = null;
		}
		
		
		public function getProcNode():ProcNode{
			return i_procNode;
		}
		
		
		//傳回對應的 Process 的 {seq, nodeNo}, 如果無, 則傳回  {-1, -1}
		public function getProcInfo():Object{
			//var t_procNode:ProcNode = getProcNode();
			if (i_procNode == null){
				return {seq:-1, nodeNo:-1};
			}else{			
				return {
					seq: Process(i_procNode.parent).getSeq(),
					nodeNo: i_procNode.getNodePos()
				};
			}
		}

		
		/*
		private function zz_connectProcNode2(pb_status:Boolean, p_node:ProcNode=null, pb_nest:Boolean=true, pb_move:Boolean=true):void{
			if (pb_status){
				//release first if need
				if (i_procNode != null){
					connectProcNode(false, p_node, pb_nest);	
				}
				
				//move to process node if need
				if (pb_move){
					i_line.moveToProcNode(is_type, p_node);
					
					//update node info 
					var t_proc:Process = p_node.parent as Process;
					//i_line.setNodeInfo(is_type, t_proc.getSeq(), p_node.getNodePos());	
				}
				
				//connect
				setColor(Node.cnRed);	
				i_procNode = p_node;
				if (pb_nest){
					i_procNode.connectLineNode(this);
				}				
			}else{
				if (i_procNode != null){
					if (pb_nest){
						i_procNode.releaseLineNode(this);
					}
					
					//update node info 
					//var t_proc:Process = p_node.parent as Process;
					//i_line.setNodeInfo(is_type, -1);						

					//release
					setColor(Node.cnGreen);	
					i_procNode = null;					
				}
				
				//i_line.moveToProcNode(is_type, null);				
			}			
		}	
		*/
	
	}
}