<!DOCTYPE html>
<html>
<head>
	<title>Docufy</title>
</head>
<body>

<div id='body'></div>
</body>
</html>


<script type="text/javascript">
document.onload = apiRequest('start');

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

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}


function logOut()
{
	document.cookie.split(";").forEach(function(c) { document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); });
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
				case 'start':
					if(myArr['logging_status'] == 'true')
					{
						document.cookie = '';
						setCookie('id', myArr['user_id'], 1);
						setCookie('hash', myArr['hash'], 1);
						
						document.getElementById("body").innerHTML = "<button onclick='window.location.href = \"groups.php\";'>Groups</button><button onclick='window.location.href = \"user.php\";'>Users</button><button onclick='window.location.href = \"project.php\";'>Project</button><button>Payment</button><br><button onclick='logOut(); apiRequest(\"start\");'>Logout</button>";
					}
					else
						document.getElementById("body").innerHTML = "Login: <input type='text' id='login'><br>Password: <input type='text' id='pass'><br><button onclick='apiRequest(\"login\");'>Sign in</button><button onclick='register();'>Sign up</button>"
				break;
				
				case 'login':
				case 'register':
					if(myArr['logging_status'] == 'true')
					{
						document.cookie = '';
						setCookie('id', myArr['user_id'], 1);
						setCookie('hash', myArr['hash'], 1);
						
						document.getElementById("body").innerHTML = "<button onclick='window.location.href = \"groups.php\";'>Groups</button><button onclick='window.location.href = \"user.php\";'>Users</button><button onclick='window.location.href = \"project.php\";'>Project</button><button>Payment</button><br><button onclick='logOut(); apiRequest(\"start\");'>Logout</button>";
					}
				break;

				case 'getGroups':
					html = "Name/Surname: <input type='text' id='name'><br>Login: <input type='text' id='rlog'><br>Password: <input type='text' id='rpass'><br>Email: <input type='text' id='email'><br>Phone: <input type='text' id='phone'><br>Group: <select id='group'>";
					myArr.forEach(group => 
					{
						html += "<option id='"+ group.GROUP_NAME +"'>"+group.GROUP_NAME+"</option>"
					});
					html += "</select><br>Access: <select id='access'><option id='all'>All</option><option value='read'>Read</option><option value='write'>Write</option><option value='view'>View</option></select><br><button onclick='apiRequest(\"register\")'>Register</button>";
					document.getElementById('body').innerHTML = html;
					break;
				default:
		
			}

		}
	};

	switch(status)
	{
		case 'start':
			let id = readCookie('id');
			let hash = readCookie('hash');
			if(id !== null && hash !== null)
				xmlhttp.open("GET", "API/api.php?action=logcheck&id="+readCookie('id')+"&hash="+readCookie('hash'), true);
			else
				document.getElementById("body").innerHTML = "Login: <input type='text' id='login'><br>Password: <input type='text' id='pass'><br><button onclick='apiRequest(\"login\");'>Sign in</button><button onclick='apiRequest(\"getGroups\");'>Sign up</button>"
		break;
		
		case 'login':
			xmlhttp.open("GET", "API/api.php?action=login&login="+ document.getElementById('login').value + "&pass=" + document.getElementById('pass').value, true);
		break;

		case 'getGroups':
			xmlhttp.open("GET", "API/api.php?action=getGroups", true);
			break;

		case 'register':
			xmlhttp.open("GET", "API/api.php?action=register"
				+"&login="+document.getElementById('rlog').value
				+"&pass="+document.getElementById("rpass").value
				+"&email="+document.getElementById("email").value
				+"&name="+document.getElementById("name").value
				+"&phone="+document.getElementById("phone").value
				+"&group="+document.getElementById("group").value
				+"&access="+document.getElementById("access").value)
			break;
		default:
	}

	xmlhttp.send();
}

</script>




<?php 






 ?>