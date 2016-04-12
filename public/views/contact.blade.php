@extends('views.layout.base')
@section('content')
<!-- 
 FAQ - Frequently asked questions
 ====================================== -->

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="section-title text-center">
                <h2>&nbsp</h2>
                <h3 class="subline">Contact Us</h3>
                <hr>
            </div>
            <h6>Office Address</h6>
            <p>
                Bungalow 97, Jayanti Nagari III
                New Somalwada, Nagpur 440005
            </p>
            <p>
                Contact No : 7757088700
            </p>
            <p>
                Email : <a href="mailto:select@sportofitt.com">select@sportofitt.com</a>
            </p>
        </div>         
    </div>
</div>
<!-- end section.faq -->


<!-- 
 Call to Action
 ====================================== -->
<section class="call-to-action">
    <div class="container">
        <div class="section-title text-center">
            <h2><span class="text-highlight">FREE ENTRY</span></h2>
            <h3 class="subline">Limited discount available. Join before its too late.</h3>
            <hr>

        </div>

        <div class="cta-button text-center vertical-space bottom-space-xl"> <a class="btn btn-success btn-xl" href="#register">JOIN THE CLUB</a>
            <h6 class="vertical-space">or Call : <a href="tel:{{$cell}}">{{$cell}}</a></h6>
        </div>

    </div>
</section>
<!-- end section.footer-action -->


<!-- 
 Call to Action
 ====================================== -->
<div class="highlight contact">
    <div class="container">

        <div class="row">

            <!-- // end .col -->

            <div class="col-sm-4">
                <p><span class="text-highlight"><strong>Contact Us</strong></span>
                    <br>{{$cell}}
                    <br>{{$email}}
                </p>
            </div>
            <!-- // end .col -->

            <div class="col-sm-4">
                <p><span class="text-highlight"><strong>Schedule</strong></span>
                    <br>10 am to 7 pm on Weekdays
                    <br>11 am to 6 pm on Weekends</p>
            </div>
            <!-- // end .col -->
        </div>
        <!-- // end .row -->
    </div>
</div>
<!-- end section.footer-action -->
@stop
