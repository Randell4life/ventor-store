<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatingExpense extends Model
{
    protected $fillable = [
        'created_by',
        'category',
        'description',
        'amount',
        'expense_date',
        'status',
        'payment_method',
        'paid_at',
        'reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
            'paid_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (OperatingExpense $expense): void {
            $expense->created_by ??= auth()->id();
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public static function categories(): array
    {
        return [
            'rent' => 'ქირა',
            'salary' => 'ხელფასები',
            'advertising' => 'რეკლამა და მარკეტინგი',
            'transport' => 'ტრანსპორტირება',
            'utilities' => 'კომუნალური',
            'taxes' => 'გადასახადები',
            'fees' => 'საბანკო და მომსახურების საკომისიო',
            'packaging' => 'შეფუთვა',
            'maintenance' => 'ტექნიკური მომსახურება',
            'other' => 'სხვა',
        ];
    }

    public static function paymentMethods(): array
    {
        return [
            'bank_transfer' => 'საბანკო გადარიცხვა',
            'cash' => 'ნაღდი ფული',
            'card' => 'ბარათი',
            'other' => 'სხვა',
        ];
    }
}
