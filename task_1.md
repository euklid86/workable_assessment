
# Assessment Answers

---

## 1) Customers with the Most Rentals at Store 2

```sql
SELECT 
    COUNT(*) AS rental_count, 
    r.customer_id 
FROM rental r
  INNER JOIN staff s
    ON s.staff_id = r.staff_id
WHERE s.store_id = 2
GROUP BY r.customer_id
ORDER BY rental_count DESC;
```

| rental_count | customer_id |
|--------------|-------------|
| 24           | 526         |
| 24           | 148         |
| 24           | 38          |
| 24           | 138         |

---

## 2) Movie Availability Check for "Image Princess"

```sql
SELECT 
    i.store_id,
    f.film_id,
    COUNT(*) AS inventory,
    (
        SELECT
          COUNT(*)
        FROM rental r
          INNER JOIN inventory i_inner
            ON i_inner.inventory_id = r.inventory_id
        WHERE '2005-07-29' BETWEEN DATE(r.rental_date) AND DATE(r.return_date)
        AND i_inner.film_id = f.film_id
        AND i_inner.store_id = i.store_id
    ) AS rented
FROM inventory i
  INNER JOIN film f
    ON f.film_id = i.film_id
WHERE f.title = 'Image Princess'
GROUP BY i.store_id, f.film_id;
```

| store_id | film_id | inventory | rented |
|----------|---------|-----------|--------|
| 1        | 453     | 3         | 3      |
| 2        | 453     | 2         | 1      |

**Conclusion:**  
It seems the movie "Image Princess" would be available for rental.

---

## 3) Active Customers Per Month

```sql
SELECT 
    COUNT(*) AS active_customers, 
    month_year
FROM (
    SELECT DISTINCT
        TO_CHAR(rental_date, 'Mon YYYY') AS month_year,
        customer_id
    FROM rental
) AS distinct_rentals
GROUP BY month_year
ORDER BY active_customers DESC;
```

| active_customers | month_year |
|------------------|------------|
| 599              | Aug 2005   |
| 599              | Jul 2005   |
| 590              | Jun 2005   |
| 520              | May 2005   |
| 158              | Feb 2006   |

**Note:**  
We can use a `WHERE` clause to filter results for a specific month.

---

## 4) Movies Longer Than the Average Movie Length

```sql
WITH average_length AS (
    SELECT AVG(length) AS avg_length 
    FROM film
)
SELECT 
    title, 
    length,
    avg_length
FROM film
CROSS JOIN average_length
WHERE length > avg_length;
```

---

## 5) Customers with Above-Average Number of Payments

```sql
WITH customer_payments AS (
    SELECT AVG(payments_count) AS average_payments
    FROM (
        SELECT 
            COUNT(*) AS payments_count, 
            customer_id 
        FROM payment
        GROUP BY customer_id
    ) AS payments_per_customer
)
SELECT 
    COUNT(*) AS payment_count, 
    p.customer_id, 
    cp.average_payments
FROM payment p
    CROSS JOIN customer_payments cp
GROUP BY p.customer_id, cp.average_payments
HAVING COUNT(*) > cp.average_payments;
```

---

## 6) Rentals Grouped by Customer and Film Category

**Summary:**  
This query returns the rentals grouped by customer and by film category.  
It can help the business better understand customer preferences.

```sql
SELECT 
    COUNT(*) AS rental_count, 
    c.name AS category_name, 
    r.customer_id
FROM rental r
  INNER JOIN inventory i
    ON i.inventory_id = r.inventory_id
  INNER JOIN film f
    ON f.film_id = i.film_id
  INNER JOIN film_category fc
    ON fc.film_id = f.film_id
  INNER JOIN category c
    ON c.category_id = fc.category_id
GROUP BY
    c.name,
    r.customer_id
ORDER BY rental_count DESC;
```
