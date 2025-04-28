<?php

namespace App\Filament\User\Pages;

use App\Models\Car;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BrowseCars extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'Browse Cars';
    protected static ?string $navigationGroup = 'My Cars';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.user.pages.browse-cars';
    protected static ?string $slug = 'browse-cars';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Car::query()
                    ->where('is_active', true)
                    ->where('is_approved', true)
                    ->where('is_sold', false)
                    ->where('user_id', '!=', Auth::id())
            )
            ->columns([
                ImageColumn::make('car_image')
                    ->label('Image')
                    ->state(fn (Car $record): string => $record->getFirstMediaUrl('car_images'))
                    ->defaultImageUrl(fn () => asset('images/car-placeholder.jpg'))
                    ->circular(false)
                    ->square(),
                TextColumn::make('make')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('registration_year')
                    ->label('Year')
                    ->sortable(),
                ColorColumn::make('color'),
                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('mileage')
                    ->label('Mileage')
                    ->suffix(' miles')
                    ->sortable(),
                TextColumn::make('transmission')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('fuel_type')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            ])
            ->filters([
                // Filters are automatically handled by the form
            ])
            ->actions([
                Action::make('view')
                    ->url(fn (Car $record): string => route('filament.user.resources.my-bid-resource.create', ['car_id' => $record->id]))
                    ->label('Make Bid')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('secondary'),
                Action::make('schedule')
                    ->url(fn (Car $record): string => route('filament.user.resources.my-appointment-resource.create', ['car_id' => $record->id]))
                    ->label('Schedule Test Drive')
                    ->icon('heroicon-o-calendar')
                    ->color('primary'),
            ])
            ->bulkActions([])
            ->paginated([10, 25, 50, 100]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    Select::make('make')
                        ->label('Make')
                        ->options(Car::select('make')->distinct()->pluck('make', 'make'))
                        ->searchable()
                        ->live(),

                    Select::make('model')
                        ->label('Model')
                        ->options(function (callable $get) {
                            $make = $get('make');
                            if (!$make) return [];

                            return Car::where('make', $make)
                                ->select('model')
                                ->distinct()
                                ->pluck('model', 'model');
                        })
                        ->searchable()
                        ->disabled(fn (callable $get) => !$get('make')),

                    Grid::make(2)->schema([
                        Select::make('min_year')
                            ->label('Min Year')
                            ->options(function () {
                                $minYear = Car::min('registration_year') ?: 2000;
                                $maxYear = Car::max('registration_year') ?: date('Y');
                                return array_combine(range($minYear, $maxYear), range($minYear, $maxYear));
                            }),

                        Select::make('max_year')
                            ->label('Max Year')
                            ->options(function () {
                                $minYear = Car::min('registration_year') ?: 2000;
                                $maxYear = Car::max('registration_year') ?: date('Y');
                                return array_combine(range($minYear, $maxYear), range($minYear, $maxYear));
                            }),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('min_price')
                            ->label('Min Price')
                            ->numeric()
                            ->prefix('$'),

                        TextInput::make('max_price')
                            ->label('Max Price')
                            ->numeric()
                            ->prefix('$'),
                    ]),

                    Select::make('transmission')
                        ->label('Transmission')
                        ->options([
                            'automatic' => 'Automatic',
                            'manual' => 'Manual',
                            'cvt' => 'CVT',
                            'semi-automatic' => 'Semi-Automatic',
                        ])
                        ->searchable(),

                    Select::make('fuel_type')
                        ->label('Fuel Type')
                        ->options([
                            'petrol' => 'Petrol',
                            'diesel' => 'Diesel',
                            'hybrid' => 'Hybrid',
                            'electric' => 'Electric',
                            'lpg' => 'LPG',
                            'other' => 'Other',
                        ])
                        ->searchable(),
                ])
            ]);
    }

    public function filter(): void
    {
        $this->tableFilters();
    }

    public function tableFilters(): array
    {
        $data = $this->form->getState();
        $this->data = $data;

        $filters = [];

        if (!empty($data['make'])) {
            $filters[] = function (Builder $query) use ($data) {
                $query->where('make', $data['make']);
            };
        }

        if (!empty($data['model'])) {
            $filters[] = function (Builder $query) use ($data) {
                $query->where('model', $data['model']);
            };
        }

        if (!empty($data['min_year'])) {
            $filters[] = function (Builder $query) use ($data) {
                $query->where('registration_year', '>=', $data['min_year']);
            };
        }

        if (!empty($data['max_year'])) {
            $filters[] = function (Builder $query) use ($data) {
                $query->where('registration_year', '<=', $data['max_year']);
            };
        }

        if (!empty($data['min_price'])) {
            $filters[] = function (Builder $query) use ($data) {
                $query->where('price', '>=', $data['min_price']);
            };
        }

        if (!empty($data['max_price'])) {
            $filters[] = function (Builder $query) use ($data) {
                $query->where('price', '<=', $data['max_price']);
            };
        }

        if (!empty($data['transmission'])) {
            $filters[] = function (Builder $query) use ($data) {
                $query->where('transmission', $data['transmission']);
            };
        }

        if (!empty($data['fuel_type'])) {
            $filters[] = function (Builder $query) use ($data) {
                $query->where('fuel_type', $data['fuel_type']);
            };
        }

        return $filters;
    }
}
