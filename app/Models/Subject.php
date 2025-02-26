<?php
// app/Models/Subject.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name'];

    // public function course()
    // {
    //     return $this->belongsTo(Course::class);
    // }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}