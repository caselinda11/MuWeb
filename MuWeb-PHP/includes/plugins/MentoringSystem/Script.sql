if not exists (select * from sysobjects where id = object_id('X_TEAM_Mentoring_System_LOG') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_Mentoring_System_LOG](
                                                 [ID] [int] IDENTITY(1,1) NOT NULL,
                                                 [Master] [varchar](10) NOT NULL,
                                                 [Servercode] [int] NOT NULL,
                                                 [apprentice] [varchar](10) NOT NULL,
                                                 [status] [int] NOT NULL,
                                                 [Date] [smalldatetime] NOT NULL,
        );
        INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_Mentoring_System_LOG');
    end;

