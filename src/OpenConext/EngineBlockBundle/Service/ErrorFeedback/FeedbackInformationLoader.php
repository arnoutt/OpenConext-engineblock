<?php

/**
 * Copyright 2019 SURFnet B.V.
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

namespace OpenConext\EngineBlockBundle\Service\ErrorFeedback;

use EngineBlock_ApplicationSingleton;
use OpenConext\EngineBlockBundle\Value\FeedbackInformationMap;

class FeedbackInformationLoader implements FeedbackInformationLoaderInterface
{
    /**
     * @var EngineBlock_ApplicationSingleton
     */
    private $application;

    public function __construct(EngineBlock_ApplicationSingleton $application)
    {
        $this->application = $application;
    }

    /**
     * Loads the feedbackInfo from the session and filters out empty valued entries.
     *
     * @return FeedbackInformationMap
     */
    public function load()
    {
        $feedbackInfo = $this->application->getSession()->get('feedbackInfo');

        return FeedbackInformationMap::fromData($feedbackInfo);
    }
}
