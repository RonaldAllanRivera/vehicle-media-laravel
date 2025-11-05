# HOWTO: Vehicle Media Admin

This guide covers common tasks: switching sources, seeding the DB, using the mock JSON API, adding new vehicles, changing the JSON base domain, and troubleshooting.

## 1) Configure the data source

Edit `.env`:

```
VEHICLE_MEDIA_SOURCE=json   # api | db | json
VEHICLE_MEDIA_JSON_BASE=http://127.0.0.1:8000
APP_URL=http://127.0.0.1:8000
```

In the Filament page (Vehicle Media Search), you can also pick Source: Local JSON, Local DB, or Live API.

## 2) Migrate and seed sample data

```
php artisan migrate
php artisan db:seed --class=Database\Seeders\VehicleMediaSeeder
php artisan optimize:clear
```

This seeds 2017 Acura ILX images (exterior/interior/colors) used by both the DB provider and mock JSON controller.

## 3) Use the mock JSON API

The app exposes a mock JSON endpoint backed by the seeded DB:

```
GET /mock/vehicle-media/{version}/{year}/{make}/{model}/{trim}
Example:
http://127.0.0.1:8000/mock/vehicle-media/v1/2017/acura/ilx/base-4dr-sedan-automatic
```

The response shape matches the live API normalization:

```
{
  "status": "success",
  "data": { "year": 2017, "make": "Acura", "model": "ILX", "trim": "Base 4dr Sedan Automatic",
    "images": { "exterior": [...], "interior": [...], "colors": [...] }
  }
}
```

## 4) Switch sources at runtime

- In `.env`: set `VEHICLE_MEDIA_SOURCE`.
- In the UI: pick Source in the form, then Search.
- Resolver routes requests to API | DB | JSON providers.

## 5) Add your own vehicles to the DB

Create a new seeder or an import command. Minimal example inside a tinker session:

```php
use App\Models\Vehicle; use App\Models\VehicleImage;

$year=2017; $make='Acura'; $model='ILX'; $trim='Base 4dr Sedan Automatic';
$slug=strtolower(trim(preg_replace('~[^a-z0-9]+~','-',$year.' '.$make.' '.$model.' '.$trim),'-'));
$v=Vehicle::firstOrCreate(['slug'=>$slug],[
  'year'=>$year,'make'=>$make,'model'=>$model,'trim'=>$trim,
]);

$images=['exterior'=>['https://.../ext1.jpg'],'interior'=>[],'colors'=>[]];
foreach(['exterior','interior','colors'] as $cat){
  foreach(($images[$cat]??[]) as $url){
    VehicleImage::firstOrCreate(['vehicle_id'=>$v->id,'url'=>$url],['category'=>$cat]);
  }
}
```

## 6) Change the JSON base domain later

- Update `.env`:
```
VEHICLE_MEDIA_JSON_BASE=https://my-json-api.example.com
```
- The JSON provider will call `{VEHICLE_MEDIA_JSON_BASE}/mock/vehicle-media/...`.

## 7) Troubleshooting

- Seeing raw `@csrf` or `@php`? Clear caches: `php artisan optimize:clear` and hard refresh (Ctrl+F5).
- API quota error (403): switch Source to `json` or `db` while developing; add rate-limit handling in production.
- Empty grid: confirm seeder ran, and that `VEHICLE_MEDIA_SOURCE` matches your intended source.
- Mock endpoint 404: ensure the vehicle exists in DB and the slugified URL parts match.

## 8) Testing

- Unit tests can `Http::fake()` for the API provider.
- Feature tests can hit the mock endpoint or use the DB provider directly.

