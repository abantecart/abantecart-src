<?php

declare(strict_types=1);

namespace Neomerx\Cors\Factory;

/*
 * Copyright 2015-2020 info@neomerx.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Neomerx\Cors\AnalysisResult;
use Neomerx\Cors\Analyzer;
use Neomerx\Cors\Contracts\AnalysisResultInterface;
use Neomerx\Cors\Contracts\AnalysisStrategyInterface;
use Neomerx\Cors\Contracts\AnalyzerInterface;
use Neomerx\Cors\Contracts\Factory\FactoryInterface;

class Factory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createAnalyzer(AnalysisStrategyInterface $strategy): AnalyzerInterface
    {
        return new Analyzer($strategy, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function createAnalysisResult(int $requestType, array $responseHeaders = []): AnalysisResultInterface
    {
        return new AnalysisResult($requestType, $responseHeaders);
    }
}
