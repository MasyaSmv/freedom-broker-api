<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Service;

/** Глушим sleep() в тестах */
function sleep(int $seconds): void
{
}
