if not exists (select * from sysobjects where id = object_id('X_TEAM_MARKET_CHAR') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_MARKET_CHAR](
                                             [ID] [int] IDENTITY(1,1) NOT NULL,
                                             [servercode] [int] NOT NULL,
                                             [username] [varchar](10) NOT NULL,
                                             [name] [varchar](10) NOT NULL,
                                             [price] [int] NOT NULL,
                                             [status] [int] DEFAULT((0)) NOT NULL,
                                             [password] [varchar](10) NULL,
                                             [tencent] [varchar](50) NULL,
                                             [other] [varchar](50) NULL,
                                             [date] [smalldatetime] NULL,
                                             [out_trade_no] [varchar](50) NULL,
                                             [buy_username] [varchar](10) NULL,
                                             [buy_price] [int] NULL,
                                             [buy_alipay] [varchar](50) NULL,
                                             [buy_wechat] [varchar](50) NULL,
                                             [pay_out_trade_no] [varchar](50) NULL,
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
                                             [item_code] [varchar](50) NOT NULL,
                                             [item_type] [int] DEFAULT((0)) NOT NULL,
                                             [flag] [int] DEFAULT((0)) NOT NULL,
                                             [status] [int] DEFAULT((0)) NOT NULL,
                                             [password] [varchar](10) NULL,
                                             [price] [int] NOT NULL,
                                             [date] [smalldatetime] NULL,
                                             [tencent] [varchar](50) NULL,
                                             [other] [varchar](50) NULL,
                                             [out_trade_no] [varchar](50) NULL,
                                             [buy_username] [varchar](10) NULL,
                                             [buy_price] [int] NULL,
                                             [buy_alipay] [varchar](50) NULL,
                                             [buy_wechat] [varchar](50) NULL,
                                             [pay_out_trade_no] [varchar](50) NULL,
                                             [buy_date] [smalldatetime] NULL
        ) ON [PRIMARY];
        INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_MARKET_ITEM');
    end;