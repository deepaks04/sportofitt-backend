<form name="savemessage" action="savemessages" method="POST">
    @foreach($messages as $key => $message) 
    @if(is_array($message))
        @foreach($message as $rule => $value)
            <input type="text" name="{{$key}}/{{$rule}}" value="{{$value}}" /> <br/>
        @endforeach
    @endif
    @endforeach
    <input type="submit" value="submit" />
</form>