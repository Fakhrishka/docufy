<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<div id='body'></div>
</body>
</html>


<script type="text/javascript">
document.onload = apiRequest('readUser');

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function apiRequest(status)
{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function()
	{
		if (this.readyState == 4 && this.status == 200)
		{
			var myArr = JSON.parse(this.responseText);
			console.log(myArr);
			switch(status)
			{
				case 'readUser':
					console.log(myArr);
					break;

				case 'createUser':
					
					break;
				default:
		
			}

		}
	};

	switch(status)
	{
		case 'readUser':
			xmlhttp.open("GET", "API/api.php?action=readUser&id=" + readCookie('id'), true);
			xmlhttp.send();
			break;
		case 'createUser':


			break;
		default:
	}

}

</script>



<?php

 ?>