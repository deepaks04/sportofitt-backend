<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<form name="savemessage" action="savemessages" method="POST" class="form-horizontal" style="margin: 5px 5px 15px 10px;">
    @foreach($messages as $key => $message) 
    @if(is_array($message))
    @foreach($message as $rule => $value)
    <div class="form-group form-group-lg">
        <label class="col-sm-2 control-label" for="formGroupInputSmall">{{$key}} : {{$rule}}</label>
        <div class="col-sm-10">
            <input type="text" name="{{$key}}/{{$rule}}" value="{{$value}}" class="form-control" /> <br/>
        </div>
    </div>    
    @endforeach
    @endif
    @endforeach
    <input type="submit" value="submit" class="btn btn-primary"/>
</form>