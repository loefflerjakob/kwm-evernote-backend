<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
<h1>
    Hello World
</h1>

<ul>
    @foreach($users as $user)
        <li>{{$user->firstName}} {{$user->lastName}}</li>
    @endforeach
</ul>

</body>
</html>
