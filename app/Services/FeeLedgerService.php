<?php

namespace App\Services;

use App\Models\StudentFeeDue;

class FeeLedgerService
{
    /**
     * Recalculate payable, balance and payment status.
     *
     * Formula:
     *
     * Payable =
     * Base
     * - Concession
     * - Fee Waiver
     * + Fine
     * - Fine Waiver
     */
    public function recalculateDue(StudentFeeDue $due): StudentFeeDue
    {
        $baseAmount = (float) $due->base_amount;

        $discountAmount =
            (float) ($due->discount_amount ?? 0);

        $waiverAmount =
            (float) ($due->waiver_amount ?? 0);

        $fineAmount =
            (float) ($due->fine_amount ?? 0);

        $fineWaiverAmount =
            (float) ($due->fine_waiver_amount ?? 0);

        $paidAmount =
            (float) ($due->paid_amount ?? 0);


        /*
        |--------------------------------------------------------------------------
        | Safety
        |--------------------------------------------------------------------------
        */

        $discountAmount = max(
            0,
            min($discountAmount, $baseAmount)
        );

        $remainingAfterDiscount = max(
            0,
            $baseAmount - $discountAmount
        );

        $waiverAmount = max(
            0,
            min(
                $waiverAmount,
                $remainingAfterDiscount
            )
        );

        $fineAmount = max(
            0,
            $fineAmount
        );

        $fineWaiverAmount = max(
            0,
            min(
                $fineWaiverAmount,
                $fineAmount
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Net payable
        |--------------------------------------------------------------------------
        */

        $payableAmount = max(
            0,

            $baseAmount

            - $discountAmount

            - $waiverAmount

            + $fineAmount

            - $fineWaiverAmount
        );


        /*
        |--------------------------------------------------------------------------
        | Balance
        |--------------------------------------------------------------------------
        */

        $balanceAmount = max(
            0,
            $payableAmount - $paidAmount
        );


        /*
        |--------------------------------------------------------------------------
        | Payment Status
        |--------------------------------------------------------------------------
        */

        if ($balanceAmount <= 0) {

            $paymentStatus = 'paid';

        } elseif ($paidAmount > 0) {

            $paymentStatus = 'partially_paid';

        } else {

            $paymentStatus = 'unpaid';
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $due->update([
            'discount_amount' =>
                round($discountAmount, 2),

            'waiver_amount' =>
                round($waiverAmount, 2),

            'fine_amount' =>
                round($fineAmount, 2),

            'fine_waiver_amount' =>
                round($fineWaiverAmount, 2),

            'payable_amount' =>
                round($payableAmount, 2),

            'balance_amount' =>
                round($balanceAmount, 2),

            'payment_status' =>
                $paymentStatus,
        ]);

        return $due->fresh();
    }


    /**
     * Remaining amount on which concession/fee waiver
     * can still be applied.
     */
    public function remainingFee(StudentFeeDue $due): float
    {
        return max(
            0,

            (float) $due->base_amount

            - (float) ($due->discount_amount ?? 0)

            - (float) ($due->waiver_amount ?? 0)
        );
    }


    /**
     * Outstanding fine which can still be waived.
     */
    public function remainingFine(StudentFeeDue $due): float
    {
        return max(
            0,

            (float) ($due->fine_amount ?? 0)

            - (float) ($due->fine_waiver_amount ?? 0)
        );
    }
}