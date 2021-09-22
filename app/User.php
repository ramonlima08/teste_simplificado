<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','user_type','cpf_cnpj'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // Regra de negócio
    // O ideal seria estar no BD
    static public function getPermissions(User $user){
        //ideal seria buscar essas infos do BD
        $permissions = [
            '1'=>"Consultar Saldo", 
            '2'=>"Histórico de Transações",
            '3'=>"Enviar Dinheiro"
        ];
        //ideal seria tratar isso no BD
        if($user->user_type == 2){
            //removendo a chave 3 (Enviar Dinheiro)
            unset($permissions[3]);
        }
        
        return $permissions;
    }
}
