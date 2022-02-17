if not exists (select * from sysobjects where id = object_id('X_TEAM_CREATE_CLASS_LOG') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_CREATE_CLASS_LOG](
                                                  [ID] [int] IDENTITY(1,1) NOT NULL,
                                                  [username] [varchar](10) NOT NULL,
                                                  [create_name] [varchar](10) NOT NULL,
                                                  [create_class] [int] NOT NULL,
                                                  [servercode] [int] NOT NULL,
                                                  [create_price] [varchar](50) NOT NULL,
                                                  [date] [smalldatetime] NULL,
        );
        INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_CREATE_CLASS_LOG');
    end