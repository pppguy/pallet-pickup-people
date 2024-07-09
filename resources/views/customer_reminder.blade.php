<!DOCTYPE html>
<html>
<head>
    <title>Pallet Pickup Reminder</title>
</head>
<body>
    <p>Dear {{$customerName}},</p>
    <p>We are looking to schedule a Pallet Pickup on {{$emailDate}}. Will you have enough pallets then for a pickup? Please respond to this email by clicking one of the buttons below:</p>
    <a href="{{ $yesUrl }}">YES</a>
    <a href="{{ $noUrl }}">NO</a>
</body>
</html>
