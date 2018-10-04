USE [GLVData]
GO

/****** Object:  Table [dbo].[Document]    Script Date: 3/7/2018 3:09:09 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[Document](
	[Id] [int] IDENTITY(1,1) NOT NULL,
	[DocumentName] [nvarchar](1000) NULL,
	[Document_GlvTypeId] [int] NULL,
	[CreatedDate] [datetime] NULL,
 CONSTRAINT [PK_Documents] PRIMARY KEY CLUSTERED 
(
	[Id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

ALTER TABLE [dbo].[Document]  WITH CHECK ADD  CONSTRAINT [FK_Document_GlvType_Document] FOREIGN KEY([Document_GlvTypeId])
REFERENCES [dbo].[Document_GlvType] ([Id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO

ALTER TABLE [dbo].[Document] CHECK CONSTRAINT [FK_Document_GlvType_Document]
GO


