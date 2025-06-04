<?php

namespace Tests\Feature;

use App\Services\Clients\OpenWeatherMapClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class WeatherControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $openWeatherMapClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->openWeatherMapClient = Mockery::mock(OpenWeatherMapClient::class);
        $this->app->instance(OpenWeatherMapClient::class, $this->openWeatherMapClient);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Тест успешного получения текущей погоды по координатам
     */
    public function test_current_weather_by_coordinates_success()
    {
        $mockResponse = [
            'name' => 'Moscow',
            'sys' => ['country' => 'RU'],
            'main' => [
                'temp' => 15.5,
                'feels_like' => 14.2,
                'humidity' => 60,
                'pressure' => 1012,
            ],
            'wind' => ['speed' => 3.5],
            'weather' => [['description' => 'ясно']],
        ];

        $this->openWeatherMapClient
            ->shouldReceive('getCurrent')
            ->once()
            ->andReturn($mockResponse);

        $response = $this->getJson('/api/weather/current?lat=55.75&lon=37.61&units=metric');

        $response->dump();

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'city' => 'Moscow',
                    'country' => 'RU',
                    'temperature' => 15.5,
                    'feels_like' => 14.2,
                    'humidity' => 60,
                    'pressure' => 1012,
                    'wind_speed' => 3.5,
                ]
            ]);
    }

    /**
     * Тест ошибки при недоступности данных текущей погоды
     */
    public function test_current_weather_unavailable()
    {
        $this->openWeatherMapClient
            ->shouldReceive('getCurrent')
            ->once()
            ->andReturn(null);

        $response = $this->getJson('/api/weather/current?lat=55.75&lon=37.61');

        $response->assertStatus(503)
            ->assertJson(['error' => 'Данные о погоде недоступны']);
    }

    /**
     * Тест валидации для некорректных координат
     */
    public function test_current_weather_invalid_coordinates()
    {
        $response = $this->getJson('/api/weather/current?lat=100&lon=200');

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'lat' => 'Широта должна быть в диапазоне от -90 до 90 градусов',
                'lon' => 'Долгота должна быть в диапазоне от -180 до 180 градусов',
            ]);
    }

    /**
     * Тест успешного получения прогноза погоды
     */
    public function test_forecast_weather_success()
    {
        $mockResponse = [
            'list' => [
                [
                    'dt' => 1634567890,
                    'dt_txt' => '2021-10-18 12:00:00',
                    'main' => [
                        'temp' => 16.0,
                        'feels_like' => 15.0,
                        'temp_min' => 14.5,
                        'temp_max' => 17.0,
                        'humidity' => 65,
                        'pressure' => 1013,
                    ],
                    'weather' => [['description' => 'облачно', 'icon' => '04d']],
                    'wind' => ['speed' => 4.0, 'deg' => 180],
                    'pop' => 0.2,
                    'visibility' => 10000,
                    'clouds' => ['all' => 75],
                ]
            ]
        ];

        $this->openWeatherMapClient
            ->shouldReceive('getForecast')
            ->once()
            ->andReturn($mockResponse);

        $response = $this->getJson('/api/weather/forecast?city=Moscow&cnt=1');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'datetime' => '2021-10-18 12:00:00',
                        'timestamp' => 1634567890,
                        'temperature' => 16.0,
                        'feels_like' => 15.0,
                        'temp_min' => 14.5,
                        'temp_max' => 17.0,
                        'description' => 'облачно',
                        'icon' => '04d',
                        'wind_speed' => 4.0,
                        'wind_direction' => 180,
                        'humidity' => 65,
                        'pressure' => 1013,
                        'pop' => 0.2,
                        'visibility' => 10000,
                        'clouds' => 75,
                    ]
                ]
            ]);
    }

    /**
     * Тест ошибки валидации для прогноза с некорректным cnt
     */
    public function test_forecast_weather_invalid_cnt()
    {
        $response = $this->getJson('/api/weather/forecast?city=Moscow&cnt=50');

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'cnt' => 'Количество не может превышать 40',
            ]);
    }

    /**
     * Тест успешного поиска местоположения
     */
    public function test_location_search_success()
    {
        $mockResponse = [
            [
                'name' => 'Moscow',
                'country' => 'RU',
                'state' => 'Moscow',
                'lat' => 55.75,
                'lon' => 37.61,
            ]
        ];

        $this->openWeatherMapClient
            ->shouldReceive('searchLocations')
            ->once()
            ->andReturn($mockResponse);

        $response = $this->getJson('/api/weather/location?query=Moscow&limit=1');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'name' => 'Moscow',
                        'country' => 'RU',
                        'state' => 'Moscow',
                        'coordinates' => [
                            'latitude' => 55.75,
                            'longitude' => 37.61,
                        ],
                    ]
                ]
            ]);
    }

    /**
     * Тест ошибки валидации для поиска без запроса
     */
    public function test_location_search_missing_query()
    {
        $response = $this->getJson('/api/weather/location');

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'query' => 'Поисковый запрос обязателен',
            ]);
    }

    /**
     * Тест ошибки при недоступности данных поиска местоположения
     */
    public function test_location_search_unavailable()
    {
        $this->openWeatherMapClient
            ->shouldReceive('searchLocations')
            ->once()
            ->andReturn(null);

        $response = $this->getJson('/api/weather/location?query=Moscow');

        $response->assertStatus(503)
            ->assertJson(['error' => 'Поиск местоположения недоступен']);
    }
}
