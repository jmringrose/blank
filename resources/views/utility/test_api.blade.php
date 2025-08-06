<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Email Sequence Subscribe/Unsubscribe</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: monospace; margin: 2em; }
        form { margin-bottom: 2em; }
        label { display:block; margin-top: 1em; }
        input[type="text"], input[type="email"] { width: 250px; }
        button { margin-top: 1em; }
    </style>
</head>
<body>
<h2>Subscribe to Sequence</h2>
<form id="subscribe-form">
    <label>First Name: <input type="text" name="First_Name" required></label>
    <label>Last Name: <input type="text" name="Last_Name" required></label>
    <label>Email: <input type="email" name="Email" required></label>
    <button type="submit">Subscribe</button>
</form>
<div id="subscribe-result"></div>

<h2>Unsubscribe from Sequence</h2>
<form id="unsubscribe-form">
    <label>
        Unsubscribe Token: <input type="text" name="token" placeholder="Enter unsub_token from subscribe response" required>
    </label>
    <button type="submit">Unsubscribe</button>
</form>
<div id="unsubscribe-result"></div>

<script>
    // Subscribe AJAX handler
    document.getElementById('subscribe-form').onsubmit = async function(e) {
        e.preventDefault();
        let form = e.target;
        let data = {
            First_Name: form.First_Name.value,
            Last_Name: form.Last_Name.value,
            Email: form.Email.value
        };
        const res = await fetch('/api/sequence/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        document.getElementById('subscribe-result').innerHTML = '<pre>' + JSON.stringify(result, null, 2) + '</pre>' +
            (result.sequence && result.sequence.unsub_token
                ? '<div>Unsubscribe Token: <b>' + result.sequence.unsub_token + '</b></div>'
                : '');
    };

    // Unsubscribe AJAX handler
    document.getElementById('unsubscribe-form').onsubmit = async function(e) {
        e.preventDefault();
        let token = e.target.token.value;
        const res = await fetch('/api/unsubscribe/' + token, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        if (res.headers.get('content-type').includes('application/json')) {
            const result = await res.json();
            document.getElementById('unsubscribe-result').innerHTML = '<pre>' + JSON.stringify(result, null, 2) + '</pre>';
        } else {
            // If unsubscribed returns an HTML view
            document.getElementById('unsubscribe-result').innerHTML = await res.text();
        }
    };
</script>
</body>
</html>
