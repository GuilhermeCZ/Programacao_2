--
-- pgdb-schema.sql
--

CREATE FUNCTION at_dateup() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
    NEW.date = now();
    RETURN NEW;
END;$$;

CREATE FUNCTION at_dupup() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
UPDATE radacct SET acctstoptime = now()
    WHERE acctstoptime IS NULL
    AND username IN (SELECT username FROM at_dupacct)
    AND acctstarttime = (SELECT acctstarttime FROM radacct
         WHERE acctstoptime IS NULL AND username IN (SELECT username FROM at_dupacct)
         ORDER BY acctstarttime LIMIT 1);
RETURN NULL;
END;$$;

CREATE VIEW at_check AS
    SELECT rcp.username, rcp.value AS password, rcp.attribute AS passtype, rcm.value AS macaddr
    FROM (radcheck rcp LEFT JOIN radcheck rcm ON rcp.username = rcm.username AND rcm.attribute = 'Calling-Station-Id')
    WHERE (rcp.attribute IN ('Cleartext-Password', 'User-Password')) ORDER BY rcp.username;

CREATE TABLE at_equip (
    id serial PRIMARY KEY NOT NULL,
    date timestamp without time zone DEFAULT now() NOT NULL,
    brnd text NOT NULL,
    srvc text NOT NULL,
    port integer NOT NULL,
    usnm text NOT NULL,
    pass text NOT NULL,
    type character(4) NOT NULL,
    grup text NOT NULL,
    ipad inet NOT NULL,
    maca macaddr NOT NULL,
    plac text,
    deta text
);

CREATE TABLE at_monitor (
    id serial PRIMARY KEY NOT NULL,
    eqid integer NOT NULL,
    name text,
    date timestamp without time zone DEFAULT now() NOT NULL,
    data text NOT NULL
);

CREATE TABLE at_session (
    id serial PRIMARY KEY NOT NULL,
    date timestamp without time zone DEFAULT now() NOT NULL,
    username text,
    php_session_id text NOT NULL,
    status boolean NOT NULL,
    ip_address inet,
    mac_address macaddr,
    connection integer[]
);

CREATE TABLE at_settings (
    id serial PRIMARY KEY NOT NULL,
    date timestamp without time zone DEFAULT now() NOT NULL,
    type text NOT NULL,
    data text NOT NULL,
    conf text,
    seqn integer
);

CREATE TABLE at_tickets (
   id serial PRIMARY KEY NOT NULL,
   customer_id integer,
   category integer NOT NULL,
   subject text NOT NULL,
   deadline timestamp without time zone NOT NULL
);

CREATE TABLE at_ticket_messages (
   id serial PRIMARY KEY NOT NULL,
   date timestamp without time zone DEFAULT now() NOT NULL,
   ticket_id integer NOT NULL,
   user_id integer NOT NULL,
   priority integer NOT NULL,
   message text NOT NULL,
   status boolean NOT NULL DEFAULT TRUE
);

CREATE TABLE at_ticket_sitrep (
   id integer PRIMARY KEY NOT NULL,
   latency integer[],
   throughput integer[],
   internal_address inet,
   internal_dns inet
);

CREATE TABLE at_userdata (
    id serial PRIMARY KEY NOT NULL,
    date timestamp without time zone DEFAULT now() NOT NULL,
    username text,
    phone integer,
    higher_id integer NOT NULL,
    data text NOT NULL,
    connection text NOT NULL,
    pic text
);

CREATE VIEW at_techs AS
    SELECT at_userdata.id, "substring"(at_userdata.data, ':"name";s:[0-9]+:"([^"]+)";') AS name
    FROM at_userdata
    WHERE (at_userdata.username IN (SELECT radusergroup.username FROM radusergroup
          WHERE radusergroup.groupname IN ('full', 'admn', 'tech')))
    UNION SELECT 0 AS id, 'unknown' AS name;

CREATE VIEW at_dupacct AS
    SELECT accounting.username, accounting.dup
    FROM (SELECT radacct.username, count(radacct.username) AS dup FROM radacct
          WHERE (radacct.acctstoptime IS NULL) GROUP BY radacct.username) accounting
    WHERE (accounting.dup > 1);

CREATE VIEW at_fipacct AS
    SELECT rug.username, ra.framedipaddress
    FROM (radusergroup rug LEFT JOIN radacct ra ON rug.username = ra.username AND ra.acctstoptime IS NULL)
    WHERE rug.groupname <> 'full' ORDER BY rug.username;

CREATE TRIGGER at_datesessup BEFORE UPDATE ON at_session FOR EACH ROW EXECUTE PROCEDURE at_dateup();

CREATE TRIGGER at_dupacctup AFTER INSERT ON radacct FOR EACH STATEMENT EXECUTE PROCEDURE at_dupup();
