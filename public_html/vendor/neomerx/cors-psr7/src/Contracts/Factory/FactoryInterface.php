<?php

declare(strict_types=1);

namespace Neomerx\Cors\Contracts\Factory;

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

use Neomerx\Cors\Contracts\AnalysisResultInterface;
use Neomerx\Cors\Contracts\AnalysisStrategyInterface;
use Neomerx\Cors\Contracts\AnalyzerInterface;

interface FactoryInterface
{
    /**
     * Create CORS Analyzer.
     */
    public function createAnalyzer(AnalysisStrategyInterface $strategy): AnalyzerInterface;

    /**
     * Create request analysis result.
     */
    public function createAnalysisResult(int $requestType, array $responseHeaders = []): AnalysisResultInterface;
}
