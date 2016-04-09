<form name="frmaddimage" id='frmaddimage' action='{{url('add/images')}}' method="POST">
<select name="facility" id="facility" >
    <option>SELECT FACILITY</option>
    @foreach($facilities as $facility)
    <option value="{{$facility->id}}" data-vendor="{{$facility->vendor->id}}">{{$facility->name}}</option>
    @endforeach
</select>

<input class="upload" type="file" name="image" id="image" value="" accept="image/*" multiple/>
<input type="hidden" value="" name="fileName" id="fileName" />
<input type="hidden" value="" name="vendor" id="vendor" />

<div id="uploadwrapper"></div>
<button type="submit" class="btn btn-lg btn-finduser">SUBMIT</button>	
</form>
<script type="text/javascript" src="{{asset('packages/js/jquery.js')}}"></script>
<script type="text/javascript" src="{{asset('packages/js/fileupload/jquery.ui.widget.js')}}"></script>
<script type="text/javascript" src="{{asset('packages/js/fileupload/jquery.iframe-transport.js')}}"></script>
<script type="text/javascript" src="{{asset('packages/js/fileupload/jquery.fileupload.js')}}"></script>

<script>
var fileUpload = "{{ route('facility.uploadmedia') }}";
var tempPath = '{{ asset("/uploads/temp") }}/';
var imagesPath = '{{ asset("packages/images")}}/';
var filesArray = new Array;
$(function () {
    $("#facility").change(function(){
        var optionSelected = $('option:selected', this).attr('data-vendor');
       $("#vendor").val(optionSelected) 
    });
    $("#image").fileupload({
        formdata:{'mediatype':'image'},
        dataType: 'json',
        url: fileUpload,
        limitMultiFileUploads: 10,
        maxNumberOfFiles: 10,
        sequentialUploads: true,
        replaceFileInput: false,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        beforeSend: function (e) {
        },
        done: function (e, data) {
            if (data.result.valid == 1) {
                filesArray.push(data.result.fileName);
                console.log(filesArray);
                $("#fileName").val(filesArray);
                $("#uploadwrapper").append('<img src="' + tempPath + data.result.fileName + '" width="100" height="100"/><a href="javascript:void(0);" class="removeuploadmedia deleteMedia" data-file="' + data.result.fileName + '"><i class="fa fa-times"></i></a>');
            }

            if (data.result.error != null) {
                $("#fileName").val("");
                $("#uploadwrapper").append('<p class="alert alert-error">' + data.result.error + '</p>');
            }
        }
    });
});
</script>