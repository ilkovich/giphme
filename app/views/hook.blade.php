<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Giphme</title>
</head>
<body>
    {{ $email->getMessage() }}
    <pre>
        {{ isset($messageData) ? json_encode($messageData->getData()) : null }}
    </pre>
</body>
</html>
