<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Bridge\Sendgrid\Transport;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

/**
 * @author Kevin Verschaeve
 */
class SendgridSmtpTransport extends EsmtpTransport
{
    public function __construct(#[\SensitiveParameter] string $key, ?EventDispatcherInterface $dispatcher = null, ?LoggerInterface $logger = null, private ?string $region = null)
    {
        parent::__construct('null' !== $region ? \sprintf('smtp.%s.sendgrid.net', $region) : 'smtp.sendgrid.net', 465, true, $dispatcher, $logger);

        $this->setUsername('apikey');
        $this->setPassword($key);
    }
}
