<?php

namespace App\Models\History;

/**
 * Virtual dictionary for history record scopes
 *
 * Name limit is 30 characters
 */
class HistoryScope
{
    public const user = 'user';
    public const position = 'position';
    public const role = 'role';
}
