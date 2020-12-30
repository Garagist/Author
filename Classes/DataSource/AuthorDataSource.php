<?php
namespace Garagist\Author\DataSource;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Flow\Security\Context as SecurityContext;
use Neos\Neos\Domain\Service\UserService;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\ContentRepository\Domain\Model\NodeInterface;
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
     * @param NodeInterface $node The node that is currently edited (optional)
     * @param array $arguments Additional arguments (key / value)
     * @return mixed JSON serializable data
     */
    public function getData(NodeInterface $node = null, array $arguments = [])
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
