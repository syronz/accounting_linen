window.onload = function WindowLoad(event) {
	// document.getElementsByTagName("p").addEventListener("click", displayDate());

// document.getElementById("fieldcontainer").addEventListener("click", displayDate);
document.querySelector("#fieldcontainer").addEventListener("click", logs);

	function logs(e){
		var txtBox = document.querySelector("#queryval");
		var query = txtBox.value;
		var caretPostion = caret(txtBox);
		console.log(query.length,caretPostion);
		if(query.length == caretPostion)
			txtBox.value += " " + e.target.text;
		else{
			var p1 = query.substr(0,caretPostion);
			var p2 = query.substr(caretPostion);
			txtBox.value = p1 + " " + e.target.text +"," + p2;
			setCaretPosition(txtBox, (p1 + " " + e.target.text +",").length);
		}
		console.log(e.target.text,txtBox.value,caret(txtBox));
		
	}


	function caret(node) {
	 //node.focus(); 
	 /* without node.focus() IE will returns -1 when focus is not on node */
	 if(node.selectionStart) return node.selectionStart;
	 else if(!document.selection) return 0;
	 var c		= "\001";
	 var sel	= document.selection.createRange();
	 var dul	= sel.duplicate();
	 var len	= 0;
	 dul.moveToElementText(node);
	 sel.text	= c;
	 len		= (dul.text.indexOf(c));
	 sel.moveStart('character',-1);
	 sel.text	= "";
	 return len;
	}

	function setCaretPosition(ctrl, pos){
		if(ctrl.setSelectionRange)
		{
			ctrl.focus();
			ctrl.setSelectionRange(pos,pos);
		}
		else if (ctrl.createTextRange) {
			var range = ctrl.createTextRange();
			range.collapse(true);
			range.moveEnd('character', pos);
			range.moveStart('character', pos);
			range.select();
		}
	}
}