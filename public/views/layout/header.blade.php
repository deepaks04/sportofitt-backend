<!--Preloader div-->
<div class="preloader"></div>
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header mobile-center">
            <a class="navbar-brand" href="{{url('/')}}">
                <img src="assets/home/images/logo.png" alt="Sportofitt" /> 
            </a>
            <span class="click-to-call"  style="margin-top: 20px;" ><a href="{{url('/select')}}" target="_blank">Become A Vendor</a></span>
        </div>
        

        <div class="click-to-call hidden-xs">
            <a href="mailto:{{$email}}"><span class="phone-icon"><i class="fa fa-mail-forward"></i></span><span> {{$email}} </span></a>
            <a href="tel:{{$cell}}" target="_blank">
                <span class="phone-icon"><i class="fa fa-phone"></i></span> <span>{{$cell}}</span>
            </a>
            
        </div>

    </div>
</nav>
<!-- // End Fixed navbar -->