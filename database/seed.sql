INSERT INTO todos (
    id,
    user_id,
    title,
    description,
    status,
    priority,
    due_date,
    created_at
)
VALUES (
    1,
    1,
    'Learn PHP',
    'Study CRUD and MySQL',
    'pending',
    'high',
    '2026-05-20 18:00:00',
    '2026-05-12 05:20:25'
);
INSERT INTO users (
    id,
    username,
    email,
    password
)
VALUES (
    1,
    'thien',
    'thien@example.com',
    '123456'
);