<?php


namespace Jakmall\Recruitment\Calculator\History;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['command', 'description', 'result', 'output'];
}
