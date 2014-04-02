-- ================================================
-- Template generated from Template Explorer using:
-- Create Inline Function (New Menu).SQL
--
-- Use the Specify Values for Template Parameters 
-- command (Ctrl-Shift-M) to fill in the parameter 
-- values below.
--
-- This block of comments will not be included in
-- the definition of the function.
-- ================================================
USE [SlabImgDB]
GO
/****** Object:  UserDefinedFunction [dbo].[unit_status_select]    Script Date: 04/02/2014 10:59:58 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Eric>
-- Create date: <04/02/2014 10:59:58>
-- Description:	<unit_status_select>
-- =============================================
CREATE FUNCTION [dbo].[unit_status_select] ( )
RETURNS @unit TABLE
	(
    id_order decimal(18, 0),
	id_location decimal(18, 0),
	status int,
	id int,
	id_ord_unit int,
	c_status varchar(108),
	c_operator varchar(108),
	c_start_time datetime,
	c_fin_time datetime,
	p_status varchar(108),
	p_operator varchar(108),
	p_start_time datetime,
	p_fin_time datetime,
	i_status varchar(108),
	i_operator varchar(108),
	i_start_time datetime,
	i_fin_time datetime
	)
AS
BEGIN
	INSERT @unit
		SELECT o.id as id_order, o.id_location, o.status, ous.id as id, ous.id_ord_unit, ous.c_status, ous.c_operator, ous.c_start_time,
			ous.c_fin_time, ous.p_status, ous.p_operator, ous.p_start_time, ous.p_fin_time, ous.i_status, ous.i_operator,
			ous.i_start_time, ous.i_fin_time
			from [order] o
			right join [ord_unit_status] ous on ous.id_order = o.id
	RETURN
END	
