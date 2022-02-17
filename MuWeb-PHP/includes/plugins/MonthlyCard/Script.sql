---------一、表不存在则创建;
if not exists (select * from sysobjects where id = object_id('X_TEAM_MONTHLY_CARD_CONFIG') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_MONTHLY_CARD_CONFIG](
                                                     [ID] [int] IDENTITY(1,1) NOT NULL,
                                                     [project_name] [varchar](255) NOT NULL,
                                                     [day] [int] NOT NULL,
                                                     [price] [int] NOT NULL,
                                                     [credit_type] [int] NOT NULL,
                                                     [daily_salary] [int] NOT NULL,
                                                     [salary_type] [int] NOT NULL,
                                                     [status] [int] NOT NULL,
        );
    end;

if not exists (select * from sysobjects where id = object_id('X_TEAM_MONTHLY_CARD_LOG') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_MONTHLY_CARD_LOG](
                                                  [ID] [int] IDENTITY(1,1) NOT NULL,
                                                  [AccountID] [varchar](10) NOT NULL,
                                                  [servercode] [int] NOT NULL,
                                                  [project_name] [varchar](255) NOT NULL,
                                                  [day] [int] NOT NULL,
                                                  [price] [int] NOT NULL,
                                                  [credit_type] [int] NOT NULL,
                                                  [date] [varchar](50) NULL,
        );
        INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_MONTHLY_CARD_LOG');
    end;

if not exists (select * from sysobjects where id = object_id('X_TEAM_MONTHLY_CARD') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
    CREATE TABLE [X_TEAM_MONTHLY_CARD](
                                          [buy_id] [int] NOT NULL,
                                          [buy_username] [varchar](10) NOT NULL,
                                          [buy_day] [int] NOT NULL,
                                          [buy_date] [varchar](50) NULL,
                                          [next_date] [varchar](50) NULL,
);
    end;