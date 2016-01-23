@extends('views.layout.base')
@section('content')
<header id="top" class="header header_menu_padding fitness_bg">
    <div class="container">
        <div class="header-content">
            <div class="row">
                <div class="col-md-6">
                    <div class="hero-area">
                        <h1 class="headline">SPORTS,FITNESS,SPA's,<span class="text-highlight">&amp;</span> OUTDOORS</h1>
                        <h3 class="headline-support">Fitness training,classes & more </h3>
                        <!-- Telephone : only visible on Mobile devices
                                          To Change Mobile on Desktop, Its above -->
                        <div class="click-to-call visible-xs">
                            <a href="tel:{{$cell}}" target="_blank">
                                <span class="phone-icon"><i class="fa fa-phone"></i></span> <span>{{$cell}}</span>
                            </a>
                        </div>
                        <p class="vertical-space">
                            Do you get bored of same routine at gyms? Want to make fitness fun? Tired of searching sports locations? Looking to relax your body & make your soul happy.</p>
                    </div>
                    <div class="benefits benefits-vertical">
                        <div class="benefit-item">
                            <div class="benefit-icon"><i class="icon icon-clock-streamline-time"> </i></div>
                            <h6 class="benefit-title">Flexible Timing</h6>
                            <p>We know you are super busy. Book facilities on your convenience be it Gym,Sports,Outdoors or Spa. </p>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon"><i class="icon icon-speech-streamline-talk-user"> </i></div>
                            <h6 class="benefit-title">Learn from experts</h6>
                            <p>Want to try something new ,stuck at same level of fitness need to take your game a notch up, we have experts to help you.</p>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon"> <i class="icon icon-bubble-love-streamline-talk"> </i></div>
                            <h6 class="benefit-title">Personal Attention</h6>
                            <p>Goal oriented fitness plans giving you maximum results.</p>
                        </div>
                    </div>
                </div>
                <!-- // end .col -->

            </div>
            <!-- // end .row -->

        </div>
        <!-- // end .header-content -->

    </div>
    <!-- end .container -->
</header>
<!-- end .header -->
<!-- 
 Appointment Section
 ====================================== -->
<section class="appointment top-space" id="register">
    <div class="container">

        <div class="row">

            <div class="col-sm-6 col-md-7">

                <div class="section-title left-aligned">
                    <h2>WHY <span class="text-highlight">FITNESS</span> IS IMPORTNAT</h2>
                    <h3 class="subline">Join Sportofitt & make your body healthy & soul happy.</h3>
                    <hr>
                </div>
<!--                <p class="bottom-space">.</p>-->

                <img src="assets/home/images/Badminton.jpg" class="img-responsive" alt="Badminton">

            </div>
            <!-- // end .col -->

            <div class="col-sm-6 col-md-5">

                <div class="appointment-form top-space-lg">

                    <div class="form-header">
                        <h4>JOIN SPORTOFITT</h4>
                        <a href="tel:{{$cell}}">Call: {{$cell}}</a>

                        <div class="or_icon">OR</div>
                        <br/>
                        <a href="mailto:{{$email}}">Email: {{$email}}</a>
                    </div>

                    <form action="{{url('/contact')}}" id="phpcontactform" method="POST">

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" name="name" required title="Your full name please">
                        </div>
                        <div class="form-group">
                            <label>Email ID</label>
                            <input type="email" class="form-control" name="email" required title="We need your email address">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" name="phone" required title="Please enter your phone number">
                        </div>
                        <div class="text-center top-space">
                            <button type="submit" class="btn btn-success btn-block btn-xl" id="js-contact-btn">Become a member</button>
                            <small class="text-muted">Hurry up, limited discount available</small>
                            <div id="js-contact-result" data-success-msg="Thank you !." data-error-msg="Oops. Something went wrong."></div>
                        </div>

                    </form>
                </div>

            </div>
            <!-- // end .col -->

        </div>
        <!-- // end .row -->

    </div>
    <!-- end .container -->
</section>
<!-- end section.appointment -->

<!-- 
 About fitness
 ====================================== -->
<section class="the-course top-space-lg">
    <div class="container">

        <div class="section-title text-center">
            <h2>MEMBER <span class="text-highlight">BENEFITS</span></h2>
            <h3 class="subline">Here's a list of why you should join SPORTOFITT.</h3>
            <hr>
        </div>

        <div class="row">
            <div class="col-md-4">

                <ul class="list-unstyled checklist checklist-full checklist-right">
                    <li>
                        <p> <i class="fa fa-check"></i> Wide variety of choices </p>
                    </li>

                    <li>
                        <p> <i class="fa fa-check"></i> Program to fit your needs </p>
                    </li>

                    <li>
                        <p> <i class="fa fa-check"></i> Fun and challenging</p>
                    </li>

                    <li>
                        <p> <i class="fa fa-check"></i> Group Exercise classes </p>
                    </li>

                    <li>
                        <p> <i class="fa fa-check"></i> Certified Personal Trainers </p>
                    </li>

                    <li>
                        <p> <i class="fa fa-check"></i>Achieve your fitness goals </p>
                    </li>

                </ul>

            </div>
            <div class="col-md-4">

                <div class="col-md-10 col-md-offset-1"> <img src="assets/home/images/fitness-pic-2.jpg" class="img-responsive center-block" alt="Yoga Parvatasana Pose"></div>

            </div>
            <div class="col-md-4">

                <ul class="list-unstyled checklist checklist-full">

                    <li>
                        <p> <i class="fa fa-check"></i> Pay per use </p>
                    </li>

                    <li>
                        <p> <i class="fa fa-check"></i> Get discount on packages </p>
                    </li>

                    <li>
                        <p> <i class="fa fa-check"></i> Book single or multiple sessions </p>
                    </li>

                    <li>
                        <p> <i class="fa fa-check"></i> Get points on your purchases</p>
                    </li>

                    <li>
                        <p> <i class="fa fa-check"></i>Find partners to play sports</p>
                    </li>

                    <li>
                        <p> <i class="fa fa-check"></i> and much more... </p>
                    </li>
                </ul>

            </div>
        </div>

    </div>
    <!-- end .container -->
</section>
<!-- end section.the-course -->

<!-- 
 OUR SERVICES
 ====================================== -->
<section class="highlight top-space-lg">

    <div class="container">

        <div class="section-title text-center">
            <h2>OUR <span class="text-highlight">SERVICES</span></h2>
            <h3 class="subline">Here are some of the services we provide.</h3>
            <hr>
        </div>

        <div class="row">
            <div class="col-sm-6 col-md-3">
                <div class="thumbnail">
                    <img src="assets/home/images/tennis.jpg" alt="Sports,Tennis">
                    <div class="caption">
                        <h6 class="caption-title text-dark">SPORTS</h6>
                        <p class="caption-text">Learn and play new sports be a beginner become a master. </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="thumbnail">
                    <img src="assets/home/images/Adventure.jpg" alt="Adventure">
                    <div class="caption">
                        <h6 class="caption-title text-dark">OUTDOORS</h6>
                        <p class="caption-text">Replace those boring getaways with a rush of adrenaline.</p>
                    </div>
                </div>
            </div>

            <div class="clearfix visible-sm"></div>

            <div class="col-sm-6 col-md-3">
                <div class="thumbnail">
                    <img src="assets/home/images/Fitness.jpg" alt="Fitness">
                    <div class="caption">
                        <h6 class="caption-title text-dark">FITNESS</h6>
                        <p class="caption-text">Try different workouts based on your goals and avoid monotony.&nbsp;.&nbsp;.&nbsp;</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div class="thumbnail">
                    <img src="assets/home/images/spa.jpg" alt="SPA">
                    <div class="caption">
                        <h6 class="caption-title text-dark">THERAPIES</h6>
                        <p class="caption-text">Feel tired with all the work, reward your body with relaxing spa therapies.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- // end .row -->

    </div>
    <!-- // end .container -->

</section>
<!-- end section.services -->

<!-- 
 FAQ - Frequently asked questions
 ====================================== -->
<section class="faq">
    <div class="container">

        <div class="section-title text-center">
            <h2>Frequently <span class="text-highlight">Asked</span> Questions</h2>
            <h3 class="subline">Got questions? We've got answers.</h3>
            <hr>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h6 class="faq-title">How can I get to the centre?</h6>
                <p>The map and Contact details are listed within the contact information. You may also call to get more accurate details.</p>
                <h6 class="faq-title">I have medical problems. Can I join?</h6>
                <p>Sure. Our instructors will warn before doing fitness job that are harmful for that medical condition. So keep away from doing that. </p>
            </div>
            <div class="col-md-6">
                <h6 class="faq-title">What payment types do you accept?</h6>
                <p>All payments are processed via PayPal. You may also pay at the centre. But please call us to join the club.</p>
                <h6 class="faq-title">Can I get a refund?</h6>
                <p>Yes. The first 3 days of your membership is completely free. You may cancel your subscription if you are not satisfied. </p>
            </div>
        </div>
    </div>
</section>
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
