USE [GLVData]
GO

/****** Object:  Table [dbo].[Comment]    Script Date: 2/28/2018 4:16:51 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[Comment](
	[Id] [int] IDENTITY(1,1) NOT NULL,
	[Comment] [varchar](1000) NULL,
	[Date] [datetime] NULL,
	[Comment_GlvType] [int] NOT NULL,
	[UserId] [varchar](250) NULL,
 CONSTRAINT [PK_GLV_Comment] PRIMARY KEY CLUSTERED 
(
	[Id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO


