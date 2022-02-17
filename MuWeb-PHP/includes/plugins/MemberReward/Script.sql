if not exists (select * from sysobjects where id = object_id('X_TEAM_MEMBER_REWARD') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_MEMBER_REWARD](
                                                 [ID] [int] IDENTITY(1,1) NOT NULL,
                                                 [reward_name] [varchar](50) NOT NULL,
                                                 [requirement_vip] [int] NOT NULL,
                                                 [reward_code] [varchar](50) NOT NULL,
                                                 [reward_Description] [text] NULL,
                                                 [status] [int] DEFAULT((0)) NOT NULL,
        );
        INSERT INTO [X_TEAM_MEMBER_REWARD] ([reward_name],[requirement_vip],[reward_code],[reward_Description],[status]) VALUES ('VIP15',15,'141B21FFFFFBC34000D000FFFFFFFFFF','纪念戒指一枚',1);
    end;

if not exists (select * from sysobjects where id = object_id('X_TEAM_MEMBER_REWARD_LOG') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
    begin
        CREATE TABLE [X_TEAM_MEMBER_REWARD_LOG](
                                                 [ID] [int] IDENTITY(1,1) NOT NULL,
                                                 [AccountID] [varchar](10) NOT NULL,
                                                 [Servercode] [int] NOT NULL,
                                                 [Name] [varchar](10) NOT NULL,
                                                 [Reward_id] [int] NOT NULL,
                                                 [Receive_VIP] [int] NOT NULL,
                                                 [Date] [smalldatetime] NOT NULL
        );
        INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_MEMBER_REWARD_LOG');
    end;

