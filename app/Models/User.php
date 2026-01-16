<?php

namespace App\Models;

use Framework\Database\Model;

/**
 * User Model
 * 
 * Represents a user in the application
 */
class User extends Model
{
    /**
     * Table name
     *
     * @var string|null
     */
    protected ?string $table = 'users';

    /**
     * Primary key
     *
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected array $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Soft delete column
     *
     * @var string|null
     */
    protected ?string $softDeleteColumn = 'deleted_at';

    /**
     * Hash password on set
     *
     * @param string $password
     * @return void
     */
    public function setPasswordAttribute(string $password): void
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Check if password matches
     *
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
