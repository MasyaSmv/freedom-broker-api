<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Http\Signer;

/**
 * Определяет договор, которому должны следовать все подписавшие запросы API.
 */
interface SignerInterface
{
    /**
     * Создайте подписную строку для данной полезной нагрузки запроса.
     *
     * @param array $payload Ассоциативный массив, который будет обозначен JSON и отправлен в API Freedom.
     */
    public function sign(array $payload): string;
}
