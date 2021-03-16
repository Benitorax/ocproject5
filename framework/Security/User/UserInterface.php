<?php

namespace Framework\Security\User;

/**
 * Represents the interface that all user classes must implement.
 */
interface UserInterface
{
    /**
     * Returns the roles granted to the user: ['user', 'admin']
     */
    public function getRoles(): array;

    /**
     * Returns the password used to authenticate the user.
     */
    public function getPassword(): string;

    /**
     * Returns the username used to authenticate the user.
     */
    public function getUsername(): string;
}