-- =========================================================================
-- Conference Scheduler - sample seed data
-- Works with SQLite (var/data_dev.db). For MySQL, the same INSERTs work
-- after you apply schema.mysql.sql (and USE conference_schedule).
--
-- Demo login accounts (bcrypt hashed, all share password: password123)
--   admin@demo.test / password123  (ROLE_ADMIN)
--   user@demo.test  / password123  (ROLE_USER)
--   jane@demo.test  / password123  (ROLE_USER)
-- =========================================================================

-- ---------- users ----------
INSERT INTO users (id, email, roles, password, first_name, last_name, created_at) VALUES
 (1, 'admin@demo.test', '["ROLE_ADMIN"]', '$2y$12$J4sdQbfvsgOt1P.wE4JKrOt/8mdQIihP3KgwOz09sTY1PD8gquCwK', 'Site', 'Admin', '2026-04-01 09:00:00'),
 (2, 'user@demo.test',  '["ROLE_USER"]',  '$2y$12$J4sdQbfvsgOt1P.wE4JKrOt/8mdQIihP3KgwOz09sTY1PD8gquCwK', 'John', 'Doe',   '2026-04-02 10:15:00'),
 (3, 'jane@demo.test',  '["ROLE_USER"]',  '$2y$12$J4sdQbfvsgOt1P.wE4JKrOt/8mdQIihP3KgwOz09sTY1PD8gquCwK', 'Jane', 'Smith', '2026-04-03 11:30:00');

-- ---------- conferences ----------
INSERT INTO conferences (id, name, description, start_date, end_date, location, capacity, created_at) VALUES
 (1, 'DevSummit 2026', 'Annual developer summit for backend, frontend and DevOps engineers.', '2026-05-10', '2026-05-12', 'Hanoi Convention Center', 500, '2026-03-01 08:00:00'),
 (2, 'AI World Expo 2026', 'Deep dive into generative AI, agents, and responsible AI engineering.', '2026-06-18', '2026-06-20', 'Saigon Exhibition Hall', 800, '2026-03-05 08:00:00'),
 (3, 'CloudNative Days 2026', 'Kubernetes, observability, and platform engineering.', '2026-09-02', '2026-09-03', 'Danang Tech Park', 300, '2026-03-10 08:00:00');

-- ---------- speakers ----------
INSERT INTO speakers (id, first_name, last_name, email, phone, bio, affiliation, profile_image, created_at, conference_id) VALUES
 (1, 'Alice',   'Nguyen',   'alice.nguyen@acme.dev',   '+84 90 111 2233', '10+ years in distributed systems.',      'Acme Corp',   NULL, '2026-03-15 09:00:00', 1),
 (2, 'Bob',     'Tran',     'bob.tran@frontly.io',     '+84 90 222 3344', 'Frontend performance enthusiast.',        'Frontly',     NULL, '2026-03-15 09:10:00', 1),
 (3, 'Charlie', 'Pham',     'charlie.pham@opslab.net', '+84 90 333 4455', 'SRE lead, chaos engineering advocate.',   'OpsLab',      NULL, '2026-03-15 09:20:00', 1),
 (4, 'Diana',   'Le',       'diana.le@aihub.ai',       '+84 90 444 5566', 'LLM safety and evaluations researcher.',  'AI Hub',      NULL, '2026-03-20 09:00:00', 2),
 (5, 'Ethan',   'Vu',       'ethan.vu@neurobots.com',  '+84 90 555 6677', 'Robotics x LLM integration.',             'NeuroBots',   NULL, '2026-03-20 09:10:00', 2),
 (6, 'Fiona',   'Hoang',    'fiona.hoang@cncf.io',     '+84 90 666 7788', 'Kubernetes maintainer and author.',       'CNCF',        NULL, '2026-03-25 09:00:00', 3);

-- ---------- sessions ----------
INSERT INTO sessions (id, title, description, start_time, end_time, room, track, capacity, conference_id) VALUES
 (1, 'Opening Keynote: The State of Dev 2026',  'Kick-off keynote and industry outlook.',          '2026-05-10 09:00:00', '2026-05-10 10:00:00', 'Main Hall',  'Keynote',   500, 1),
 (2, 'Scaling Microservices with Event Sourcing','Patterns that survive production traffic.',       '2026-05-10 10:30:00', '2026-05-10 11:30:00', 'Room A',     'Backend',   120, 1),
 (3, 'Web Performance Deep Dive',                'Core Web Vitals, streaming SSR, and edge.',       '2026-05-10 13:00:00', '2026-05-10 14:00:00', 'Room B',     'Frontend',  120, 1),
 (4, 'Chaos Engineering in Practice',            'Hands-on failure injection workflows.',           '2026-05-11 09:30:00', '2026-05-11 11:00:00', 'Room C',     'DevOps',     80, 1),
 (5, 'LLM Agents: From Demo to Production',      'Architecting reliable agent systems.',            '2026-06-18 09:30:00', '2026-06-18 10:30:00', 'Main Hall',  'Keynote',   800, 2),
 (6, 'Evaluating AI Safety at Scale',            'Evaluation harnesses, red-teaming, guardrails.',  '2026-06-18 11:00:00', '2026-06-18 12:00:00', 'Room 1',     'AI Safety', 200, 2),
 (7, 'Robotics meets LLMs',                      'Grounding language models in the physical world.','2026-06-19 14:00:00', '2026-06-19 15:00:00', 'Room 2',     'Robotics',  200, 2),
 (8, 'Platform Engineering 101',                 'Building a golden path for your devs.',           '2026-09-02 10:00:00', '2026-09-02 11:00:00', 'Hall Alpha', 'Platform',  300, 3),
 (9, 'Observability Beyond Dashboards',          'Tracing, SLOs, and incident response.',           '2026-09-02 13:30:00', '2026-09-02 14:30:00', 'Hall Beta',  'SRE',       150, 3);

-- ---------- attendees ----------
INSERT INTO attendees (id, first_name, last_name, email, phone, company, job_title, ticket_type, registered_at, checked_in, checked_in_at, conference_id) VALUES
 (1, 'Long',   'Bui',       'long.bui@company1.vn',    '0912000001', 'Company One',    'Senior Engineer',   'VIP',      '2026-04-01 10:00:00', 1, '2026-05-10 08:40:00', 1),
 (2, 'Mai',    'Do',        'mai.do@startupx.vn',      '0912000002', 'StartupX',       'CTO',               'Standard', '2026-04-02 11:00:00', 0, NULL,                  1),
 (3, 'Nam',    'Pham',      'nam.pham@bigcorp.vn',     '0912000003', 'BigCorp',        'Tech Lead',         'Standard', '2026-04-03 12:00:00', 1, '2026-05-10 08:55:00', 1),
 (4, 'Oanh',   'Ngo',       'oanh.ngo@agency.vn',      '0912000004', 'Agency Ltd',     'UX Designer',       'Student',  '2026-04-04 09:30:00', 0, NULL,                  1),
 (5, 'Phuc',   'Ha',        'phuc.ha@cloudly.io',      '0912000005', 'Cloudly',        'Platform Engineer', 'VIP',      '2026-04-10 14:00:00', 0, NULL,                  2),
 (6, 'Quynh',  'Tran',      'quynh.tran@airesearch.ai','0912000006', 'AI Research Co', 'ML Engineer',       'Standard', '2026-04-12 15:00:00', 0, NULL,                  2),
 (7, 'Rinh',   'Vu',        'rinh.vu@robotix.io',      '0912000007', 'Robotix',        'Robotics Engineer', 'Standard', '2026-04-14 16:00:00', 0, NULL,                  2),
 (8, 'Son',    'Le',        'son.le@devops.vn',        '0912000008', 'DevOps VN',      'SRE',               'Standard', '2026-04-20 09:00:00', 0, NULL,                  3),
 (9, 'Trang',  'Nguyen',    'trang.nguyen@platco.vn',  '0912000009', 'PlatCo',         'Platform PM',       'VIP',      '2026-04-20 10:00:00', 0, NULL,                  3);

-- ---------- session_speaker (many-to-many) ----------
INSERT INTO session_speaker (session_id, speaker_id) VALUES
 (1, 1),
 (2, 1),
 (3, 2),
 (4, 3),
 (5, 4),
 (5, 5),
 (6, 4),
 (7, 5),
 (8, 6),
 (9, 6);

-- ---------- session_attendee (many-to-many) ----------
INSERT INTO session_attendee (session_id, attendee_id) VALUES
 (1, 1), (1, 2), (1, 3), (1, 4),
 (2, 1), (2, 3),
 (3, 2), (3, 4),
 (4, 1),
 (5, 5), (5, 6), (5, 7),
 (6, 5), (6, 6),
 (7, 7),
 (8, 8), (8, 9),
 (9, 8);
