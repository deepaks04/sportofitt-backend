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
            <h3 class="subline">About Us</h3>
            <hr>
        </div>
          SPORTOFITT is web based one stop destination for Sports, Adventure sports and Fitness. We have a mission to make Sports/Fitness a habit to help you stay active in the modern world, and do so through sports and fitness. Regardless of your skill level or physical condition, sportofitt.com will help you connect to the activities of your choice and find other people with similar interests or abilities and bring back the joy of playing and workout. 
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
