SELECT id, name, email, created_at, updated_at
FROM users
WHERE deleted_at IS NULL
ORDER BY created_at DESC
