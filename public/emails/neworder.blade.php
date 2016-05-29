<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <p>Dear {{$fname}} {{$lname}},</p>
        <p>Your Booking is confirmed. Kindly show this message or SMS at the reception to avail the services at the venue.</p>
        <p>Booking id: {{$orderNumber}} Booking Date {{$bookingDate}} &amp; time: {{$bookingTime}} </p>
        <p>{{$venueName}} - {{$subCategoryName}}</p>
        <p>{{$address}} - {{$pincode}}</p>
        <p>Payment Mode: {{$paymentMode}}</p>
        <p>Order Summary</p>
        <p>Order Amount : Rs {{$bookingAmount}}</p>
        <p>Convenience Charges: Rs {{$convenienceCharges}}</p>
        <p>Discount Amount: Rs {{$discountAmount}}</p>
        <p>__________________________</p>
        <p>Total Amount: Rs {{$totalAmount}}</p>
        <p>Note: Cancellation &amp;amp; refund policy are applicable as mentioned by Venueproviders</p> </body>
</html>