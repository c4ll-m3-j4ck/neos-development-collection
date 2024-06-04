<?php

namespace Neos\Neos\Domain\Service;

/*
 * This file is part of the Neos.Neos package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Service\Context;
use Neos\ContentRepository\Validation\Validator\NodeIdentifierValidator;
use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
class NodeSearchIdentifierResolver implements NodeSearchResolverInterface
{
    /**
     * @param string[] $searchNodeTypes
     * @return NodeInterface[]
     */
    public function resolve(
        string $term,
        array $searchNodeTypes,
        Context $context,
        NodeInterface $startingPoint = null
    ): array {
        $nodeByIdentifier = $context->getNodeByIdentifier($term);
        if ($nodeByIdentifier !== null && $this->nodeSatisfiesSearchNodeTypes(
                $nodeByIdentifier,
                $searchNodeTypes
            )) {
            return [$nodeByIdentifier->getPath() => $nodeByIdentifier];
        }
        return [];
    }

    /**
     * This resolver accepts node identifiers only
     *
     * @param string[] $searchNodeTypes
     */
    public function matches(
        string $term,
        array $searchNodeTypes,
        Context $context,
        NodeInterface $startingPoint = null
    ): bool {
        return preg_match(NodeIdentifierValidator::PATTERN_MATCH_NODE_IDENTIFIER, $term) === 1;
    }

    /**
     * Whether the given $node satisfies the specified types
     *
     * @param string[] $searchNodeTypes
     */
    protected function nodeSatisfiesSearchNodeTypes(NodeInterface $node, array $searchNodeTypes): bool
    {
        foreach ($searchNodeTypes as $nodeTypeName) {
            if ($node->getNodeType()->isOfType($nodeTypeName)) {
                return true;
            }
        }
        return false;
    }
}
