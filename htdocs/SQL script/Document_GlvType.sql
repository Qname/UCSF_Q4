USE [GLVData]
GO

/****** Object:  Table [dbo].[Document_GlvType]    Script Date: 3/7/2018 3:09:00 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[Document_GlvType](
	[Id] [int] IDENTITY(1,1) NOT NULL,
	[UniqueId] [nvarchar](20) NULL,
	[GlvType] [varchar](50) NULL,
 CONSTRAINT [PK_Document_GlvType] PRIMARY KEY CLUSTERED 
(
	[Id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO


