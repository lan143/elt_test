<?php
/**
 * @var \App\Entities\Message[] $messages
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Guestbook</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1>Guestbook</h1>

        @if ($messages->count() > 0)
            @foreach ($messages as $message)
                <div class="row">
                    <div class="col-sm" data-guid="{{ $message->getGuid() }}">
                        <label>
                            {{ $message->getCreatedAt()->format('d.m.Y H:i:s') }}
                        </label>
                        <p>{{ $message->getMessage() }}</p>
                        <button class="btn btn-primary reply">Reply</button>
                        <button class="btn btn-primary edit">Edit</button>

                        @foreach ($message->getChild() as $childMessage)
                            <div class="row">
                                <div class="col-sm" style="padding-left: 50px;" data-guid="{{ $childMessage->getGuid() }}">
                                    <label>
                                        {{ $childMessage->getCreatedAt()->format('d.m.Y H:i:s') }}
                                    </label>
                                    <p>{{ $childMessage->getMessage() }}</p>
                                    <button class="btn btn-primary reply">Reply</button>
                                    <button class="btn btn-primary edit">Edit</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <p>Records not found</p>
        @endif

        <div class="row">
            <form method="POST" action="/">
                <div class="col-sm">
                    <textarea name="message" cols="30" rows="10" class="form-control"></textarea>
                </div>

                <div class="col-sm">
                    <input type="submit" value="Add new message" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(function () {
            $('button.edit').click(function (e) {
                e.preventDefault();

                $(this).hide();

                $(this).parent().append('<form method="POST" action="/' + $(this).parent().attr('data-guid') + '/update">' +
                    '<textarea name="message" cols="30" rows="10" class="form-control"></textarea>' +
                    '<input type="submit" value="Update" class="btn btn-primary">' +
                    '</form>');

                return false;
            });

            $('button.reply').click(function (e) {
                e.preventDefault();

                $(this).hide();

                $(this).parent().append('<form method="POST" action="/">' +
                    '<input type="hidden" name="parent" value="' + $(this).parent().attr('data-guid') + '">' +
                    '<textarea name="message" cols="30" rows="10" class="form-control"></textarea>' +
                    '<input type="submit" value="Reply" class="btn btn-primary">' +
                    '</form>');

                return false;
            });
        });
    </script>
</body>
</html>
