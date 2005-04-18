BEGIN work;

INSERT INTO config (`key`, `value`) VALUES
  ('owner/email',      'admin@slashnburn.com'),
  ('owner/first_name', 'Admin'),
  ('owner/last_name',  'User'),
  
  ('global/username',       'admin'),
  ('global/password',       'admin'),
  ('global/site_title',     'Slash \'N Burn'),
  ('global/site_slogan',    'Shredding through overcomplexity with elegance'),
  ('global/language',       'en-US'),
  ('global/theme',          'default'),
  ('global/default_module', 'summary')
;

INSERT INTO active_modules (`name`) VALUES
  ('summary'),
  ('admin')
;

INSERT INTO navigation (`type`, `url`, `caption`, `module_id`, `order_id`) VALUES
  ('0', '/', 'Home', '', '0'),
  ('1', '', '', 'articles', '1')
;

COMMIT;

