SELECT id, name, email, created_at, updated_at
FROM users
WHERE id = ?
AND deleted_at IS NULL
LIMIT 1
