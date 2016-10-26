	-- SOMME ENTRE 2 DATES
	SELECT SUM(amount), currency
	FROM `operation`
	WHERE op_date BETWEEN '2016-06-01' and '2016-06-30'
	group by currency
	;

	SELECT DATE_FORMAT(o.op_date, "%m/%Y") as d, SUM(amount), currency
	FROM operation o
	JOIN subcategory s on s.id = o.subcategory_id
	JOIN category c  on c.id = s.category_id
	-- WHERE o.currency = 'BRL'
	group by  DATE_FORMAT(o.op_date, "%Y%m"), o.currency
	;

	SELECT DATE_FORMAT(o.op_date, "%m/%Y") as `date`, c.name as `category`, s.name as `subcategory`, SUM(amount), currency
	FROM operation o
	JOIN subcategory s on s.id = o.subcategory_id
	JOIN category c  on c.id = s.category_id
	-- WHERE op_date BETWEEN '2016-06-01' and '2016-06-30'
	WHERE o.currency = 'BRL'
	group by  DATE_FORMAT(o.op_date, "%Y%m"), o.subcategory_id, o.currency
	order by DATE_FORMAT(o.op_date, "%Y%m")
	;

	SELECT
			DATE_FORMAT(o.op_date, "%m/%Y") as `date`,
			c.name as `category`,
			SUM(amount) as  `amount`, currency
		FROM operation o
		JOIN subcategory s on s.id = o.subcategory_id
		JOIN category c  on c.id = s.category_id
		WHERE o.currency = "BRL" AND o.amount > 0
		group by  DATE_FORMAT(o.op_date, "%Y%m"), c.id, o.currency
		order by DATE_FORMAT(o.op_date, "%Y%m"), c.name
		;