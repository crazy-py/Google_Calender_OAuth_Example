<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>

        <title>Login</title>
  <body>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
<style media="screen">
      .block {
      display: block;
      width: 100%;
      border: none;
      background-color: #4CAF50;
      padding: 14px 28px;
      font-size: 16px;
      cursor: pointer;
      text-align: center;
      }
</style>
    </head>
    <body>
          <div>
              <h1 style="text-align:center">Google Calender Oauth</h1>
                   <button type="button" class="block">
                     <a href="{{url('/redirect')}}" class="btn btn-primary">Login Using Google</a>
                     </button>
             </div>
           </div>
    </body>
</html>
