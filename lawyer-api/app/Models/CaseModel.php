<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseModel extends Model
{
    use SoftDeletes;

    const ALLOWED_STATUSES = ['OPEN', 'CLOSED', 'PENDING'];
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_OPEN = 'OPEN';
    const STATUS_PENDING = 'PENDING';

    const STATUS_LABEL = [
        'OPEN' => 'success',
        'PENDING' => 'primary',
        'CLOSED' => 'default',
    ];

    protected $table = 'cases';

    protected $fillable = [
        'number',
        'title',
        'message',
        'user_id',
        'city_id',
        'language_id',
        'competency_id',
        'status',
        'is_reopen'
    ];


    public function caseAttachments()
    {
        return $this->hasMany(CaseAttachment::class, 'case_id', 'id');
    }

    public function lawyers()
    {
        return $this->hasMany(LawyerCase::class, 'case_id', 'id');
    }

    public function suggested()
    {
        return $this->hasMany(LawyerCase::class, 'case_id', 'id')->where('status', LawyerCase::STATUS_SUGGESTED);
    }

    public function accepted()
    {
        return $this->hasMany(LawyerCase::class, 'case_id', 'id')->where('status', LawyerCase::STATUS_ACCEPTED);
    }

    public function requested()
    {
        return $this->hasMany(LawyerCase::class, 'case_id', 'id')->where('status', LawyerCase::STATUS_REQUESTED);
    }


    public function caseActions()
    {
        return $this->hasMany(CaseAction::class, 'case_id', 'id');
    }

    public function lawyerCaseHistory()
    {
        return $this->hasMany(LawyerCaseHistory::class, 'case_id', 'id');
    }

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }


    public function isClosed()
    {
        return $this->status == 'CLOSED';
    }

    public function isPending()
    {
        return $this->status == 'PENDING';
    }

    public function isOpen()
    {
        return $this->status == 'OPEN';
    }
}
