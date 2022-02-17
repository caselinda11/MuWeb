if not exists (select * from sysobjects where id = object_id('X_TEAM_CHANGE_NAME_LOG')
                                          and OBJECTPROPERTY(id, 'IsUserTable') = 1)
begin
    CREATE TABLE [X_TEAM_CHANGE_NAME_LOG](
                                         [ID] [int] IDENTITY(1,1) NOT NULL,
                                         [AccountID] [varchar](10) NOT NULL,
                                         [servercode] [int] NOT NULL,
                                         [OLD_NAME] [varchar](10) NOT NULL,
                                         [NEW_NAME] [varchar](10) NOT NULL,
                                         [date] [smalldatetime] NULL,
    );
    INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_CHANGE_NAME_LOG');
end