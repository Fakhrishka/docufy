<script type="module">
import { RequestAPI } from './requestapi.js';
window.RequestAPI = RequestAPI;
document.onload = RequestAPI.Request('readUser');
</script>


<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<div id='body'></div>
<br>
<button onclick="window.location.href = 'index.php';">Back</button>
</body>
</html>




<?php

 ?>