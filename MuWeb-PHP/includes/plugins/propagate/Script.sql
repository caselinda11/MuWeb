if not exists (select 1 from syscolumns where name = 'reward_item' and id = object_id('X_TEAM_VOTE_SITES'))
alter table X_TEAM_VOTE_SITES
    add reward_item [varchar](100) NULL;