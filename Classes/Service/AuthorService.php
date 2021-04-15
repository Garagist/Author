<?php

namespace Garagist\Author\Service;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Doctrine\PersistenceManager;
use Neos\Neos\Domain\Service\UserService;

/**
 * A service for authors
 *
 * @Flow\Scope("singleton")
 * @api
 */
class AuthorService
{

    /**
     * @Flow\Inject
     * @var UserService
     */
    protected $userService;

    /**
     * @Flow\Inject
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * this functions listens to the nodeAdded event
     *
     * @param NodeInterface $node
     * @throws \Neos\Eel\Exception
     */
    public function afterNodeAdded(NodeInterface $node)
    {
        if (!$node->getNodeType()->isOfType('Garagist.Author:Mixin.User')) {
            return;
        }

        $this->setAuthorOfPostNodeToCurrentUser($node);
    }

    /**
     * sets the current user roles
     *
     * @param NodeInterface $node
     * @return void
     */
    protected function setAuthorOfPostNodeToCurrentUser(NodeInterface $node): void
    {
        $currentUser = $this->userService->getCurrentUser();
        $userIdentifier = $this->persistenceManager->getIdentifierByObject($currentUser);

        $node->setProperty('user', $userIdentifier);
    }
}
