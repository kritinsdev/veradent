<?php

namespace App\Filament\Resources;

use App\Enums\Material;
use App\Enums\TeethPosition;
use App\Enums\Type;
use App\Filament\Resources\TaskResource\Pages;
use App\Models\Doctor;
use App\Models\Task;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $pluralModelLabel = 'Darbi';
    protected static ?string $modelLabel = 'Darbs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)
                    ->schema([
                        Grid::make()
                            ->schema([
                                Section::make('Informācija')
                                    ->schema([
                                        Select::make('doctor_id')
                                            ->label('Ārsts')
                                            ->options(Doctor::all()->pluck('name', 'id'))
                                            ->searchable()
                                            ->required(),
                                        TextInput::make('patient_name')
                                            ->label('Pacients'),
                                        TextInput::make('color')
                                            ->label('Krāsa')
                                            ->required(),
                                    ]),
                            ])
                            ->columnSpan(3),

                        Grid::make()
                            ->schema([
                                Section::make('Veiktie darbi')
                                    ->schema([
                                        Repeater::make('sections')
                                            ->relationship()
                                            ->label('Darbi')
                                            ->schema([
                                                Grid::make()
                                                    ->schema([
                                                        Select::make('type')
                                                            ->label('Tips')
                                                            ->options(Type::class)
                                                            ->reactive()
                                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                                static::calculateTotalPrice($get, $set);
                                                            })
                                                            ->columnSpanFull()
                                                            ->required(),
                                                        Select::make('teeth_position')
                                                            ->label('Zobs')
                                                            ->options(TeethPosition::class)
                                                            ->required(),
                                                        Select::make('material')
                                                            ->label('Matereāls')
                                                            ->options(Material::class)
                                                            ->required(),
                                                    ])
                                            ])
                                            ->grid(1)
                                            ->collapsed()
                                            ->defaultItems(1)
                                            ->itemLabel(function (array $state) {
                                                $label = '';

                                                if (!empty($state['type'])) {
                                                    $label .= Type::from($state['type'])->getLabel();
                                                }

                                                if(!empty($state['teeth_position'])) {
                                                    $label .= ' / ' . 'B12';
                                                }

                                                if(!empty($state['material'])) {
                                                    $label .= ' / ' . Material::from($state['material'])->getLabel();
                                                }

                                                if(
                                                    !empty($state['type']) &&
                                                    !empty($state['teeth_position']) &&
                                                    !empty($state['material'])
                                                ) {
                                                    return $label;
                                                }

                                                return 'Jauns darbs';
                                            })
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                static::calculateTotalPrice($get, $set);
                                            })
                                    ])

                            ])
                            ->columnSpan(6),

                        Section::make('Papildus Informācija')
                            ->schema([
                                TextInput::make('scan_models')
                                    ->label('Skenētie modeļi')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(0)
                                    ->maxValue(2)
                                    ->step(1)
                                    ->reactive()
                                    ->hidden(fn(Get $get): bool =>  !empty($get('3d_models')))
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        if ($get('scan_models') > 0) {
                                            $set('3d_models', null);
                                            $set('3d_models_full', null);
                                        }
                                        static::calculateTotalPrice($get, $set);
                                    }),
                                TextInput::make('3d_models')
                                    ->label('3D Modeļi')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(0)
                                    ->step(1)
                                    ->reactive()
                                    ->hidden(fn(Get $get): bool =>  !empty($get('scan_models')))
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        if ($get('3d_models') > 0) {
                                            $set('scan_models', null);
                                        } elseif ($get('3d_models') == 0) {
                                            $set('3d_models_full', null);
                                        }
                                        static::calculateTotalPrice($get, $set);
                                    }),
                                Toggle::make('3d_models_full')
                                    ->label('Pilns 3D modelis')
                                    ->reactive()
                                    ->hidden(fn(Get $get): bool => $get('3d_models') == 0)
                                    ->dehydrated(fn(Get $get): bool => $get('3d_models') > 0)
                                    ->required(fn(Get $get): bool => $get('3d_models') > 0)
                                    ->afterStateUpdated(fn(Get $get, Set $set) => static::calculateTotalPrice($get, $set)),
                                TextInput::make('total_price')
                                    ->label('Samaksa')
                                    ->numeric()
                                    ->suffix('€')
                                    ->readOnly()
                            ])
                            ->columnSpan(3)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Nr.'),
                TextColumn::make('doctor.name')
                    ->label('Ārsts')
                    ->searchable(),
                TextColumn::make('patient_name')
                    ->label('Pacients')
                    ->searchable(),
                TextColumn::make('sections_count')
                    ->label('Veiktie darbi')
                    ->counts('sections'),
                TextColumn::make('total_price')
                    ->label('Cena')
                    ->suffix('€'),
                TextColumn::make('created_at')
                    ->label('Izveidots')
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->format('d/m/Y');
                    }),
            ])
            ->filters([

            ])
            ->actions([
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    protected static function calculateTotalPrice(Get $get, Set $set): void
    {
        $totalPrice = 0;

        if (!empty($get('scan_models'))) {
            $totalPrice = $get('scan_models') * 3;
        }

        if (!empty($get('3d_models'))) {
            $multiplier = !empty($get('3d_models_full')) ? 3 : 1.5;
            $totalPrice = $get('3d_models') * $multiplier;
        }

        $sections = $get('sections') ?? [];

        foreach ($sections as $section) {
            if (isset($section['type'])) {
                $type = Type::from($section['type']);
                $totalPrice += $type->price();
            }
        }

        $set('total_price', $totalPrice);
    }
}
