@extends('crudbooster::admin_template')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />



<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">


<style>
  #charts_container {
    width: 100%;
    display: flex; /* Allows flexbox layout */
    flex-wrap: wrap; /* Enables wrapping for multiple charts */
}
.chart {
    flex: 1 1 auto; /* Allow charts to grow and shrink */
    min-width: 300px; /* Set a minimum width */
    height: 500px; /* Fixed height */
    position: relative; /* Ensure positioning works */
}
</style>

@endpush

@section('content')

<div class="main-content">
    <button id="saveChartBtn">Save Chart</button>
    {{-- <div id="chart_div"></div> --}}

    <div id="charts_container"></div>
    

</div>

@endsection

@push('bottom')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">

$(function() {
    const prevYear = @json($yearData['previousYear']);
    const currYear = @json($yearData['currentYear']);
    const channelCodes = @json($channel_codes);
    const lastThreeDays = @json($lastThreeDaysDates);

    console.group('backendData');
    console.log(prevYear);
    console.log(currYear);
    console.log(channelCodes);
    console.log(lastThreeDays);
    console.groupEnd();

    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages': ['corechart']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawChart);

    // Variable to hold the chart instance
    let chart;

    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
    function drawChart2() {
        var data = google.visualization.arrayToDataTable([
            ['Month', '2021', '2022'],
            ['January', 1031230, 1213120],
            ['February', 201230, 1321380],
            ['March', 301230, 312220],
            ['April', 421300, 381240],
            ['May', 100123, 121240],  // Fixed duplicate month
            ['June', 2012310, 180124], // Fixed duplicate month
            ['July', 300123, 321230],  // Fixed duplicate month
            ['August', 4001241, 380123], // Fixed duplicate month
            ['September', 100132, 121230], // Fixed duplicate month
            ['October', 201240, 181230], // Fixed duplicate month
            ['November', 30042, 32124], // Fixed duplicate month
            ['December', 400124, 381240] // Fixed duplicate month
        ]);

        var options = {
            title: 'Channel',
            hAxis: {title: 'Categories'},
            vAxis: {title: 'Values'},
            isStacked: false
        };

        chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);

        // Get the chart as an image
        var imgUri = chart.getImageURI();
        console.log(imgUri); // This is the image URI to use in PDF
    }

    function drawChart3() {
        // Prepare the data for the chart
        const dataArray = [['Month', `${prevYear}`, `${currYear}`]];

        // Get the current month (0-11) and create the months array dynamically
        const currentMonth = new Date().getMonth(); // 0 = January, 11 = December
        const months = [
            "January", "February", "March", "April", "May", "June", 
            "July", "August", "September", "October", "November", "December"
        ].slice(0, currentMonth + 1); // Get only months up to the current month
        
        months.forEach((month, index) => {
            const prevSales = channelCodes['ECOMM'][prevYear]['months'][`M${String(index + 1).padStart(2, '0')}`]?.sum_of_net_sales || 0;
            const currSales = channelCodes['ECOMM'][currYear]['months'][`M${String(index + 1).padStart(2, '0')}`]?.sum_of_net_sales || 0;
            dataArray.push([month, prevSales, currSales]);
        });

        // Create the DataTable
        const data = google.visualization.arrayToDataTable(dataArray);

        const options = {
            title: 'Sales Comparison',
            // hAxis: { title: 'Months' },
            // vAxis: { title: 'Sales' },
            isStacked: false
        };

        chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);

        // Get the chart as an image
        const imgUri = chart.getImageURI();
        console.log(imgUri); // This is the image URI to use in PDF
    }

    // function drawChart() {
    //     const currentMonth = new Date().getMonth(); // 0 = January, 11 = December
    //     const months = [
    //         "January", "February", "March", "April", "May", "June", 
    //         "July", "August", "September", "October", "November", "December"
    //     ].slice(0, currentMonth + 1); // Get only months up to the current month

    //     // Array to store chart images
    //     const chartImages = [];

    //     // Loop through channel codes
    //     Object.keys(channelCodes).forEach(channel => {
    //         const dataArray = [['Month', `${channel} Previous Year`, `${channel} Current Year`]];

    //         months.forEach((month, index) => {
    //             const prevSales = channelCodes[channel][prevYear]['months'][`M${String(index + 1).padStart(2, '0')}`]?.sum_of_net_sales || 0;
    //             const currSales = channelCodes[channel][currYear]['months'][`M${String(index + 1).padStart(2, '0')}`]?.sum_of_net_sales || 0;
    //             dataArray.push([month, prevSales, currSales]);
    //         });

    //         // Create the DataTable
    //         const data = google.visualization.arrayToDataTable(dataArray);

    //         const options = {
    //             title: `${channel} Sales Comparison`,
    //             hAxis: { title: 'Months' },
    //             vAxis: { title: 'Sales' },
    //             isStacked: false
    //         };

    //         // Create a new div for each chart
    //         const chartDiv = document.createElement('div');
    //         chartDiv.id = `${channel}-chart`;
    //         chartDiv.className = 'chart';
    //         document.getElementById('charts_container').appendChild(chartDiv);

    //         chart = new google.visualization.ColumnChart(chartDiv);
    //         chart.draw(data, options);

       
    //         // Get the chart as an image and store it
    //         const imgUri = chart.getImageURI();
    //         chartImages.push(imgUri); // Store image URI for PDF generation
    //     });

    //     // Now you can call a function to generate the PDF with the chart images
    //     // generatePDF(chartImages);
    // }


    // Set up the button click event
//     document.getElementById('saveChartBtn').addEventListener('click', saveChart);

//     function saveChart() {
//     if (!chart) {
//         console.error('Chart is not initialized.');
//         return;
//     }

//     var imgUri = chart.getImageURI();

//     fetch('/admin/save_chart', {
//         method: 'POST',
//         body: JSON.stringify({ image: imgUri }),
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': '{{ csrf_token() }}'
//         }
//     })
//     .then(response => {
//         if (!response.ok) {
//             throw new Error('Network response was not ok ' + response.statusText);
//         }
//         return response.blob(); // Change to blob for downloading
//     })
//     .then(blob => {
//         const url = window.URL.createObjectURL(blob);
//         const a = document.createElement('a');
//         a.href = url;
//         a.download = 'document.pdf'; // Specify the filename
//         document.body.appendChild(a);
//         a.click();
//         a.remove();
//         window.URL.revokeObjectURL(url); // Clean up
//     })
//     .catch(error => console.error('Error:', error));
// }

    // Set up the button click event
   
    document.getElementById('saveChartBtn').addEventListener('click', saveChart);

    const charts = {}; // Object to store chart instances
    const chartImages = [];

    function drawChart() {
        const currentMonth = new Date().getMonth(); // 0 = January, 11 = December
        const months = [
            "January", "February", "March", "April", "May", "June", 
            "July", "August", "September", "October", "November", "December"
        ].slice(0, currentMonth + 1); // Get only months up to the current month

        // Loop through channel codes
        Object.keys(channelCodes).forEach(channel => {
            const dataArray = [['Month', `${prevYear}`, `${currYear}`]];

            months.forEach((month, index) => {
                const prevSales = channelCodes[channel][prevYear]['months'][`M${String(index + 1).padStart(2, '0')}`]?.sum_of_net_sales || 0;
                const currSales = channelCodes[channel][currYear]['months'][`M${String(index + 1).padStart(2, '0')}`]?.sum_of_net_sales || 0;
                
                dataArray.push([month, prevSales, currSales]);
            });

            // Create the DataTable
            const data = google.visualization.arrayToDataTable(dataArray);

            const options = {
                colors: ['#76A7FA', '#33FF57', '#3357FF'], 
                
                
                title: `${channel} Sales Report`,
                hAxis: { title: 'Months' },
                vAxis: { title: 'Sales' },
                is3D: true,
                isStacked: false,
                //   pieSliceText: 'value',
                chartArea: {
                    width:'100%',
                    height:'100%',
                    top: 100, 
                    left: 150, 
                    right:50,
                    bottom:50,
                },
           
                legend:{position: 'top', textStyle: { fontSize: 13}, alignment:'end'},
                annotations: {
                textStyle: {
                    color: '#000', // Change color if needed
                    fontSize: 12 // Change font size if needed
                }
            }
            };

            // Create a new div for each chart
            const chartDiv = document.createElement('div');
            chartDiv.id = `${channel}-chart`;
            chartDiv.className = 'chart';
            document.getElementById('charts_container').appendChild(chartDiv);

            // Create and store the chart instance
            const channelChart = new google.visualization.ColumnChart(chartDiv);
            channelChart.draw(data, options);
            
            // Store the chart instance for later use
            charts[channel] = channelChart;

            // Add an event listener for window resizing
            window.addEventListener('resize', () => {
                // Redraw the chart
                channelChart.draw(data, options); // You might need to access the chart instances stored in `charts`
            });

            // Get the chart as an image and store it
            const imgUri = channelChart.getImageURI();
            chartImages.push(imgUri); // Store image URI for PDF generation
        });


     

        // Now you can call a function to generate the PDF with the chart images
        // generatePDF(chartImages);
    }

    // Save chart function
    function saveChart(event) {
        event.preventDefault();
        console.log('Button clicked!');

        const chartImages = []; // Array to hold all chart images
        const jsonChannels = @json($channel_codes); // Fetch channel codes from backend
        const channelCodes = Object.keys(jsonChannels); // Extract channel keys

        // Loop through each channel and get its chart image
        channelCodes.forEach(channel => {
            if (charts[channel]) { // Check if the chart instance exists
                const imgUri = charts[channel].getImageURI(); // Use the existing chart instance
                chartImages.push(imgUri); // Store the image URI
            } else {
                console.warn(`Chart instance for ${channel} not found.`);
            }
        });

        fetch('/admin/save_chart', {
            method: 'POST',
            body: JSON.stringify({ images: chartImages }), // Send the array of images
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.blob(); // Change to blob for downloading
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'document.pdf'; // Specify the filename
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url); // Clean up
        })
        .catch(error => console.error('Error:', error));
    }
});


</script>



</script>
@endpush
