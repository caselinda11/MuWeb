if not exists (select * from sysobjects where id = object_id('X_TEAM_SHOP') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_SHOP](
                                      [id] [int] IDENTITY(1,1) NOT NULL,
                                      [item_name] [varchar](255) NULL,
                                      [item_code] [varchar](max) NOT NULL,
                                      [class_type] [tinyint] DEFAULT((0)) NOT NULL,
                                      [item_type] [tinyint] DEFAULT((0)) NOT NULL,
                                      [item_count] [int] DEFAULT((0)) NOT NULL,
                                      [item_content] [text] NULL,
                                      [item_price] [int] DEFAULT((0)) NOT NULL,
                                      [price_type] [int] DEFAULT((0)) NOT NULL,
                                      [status] [tinyint] DEFAULT((0)) NOT NULL
        ) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];
    end;
if not exists (select * from sysobjects where id = object_id('X_TEAM_SHOP_LOG') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_SHOP_LOG](
                                          [buy_username] [varchar](10) NOT NULL,
                                          [servercode] [int] DEFAULT((0)) NOT NULL,
                                          [buy_character_name] [varchar](10) NOT NULL,
                                          [buy_item_name] [varchar](255) NOT NULL,
                                          [buy_id] [int] NOT NULL,
                                          [buy_price] [int] DEFAULT((0)) NOT NULL,
                                          [buy_price_type] [int] DEFAULT((0)) NOT NULL,
                                          [buy_date] [smalldatetime] NOT NULL
        );
        INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_SHOP_LOG');
    end;
