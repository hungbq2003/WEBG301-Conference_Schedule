-- =========================================================================
-- Conference Scheduler - SQLite schema
-- Target DB file: var/data_dev.db  (configured in .env via DATABASE_URL)
-- Generated from Doctrine entities
-- =========================================================================

PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS session_attendee;
DROP TABLE IF EXISTS session_speaker;
DROP TABLE IF EXISTS attendees;
DROP TABLE IF EXISTS speakers;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS conferences;
DROP TABLE IF EXISTS users;

-- ---------- users ----------
CREATE TABLE users (
    id         INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    email      VARCHAR(180) NOT NULL,
    roles      CLOB         NOT NULL,
    password   VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name  VARCHAR(100) NOT NULL,
    created_at DATETIME     NOT NULL
);
CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email);

-- ---------- conferences ----------
CREATE TABLE conferences (
    id          INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    name        VARCHAR(255) NOT NULL,
    description CLOB         DEFAULT NULL,
    start_date  DATE         NOT NULL,
    end_date    DATE         NOT NULL,
    location    VARCHAR(255) NOT NULL,
    capacity    INTEGER      NOT NULL,
    created_at  DATETIME     NOT NULL
);

-- ---------- speakers ----------
CREATE TABLE speakers (
    id            INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    first_name    VARCHAR(255) NOT NULL,
    last_name     VARCHAR(255) NOT NULL,
    email         VARCHAR(255) NOT NULL,
    phone         VARCHAR(255) DEFAULT NULL,
    bio           CLOB         DEFAULT NULL,
    affiliation   VARCHAR(255) DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at    DATETIME     NOT NULL,
    conference_id INTEGER      NOT NULL,
    CONSTRAINT FK_21C01B1E604B8382 FOREIGN KEY (conference_id) REFERENCES conferences (id)
);
CREATE UNIQUE INDEX UNIQ_21C01B1EE7927C74 ON speakers (email);
CREATE INDEX IDX_21C01B1E604B8382 ON speakers (conference_id);

-- ---------- sessions ----------
CREATE TABLE sessions (
    id            INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    title         VARCHAR(255) NOT NULL,
    description   CLOB         DEFAULT NULL,
    start_time    DATETIME     NOT NULL,
    end_time      DATETIME     NOT NULL,
    room          VARCHAR(255) NOT NULL,
    track         VARCHAR(100) NOT NULL,
    capacity      INTEGER      NOT NULL,
    conference_id INTEGER      NOT NULL,
    CONSTRAINT FK_9A609D13604B8382 FOREIGN KEY (conference_id) REFERENCES conferences (id)
);
CREATE INDEX IDX_9A609D13604B8382 ON sessions (conference_id);

-- ---------- attendees ----------
CREATE TABLE attendees (
    id             INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    first_name     VARCHAR(255) NOT NULL,
    last_name      VARCHAR(255) NOT NULL,
    email          VARCHAR(255) NOT NULL,
    phone          VARCHAR(20)  DEFAULT NULL,
    company        VARCHAR(255) NOT NULL,
    job_title      VARCHAR(255) NOT NULL,
    ticket_type    VARCHAR(100) NOT NULL,
    registered_at  DATETIME     NOT NULL,
    checked_in     BOOLEAN      NOT NULL,
    checked_in_at  DATETIME     DEFAULT NULL,
    conference_id  INTEGER      NOT NULL,
    CONSTRAINT FK_C8C96B25604B8382 FOREIGN KEY (conference_id) REFERENCES conferences (id)
);
CREATE UNIQUE INDEX UNIQ_C8C96B25E7927C74 ON attendees (email);
CREATE INDEX IDX_C8C96B25604B8382 ON attendees (conference_id);

-- ---------- session_speaker (M-N) ----------
CREATE TABLE session_speaker (
    session_id INTEGER NOT NULL,
    speaker_id INTEGER NOT NULL,
    PRIMARY KEY (session_id, speaker_id),
    CONSTRAINT FK_695D593B613FECDF FOREIGN KEY (session_id) REFERENCES sessions (id) ON DELETE CASCADE,
    CONSTRAINT FK_695D593BD04A0F27 FOREIGN KEY (speaker_id) REFERENCES speakers (id) ON DELETE CASCADE
);
CREATE INDEX IDX_695D593B613FECDF ON session_speaker (session_id);
CREATE INDEX IDX_695D593BD04A0F27 ON session_speaker (speaker_id);

-- ---------- session_attendee (M-N) ----------
CREATE TABLE session_attendee (
    session_id  INTEGER NOT NULL,
    attendee_id INTEGER NOT NULL,
    PRIMARY KEY (session_id, attendee_id),
    CONSTRAINT FK_9AFCB50F613FECDF FOREIGN KEY (session_id) REFERENCES sessions (id) ON DELETE CASCADE,
    CONSTRAINT FK_9AFCB50FBCFD782A FOREIGN KEY (attendee_id) REFERENCES attendees (id) ON DELETE CASCADE
);
CREATE INDEX IDX_9AFCB50F613FECDF ON session_attendee (session_id);
CREATE INDEX IDX_9AFCB50FBCFD782A ON session_attendee (attendee_id);
