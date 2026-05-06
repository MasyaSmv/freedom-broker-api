<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class CpsFileDTO
{
    public function __construct(
        public string $file,
        public string $mime,
        public string $fileName,
        public string $extension,
        public string $encodingType,
    ) {
    }
}
