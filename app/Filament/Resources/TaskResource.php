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
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

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
                                            ->label('Krāsa'),
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
                                                            ->extraInputAttributes(
                                                                [
                                                                    'class' => 'teeth-type',
                                                                    // 'id' => 'your-select-element-id'
                                                                ]
                                                            )
                                                            ->reactive()
                                                            ->afterStateUpdated(function (Get $get, Set $set, $livewire) {
                                                                static::calculateTotalPrice($get, $set);

                                                                // $livewire->dispatch('select-updated', [
                                                                //     'selectId' => 'your-select-element-id',
                                                                // ]);
                                                            })
                                                            ->columnSpanFull()
                                                            ->required(),
                                                        Select::make('teeth_position')
                                                            ->label('Zobs')
                                                            ->options(TeethPosition::class)
                                                            ->extraInputAttributes(['class' => 'teeth-position'])
                                                            ->required(),
                                                        Select::make('material')
                                                            ->label('Matereāls')
                                                            ->extraInputAttributes(['class' => 'teeth-material'])
                                                            ->options(Material::class)
                                                            ->required(),
                                                    ])
                                            ])
                                            ->grid(1)
                                            ->collapsed()
                                            ->defaultItems(0)
                                            ->itemLabel(function (array $state) {
                                                $label = '';

                                                if (!empty($state['type'])) {
                                                    $label .= Type::from($state['type'])->getLabel();
                                                }

                                                if (!empty($state['teeth_position'])) {
                                                    $label .= ' / ' . TeethPosition::from($state['teeth_position'])->getLabel();
                                                }

                                                if (!empty($state['material'])) {
                                                    $label .= ' / ' . Material::from($state['material'])->getLabel();
                                                }

                                                if (
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
                                    ->maxValue(5)
                                    ->step(1)
                                    ->reactive()
                                    ->default(0)
                                    ->visible(fn(Get $get): bool => empty($get('3d_models')))
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        if ($get('scan_models') > 0) {
                                            $set('3d_models', 0);
                                            $set('3d_models_full', false);
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
                                    ->default(0)
                                    ->visible(fn(Get $get): bool => empty($get('scan_models')))
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        if ($get('3d_models') > 0) {
                                            $set('scan_models', 0);
                                        } elseif ($get('3d_models') == 0) {
                                            $set('3d_models_full', false);
                                        }
                                        static::calculateTotalPrice($get, $set);
                                    }),
                                Toggle::make('3d_models_full')
                                    ->label('Pilns 3D modelis')
                                    ->reactive()
                                    ->visible(fn(Get $get): bool => $get('3d_models') != 0)
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
            ->defaultSort('created_at', 'desc')
            ->paginated([100, 200, 500, 'all'])
            ->defaultPaginationPageOption(100)
            ->filters([])
            ->actions([
                Action::make('viewSections')
                    ->label('Apskatīt')
                    ->icon('heroicon-o-eye')
                    ->action(function ($record, $livewire) {
                        $livewire->emit('openSectionsModal', $record->sections);
                    })
                    ->modalHeading('Paveiktie darbi')
                    ->modalWidth('lg')
                    ->modalContent(function ($record) {
                        return view('task-resource.sections-view', ['sections' => $record->sections]);
                    }),

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
