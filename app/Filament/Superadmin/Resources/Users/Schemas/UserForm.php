<?php

namespace App\Filament\Superadmin\Resources\Users\Schemas;

use Dom\Text;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            TextInput::make('name')->required(),
            TextInput::make('email')->email()->required()->unique(),
            
            // Status Select
            Toggle::make('is_active')
                ->label('Active')
                ->onColor('success')
                ->offColor('danger')
                ->default(true)
                ->required(),
            
            Select::make('role')
                ->relationship('roles', 'name')
                ->default(fn () => Role::where('name', 'admin')->first()->id)
                ->required(),

            TextInput::make('password')
                ->label('Password')
                ->required()
                ->password(),

            TextInput::make('password_confirmation')
                ->label('Confirm Password')
                ->password()
                ->required()
                ->dehydrated(false) // Don't save this to the database
                ->same('password'), // Ensure it matches the password field
                
            

            // Section::make('Credentials')
            //     ->schema([
            //         // Choice between Manual or Email
            //         Select::make('password_strategy')
            //             ->label('Password Method')
            //             ->options([
            //                 'assign' => 'Manually Assign Password',
            //                 'send_link' => 'Send Reset Link via Email',
            //             ])
            //             ->default('send_link')
            //             ->live(), // This field isn't in the database

            //         // Only show if 'assign' is selected
            //         TextInput::make('password')
            //             ->password()
            //             ->required(fn (Get $get) => $get('password_strategy') === 'assign')
            //             ->visible(fn (Get $get) => $get('password_strategy') === 'assign')
            //             // We only need to hash the state if they manually typed one in
            //             ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            //             // Only dehydrate if the field has a value
            //             ->dehydrated(fn ($state) => filled($state)),
                        
            //         // Information text if 'send_link' is selected
            //         Placeholder::make('info')
            //             ->content('The user will receive an email to set their own password.')
            //             ->visible(fn (Get $get) => $get('password_strategy') === 'send_link'),
            // ])
        ]);
    }
}
