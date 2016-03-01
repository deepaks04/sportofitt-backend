<html>
    <body>
        <span><a href="{{url('messages')}}">Edit Messages</a></span>
        <table cellspacing="1" cellpadding=1 border="1">
            <tr>
                <th>Attribute</th>
                <th>Rule</th>
                <th>Message</th>
            </tr>
            @foreach($messages as $key => $message) 
            @if(is_array($message))
            @foreach($message as $rule => $value)
            <tr>
                <td>{{ucfirst(str_replace("_"," ",$key))}} </td>
                <td>{{$rule}}</td>
                <td>{{$value}}</td>
            </tr>
            @endforeach
            @endif
            @endforeach
        </table>

    </body>
</html>
