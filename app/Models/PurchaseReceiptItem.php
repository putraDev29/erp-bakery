<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_receipt_id',
        'purchase_item_id',
        'ingredient_id',
        'quantity_received',
        'unit_cost',
        'batch_number',
        'expiry_date',
    ];

    protected $casts = [
        'quantity_received' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceipt::class, 'purchase_receipt_id');
    }

    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
