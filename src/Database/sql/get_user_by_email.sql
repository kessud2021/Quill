SELECT id, name, email, password, created_at, updated_at
FROM users
WHERE email = ?
AND deleted_at IS NULL
LIMIT 1
