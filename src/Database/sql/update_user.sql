UPDATE users
SET name = ?, email = ?, updated_at = ?
WHERE id = ?
AND deleted_at IS NULL
