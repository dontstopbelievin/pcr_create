<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		body, html{
			padding: 0px;
			margin: 0px;
		}
	</style>
</head>
<body>

<script type="text/javascript">

	function b64toBlob(dataURI){
        var byteString = atob(dataURI);
        var ab = new ArrayBuffer(byteString.length);
        var ia = new Uint8Array(ab);

        for (var i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
        }
        return new Blob([ab], {type: 'application/pdf'});
    }

	//FUNCTION TO OPEN PDF IN NEW TAB
	var saveByteArray = (function () {
	var a = document.createElement("a");
	document.body.appendChild(a);
	a.style = "display: none";

	return function (data, name) {
		var blob = b64toBlob(data);
		var url = window.URL.createObjectURL(blob);
		a.href = url;
		a.target = "_blank";
		a.click();
		setTimeout(function() {window.URL.revokeObjectURL(url);},3);
	};

	}());
	//RUN MAIN FUNCTION
	saveByteArray(encodeURI(<?php echo json_encode($data['file']) ?>), "test.pdf");

</script>
</body>
</html>
