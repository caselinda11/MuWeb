USE [master]
GO
/****** Object:  Database [X_MuWeb]    Script Date: 12/20/2021 13:03:48 ******/
CREATE DATABASE [X_MuWeb] ON  PRIMARY 
( NAME = N'X_MuWeb', FILENAME = N'c:\Program Files\Microsoft SQL Server\MSSQL10_50.MSSQLSERVER\MSSQL\DATA\X_MuWeb.mdf' , SIZE = 2304KB , MAXSIZE = UNLIMITED, FILEGROWTH = 1024KB )
 LOG ON 
( NAME = N'X_MuWeb_log', FILENAME = N'c:\Program Files\Microsoft SQL Server\MSSQL10_50.MSSQLSERVER\MSSQL\DATA\X_MuWeb_log.LDF' , SIZE = 576KB , MAXSIZE = 2048GB , FILEGROWTH = 10%)
GO
ALTER DATABASE [X_MuWeb] SET COMPATIBILITY_LEVEL = 100
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [X_MuWeb].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [X_MuWeb] SET ANSI_NULL_DEFAULT OFF
GO
ALTER DATABASE [X_MuWeb] SET ANSI_NULLS OFF
GO
ALTER DATABASE [X_MuWeb] SET ANSI_PADDING OFF
GO
ALTER DATABASE [X_MuWeb] SET ANSI_WARNINGS OFF
GO
ALTER DATABASE [X_MuWeb] SET ARITHABORT OFF
GO
ALTER DATABASE [X_MuWeb] SET AUTO_CLOSE ON
GO
ALTER DATABASE [X_MuWeb] SET AUTO_CREATE_STATISTICS ON
GO
ALTER DATABASE [X_MuWeb] SET AUTO_SHRINK OFF
GO
ALTER DATABASE [X_MuWeb] SET AUTO_UPDATE_STATISTICS ON
GO
ALTER DATABASE [X_MuWeb] SET CURSOR_CLOSE_ON_COMMIT OFF
GO
ALTER DATABASE [X_MuWeb] SET CURSOR_DEFAULT  GLOBAL
GO
ALTER DATABASE [X_MuWeb] SET CONCAT_NULL_YIELDS_NULL OFF
GO
ALTER DATABASE [X_MuWeb] SET NUMERIC_ROUNDABORT OFF
GO
ALTER DATABASE [X_MuWeb] SET QUOTED_IDENTIFIER OFF
GO
ALTER DATABASE [X_MuWeb] SET RECURSIVE_TRIGGERS OFF
GO
ALTER DATABASE [X_MuWeb] SET  ENABLE_BROKER
GO
ALTER DATABASE [X_MuWeb] SET AUTO_UPDATE_STATISTICS_ASYNC OFF
GO
ALTER DATABASE [X_MuWeb] SET DATE_CORRELATION_OPTIMIZATION OFF
GO
ALTER DATABASE [X_MuWeb] SET TRUSTWORTHY OFF
GO
ALTER DATABASE [X_MuWeb] SET ALLOW_SNAPSHOT_ISOLATION OFF
GO
ALTER DATABASE [X_MuWeb] SET PARAMETERIZATION SIMPLE
GO
ALTER DATABASE [X_MuWeb] SET READ_COMMITTED_SNAPSHOT OFF
GO
ALTER DATABASE [X_MuWeb] SET HONOR_BROKER_PRIORITY OFF
GO
ALTER DATABASE [X_MuWeb] SET  READ_WRITE
GO
ALTER DATABASE [X_MuWeb] SET RECOVERY SIMPLE
GO
ALTER DATABASE [X_MuWeb] SET  MULTI_USER
GO
ALTER DATABASE [X_MuWeb] SET PAGE_VERIFY CHECKSUM
GO
ALTER DATABASE [X_MuWeb] SET DB_CHAINING OFF
GO
USE [X_MuWeb]
GO
/****** Object:  Table [dbo].[X_TEAM_VOTES]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_VOTES](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[user_id] [int] NOT NULL,
	[user_ip] [varchar](50) NOT NULL,
	[user_machine] [varchar](50) NOT NULL,
	[vote_site_id] [int] NOT NULL,
	[timestamp] [varchar](50) NOT NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[X_TEAM_VOTE_SITES]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_VOTE_SITES](
	[votesite_id] [int] IDENTITY(1,1) NOT NULL,
	[votesite_title] [varchar](50) NOT NULL,
	[votesite_link] [varchar](max) NOT NULL,
	[votesite_reward] [int] NOT NULL,
	[votesite_time] [int] NOT NULL,
 CONSTRAINT [PK_X_TEAM_VOTE_SITES] PRIMARY KEY CLUSTERED 
(
	[votesite_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[X_TEAM_VOTE_LOGS]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_VOTE_LOGS](
	[servercode] [int] NOT NULL,
	[user_id] [int] NOT NULL,
	[votesite_id] [int] NOT NULL,
	[timestamp] [varchar](50) NOT NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[X_TEAM_SHOP_LOG]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_SHOP_LOG](
	[buy_username] [varchar](10) NOT NULL,
	[servercode] [int] NOT NULL,
	[buy_character_name] [varchar](10) NOT NULL,
	[buy_item_name] [varchar](255) NOT NULL,
	[buy_id] [int] NOT NULL,
	[buy_price] [int] NOT NULL,
	[buy_price_type] [int] NOT NULL,
	[buy_date] [smalldatetime] NOT NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[X_TEAM_SHOP]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_SHOP](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[item_name] [varchar](255) NULL,
	[item_code] [varchar](max) NOT NULL,
	[class_type] [tinyint] NOT NULL,
	[item_type] [tinyint] NOT NULL,
	[item_count] [int] NOT NULL,
	[item_content] [text] NULL,
	[item_price] [int] NOT NULL,
	[price_type] [int] NOT NULL,
	[status] [tinyint] NOT NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[X_TEAM_SHOP] ON
INSERT [dbo].[X_TEAM_SHOP] ([id], [item_name], [item_code], [class_type], [item_type], [item_count], [item_content], [item_price], [price_type], [status]) VALUES (1, N'礼包', N'1600010628A3CC0000E000FFFFFFFFFF', 0, 0, 0, N'创造宝石*1', 300, 1, 1)
INSERT [dbo].[X_TEAM_SHOP] ([id], [item_name], [item_code], [class_type], [item_type], [item_count], [item_content], [item_price], [price_type], [status]) VALUES (2, N'创造宝石', N'1600010628A3CC0000E000FFFFFFFFFF', 0, 7, 0, N'创造宝石', 100, 1, 1)
SET IDENTITY_INSERT [dbo].[X_TEAM_SHOP] OFF
/****** Object:  Table [dbo].[X_TEAM_REGISTER_ACCOUNT]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_REGISTER_ACCOUNT](
	[registration_account] [varchar](50) NOT NULL,
	[registration_password] [varchar](50) NOT NULL,
	[registration_email] [varchar](50) NOT NULL,
	[registration_date] [varchar](50) NOT NULL,
	[registration_ip] [varchar](50) NOT NULL,
	[registration_key] [varchar](50) NOT NULL,
 CONSTRAINT [PK_X_TEAM_REGISTER_ACCOUNT] PRIMARY KEY CLUSTERED 
(
	[registration_account] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[X_TEAM_PLUGINS]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_PLUGINS](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[name] [varchar](100) NOT NULL,
	[author] [varchar](50) NOT NULL,
	[version] [varchar](50) NOT NULL,
	[compatibility] [varchar](max) NOT NULL,
	[folder] [varchar](max) NOT NULL,
	[files] [varchar](max) NOT NULL,
	[status] [int] NOT NULL,
	[install_date] [varchar](50) NOT NULL,
	[installed_by] [varchar](50) NOT NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[X_TEAM_PLUGINS] ON
INSERT [dbo].[X_TEAM_PLUGINS] ([id], [name], [author], [version], [compatibility], [folder], [files], [status], [install_date], [installed_by]) VALUES (7, N'透视物品[顶级]', N'mason X', N'1.0.0', N'2.0.0,2.1.0,2.2.0', N'items', N'loader.php', 1, N'1639809267', N'admin')
INSERT [dbo].[X_TEAM_PLUGINS] ([id], [name], [author], [version], [compatibility], [folder], [files], [status], [install_date], [installed_by]) VALUES (6, N'交易市场', N'mason X', N'1.0.0', N'2.0.0,2.1.0,2.2.0', N'Market', N'loader.php', 1, N'1639808592', N'admin')
INSERT [dbo].[X_TEAM_PLUGINS] ([id], [name], [author], [version], [compatibility], [folder], [files], [status], [install_date], [installed_by]) VALUES (3, N'商城插件', N'mason X', N'1.0.0', N'2.0.0,2.1.0,2.2.0', N'Shop', N'loader.php', 1, N'1632564174', N'admin')
INSERT [dbo].[X_TEAM_PLUGINS] ([id], [name], [author], [version], [compatibility], [folder], [files], [status], [install_date], [installed_by]) VALUES (4, N'在线夺宝', N'mason X', N'1.0.0', N'2.0.0,2.1.0,2.2.0', N'lottery', N'loader.php', 1, N'1632564256', N'admin')
INSERT [dbo].[X_TEAM_PLUGINS] ([id], [name], [author], [version], [compatibility], [folder], [files], [status], [install_date], [installed_by]) VALUES (5, N'溯源系统', N'mason X', N'1.0.0', N'2.0.0,2.1.0,2.2.0', N'drop', N'loader.php', 1, N'1639808572', N'admin')
SET IDENTITY_INSERT [dbo].[X_TEAM_PLUGINS] OFF
/****** Object:  Table [dbo].[X_TEAM_PAYPAL_TRANSACTIONS]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_PAYPAL_TRANSACTIONS](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[transaction_id] [varchar](50) NOT NULL,
	[user_id] [int] NOT NULL,
	[payment_amount] [varchar](50) NOT NULL,
	[paypal_email] [varchar](200) NOT NULL,
	[transaction_date] [varchar](50) NOT NULL,
	[transaction_status] [int] NOT NULL,
	[order_id] [varchar](50) NOT NULL,
 CONSTRAINT [PK_X_TEAM_PAYPAL_TRANSACTIONS] PRIMARY KEY CLUSTERED 
(
	[transaction_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[X_TEAM_PASSCHANGE_REQUEST]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_PASSCHANGE_REQUEST](
	[user_id] [int] NOT NULL,
	[new_password] [varchar](200) NOT NULL,
	[auth_code] [varchar](50) NOT NULL,
	[request_date] [varchar](50) NOT NULL,
 CONSTRAINT [PK_X_TEAM_PASSCHANGE_REQUEST] PRIMARY KEY CLUSTERED 
(
	[user_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[X_TEAM_NEWS]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_NEWS](
	[news_id] [int] IDENTITY(1,1) NOT NULL,
	[news_title] [varchar](max) NOT NULL,
	[title_color] [varchar](50) NOT NULL,
	[news_type] [int] NOT NULL,
	[type_color] [varchar](50) NOT NULL,
	[news_author] [varchar](50) NOT NULL,
	[news_date] [varchar](50) NOT NULL,
	[news_content] [text] NOT NULL,
	[allow_comments] [int] NOT NULL,
	[sort] [int] NOT NULL,
	[status] [tinyint] NOT NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[X_TEAM_NEWS] ON
INSERT [dbo].[X_TEAM_NEWS] ([news_id], [news_title], [title_color], [news_type], [type_color], [news_author], [news_date], [news_content], [allow_comments], [sort], [status]) VALUES (1, N'测试文章[1]', N'#5bc0de', 1, N'#5bc0de', N'mason X', N'1577808000', N'感谢您支持我们的网站系统，<br />您可以登陆您的管理员账号->后台->模块管理->新闻系统->切换显示模式哦!', 0, 5, 1)
INSERT [dbo].[X_TEAM_NEWS] ([news_id], [news_title], [title_color], [news_type], [type_color], [news_author], [news_date], [news_content], [allow_comments], [sort], [status]) VALUES (2, N'测试文章[2]', N'#f0ad4e', 1, N'#f0ad4e', N'mason X', N'1577808000', N'感谢您支持我们的网站系统，<br />您可以登陆您的管理员账号->后台->模块管理->新闻系统->切换显示模式哦!', 0, 10, 1)
INSERT [dbo].[X_TEAM_NEWS] ([news_id], [news_title], [title_color], [news_type], [type_color], [news_author], [news_date], [news_content], [allow_comments], [sort], [status]) VALUES (3, N'测试文章[3]', N'#d43f3a', 1, N'#d43f3a', N'mason X', N'1577808000', N'感谢您支持我们的网站系统，<br />您可以登陆您的管理员账号->后台->模块管理->新闻系统->切换显示模式哦!', 0, 100, 1)
INSERT [dbo].[X_TEAM_NEWS] ([news_id], [news_title], [title_color], [news_type], [type_color], [news_author], [news_date], [news_content], [allow_comments], [sort], [status]) VALUES (4, N'测试文章[1]', N'#5bc0de', 1, N'#5bc0de', N'mason X', N'1577808000', N'感谢您支持我们的网站系统，<br />您可以登陆您的管理员账号->后台->模块管理->新闻系统->切换显示模式哦!', 0, 5, 1)
INSERT [dbo].[X_TEAM_NEWS] ([news_id], [news_title], [title_color], [news_type], [type_color], [news_author], [news_date], [news_content], [allow_comments], [sort], [status]) VALUES (5, N'测试文章[2]', N'#f0ad4e', 1, N'#f0ad4e', N'mason X', N'1577808000', N'感谢您支持我们的网站系统，<br />您可以登陆您的管理员账号->后台->模块管理->新闻系统->切换显示模式哦!', 0, 10, 1)
INSERT [dbo].[X_TEAM_NEWS] ([news_id], [news_title], [title_color], [news_type], [type_color], [news_author], [news_date], [news_content], [allow_comments], [sort], [status]) VALUES (6, N'测试文章[3]', N'#d43f3a', 1, N'#d43f3a', N'mason X', N'1577808000', N'感谢您支持我们的网站系统，<br />您可以登陆您的管理员账号->后台->模块管理->新闻系统->切换显示模式哦!', 0, 100, 1)
SET IDENTITY_INSERT [dbo].[X_TEAM_NEWS] OFF
/****** Object:  Table [dbo].[X_TEAM_MARKET_ITEM]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_MARKET_ITEM](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[servercode] [int] NOT NULL,
	[username] [varchar](10) NOT NULL,
	[name] [varchar](10) NOT NULL,
	[item_code] [varchar](50) NOT NULL,
	[item_type] [int] NOT NULL,
	[flag] [int] NOT NULL,
	[status] [int] NOT NULL,
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
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[X_TEAM_MARKET_ITEM] ON
INSERT [dbo].[X_TEAM_MARKET_ITEM] ([ID], [servercode], [username], [name], [item_code], [item_type], [flag], [status], [password], [price], [date], [tencent], [other], [out_trade_no], [buy_username], [buy_price], [buy_alipay], [buy_wechat], [pay_out_trade_no], [buy_date]) VALUES (1, 0, N'admin', N'土豆土豆', N'1600010628A3CC0000E000FFFFFFFFFF', 6, 0, 0, NULL, 10, CAST(0xAE020371 AS SmallDateTime), N'55555', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[X_TEAM_MARKET_ITEM] ([ID], [servercode], [username], [name], [item_code], [item_type], [flag], [status], [password], [price], [date], [tencent], [other], [out_trade_no], [buy_username], [buy_price], [buy_alipay], [buy_wechat], [pay_out_trade_no], [buy_date]) VALUES (2, 0, N'admin', N'土豆土豆', N'036C4201E856354005A000FFFFFFFFFF', 2, 0, 0, NULL, 30, CAST(0xAE020371 AS SmallDateTime), N'55555', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)
SET IDENTITY_INSERT [dbo].[X_TEAM_MARKET_ITEM] OFF
/****** Object:  Table [dbo].[X_TEAM_MARKET_CHAR]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_MARKET_CHAR](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[servercode] [int] NOT NULL,
	[username] [varchar](10) NOT NULL,
	[name] [varchar](10) NOT NULL,
	[price] [int] NOT NULL,
	[status] [int] NOT NULL,
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
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[X_TEAM_MARKET_CHAR] ON
INSERT [dbo].[X_TEAM_MARKET_CHAR] ([ID], [servercode], [username], [name], [price], [status], [password], [tencent], [other], [date], [out_trade_no], [buy_username], [buy_price], [buy_alipay], [buy_wechat], [pay_out_trade_no], [buy_date]) VALUES (1, 0, N'admin', N'查挂专员', 123, 0, NULL, N'55555', NULL, CAST(0xAE020371 AS SmallDateTime), NULL, NULL, NULL, NULL, NULL, NULL, NULL)
SET IDENTITY_INSERT [dbo].[X_TEAM_MARKET_CHAR] OFF
/****** Object:  Table [dbo].[X_TEAM_LOTTERY_SHOP]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_LOTTERY_SHOP](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[reward_item_name] [varchar](250) NOT NULL,
	[reward_item_code] [varchar](64) NOT NULL,
	[reward_item_price] [int] NOT NULL,
	[status] [int] NOT NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[X_TEAM_LOTTERY_LOG]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_LOTTERY_LOG](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[servercode] [int] NOT NULL,
	[username] [varchar](10) NOT NULL,
	[win_code] [varchar](64) NOT NULL,
	[date] [smalldatetime] NULL,
	[status] [int] NOT NULL,
	[receive_date] [smalldatetime] NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[X_TEAM_LOTTERY_LOG] ON
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (3, 0, N'admin', N'1F0000000000000000C000FFFFFFFFFF', CAST(0xADC40480 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (4, 0, N'admin', N'水晶', CAST(0xADCE0504 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (5, 0, N'admin', N'1F0000000000000000C000FFFFFFFFFF', CAST(0xADCE0504 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (6, 0, N'admin', N'8D0000000000000000C000FFFFFFFFFF', CAST(0xADCE0504 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (7, 0, N'admin', N'1F0000000000000000C000FFFFFFFFFF', CAST(0xADCE0504 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (8, 0, N'admin', N'1F0000000000000000C000FFFFFFFFFF', CAST(0xADCE0504 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (9, 0, N'admin', N'01009E065A314A0000D000FFFFFFFFFF', CAST(0xADCE0504 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (10, 0, N'admin', N'8D0000000000000000C000FFFFFFFFFF', CAST(0xADD00396 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (11, 0, N'admin', N'0D00010662656C0000E000FFFFFFFFFF', CAST(0xADD00397 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (12, 0, N'admin', N'0F000006699F940000C000FFFFFFFFFF', CAST(0xADD00397 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (13, 0, N'admin', N'0D00010662656C0000E000FFFFFFFFFF', CAST(0xADD00397 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (14, 0, N'admin', N'1F0000000000000000C000FFFFFFFFFF', CAST(0xADD00397 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (15, 0, N'admin', N'0D00010662656C0000E000FFFFFFFFFF', CAST(0xADD00397 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (16, 0, N'admin', N'0F000006699F940000C000FFFFFFFFFF', CAST(0xADD00397 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (17, 0, N'admin', N'0D00010662656C0000E000FFFFFFFFFF', CAST(0xADD00397 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (18, 0, N'admin', N'0F000006699F940000C000FFFFFFFFFF', CAST(0xADF5045B AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (19, 0, N'admin', N'1F0000000000000000C000FFFFFFFFFF', CAST(0xADF5045C AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (20, 0, N'admin', N'0F000006699F940000C000FFFFFFFFFF', CAST(0xADF5045C AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (21, 0, N'admin', N'1600010628A3CC0000E000FFFFFFFFFF', CAST(0xADF5045C AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (22, 0, N'admin', N'0F000006699F940000C000FFFFFFFFFF', CAST(0xADF5045C AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (23, 0, N'admin', N'01009E065A314A0000D000FFFFFFFFFF', CAST(0xADF5045C AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (24, 0, N'admin', N'0D00010662656C0000E000FFFFFFFFFF', CAST(0xADF5045C AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (25, 0, N'admin', N'8D0000000000000000C000FFFFFFFFFF', CAST(0xADF60047 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (26, 0, N'admin', N'036C4201E856354005A000FFFFFFFFFF', CAST(0xADF80586 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (27, 0, N'admin', N'0F000006699F940000C000FFFFFFFFFF', CAST(0xADF80587 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (28, 0, N'admin', N'0F000006699F940000C000FFFFFFFFFF', CAST(0xADF80587 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (29, 0, N'admin', N'0D00010662656C0000E000FFFFFFFFFF', CAST(0xADF80587 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (30, 0, N'admin', N'8D0000000000000000C000FFFFFFFFFF', CAST(0xADF80587 AS SmallDateTime), 0, NULL)
INSERT [dbo].[X_TEAM_LOTTERY_LOG] ([ID], [servercode], [username], [win_code], [date], [status], [receive_date]) VALUES (31, 0, N'admin', N'0F000006699F940000C000FFFFFFFFFF', CAST(0xADF80587 AS SmallDateTime), 0, NULL)
SET IDENTITY_INSERT [dbo].[X_TEAM_LOTTERY_LOG] OFF
/****** Object:  Table [dbo].[X_TEAM_FLA]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_FLA](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[username] [varchar](50) NOT NULL,
	[ip_address] [varchar](50) NOT NULL,
	[unlock_timestamp] [varchar](50) NOT NULL,
	[failed_attempts] [int] NOT NULL,
	[timestamp] [varchar](50) NOT NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[X_TEAM_DOWNLOADS]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_DOWNLOADS](
	[download_id] [int] IDENTITY(1,1) NOT NULL,
	[download_title] [varchar](100) NOT NULL,
	[download_description] [varchar](100) NULL,
	[download_link] [varchar](max) NOT NULL,
	[download_size] [varchar](10) NULL,
	[download_type] [int] NOT NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[X_TEAM_DOWNLOADS] ON
INSERT [dbo].[X_TEAM_DOWNLOADS] ([download_id], [download_title], [download_description], [download_link], [download_size], [download_type]) VALUES (1, N'Microsoft DirectX', N'微软官方支持库，无法正常启动游戏的请安装此文件。', N'https://www.microsoft.com/zh-CN/download/details.aspx?id=35', N'286 KB', 3)
INSERT [dbo].[X_TEAM_DOWNLOADS] ([download_id], [download_title], [download_description], [download_link], [download_size], [download_type]) VALUES (2, N'Microsoft Visual C++ 2008', N'微软官方支持库，无法正常启动游戏的请安装此文件。', N'https://www.microsoft.com/zh-CN/download/details.aspx?id=29', N'1.7 MB', 3)
INSERT [dbo].[X_TEAM_DOWNLOADS] ([download_id], [download_title], [download_description], [download_link], [download_size], [download_type]) VALUES (3, N'向日葵远控工具', N'一款方便上班族提供可手机操作电脑的软件', N'https://sunlogin.oray.com/download/', N'5.21MB', 3)
SET IDENTITY_INSERT [dbo].[X_TEAM_DOWNLOADS] OFF
/****** Object:  Table [dbo].[X_TEAM_CRON]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_CRON](
	[cron_id] [int] IDENTITY(1,1) NOT NULL,
	[cron_name] [varchar](100) NOT NULL,
	[cron_description] [varchar](max) NULL,
	[cron_file_run] [varchar](max) NOT NULL,
	[cron_run_time] [varchar](50) NOT NULL,
	[cron_last_run] [varchar](50) NULL,
	[cron_status] [int] NOT NULL,
	[cron_protected] [int] NOT NULL,
	[cron_file_md5] [varchar](50) NOT NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[X_TEAM_CRON] ON
INSERT [dbo].[X_TEAM_CRON] ([cron_id], [cron_name], [cron_description], [cron_file_run], [cron_run_time], [cron_last_run], [cron_status], [cron_protected], [cron_file_md5]) VALUES (1, N'等级排行', N'定时缓存等级排行数据', N'levels_ranking.php', N'300', N'1639976144', 1, 0, N'dc818434fe057b7133eaccf48af326cd')
INSERT [dbo].[X_TEAM_CRON] ([cron_id], [cron_name], [cron_description], [cron_file_run], [cron_run_time], [cron_last_run], [cron_status], [cron_protected], [cron_file_md5]) VALUES (2, N'家族排行', N'定时缓存家族排行数据', N'gens_ranking.php', N'300', N'1639976145', 1, 0, N'54b801d10409bd0ea2ce02dadc42215b')
INSERT [dbo].[X_TEAM_CRON] ([cron_id], [cron_name], [cron_description], [cron_file_run], [cron_run_time], [cron_last_run], [cron_status], [cron_protected], [cron_file_md5]) VALUES (3, N'战盟排行', N'定时缓存战盟排行数据', N'guilds_ranking.php', N'300', N'1639976145', 1, 0, N'52c788d5508ac13e2e778fd4315e0a3b')
INSERT [dbo].[X_TEAM_CRON] ([cron_id], [cron_name], [cron_description], [cron_file_run], [cron_run_time], [cron_last_run], [cron_status], [cron_protected], [cron_file_md5]) VALUES (4, N'推广排名', N'定时缓存推广排名数据', N'votes_ranking.php', N'300', N'1639976145', 1, 0, N'f37a128847456c14df2b30253012483c')
INSERT [dbo].[X_TEAM_CRON] ([cron_id], [cron_name], [cron_description], [cron_file_run], [cron_run_time], [cron_last_run], [cron_status], [cron_protected], [cron_file_md5]) VALUES (5, N'罗兰城主', N'定时缓存罗兰城主数据', N'castle_siege.php', N'300', N'1639976145', 1, 0, N'de6ff83bd324e90c2992ce701af5fa29')
INSERT [dbo].[X_TEAM_CRON] ([cron_id], [cron_name], [cron_description], [cron_file_run], [cron_run_time], [cron_last_run], [cron_status], [cron_protected], [cron_file_md5]) VALUES (6, N'禁用系统', N'定时解除限时封停的账号', N'temporal_bans.php', N'300', N'1639976145', 1, 0, N'1c305c4b8b42e12e7e58eb1eb61b5572')
INSERT [dbo].[X_TEAM_CRON] ([cron_id], [cron_name], [cron_description], [cron_file_run], [cron_run_time], [cron_last_run], [cron_status], [cron_protected], [cron_file_md5]) VALUES (7, N'服务器信息', N'定时缓存统计服务器信息', N'server_info.php', N'300', N'1639976145', 1, 0, N'd8ce9837cd4abc175b1be915452a3405')
INSERT [dbo].[X_TEAM_CRON] ([cron_id], [cron_name], [cron_description], [cron_file_run], [cron_run_time], [cron_last_run], [cron_status], [cron_protected], [cron_file_md5]) VALUES (8, N'溯源系统', N'溯源系统', N'drop.php', N'60', N'1639976435', 1, 0, N'24d78d8e79b8fb174d3369fa6c8a64b8')
SET IDENTITY_INSERT [dbo].[X_TEAM_CRON] OFF
/****** Object:  Table [dbo].[X_TEAM_CREDITS_LOGS]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_CREDITS_LOGS](
	[log_id] [int] IDENTITY(1,1) NOT NULL,
	[log_config] [varchar](50) NOT NULL,
	[log_identifier] [varchar](50) NOT NULL,
	[log_credits] [int] NOT NULL,
	[log_transaction] [varchar](50) NOT NULL,
	[log_date] [varchar](50) NOT NULL,
	[log_inadmincp] [tinyint] NULL,
	[log_module] [varchar](50) NULL,
	[log_ip] [varchar](50) NULL,
 CONSTRAINT [PK_X_TEAM_CREDITS_LOGS] PRIMARY KEY CLUSTERED 
(
	[log_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[X_TEAM_CREDITS_LOGS] ON
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (4, N'元宝', N'admin', 300, N'subtract', N'2021-10-17 19:12:18', 0, N'usercp/lottery', N'124.15.4.23')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (5, N'积分', N'admin', 100, N'subtract', N'2021-10-17 19:12:48', 0, N'usercp/buyzen', N'124.15.4.23')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (6, N'元宝', N'admin', 1000, N'subtract', N'2021-10-27 21:24:07', 0, N'usercp/lottery', N'27.18.24.232')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (7, N'元宝', N'admin', 300, N'subtract', N'2021-10-27 21:24:26', 0, N'usercp/lottery', N'27.18.24.232')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (8, N'元宝', N'admin', 300, N'subtract', N'2021-10-29 15:18:25', 0, N'usercp/lottery', N'23.225.248.250')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (9, N'元宝', N'admin', 300, N'subtract', N'2021-10-29 15:18:37', 0, N'usercp/lottery', N'23.225.248.250')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (10, N'元宝', N'admin', 1000, N'subtract', N'2021-10-29 15:18:50', 0, N'usercp/lottery', N'23.225.248.250')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (11, N'元宝', N'admin', 300, N'subtract', N'2021-10-29 15:19:07', 0, N'usercp/lottery', N'23.225.248.250')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (12, N'元宝', N'admin', 300, N'subtract', N'2021-12-05 18:35:16', 0, N'usercp/lottery', N'113.81.50.197')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (13, N'元宝', N'admin', 1000, N'subtract', N'2021-12-05 18:35:35', 0, N'usercp/lottery', N'113.81.50.197')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (14, N'元宝', N'admin', 300, N'subtract', N'2021-12-05 18:35:47', 0, N'usercp/lottery', N'113.81.50.197')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (15, N'元宝', N'admin', 300, N'subtract', N'2021-12-06 01:11:07', 0, N'usercp/lottery', N'119.249.200.170')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (16, N'元宝', N'admin', 300, N'subtract', N'2021-12-08 23:34:24', 0, N'usercp/lottery', N'182.37.110.159')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (17, N'元宝', N'admin', 1000, N'subtract', N'2021-12-08 23:34:38', 0, N'usercp/lottery', N'182.37.110.159')
INSERT [dbo].[X_TEAM_CREDITS_LOGS] ([log_id], [log_config], [log_identifier], [log_credits], [log_transaction], [log_date], [log_inadmincp], [log_module], [log_ip]) VALUES (18, N'积分', N'admin', 300, N'subtract', N'2021-12-14 23:17:32', 0, N'shop/buy', N'119.249.202.90')
SET IDENTITY_INSERT [dbo].[X_TEAM_CREDITS_LOGS] OFF
/****** Object:  Table [dbo].[X_TEAM_CREDITS_CONFIG]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_CREDITS_CONFIG](
	[config_id] [int] IDENTITY(1,1) NOT NULL,
	[config_title] [varchar](50) NOT NULL,
	[config_database] [varchar](50) NOT NULL,
	[config_table] [varchar](50) NOT NULL,
	[config_credits_col] [varchar](50) NOT NULL,
	[config_user_col] [varchar](50) NOT NULL,
	[config_user_col_id] [varchar](50) NOT NULL,
	[config_buy_link] [varchar](max) NOT NULL,
	[config_checkonline] [tinyint] NOT NULL,
	[config_display] [tinyint] NOT NULL,
 CONSTRAINT [PK_X_TEAM_CREDITS_CONFIG] PRIMARY KEY CLUSTERED 
(
	[config_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[X_TEAM_CREDITS_CONFIG] ON
INSERT [dbo].[X_TEAM_CREDITS_CONFIG] ([config_id], [config_title], [config_database], [config_table], [config_credits_col], [config_user_col], [config_user_col_id], [config_buy_link], [config_checkonline], [config_display]) VALUES (1, N'积分', N'MuOnline', N'MEMB_INFO', N'JF', N'memb___id', N'username', N'http://baidu.com/123', 0, 1)
INSERT [dbo].[X_TEAM_CREDITS_CONFIG] ([config_id], [config_title], [config_database], [config_table], [config_credits_col], [config_user_col], [config_user_col_id], [config_buy_link], [config_checkonline], [config_display]) VALUES (2, N'元宝', N'MuOnline', N'MEMB_INFO', N'YB', N'memb___id', N'username', N'http://baidu.com/123', 0, 1)
SET IDENTITY_INSERT [dbo].[X_TEAM_CREDITS_CONFIG] OFF
/****** Object:  Table [dbo].[X_TEAM_COMBINE_SERVER]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[X_TEAM_COMBINE_SERVER](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Query] [text] NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET IDENTITY_INSERT [dbo].[X_TEAM_COMBINE_SERVER] ON
INSERT [dbo].[X_TEAM_COMBINE_SERVER] ([ID], [Query]) VALUES (1, N'X_TEAM_SHOP_LOG')
INSERT [dbo].[X_TEAM_COMBINE_SERVER] ([ID], [Query]) VALUES (2, N'X_TEAM_LOTTERY_LOG')
INSERT [dbo].[X_TEAM_COMBINE_SERVER] ([ID], [Query]) VALUES (3, N'X_TEAM_MARKET_CHAR')
INSERT [dbo].[X_TEAM_COMBINE_SERVER] ([ID], [Query]) VALUES (4, N'X_TEAM_MARKET_ITEM')
SET IDENTITY_INSERT [dbo].[X_TEAM_COMBINE_SERVER] OFF
/****** Object:  Table [dbo].[X_TEAM_BLOCKED_IP]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_BLOCKED_IP](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[block_ip] [varchar](50) NOT NULL,
	[block_by] [varchar](25) NOT NULL,
	[block_date] [varchar](50) NOT NULL,
 CONSTRAINT [PK_X_TEAM_BLOCKED_IP] PRIMARY KEY CLUSTERED 
(
	[block_ip] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[X_TEAM_BANS]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_BANS](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[servercode] [int] NOT NULL,
	[account_id] [varchar](50) NOT NULL,
	[banned_by] [varchar](50) NOT NULL,
	[ban_date] [int] NOT NULL,
	[ban_days] [int] NOT NULL,
	[ban_reason] [varchar](100) NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[X_TEAM_BAN_LOG]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_BAN_LOG](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[servercode] [int] NOT NULL,
	[account_id] [varchar](50) NOT NULL,
	[banned_by] [varchar](50) NOT NULL,
	[ban_type] [varchar](50) NOT NULL,
	[ban_date] [varchar](50) NOT NULL,
	[ban_days] [int] NULL,
	[ban_reason] [varchar](100) NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[X_TEAM_ACCOUNT]    Script Date: 12/20/2021 13:03:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[X_TEAM_ACCOUNT](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[Account] [varchar](10) NOT NULL,
	[servercode] [int] NOT NULL,
	[MachineID] [varchar](50) NULL,
	[invite_ID] [varchar](10) NULL,
	[CreateTime] [smalldatetime] NULL,
	[alipay] [varchar](50) NULL,
	[wechat] [varchar](50) NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[X_TEAM_ACCOUNT] ON
INSERT [dbo].[X_TEAM_ACCOUNT] ([id], [Account], [servercode], [MachineID], [invite_ID], [CreateTime], [alipay], [wechat]) VALUES (2, N'admin', 0, NULL, NULL, NULL, N'2088302054421665', NULL)
SET IDENTITY_INSERT [dbo].[X_TEAM_ACCOUNT] OFF
/****** Object:  Default [DF__X_TEAM_SH__serve__25869641]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_SHOP_LOG] ADD  DEFAULT ((0)) FOR [servercode]
GO
/****** Object:  Default [DF__X_TEAM_SH__buy_p__267ABA7A]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_SHOP_LOG] ADD  DEFAULT ((0)) FOR [buy_price]
GO
/****** Object:  Default [DF__X_TEAM_SH__buy_p__276EDEB3]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_SHOP_LOG] ADD  DEFAULT ((0)) FOR [buy_price_type]
GO
/****** Object:  Default [DF__X_TEAM_SH__class__1ED998B2]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_SHOP] ADD  DEFAULT ((0)) FOR [class_type]
GO
/****** Object:  Default [DF__X_TEAM_SH__item___1FCDBCEB]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_SHOP] ADD  DEFAULT ((0)) FOR [item_type]
GO
/****** Object:  Default [DF__X_TEAM_SH__item___20C1E124]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_SHOP] ADD  DEFAULT ((0)) FOR [item_count]
GO
/****** Object:  Default [DF__X_TEAM_SH__item___21B6055D]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_SHOP] ADD  DEFAULT ((0)) FOR [item_price]
GO
/****** Object:  Default [DF__X_TEAM_SH__price__22AA2996]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_SHOP] ADD  DEFAULT ((0)) FOR [price_type]
GO
/****** Object:  Default [DF__X_TEAM_SH__statu__239E4DCF]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_SHOP] ADD  DEFAULT ((0)) FOR [status]
GO
/****** Object:  Default [DF__X_TEAM_NEW__sort__08EA5793]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_NEWS] ADD  DEFAULT ((10)) FOR [sort]
GO
/****** Object:  Default [DF__X_TEAM_NE__statu__09DE7BCC]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_NEWS] ADD  DEFAULT ((1)) FOR [status]
GO
/****** Object:  Default [DF__X_TEAM_MA__item___2F10007B]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_MARKET_ITEM] ADD  DEFAULT ((0)) FOR [item_type]
GO
/****** Object:  Default [DF__X_TEAM_MAR__flag__300424B4]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_MARKET_ITEM] ADD  DEFAULT ((0)) FOR [flag]
GO
/****** Object:  Default [DF__X_TEAM_MA__statu__30F848ED]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_MARKET_ITEM] ADD  DEFAULT ((0)) FOR [status]
GO
/****** Object:  Default [DF__X_TEAM_MA__statu__2D27B809]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_MARKET_CHAR] ADD  DEFAULT ((0)) FOR [status]
GO
/****** Object:  Default [DF__X_TEAM_LO__statu__29572725]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_LOTTERY_SHOP] ADD  DEFAULT ((0)) FOR [status]
GO
/****** Object:  Default [DF__X_TEAM_LO__statu__2B3F6F97]    Script Date: 12/20/2021 13:03:49 ******/
ALTER TABLE [dbo].[X_TEAM_LOTTERY_LOG] ADD  DEFAULT ((0)) FOR [status]
GO
