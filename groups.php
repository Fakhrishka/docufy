<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<div id='body'></div>
<div id='body2'></div>
</body>
</html>


<script type="text/javascript">
document.onload = apiRequest('readGroup');

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

		console.log('wtf');
		if (this.readyState == 4 && this.status == 200)
		{
			var myArr = JSON.parse(this.responseText);
			console.log(myArr);
			switch(status)
			{
				case 'readGroup':
					let html = myArr[0]['GROUP_NAME'] + "<br>" + myArr[0]['NUM_USERS'] + "<br>";
					if(myArr['ACCESS'] == 'ALL')
						html += "<br><br><br>Group name: <input type='text' id='group_name'><br>Access: <input type='text' id='access'><br><button onclick='apiRequest(\"createGroup\")'>Create new group</button>";

					document.getElementById('body').innerHTML = html;
					break;

				case 'createGroup':
					if(myArr['response'] == 'success')
						document.getElementById('body2').innerHTML = 'Group added...';
					break;
				default:
		
			}

		}
	};

	switch(status)
	{
		case 'readGroup':
			xmlhttp.open("GET", "API/api.php?action=readGroup&id=" + readCookie('id'), true);
			xmlhttp.send();
			break;
		case 'createGroup':
			xmlhttp.open("GET", "API/api.php?action=createGroup&group_name="+document.getElementById('group_name').value + "&access="+document.getElementById('access').value, true);
			xmlhttp.send();

			break;
		default:
	}

}

</script>



<?php

 ?>