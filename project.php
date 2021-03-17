<script type="module">
import { RequestAPI } from './requestapi.js';
window.RequestAPI = RequestAPI;
window.updProject = updProject;
document.onload = RequestAPI.Request('readProjects');

function updProject(extra)
{
	document.getElementById('projectedit').innerHTML = "<br><input type='text' id='newprojname'><button onclick='RequestAPI.Request(\"updateProject\", \""+extra+"\")'>Save changes</button>";
}

</script>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<div id='body'></div>
<div id='project'></div>
<br>
	<button onclick="window.location.href = 'index.php';">Back</button>
</body>
</html>

