
use SlabImgDB
CREATE TABLE ord_unit_status(
	id INTEGER IDENTITY primary key,
	id_order INTEGER not null,
	id_ord_unit INTEGER not null,
	c_status VARCHAR(108) null,
	c_operator VARCHAR(108) null,
	c_start_time DATETIME null,
	c_fin_time DATETIME null,
	p_status VARCHAR(108) null,
	p_operator VARCHAR(108) null,
	p_start_time DATETIME null,
	p_fin_time DATETIME null,
	i_status VARCHAR(108) null,
	i_operator VARCHAR(108) null,
	i_start_time DATETIME null,
	i_fin_time DATETIME null
)