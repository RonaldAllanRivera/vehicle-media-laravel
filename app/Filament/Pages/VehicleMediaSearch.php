<?php

namespace App\Filament\Pages;

use App\Services\VehicleMediaClient;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use UnitEnum;

class VehicleMediaSearch extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected string $view = 'filament.pages.vehicle-media-search';

    protected static UnitEnum|string|null $navigationGroup = 'Vehicle Media';

    protected static ?string $title = 'Vehicle Media Search';

    public ?array $formData = [];

    public ?array $result = null;

    public function mount(): void
    {
        $this->form->fill([
            'year' => 2017,
            'make' => 'Acura',
            'model' => 'ILX',
            'trim' => 'Base 4dr Sedan Automatic',
        ]);
    }

    protected function getFormSchema(): array
    {
        $years = array_combine(range(2025, 1999), range(2025, 1999));

        return [
            Forms\Components\Select::make('year')
                ->label('Year')
                ->options($years)
                ->required(),
            Forms\Components\Select::make('make')
                ->label('Make')
                ->options([
                    'Acura' => 'Acura',
                ])
                ->searchable()
                ->required(),
            Forms\Components\Select::make('model')
                ->label('Model')
                ->options([
                    'ILX' => 'ILX',
                ])
                ->searchable()
                ->required(),
            Forms\Components\Select::make('trim')
                ->label('Trim')
                ->options([
                    'Base 4dr Sedan Automatic' => 'Base 4dr Sedan Automatic',
                ])
                ->searchable()
                ->required(),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'formData';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('search')
                ->label('Search')
                ->color('primary')
                ->action('search'),
        ];
    }

    public function search(): void
    {
        $data = $this->form->getState();
        $client = app(VehicleMediaClient::class);
        $this->result = $client->getMedia((int) $data['year'], (string) $data['make'], (string) $data['model'], (string) $data['trim']);
    }
}

