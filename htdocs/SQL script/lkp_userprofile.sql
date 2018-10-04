USE [GLVData]
ALTER TABLE lkp_userprofile
  ADD password VARCHAR(50);
GO
delete from lkp_user_roles;
delete from lkp_userprofile;
SET IDENTITY_INSERT [dbo].[lkp_user_roles] ON 


INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (3, N'ucsfmanager@gmail.com', N'Sysadmin', CAST(N'2017-03-23 16:41:45.047' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (4, N'026504134', N'Sysadmin', CAST(N'2017-03-28 11:41:14.697' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (5, N'021444021', N'Sysadmin', CAST(N'2017-04-04 05:55:00.737' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (6, N'027454495', N'Sysadmin', CAST(N'2017-04-04 06:54:16.147' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (7, N'029211927', N'Sysadmin', CAST(N'2017-04-04 06:54:58.290' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (9, N'024485658', N'Sysadmin', CAST(N'2017-04-10 00:00:00.000' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (12, N'025858382', N'Verifier', CAST(N'2017-07-05 18:30:39.000' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (13, N'024047037', N'Sysadmin', CAST(N'2017-09-15 01:58:52.000' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (15, N'029036324', N'Approver', CAST(N'2017-11-07 19:47:01.000' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (16, N'027568500', N'Approver', CAST(N'2017-11-07 19:51:04.000' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (17, N'tu.le@evizi.com', N'Approver', CAST(N'2017-11-14 09:15:39.000' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (18, N'ngo.nguyen@evizi.com', N'Sysadmin', CAST(N'2017-11-14 09:19:39.000' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (19, N'hiep.khuc@evizi.com', N'Sysadmin', CAST(N'2017-11-14 09:20:30.000' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (20, N'long.vu1@evizi.com', N'Approver', CAST(N'2017-11-14 09:21:04.000' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (21, N'viethieu.tran@evizi.com', N'Sysadmin', CAST(N'2017-11-14 09:21:31.000' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (22, N'trung.le@evizi.com', N'Sysadmin', CAST(N'2017-11-14 09:22:01.000' AS DateTime))
INSERT [dbo].[lkp_user_roles] ([id], [user_id], [authorized_role], [createdate]) VALUES (25, N'''', N'Approver', CAST(N'2017-11-16 08:12:37.000' AS DateTime))
SET IDENTITY_INSERT [dbo].[lkp_user_roles] OFF
SET IDENTITY_INSERT [dbo].[lkp_userprofile] ON 

INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (1, N'ucsfmanager@gmail.com', N'ucsfmanager@gmail.com', N'First nam', N'last name', N'ucsfmanager@gmail.com', NULL, CAST(N'2017-03-22 13:20:16.070' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (2, N'026504134', N'dmccormick', N'Mccormick', N'Dion', N'Dion.Mccormick@ucsf.edu', N'Controller''s Office', CAST(N'2017-03-28 11:33:06.043' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (3, N'021444021', N'shrestham', N'Mavi', N'Shrestha', N'mavi.shrestha@ucsf.edu', N'Controller''s Office', CAST(N'2017-04-04 05:52:16.147' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (4, N'027454495', N'sobrien1', N'Susan', N'Obrien', N'susan.o''brien@ucsf.edu', N'', CAST(N'2017-04-04 06:54:16.320' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (5, N'029211927', N'ftai', N'Freddie', N'Tai', N'freddie.tai@ucsf.edu', N'', CAST(N'2017-04-04 06:54:58.480' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (6, N'024485658', N'mkincaid', N'Kincaid', N'Mike', N'mike.kincaid@ucsf.edu', NULL, CAST(N'2017-04-12 17:11:08.193' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (9, N'025858382', N'STurner', N'Turner', N'Shannon', N'Shannon.Turner@ucsf.edu', N'Controller''s Office', CAST(N'2017-07-05 18:30:39.000' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (10, N'024047037', N'aa-jdavis4-cmp;jdavis4', N'Davis', N'Joel', N'Joel.Davis@ucsf.edu', N'Controller''s Office', CAST(N'2017-09-15 01:58:52.000' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (12, N'029036324', N'sanderson1', N'Anderson', N'Sharon', N'Sharon.Anderson@ucsf.edu', N'', CAST(N'2017-11-07 19:47:01.000' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (13, N'027568500', N'MBurgess1', N'Burgess', N'Michael', N'Michael.Burgess2@ucsf.edu', N'', CAST(N'2017-11-07 19:51:04.000' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (14, N'tu.le@evizi.com', N'tu.le', N'le', N'tu', N'tu.le@evizi.com', N'', CAST(N'2017-11-14 09:15:39.000' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (15, N'ngo.nguyen@evizi.com', N'ngo.nguyen', N'Nguyen', N'Ngo', N'ngo.nguyen@evizi.com', N'Dept', CAST(N'2017-11-14 09:19:39.000' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (16, N'hiep.khuc@evizi.com', N'hiep.khuc', N'Khuc', N'Hiep', N'hiep.khuc@evizi.com', N'Dept', CAST(N'2017-11-14 09:20:30.000' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (17, N'long.vu1@evizi.com', N'long.vu1', N'Vu', N'Long', N'long.vu1@evizi.com', N'Dept', CAST(N'2017-11-14 09:21:04.000' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (18, N'viethieu.tran@evizi.com', N'viethieu.tran', N'Tran', N'Hieu', N'viethieu.tran@evizi.com', N'Dept', CAST(N'2017-11-14 09:21:31.000' AS DateTime), N'Abcd1234!@#$')
INSERT [dbo].[lkp_userprofile] ([id], [user_id], [user_name], [nameLast], [nameFirst], [email], [departmentname], [createdate], [password]) VALUES (19, N'trung.le@evizi.com', N'trung.le', N'Le', N'Trung', N'trung.le@evizi.com', N'Dept', CAST(N'2017-11-14 09:22:01.000' AS DateTime), N'Abcd1234!@#$')
SET IDENTITY_INSERT [dbo].[lkp_userprofile] OFF
