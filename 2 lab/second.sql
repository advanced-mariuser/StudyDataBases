-- 1. Для всех рейсов Домодедово (вылетающих и прибывающих), находящихся в статусе 'Delayed', поменять статус на 'Cancelled'
SELECT *
FROM flights
WHERE (departure_airport = 'DME' OR arrival_airport = 'DME')
  AND status = 'Delayed';

-- Сам запрос
UPDATE flights
SET status = 'Cancelled'
WHERE (departure_airport = 'DME' OR arrival_airport = 'DME')
  AND status = 'Delayed';

-- Обратный запрос
UPDATE flights
SET status = 'Delayed'
WHERE flight_id IN (348, 348, 761, 974, 2469, 5377, 5858, 34275, 36780, 46165, 53170, 59329);

-- 2. Для всех рейсов аэропорта Йошкар-Олы (вылетающих и прибывающих), находящихся в статусе 'Scheduled', поменять статус на 'Arrived'
-- и установить фактические даты вылета и прилёта равными запланированным
SELECT *
FROM flights
WHERE departure_airport = 'JOK'
  AND status = 'Arrived'
  AND actual_departure = scheduled_departure AND
        actual_arrival   = scheduled_arrival;

# 33347
# 33422
# 33624
# 33642

-- Сам запрос
UPDATE flights
SET status           = 'Arrived',
    actual_departure = scheduled_departure,
    actual_arrival   = scheduled_arrival
WHERE departure_airport = 'JOK'
  AND status = 'Scheduled';

-- Не выбирает обработанные строки подзапрос
-- Обратный запрос
UPDATE flights
SET status           = 'Scheduled',
    actual_departure = NULL,
    actual_arrival   = NULL
WHERE flight_id IN (SELECT flight_id
                    FROM flights
                    WHERE departure_airport = 'JOK'
                      AND status = 'Scheduled'
                      AND actual_departure = scheduled_departure
                      AND actual_arrival = scheduled_arrival);

-- 3. Удалить всю информацию о билетах пассажира Gennadiy Nikitin
SELECT ticket_no
FROM tickets
WHERE passenger_name = 'Gennadiy Nikitin';

SELECT *
FROM bookings
WHERE book_ref IN (SELECT book_ref
                   FROM tickets
                   WHERE passenger_name = 'Gennadiy Nikitin');

DELETE
FROM ticket_flights
WHERE ticket_no IN (SELECT ticket_no
                    FROM tickets
                    WHERE passenger_name = 'Gennadiy Nikitin');

DELETE
FROM boarding_passes
WHERE ticket_no IN (SELECT ticket_no
                    FROM tickets
                    WHERE passenger_name = 'Gennadiy Nikitin');

DELETE
FROM tickets
WHERE passenger_name = 'Gennadiy Nikitin';

DELETE
FROM bookings
WHERE book_ref IN (SELECT book_ref
                   FROM tickets
                   WHERE passenger_name = 'Gennadiy Nikitin');