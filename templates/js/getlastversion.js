var request = null;
if(window.XMLHttpRequest){
	request = new XMLHttpRequest();
} else if(window.ActiveXObject){
	request = new ActiveXObject("Microsoft.XMLHTTP");
}

function get_last_version(){
	if(request){
		request.open("POST","http://rpvg.altervista.org/phpsge/func.php");
		request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset = UTF-8");
		
		request.onreadystatechange = function(){
			if(request.readyState==4){
				if( request.responseText.length >1 ) document.getElementById('laver').innerHTML = request.responseText;
				else document.getElementById('laver').innerHTML = "Failed to get";
			}
		}
		var str_sendr = "f=showver";
		request.send(str_sendr);
	} else alert("Ajax Eror!");
}