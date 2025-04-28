<?php

namespace App\Filament\User\Widgets;

use App\Models\Car;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Widgets\Widget;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\ViewAction;

class CarSearchWidget extends Widget
{
    protected static string $view = 'filament.user.widgets.car-search-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = Auth::user();

        $latestCars = Car::where('is_active', true)
            ->where('is_approved', true)
            ->where('is_sold', false)
            ->where('user_id', '!=', $user->id)
            ->latest()
            ->take(6)
            ->get();

        $makes = Car::select('make')->distinct()->pluck('make');
        $minYear = Car::min('registration_year') ?: 2000;
        $maxYear = Car::max('registration_year') ?: date('Y');
        $minPrice = Car::min('price') ?: 0;
        $maxPrice = Car::max('price') ?: 100000;

        return [
            'latestCars' => $latestCars,
            'makes' => $makes,
            'years' => range($minYear, $maxYear),
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ];
    }

    public function searchAction(): Action
    {
        return Action::make('search')
            ->label('Search Cars')
            ->icon('heroicon-m-magnifying-glass')
            ->form([
                Grid::make(2)->schema([
                    Select::make('make')
                        ->label('Make')
                        ->options(Car::select('make')->distinct()->pluck('make', 'make'))
                        ->searchable(),

                    Select::make('model')
                        ->label('Model')
                        ->options(function ($get) {
                            $make = $get('make');
                            if (!$make) return [];

                            return Car::where('make', $make)
                                ->select('model')
                                ->distinct()
                                ->pluck('model', 'model');
                        })
                        ->searchable()
                        ->disabled(fn ($get) => !$get('make')),

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

                    TextInput::make('min_price')
                        ->label('Min Price')
                        ->numeric()
                        ->prefix('$'),

                    TextInput::make('max_price')
                        ->label('Max Price')
                        ->numeric()
                        ->prefix('$'),
                ])
            ])
            ->action(function (array $data) {
                // Redirect to cars page with search filters
                return redirect()->route('filament.user.pages.browse-cars', [
                    'make' => $data['make'] ?? null,
                    'model' => $data['model'] ?? null,
                    'min_year' => $data['min_year'] ?? null,
                    'max_year' => $data['max_year'] ?? null,
                    'min_price' => $data['min_price'] ?? null,
                    'max_price' => $data['max_price'] ?? null,
                ]);
            });
    }
}
