<?php

namespace Hxc\HxcLaravelTool\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    use HasFactory;

    protected $fillable = ['phone','content','scene','ip','status','end_time','code'];
}