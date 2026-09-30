<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <title>
        {{ $feeReceipt->receipt_no }}
    </title>

    <style>

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #222;
            margin: 25px;
        }

        .receipt {
            max-width: 800px;
            margin: auto;
            border: 1px solid #bbb;
            padding: 25px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            margin: 0 0 5px;
        }

        .info {
            width: 100%;
            margin-bottom: 20px;
        }

        .info td {
            padding: 5px;
        }

        table.fees {
            width: 100%;
            border-collapse: collapse;
        }

        .fees th,
        .fees td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        .text-right {
            text-align: right;
        }

        .total {
            font-weight: bold;
        }

        .cancelled {
            color: #b00020;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
        }

        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        @media print {

            .no-print {
                display: none;
            }

            body {
                margin: 0;
            }

            .receipt {
                border: none;
            }
        }

    </style>

</head>


<body>


<div class="receipt">

    <div class="header">

        <h2>
            {{ $feeReceipt->school->school_name ?? 'School' }}
        </h2>

        @if($feeReceipt->school->address ?? null)

            <div>
                {{ $feeReceipt->school->address }}
            </div>

        @endif

        <h3>
            FEE RECEIPT
        </h3>

    </div>


    @if($feeReceipt->status === 'cancelled')

        <div class="cancelled">
            *** CANCELLED RECEIPT ***
        </div>

    @endif


    <table class="info">

        <tr>

            <td>
                <strong>Receipt No:</strong>
                {{ $feeReceipt->receipt_no }}
            </td>

            <td>
                <strong>Date:</strong>
                {{ optional($feeReceipt->payment_date)->format('d-m-Y') }}
            </td>

        </tr>


        <tr>

            <td>
                <strong>Student:</strong>

                {{ $feeReceipt->student->student_name
                    ?? $feeReceipt->student->name
                    ?? '-' }}
            </td>

            <td>
                <strong>Admission No:</strong>

                {{ $feeReceipt->student->admission_no ?? '-' }}
            </td>

        </tr>


        <tr>

            <td>
                <strong>Class:</strong>

                {{ $feeReceipt->enrollment->schoolClass->name ?? '-' }}

                @if($feeReceipt->enrollment->section ?? null)
                    -
                    {{ $feeReceipt->enrollment->section->name }}
                @endif

            </td>

            <td>
                <strong>Academic Year:</strong>

                {{ $feeReceipt->academicYear->name ?? '-' }}
            </td>

        </tr>


        <tr>

            <td>
                <strong>Payment Mode:</strong>

                {{ $feeReceipt->paymentMode->name
                    ?? ucfirst($feeReceipt->payment_mode ?? '-') }}
            </td>

            <td>

                @if($feeReceipt->transaction_no)

                    <strong>Reference:</strong>
                    {{ $feeReceipt->transaction_no }}

                @endif

            </td>

        </tr>

    </table>


    <table class="fees">

        <thead>

            <tr>
                <th>Fee Component</th>
                <th>Installment</th>
                <th>Due Date</th>
                <th class="text-right">Paid</th>
            </tr>

        </thead>


        <tbody>

            @foreach($feeReceipt->items as $item)

                <tr>

                    <td>
                        {{ $item->fee_head_name }}
                    </td>

                    <td>
                        {{ $item->installment_name }}
                    </td>

                    <td>
                        {{ optional($item->due_date)->format('d-m-Y') }}
                    </td>

                    <td class="text-right">

                        ₹{{ number_format($item->amount, 2) }}

                    </td>

                </tr>

            @endforeach

        </tbody>


        <tfoot>

            <tr class="total">

                <td colspan="3"
                    class="text-right">

                    Total Received

                </td>

                <td class="text-right">

                    ₹{{ number_format($feeReceipt->total_amount, 2) }}

                </td>

            </tr>

        </tfoot>

    </table>


    @if($feeReceipt->remarks)

        <p>
            <strong>Remarks:</strong>
            {{ $feeReceipt->remarks }}
        </p>

    @endif


    <div class="footer">

        <div>
            Received By:
            {{ $feeReceipt->collector->name ?? '-' }}
        </div>

        <div>
            Authorized Signature
        </div>

    </div>


    <div class="no-print"
         style="text-align:center; margin-top:30px;">

        <button onclick="window.print()">
            Print Receipt
        </button>

    </div>

</div>


</body>
</html>