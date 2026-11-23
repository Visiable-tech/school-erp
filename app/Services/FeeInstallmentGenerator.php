<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\FeeInstallment;
use App\Models\FeeStructure;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FeeInstallmentGenerator
{
    /**
     * Called inside the controller's DB transaction, after saving items.
     * Never modifies previously generated installment records.
     */
    public function generate(FeeStructure $structure): int
    {
        $year = AcademicYear::where('school_id', $structure->school_id)
            ->findOrFail($structure->academic_year_id);

        $start = CarbonImmutable::parse($year->start_date)->startOfDay();
        $end = CarbonImmutable::parse($year->end_date)->startOfDay();

        if ($end->lt($start)) {
            throw ValidationException::withMessages([
                'academic_year_id' => 'Academic year end date must follow its start date.',
            ]);
        }

        $months = ($end->year - $start->year) * 12 + ($end->month - $start->month) + 1;
        $items = DB::table('fee_structure_items as i')
            ->join('fee_heads as h', 'h.id', '=', 'i.fee_head_id')
            ->join('fee_cycles as c', 'c.id', '=', 'h.fee_cycle_id')
            ->where('i.fee_structure_id', $structure->id)
            ->where('i.status', 1)
            ->select('i.id', 'i.amount', 'i.sort_order', 'h.name as head_name',
                'c.cycle_type', 'c.installments_count')
            ->orderBy('i.sort_order')
            ->get();

        $created = 0;
        foreach ($items as $item) {
            $type = strtolower(trim((string) $item->cycle_type));
            $interval = match ($type) {
                'monthly' => 1,
                'quarterly' => 3,
                'half-yearly', 'half_yearly', 'half yearly', 'semiannual' => 6,
                'annual', 'yearly' => 12,
                default => null,
            };

            if ($interval === null) {
                throw ValidationException::withMessages([
                    'items' => "Unsupported fee cycle '{$item->cycle_type}' for {$item->head_name}.",
                ]);
            }

            $expected = (int) ceil($months / $interval);
            if ((int) $item->installments_count !== $expected) {
                throw ValidationException::withMessages([
                    'items' => "Fee cycle for {$item->head_name} expects {$item->installments_count} installments, but the academic year requires {$expected}. Check the Fee Cycle settings.",
                ]);
            }

            $existing = FeeInstallment::where('fee_structure_item_id', $item->id)->count();
            if ($existing > 0) {
                // Never regenerate a partially created or already billed schedule.
                if ($existing !== $expected) {
                    throw ValidationException::withMessages([
                        'items' => "{$item->head_name} already has {$existing} of {$expected} installments. Review existing dues before repairing its schedule.",
                    ]);
                }
                continue;
            }

            for ($i = 0; $i < $expected; $i++) {
                $periodStart = $start->addMonthsNoOverflow($i * $interval);
                $nextStart = $start->addMonthsNoOverflow(($i + 1) * $interval);
                $periodEnd = $nextStart->subDay();
                if ($periodEnd->gt($end)) {
                    $periodEnd = $end;
                }

                FeeInstallment::create([
                    'school_id' => $structure->school_id,
                    'fee_structure_id' => $structure->id,
                    'fee_structure_item_id' => $item->id,
                    'installment_name' => $item->head_name . ' - ' . $periodStart->format('M Y')
                        . ($interval > 1 ? ' to ' . $periodEnd->format('M Y') : ''),
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                    // Default due date: first day of the installment period.
                    // Adjust this rule to match the school's payment policy.
                    'due_date' => $periodStart->toDateString(),
                    'amount' => $item->amount,
                    'sort_order' => $i + 1,
                    'status' => 1,
                ]);
                $created++;
            }
        }

        return $created;
    }
}
