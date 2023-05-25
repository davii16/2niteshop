<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PgbRelaHome
 *
 * @property int $id
 * @property int $pgb_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PageBuilder $pgb_data
 * @method static \Illuminate\Database\Eloquent\Builder|PgbRelaHome newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PgbRelaHome newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PgbRelaHome query()
 * @mixin \Eloquent
 */
class PgbRelaHome extends Model
{
    use HasFactory;
    protected $table = "pgb_rela_home";
    protected $fillable = [

        'pgb_id',
    ];

    public function pgb_data()
    {
        return $this->belongsTo("App\Models\PageBuilder", 'pgb_id', 'id');
    }
}
