export class RequestAPI
{
	static logOut()
	{
		document.cookie.split(";").forEach(function(c) { document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); });
	}

	static readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}


	static setCookie(cname, cvalue, exdays)
	{
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires="+ d.toUTCString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

	static Request(status, extra = "")
	{
		var xmlhttp = new XMLHttpRequest();

		// RESPONSE PART
		xmlhttp.onreadystatechange = function()
		{

			console.log('wtf');
			if (this.readyState == 4 && this.status == 200)
			{
				var myArr = JSON.parse(this.responseText);
				console.log(typeof myArr);
				console.log(status);
				let html;
				switch(status)
				{
					case 'start':
						if(myArr['logging_status'] == 'true')
						{
							document.cookie = '';
							RequestAPI.setCookie('id', myArr['user_id'], 1);
							RequestAPI.setCookie('hash', myArr['hash'], 1);
							
							document.getElementById("body").innerHTML = "<button onclick='window.location.href = \"groups.php\";'>Groups</button><button onclick='window.location.href = \"user.php\";'>Users</button><button onclick='window.location.href = \"project.php\";'>Project</button><button onclick='window.location.href = \"payment.php\";'>Payment</button><br><button onclick='RequestAPI.logOut(); RequestAPI.Request(\"start\");'>Logout</button>";
						}
						else
							document.getElementById("body").innerHTML = "Login: <input type='text' id='login'><br>Password: <input type='text' id='pass'><br><button onclick='RequestAPI.Request(\"login\");'>Sign in</button><button onclick='register();'>Sign up</button>"
					break;
					
					case 'login':
					case 'register':
						if(myArr['logging_status'] == 'true')
						{
							document.cookie = '';
							RequestAPI.setCookie('id', myArr['user_id'], 1);
							RequestAPI.setCookie('hash', myArr['hash'], 1);
							
							document.getElementById("body").innerHTML = "<button onclick='window.location.href = \"groups.php\";'>Groups</button><button onclick='window.location.href = \"user.php\";'>Users</button><button onclick='window.location.href = \"project.php\";'>Project</button><button onclick='window.location.href = \"payment.php\";'>Payment</button><br><button onclick='RequestAPI.logOut(); RequestAPI.Request(\"start\");'>Logout</button>";
						}
					break;

					case 'getGroups':
						html = "Name/Surname: <input type='text' id='name'><br>Login: <input type='text' id='rlog'><br>Password: <input type='text' id='rpass'><br>Email: <input type='text' id='email'><br>Phone: <input type='text' id='phone'><br>Group: <select id='group'>";
						myArr.forEach(group => 
						{
							html += "<option id='"+ group.GROUP_NAME +"'>"+group.GROUP_NAME+"</option>"
						});
						html += "</select><br>Access: <select id='access'><option id='all'>All</option><option value='read'>Read</option><option value='write'>Write</option><option value='view'>View</option></select><br><button onclick='RequestAPI.Request(\"register\")'>Register</button>";
						document.getElementById('body').innerHTML = html;
						break;

					case 'readProjects':
						if(myArr['status'] == 'no_project')
							html = 'Sorry man, you ain\'t got any project...';
						else
						{
							html = "";
							for(const project in myArr)
							{
								if(typeof myArr[project] !== 'object')
									continue;
								html += "<div style='background-color: lightgrey;width: 300px;border: 2px solid green;padding: 50px;margin: 5px;'>Project name: "+myArr[project]['NAME']+"<br>Creater: "+myArr[project]['CREATER']+"<br>Project workers: "+myArr[project]['PROJECT_WORKERS']+"<br>Finish date: "+myArr[project]['DATE_FINISH']+"</div>";
							}

							if(myArr['newproject'])
								html += "<br><button onclick='RequestAPI.Request(\"getUsers\");'>Create new project</button>";
							console.log(myArr);
						} 
						document.getElementById('body').innerHTML = html;
						break;

					case 'getUsers':
						console.log('test');
						html = "Name: <input type='text' id='proj_name'><br>Date: <input type='date' id='date'><br>Workers: <select id='workers'>";
					
						console.log(myArr);
						for(const user in myArr)
						{
							if(typeof myArr[user] !== 'object')
								continue;
							html += "<option id='"+ myArr[user]['LOGIN'] +"'>"+myArr[user]['NAME']+"</option>";
						}

						// myArr.forEach(user => 
						// {
						//     html += "<option id='"+ user.LOGIN +"'>"+user.NAME+"</option>"
						// });
						html += "</select>Work to do: <input type='text' id='worktodo'><br>Status: <select id='status'><option id='draft'>Draft</option><option id='process'>In process</option><option id='finished'>Finished</option><option id='declined'>Declined</option></select><br><button onclick='RequestAPI.Request(\"createProject\");'>Submit new project</button>";
						document.getElementById('project').innerHTML = html;
						break;

					case 'deleteGroup':
						console.log(myArr['status']);
						if(myArr['status'] == '200')
							RequestAPI.Request('readGroup');
						break;

					case 'readGroup':
						html = "";
						console.log(myArr);

						for(const group in myArr)
						{
							if(typeof myArr[group] !== 'object')
								continue;

							html += 'Group name: ' + myArr[group]['GROUP_NAME'] + "<br>People in group: " + myArr[group]['NUM_USERS'] + "";
							if(myArr['newgroup'])
								html += "<br><button onclick='RequestAPI.Request(\"deleteGroup\", \""+ myArr[group]['GROUP_NAME']+"\")'>Delete</button><br>"

						}
						if(myArr['newgroup'])
							html += "<br><br><br>Group name: <input type='text' id='group_name'><br>Access: <select id='access'><option id='all'>All</option><option value='read'>Read</option><option value='write'>Write</option><option value='view'>View</option></select><br><button onclick='RequestAPI.Request(\"createGroup\")'>Create new group</button>";

						document.getElementById('body').innerHTML = html;
						break;

					case 'createGroup':
						if(myArr['response'] == 'success')
							document.getElementById('body2').innerHTML = 'Group added...';
						break;

					case 'readUser':
						html = "";
						if(myArr['status'] == 'no_group')
							html = 'Sorry man, you are not in any group...';
						else
						{
							for(const user in myArr)
							{
								if(typeof myArr[user] !== 'object')
									continue;
								html += "User name: "+myArr[user]['NAME']+"<br>Projects: "+myArr[user]['PROJECTS']+"<br>Phone:"+myArr[user]['PHONE']+"<br>Email:"+myArr[user]['EMAIL']+"<br>Group name: "+myArr[user]['GROUP_NAME']+"<br><br>";
							}
						}

						document.getElementById('body').innerHTML = html;
						break;
					case 'readPayment':
						html = "";
						let workers;

						if(myArr.length == 0)
							html = 'Sorry man, you aint got any project...';
						else
						{
							for(const payment in myArr)
							{
								if(typeof myArr[payment] !== 'object')
									continue;
								if(!myArr[payment]['WORKERS'])
									workers = 'No worker on the project';
								else
									workers = myArr[payment]['WORKERS'];
								html += "<div style='background-color: lightgrey;width: 350px;border: 2px solid green;padding: 50px;margin: 5px;'>Project name: "+myArr[payment]['PROJECT_NAME']+"<br>Create date: "+myArr[payment]['CREATE_DATE']+"<br>Project workers: "+workers+"<br>Finish date: "+myArr[payment]['EXPECTED_FINDATE']+"<br>Payment: "+myArr[payment]['PRICE']+"$<br></div>";
							}
						}

						console.log(myArr);
						document.getElementById('body').innerHTML = html;
						break;
					default:
			
				}

			}
		};


		// REQUEST PART
		switch(status)
		{
			case 'start':
				let id = RequestAPI.readCookie('id');
				let hash = RequestAPI.readCookie('hash');
				if(id !== null && hash !== null)
				{
					xmlhttp.open("GET", "API/api.php?action=logcheck&id="+RequestAPI.readCookie('id')+"&hash="+RequestAPI.readCookie('hash'), true);
					xmlhttp.send();
				}
				else
					document.getElementById("body").innerHTML = "Login: <input type='text' id='login'><br>Password: <input type='text' id='pass'><br><button onclick='RequestAPI.Request(\"login\");'>Sign in</button><button onclick='RequestAPI.Request(\"getGroups\");'>Sign up</button>"
			break;
			
			case 'login':
				xmlhttp.open("GET", "API/api.php?action=login&login="+ document.getElementById('login').value + "&pass=" + document.getElementById('pass').value, true);
				xmlhttp.send();
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
				xmlhttp.send();
				break;

			case 'readProjects':
				xmlhttp.open("GET", "API/api.php?action=readProjects&id=" + RequestAPI.readCookie('id'), true);
				xmlhttp.send();
				break;
			case 'createProject':
				xmlhttp.open("GET", "API/api.php?action=createProject&name="+document.getElementById('proj_name').value+"&date="+document.getElementById('date').value + "&workers="+document.getElementById('workers').value+"&worktodo="+document.getElementById('worktodo').value+"&status="+document.getElementById('status').value+"&creater="+RequestAPI.readCookie('id'), true);
				xmlhttp.send();
				break;
			case 'getUsers':
				xmlhttp.open("GET", "API/api.php?action=getUsers", true);
				xmlhttp.send();
				break;

			case 'getGroups':
				xmlhttp.open("GET", "API/api.php?action=getGroups", true);
				xmlhttp.send();
				break;
			case 'readGroup':
				xmlhttp.open("GET", "API/api.php?action=readGroup&id=" + RequestAPI.readCookie('id'), true);
				xmlhttp.send();
				break;
			case 'createGroup':
				xmlhttp.open("GET", "API/api.php?action=createGroup&group_name="+document.getElementById('group_name').value + "&access="+document.getElementById('access').value, true);
				xmlhttp.send();
				break;
			case 'deleteGroup':
				xmlhttp.open("GET", "API/api.php?action=deleteGroup&group_name="+extra, true);
				xmlhttp.send();
				break;

			case 'readUser':
				xmlhttp.open("GET", "API/api.php?action=readUser&id=" + RequestAPI.readCookie('id'), true);
				xmlhttp.send();
				break;

			case 'readPayment':
				xmlhttp.open("GET", "API/api.php?action=readPayment&id=" + RequestAPI.readCookie('id'), true);
				xmlhttp.send();

			default:

		}

		// xmlhttp.send();
	}
}