---------一、表不存在则创建;
if not exists (select * from sysobjects where id = object_id('X_TEAM_EXCHANGE_MASTER_LOG') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
begin
    CREATE TABLE [X_TEAM_EXCHANGE_MASTER_LOG](
                                                 [ID] [int] IDENTITY(1,1) NOT NULL,
                                                 [AccountID] [varchar](10) NOT NULL,
                                                 [Name] [varchar](10) NOT NULL,
                                                 [servercode] [int] NOT NULL,
                                                 [exchange_number] [int] NOT NULL,
                                                 [date] [smalldatetime] NULL,
    );
    INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_EXCHANGE_MASTER_LOG');
end