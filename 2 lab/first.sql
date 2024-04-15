SHOW TABLES;

-- 1. Выбрать всю информацию о рейсах (flights), в которых номер рейса (flight_no) заканчивается на '488'
EXPLAIN ANALYZE
SELECT *
FROM flights
WHERE flight_no LIKE '%488';

-- почему запрос получился без индекса

-- 2. Выбрать всю информацию о рейсах (flights), для которых аэропорт Краснодар является пунктом отправления либо прибытия
SET @kr_airport_code := (SELECT airport_code
                         FROM airports_data
                         WHERE airport_name ->> '$.ru' = 'Краснодар');
SELECT @kr_airport_code;

EXPLAIN ANALYZE
SELECT *
FROM flights
WHERE arrival_airport = @kr_airport_code
   OR departure_airport = @kr_airport_code;

-- какова сложность слияния двух отсортированных массивов. какой алгоритм слияния

-- 3. Выбрать всю информацию о рейсах (flights) на самолёте Сухой Суперджет-100, для которых аэропорт Чебоксар является пунктом отправления либо прибытия
SET @cheb_airport_code := (SELECT airport_code
                           FROM airports_data
                           WHERE airport_name ->> '$.ru' = 'Чебоксары');
SELECT @cheb_airport_code;

SET @super_jet := (SELECT aircraft_code
                   FROM aircrafts_data
                   WHERE model ->> '$.ru' = 'Сухой Суперджет-100');
SELECT @super_jet;

EXPLAIN ANALYZE
SELECT *
FROM flights
WHERE aircraft_code = @super_jet
  AND (departure_airport = @cheb_airport_code OR arrival_airport = @cheb_airport_code);

-- посчитать количество аэропортов общее, аэропортов вылета в таблице flights. количество самолетов общее, в таблице flights

SELECT DISTINCT
    COUNT(departure_airport),
    COUNT(arrival_airport),
    COUNT(departure_airport AND arrival_airport)
FROM flights;

-- 4. Выбрать идентификаторы и стоимости 10 самых дорогостоящих бронирований (bookings)
EXPLAIN ANALYZE
SELECT book_ref, total_amount
FROM bookings
ORDER BY total_amount DESC
LIMIT 10;

-- 5. Выбрать имена и контактные данные всех пассажиров, указанных в самом дорогостоящем бронировании (среди всех, что есть в базе данных)
SET @most_expensive_booking := (SELECT book_ref
                                FROM bookings
                                ORDER BY total_amount DESC
                                LIMIT 1);
SELECT @most_expensive_booking;

EXPLAIN ANALYZE
SELECT passenger_name, contact_data
FROM tickets
WHERE book_ref = @most_expensive_booking;

-- 6. Выбрать идентификаторы самолётов, в которых есть посадочные места с редким классом 'Comfort' (вместо более привычных 'Economy' / 'Business')
--    Используйте SELECT DISTINCT, чтобы убрать дубликаты из результатов запроса
EXPLAIN ANALYZE
SELECT DISTINCT aircraft_code
FROM seats
WHERE fare_conditions = 'Comfort';
