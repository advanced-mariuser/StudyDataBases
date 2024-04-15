# 1. Для билетов с кодом бронирования '58DF57' выбрать имена пассажиров, номер рейса, дату-время отправления и дату-время прибытия
#   Таблица результатов должна содержать по одной строке на билет

EXPLAIN ANALYZE
SELECT f.flight_no,
       t.passenger_name,
       f.actual_departure,
       f.actual_arrival
FROM tickets as t
         INNER JOIN ticket_flights tf on t.ticket_no = tf.ticket_no
         INNER JOIN bookings.flights f on tf.flight_id = f.flight_id
WHERE book_ref = '58DF57';

-- алгоритмическая сложность, операции с деревом

# 2. Для всех типов самолётов выбрать количество мест по классам обслуживания
#   Ожидаемая схема набора результатов: (aircraft_code, fare_conditions, seat_count)

EXPLAIN ANALYZE
SELECT aircraft_code,
       fare_conditions,
       COUNT(seat_no)
FROM seats
GROUP BY aircraft_code, fare_conditions;

# 3. Выбрать все «счастливые» коды бронирования со списками имён пассажиров в каждом из них
#   На одно бронирование со «счастливым» кодом должен быть ровно один результат запроса
#   Под «счастливым» кодом понимается код, в котором первые три символа совпадают с тремя последними (например, '0DA0DA')

EXPLAIN ANALYZE
SELECT book_ref,
       GROUP_CONCAT(
               passenger_name
               SEPARATOR ', '
       ) AS name
FROM tickets
WHERE SUBSTRING(book_ref, 1, 3) = SUBSTRING(book_ref, 4, 3)
GROUP BY book_ref;


# 4. Выбрать номер рейса, дату-время отправления и дату-время прибытия последнего по времени отправления рейса,
# прибывшего из Краснодара в Калининград
#   Следует выбирать только рейсы в состоянии 'Arrived'
#   Даты отправления и прибытия следует выбирать фактические, а не запланированные

SET @kr_airport_code := (SELECT airport_code
                         FROM airports_data
                         WHERE city ->> '$.ru' = 'Краснодар');

SET @kal_airport_code := (SELECT airport_code
                          FROM airports_data
                          WHERE city ->> '$.ru' = 'Калининград');

SELECT flight_no,
       actual_arrival,
       actual_departure
FROM flights
WHERE status = 'Arrived'
  AND arrival_airport = @kal_airport_code
  AND departure_airport = @kr_airport_code
ORDER BY actual_departure DESC
LIMIT 1
;

# 5. Выбрать номер рейса и дату-время отправления для 10 рейсов, принёсших наибольшую выручку
#   Следует выбирать только рейсы в состоянии 'Arrived'
#   Даты отправления следует выбирать фактические, а не запланированные

SELECT
    f.flight_no,
    f.actual_departure,
    SUM(tf.amount) AS total_amount
FROM flights f
         INNER JOIN ticket_flights tf ON f.flight_id = tf.flight_id
WHERE f.status = 'Arrived'
GROUP BY f.flight_no, f.actual_departure
ORDER BY total_amount DESC
LIMIT 10;

# 6. Выбрать номер рейса, дату-время отправления и количество свободных мест класса Эконом для перелёта из
# Владивостока в Москву ближайшим рейсом
#   Следует выбирать только рейсы в состоянии 'Scheduled'

SET @vl_airport_code := (SELECT airport_code
                         FROM airports_data
                         WHERE city ->> '$.ru' = 'Владивосток');

SET @msc_airport_code := (SELECT GROUP_CONCAT(airport_code)
                          FROM airports_data
                          WHERE city ->> '$.ru' = 'Москва');

SELECT f.flight_no,
       f.actual_departure
FROM flights f
         INNER JOIN boarding_passes bp ON f.flight_id = bp.flight_id
         INNER JOIN aircrafts_data ad ON f.aircraft_code = ad.aircraft_code
         INNER JOIN seats s on ad.aircraft_code = s.aircraft_code
WHERE f.status = 'Scheduled'
  AND f.departure_airport = @vl_airport_code
  AND FIND_IN_SET(f.arrival_airport, @msc_airport_code)
  AND s.fare_conditions = 'Economy'
;

SHOW CREATE TABLE aircrafts_data;