<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;


class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;
	/**
     * The database table used by the model.
     *
     * @var string
     */
    /**
     * The primary key for the model.
     *
     * @var string
     */
	protected $primaryKey = 'id';
    public $timestamps = true;

/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 protected $fillable = [
         'email', 'password','profil_code','agent_code','created_by','update_by', "password_reset_code", "password_reset_expiration", "password_reset_used"
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password'];


// Récupération de l'unité administrative de l'agent
    public function profil(){
        return $this->belongsTo('App\Models\Profil','profil_code','code');
    }

    public function agent(){
        return $this->belongsTo('App\Models\Agent','agent_code', 'code');
    }



    public function getLibelleUaAttribute(){
        $listeprofil = Profil::all();
        foreach ($listeprofil as $pr) {
            if($this->idprofil==$pr->id)
                return $pr->libelle;
        }
    }

}
