<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pusher live chat</title>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <div class="chat">
        <div class="top">
            <img src="https://cdn.iconscout.com/icon/free/png-256/free-avatar-370-456322.png?f=webp" alt="Avatar">
            <div>
                <p>Public chat</p>
                <small>Online</small>
            </div>
        </div>

        <div class="messages">
            @include('receive', ['message'=>"Hey whats up yo!"])
        </div>

        <div class="bottom">
            <form>
                <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
                <button type="submit"></button>
            </form>
        </div>
    </div>
</body>

<script>
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {cluster: 'ap2'});
    const channel = pusher.subscribe('public');
    // receive message
    channel.bind('chat', function(data) {
        $.post('/receive', {
            _token: '{{ csrf_token() }}',
            message: data.message,
        })
        .done(function(res){
            $(".messages > .message").last().after(res);
            $(document).scrollTop($(document).height());
        })
    });
    
    $("form").submit(function(event){
        event.preventDefault();

        $.ajax({
            url: '/broadcast',
            method: 'POST',
            headers: {
                'X-Socket-Id': pusher.connection.socket_id
            },
            data: {
                _token: '{{ csrf_token() }}',
                message: $("form #message").val()
            }
        })
        .done(function (res){
            $(".messages > .message").last().after(res);
            $("form #message").val('');
            $(document).scrollTop($(document).height());
        })
    });
</script>
</html>