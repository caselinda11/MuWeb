if not exists (select * from sysobjects where id = object_id('X_TEAM_LOTTERY_SHOP') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
	begin
		CREATE TABLE [X_TEAM_LOTTERY_SHOP](
			[ID] [int] IDENTITY(1,1) NOT NULL,
			[reward_item_name] [varchar](250) NOT NULL,
			[reward_item_code] [varchar](64) NOT NULL,
			[reward_item_price] [int] NOT NULL,
			[status] [int] DEFAULT((0)) NOT NULL
		) ON [PRIMARY];
	end
if not exists (select * from sysobjects where id = object_id('X_TEAM_LOTTERY_LOG') and OBJECTPROPERTY(id, 'IsUserTable') = 1)
	begin
		CREATE TABLE [X_TEAM_LOTTERY_LOG](
			[ID] [int] IDENTITY(1,1) NOT NULL,
			[servercode] [int] NOT NULL,
			[username] [varchar](10) NOT NULL,
			[win_code] [varchar](64) NOT NULL,
			[date] [smalldatetime] NULL,
			[status] [int] DEFAULT((0)) NOT NULL,
			[receive_date] [smalldatetime] NULL
		) ON [PRIMARY];
		INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_LOTTERY_LOG');
	end
