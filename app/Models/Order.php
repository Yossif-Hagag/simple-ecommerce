<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'total_price', 'status', 'payment_method', 'shipping_address',
        'contact_number', 'email', 'name'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complete()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();
    }

    public function cancel()
    {
        $this->status = self::STATUS_CANCELED;
        $this->save();
    }
}
