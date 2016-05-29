<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <p>Hello {{$vendorName}}!,</p>
        <p>{{$fname}} {{$lname}} has made booking. </p> 
        <p>Booking id: {{$orderNumber}} Booking Date {{$bookingDate}} @if($packageType == 2) &amp; time: {{$bookingTime}} @endif </p>
        <p>{{$venueName}} - {{$subCategoryName}}</p>
        <p>{{$address}} - {{$pincode}}</p>
        <p>Payment Mode: {{$paymentMode}}</p>
        <p>Order Summary</p>
        <p>Order Amount : Rs {{$bookingAmount}}</p>
        <p>Convenience Charges: Rs {{$convenienceCharges}}</p>
        <p>Discount Amount: Rs {{$discountAmount}}</p>
        <p>__________________________</p>
        <p>Total Amount: Rs {{$totalAmount}}</p>
        <p>Note: Cancellation &amp;amp; refund policy are applicable as mentioned by Venue providers</p> 
    </body>
</html>