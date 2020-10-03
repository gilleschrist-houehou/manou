<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $courrier_code
 * @property string $uniteadmin_code
 * @property string $agent_code
 * @property string $fonctionagent_code
 * @property string $image
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class Signaturenoteexplicative extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['courrier_code', 'uniteadmin_code', 'agent_code', 'fonctionagent_code', 'image', 'created_by', 'updated_by', 'created_at', 'updated_at'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fonction()
    {
        return $this->belongsTo('App\Models\Fonctionagent', 'fonctionagent_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo('App\Models\Agent', 'agent_code', 'id');
    }

}
