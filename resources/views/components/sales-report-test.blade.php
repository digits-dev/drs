@props(['channel' => 'Total',])

<style type="text/css">
    .sales-report {
        width: 100%;
        margin: 20px auto;
        font-family: Arial, sans-serif;
    }

    .sales-report table {
        width: 100%;
        border-collapse: collapse;
    }

    .sales-report th, .sales-report td {
        /* border: 1px solid #ccc; */
        /* border: 1px solid red; */
        padding: 8px;
        text-align: center;
    }

    .sales-report th {
        background-color: #004b87;
        color: white;
        font-weight: bold;
    }


    .sales-report tbody tr:nth-child(odd) {
        background-color: white;
    }

    .sales-report tbody tr:nth-child(even) {
        background-color: white;
    }

    .sales-report td {
        font-size: 14px;
    }

    .sales-report tbody tr td:first-child {
        font-weight: bold;
    }

    .sales-report td {
        padding: 12px;
        border: 1px solid #e1e1e1;
    }

    /* Add green background ONLY to the last three header cells */
    .sales-report th:nth-last-child(-n+3) {
        background-color: #d4edda; /* Light green */
        color: #155724; /* Dark green text */
    }

    .none {
        background-color: white !important;
        border: none !important;
    }


    .bg-white{
        background: white !important;
        color:black !important;
        border: none;       
    }


    .bg-light-blue{
        background-color: #d9eaf9 !important;
        color: black !important;
    }

    .underline {
        position: relative; 
    }
        
    .underline::after {
        content: ""; 
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: 2px; 
        height: 1px; 
        width: 50%;
        background: black; 
    }


    .channel-width{
        width: 100px;
    }

    th{
        font-size:10px;
    }
    
</style>

<div>

    <div class="sales-report">
        <table>
            <thead>
                <tr>
                    {{-- <th rowspan="1">{{strtoupper($channel) }}</th> --}}
                    <th class="channel-width" >{{strtoupper($channel) }}</th>
                    <th >YEAR</th>
                    <th class="none">&nbsp;</th>
                    <th >RUNNING</th>
                    <th >WEEK 1</th>
                    <th >WEEK 2</th>
                    <th >WEEK 3</th>
                    <th >WEEK 4</th>
                    <th>22-Sep</th>
                    <th>23-Sep</th>
                    <th>24-Sep</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td style="font-size: 10px;">% GROWTH</td>
                    <td class="none">&nbsp;</td>
                    <td style="font-size: 10px;" class="none">57%</td>
                    <td style="font-size: 10px;" class="none">59%</td>
                    <td style="font-size: 10px;" class="none">67%</td>
                    <td style="font-size: 10px;" class="none">48%</td>
                    <td style="font-size: 10px;" class="none">55%</td>
                    <td style="font-size: 10px;" class="none">93%</td>
                    <td style="font-size: 10px;" class="none">45%</td>
                    <td style="font-size: 10px;" class="none">28%</td>
                </tr>
                <tr>
                    <td><b>TOTAL</b></td>
                    <td><b>2023</b></td>
                    <td class="none">&nbsp;</td>
                    <td><b>364,066,960.69</b></td>
                    <td><b>90,776,862.44</b></td>
                    <td><b>88,247,412.64</b></td>
                    <td><b>128,512,623.84</b></td>
                    <td><b>56,530,061.77</b></td>
                    <td><b>19,322,158.40</b></td>
                    <td><b>15,543,198.50</b></td>
                    <td><b>21,664,704.87</b></td>
                </tr>
                <tr>
                    <td><b>TOTAL</b></td>
                    <td><b>2024</b></td>
                    <td class="none">&nbsp;</td>
                    <td><b>569,851,305.99</b></td>
                    <td><b>144,294,728.28</b></td>
                    <td><b>147,706,921.38</b></td>
                    <td><b>190,414,056.34</b></td>
                    <td><b>87,435,599.99</b></td>
                    <td><b>37,196,599.03</b></td>
                    <td><b>22,522,368.21</b></td>
                    <td><b>27,716,632.75</b></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>