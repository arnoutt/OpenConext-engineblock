<?php

/**
 * Copyright 2010 SURFnet B.V.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace OpenConext\EngineBlock\Metadata\MetadataRepository;

use OpenConext\EngineBlock\Metadata\Entity\IdentityProvider;
use OpenConext\EngineBlock\Metadata\Entity\ServiceProvider;
use OpenConext\EngineBlock\Metadata\Factory\Collection\IdentityProviderEntityCollection;
use OpenConext\EngineBlock\Metadata\Factory\Factory\IdentityProviderFactory;
use OpenConext\EngineBlockBundle\Exception\EntityCanNotBeFoundException;
use OpenConext\EngineBlockBundle\Url\UrlProvider;

class IdpsMetadataRepository
{
    /**
     * @var CachedDoctrineMetadataRepository
     */
    private $repository = array();

    /**
     * @var IdentityProviderFactory
     */
    private $idpFactory;

    /**
     * @var UrlProvider
     */
    private $urlProvider;

    public function __construct(
        CachedDoctrineMetadataRepository $repository,
        IdentityProviderFactory $idpFactory,
        UrlProvider $urlProvider
    ) {
        $this->repository = $repository;
        $this->idpFactory = $idpFactory;
        $this->urlProvider = $urlProvider;
    }

    /**
     * @param string $entityId
     * @return ServiceProvider
     * @throws EntityNotFoundException
     */
    public function fetchServiceProviderByEntityId($entityId)
    {
        try {
            return $this->repository->fetchServiceProviderByEntityId($entityId);
        } catch (EntityNotFoundException $e) {
            throw new EntityCanNotBeFoundException(sprintf('Unable to find the SP with entity ID "%s".', $entityId));
        }
    }

    /**
     * @return IdentityProviderEntityCollection
     */
    public function findIdentityProviders(string $keyId)
    {
        return $this->convertIdentityProviders(
            $this->repository->findIdentityProviders(),
            $keyId
        );
    }

    /**
     * @param array $identityProviderEntityIds
     * @return IdentityProviderEntityCollection
     */
    public function findIdentityProvidersByEntityId(
        array $identityProviderEntityIds,
        string $keyId
    ) {
        return $this->convertIdentityProviders(
            $this->repository->findIdentityProvidersByEntityId($identityProviderEntityIds),
            $keyId
        );
    }

    /**
     * @param IdentityProvider[] $idps
     */
    private function convertIdentityProviders(
        array $idps,
        string $keyId
    ): IdentityProviderEntityCollection {

        $collection = new IdentityProviderEntityCollection();

        foreach ($idps as $idp) {
            // Don't add ourselves
            // TODO: remove after the 'magic' entities are removed
            // @see: https://www.pivotaltracker.com/story/show/168249058
            if ($idp->entityId === $this->urlProvider->getUrl('metadata_idp', false, null, null)) {
                continue;
            }

            // Do not reveal hidden IdP's
            if ($idp->getCoins()->hidden()) {
                continue;
            }

            $collection->add($this->idpFactory->createEngineBlockEntityFromEntity($idp, $keyId));
        }
        return $collection;
    }
}
