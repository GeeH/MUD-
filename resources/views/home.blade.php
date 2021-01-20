<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MUD!</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>

        .results {
            width: 96%;
            margin: 2% 2% 0 2%;
            border: 2px solid;
            background: black;
            color: lawngreen;
            font-size: 1.4em;
            padding: 8px;
        }

        body {
            font-family: 'Nunito';
        }
    </style>

    <script type="text/javascript" src="//media.twiliocdn.com/sdk/js/sync/v1.0/twilio-sync.min.js"></script>

</head>
<body class="antialiased">

<form method="POST">
    @csrf
    <textarea rows="40"
              name="output"
              id="output"
              class="results"
              readonly>{{ $output ?? '' }}</textarea>
    <input type="text"
           name="command"
           required
           style="font-size: 1.4em; width:76%; margin: 0 0 0 2%; border: 2px solid; background: white; color: black;"
           autofocus
           autocomplete="off"
    />
    <input type="submit"
           style="font-size: 1.4em; width:20%; border: 2px solid; background: white; color: black;"
           value="BOOOOP">
</form>

<footer>
    <div style="font-size: 0.8em; padding: 2%">{{ $userId }}</div>
    <div style="font-size: 0.8em; padding: 2%">{{ $token }}</div>
</footer>
</body>
<script>
    const syncClient = new Twilio.Sync.Client('{{ $token }}', {logLevel: 'info'});
    syncClient.map('{{ \App\Service\UserService::LIST_MAP_NAME }}')
        .then((syncMap) => {
            syncMap.get('{{ $userId }}')
                .then(mapItem => {
                    const outputElement = document.getElementById("output");
                    mapItem.data.output.forEach(line => outputElement.append(line + "\n"));
                    document.getElementById("output").scrollTop = document.getElementById("output").scrollHeight;
                });

            syncMap.on('itemUpdated', event => {
                console.debug("Map was updated", event.isLocal ? "locally." : "by the other person.");
                if (event.item.descriptor.key === '{{ $userId }}') {
                    const outputElement = document.getElementById("output");
                    outputElement.innerText = '';
                    event.item.descriptor.data.output.forEach(line => outputElement.append(line + "\n"));
                    document.getElementById("output").scrollTop = document.getElementById("output").scrollHeight;
                }
            });
        });


</script>
</html>
