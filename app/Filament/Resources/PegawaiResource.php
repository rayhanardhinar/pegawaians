<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Filament\Resources\PegawaiResource\RelationManagers;
use App\Models\Pegawai;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Pegawai';

    protected static ?string $navigationGroup = 'Manage';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    TextInput::make('name'),
                TextInput::make('email')
                    ->email(),
                TextInput::make('phone')
                    ->integer() ,
                TextInput::make('address'),
                Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->preload()
                    ->required(),
                Select::make('position')
                    ->options([
                        'staff' => 'Staff',
                        'manager' => 'Manager',
                        'admin' => 'Admin',
                    ]),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'deactive' => 'Deactive',
                        'probation' => 'Probation',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('phone'),
                TextColumn::make('address'),
                TextColumn::make('department.name'),
                TextColumn::make('position')
                    ->label('Posisi')
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
                BadgeColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn (string $state) => match ($state) {
                    'active' => 'Aktif',
                    'deactive' => 'Tidak Aktif',
                    'probation' => 'Percobaan',
                    default => ucfirst($state),
                })
                ->colors([
                    'success' => 'active',
                    'danger' => 'deactive',
                    'warning' => 'probation',
                ])
                ->icons([
                    'heroicon-o-check-circle' => 'active',
                    'heroicon-o-x-circle' => 'deactive',
                    'heroicon-o-clock' => 'probation',
                ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
