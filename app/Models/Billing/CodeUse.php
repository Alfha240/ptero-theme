<?php

namespace Pterodactyl\Models\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pterodactyl\Models\User;

class CodeUse extends Model
{
    use HasFactory;

    protected $table = 'billing_code_uses';

    protected $fillable = [
        'code_id',
        'user_id',
    ];

    public function code()
    {
        return $this->belongsTo(RedeemCode::class, 'code_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
