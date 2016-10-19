	SELECT tags, SUM(amount), currency
	FROM `operation`
	WHERE op_date BETWEEN '2016-09-01' and '2016-09-31'
	group by tags, currency
	;

	SELECT SUM(amount), currency
	FROM `operation`
	WHERE op_date BETWEEN '2016-09-01' and '2016-09-31'
	group by  currency
	;

	SELECT SUM(amount), currency
	FROM `operation`
	WHERE op_date BETWEEN '2016-08-01' and '2016-08-31'
	group by  currency
	;

	SELECT DATE_FORMAT(o.op_date, "%Y%m") as d, SUM(amount), currency
	FROM operation o
	JOIN subcategory s on s.id = o.subcategory_id
	JOIN category c  on c.id = s.category_id
	WHERE o.currency = 'BRL'
	group by  DATE_FORMAT(o.op_date, "%Y%m")
	;