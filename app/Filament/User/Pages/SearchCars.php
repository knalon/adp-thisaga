<?php

namespace App\Filament\User\Pages;

use App\Models\Car;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SearchCars extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $slug = 'search-cars';
    protected static ?string $navigationLabel = 'Search Cars';
    protected static ?string $title = 'Search Available Cars';
    protected static ?string $navigationGroup = 'My Cars';
    protected static ?int $navigationSort = 5;

    public ?array $data = [];
    public Collection $results;
    public bool $isSearchPerformed = false;

    public function mount(): void
    {
        $this->form->fill();
        $this->results = collect();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Search Filters')
                    ->description('Use the filters below to find cars that match your criteria.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('make')
                                    ->placeholder('Any make')
                                    ->maxLength(255),
                                
                                TextInput::make('model')
                                    ->placeholder('Any model')
                                    ->maxLength(255),
                                
                                Select::make('registration_year')
                                    ->placeholder('Any year')
                                    ->options(
                                        collect(range(date('Y'), 1990))
                                            ->mapWithKeys(fn ($year) => [$year => $year])
                                    ),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('min_price')
                                    ->label('Min Price ($)')
                                    ->placeholder('Min price')
                                    ->numeric(),
                                
                                TextInput::make('max_price')
                                    ->label('Max Price ($)')
                                    ->placeholder('Max price')
                                    ->numeric(),
                            ]),
                        
                        Select::make('transmission')
                            ->placeholder('Any transmission')
                            ->options([
                                'automatic' => 'Automatic',
                                'manual' => 'Manual',
                                'cvt' => 'CVT',
                                'semi-automatic' => 'Semi-Automatic',
                            ]),
                        
                        Select::make('fuel_type')
                            ->placeholder('Any fuel type')
                            ->options([
                                'petrol' => 'Petrol',
                                'diesel' => 'Diesel',
                                'hybrid' => 'Hybrid',
                                'electric' => 'Electric',
                                'lpg' => 'LPG',
                                'other' => 'Other',
                            ]),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function search(): void
    {
        $this->validate();
        
        $query = Car::query()
            ->where('is_active', true)
            ->where('is_approved', true);
        
        // Apply filters
        if (!empty($this->data['make'])) {
            $query->where('make', 'like', '%' . $this->data['make'] . '%');
        }
        
        if (!empty($this->data['model'])) {
            $query->where('model', 'like', '%' . $this->data['model'] . '%');
        }
        
        if (!empty($this->data['registration_year'])) {
            $query->where('registration_year', $this->data['registration_year']);
        }
        
        if (!empty($this->data['min_price'])) {
            $query->where('price', '>=', $this->data['min_price']);
        }
        
        if (!empty($this->data['max_price'])) {
            $query->where('price', '<=', $this->data['max_price']);
        }
        
        if (!empty($this->data['transmission'])) {
            $query->where('transmission', $this->data['transmission']);
        }
        
        if (!empty($this->data['fuel_type'])) {
            $query->where('fuel_type', $this->data['fuel_type']);
        }
        
        $this->results = $query->with('user')->get();
        $this->isSearchPerformed = true;
    }

    public function saveSearch(): void
    {
        $this->notify('success', 'Search saved successfully. You will receive notifications when new cars match your criteria.');
    }

    public function render(): View
    {
        return view('filament.user.pages.search-cars');
    }
} 