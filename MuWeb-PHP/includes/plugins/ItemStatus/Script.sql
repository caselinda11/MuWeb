if not exists (select * from sysobjects where id = object_id('X_TEAM_ITEMSTATUS_LOG') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_ITEMSTATUS_LOG](
                                                 [ID] [int] IDENTITY(1,1) NOT NULL,
                                                 [AccountID] [varchar](10) NOT NULL,
                                                 [Servercode] [int] NOT NULL,
                                                 [Name] [varchar](10) NOT NULL,
                                                 [Stage] [int] DEFAULT 0 NOT NULL,
                                                 [item] [varchar](255) NOT NULL,
                                                 [item_number] [int] DEFAULT 0 NOT NULL,
                                                 [Date] [smalldatetime] NOT NULL
        );
        INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_ITEMSTATUS_LOG');
    end;

