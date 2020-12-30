<?php
namespace Garagist\Author\DataSource;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Neos\Domain\Service\UserService;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Security\Account;
use Neos\Flow\Security\Policy\Role;
use Neos\Neos\Domain\Model\User;

class AuthorDataSource extends AbstractDataSource
{

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
        $options = [];

        /** @var User */
        foreach ($this->userService->getUsers() as $user) {
            
            /** @var Account */
            foreach ($user->getAccounts() as $account) {
                $roles = $account->getRoles();

                /** @var Role */
                foreach($roles as $role) {
                    if ((string) $role === 'Neos.Neos:Editor') {
                        $options[$this->persistenceManager->getIdentifierByObject($user)] = ['label' => $user->getLabel()];
                    }
                }
            }
        }
        return $options;
    }
}
