<?php
namespace Garagist\Author\DataSource;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Flow\Security\Context as SecurityContext;
use Neos\Neos\Domain\Service\UserService;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\Flow\Security\Account;
use Neos\Flow\Security\Policy\Role;
use Neos\Neos\Domain\Model\User;

class AuthorDataSource extends AbstractDataSource
{
    /**
     * @Flow\InjectConfiguration(package="Garagist.Author", path="filterByRole")
     * @var array
     */
    protected $filters;

    /**
     * @var string
     */
    static protected $identifier = 'author-source';

    /**
     * @Flow\Inject
     * @var UserService
     */
    protected $userService;

    /**
     * @Flow\Inject
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @return array JSON serializable data
     */
    public function getData(): array
    {
        $users = $this->userService->getUsers();
        $data = [];
        $filteredUsers = [];

        if (is_array($this->filters) && !empty($this->filters)) {
            /** @var User $user */
            foreach ($users as $user) {
                $accounts = $user->getAccounts();

                /** @var Account $account */
                foreach ($accounts as $account) {
                    $roles = $account->getRoles();

                    /** @var Role $role */
                    foreach ($roles as $role) {
                        if (in_array($role->getIdentifier(), $this->filters)) {
                            $filteredUsers[] = $user;
                        }
                    }
                }
            }
        } else {
            $filteredUsers = $users;
        }

        foreach ($filteredUsers as $user) {
            $data[] = [
                'label' => $user->getLabel(),
                'value' => $this->persistenceManager->getIdentifierByObject($user)
            ];
        }

        return $data;
    }
}
