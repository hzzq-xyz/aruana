<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser; // Adicionado para controle de painéis
use Filament\Panel; // Adicionado para controle de painéis
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Adicionado para permitir salvar a função
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Define quem pode acessar cada painel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Se for o painel admin, só entra quem tem role 'admin'
        if ($panel->getId() === 'admin') {
            return $this->role === 'admin';
        }

        // No painel cliente, administradores e clientes podem entrar
        if ($panel->getId() === 'cliente') {
            return in_array($this->role, ['admin', 'cliente']);
        }

        return false;
    }

    /**
     * Configuração do log de atividades
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role']) // Adicionado 'role' no monitoramento
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs() 
            ->useLogName('user') 
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Usuário criado',
                'updated' => 'Usuário atualizado', 
                'deleted' => 'Usuário excluído',
                default => "Usuário {$eventName}"
            });
    }
}