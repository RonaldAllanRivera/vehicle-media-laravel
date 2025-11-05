<?php

namespace App\Filament\Pages;

use App\Services\VehicleMediaResolver;
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
            'source' => config('vehicle_media.source', 'json'),
        ]);
    }

    protected function getFormSchema(): array
    {
        $years = array_combine(range(2025, 1999), range(2025, 1999));

        return [
            Forms\Components\Select::make('source')
                ->label('Source')
                ->options([
                    'json' => 'Local JSON',
                    'db' => 'Local DB',
                    'api' => 'Live API',
                ])
                ->hint('Local DB: reads from seeded database. Local JSON: hits this app\'s /mock/vehicle-media endpoint (no API credits). Live API: calls Vehicle Databases API (rate limits apply).')
                ->hintIcon('heroicon-m-question-mark-circle')
                ->default(fn () => config('vehicle_media.source', 'json'))
                ->native(false),
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
        $resolver = app(VehicleMediaResolver::class);
        $this->result = $resolver->getMedia(
            (int) $data['year'],
            (string) $data['make'],
            (string) $data['model'],
            (string) $data['trim'],
            ['source' => $data['source'] ?? config('vehicle_media.source', 'json')]
        );
    }

    public function getGridCards(): array
    {
        $cards = [];
        $res = $this->result;
        if (! is_array($res) || (($res['status'] ?? null) === 'error')) {
            // show 15 samples to preserve the layout when no data yet
            for ($i = 0; $i < 15; $i++) {
                $cards[] = [
                    'url' => 'https://placehold.co/600x600?text=Sample',
                    'category' => 'sample',
                    'year' => null,
                    'make' => null,
                    'model' => null,
                    'trim' => null,
                ];
            }
            return $cards;
        }

        $data = $res['data'] ?? [];
        $images = $data['images'] ?? [];
        $year = $data['year'] ?? null;
        $make = $data['make'] ?? null;
        $model = $data['model'] ?? null;
        $trim = $data['trim'] ?? null;

        foreach (['exterior', 'interior', 'colors'] as $cat) {
            foreach (($images[$cat] ?? []) as $u) {
                $cards[] = [
                    'url' => $u,
                    'category' => $cat,
                    'year' => $year,
                    'make' => $make,
                    'model' => $model,
                    'trim' => $trim,
                ];
            }
        }

        if (count($cards) < 15) {
            for ($i = count($cards); $i < 15; $i++) {
                $cards[] = [
                    'url' => 'https://placehold.co/600x600?text=Sample',
                    'category' => 'sample',
                    'year' => $year,
                    'make' => $make,
                    'model' => $model,
                    'trim' => $trim,
                ];
            }
        }

        return array_slice($cards, 0, 15);
    }
}

