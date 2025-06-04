# Погодное приложение на Laravel

Это простое API-приложение для получения текущей погоды, прогноза и поиска местоположений с использованием OpenWeatherMap API.

## Функциональность

- **Текущая погода** (`/api/weather/current`): Возвращает текущую погоду по координатам или городу
- **Прогноз погоды** (`/api/weather/forecast`): Возвращает прогноз погоды на заданное количество временных интервалов
- **Поиск местоположений** (`/api/weather/location`): Поиск городов по запросу с возвратом координат
- Поддержка метрических и имперских единиц измерения
- Кэширование ответов для оптимизации запросов
- Геолокация по IP для автоматического определения местоположения

## Требования

- PHP 8.1+
- Composer
- Docker (для запуска в контейнерах)
- Redis (для кэширования)
- API-ключ OpenWeatherMap ([получить здесь](https://openweathermap.org))

## Установка

1. Клонируйте репозиторий:
```bash
git clone git@github.com:Mergn89/my_weather.git
cd <repository-directory>
```

2. Установите зависимости:
```bash
composer install
```

3. Настройте окружение:
   - Скопируйте `.env.example` в `.env`:
     ```bash
     cp .env.example .env
     ```
   - Укажите API-ключ OpenWeatherMap и другие настройки в `.env`:
     ```env     
     OPENWEATHERMAP_API_KEY=your_api_key
     OPENWEATHERMAP_WEATHER_URL=https://api.openweathermap.org/data/2.5/weather
     OPENWEATHERMAP_GEOCODING_URL=https://api.openweathermap.org/geo/1.0
     WEATHER_DEFAULT_CITY=Moscow
     WEATHER_UNITS=metric
     WEATHER_FORECAST_CNT=8
     ```

4. Настройте Docker:
   - Убедитесь, что Docker и Docker Compose установлены
   - Запустите контейнеры:
     ```bash
     docker-compose up -d
     ```

5. Сгенерируйте ключ приложения:
```bash
   php artisan key:generate
```


## Запуск тестов

Проект включает функциональные тесты для API-эндпоинтов с использованием PHPUnit и Mockery.

1. Установите зависимости для тестов:
```bash
   composer require --dev mockery/mockery
```

2. Запустите тесты:
```bash
   php artisan test
```

```
```
3. Просмотр содержимого ответов API:
   - Добавьте `$response->dump()` в тесты для вывода JSON-ответов в консоль. Пример:
     ```php
     $response = $this->getJson('/api/weather/current?lat=55.75&lon=37.61&units=metric');
     $response->dump();
     ```
   - Для сохранения ответов в лог:
     ```php
     \Log::info('Response: ' . json_encode($response->json(), JSON_PRETTY_PRINT));
     ```
     Лог доступен в `storage/logs/laravel.log`.
         
     

### Использование API

API доступно по префиксу `/api`. Примеры запросов:

### 1. Текущая погода
```bash
   GET /api/weather/current?lat=55.75&lon=37.61&units=metric
```
**Ответ**:
```json
{
  "data": {
    "city": "Moscow",
    "country": "RU",
    "temperature": 15.5,
    "feels_like": 14.2,
    "wind_speed": 3.5,
    "humidity": 60,
    "pressure": 1012
  }
}
```

### 2. Прогноз погоды
```bash
   GET /api/weather/forecast?city=Moscow&cnt=1&units=metric
```
**Ответ**:
```json
{
  "data": [
    {
      "datetime": "2021-10-18 12:00:00",
      "timestamp": 1634567890,
      "temperature": 16.0,
      "feels_like": 15.0,
      "temp_min": 14.5,
      "temp_max": 17.0,
      "description": "облачно",
      "icon": "04d",
      "wind_speed": 4.0,
      "wind_direction": 180,
      "humidity": 65,
      "pressure": 1013,
      "pop": 0.2,
      "visibility": 10000,
      "clouds": 75
    }
  ]
}
```

### 3. Поиск местоположения
```bash
   GET /api/weather/location?query=Moscow&limit=1
```
**Ответ**:
```json
{
  "data": [
    {
      "name": "Moscow",
      "country": "RU",
      "state": "Moscow",
      "coordinates": {
        "latitude": 55.75,
        "longitude": 37.61
      }
    }
  ]
}
```


## Дополнительно

- Документация OpenWeatherMap: [openweathermap.org/api](https://openweathermap.org/api)
- PHPUnit: [phpunit.de](https://phpunit.de/)

