<script type="module" >
import { RequestAPI } from './requestapi.js';
window.RequestAPI = RequestAPI;
window.updGroup = updGroup;
document.onload = RequestAPI.Request('readGroup');

function updGroup(extra)
{
	document.getElementById('groupchange').innerHTML = "<br><input type='text' id='newgroupname'><button onclick='RequestAPI.Request(\"updateGroup\", \""+extra+"\")'>Save changes</button>";
}

</script>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<div id='body'></div>
<div id='body2'></div>
<br>	
	<button onclick="window.location.href = 'index.php';">Back</button>
</body>
</html>





<?php

 ?>