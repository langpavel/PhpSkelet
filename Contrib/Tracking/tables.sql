/* visitor list */
CREATE TABLE track_uri
(
    id bigint not null primary key auto_increment,
/*  schema varchar(5) not null comment 'http, https, ...', */
    uri varchar(255) not null comment '',
    track_params bool default false comment 'if GET method parameter tracking is enabled',

    cnt bigint not null,

    index(uri)
);

/* HTTP_USER_AGENT list */
CREATE TABLE track_user_agent
(
    id int not null primary key auto_increment,
    ua_val varchar(255) not null comment 'full HTTP_USER_AGENT value case sensitive',
    browser varchar(25) comment 'real browser name (defined by administrator)',

    index(ua_val)
);

/* visitor list */
CREATE TABLE track_visitor
(
    id bigint not null primary key auto_increment,
    ip varchar(40) comment 'IPv4 or IPv6 address of visitor',
    ua_id int not null, 
    session_id varchar(40) comment 'session id from PHP session mechanism',
    user_id bigint comment 'only if user is logged in'
);

/* this table is filled for every request */
CREATE TABLE track_visit
(
    id bigint not null primary key auto_increment,
    tstamp timestamp not null default CURRENT_TIMESTAMP,
    uri_id bigint not null,
    method char(10) not null comment 'HTTP/1.1 method GET, POST, PUT, DELETE, ...',
    visitor_id bigint, 
    params text
);


