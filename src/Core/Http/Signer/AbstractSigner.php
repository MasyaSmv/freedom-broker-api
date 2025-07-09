<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Http\Signer;

/**
 * Обеспечивает общую предварительную рутину, описанную в официальной документации API Freedom.
 */
abstract class AbstractSigner implements SignerInterface
{
    protected function preSign(array $data): string
    {
        ksort($data);
        $parts = [];

        foreach ($data as $k => $v) {
            $parts[] = is_array($v)
                ? "$k=" . $this->preSign($v)
                : "$k=$v";
        }

        return implode('&', $parts);
    }
}
