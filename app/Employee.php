<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable  as AuditableContract;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;
    protected $guarded = [];

    protected $auditInclude = [
        'empId', 
        'name', 
        'dept',
        'location', 
        'address1', 
        'address2',
        'expireDate', 
        'cid', 
        'isNotActive',
    ];

    public function blacklists()
    {
        return $this->hasOne('App\Blacklist','employee_id', 'empId');
    }

    public function priorities()
    {
        return $this->hasOne('App\Priority','employee_id', 'empId', 'id');
    }

    public function bookings()
    {
        return $this->hasMany('App\Booking','employee_id', 'empId');
    }

    public function feedbacks()
    {
        return $this->hasMany('App\Feedback');
    }

    public function ptype()
    {
        $priority = \App\Priority::where("employee_id", $this->empId)->first();
        return $priority ? $priority->priority_type : null;
    }
}