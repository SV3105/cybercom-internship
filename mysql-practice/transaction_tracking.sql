create database transaction_tracking;
use transaction_tracking;

CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL
);

INSERT INTO transactions (transaction_date, amount) VALUES
('2024-01-15', 50000),
('2024-02-12', 60000),
('2024-03-18', 55000),
('2024-04-10', 70000),
('2024-05-14', 65000),
('2024-06-20', 72000),
('2024-07-05', 68000),
('2024-08-25', 75000),
('2024-09-17', 80000),
('2024-10-08', 85000),
('2024-11-19', 90000),
('2024-12-30', 95000),

('2025-01-11', 60000),
('2025-02-09', 62000),
('2025-03-21', 75000),
('2025-04-13', 80000),
('2025-05-27', 82000),
('2025-06-18', 90000),
('2025-07-07', 95000),
('2025-08-23', 100000),
('2025-09-16', 110000),
('2025-10-05', 115000),
('2025-11-28', 120000),
('2025-12-22', 130000);


with monthly_data as (
	select 
		year(t.transaction_date) as year,
        month(t.transaction_date) as month,
        sum(t.amount) as monthly_revenue
	from transactions t
    WHERE t.transaction_date >= DATE_SUB(CURDATE(), INTERVAL 24 MONTH)
    group by
		year(t.transaction_date),
        month(t.transaction_date)
)
select
	year,
    month,
    monthly_revenue,
    
    -- Running Total (overall cumulative)
    sum(monthly_revenue) over (
    order by year, month
    ) as running_total,
    
    -- Year-To-Date (resets every year)
    
    sum(monthly_revenue) over (
    partition by year 
    order by month
    ) as ytd_total,
    
    -- Previous Month Revenue
    lag(monthly_revenue) over (
		order by year, month 
    ) as previous_month_revenue
   
from monthly_data
order by year, month;
    
