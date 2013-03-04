/**
* Encrypts and decrypts text with the Rijndael algorithm.
* @authors Mika Palmu
* @version 2.0
*
* Original Javascript implementation:
* Fritz Schneider, University of California
* Algorithm: Joan Daemen and Vincent Rijmen
* See http://www.cs.ucsd.edu/~fritz/rijndael.html
*/

//中文字無法解碼 (done)

//class it.pacem.cryptography.Rijndael {
package com {
	import flash.utils.ByteArray;
	
	
	public class Rijndael {
	
		/**
		* Variables
		* @exclude
		*/
		//private var iaRoundArray:Array;
		//private var iaShiftOffset:Array;
		private var inRounds:Number, inKeys:Number, inBlocks:Number;
		private var iaRcon:Array = [0x01, 0x02, 0x04, 0x08, 0x10, 0x20,0x40, 0x80, 0x1b, 0x36, 0x6c, 0xd8,0xab, 0x4d, 0x9a, 0x2f, 0x5e, 0xbc,0x63, 0xc6, 0x97, 0x35, 0x6a, 0xd4,0xb3, 0x7d, 0xfa, 0xef, 0xc5, 0x91 ];
		private var iaSbox:Array = [ 99, 124, 119, 123, 242, 107, 111, 197, 48, 1, 103, 43, 254, 215, 171, 118, 202, 130, 201, 125, 250, 89, 71, 240, 173, 212, 162, 175, 156, 164, 114, 192, 183, 253, 147, 38, 54, 63, 247, 204, 52, 165, 229, 241, 113, 216, 49, 21, 4, 199, 35, 195, 24, 150, 5, 154, 7, 18, 128, 226, 235, 39, 178, 117, 9, 131, 44, 26, 27, 110, 90, 160, 82, 59, 214, 179, 41, 227, 47, 132,  83, 209, 0, 237, 32, 252, 177, 91, 106, 203, 190, 57, 74, 76, 88, 207, 208, 239, 170, 251, 67, 77, 51, 133, 69, 249, 2, 127, 80, 60, 159, 168, 81, 163, 64, 143, 146, 157, 56, 245, 188, 182, 218, 33, 16, 255, 243, 210, 205, 12, 19, 236, 95, 151, 68, 23, 196, 167, 126, 61, 100, 93, 25, 115, 96, 129, 79, 220, 34, 42, 144, 136, 70, 238, 184, 20, 222, 94, 11, 219, 224, 50, 58, 10, 73, 6, 36, 92, 194, 211, 172, 98, 145, 149, 228, 121, 231, 200, 55, 109, 141, 213, 78, 169, 108, 86, 244, 234, 101, 122, 174, 8, 186, 120, 37, 46, 28, 166, 180, 198, 232, 221, 116, 31, 75, 189, 139, 138, 112, 62, 181, 102, 72, 3, 246, 14, 97, 53, 87, 185, 134, 193, 29, 158, 225, 248, 152, 17, 105, 217, 142, 148, 155, 30, 135, 233, 206, 85, 40, 223, 140, 161, 137, 13, 191, 230, 66, 104, 65, 153, 45, 15, 176, 84, 187, 22];
		private var iaSbox2:Array = [ 82, 9, 106, 213, 48, 54, 165, 56, 191, 64, 163, 158, 129, 243, 215, 251, 124, 227, 57, 130, 155, 47, 255, 135, 52, 142, 67, 68, 196, 222, 233, 203, 84, 123, 148, 50, 166, 194, 35, 61, 238, 76, 149, 11, 66, 250, 195, 78, 8, 46, 161, 102, 40, 217, 36, 178, 118, 91, 162, 73, 109, 139, 209, 37, 114, 248, 246, 100, 134, 104, 152, 22, 212, 164, 92, 204, 93, 101, 182, 146, 108, 112, 72, 80, 253, 237, 185, 218, 94, 21, 70, 87, 167, 141, 157, 132, 144, 216, 171, 0, 140, 188, 211, 10, 247, 228, 88, 5, 184, 179, 69, 6, 208, 44, 30, 143, 202, 63, 15, 2, 193, 175, 189, 3, 1, 19, 138, 107, 58, 145, 17, 65, 79, 103,220, 234, 151, 242, 207, 206, 240, 180, 230, 115, 150, 172, 116, 34, 231, 173, 53, 133, 226, 249, 55, 232, 28, 117, 223, 110, 71, 241, 26, 113, 29, 41, 197, 137, 111, 183, 98, 14, 170, 24, 190, 27, 252, 86, 62, 75, 198, 210, 121, 32, 154, 219, 192, 254, 120, 205, 90, 244, 31, 221, 168, 51, 136, 7, 199, 49, 177, 18, 16, 89, 39, 128, 236, 95, 96, 81, 127, 169, 25, 181, 74, 13, 45, 229, 122, 159, 147, 201, 156, 239, 160, 224, 59, 77, 174, 42, 245, 176, 200, 235, 187, 60, 131, 83, 153, 97, 23, 43, 4, 126, 186, 119, 214, 38, 225, 105, 20, 99, 85, 33, 12, 125];
		private var inKeySize:Number = 256;
		private var inBlockSize:Number = 128;
		private var iaRoundArray:Array = [0,0,0,0,[0,0,0,0,10,0,12,0,14],0,[0,0,0,0,12,0,12,0,14],0,[0,0,0,0,14,0,14,0,14]];
		private var iaShiftOffset:Array = [0,0,0,0,[0,1,2,3],0,[0,1,2,3],0,[0,1,3,4]];
		private var iaKeyByte:Array;
	
		/**
		* Constructor
		* @exclude
		*/
		public function Rijndael(pa_keyByte:Array){
			iaKeyByte = pa_keyByte;
			inKeySize = iaKeyByte.length * 8;
			//if (pn_blockSize > 0) 
			//	inBlockSize = pn_blockSize;
			//this.iaRoundArray = [0,0,0,0,[0,0,0,0,10,0,12,0,14],0,[0,0,0,0,12,0,12,0,14],0,[0,0,0,0,14,0,14,0,14]];
			//this.iaShiftOffset = [0,0,0,0,[0,1,2,3],0,[0,1,2,3],0,[0,1,3,4]];
			inBlocks = inBlockSize / 32; 
			inKeys = inKeySize / 32;
			inRounds = iaRoundArray[inKeys][inBlocks];
		}
	
		/**
		* Encrypts a string with the specified key and mode.
		*/
		//public function encrypt(ps_src:String, ps_key:String, ps_mode:String):String {
		//public function encrypt(ps_src:String, ps_key:String):String {
		public function encrypt(ps_src:String):String {
			var ta_ct:Array = [];
			var ta_block:Array = [];
			var tn_bpb:Number = this.inBlockSize / 8;
			
			//ps_src = strToUtf8Str(ps_src);
			//ps_key = strToUtf8Str(ps_key);
			
			//if (ps_mode == "CBC") 
			//	ta_ct = this.getRandomBytes(tn_bpb);
			//var ta_char:Array = this.formatPlaintext(strToUtf8s(ps_src));
			//var ta_expKey:Array = this.keyExpansion(strToUtf8s(ps_key));
			var ta_char:Array = this.formatPlaintext(strToBytes(ps_src));
			//var ta_expKey:Array = this.keyExpansion(strToBytes(ps_key));
			var ta_expKey:Array = this.keyExpansion(iaKeyByte);
			for (var tn_block:Number = 0; tn_block<ta_char.length / tn_bpb; tn_block++) {
				ta_block = ta_char.slice(tn_block*tn_bpb, (tn_block+1)*tn_bpb);
				/*
				if (ps_mode == "CBC") {
					for (var i:Number = 0; i<tn_bpb; i++) {
						ta_block[i] ^= ta_ct[tn_block*tn_bpb + i];
					}
				}
				*/
				ta_ct = ta_ct.concat(this.encryption(ta_block, ta_expKey));
			}
			return bytesToHexStr(ta_ct);
		}
	
		/**
		* Decrypts a string with the specified key and mode.
		*/
		//public function decrypt(ps_hex:String, ps_key:String):String {
		public function decrypt(ps_hex:String):String {
			
			var ta_pt:Array = [];
			var ta_block:Array = [];
			//var ta_char:Array = hexToBytes(ps_hex);
			var ta_char:Array = hexStrToBytes(ps_hex);
			var tn_bpb:Number = this.inBlockSize / 8;
			//var ta_expKey:Array = this.keyExpansion(strToBytes(ps_key));
			var ta_expKey:Array = this.keyExpansion(iaKeyByte);
			for (var tn_block:Number = (ta_char.length/tn_bpb)-1; tn_block>0; tn_block--) {
				ta_block = decryption(ta_char.slice(tn_block*tn_bpb, (tn_block+1)*tn_bpb), ta_expKey);
				/*
				if(ps_mode == "CBC") {
					for (var i:Number = 0; i<tn_bpb; i++) {
						ta_pt[(tn_block-1)*tn_bpb+i] = ta_block[i] ^ ta_char[(tn_block-1)*tn_bpb+i];
					}
				}else{ 
				*/	
					ta_pt = ta_block.concat(ta_pt);
				//}
			}
			//if (ps_mode == "ECB") {
				ta_pt = this.decryption(ta_char.slice(0,tn_bpb), ta_expKey).concat(ta_pt);
			//}
			return bytesToStr(ta_pt);
		}
	
		/**
		* Private methods.
		*/
		private function cyclicShiftLeft(pa_src:Array, pn_pos:Number):Array {
			var ta_temp:Array = pa_src.slice(0, pn_pos);
			pa_src = pa_src.slice(pn_pos).concat(ta_temp);
			return pa_src;
		}
		private function xtime(pn_poly:Number):Number {
			pn_poly <<= 1;
			return ((pn_poly & 0x100) ? (pn_poly ^ 0x11B) : (pn_poly));
		}
		private function mult_GF256(pn_x:Number, pn_y:Number):Number {
			var tn_result:Number = 0;
			for (var tn_bit:Number = 1; tn_bit<256; tn_bit *= 2, pn_y = xtime(pn_y)) {
				if(pn_x & tn_bit) 
					tn_result ^= pn_y;
			}
			return tn_result;
		}
		private function byteSub(pa_state:Array, pb_encode:Boolean):void {
			var ta_S:Array;
			if(pb_encode) 
				ta_S = this.iaSbox;
			else 
				ta_S = this.iaSbox2;
				
			for (var i:Number = 0; i<4; i++) {
				for (var j:int = 0; j<this.inBlocks; j++) pa_state[i][j] = ta_S[pa_state[i][j]];
			}
		}
		private function shiftRow(pa_state:Array, pb_encode:Boolean):void {
			for (var i:Number = 1; i<4; i++) {
				if (pb_encode) 
					pa_state[i] = this.cyclicShiftLeft(pa_state[i], this.iaShiftOffset[inBlocks][i]);
				else 
					pa_state[i] = this.cyclicShiftLeft(pa_state[i], this.inBlocks-this.iaShiftOffset[inBlocks][i]);
			}
		}
		private function mixColumn(pa_state:Array, pb_encode:Boolean):void {
			var ta_b:Array = [];
			var i:Number;
			for (var j:Number = 0; j<this.inBlocks; j++) {
				for(i = 0; i<4; i++) {
					if (pb_encode) 
						ta_b[i] = this.mult_GF256(pa_state[i][j], 2) ^ this.mult_GF256(pa_state[(i+1)%4][j], 3) ^ pa_state[(i+2)%4][j] ^ pa_state[(i+3)%4][j];
					else 
						ta_b[i] = this.mult_GF256(pa_state[i][j], 0xE) ^ this.mult_GF256(pa_state[(i+1)%4][j], 0xB) ^ this.mult_GF256(pa_state[(i+2)%4][j], 0xD) ^ this.mult_GF256(pa_state[(i+3)%4][j], 9);
				}
				for (i = 0; i<4; i++) {
					pa_state[i][j] = ta_b[i];
				}
			}
		}
		private function addRoundKey(pa_state:Array, pa_roundKey:Array):void {
			for (var j:Number = 0; j<this.inBlocks; j++) {
				pa_state[0][j] ^= (pa_roundKey[j] & 0xFF);
				pa_state[1][j] ^= ((pa_roundKey[j]>>8) &0xFF);
				pa_state[2][j] ^= ((pa_roundKey[j]>>16) &0xFF);
				pa_state[3][j] ^= ((pa_roundKey[j]>>24) &0xFF);
			}
		}
		//private function keyExpansion(key:Array):Array {
		private function keyExpansion(key:Array):Array {
			var temp:Number = 0;
			this.inKeys = this.inKeySize/32;
			this.inBlocks = this.inBlockSize/32;
			var ta_expKey:Array = [];
			this.inRounds = this.iaRoundArray[this.inKeys][this.inBlocks];
			var j:Number;
			for (j = 0; j<this.inKeys; j++) 
				ta_expKey[j] = (key[4*j]) | (key[4*j+1]<<8) | (key[4*j+2]<<16) | (key[4*j+3]<<24);
				
			for (j = this.inKeys; j<this.inBlocks*(this.inRounds+1); j++) {
				temp = ta_expKey[j-1];
				if (j % this.inKeys == 0) 
					temp = ( (this.iaSbox[(temp>>8) & 0xFF]) | (this.iaSbox[(temp>>16) & 0xFF]<<8) | (this.iaSbox[(temp>>24) & 0xFF]<<16) | (this.iaSbox[temp & 0xFF]<<24) ) ^ this.iaRcon[Math.floor(j / this.inKeys) - 1];
				else if (this.inKeys > 6 && j % this.inKeys == 4) 
					temp = (this.iaSbox[(temp>>24) & 0xFF]<<24) | (this.iaSbox[(temp>>16) & 0xFF]<<16) | (this.iaSbox[(temp>>8) & 0xFF]<<8) | (this.iaSbox[temp & 0xFF]);
					
				ta_expKey[j] = ta_expKey[j-this.inKeys] ^ temp;
			}
			return ta_expKey;
		}
		private function Round(pa_state:Array, pa_roundKey:Array):void {
			this.byteSub(pa_state, true);
			this.shiftRow(pa_state, true);
			this.mixColumn(pa_state, true);
			this.addRoundKey(pa_state, pa_roundKey);
		}
		private function InverseRound(pa_state:Array, pa_roundKey:Array):void {
			this.addRoundKey(pa_state, pa_roundKey);
			this.mixColumn(pa_state, false);
			this.shiftRow(pa_state, false);
			this.byteSub(pa_state, false);
		}
		private function FinalRound(pa_state:Array, pa_roundKey:Array):void {
			this.byteSub(pa_state, true);
			this.shiftRow(pa_state, true);
			this.addRoundKey(pa_state, pa_roundKey);
		}
		private function InverseFinalRound(pa_state:Array, pa_roundKey:Array):void {
			this.addRoundKey(pa_state, pa_roundKey);
			this.shiftRow(pa_state, false);
			this.byteSub(pa_state, false);
		}
		private function encryption(pa_block:Array, ta_expKey:Array):Array {
			pa_block = this.packBytes(pa_block);
			this.addRoundKey(pa_block, ta_expKey);
			for (var i:Number = 1; i<inRounds; i++) {
				this.Round(pa_block, ta_expKey.slice(this.inBlocks*i, this.inBlocks*(i+1)));
			}
			this.FinalRound(pa_block, ta_expKey.slice(this.inBlocks*this.inRounds));
			return this.unpackBytes(pa_block);
		}
		private function decryption(pa_block:Array, pa_expKey:Array):Array {
			pa_block = this.packBytes(pa_block);
			this.InverseFinalRound(pa_block, pa_expKey.slice(this.inBlocks*this.inRounds));
			for (var i:Number = inRounds-1; i>0; i--) {
				this.InverseRound(pa_block, pa_expKey.slice(this.inBlocks*i, this.inBlocks*(i+1)));
			}
			this.addRoundKey(pa_block, pa_expKey);
			return this.unpackBytes(pa_block);
		}
		private function packBytes(octets:Array):Array {
			var ta_state:Array = [];
			ta_state[0] = []; 
			ta_state[1] = [];
			ta_state[2] = []; 
			ta_state[3] = [];
			for (var j:Number = 0; j<octets.length; j+= 4) {
				ta_state[0][j/4] = octets[j];
				ta_state[1][j/4] = octets[j+1];
				ta_state[2][j/4] = octets[j+2];
				ta_state[3][j/4] = octets[j+3];
			}
			return ta_state;
		}
		private function unpackBytes(packed:Array):Array {
			var ta_result:Array = [];
			for (var j:Number = 0; j<packed[0].length; j++) {
				ta_result[ta_result.length] = packed[0][j];
				ta_result[ta_result.length] = packed[1][j];
				ta_result[ta_result.length] = packed[2][j];
				ta_result[ta_result.length] = packed[3][j];
			}
			return ta_result;
		}
		//private function formatPlaintext(plaintext:Array):Array {
		private function formatPlaintext(plaintext:Array):Array {
			var tn_bpb:Number = inBlockSize / 8;
			for (var i:Number = tn_bpb-(plaintext.length % tn_bpb); i>0 && i<tn_bpb; i--) {
				plaintext[plaintext.length] = 0;
			}
			return plaintext;
		}
		private function getRandomBytes(howMany:Number):Array {
			var bytes:Array = [];
			for (var i:Number = 0; i<howMany; i++) {
				bytes[i] = Math.round(Math.random()*255);
			}
			return bytes;
		}
		
		
		//====================
		/**
		 * 把 16 進位字串轉換成 byte array 
		 * @
		 */ 
		public static function hexStrToBytes(ps_hex:String):Array {
			var ta_codes:Array = [];
			for (var i:Number = (ps_hex.substr(0, 2) == "0x") ? 2 : 0; i<ps_hex.length; i+=2) {
				ta_codes.push(parseInt(ps_hex.substr(i, 2), 16));
			}
			return ta_codes;
		}
		
		
		/*
		public function zz_hexStrToBytes(ps_hex:String):Array {
			ps_hex = ps_hex.replace(/\s|:/gm,'');
			var ta_byte:Array = [];
			if (ps_hex.length&1==1) 
				ps_hex="0"+ps_hex;
				
			for (var i:uint=0;i<ps_hex.length;i+=2)
				ta_byte[i/2] = parseInt(ps_hex.substr(i,2),16);
				
			return ta_byte;
		}
		*/
		
		/**
		 * 把 byte array 轉換成 16 進位字串
		 * @
		 */ 
		public static function bytesToHexStr(pa_code:Array):String {
			//var ts_result:String = new String("");
			var ts_result:String = "";
			var ta_hex:Array = ["0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"];
			for (var i:Number = 0; i<pa_code.length; i++) {
				ts_result += ta_hex[pa_code[i] >> 4] + ta_hex[pa_code[i] & 0xf];
			}
			return ts_result;
		}
		

		/**
		 * 把 unicode 數字陣列轉換成字串
		 * @param pa_code unicode 數字陣列
		 * @return 字串
		 */  
		public static function bytesToStr(pa_code:Array):String {
			var result : String = "";
			var i : int = 0;
			var c1 : int = 0, c2 : int = 0, c3 : int = 0;
	 
			while ( i < pa_code.length ) {
	 
				//c1 = text.charCodeAt(i);
				c1 = pa_code[i];
	 
				if (c1 < 128) {
					result += String.fromCharCode(c1);
					i++;
				} else if((c1 > 191) && (c1 < 224)) {
					//c2 = text.charCodeAt(i + 1);
					c2 = pa_code[i + 1];
					result += String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
					i += 2;
				} else {
					c2 = pa_code[i + 1];
					c3 = pa_code[i + 2];
					//c2 = text.charCodeAt(i + 1);
					//c3 = text.charCodeAt(i + 2);
					result += String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
					i += 3;
				}
			}
	 
			return result;
		}
	
	
		/**
		 * 把字串轉換成 unicode 數字陣列, 陣列長度等於字串長度
		 * @param ps_str 原始字串
		 * @return unicode 數字陣列
		 */  
		public static function strToBytes(ps_str:String):Array {
		 	var ta_byte:ByteArray = new ByteArray()
		 	ta_byte.writeUTFBytes(ps_str);
		 	var ta_byte2:Array = [];
		 	var j:int=0;
		 	for (var i:int=0; i< ta_byte.length; i++){
		 		if (ta_byte[i] != 0){		 			
		 			ta_byte2[j] = ta_byte[i];
		 			j++;
		 		}
		 	}
		 	return ta_byte2; 
		}
		
	}//class
}//package