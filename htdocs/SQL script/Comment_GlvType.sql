USE [GLVData]
GO

/****** Object:  Table [dbo].[Comment_GlvType]    Script Date: 3/9/2018 11:03:19 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[Comment_GlvType](
	[Id] [int] IDENTITY(1,1) NOT NULL,
	[UniqueId] [varchar](250) NULL,
	[CommentType] [varchar](50) NULL,
 CONSTRAINT [PK_Comment_Users] PRIMARY KEY CLUSTERED 
(
	[Id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
 CONSTRAINT [UniqueID_CommentType] UNIQUE NONCLUSTERED 
(
	[UniqueId] ASC,
	[CommentType] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

ALTER TABLE [dbo].[Comment_GlvType]  WITH CHECK ADD  CONSTRAINT [FK_Payroll_Comment_Users_Payroll_Comment_Users] FOREIGN KEY([Id])
REFERENCES [dbo].[Comment_GlvType] ([Id])
GO

ALTER TABLE [dbo].[Comment_GlvType] CHECK CONSTRAINT [FK_Payroll_Comment_Users_Payroll_Comment_Users]
GO


