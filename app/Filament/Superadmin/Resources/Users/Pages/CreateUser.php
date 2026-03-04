<?php

namespace App\Filament\Superadmin\Resources\Users\Pages;

use App\Filament\Superadmin\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     if (isset($data['password_strategy']) && $data['password_strategy'] === 'send_link') {
    //         $data['password'] = Hash::make(Str::random(32));
    //     }

    //     unset($data['password_strategy']);

    //     return $data;
    // }

    // protected function afterCreate(): void
    // {
    //     $user = $this->record;

    //     if (isset($this->data['password_strategy']) && $this->data['password_strategy'] === 'send_link') {
            
    //         // Generate the token
    //         $token = Password::broker()->createToken($user);

    //         // Send standard email (Laravel will now successfully use the route in web.php!)
    //         $user->sendPasswordResetNotification($token);
    //     }
    // }
}
