<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            /*width: 90%;*/
            margin: auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            /*margin-top: 10px;*/
            /*margin-bottom: 10px;*/
            height: 920px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            padding: 10px 0px 10px 0px;
        }
        table h3 {
                text-align:left;
                margin: 0px;
                float:left;
        }
        .lt {
            text-align:left;
        }
        .rt {
            text-align:right;
        }
        .header p {
            margin: 5px 0 0;
        }
        .company-details, .customer-details {
            width: 50%;
            float: left;
            margin-bottom: 20px;
        }
        .customer-details {
            text-align: right;
        }
        .details {
            clear: both;
            margin-bottom: 20px;
        }
         table {
            border-collapse: collapse;
        }
        #content {
            width: 100%;
            margin-bottom: 40px;
        }
        #content, #content th, #content td {
            border: 1px solid #ddd;
        }
        #content th, #content td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            text-align: right;
            margin-bottom: 20px;
        }
        .total strong {
            font-size: 1.2em;
        }
        /*.footer {*/
        /*    text-align: center;*/
        /*    color: #666;*/
        /*    font-size: 0.9em;*/
        /*    margin-top: 40px;*/
        /*    padding-top: 20px;*/
        /*    border-top: 1px solid #ddd;*/
        /*}*/
         .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
        }
        table {border: none;}
    </style>
</head>
<body>
    <?php
    $orderRequest = @$order['order_request'];
    $company      = @$orderRequest['company_details'];
    $customer     = @$order['user_details'];
    $count = 1;
    ?>
    <div class="container">
        <div class="header">
            <h1>Invoice</h1>
            <table cellspacing="0" cellpadding="0" style="width:100%;">
                <thead>
                    <tr><td colspan="3"><h3>Company:</h3></td>
                    
                    <td colspan="2"></td>
                        <td>
                            <p class="lt">Invoice : #{{str_pad($order['id'], 4, "0", STR_PAD_LEFT)}}<br>
                            Date    : {{date('F d, Y')}}</p>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <p class="lt">{{@$Company['company_name']}}<br>
                                {{@$Company['company_address']}}<br>
                                <!--EIN: {{@$Company['ein']}}<br>-->
                                <!--SSN: {{@$Company['ssn']}}<br>-->
                                Phone: {{@$Company['phone']}}<br>
                                Email: {{@$Company['phone']}}</p>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="details">
            <div class="company-details">
                <h3>Provider:</h3>
                <p class="lt">{{@$company['company_name']}}<br>
                {{@$company['company_address']}}<br>
                EIN: {{@$company['ein']}}<br>
                SSN: {{@$company['ssn']}}<br>
                Phone: {{@$company['phone']}}<br>
                Email: {{@$company['phone']}}</p>
            </div>
            <div class="customer-details">
                <h3>Customer:</h3>
                <p class="lt">{{@$customer['name']}}<br>
                {{@$order['address']}}<br>
                Phone: {{@$customer['phone']}}<br>
                Email: {{@$customer['email']}}</p>
            </div>
        </div>
        <table id="content" style="padding-top:30%;">
            <thead>
                <tr>
                    <th>Index</th>
                    <th>Service</th>
                    <th>Sub-Service</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach(@$order['services'] as $service)
                    @foreach(@$service['sub_services'] as $sub_service)
                        @foreach(@$sub_service['product'] as $product)
                            <tr>
                                <td>{{$count++}}</td>
                                <td>{{$service['name']}}</td>
                                <td>{{$sub_service['sub_service_name']}}</td>
                                <td>{{$product['name']}}</td>
                                <td>{{$product['quantity']}}</td>
                                <td>$ {{$product['product_price']}}</td>
                                <td>$ {{$product['quantity']*$product['product_price']}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
        <div class="total">
            <table style="width:100%;">
                <tbody>
                    <tr>
                        <td colspan="2"></td>
                        <td>
                            <p style="float: right;">Total Amount: ${{number_format((float)$order['amount'], 2, '.', '')}}<br>
                             Tax         : ${{number_format((float)$order['tax'], 2, '.', '')}}<br>
                            Discount    : ${{number_format((float)$order['discount'], 2, '.', '')}}<br>
                            Net Amount  : ${{number_format((float)$order['net_amount'], 2, '.', '')}}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Please make the payment by the due date.</p>
        </div>
    </div>
</body>
</html>