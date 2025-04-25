<?php

namespace App\Filament\User\Pages;

use App\Models\Appointment;
use App\Models\Car;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class CarDetails extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $slug = 'car-details/{id}';
    protected static ?string $title = 'Car Details';
    protected static bool $shouldRegisterNavigation = false;

    public Car $car;
    public ?array $data = [];

    public function mount(string $id): void
    {
        $this->car = Car::with(['user', 'media'])->findOrFail($id);
        
        if (!$this->car->is_active || !$this->car->is_approved) {
            $this->redirect('/dashboard/search-cars');
        }
        
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Schedule a Test Drive')
                    ->description('Request a test drive for this vehicle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('appointment_date')
                                    ->label('Preferred Date & Time')
                                    ->required()
                                    ->minDate(now()->addHour())
                                    ->seconds(false),
                                
                                TextInput::make('bid_price')
                                    ->label('Your Bid (Optional)')
                                    ->helperText('Enter your bid amount if you wish to make an offer')
                                    ->prefix('$')
                                    ->numeric(),
                            ]),
                        
                        Textarea::make('notes')
                            ->label('Additional Notes')
                            ->placeholder('Any specific questions or requests?'),
                    ]),
            ])
            ->statePath('data');
    }

    public function scheduleDrive(): void
    {
        $data = $this->form->getState();
        
        // Check if user already has an appointment for this car
        $existingAppointment = Appointment::where('user_id', Auth::id())
            ->where('car_id', $this->car->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();
        
        if ($existingAppointment) {
            Notification::make()
                ->title('You already have an appointment for this car')
                ->body('Please check your appointments page to see the status.')
                ->warning()
                ->send();
            
            return;
        }
        
        // Create the appointment
        Appointment::create([
            'user_id' => Auth::id(),
            'car_id' => $this->car->id,
            'appointment_date' => $data['appointment_date'],
            'bid_price' => $data['bid_price'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);
        
        Notification::make()
            ->title('Test Drive Scheduled')
            ->body('Your test drive request has been submitted and is pending approval.')
            ->success()
            ->send();
        
        $this->redirect("/dashboard/appointments");
    }

    public function getViewData(): array
    {
        return [
            'car' => $this->car,
            'images' => $this->car->getMedia('car_images'),
        ];
    }

    public function render(): View
    {
        return view('filament.user.pages.car-details', $this->getViewData());
    }
} 