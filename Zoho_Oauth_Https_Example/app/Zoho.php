<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zoho extends Model
{
    protected $table = 'Users';
    protected $primaryKey='id';
    protected $fillable = ['refresh_token', 'accces_token', 'api_domain', 'expire_time','account_server'];
}
