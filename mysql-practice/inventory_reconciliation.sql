CREATE TABLE system1_inventory (
    product_id INT PRIMARY KEY,
    product_name VARCHAR(100),
    stock INT
);

CREATE TABLE system2_inventory (
    product_id INT PRIMARY KEY,
    product_name VARCHAR(100),
    stock INT
);

INSERT INTO system1_inventory VALUES
(1, 'Laptop', 100),
(2, 'Mobile', 50),
(3, 'Headphones', 20),
(5, 'Keyboard', 15);

INSERT INTO system2_inventory VALUES
(1, 'Laptop', 100),      
(2, 'Mobile', 60),       
(4, 'Watch', 40),         
(5, 'Keyboard', 15);      

select * from system1_inventory;
select * from system2_inventory;

select 
	coalesce(s1.product_id, s2.product_id) as product_id,
	coalesce(s1.product_name, s2.product_name) as product_name,
	s1.stock as system1_stock,
	s2.stock as system2_stock,
	
	case 
		when s1.product_id is null then 'missing in system 1'
		when s2.product_id is null then 'missing in system 2'
		when s1.stock <> s2.stock then 'mismatch'
		else 'match'
	end as status
	
	from system1_inventory s1 full outer join 
	system2_inventory s2 on s1.product_id = s2.product_id
	order by product_id;
