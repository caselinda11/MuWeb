---对游戏库执行
use [MuOnline]
if not exists (select * from syscolumns where name = 'shifu' and id = object_id('MEMB_INFO')) alter table [MEMB_INFO] add shifu [varchar](10) NULL
GO