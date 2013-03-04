package flow{
	//import flash.display.Sprite;
	//import flash.events.MouseEvent;
	import flash.geom.Point;
	

	/**
	 * ProcNode 是程序上面的節點, 一個程序會有4個節點, 位置分別為 0(上),1(左),2(右),3(下)
	 * 這些節點可以用來連結 Line.
	 */  
	public class ProcNode extends Node
	{

		private var ia_lineNode:Array = [];		//can connect to many lineNodes
		private var in_node:int;				//node no, auto(0-3)
		private var i_process:Process;			//parent process of this node
		private var i_area:WorkArea;
		
		/**
		 * Constructor.
		 * 一個 Process 會有 4 個 ProcNode.
		 * @param p_process 擁有這個 Node 的 Process.
		 * @param pn_node 序號, 表示節點的位置, 其值為 0-3
		 * @param p_point 相對於 Process 的 xy 座標.
		 */  
		public function ProcNode(p_process:Process, pn_node:int, p_point:Point){
			super(p_point);
			
			i_area = FlowFun.gArea;
			i_process = p_process;
			in_node = pn_node;
			
			this.buttonMode = true;
			this.useHandCursor = true;
			            
            setColor(Node.cnNoColor);             
		}

		
		/**
		 * 傳回這個節點所連結的 Line Array. (一個節點可以連結多個 Line)
		 * @return LineNode Array.
		 */ 
		public function getLineNodes():Array{
			return ia_lineNode;
		}
		

		/**
		 * 傳回這個節點的位置序號 (0-3)
		 */ 
		public function getNodePos():int{
			return in_node;
		}


		/**
		 * 判斷這個節點是否有連結 Line.
		 */ 
		public function isConnect():Boolean{
			return (ia_lineNode.length > 0);
		}
		
		
		/**
		 * 連結到某個 LineNode.
		 * @param p_node LineNode
		 */ 
		public function connectLineNode(p_lineNode:LineNode):void{
			//connectLineNode2(p_node, true);
			var tn_node:int = getLineNodeNo(p_lineNode);
			if (tn_node == -1){
				var tn_len:int = ia_lineNode.length;
				//i_process.setLineNodes(true);
				ia_lineNode[tn_len] = p_lineNode;
				
				//set color to connected if need
				if (tn_len == 0){
					setColor(Node.cnRed);
				}
			}
			
			/*
			//trigger WorkArea function.
			if (i_area.fProcNodeToLineNode != null){
				i_area.fProcNodeToLineNode(this, p_lineNode, null)
			}
			*/
		}
		
		
		private function getLineNodeNo(p_lineNode:LineNode):int{
			//var tn_len:int = ia_lineNode.length;
			for (var i:int=0; i<ia_lineNode.length; i++){
				if (p_lineNode == ia_lineNode[i]){
					return i;
				}
			}
			
			//case not find
			return -1;
		}

		/**
		 * @param pb_status true(連結), false(取消連結)
		 */ 
		/*
		private function connectLineNode2(p_lineNode:LineNode, pb_status:Boolean):void{
			//find lineNode
			var tn_node:int = -1;
			var tn_len:int = ia_lineNode.length;
			for (var i:int=0; i<tn_len; i++){
				if (p_lineNode == ia_lineNode[i]){
					tn_node = i;
					break;
				}
			}

			//add or remove one ia_lineNode[] element
			if (pb_status){
				if (tn_node == -1){
					//i_process.setLineNodes(true);
					ia_lineNode[tn_len] = p_lineNode;
					
					//set color to connected if need
					if (tn_len == 0){
						setColor(Node.cnRed);
					}
				}
			}else{
				if (tn_node >= 0){
					//i_process.setLineNodes(false);
					ia_lineNode.splice(tn_node, 1);
					
					//set color to disconnect if need
					if (tn_len == 1){
						setColor(cnNoColor);
					}
				}
			}			
		}	
		*/
		
		
		/**
		 * 取消連結這個節點上的某個 LineNode.
		 * @param p_lineNode LineNode
		 * @param pb_byThis true(called by Process), false(called by LineNode)
		 */ 
		public function breakLineNode(p_lineNode:LineNode, pb_byThis:Boolean):void{
			//處理自己的部分
			var tn_node:int = getLineNodeNo(p_lineNode);
			//if (tn_node >= 0){
				var tn_len:int = ia_lineNode.length;
				//i_process.setLineNodes(false);
				ia_lineNode.splice(tn_node, 1);
				
				//set color to disconnect if need
				if (tn_len == 1){
					setColor(cnNoColor);
				}
			//}
			
			//call LineNode
			if (pb_byThis){		
				p_lineNode.breakProcNode(false);
			}			
		}
		
		
		/**
		 * 取消連結這個節點上所有的 LineNodes, called when Process is deleted.
		 */ 
		public function breakLineNodes():void{
			/*
			if (ia_lineNode.length > 0){
				ia_lineNode = [];
				setColor(cnNoColor);
			}
			*/
			
			//var tn_len:int = ia_lineNode.length;
			for (var i:int=ia_lineNode.length - 1; i>=0; i--){
				//(ia_lineNode[i] as LineNode).connectProcNode(false, null, false);
				breakLineNode(ia_lineNode[i] as LineNode, true);
			}
		}
			
		/*
		//update lineNode position
		//called by process.setLineNodePos() when moving flow object
		public function setLineNodePos():void{
			for (var i:int=0; i<ia_lineNode.length; i++){
				ia_lineNode[i].moveToProcNode(this);
			}
		}	
		*/
	}
}