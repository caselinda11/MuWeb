---------一、表不存在则创建;
if not exists (select * from sysobjects where id = object_id('X_TEAM_WCOIN_TRANSFER_LOG') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
begin
    CREATE TABLE [X_TEAM_WCOIN_TRANSFER_LOG](
                                                 [ID] [int] IDENTITY(1,1) NOT NULL,
                                                 [send_username] [varchar](10) NOT NULL,
                                                 [servercode] [int] NOT NULL,
                                                 [credit_price] [int] NOT NULL,
                                                 [credit_type] [int] NOT NULL,
                                                 [receive_username] [varchar](10) NOT NULL,
                                                 [date] [smalldatetime] NULL,
    );
    INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_WCOIN_TRANSFER_LOG');
end