<?php
namespace Main\PageBundle\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
class RequestVoter implements VoterInterface
{
    private $requestStack;
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    public function matchItem(ItemInterface $item)
    {
        if ($item->getUri() === $this->requestStack->getCurrentRequest()->getRequestUri()) {
            // URL's completely match
            return true;
        } else if($item->getUri() !== '/' && (substr($this->requestStack->getCurrentRequest()->getRequestUri(), 0, strlen($item->getUri())) === $item->getUri())) {
            // URL isn't just "/" and the first part of the URL match
            return true;
        }
        return null;
    }
    
}