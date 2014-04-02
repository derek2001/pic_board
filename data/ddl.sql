/****** Object:  Table [dbo].[stone_image]    Script Date: 04/02/2014 13:34:55 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[stone_image](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[id_slab_frame] [int] NOT NULL,
	[id_slab] [int] NOT NULL,
	[id_frame] [int] NOT NULL,
	[id_stone] [int] NOT NULL,
	[label] [nvarchar](50) NOT NULL,
	[type] [int] NOT NULL,
	[size] [int] NOT NULL,
	[status] [int] NOT NULL,
	[full_path] [nvarchar](max) NULL,
	[create_date] [datetime] NULL,
	[update_date] [datetime] NULL,
	[rowversion] [timestamp] NULL,
 CONSTRAINT [PK_stone_image] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

ALTER TABLE [dbo].[stone_image] ADD  DEFAULT ((0)) FOR [status]
GO


