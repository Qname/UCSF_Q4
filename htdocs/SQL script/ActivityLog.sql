USE [GLVData]
GO

/****** Object:  Table [dbo].[ActivityLog]    Script Date: 11/15/2017 3:06:45 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[ActivityLog](
	[Id] [int] IDENTITY(1,1) NOT NULL,
	[LogSubject] [nvarchar](250) NULL,
	[Description] [nvarchar](1000) NULL,
	[CreatedDate] [datetime] NULL,
	[AdditionalInfo] [nvarchar](1000) NULL,
	[UserId] [nvarchar](100) NULL,
 CONSTRAINT [PK_ActivityLog] PRIMARY KEY CLUSTERED 
(
	[Id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO


