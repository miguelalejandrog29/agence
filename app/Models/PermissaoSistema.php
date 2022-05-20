<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissaoSistema extends Model
{
    use HasFactory;

    protected $table = 'permissao_sistema';

    public $timestamps = false;

    protected $fillable = [];
}
