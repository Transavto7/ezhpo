<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OpenAPI</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui.css"/>
</head>
<body>
<div id="swagger-ui"></div>

<script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-bundle.js" crossorigin></script>
<script>
    window.onload = () => {
        window.ui = SwaggerUIBundle({
            url: '{{ asset('openapi.yaml') }}',
            dom_id: '#swagger-ui',
            requestInterceptor: function (request) {
                request.headers['Authorization'] = 'Bearer {{ $apiToken }}';

                return request
            }
        });
    };
</script>
</body>
</html>
