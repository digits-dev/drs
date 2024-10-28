<!DOCTYPE html>
<html>
<head>
    <title>Document</title>
    <style>
        /* Add any styles you need for the PDF here */
    </style>
</head>
<body>
    {{-- <img src="{{ $chartImage }}" alt="Chart" style="width: 100%; height: auto;" /> --}}
    <!-- Add more content as needed -->

    @foreach ($chartImages as $chartImage)
        {{-- <img src="{{ $chartImage }}" alt="Chart" style="width: 100%; height: auto; margin-bottom: 20px;"> --}}
        <div >
            <img src="{{ $chartImage }}" alt="Chart"  style="display:block; margin:0 auto;">
        </div>
    @endforeach
</body>
</html>
