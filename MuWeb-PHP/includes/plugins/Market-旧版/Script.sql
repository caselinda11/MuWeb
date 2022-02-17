if not exists (select * from sysobjects where id = object_id('X_TEAM_MARKET_CHAR') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
begin
        CREATE TABLE [X_TEAM_MARKET_CHAR](
                                             [ID] [int] IDENTITY(1,1) NOT NULL,
                                             [servercode] [int] NOT NULL,
                                             [username] [varchar](10) NOT NULL,
                                             [name] [varchar](10) NOT NULL,
                                             [price] [int] NOT NULL,
                                             [price_type] [int] NOT NULL,
                                             [date] [smalldatetime] NULL,
                                             [status] [int] DEFAULT((0)) NOT NULL,
                                             [buy_username] [varchar](10) NULL,
                                             [buy_date] [smalldatetime] NULL
        ) ON [PRIMARY];
        INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_MARKET_CHAR');
end;
if not exists (select * from sysobjects where id = object_id('X_TEAM_MARKET_ITEM') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_MARKET_ITEM](
                                             [ID] [int] IDENTITY(1,1) NOT NULL,
                                             [servercode] [int] NOT NULL,
                                             [username] [varchar](10) NOT NULL,
                                             [name] [varchar](10) NOT NULL,
                                             [price] [int] NOT NULL,
                                             [price_type] [int] NOT NULL,
                                             [date] [smalldatetime] NULL,
                                             [status] [int] DEFAULT((0)) NOT NULL,
                                             [buy_username] [varchar](10) NULL,
                                             [buy_date] [smalldatetime] NULL
        ) ON [PRIMARY];
        INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_MARKET_ITEM');
    end;

