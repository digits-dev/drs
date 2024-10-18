<!DOCTYPE html>
<html>
<head>
    <title>Document</title>
    <style>
        /* Add any styles you need for the PDF here */
    </style>
</head>
<body>
    <h1>Sales Report</h1>
    {{-- <img src="{{ $chartImage }}" alt="Chart" style="width: 100%; height: auto;" /> --}}
    <!-- Add more content as needed -->

    @foreach ($chartImages as $chartImage)
        <img src="{{ $chartImage }}" alt="Chart" style="width: 100%; height: auto; margin-bottom: 20px;">
    @endforeach
</body>
</html>
