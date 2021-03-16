<script type="module">
	import { RequestAPI } from './requestapi.js';
	window.RequestAPI = RequestAPI;
	document.onload = RequestAPI.Request('start');

</script>


<!DOCTYPE html>
<html>
<head>
	<title>Docufy</title>
</head>
<body>
<div id='body'></div>
</body>
</html>