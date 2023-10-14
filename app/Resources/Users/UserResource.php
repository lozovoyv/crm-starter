<?php
declare(strict_types=1);

namespace App\Resources\Users;

use App\Exceptions\Model\ModelLockedException;
use App\Exceptions\Model\ModelNotFoundException;
use App\Exceptions\Model\ModelWrongHashException;
use App\Models\Users\User;
use App\Resources\EntryResource;

class UserResource extends EntryResource
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user.
     *
     * @param int|null $id
     * @param string|null $hash
     * @param bool $check
     * @param bool $onlyExisting
     *
     * @return UserResource
     *
     * @throws ModelLockedException
     * @throws ModelNotFoundException
     * @throws ModelWrongHashException
     */
    public static function make(?int $id, ?string $hash = null, bool $check = false, bool $onlyExisting = true): self
    {
        /** @var User|null $user */
        if ($id === null) {
            $user = $onlyExisting ? null : new User();
        } else {
            $user = User::query()->where('id', $id)->first();
        }

        if ($user === null) {
            throw new ModelNotFoundException('Учётная запись не найдена');
        }
        if ($check && $user->exists && !$user->isHash($hash)) {
            throw new ModelWrongHashException('Учётная запись была изменена в другом месте.');
        }
        if ($check && $user->locked) {
            throw new ModelLockedException('Эту учётную запись нельзя изменить или удалить');
        }

        return new static($user);
    }

    public function user(): User
    {
        return $this->user;
    }

    /**
     * Get user hash.
     *
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->user->getHash();
    }

    /**
     * Get formatted fields values
     *
     * @param array $fields
     *
     * @return array
     */
    public function values(array $fields): array
    {
        $values = [];

        foreach ($fields as $field) {
            $values[$field] = $this->user->getAttribute($field);
        }

        return $values;
    }
}
