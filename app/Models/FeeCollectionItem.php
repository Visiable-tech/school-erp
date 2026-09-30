<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeCollectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'fee_collection_id',
        'student_fee_due_id',

        'amount',

        'fee_head_name',
        'installment_name',
        'due_date',
        'due_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function collection()
    {
        return $this->belongsTo(
            FeeCollection::class,
            'fee_collection_id'
        );
    }

    public function due()
    {
        return $this->belongsTo(
            StudentFeeDue::class,
            'student_fee_due_id'
        );
    }

    public function refundItems(){ return $this->hasMany(\App\Models\FeeRefundItem::class,'fee_collection_item_id'); }
}