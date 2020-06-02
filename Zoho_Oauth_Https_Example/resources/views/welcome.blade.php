<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Oauth2</title>

    </head>
    <body>
          <div>
              <h1 style="text-align:center">User Oauth</h1>
              <button type="button" class="block">
              <a href="{{url('/redirect')}}" class="btn btn-primary">Zoho Auth</a>
              </button>
        </div>


    </body>
</html>
