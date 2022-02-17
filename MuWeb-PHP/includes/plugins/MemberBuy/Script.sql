if not exists (select * from sysobjects where id = object_id('X_TEAM_MEMBER_BUY') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_MEMBER_BUY](
                                                 [ID] [int] IDENTITY(1,1) NOT NULL,
                                                 [vip_name] [varchar](50) NOT NULL,
                                                 [vip_code] [int] DEFAULT((0)) NOT NULL,
                                                 [Description] [text] NULL,
                                                 [price] [int] DEFAULT((0)) NOT NULL,
                                                 [price_type] [int] DEFAULT((0)) NOT NULL,
                                                 [status] [int] DEFAULT((0)) NOT NULL,
        );
        INSERT INTO [X_TEAM_MEMBER_BUY]([vip_name],[vip_code],[Description],[price],[price_type],[status]) VALUES ('VIP-1',1,'1级价格',500,1,1);
        INSERT INTO [X_TEAM_MEMBER_BUY]([vip_name],[vip_code],[Description],[price],[price_type],[status]) VALUES ('VIP-2',2,'2级价格',500,1,1);
        INSERT INTO [X_TEAM_MEMBER_BUY]([vip_name],[vip_code],[Description],[price],[price_type],[status]) VALUES ('VIP-3',3,'3级价格',500,1,1);
        INSERT INTO [X_TEAM_MEMBER_BUY]([vip_name],[vip_code],[Description],[price],[price_type],[status]) VALUES ('VIP-4',4,'4级价格',500,1,1);
        INSERT INTO [X_TEAM_MEMBER_BUY]([vip_name],[vip_code],[Description],[price],[price_type],[status]) VALUES ('VIP-5',5,'5级价格',500,1,1);
        INSERT INTO [X_TEAM_MEMBER_BUY]([vip_name],[vip_code],[Description],[price],[price_type],[status]) VALUES ('VIP-6',6,'6级价格',500,1,1);
        INSERT INTO [X_TEAM_MEMBER_BUY]([vip_name],[vip_code],[Description],[price],[price_type],[status]) VALUES ('VIP-7',7,'7级价格',500,1,1);
        INSERT INTO [X_TEAM_MEMBER_BUY]([vip_name],[vip_code],[Description],[price],[price_type],[status]) VALUES ('VIP-8',8,'8级价格',500,1,1);
        INSERT INTO [X_TEAM_MEMBER_BUY]([vip_name],[vip_code],[Description],[price],[price_type],[status]) VALUES ('VIP-9',9,'9级价格',500,1,1);
        INSERT INTO [X_TEAM_MEMBER_BUY]([vip_name],[vip_code],[Description],[price],[price_type],[status]) VALUES ('VIP-10',10,'10级价格',500,1,1);
    end;

if not exists (select * from sysobjects where id = object_id('X_TEAM_MEMBER_BUY_LOG') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_MEMBER_BUY_LOG](
                                                 [ID] [int] IDENTITY(1,1) NOT NULL,
                                                 [AccountID] [varchar](10) NOT NULL,
                                                 [Servercode] [int] NOT NULL,
                                                 [buy_id] [int] NOT NULL,
                                                 [price] [int] DEFAULT((0)) NOT NULL,
                                                 [price_type] [int] DEFAULT((0)) NOT NULL,
                                                 [Date] [smalldatetime] NOT NULL
        );
        INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_MEMBER_BUY_LOG');
    end;

